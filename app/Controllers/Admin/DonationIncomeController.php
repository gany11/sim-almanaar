<?php
/**
 *   (1) record($id_pemasukan_donasi)   -> perubahan status/calon donatur, mengisi
 *                                          data finansial dari row yg SUDAH ADA
 *                                          (donatur & donasi sudah fix, jumlah/
 *                                          metode/bukti masih kosong)
 *   (2) donorRecord($id_donatur)       -> dari profil donatur, donatur fix, tapi
 *                                          user memilih dulu jenis pencatatan:
 *                                          donasi* / kas** / tercatat***
 *   (3) create($id_donasi)             -> dari tombol "Tambah Pemasukan" di dalam
 *                                          program donasi tertentu, donasi fix,
 *                                          menampilkan SEMUA donatur; jika donatur
 *                                          terpilih sudah aktif di program ini,
 *                                          kwitansi lama otomatis dipakai ulang
 *   (4) edit($id_pemasukan_donasi)     -> edit penuh atas row yang sudah lengkap
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemasukanDonasiModel;
use App\Models\HistoriStatusDonasiModel;
use App\Models\DonaturModel;
use App\Models\DonasiModel;
use App\Models\StatusDonasiModel;
use App\Models\MetodePemasukanModel;
use App\Models\KategoriKeuanganModel;
use App\Models\AlokasiModel;
use App\Models\DetailAlokasiModel;
use App\Models\KeuanganModel;
use App\Models\LaporanMingguanModel;
use App\Services\WhatsAppService;

use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;

class DonationIncomeController extends BaseController
{
    protected PemasukanDonasiModel $pemasukanModel;
    protected HistoriStatusDonasiModel $historiModel;
    protected DonaturModel $donaturModel;
    protected DonasiModel $donasiModel;
    protected StatusDonasiModel $statusDonasiModel;
    protected MetodePemasukanModel $metodeModel;
    protected KategoriKeuanganModel $kategoriModel;
    protected AlokasiModel $alokasiModel;
    protected DetailAlokasiModel $detailAlokasiModel;
    protected KeuanganModel $keuanganModel;
    protected LaporanMingguanModel $laporanModel;
    protected WhatsAppService $whatsappService;

    /** Akronim kwitansi default masjid, dipakai utk tipe kas & tercatat */
    protected string $akronimDefault = 'ALMNR';

    /** id_status_donasi yang dianggap kandidat/berjalan aktif */
    protected array $statusAktif = [1, 2, 3];

    /** id_status_donasi yang dikunci pada pintu (1) record() -> "pencatatan dana" */
    protected int $statusLunasId = 4;

    /** Folder upload bukti (relatif terhadap FCPATH) */
    protected string $uploadPath = 'uploads/donasi';

    public function __construct()
    {
        $this->pemasukanModel     = new PemasukanDonasiModel();
        $this->historiModel       = new HistoriStatusDonasiModel();
        $this->donaturModel       = new DonaturModel();
        $this->donasiModel        = new DonasiModel();
        $this->statusDonasiModel  = new StatusDonasiModel();
        $this->metodeModel        = new MetodePemasukanModel();
        $this->kategoriModel      = new KategoriKeuanganModel();
        $this->alokasiModel       = new AlokasiModel();
        $this->detailAlokasiModel = new DetailAlokasiModel();
        $this->keuanganModel      = new KeuanganModel();
        $this->laporanModel       = new LaporanMingguanModel();
        $this->whatsappService    = new WhatsAppService();
    }

    // =========================================================================
    // (1) GET admin/donation-incomes/record/(:num)
    // =========================================================================
    public function record($id_pemasukan_donasi = null)
    {
        $pemasukan = $this->pemasukanModel->find($id_pemasukan_donasi);
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

        if (in_array($pemasukan['id_status_donasi'], [5, 6])) {
            $statusPesan = '';
            
            switch ($pemasukan['id_status_donasi']) {
                case 5:
                    $statusPesan = 'Dikembalikan / Gagal Kirim';
                    break;
                case 6:
                    $statusPesan = 'Dibatalkan';
                    break;
            }

            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Data list sudah tidak dapat diedit karena statusnya ' . $statusPesan);
        }

        $donatur = $this->donaturModel->find($pemasukan['id_donatur']);
        if (!$donatur) {
            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Data donatur tidak valid.');
        }

        return $this->showForm('record', [
            'pemasukan' => $pemasukan,
            'donatur'   => $donatur,
            'donasi'    => $donasi,
        ]);
    }

    // =========================================================================
    // (2) GET admin/donors/record/(:num)
    // =========================================================================
    public function donorRecord($id_donatur = null)
    {
        $donatur = $this->donaturModel->find($id_donatur);
        if (!$donatur) {
            return redirect()->to('admin/donors')->with('error', 'Gagal memperbarui! Data donatur tidak ditemukan.');
        }

        // Daftar program donasi yang masih tersedia (belum dikunci) -> opsi tipe "donasi"
        $donasiTersedia = $this->donasiModel
            ->where('closed_at', null)
            ->orderBy('id_donasi', 'DESC')
            ->findAll();

        // no_kwitansi aktif milik donatur ini per program donasi (kalau donatur ini
        // SUDAH terdaftar aktif di program tsb), supaya saat dipilih di tipe "donasi",
        // kwitansi lama dipakai lagi -> bukan generate baru.
        $registrasiAktif = $this->pemasukanModel
            ->select('id_donasi, no_kwitansi')
            ->where('id_donatur', $id_donatur)
            ->whereIn('id_status_donasi', $this->statusAktif)
            ->findAll();

        $kwitansiPerDonasi = [];
        foreach ($registrasiAktif as $row) {
            // Ambil yang pertama saja per program (asumsi 1 kwitansi aktif/donatur/donasi)
            $kwitansiPerDonasi[$row['id_donasi']] ??= $row['no_kwitansi'];
        }

        foreach ($donasiTersedia as &$dn) {
            $dn['no_kwitansi_aktif'] = $kwitansiPerDonasi[$dn['id_donasi']] ?? null;
        }
        unset($dn);

        return $this->showForm('donor_record', [
            'donatur'        => $donatur,
            'donasiTersedia' => $donasiTersedia,
        ]);
    }

    // =========================================================================
    // (3) GET admin/donation-incomes/create/(:num)
    // =========================================================================
    public function create($id_donasi = null)
    {
        $donasi = $this->donasiModel->find($id_donasi);
        if (!$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        if (!empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $idDonasi)->with('error', 'Donasi ini sudah ditutup.');
        }

        // no_kwitansi aktif per donatur untuk program donasi ini (kalau donatur tsb
        // SUDAH terdaftar aktif/status 1,2,3), supaya saat dipilih lagi, kwitansi
        // lama dipakai lagi -> bukan generate baru.
        $registrasiAktif = $this->pemasukanModel
            ->select('id_donatur, no_kwitansi')
            ->where('id_donasi', $id_donasi)
            ->whereIn('id_status_donasi', $this->statusAktif)
            ->findAll();

        $kwitansiPerDonatur = [];
        foreach ($registrasiAktif as $row) {
            $kwitansiPerDonatur[$row['id_donatur']] ??= $row['no_kwitansi'];
        }

        // Tampilkan SEMUA donatur (tidak difilter lagi)
        $semuaDonatur = $this->donaturModel->orderBy('nama', 'ASC')->findAll();

        foreach ($semuaDonatur as &$d) {
            $d['no_kwitansi_aktif'] = $kwitansiPerDonatur[$d['id_donatur']] ?? null;
        }
        unset($d);

        return $this->showForm('create', [
            'donasi'       => $donasi,
            'calonDonatur' => $semuaDonatur,
        ]);
    }

    // =========================================================================
    // (4) GET admin/donation-incomes/edit/(:num)
    // =========================================================================
    public function edit($id_pemasukan_donasi = null)
    {
        $pemasukan = $this->pemasukanModel->find($id_pemasukan_donasi);
        if (!$pemasukan) {
            return redirect()->to('admin/donations')->with('error', 'Data transaksi donasi tidak valid.');
        }

        $donatur = $this->donaturModel->find($pemasukan['id_donatur']);
        if (!$donatur) {
            $redirectTo = $pemasukan['id_donasi'] ? 'admin/donations/detail/' . $pemasukan['id_donasi'] : 'admin/donors/detail/' . $pemasukan['id_donatur'];
            return redirect()->to($redirectTo)->with('error', 'Data donatur tidak valid.');
        }

        $donasi = $pemasukan['id_donasi'] ? $this->donasiModel->find($pemasukan['id_donasi']) : null;

        if (!empty($pemasukan['id_donasi']) && !$donasi) {
            return redirect()->to('admin/donations')->with('error', 'Data donasi tidak valid.');
        }

        // Skenario A: Jika memiliki id_donasi dan donasinya sudah ditutup
        if ($donasi && !empty($donasi['closed_at'])) {
            return redirect()->to('admin/donations/detail/' . $pemasukan['id_donasi'])->with('error', 'Donasi ini sudah ditutup.');
        }

        // Skenario B: Jika TIDAK memiliki id_donasi, cek batas waktu created_at (Lewat Kamis 23:59 / Jumat 00:00)
        if (empty($pemasukan['id_donasi']) && !empty($pemasukan['created_at'])) {
            $createdAt = strtotime($pemasukan['created_at']);
            
            // 1. Cari hari Jumat (pukul 00:00:00) dari tanggal data dibuat
            $jumatData = strtotime('friday this week 00:00:00', $createdAt);
            
            // 2. Jika data dibuat pada hari Jumat atau setelahnya (hingga Kamis malam minggu itu),
            //    maka batas kuncinya adalah Hari Jumat di MINGGU DEPANNYA.
            if ($createdAt >= $jumatData) {
                $batasKunci = strtotime('+1 week', $jumatData);
            } else {
                // Jika dibuat sebelum hari Jumat (misal dini hari Jumat sebelum jam 00:00, atau penyesuaian lain)
                $batasKunci = $jumatData;
            }
            
            // 3. Jika waktu saat ini sudah melewati atau pas batas Jumat minggu depannya
            if (time() >= $batasKunci) {
                return redirect()->to('admin/donors/detail/' . $pemasukan['id_donatur'])->with('error', 'Batas waktu edit untuk pemasukan tanpa program donasi telah lewat (maksimal hari Kamis di minggu berikutnya).');
            }
        }

        // Validasi status: Jika status bukan 4, tidak boleh diedit
        if ($pemasukan['id_status_donasi'] != 4) {
            $redirectTo = $pemasukan['id_donasi'] ? 'admin/donations/detail/' . $pemasukan['id_donasi'] : 'admin/donors/detail/' . $pemasukan['id_donatur'];
            return redirect()->to($redirectTo)->with('error', 'Data list sudah tidak dapat diedit karena statusnya bukan Pencatatan Dana.');
        }

        return $this->showForm('edit', [
            'pemasukan' => $pemasukan,
            'donatur'   => $donatur,
            'donasi'    => $donasi,
        ]);
    }

    // =========================================================================
    // Helper render form terpusat
    // =========================================================================
    private function showForm(string $source, array $extra = [])
    {
        /*
        * Tentukan jenis pencatatan
        *
        * donor_record:
        *   default = donasi
        *   dapat berubah melalui radio di frontend
        *
        * create:
        *   selalu donasi
        *
        * record:
        *   merupakan pencatatan dari pemasukan donasi yang sudah ada
        *   sehingga dianggap donasi
        *
        * edit:
        *   jika id_donasi ada     = donasi
        *   jika id_donasi kosong:
        *       ada data keuangan  = kas
        *       tidak ada          = tercatat
        */

        $jenisPencatatan = 'donasi';
        $keuangan = null;

        if ($source === 'edit' && !empty($extra['pemasukan'])) {

            $pemasukan = $extra['pemasukan'];

            /*
            |--------------------------------------------------------------------------
            | DONASI
            |--------------------------------------------------------------------------
            |
            | Jika memiliki id_donasi berarti jenisnya DONASI.
            |
            */

            if (!empty($pemasukan['id_donasi'])) {

                $jenisPencatatan = 'donasi';

            } else {

                /*
                |--------------------------------------------------------------------------
                | TANPA PROGRAM DONASI
                |--------------------------------------------------------------------------
                |
                | Cek apakah memiliki data di tabel keuangan.
                |
                */

                $keuangan = $this->keuanganModel
                    ->where(
                        'id_pemasukan_donasi',
                        $pemasukan['id_pemasukan_donasi']
                    )
                    ->first();

                if ($keuangan) {

                    $keuangan['id_alokasi'] = $this->detailAlokasiModel
                                                ->find($keuangan['id_detail_alokasi'])['id_alokasi'] ?? null;

                    // Ada relasi keuangan = KAS
                    $jenisPencatatan = 'kas';

                } else {

                    // Tidak ada relasi keuangan = TERCATAT
                    $jenisPencatatan = 'tercatat';
                }
            }
        }

        /*
        * Untuk donor_record, ambil old value jika sebelumnya
        * gagal validasi.
        */
        if ($source === 'donor_record') {
            $jenisPencatatan = old('donation_type', 'donasi');
        }

        $data = array_merge([
            'source'            => $source,
            'pemasukan'         => null,
            'donatur'           => null,
            'donasi'            => null,
            'keuangan'          => $keuangan,
            'calonDonatur'      => [],
            'donasiTersedia'    => [],
            'akronimDefault'    => $this->akronimDefault,
            'statusLunasId'     => $this->statusLunasId,

            // Jenis pencatatan
            'jenisPencatatan'  => $jenisPencatatan,

            'statusDonasiList' => $this->statusDonasiModel->findAll(),
            'metodeList'       => $this->metodeModel->findAll(),

            'kategoriKeuanganList' => $this->kategoriModel
                ->whereIn('id_kategori_keuangan', [1, 2, 3, 4])
                ->findAll(),

            'alokasiList' => $this->alokasiModel
                ->orderBy('urutan', 'ASC')
                ->findAll(),

            // Khusus donor_record dan create
            'pengurusList' => in_array(
                $source,
                ['donor_record', 'create'],
                true
            )
                ? $this->historiModel
                    ->distinct()
                    ->select('nama_pengurus')
                    ->where('nama_pengurus !=', '')
                    ->findAll()
                : [],

        ], $extra);

        // dd($data);

        return view('admin/donation_income/v_form', $data);
    }

    // =========================================================================
    // POST admin/donation-incomes/save
    // =========================================================================
    public function save()
    {
        return $this->processSave(null);
    }

    // =========================================================================
    // POST admin/donation-incomes/update/(:num)
    // =========================================================================
    public function update($id_pemasukan_donasi)
    {
        return $this->processSave((int) $id_pemasukan_donasi);
    }

    private function processSave(?int $id_pemasukan_donasi = null)
    {
        $source = trim((string) $this->request->getPost('source'));

        $isRecord      = ($source === 'record');
        $isDonorRecord = ($source === 'donor_record');
        $isCreate      = ($source === 'create');
        $isEdit        = ($source === 'edit');

        if (!in_array($source, ['record', 'donor_record', 'create', 'edit'], true)) {
            return redirect()->back()->withInput()->with('error', 'Sumber pencatatan tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | DATA LAMA UNTUK EDIT — DIAMBIL PALING AWAL
        |--------------------------------------------------------------------------
        | Untuk source=edit, jenis pencatatan, donatur, program donasi, dan
        | tanggal WAJIB di-derive dari data lama di DB, bukan dipercaya dari
        | POST — karena field-field itu tidak boleh diedit.
        |--------------------------------------------------------------------------
        */
        $pemasukanLama = null;
        $keuanganLama  = null;

        if ($id_pemasukan_donasi !== null) {

            $pemasukanLama = $this->pemasukanModel->find($id_pemasukan_donasi);

            if (!$pemasukanLama) {
                return redirect()->back()->withInput()->with('errors', [
                    'id_pemasukan_donasi' => 'Data pemasukan yang akan diedit tidak ditemukan.'
                ]);
            }

            if (empty($pemasukanLama['id_donasi']) && !empty($pemasukanLama['created_at'])) {
                $createdAt = strtotime($pemasukanLama['created_at']);
                
                $jumatData = strtotime('friday this week 00:00:00', $createdAt);
                
                if ($createdAt >= $jumatData) {
                    $batasKunci = strtotime('+1 week', $jumatData);
                } else {
                    $batasKunci = $jumatData;
                }
                
                if (time() >= $batasKunci) {
                    return redirect()->to('admin/donors/detail/' . $pemasukanLama['id_donatur'])->with('error', 'Batas waktu edit untuk pemasukan tanpa program donasi telah lewat (maksimal hari Kamis di minggu berikutnya).');
                }
            }

            if (empty($pemasukanLama['id_donasi'])) {
                $keuanganLama = $this->keuanganModel
                    ->where('id_pemasukan_donasi', $id_pemasukan_donasi)
                    ->first();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | JENIS PENCATATAN
        |--------------------------------------------------------------------------
        | record / create -> selalu donasi
        | donor_record     -> dipilih user lewat form (radio)
        | edit             -> DI-DERIVE dari data lama, TIDAK dipercaya dari POST
        |--------------------------------------------------------------------------
        */

        if ($isRecord || $isCreate) {

            $donationType = 'donasi';

        } elseif ($isEdit) {

            if (!empty($pemasukanLama['id_donasi'])) {
                $donationType = 'donasi';
            } else {
                $donationType = $keuanganLama ? 'kas' : 'tercatat';
            }

        } else {
            // donor_record
            $donationType = trim((string) $this->request->getPost('donation_type'));

            if (!in_array($donationType, ['donasi', 'kas', 'tercatat'], true)) {
                return redirect()->back()->withInput()->with('errors', [
                    'donation_type' => 'Jenis pencatatan wajib dipilih.'
                ]);
            }
        }

        $isKas = in_array($source, ['donor_record', 'edit'], true) && $donationType === 'kas';
        $isTercatat = in_array($source, ['donor_record', 'edit'], true) && $donationType === 'tercatat';
        $needsDonasi = !$isKas && !$isTercatat;

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA ID DARI FORM
        |--------------------------------------------------------------------------
        */

        $idMetodePemasukan = (int) $this->request->getPost('id_metode_pemasukan');

        if ($isEdit) {

            // Donatur & program donasi TIDAK BOLEH diedit -> paksa pakai data lama
            $idDonatur = (int) $pemasukanLama['id_donatur'];
            $idDonasi  = $pemasukanLama['id_donasi'] ? (int) $pemasukanLama['id_donasi'] : null;

        } else {

            $idDonatur = (int) $this->request->getPost('id_donatur');

            $idDonasiPost = trim((string) $this->request->getPost('id_donasi'));
            $idDonasi = ($idDonasiPost !== '' && ctype_digit($idDonasiPost))
                ? (int) $idDonasiPost
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | RULES VALIDASI
        |--------------------------------------------------------------------------
        */

        $rules = [

            'id_donatur' => [
                'rules' => 'required|is_natural_no_zero',
            ],

            'id_metode_pemasukan' => [
                'rules' => 'required|is_natural_no_zero',
            ],

            'tanggal' => [
                'rules' => 'required|valid_date',
            ],

            'jumlah' => [
                'rules' => 'required|is_natural_no_zero',
            ],

            'akronim_kwitansi' => [
                'rules' => 'permit_empty|max_length[20]',
            ],

            'no_kwitansi' => [
                'rules' => 'permit_empty|max_length[50]',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | RULE PROGRAM DONASI
        |--------------------------------------------------------------------------
        */

        if ($needsDonasi) {

            $rules['id_donasi'] = [
                'rules' => 'required|is_natural_no_zero',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | RULE KAS
        |--------------------------------------------------------------------------
        */

        if ($isKas) {

            $rules['id_kategori_keuangan'] = [
                'rules' => 'required|is_natural_no_zero',
            ];

            $rules['id_detail_alokasi'] = [
                'rules' => 'required|is_natural_no_zero',
            ];

            $rules['keterangan'] = [
                'rules' => 'required|max_length[1000]',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | RULE TERCATAT
        |--------------------------------------------------------------------------
        */

        if ($isTercatat) {

            $rules['keterangan'] = [
                'rules' => 'required|max_length[1000]',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | WAKTU PENERIMAAN & NAMA PENGURUS
        |--------------------------------------------------------------------------
        |
        | Hanya donor_record dan create.
        |
        | Keduanya OPSIONAL.
        |
        */

        if (in_array(
            $source,
            ['donor_record', 'create'],
            true
        )) {

            $rules['waktu_penerimaan'] = [
                'rules' => 'permit_empty|valid_date',
            ];

            $rules['nama_pengurus'] = [
                'rules' => 'permit_empty|max_length[255]',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FILE BUKTI
        |--------------------------------------------------------------------------
        */

        $fileBukti = $this->request->getFile('bukti');

        if ($fileBukti && $fileBukti->isValid()) {

            $rules['bukti'] = [
                'rules' =>
                    'ext_in[bukti,pdf,jpg,jpeg,png]|max_size[bukti,5120]',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PESAN VALIDASI
        |--------------------------------------------------------------------------
        */

        $messages = [

            'id_donatur' => [
                'required' =>
                    'Donatur wajib dipilih.',

                'is_natural_no_zero' =>
                    'Donatur yang dipilih tidak valid.',
            ],

            'id_metode_pemasukan' => [
                'required' =>
                    'Metode pemasukan wajib dipilih.',

                'is_natural_no_zero' =>
                    'Metode pemasukan yang dipilih tidak valid.',
            ],

            'tanggal' => [
                'required' =>
                    'Tanggal wajib diisi.',

                'valid_date' =>
                    'Tanggal yang dimasukkan tidak valid.',
            ],

            'jumlah' => [
                'required' => 'Nominal wajib diisi.',
                'is_natural_no_zero' => 'Nominal harus berupa angka bulat lebih besar dari 0.',
            ],

            'akronim_kwitansi' => [
                'max_length' =>
                    'Akronim kwitansi maksimal 20 karakter.',
            ],

            'no_kwitansi' => [
                'max_length' =>
                    'Nomor kwitansi maksimal 50 karakter.',
            ],

            'id_donasi' => [
                'required' =>
                    'Program donasi wajib dipilih.',

                'is_natural_no_zero' =>
                    'Program donasi yang dipilih tidak valid.',
            ],

            'id_kategori_keuangan' => [
                'required' =>
                    'Kategori kas wajib dipilih.',

                'is_natural_no_zero' =>
                    'Kategori kas yang dipilih tidak valid.',
            ],

            'id_detail_alokasi' => [
                'required' =>
                    'Detail alokasi wajib dipilih.',

                'is_natural_no_zero' =>
                    'Detail alokasi yang dipilih tidak valid.',
            ],

            'keterangan' => [
                'required' => $isKas
                    ? 'Peruntukan dana kas wajib diisi.'
                    : 'Keterangan pencatatan wajib diisi.',

                'max_length' =>
                    'Keterangan maksimal 1000 karakter.',
            ],

            'waktu_penerimaan' => [
                'valid_date' =>
                    'Waktu penerimaan tidak valid.',
            ],

            'nama_pengurus' => [
                'max_length' =>
                    'Nama pengurus maksimal 255 karakter.',
            ],

            'bukti' => [
                'ext_in' =>
                    'Bukti harus berformat PDF, JPG, JPEG, atau PNG.',

                'max_size' =>
                    'Ukuran file bukti maksimal 5MB.',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | JALANKAN VALIDASI
        |--------------------------------------------------------------------------
        */

        if (!$this->validate($rules, $messages)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->validator->getErrors()
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI ID DONATUR
        |--------------------------------------------------------------------------
        */

        $donatur = $this->donaturModel
            ->find($idDonatur);

        if (!$donatur) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'id_donatur' =>
                        'Donatur yang dipilih tidak ditemukan.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI METODE PEMASUKAN
        |--------------------------------------------------------------------------
        */

        $metodePemasukan = $this->metodeModel
            ->find($idMetodePemasukan);

        if (!$metodePemasukan) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'id_metode_pemasukan' =>
                        'Metode pemasukan yang dipilih tidak ditemukan.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PROGRAM DONASI
        |--------------------------------------------------------------------------
        */

        $dataDonasi = null;

        if ($needsDonasi) {

            $dataDonasi = $this->donasiModel
                ->find($idDonasi);

            if (!$dataDonasi) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'id_donasi' =>
                            'Program donasi yang dipilih tidak ditemukan.'
                    ]);
            }

            /*
            | Program sudah ditutup
            */

            if (!empty($dataDonasi['closed_at'])) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'id_donasi' =>
                            'Program donasi tersebut sudah ditutup dan tidak dapat menerima pemasukan baru.'
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KATEGORI KEUANGAN
        |--------------------------------------------------------------------------
        */

        if ($isKas) {

            $idKategoriKeuangan = (int)
                $this->request->getPost(
                    'id_kategori_keuangan'
                );

            $kategoriKeuangan = $this->kategoriModel
                ->find($idKategoriKeuangan);

            if (!$kategoriKeuangan) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'id_kategori_keuangan' =>
                            'Kategori kas yang dipilih tidak ditemukan.'
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DETAIL ALOKASI
        |--------------------------------------------------------------------------
        */

        if ($isKas) {

            $idDetailAlokasi = (int)
                $this->request->getPost(
                    'id_detail_alokasi'
                );

            $detailAlokasi = $this->detailAlokasiModel
                ->find($idDetailAlokasi);

            if (!$detailAlokasi) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'id_detail_alokasi' =>
                            'Detail alokasi yang dipilih tidak ditemukan.'
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NOMOR KWITANSI
        |--------------------------------------------------------------------------
        */

        $akronim = trim(
            (string) $this->request->getPost(
                'akronim_kwitansi'
            )
        );

        if ($akronim === '') {
            $akronim = $this->akronimDefault;
        }

        $nomorInput = trim(
            (string) $this->request->getPost(
                'no_kwitansi'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | EDIT:
        | Jika nomor kwitansi lama sudah ada:
        |
        | - Input kosong => gunakan nomor lama
        | - Input berbeda => ERROR
        |
        */

        if (
            $isEdit
            && $pemasukanLama
            && !empty($pemasukanLama['no_kwitansi'])
        ) {

            $noKwitansiLama =
                trim($pemasukanLama['no_kwitansi']);

            /*
            | Jika user mengosongkan nomor kwitansi,
            | jangan generate nomor baru.
            */

            if ($nomorInput === '') {

                $noKwitansi = $noKwitansiLama;

            } else {

                /*
                | Hilangkan prefix akronim.
                */

                $nomorNormalisasi = preg_replace(
                    '/^' . preg_quote($akronim, '/') . '-/i',
                    '',
                    $nomorInput
                );

                $noKwitansiBaru =
                    $akronim . '-' . $nomorNormalisasi;

                /*
                | Nomor berubah -> tolak.
                */

                if ($noKwitansiBaru !== $noKwitansiLama) {

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('errors', [
                            'no_kwitansi' =>
                                'Nomor kwitansi tidak boleh diubah. Nomor kwitansi yang terdaftar adalah "' .
                                $noKwitansiLama .
                                '".'
                        ]);
                }

                $noKwitansi = $noKwitansiLama;
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | CREATE / DONOR RECORD / RECORD
            |--------------------------------------------------------------------------
            */

            if ($nomorInput === '') {

                $noKwitansi =
                    $this->generateNomorKwitansi(
                        $akronim
                    );

            } else {

                /*
                | Hilangkan prefix jika user memasukkannya.
                */

                $nomorNormalisasi = preg_replace(
                    '/^' . preg_quote($akronim, '/') . '-/i',
                    '',
                    $nomorInput
                );

                $noKwitansi =
                    $akronim . '-' . $nomorNormalisasi;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CEK DONATUR AKTIF DALAM PROGRAM DONASI
        |--------------------------------------------------------------------------
        |
        | Dijalankan SEBELUM pengecekan unik kwitansi global, supaya pesan error
        | yang muncul untuk kasus "donatur sudah aktif di program ini" selalu
        | kontekstual dan informatif -- bukan tertutup oleh pengecekan generik
        | yang duluan menemukan row lama milik donatur yang sama.
        |
        | Hanya berlaku jika:
        | id_donasi ada + id_donatur ada
        |
        | Status aktif: 1, 2, 3
        |
        | ATURAN:
        | - Nomor kwitansi SAMA dengan yang sudah aktif -> LOLOS (dianggap
        |   kelanjutan/cicilan yang sah untuk donatur+program yang sama).
        | - Nomor kwitansi BEDA dari yang sudah aktif -> DITOLAK, karena
        |   kwitansi tidak boleh diubah selama masih ada record aktif.
        |
        */

        $existingCandidateId = null;

        if ($needsDonasi && $idDonasi !== null && $idDonatur > 0) {

            $existingCandidate = $this->pemasukanModel
                ->where('id_donasi', $idDonasi)
                ->where('id_donatur', $idDonatur)
                ->whereIn('id_status_donasi', [1, 2, 3])
                ->when(
                    $id_pemasukan_donasi !== null,
                    function ($query) use ($id_pemasukan_donasi) {
                        return $query->where('id_pemasukan_donasi !=', $id_pemasukan_donasi);
                    }
                )
                ->first();

            if ($existingCandidate) {

                $noKwitansiTerdaftar = trim((string) $existingCandidate['no_kwitansi']);

                /*
                | Nomor berbeda dari yang sudah terdaftar -> tolak, minta pakai nomor lama.
                */
                if ($noKwitansiTerdaftar !== '' && $noKwitansi !== $noKwitansiTerdaftar) {

                    return redirect()->back()->withInput()->with('errors', [
                        'id_donatur' =>
                            'Donatur ini sudah terdaftar sebagai calon donatur aktif dalam program tersebut.',
                        'no_kwitansi' =>
                            'Nomor kwitansi berbeda dengan nomor kwitansi yang sudah terdaftar, yaitu "' .
                            $noKwitansiTerdaftar . '". Nomor kwitansi tidak boleh diubah.'
                    ]);
                }

                /*
                | Nomor SAMA dengan yang sudah aktif -> LOLOS.
                | Simpan ID row lama supaya bisa di-exclude di pengecekan
                | kwitansi global di bawah (karena row itu memang sengaja
                | punya nomor yang sama, bukan duplikat yang perlu ditolak).
                */
                $existingCandidateId = $existingCandidate['id_pemasukan_donasi'];
                $id_pemasukan_donasi = $existingCandidate['id_pemasukan_donasi'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI NOMOR KWITANSI GLOBAL
        |--------------------------------------------------------------------------
        |
        | Nomor kwitansi harus unik. Dijalankan SETELAH cek donatur aktif.
        |
        | Row milik $existingCandidateId (kalau ada) di-exclude dari pengecekan
        | ini, karena kemunculannya di sana BUKAN duplikat yang perlu ditolak --
        | itu adalah row aktif yang sama yang sengaja di-reuse nomornya (sudah
        | divalidasi & diloloskan di blok CEK DONATUR AKTIF di atas).
        |
        */

        $existingKwitansi = $this->pemasukanModel
            ->where('no_kwitansi', $noKwitansi)
            ->when(
                $id_pemasukan_donasi !== null,
                function ($query) use ($id_pemasukan_donasi) {
                    return $query->where('id_pemasukan_donasi !=', $id_pemasukan_donasi);
                }
            )
            ->when(
                $existingCandidateId !== null,
                function ($query) use ($existingCandidateId) {
                    return $query->where('id_pemasukan_donasi !=', $existingCandidateId);
                }
            )
            ->first();

        if ($existingKwitansi) {
            return redirect()->back()->withInput()->with('errors', [
                'no_kwitansi' =>
                    'Nomor kwitansi "' . $noKwitansi . '" sudah digunakan. Silakan gunakan nomor kwitansi yang berbeda.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS HISTORI
        |--------------------------------------------------------------------------
        */

        $statusHistoriUser   = 3;
        $statusHistoriSistem = 4;

        /*
        |--------------------------------------------------------------------------
        | JUMLAH
        |--------------------------------------------------------------------------
        */

        $jumlahRaw = trim(
            (string) $this->request->getPost('jumlah')
        );

        $jumlah = (int) preg_replace(
            '/\D/',
            '',
            $jumlahRaw
        );

        /*
        |--------------------------------------------------------------------------
        | SAMARKAN
        |--------------------------------------------------------------------------
        */

        $samarkan = (
            $this->request->getPost('samarkan') === 'ya'
        )
            ? 'Y'
            : 'N';

        /*
        |--------------------------------------------------------------------------
        | KETERANGAN
        |--------------------------------------------------------------------------
        |
        | donasi    => pemasukan_donasi.keterangan = NULL
        | tercatat  => pemasukan_donasi.keterangan
        | kas       => keuangan.keterangan
        |
        */

        $keterangan = trim(
            (string) $this->request->getPost('keterangan')
        );

        /*
        |--------------------------------------------------------------------------
        | PAYLOAD PEMASUKAN DONASI
        |--------------------------------------------------------------------------
        */

        $payload = [

            'id_donasi'  => $needsDonasi ? $idDonasi : null,
            'id_donatur' => $idDonatur,
            'id_metode_pemasukan' => $idMetodePemasukan,
            'tanggal' => $isEdit ? $pemasukanLama['tanggal'] : $this->request->getPost('tanggal'),
            'no_kwitansi' => $noKwitansi,
            'jumlah'      => $jumlah,
            'id_status_donasi' => $statusHistoriSistem,
            'keterangan'  => $isTercatat ? $keterangan : null,
            'samarkan'    => $samarkan,
        ];
        /*
        |--------------------------------------------------------------------------
        | DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */

        $db = Database::connect();

        $db->transStart();

        /*
        |--------------------------------------------------------------------------
        | UPLOAD BUKTI
        |--------------------------------------------------------------------------
        */

        $namaBukti =
            $pemasukanLama['bukti']
            ?? null;

        if (
            $fileBukti
            && $fileBukti->isValid()
            && !$fileBukti->hasMoved()
        ) {

            $namaBukti =
                $fileBukti->getRandomName();

            $uploadPath =
                FCPATH . 'uploads/donasi/bukti';

            if (!is_dir($uploadPath)) {

                mkdir(
                    $uploadPath,
                    0755,
                    true
                );
            }

            $fileBukti->move(
                $uploadPath,
                $namaBukti
            );

            /*
            | Hapus file lama jika EDIT.
            */

            if (
                $pemasukanLama
                && !empty($pemasukanLama['bukti'])
            ) {

                $fileLama =
                    $uploadPath
                    . DIRECTORY_SEPARATOR
                    . $pemasukanLama['bukti'];

                if (is_file($fileLama)) {
                    @unlink($fileLama);
                }
            }

            $payload['bukti'] =
                $namaBukti;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT / UPDATE PEMASUKAN
        |--------------------------------------------------------------------------
        */
        try {

            if ($id_pemasukan_donasi !== null) {

                $payload['id_pemasukan_donasi'] = $id_pemasukan_donasi;
                $payload['updated_by'] = session()->get('id_akun');

                $updated = $this->pemasukanModel->update(
                    $id_pemasukan_donasi,
                    $payload
                );

                if ($updated === false) {

                    log_message(
                        'error',
                        'GAGAL UPDATE PEMASUKAN DONASI | ' .
                        'ID: ' . $id_pemasukan_donasi . ' | ' .
                        'Errors Model: ' . json_encode(
                            $this->pemasukanModel->errors(),
                            JSON_UNESCAPED_UNICODE
                        ) . ' | ' .
                        'DB Error: ' . json_encode(
                            $db->error(),
                            JSON_UNESCAPED_UNICODE
                        )
                    );

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('errors', [
                            'database' =>
                                'Data pemasukan donasi gagal diperbarui. Silakan periksa data atau coba lagi.'
                        ]);
                }

                log_message(
                    'info',
                    'BERHASIL UPDATE PEMASUKAN DONASI | ' .
                    'ID: ' . $id_pemasukan_donasi . ' | ' .
                    'User ID: ' . session()->get('id_akun')
                );

            } else {

                $payload['created_by'] = session()->get('id_akun');

                $id_pemasukan_donasi =
                    $this->pemasukanModel->insert($payload);

                if ($id_pemasukan_donasi === false) {

                    log_message(
                        'error',
                        'GAGAL INSERT PEMASUKAN DONASI | ' .
                        'Errors Model: ' . json_encode(
                            $this->pemasukanModel->errors(),
                            JSON_UNESCAPED_UNICODE
                        ) . ' | ' .
                        'DB Error: ' . json_encode(
                            $db->error(),
                            JSON_UNESCAPED_UNICODE
                        ) . ' | ' .
                        'Payload: ' . json_encode(
                            $payload,
                            JSON_UNESCAPED_UNICODE
                        )
                    );

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('errors', [
                            'database' =>
                                'Data pemasukan donasi gagal disimpan. Silakan periksa data atau coba lagi.'
                        ]);
                }

                log_message(
                    'info',
                    'BERHASIL INSERT PEMASUKAN DONASI | ' .
                    'ID BARU: ' . $id_pemasukan_donasi . ' | ' .
                    'User ID: ' . session()->get('id_akun')
                );
            }

        } catch (\Throwable $e) {

            log_message(
                'error',
                'EXCEPTION PEMASUKAN DONASI | ' .
                'ID: ' . ($id_pemasukan_donasi ?? 'NULL') . ' | ' .
                'User ID: ' . session()->get('id_akun') . ' | ' .
                'Message: ' . $e->getMessage() . ' | ' .
                'File: ' . $e->getFile() . ' | ' .
                'Line: ' . $e->getLine()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'database' =>
                        'Terjadi kesalahan saat menyimpan data pemasukan donasi.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SINKRONISASI KEUANGAN
        |--------------------------------------------------------------------------
        |
        | Hanya tipe KAS.
        |
        */

        if ($isKas) {

            $keuanganData = [

                'id_kategori_keuangan' =>
                    (int) $this->request->getPost(
                        'id_kategori_keuangan'
                    ),

                'id_detail_alokasi' =>
                    (int) $this->request->getPost(
                        'id_detail_alokasi'
                    ),

                'id_pemasukan_donasi' =>
                    $id_pemasukan_donasi,

                'tanggal' =>
                    $payload['tanggal'],

                'jumlah' =>
                    $payload['jumlah'],

                'jenis' =>
                    'pemasukan',
                
                'method_input' => 
                    'manual',

                /*
                | KAS:
                | keterangan disimpan di tabel keuangan.
                */

                'keterangan' =>
                    $keterangan,

                'pic' =>
                    trim(
                        (string) $this->request->getPost(
                            'nama_pengurus'
                        )
                    ) ?: session()->get('nama'),

                'method_input' =>
                    'donasi',

                'bukti' =>
                    $payload['bukti']
                    ?? ($pemasukanLama['bukti'] ?? null),
            ];

            /*
            | Cari data keuangan lama.
            */

            $existingKeuangan =
                $this->keuanganModel
                    ->where(
                        'id_pemasukan_donasi',
                        $id_pemasukan_donasi
                    )
                    ->first();

            if ($existingKeuangan) {

                log_message(
                    'debug',
                    '[KEUANGAN] UPDATE - id_keuangan=' .
                    $existingKeuangan['id_keuangan'] .
                    ', id_pemasukan_donasi=' .
                    $id_pemasukan_donasi
                );

                $keuanganData['updated_by'] =
                    session()->get('id_akun');

                $result = $this->keuanganModel->update(
                    $existingKeuangan['id_keuangan'],
                    $keuanganData
                );

                if ($result === false) {

                    log_message(
                        'error',
                        '[KEUANGAN] UPDATE GAGAL - ' .
                        json_encode(
                            $this->keuanganModel->errors(),
                            JSON_UNESCAPED_UNICODE
                        )
                    );

                } else {

                    log_message(
                        'info',
                        '[KEUANGAN] UPDATE BERHASIL - id_keuangan=' .
                        $existingKeuangan['id_keuangan']
                    );
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | PASTIKAN LAPORAN KEUANGAN TERSEDIA
                |--------------------------------------------------------------------------
                */

                $waktu = $keuanganData['tanggal'];

                $laporan = $this->laporanModel
                    ->where('started_at <=', $waktu)
                    ->where('ended_at >=', $waktu)
                    ->first();

                if (!$laporan) {

                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG JUMAT TERDEKAT SEBAGAI START
                    |--------------------------------------------------------------------------
                    */

                    $timestamp = strtotime($waktu);

                    $start = date(
                        'Y-m-d 00:00:00',
                        strtotime('last friday', $timestamp)
                    );

                    if (date('N', $timestamp) == 5) {
                        $start = date(
                            'Y-m-d 00:00:00',
                            $timestamp
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | END = 6 HARI SETELAH START
                    |--------------------------------------------------------------------------
                    */

                    $end = date(
                        'Y-m-d 23:59:59',
                        strtotime($start . ' +6 days')
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG PEKAN
                    |--------------------------------------------------------------------------
                    */

                    $dayOfMonth = date(
                        'j',
                        strtotime($start)
                    );

                    $weekNum = ceil($dayOfMonth / 7);

                    $monthName = format_indo(
                        $start,
                        'month_only'
                    );

                    $dateStartIndo = format_indo(
                        $start,
                        'full_date'
                    );

                    $dateEndIndo = format_indo(
                        $end,
                        'full_date'
                    );

                    $title =
                        "Laporan Keuangan {$dateStartIndo} - {$dateEndIndo} (P{$weekNum} {$monthName})";

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE LAPORAN
                    |--------------------------------------------------------------------------
                    */

                    $idLaporan = $this->laporanModel->insert([

                        'judul' =>
                            $title,

                        'started_at' =>
                            $start,

                        'ended_at' =>
                            $end,

                        'created_by' =>
                            session()->get('id_akun'),

                    ]);

                    if ($idLaporan === false) {

                        log_message(
                            'error',
                            '[LAPORAN] GAGAL CREATE - ' .
                            json_encode(
                                $this->laporanModel->errors(),
                                JSON_UNESCAPED_UNICODE
                            )
                        );

                    } else {

                        log_message(
                            'info',
                            '[LAPORAN] CREATE BERHASIL - ' .
                            'id_laporan=' . $idLaporan .
                            ', start=' . $start .
                            ', end=' . $end
                        );

                    }

                } else {

                    log_message(
                        'debug',
                        '[LAPORAN] Sudah tersedia - ' .
                        'id_laporan=' . $laporan['id_laporan_mingguan'] .
                        ', start=' . $laporan['started_at'] .
                        ', end=' . $laporan['ended_at']
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | INSERT KEUANGAN
                |--------------------------------------------------------------------------
                */

                $keuanganData['created_by'] =
                    session()->get('id_akun');

                log_message(
                    'debug',
                    '[KEUANGAN] INSERT - payload=' .
                    json_encode(
                        $keuanganData,
                        JSON_UNESCAPED_UNICODE
                    )
                );

                $idKeuanganBaru =
                    $this->keuanganModel->insert(
                        $keuanganData
                    );

                if ($idKeuanganBaru === false) {

                    log_message(
                        'error',
                        '[KEUANGAN] INSERT GAGAL - ' .
                        json_encode(
                            $this->keuanganModel->errors(),
                            JSON_UNESCAPED_UNICODE
                        )
                    );

                } else {

                    log_message(
                        'info',
                        '[KEUANGAN] INSERT BERHASIL - ' .
                        'id_keuangan=' . $idKeuanganBaru .
                        ', id_pemasukan_donasi=' . $id_pemasukan_donasi
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HISTORI STATUS 3 + 4
        |--------------------------------------------------------------------------
        |
        | donor_record / create:
        |
        | STATUS 3
        | - waktu_penerimaan dari form
        | - nama_pengurus dari form
        |
        | STATUS 4
        | - waktu sekarang
        | - nama session
        |
        */

        if (
            !$isEdit
            && in_array(
                $source,
                ['donor_record', 'create'],
                true
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | HISTORI STATUS 3
            |--------------------------------------------------------------------------
            */

            $waktuPenerimaan = trim(
                (string) $this->request->getPost(
                    'waktu_penerimaan'
                )
            );

            $namaPengurus = trim(
                (string) $this->request->getPost(
                    'nama_pengurus'
                )
            );

            $waktuHistori3 =
                $waktuPenerimaan !== ''
                    ? str_replace(
                        'T',
                        ' ',
                        $waktuPenerimaan
                    )
                    : date('Y-m-d H:i:s');

            $namaHistori3 =
                $namaPengurus !== ''
                    ? $namaPengurus
                    : session()->get('nama');

            $this->historiModel->insert([

                'id_status_donasi' =>
                    $statusHistoriUser,

                'id_pemasukan_donasi' =>
                    $id_pemasukan_donasi,

                'waktu' =>
                    $waktuHistori3,

                'nama_pengurus' =>
                    $namaHistori3,

                'created_by' =>
                    session()->get('id_akun'),
            ]);

            /*
            |--------------------------------------------------------------------------
            | HISTORI STATUS 4
            |--------------------------------------------------------------------------
            */

            $this->historiModel->insert([

                'id_status_donasi' =>
                    $statusHistoriSistem,

                'id_pemasukan_donasi' =>
                    $id_pemasukan_donasi,

                'waktu' =>
                    date('Y-m-d H:i:s'),

                'nama_pengurus' =>
                    session()->get('nama'),

                'created_by' =>
                    session()->get('id_akun'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RECORD
        |--------------------------------------------------------------------------
        |
        | record hanya membuat histori status 4.
        |
        */

        if ($isRecord) {

            $this->historiModel->insert([

                'id_status_donasi' =>
                    $statusHistoriSistem,

                'id_pemasukan_donasi' =>
                    $id_pemasukan_donasi,

                'waktu' =>
                    date('Y-m-d H:i:s'),

                'nama_pengurus' =>
                    session()->get('nama'),

                'created_by' =>
                    session()->get('id_akun'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SELESAI TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $db->transComplete();

        if (!$db->transStatus()) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal menyimpan data. Silakan coba lagi.'
                );
        }

        if (!$isEdit && !empty($donatur['telepon'])) {
            try {

                // Data donatur
                $namaDonatur = $donatur['nama'] ?? 'Donatur';
                $jumlahFormatted = number_format($jumlah, 0, ',', '.');
                $noKw = $payload['no_kwitansi'];
                $namaProgram = $dataDonasi['judul'] ?? ($keterangan ?: 'Kas / Umum');


                // Template Pesan dengan format yang terbukti bisa otomatis menjadi link
                $message = "Assalamu'alaikum Wr. Wb.,\n\n" .
                           "*Yth. Bpk/Ibu. " . $namaDonatur . "*,\n\n" .
                           "Terima kasih atas kebaikan dan donasi/kontribusi Anda yang telah kami terima.\n\n" .
                           "*Detail Transaksi:*\n" .
                           "• No. Kwitansi: " . $noKw . "\n" .
                           "• Jumlah: Rp " . $jumlahFormatted . "\n" .
                           "• Peruntukan/Program: " . $namaProgram . "\n\n" .
                           "Anda dapat melihat dan memantau data serta riwayat donasi Anda melalui tautan di bawah ini:\n\n" .
                           base_url('donatur/' . $donatur['noreg'] . '/' . $donatur['token']) . "\n\n" .
                           "Jazakumullah khairan katsiran. Semoga harta Anda diberkahi dan diluaskan rezekinya.\n\n" .
                           "Wassalamu'alaikum Wr. Wb.";

                // Panggil layanan WhatsApp
                if (isset($this->whatsappService)) {
                    $this->whatsappService->send(
                        // phone: $donatur['telepon'],
                        phone: "088210841268",
                        message: $message
                    );
                }
            } catch (\Throwable $waEx) {
                log_message('error', 'GAGAL KIRIM WA NOTIFIKASI DONASI: ' . $waEx->getMessage());
            }
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        if (!empty($payload['id_donasi'])) {

            $redirectUrl = base_url(
                'admin/donations/detail/'
                . $payload['id_donasi']
            );

        } else {

            $redirectUrl = base_url(
                'admin/donors/detail/'
                . $payload['id_donatur']
            );
        }

        return redirect()
            ->to($redirectUrl)
            ->with(
                'success',
                'Data pemasukan donasi berhasil disimpan.'
            );
    }

    /**
     * Generate nomor urut kwitansi berikutnya untuk akronim tertentu.
     * Dipakai saat user mengosongkan input nomor (generate otomatis).
     * Format tersimpan: "AKRONIM-001".
     */
    private function generateNomorKwitansi(string $akronim): string
    {
        $prefix = $akronim . '-';

        $last = $this->pemasukanModel
            ->withDeleted()
            ->select('no_kwitansi')
            ->like('no_kwitansi', $prefix, 'after')
            ->orderBy('id_pemasukan_donasi', 'DESC')
            ->first();

        $nextNumber = 1;
        if ($last && !empty($last['no_kwitansi'])) {
            $nextNumber = ((int) str_replace($prefix, '', $last['no_kwitansi'])) + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // POST admin/donation-incomes/delete (respon JSON, dipanggil via AJAX)
    // =========================================================================
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permintaan tidak valid.'])
                ->setStatusCode(403);
        }

        $id = $this->request->getPost('id_pemasukan_donasi');
        $pemasukan = $this->pemasukanModel->find($id);
        if (!$pemasukan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.'])
                ->setStatusCode(404);
        }

        $donasi = $pemasukan['id_donasi'] ? $this->donasiModel->find($pemasukan['id_donasi']) : null;

        // ==========================================
        // VALIDASI PENGAMAN (SAMA SEPERTI DI EDIT)
        // ==========================================

        // Skenario A: Jika memiliki id_donasi dan donasinya sudah ditutup
        if ($donasi && !empty($donasi['closed_at'])) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Donasi ini sudah ditutup, data tidak dapat dihapus.'
            ])->setStatusCode(400);
        }

        // Skenario B: Jika TIDAK memiliki id_donasi, cek batas waktu created_at (Siklus Jumat s.d. Kamis minggu berikutnya)
        if (empty($pemasukan['id_donasi']) && !empty($pemasukan['created_at'])) {
            $createdAt = strtotime($pemasukan['created_at']);
            
            $jumatData = strtotime('friday this week 00:00:00', $createdAt);
            
            if ($createdAt >= $jumatData) {
                $batasKunci = strtotime('+1 week', $jumatData);
            } else {
                $batasKunci = $jumatData;
            }
            
            if (time() >= $batasKunci) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Batas waktu hapus untuk pemasukan tanpa program donasi telah lewat (maksimal hari Kamis di minggu berikutnya).'
                ])->setStatusCode(400);
            }
        }

        // Validasi status: Jika status bukan 4, tidak boleh dihapus
        // if ($pemasukan['id_status_donasi'] != 4) {
        //     return $this->response->setJSON([
        //         'status' => 'error', 
        //         'message' => 'Data list sudah tidak dapat dihapus karena statusnya bukan Pencatatan Dana.'
        //     ])->setStatusCode(400);
        // }

        // ==========================================
        // PROSES HAPUS (TRANSAKSI DATABASE)
        // ==========================================

        $idAkun = session()->get('id_akun');
        $db = Database::connect();
        $db->transStart();

        $this->pemasukanModel->update($id, ['deleted_by' => $idAkun]);
        $this->pemasukanModel->delete($id);

        // 2. Soft delete histori status terkait
        $this->historiModel
            ->where('id_pemasukan_donasi', $id)
            ->update(null, ['deleted_by' => $idAkun]);
        $this->historiModel->where('id_pemasukan_donasi', $id)->delete();

        // 3. Soft delete data keuangan terkait
        $this->keuanganModel
            ->where('id_pemasukan_donasi', $id)
            ->update(null, ['deleted_by' => $idAkun]);
        $this->keuanganModel->where('id_pemasukan_donasi', $id)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data.'])
                ->setStatusCode(500);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
    }

    public function toggleSamarkan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Request tidak valid.'
                ])
                ->setStatusCode(403);
        }

        $id = $this->request->getPost('id_pemasukan_donasi');
        $samarkan = $this->request->getPost('samarkan');

        if (empty($id)) {
            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'ID pemasukan donasi tidak ditemukan.'
                ])
                ->setStatusCode(400);
        }

        // Hanya menerima Y / N
        if (!in_array($samarkan, ['Y', 'N'], true)) {
            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Nilai penyamaran tidak valid.'
                ])
                ->setStatusCode(400);
        }

        $pemasukan = $this->pemasukanModel->find($id);

        if (!$pemasukan) {
            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Data pemasukan donasi tidak ditemukan.'
                ])
                ->setStatusCode(404);
        }

        /*
        * Pastikan data memang sudah terkunci.
        *
        * Jika terikat program donasi:
        * cek closed_at.
        *
        * Jika tidak terikat:
        * gunakan siklus Jumat -> Jumat seperti
        * aturan isEditable di view.
        */

        $isLocked = false;

        // =========================================================
        // 1. TERIKAT KE DONASI
        // =========================================================

        if (!empty($pemasukan['id_donasi'])) {

            $donasi = $this->donasiModel
                ->find($pemasukan['id_donasi']);

            if ($donasi && !empty($donasi['closed_at'])) {
                $isLocked = true;
            }

        }

        // =========================================================
        // 2. TIDAK TERIKAT KE DONASI
        // =========================================================

        else {

            if (!empty($pemasukan['created_at'])) {

                $createdAt = strtotime(
                    $pemasukan['created_at']
                );

                $jumatData = strtotime(
                    'friday this week 00:00:00',
                    $createdAt
                );

                if ($createdAt >= $jumatData) {

                    $batasKunci = strtotime(
                        '+1 week',
                        $jumatData
                    );

                } else {

                    $batasKunci = $jumatData;

                }

                if (time() >= $batasKunci) {
                    $isLocked = true;
                }
            }
        }

        // =========================================================
        // CEGAH TOGGLE PADA DATA YANG BELUM TERKUNCI
        // =========================================================

        if (!$isLocked) {

            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Penyamaran hanya dapat diubah pada data yang sudah terkunci.'
                ])
                ->setStatusCode(400);
        }

        // =========================================================
        // UPDATE
        // =========================================================

        $idAkun = session()->get('id_akun');

        $updateData = [
            'samarkan'  => $samarkan,
            'updated_by' => $idAkun
        ];

        if (!$this->pemasukanModel->update($id, $updateData)) {

            return $this->response
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal mengubah status penyamaran.'
                ])
                ->setStatusCode(500);
        }

        return $this->response
            ->setJSON([
                'status'  => 'success',
                'message' => $samarkan === 'Y'
                    ? 'Nama donatur berhasil disamarkan.'
                    : 'Nama donatur berhasil ditampilkan kembali.'
            ]);
    }
}