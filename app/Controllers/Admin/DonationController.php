<?php

namespace App\Controllers\Admin;

use App\Models\DonasiModel;
use App\Models\PemasukanDonasiModel;
use App\Models\PengeluaranDonasiModel;
use App\Models\HistoriStatusDonasiModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DonationController extends BaseController
{
    protected $donasiModel;
    protected $pemasukanModel;
    protected $pengeluaranModel;
    protected $historiStatusDonasiModel;

    public function __construct() {
        $this->donasiModel = new DonasiModel();
        $this->pemasukanModel = new PemasukanDonasiModel();
        $this->pengeluaranModel = new PengeluaranDonasiModel();
        $this->historiStatusDonasiModel = new HistoriStatusDonasiModel();
    }

    public function index()
    {
        return view('admin/donation/v_index', ['title' => 'Manajemen Program Donasi']);
    }

    public function list()
    {
        $status = $this->request->getPost('status');
        $builder = $this->donasiModel;

        if ($status !== '' && $status !== null) {
            $builder->where('status', $status);
        }

        $donations = $builder->orderBy('created_at', 'DESC')->findAll();
        
        // Database connection untuk sum manual
        $db = \Config\Database::connect();

        // Lakukan iterasi untuk mengecek dan menghitung total secara dinamis jika belum closed
        foreach ($donations as &$d) {
            if (empty($d['closed_at'])) {
                // Sum manual pemasukan yang aktif
                $d['total_pemasukan'] = $db->table('pemasukan_donasi')
                    ->selectSum('jumlah')
                    ->where('id_donasi', $d['id_donasi'])
                    ->where('deleted_at', null)
                    ->get()->getRow()->jumlah ?? 0;

                // Sum manual pengeluaran yang aktif
                $d['total_pengeluaran'] = $db->table('pengeluaran_donasi')
                    ->selectSum('sub_total')
                    ->where('id_donasi', $d['id_donasi'])
                    ->where('deleted_at', null)
                    ->get()->getRow()->sub_total ?? 0;
            }
        }
        unset($d);

        $data['donations'] = $donations;
        
        return view('admin/donation/v_list_partial', $data);
    }

    public function detail($id)
    {
        $donation = $this->donasiModel->find($id);

        if (!$donation) {
            return redirect()->to('admin/donations')->with('error', 'Gagal memuat! Program donasi tidak ditemukan.');
        }

        $data = $this->getDonationDetailData($id, $donation);
        $pengurusList = $this->historiStatusDonasiModel->distinct()->select('nama_pengurus')->findAll();

        // ==========================================
        // HITUNG DINAMIS JIKA BELUM CLOSED
        // ==========================================
        if (empty($donation['closed_at'])) {
            $db = \Config\Database::connect();

            // Sum manual pemasukan yang aktif
            $donation['total_pemasukan'] = $db->table('pemasukan_donasi')
                ->selectSum('jumlah')
                ->where('id_donasi', $id)
                ->where('deleted_at', null)
                ->get()->getRow()->jumlah ?? 0;

            // Sum manual pengeluaran yang aktif
            $donation['total_pengeluaran'] = $db->table('pengeluaran_donasi')
                ->selectSum('sub_total')
                ->where('id_donasi', $id)
                ->where('deleted_at', null)
                ->get()->getRow()->sub_total ?? 0;
        }

        return view('admin/donation/v_detail', [
            'title'             => 'Detail Program Donasi',
            'donation'          => $donation,
            'pengurusList'      => $pengurusList,
            'incomes'           => $data['incomes'],
            'candidateDonors'   => $data['candidateDonors'],
            'statusCounts'      => $data['statusCounts'],
            'expenses'          => $data['expenses'],
        ]);
    }

    public function partialDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('admin/donations');
        }

        $donation = $this->donasiModel->find($id);

        if (!$donation) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Program donasi tidak ditemukan.'
                ]);
        }

        $data = $this->getDonationDetailData($id, $donation);

        return $this->response->setJSON([
            'incomesHtml' => view(
                'admin/donation/partials/v_incomes',
                [
                    'incomes'  => $data['incomes'],
                    'donation' => $donation
                ]
            ),

            'candidatesHtml' => view(
                'admin/donation/partials/v_candidate_donors',
                [
                    'candidateDonors' => $data['candidateDonors'],
                    'donation'        => $donation,
                    'statusCounts'    => $data['statusCounts']
                ]
            ),

            'expensesHtml' => view(
                'admin/donation/partials/v_expenses',
                [
                    'expenses' => $data['expenses'],
                    'donation' => $donation
                ]
            ),

            'totalPemasukan' => 'Rp ' . number_format(
                $data['totalPemasukan'],
                0,
                ',',
                '.'
            ),

            'totalPengeluaran' => 'Rp ' . number_format(
                $data['totalPengeluaran'],
                0,
                ',',
                '.'
            ),

            'saldoAkhir' => 'Rp ' . number_format(
                $data['saldoAkhir'],
                0,
                ',',
                '.'
            )
        ]);
    }


    /**
     * Ambil seluruh data yang dibutuhkan halaman detail donasi.
     */
    private function getDonationDetailData($id, &$donation)
    {
        // ==========================================
        // DATA PEMASUKAN DONASI
        // ==========================================

        $allIncomes = $this->pemasukanModel
            ->select('
                pemasukan_donasi.*,
                donatur.nama AS nama_donatur,
                donatur.noreg AS noreg_donatur,
                donatur.telepon AS telepon_donatur,
                donatur.alamat AS alamat_donatur,
                metode_pemasukan.metode_pemasukan,
                status_donasi.id_status_donasi,
                status_donasi.status_donasi,
                status_donasi.class_color AS status_color
            ')
            ->join(
                'donatur',
                'donatur.id_donatur = pemasukan_donasi.id_donatur',
                'left'
            )
            ->join(
                'metode_pemasukan',
                'metode_pemasukan.id_metode_pemasukan = pemasukan_donasi.id_metode_pemasukan',
                'left'
            )
            ->join(
                'status_donasi',
                'status_donasi.id_status_donasi = pemasukan_donasi.id_status_donasi',
                'left'
            )
            ->where('pemasukan_donasi.id_donasi', $id)
            ->orderBy('pemasukan_donasi.tanggal', 'DESC')
            ->findAll();


        // ==========================================
        // RIWAYAT STATUS
        // ==========================================

        foreach ($allIncomes as &$income) {

            $income['histories'] = $this->historiStatusDonasiModel
                ->select('
                    histori_status_donasi.*,
                    status_donasi.status_donasi,
                    status_donasi.class_color
                ')
                ->join(
                    'status_donasi',
                    'status_donasi.id_status_donasi = histori_status_donasi.id_status_donasi',
                    'left'
                )
                ->where(
                    'histori_status_donasi.id_pemasukan_donasi',
                    $income['id_pemasukan_donasi']
                )
                ->orderBy('histori_status_donasi.waktu', 'DESC')
                ->orderBy('histori_status_donasi.id_status_donasi', 'DESC')
                ->findAll();
        }

        unset($income);


        // ==========================================
        // PEMISAHAN PEMASUKAN
        // ==========================================

        $incomes = [];
        $candidateDonors = [];

        foreach ($allIncomes as $income) {

            $isComplete =
                !empty($income['tanggal']) &&
                !empty($income['jumlah']) &&
                (float) $income['jumlah'] > 0 &&
                !empty($income['id_metode_pemasukan']);

            // Pemasukan yang sudah lengkap
            if ($isComplete) {
                $incomes[] = $income;
                continue;
            }

            // Candidate donor hanya untuk status:
            // 1 = Menunggu
            // 2 = ...
            // 3 = ...
            // 5 = ...
            // 6 = ...
            $allowedCandidateStatus = [1, 2, 3, 5, 6];

            if (
                in_array(
                    (int) ($income['id_status_donasi'] ?? 0),
                    $allowedCandidateStatus,
                    true
                )
            ) {
                $candidateDonors[] = $income;
            }
        }


        // ==========================================
        // COUNT STATUS CANDIDATE DONOR
        // ==========================================

        $statusCounts = [];

        foreach ($candidateDonors as $candidate) {

            $statusName = $candidate['status_donasi'] ?? 'Menunggu';

            if (!isset($statusCounts[$statusName])) {
                $statusCounts[$statusName] = 0;
            }

            $statusCounts[$statusName]++;
        }


        // ==========================================
        // PENGELUARAN
        // ==========================================

        $expenses = $this->pengeluaranModel
            ->where('id_donasi', $id)
            ->orderBy('tanggal', 'DESC')
            ->findAll();


        // ==========================================
        // TOTAL KEUANGAN
        // ==========================================

        $db = \Config\Database::connect();

        $totalPemasukan = $db->table('pemasukan_donasi')
            ->selectSum('jumlah')
            ->where('id_donasi', $id)
            ->where('deleted_at', null)
            ->get()
            ->getRow()
            ->jumlah ?? 0;

        $totalPengeluaran = $db->table('pengeluaran_donasi')
            ->selectSum('sub_total')
            ->where('id_donasi', $id)
            ->where('deleted_at', null)
            ->get()
            ->getRow()
            ->sub_total ?? 0;

        $saldoAkhir = $totalPemasukan - $totalPengeluaran;


        return [
            'incomes'         => array_values($incomes),
            'candidateDonors' => array_values($candidateDonors),
            'statusCounts'    => $statusCounts,
            'expenses'        => $expenses,

            'totalPemasukan'  => $totalPemasukan,
            'totalPengeluaran'=> $totalPengeluaran,
            'saldoAkhir'      => $saldoAkhir
        ];
    }

    public function create() {
        return view('admin/donation/v_form', ['title' => 'Tambah Program Donasi']);
    }

    public function edit($id) {
        $donation = $this->donasiModel->find($id);
        if (!$donation) {
            return redirect()->to('admin/donations')->with('error', 'Gagal memuat! Program donasi tidak ditemukan.');
        }

        return view('admin/donation/v_form', [
            'title'    => 'Edit Program Donasi',
            'donation' => $donation
        ]);
    }

    public function save() {
        return $this->_store();
    }

    public function update($id) {
        $donation = $this->donasiModel->find($id);
        if (!$donation) {
            return redirect()->to('admin/donations')->with('error', 'Gagal memperbarui! Program donasi tidak ditemukan.');
        }
        
        return $this->_store($id);
    }

    private function _store($id = null) {
        $donationLama = $id ? $this->donasiModel->find($id) : null;

        // 1. Susun data lengkap yang akan divalidasi dan disimpan
        $dataSimpan = [
            'judul'            => $this->request->getPost('judul'),
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'akronim_kwitansi' => $this->request->getPost('akronim_kwitansi'),
            'jenis_donasi'     => $this->request->getPost('jenis_donasi'),
            'status'           => $this->request->getPost('status') ? 'aktif' : 'pasif',
        ];

        // Sertakan id_donasi jika sedang proses edit (untuk aturan is_unique di model)
        if ($id) {
            $dataSimpan['id_donasi'] = $id;
        }

        // 2. Siapkan aturan validasi tambahan khusus file upload
        $rulesFile = [];
        $fileProposal = $this->request->getFile('proposal');
        if ($fileProposal && $fileProposal->isValid()) {
            $rulesFile['proposal'] = 'ext_in[proposal,pdf]|max_size[proposal,5120]';
        }

        $fileLaporan = $this->request->getFile('laporan');
        if ($fileLaporan && $fileLaporan->isValid()) {
            $rulesFile['laporan'] = 'ext_in[laporan,pdf]|max_size[laporan,5120]';
        }

        $messagesFile = [
            'proposal' => [
                'ext_in'   => 'File proposal harus berformat PDF.',
                'max_size' => 'Ukuran file proposal maksimal 5MB.',
            ],
            'laporan' => [
                'ext_in'   => 'File laporan harus berformat PDF.',
                'max_size' => 'Ukuran file laporan maksimal 5MB.',
            ],
        ];

        // 3. Jalankan validasi file (jika ada file diunggah)
        if (!empty($rulesFile) && !$this->validate($rulesFile, $messagesFile)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Jalankan validasi utama menggunakan model
        if (!$this->donasiModel->skipValidation(false)->validate($dataSimpan)) {
            return redirect()->back()->withInput()->with('errors', $this->donasiModel->errors());
        }

        // 5. Proses Upload File Proposal jika ada file baru yang valid
        $namaProposal = $donationLama ? $donationLama['proposal'] : null;
        if ($fileProposal && $fileProposal->isValid() && !$fileProposal->hasMoved()) {
            $namaProposal = $fileProposal->getRandomName();
            $fileProposal->move('uploads/donasi/proposal', $namaProposal);
            
            if ($donationLama && !empty($donationLama['proposal']) && file_exists('uploads/donasi/proposal/' . $donationLama['proposal'])) {
                @unlink('uploads/donasi/proposal/' . $donationLama['proposal']);
            }
        }

        // 6. Proses Upload File Laporan jika ada file baru yang valid
        $namaLaporan = $donationLama ? $donationLama['laporan'] : null;
        if ($fileLaporan && $fileLaporan->isValid() && !$fileLaporan->hasMoved()) {
            $namaLaporan = $fileLaporan->getRandomName();
            $fileLaporan->move('uploads/donasi/laporan', $namaLaporan);
            
            if ($donationLama && !empty($donationLama['laporan']) && file_exists('uploads/donasi/laporan/' . $donationLama['laporan'])) {
                @unlink('uploads/donasi/laporan/' . $donationLama['laporan']);
            }
        }

        // Masukkan nama file yang sudah diproses ke dalam array `$dataSimpan`
        $dataSimpan['proposal'] = $namaProposal;
        $dataSimpan['laporan']  = $namaLaporan;

        $idAkun = session()->get('id_akun');

        // 7. Simpan ke database dengan menyertakan created_by / updated_by
        if (!$id) {
            unset($dataSimpan['id_donasi']);
            $dataSimpan['created_by'] = $idAkun;

            if (!$this->donasiModel->skipValidation(true)->insert($dataSimpan)) {
                return redirect()->back()->withInput()->with('errors', $this->donasiModel->errors());
            }
            $pesan = 'Program donasi baru berhasil dibuat.';
        } else {
            $dataSimpan['updated_by'] = $idAkun;

            if (!$this->donasiModel->skipValidation(true)->update($id, $dataSimpan)) {
                return redirect()->back()->withInput()->with('errors', $this->donasiModel->errors());
            }
            $pesan = 'Perubahan program donasi berhasil disimpan.';
        }

        return redirect()->to('admin/donations')->with('success', $pesan);
    }

    public function delete() {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id_donasi');
            $donation = $this->donasiModel->find($id);

            if (!$donation) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Program donasi tidak ditemukan.'
                ])->setStatusCode(404);
            }
            if (!empty($donation['closed_at'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Program donasi yang sudah ditutup tidak dapat dihapus.'
                ])->setStatusCode(400);
            }

            // Catat deleted_by sebelum melakukan soft delete
            $idAkun = session()->get('id_akun');
            $this->donasiModel->update($id, ['deleted_by' => $idAkun]);

            if ($this->donasiModel->delete($id)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Program donasi berhasil dihapus.'
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus program donasi.'
            ])->setStatusCode(500);
        }
    }

    public function close() {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id_donasi');
            $donation = $this->donasiModel->find($id);

            if (!$donation) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Program donasi tidak ditemukan.'
                ])->setStatusCode(404);
            }

            if (!empty($donation['closed_at'])) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Program donasi sudah ditutup sebelumnya.'
                ]);
            }

            $userId = session()->get('id_akun') ?? null;
            $result = $this->donasiModel->tutupDonasi($id, $userId);

            if ($result['status'] === 'error') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $result['message']
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $result['message']
            ]);
        }
    }
}