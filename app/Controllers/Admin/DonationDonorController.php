<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DonasiModel;
use App\Models\DonaturModel;
use App\Models\PemasukanDonasiModel;
use App\Models\StatusDonasiModel;
use App\Models\HistoriStatusDonasiModel;
use App\Models\MetodePemasukanModel;

class DonationDonorController extends BaseController
{
    protected $donasiModel;
    protected $donaturModel;
    protected $pemasukanModel;
    protected $statusDonasiModel;
    protected $historiModel;
    protected $metodePemasukanModel;

    public function __construct()
    {
        $this->donasiModel       = new DonasiModel();
        $this->donaturModel      = new DonaturModel();
        $this->pemasukanModel    = new PemasukanDonasiModel();
        $this->statusDonasiModel = new StatusDonasiModel();
        $this->historiModel      = new HistoriStatusDonasiModel();
        $this->metodePemasukanModel      = new MetodePemasukanModel();
    }

    public function create($idDonasi = null)
    {
        $donasi = $this->donasiModel->find($idDonasi);
        if (!$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }
        if (!empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $idDonasi)->with('error', 'Donasi ini sudah ditutup.');
        }

        // Ambil data distinct nama_pengurus dari histori untuk pilihan select2
        $pengurusList = $this->historiModel->distinct()->select('nama_pengurus')->findAll();

        // Hanya ambil status 1, 2, atau 3 untuk input awal list donatur/calon donatur
        $statuses = $this->statusDonasiModel->whereIn('id_status_donasi', [1, 2, 3])->findAll();

        return view('admin/donation_donor/v_form', [
            'title'           => 'Tambah List Donatur',
            'donasi'          => $donasi,
            'selectedDonasi'  => $idDonasi,
            'donaturList'     => $this->donaturModel->findAll(),
            'statuses'        => $statuses,
            'pengurusList'    => $pengurusList,
        ]);
    }

    public function edit($id)
    {
        $pemasukan = $this->pemasukanModel->find($id);
        if (!$pemasukan) {
            return redirect()->to('admin/donations')->with('error', 'Data list donatur tidak valid.');
        }

        $donasi = $this->donasiModel->find($pemasukan['id_donasi']);
        if (!$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        if (!empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Donasi ini sudah ditutup.');
        }

        if (in_array($pemasukan['id_status_donasi'], [4, 5, 6])) {
            $statusPesan = '';
            
            switch ($pemasukan['id_status_donasi']) {
                case 4:
                    $statusPesan = 'Pencatatan Dana';
                    break;
                case 5:
                    $statusPesan = 'Dikembalikan / Gagal Kirim';
                    break;
                case 6:
                    $statusPesan = 'Dibatalkan';
                    break;
            }

            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Data list sudah tidak dapat diedit karena statusnya ' . $statusPesan);
        }

        // Ambil histori status terkait pemasukan ini untuk mendapatkan data waktu & nama_pengurus terakhir
        $histori = $this->historiModel->where('id_pemasukan_donasi', $id)->orderBy('id_histori_status_donasi', 'DESC')->first();

        $pengurusList = $this->historiModel->distinct()->select('nama_pengurus')->findAll();

        $currentStatus = (int) $pemasukan['id_status_donasi'];
        $statuses = $this->statusDonasiModel->where('id_status_donasi', $currentStatus)->findAll();

        return view('admin/donation_donor/v_form', [
            'title'           => 'Edit List Donatur',
            'pemasukan'       => $pemasukan,
            'donasi'          => $donasi,
            'donaturList'     => $this->donaturModel->findAll(),
            'statuses'        => $statuses,
            'pengurusList'    => $pengurusList,
            'histori'         => $histori,
            'donaturSelected' => $this->donaturModel->find($pemasukan['id_donatur'])
        ]);
    }

    public function save()
    {
        return $this->_store();
    }

    public function update($id)
    {
        $pemasukan = $this->pemasukanModel->find($id);
        if (!$pemasukan) {
            return redirect()->to('admin/donations')->with('error', 'Data list donatur tidak valid.');
        }

        $donasi = $this->donasiModel->find($pemasukan['id_donasi']);
        if (!$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        if (!empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Donasi ini sudah ditutup.');
        }

        if (in_array($pemasukan['id_status_donasi'], [4, 5, 6])) {
            $statusPesan = '';
            
            switch ($pemasukan['id_status_donasi']) {
                case 4:
                    $statusPesan = 'Pencatatan Dana';
                    break;
                case 5:
                    $statusPesan = 'Dikembalikan / Gagal Kirim';
                    break;
                case 6:
                    $statusPesan = 'Dibatalkan';
                    break;
            }

            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Data list sudah tidak dapat diedit karena statusnya ' . $statusPesan);
        }

        return $this->_store($id);
    }

    private function _store($id = null)
    {
        $idDonasi = $this->request->getPost('id_donasi');
        $donasi = $this->donasiModel->find($idDonasi);
        if (!$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        if (!empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Donasi ini sudah ditutup.');
        }

        $inputDonatur = trim($this->request->getPost('id_donatur'));
        $idDonatur = null;

        if (is_numeric($inputDonatur)) {
            $idDonatur = (int) $inputDonatur;
            $cekDonatur = $this->donaturModel->find($idDonatur);
            if (!$cekDonatur) {
                $idDonatur = null;
            }
        }

        if (empty($idDonatur) && !empty($inputDonatur)) {
            $datePrefix = date('ymd');
            do {
                $randomDigits = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
                $generatedNoreg = $datePrefix . $randomDigits;
                $exists = $this->donaturModel->where('noreg', $generatedNoreg)->first();
            } while ($exists);

            $dataDonatur = [
                'nama'       => $inputDonatur,
                'noreg'      => $generatedNoreg,
                'token'      => bin2hex(random_bytes(32)),
                'created_by' => session()->get('id_akun'),
            ];

            $this->donaturModel->insert($dataDonatur);
            $idDonatur = $this->donaturModel->getInsertID();
        }

        if (empty($idDonatur) ) {
            return redirect()->back()->withInput()->with('errors', [
                'id_donatur' => 'Data donatur wajib dipilih atau diisi.'
            ]);
        }

        $existingCandidate = $this->pemasukanModel
            ->where('id_donasi', $idDonasi)
            ->where('id_donatur', $idDonatur)
            ->when($id, function($query) use ($id) {
                return $query->where('id_pemasukan_donasi !=', $id);
            })
            ->first();

        if ($existingCandidate) {
            $statusAktif = (int) $existingCandidate['id_status_donasi'];
            // Jika statusnya 1, 2, atau 3 (dianggap masih tertunda/calon donatur aktif)
            if (in_array($statusAktif, [1, 2, 3])) {
                return redirect()->back()->withInput()->with('errors', [
                    'id_donatur' => 'Donatur ini sudah terdaftar sebagai list calon donatur dalam program ini.'
                ]);
            }
        }
        // ==========================================

        $idStatusDonasi = $this->request->getPost('id_status_donasi') ?? 1;
        $waktu          = $this->request->getPost('waktu') ?: date('Y-m-d H:i:s');
        $namaPengurus   = $this->request->getPost('nama_pengurus') ?: (session()->get('nama') ?? 'Administrator');
        $idAkun         = session()->get('id_akun');

        // Tangani input nomor kwitansi (user hanya input nomor, digabungkan dengan akronim)
        $inputNoKwitansi = trim($this->request->getPost('no_kwitansi'));
        $akronim = $donasi['akronim_kwitansi'] ?? 'ALMNR';

        if (!empty($inputNoKwitansi)) {
            $nomorNormalisasi = preg_replace('/^' . preg_quote($akronim, '/') . '-/i', '', $inputNoKwitansi);
            $noKwitansi = $akronim . '-' . ltrim($nomorNormalisasi, '-');
        } else {
            // Auto generate nomor berikutnya jika dikosongkan (TAMBAHKAN withDeleted() DI SINI)
            $lastRecord = $this->pemasukanModel
                ->withDeleted()
                ->like('no_kwitansi', $akronim, 'after')
                ->orderBy('id_pemasukan_donasi', 'DESC')
                ->first();
            
            $nextNumber = 1;
            if ($lastRecord && !empty($lastRecord['no_kwitansi'])) {
                $parts = explode('-', $lastRecord['no_kwitansi']);
                $numPart = end($parts);
                if (is_numeric($numPart)) {
                    $nextNumber = ((int) $numPart) + 1;
                }
            }
            $noKwitansi = $akronim . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        $dataPemasukan = [
            'id_donasi'        => (int) $idDonasi,
            'id_donatur'       => $idDonatur,
            'id_status_donasi' => (int) $idStatusDonasi,
            'no_kwitansi'      => $noKwitansi,
            'samarkan'         => 'N',
        ];


        if (!$id) {
            $dataPemasukan['created_by'] = $idAkun;
            if (!$this->pemasukanModel->insert($dataPemasukan)) {
                return redirect()->back()->withInput()->with('errors', $this->pemasukanModel->errors());
            }
            $idPemasukanBaru = $this->pemasukanModel->getInsertID();

            // 1. Data baru: selalu catat histori pertama
            $this->historiModel->insert([
                'id_status_donasi'    => $idStatusDonasi,
                'id_pemasukan_donasi' => $idPemasukanBaru,
                'waktu'               => $waktu,
                'nama_pengurus'       => $namaPengurus,
                'created_by'          => $idAkun,
            ]);

            $pesan = 'List donatur berhasil ditambahkan.';
        } else {
            $dataPemasukan['id_pemasukan_donasi'] = $id;
            $dataPemasukan['updated_by'] = $idAkun;

            if (!$this->pemasukanModel->update($id, $dataPemasukan)) {
                return redirect()->back()->withInput()->with('errors', $this->pemasukanModel->errors());
            }

            // 2. Ambil histori status terakhir yang tercatat di database untuk pemasukan ini
            $lastHistori = $this->historiModel
                ->where('id_pemasukan_donasi', $id)
                ->orderBy('id_histori_status_donasi', 'DESC')
                ->first();

            $statusSama  = $lastHistori && ((int) $lastHistori['id_status_donasi'] === (int) $idStatusDonasi);
            $waktuSama   = $lastHistori && ($lastHistori['waktu'] === $waktu);
            $pengurusSama = $lastHistori && ($lastHistori['nama_pengurus'] === $namaPengurus);

            if ($statusSama) {
                // Jika statusnya masih sama persis
                if (!$waktuSama || !$pengurusSama) {
                    $this->historiModel->update($lastHistori['id_histori_status_donasi'], [
                        'waktu'      => $waktu,
                        'nama_pengurus' => $namaPengurus,
                        'updated_by' => $idAkun,
                    ]);
                }
            } else {
                $this->historiModel->insert([
                    'id_status_donasi'    => $idStatusDonasi,
                    'id_pemasukan_donasi' => $id,
                    'waktu'               => $waktu,
                    'nama_pengurus'       => $namaPengurus,
                    'created_by'          => $idAkun,
                ]);
            }

            $pesan = 'List donatur berhasil diperbarui.';
        }

        return redirect()->to('admin/donations/detail/' . $idDonasi)->with('success', $pesan);
    }

    public function getStatusOptions($idPemasukan)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $pemasukan = $this->pemasukanModel->find($idPemasukan);
        if (!$pemasukan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data list donatur tidak valid.'])->setStatusCode(404);
        }

        $donasi = $this->donasiModel->find($pemasukan['id_donasi']);
        if (!$donasi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data donasi tidak valid.'])->setStatusCode(400);
        }

        if (!empty($donasi['closed_at'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Donasi ini sudah ditutup.'])->setStatusCode(400);
        }

        if (in_array($pemasukan['id_status_donasi'], [4, 5, 6])) {
            $statusPesan = '';
            switch ($pemasukan['id_status_donasi']) {
                case 4: $statusPesan = 'Pencatatan Dana'; break;
                case 5: $statusPesan = 'Dikembalikan / Gagal Kirim'; break;
                case 6: $statusPesan = 'Dibatalkan'; break;
            }
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Data list sudah tidak dapat diedit karena statusnya ' . $statusPesan
            ])->setStatusCode(400);
        }

        $currentStatus = (int) $pemasukan['id_status_donasi'];
        
        $allowedIds = [];
        if ($currentStatus === 1) {
            $allowedIds = [2, 3, 5, 6];
        } elseif ($currentStatus === 2) {
            $allowedIds = [3, 5, 6];
        } elseif ($currentStatus === 3) {
            $allowedIds = [4, 6];
        }

        $statuses = [];
        if (!empty($allowedIds)) {
            $statuses = $this->statusDonasiModel->whereIn('id_status_donasi', $allowedIds)->findAll();
        }

        $metodeList = $this->metodePemasukanModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'current_status' => $currentStatus,
            'allowed_statuses' => $statuses,
            'metode_list' => $metodeList,
            'default_waktu' => date('Y-m-d\TH:i'),
            'default_pengurus' => session()->get('nama') ?? 'Administrator'
        ]);
    }

    public function updateStatus($idPemasukan)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $pemasukan = $this->pemasukanModel->find($idPemasukan);
        if (!$pemasukan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data list donatur tidak valid.'])->setStatusCode(404);
        }

        $donasi = $this->donasiModel->find($pemasukan['id_donasi']);
        if (!$donasi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data donasi tidak valid.'])->setStatusCode(400);
        }

        if (!empty($donasi['closed_at'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Donasi ini sudah ditutup.'])->setStatusCode(400);
        }

        if (in_array($pemasukan['id_status_donasi'], [4, 5, 6])) {
            $statusPesan = '';
            switch ($pemasukan['id_status_donasi']) {
                case 4: $statusPesan = 'Pencatatan Dana'; break;
                case 5: $statusPesan = 'Dikembalikan / Gagal Kirim'; break;
                case 6: $statusPesan = 'Dibatalkan'; break;
            }
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Data list sudah tidak dapat diedit karena statusnya ' . $statusPesan
            ])->setStatusCode(400);
        }

        $currentStatus = (int) $pemasukan['id_status_donasi'];
        $newStatus = (int) $this->request->getPost('id_status_donasi');

        // ==========================================
        // VALIDASI ALLOWED IDS (MENCEGAH STATUS ILLEGAL)
        // ==========================================
        $allowedIds = [];
        if ($currentStatus === 1) {
            $allowedIds = [2, 3, 5, 6];
        } elseif ($currentStatus === 2) {
            $allowedIds = [3, 5, 6];
        } elseif ($currentStatus === 3) {
            $allowedIds = [4, 6];
        }

        if (!in_array($newStatus, $allowedIds, true)) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Pilihan status baru tidak diizinkan.'
            ])->setStatusCode(400);
        }
        // ==========================================

        $waktu = $this->request->getPost('waktu') ?: date('Y-m-d H:i:s');
        $namaPengurus = $this->request->getPost('nama_pengurus') ?: (session()->get('nama') ?? 'Administrator');
        $idAkun = session()->get('id_akun');

        $updateData = [
            'id_status_donasi' => $newStatus,
            'updated_by'       => $idAkun
        ];

        if (!$this->pemasukanModel->update($idPemasukan, $updateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui status donasi.'])->setStatusCode(500);
        }

        $this->historiModel->insert([
            'id_status_donasi'    => $newStatus,
            'id_pemasukan_donasi' => $idPemasukan,
            'waktu'               => $waktu,
            'nama_pengurus'       => $namaPengurus,
            'created_by'          => $idAkun
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Status donasi berhasil diperbarui.'
        ]);
    }
}