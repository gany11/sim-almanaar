<?php

namespace App\Controllers\Admin;

use App\Models\PengeluaranDonasiModel;
use App\Models\PemasukanDonasiModel;
use App\Models\DonasiModel;
use App\Controllers\BaseController;

class DonationExpenseController extends BaseController
{
    protected $pengeluaranModel;
    protected $pemasukanDonasiModel;
    protected $donasiModel;

    public function __construct() {
        $this->pengeluaranModel = new PengeluaranDonasiModel();
        $this->pemasukanDonasiModel = new PemasukanDonasiModel();
        $this->donasiModel = new DonasiModel();
    }

    public function create($idDonasi = null) {
        if (!empty($idDonasi)) {
            $exists = $this->donasiModel->find($idDonasi);
            
            if (!$exists) {
                return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
            }

            // 2. Cek apakah closed_at ADA isinya (artinya sudah ditutup)
            if (!empty($exists['closed_at'])) {
                return redirect()->to('admin/donations/detail/' . $idDonasi)->with('error', 'Donasi ini sudah ditutup.');
            }
        }
        
        $builder = $this->donasiModel->where('closed_at', null);
        
        // Jika diakses dengan parameter ID (misal: /create/2), batasi hanya ambil data tersebut
        if (!empty($idDonasi)) {
            $builder->where('id_donasi', $idDonasi);
        }

        $units = $this->pengeluaranModel
            ->distinct()
            ->select('satuan')
            ->where('deleted_at', null)
            ->findAll();
        
        $donations = $builder->findAll();

        return view('admin/donation_expense/v_form', [
            'title'          => 'Tambah Pengeluaran Donasi',
            'donations'      => $donations,
            'selectedDonasi' => $idDonasi,
            'units'          => $units
        ]);
    }

    public function edit($id) {
        $expense = $this->pengeluaranModel->find($id);
        if (!$expense) {
            return redirect()->to('admin/donations')->with('error', 'Data pengeluaran tidak valid.');
        }

        $linkedDonation = $this->donasiModel->find($expense['id_donasi']);
        if (!$linkedDonation) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        if (!empty($linkedDonation['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $expense['id_donasi'])->with('error', 'Donasi ini sudah ditutup.');
        }

        $donations = $this->donasiModel
            ->where('closed_at', null)
            ->findAll();

        // Ambil data satuan unik (distinct)
        $units = $this->pengeluaranModel
            ->distinct()
            ->select('satuan')
            ->where('deleted_at', null)
            ->findAll();

        return view('admin/donation_expense/v_form', [
            'title'     => 'Edit Pengeluaran Donasi',
            'expense'   => $expense,
            'donations' => $donations,
            'units'     => $units
        ]);
    }

    public function save() {
        return $this->_store();
    }

    public function update($id) {
        return $this->_store($id);
    }

    private function _store($id = null) {
        $idDonasi = $this->request->getPost('id_donasi');

        $selectedDonation = $this->donasiModel->find($idDonasi);
        if (!$selectedDonation) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }
        if (!empty($selectedDonation['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $idDonasi)->with('error', 'Donasi ini sudah ditutup.');
        }

        $expenseLama = null;
        if ($id) {
            $expenseLama = $this->pengeluaranModel->find($id);
            if (!$expenseLama) {
                return redirect()->to('admin/donations')->with('error', 'Data pengeluaran tidak valid.');
            }
        }

        // 1. Validasi file bukti secara mandiri jika ada file diunggah
        $fileBukti = $this->request->getFile('bukti');
        if ($fileBukti && $fileBukti->isValid()) {
            $rulesFile = [
                'bukti' => 'ext_in[bukti,pdf,jpg,jpeg,png]|max_size[bukti,5120]'
            ];
            $messagesFile = [
                'bukti' => [
                    'ext_in'   => 'Bukti nota harus berformat PDF atau Gambar (JPG/JPEG/PNG).',
                    'max_size' => 'Ukuran file bukti nota maksimal 5MB.',
                ]
            ];
            if (!$this->validate($rulesFile, $messagesFile)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        $jumlah      = (float) $this->request->getPost('jumlah');
        $hargaSatuan = (float) $this->request->getPost('harga_satuan');
        $subTotal    = $jumlah * $hargaSatuan;

        // 2. Cek ketersediaan Saldo Program Donasi (Total Pemasukan - Total Pengeluaran)
        // Hitung total pemasukan yang sah untuk program ini
        $totalPemasukan = $this->pemasukanDonasiModel
            ->where('id_donasi', $idDonasi)
            ->where('tanggal IS NOT NULL', null, false)
            ->where('jumlah >', 0)
            ->selectSum('jumlah')
            ->get()
            ->getRow()->jumlah ?? 0;

        // Hitung total pengeluaran saat ini untuk program ini
        $pengeluaranQuery = $this->pengeluaranModel->where('id_donasi', $idDonasi);
        if ($id) {
            // Jika sedang edit, abaikan pengeluaran miliknya sendiri dari kalkulasi akumulasi
            $pengeluaranQuery->where('id_pengeluaran_donasi !=', $id);
        }
        $totalPengeluaranLainnya = $pengeluaranQuery
            ->selectSum('sub_total')
            ->get()
            ->getRow()->sub_total ?? 0;

        $saldoTersedia = (float) $totalPemasukan - (float) $totalPengeluaranLainnya;

        // Jika sub_total pengeluaran baru melebihi sisa saldo, tolak dengan flash error
        if ($subTotal > $saldoTersedia) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan! Saldo program donasi tidak mencukupi. Sisa saldo saat ini: Rp ' . number_format($saldoTersedia, 0, ',', '.'));
        }

        // 3. Proses Upload File Fisik jika ada file baru yang valid
        $namaBukti = $expenseLama ? $expenseLama['bukti'] : null;
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $namaBukti = $fileBukti->getRandomName();
            $fileBukti->move('uploads/donasi/bukti', $namaBukti);

            if ($expenseLama && !empty($expenseLama['bukti']) && file_exists('uploads/donasi/bukti/' . $expenseLama['bukti'])) {
                @unlink('uploads/donasi/bukti/' . $expenseLama['bukti']);
            }
        }

        $idAkun = session()->get('id_akun');

        // 4. Susun data untuk disimpan ke database
        $data = [
            'id_donasi'    => $idDonasi,
            'tanggal'      => $this->request->getPost('tanggal'),
            'keterangan'   => $this->request->getPost('keterangan'),
            'jumlah'       => $jumlah,
            'satuan'       => $this->request->getPost('satuan'),
            'harga_satuan' => $hargaSatuan,
            'sub_total'    => $subTotal,
            'bukti'        => $namaBukti,
        ];

        if (!$id) {
            $data['created_by'] = $idAkun;
            if (!$this->pengeluaranModel->insert($data)) {
                return redirect()->back()->withInput()->with('errors', $this->pengeluaranModel->errors());
            }
            $pesan = 'Pengeluaran donasi berhasil ditambahkan.';
        } else {
            $data['id_pengeluaran_donasi'] = $id;
            $data['updated_by'] = $idAkun;
            if (!$this->pengeluaranModel->update($id, $data)) {
                return redirect()->back()->withInput()->with('errors', $this->pengeluaranModel->errors());
            }
            $pesan = 'Perubahan pengeluaran donasi berhasil disimpan.';
        }

        return redirect()->to('admin/donations/detail/' . $idDonasi)->with('success', $pesan);
    }

    public function delete() {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id_pengeluaran_donasi');
            $expense = $this->pengeluaranModel->find($id);

            if (!$expense) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data pengeluaran tidak ditemukan.'
                ])->setStatusCode(404);
            }

            if (!empty($expense['id_donasi'])) {
                $donation = $this->donasiModel->find($expense['id_donasi']);

                if (!$donation) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data donasi tidak valid.'
                    ])->setStatusCode(400);
                }

                // 2. Validasi jika donasi sudah ditutup
                if (!empty($donation['closed_at'])) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Donasi ini sudah ditutup, pengeluaran tidak dapat dihapus.'
                    ])->setStatusCode(400);
                }
            }

            // Catat deleted_by sebelum soft delete
            $idAkun = session()->get('id_akun');
            $this->pengeluaranModel->update($id, ['deleted_by' => $idAkun]);

            if ($this->pengeluaranModel->delete($id)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Data pengeluaran berhasil dihapus.'
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus data pengeluaran.'
            ])->setStatusCode(500);
        }
    }
}