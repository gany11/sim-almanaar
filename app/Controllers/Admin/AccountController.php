<?php

namespace App\Controllers\Admin;

use App\Models\AkunModel;
use App\Models\PeranModel;
use App\Models\TokenModel;
use CodeIgniter\I18n\Time;

use App\Services\WhatsAppService;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AccountController extends BaseController
{
    protected $akunModel;
    protected $peranModel;
    protected $tokenModel;
    protected WhatsAppService $whatsappService;

    public function __construct()
    {
        $this->akunModel  = new AkunModel();
        $this->peranModel = new PeranModel();
        $this->tokenModel = new TokenModel();
        $this->whatsappService = new WhatsAppService();
    }

    public function index() {
        return view('admin/account/v_index', ['title' => 'Manajemen Akun']);
    }

    public function list() {
        $status = $this->request->getPost('status');
        $id_sesi = session()->get('id_akun');

        $db = \Config\Database::connect();
        
        // 1. Buat subquery untuk log login terakhir dan kompilasi ke SQL
        $subquery = $db->table('log_login')
            ->select('id_akun, MAX(id_log) as max_id')
            ->groupBy('id_akun');
        
        $subQuerySql = $subquery->getCompiledSelect();

        // 2. Susun query utama menggunakan akunModel
        $query = $this->akunModel->select('akun.*, peran.nama AS nama_peran, peran.class_color, log_login.login_at, log_login.logout_at, log_login.ip_address, log_login.status AS status_log')
            ->join('peran', 'peran.id_peran = akun.id_peran')
            // Join ke subquery dulu (dihubungkan dengan id_akun)
            ->join("({$subQuerySql}) AS latest_log", 'latest_log.id_akun = akun.id_akun', 'left', false)
            // Baru kemudian join tabel log_login asli menggunakan max_id
            ->join('log_login', 'log_login.id_log = latest_log.max_id', 'left', false)
            ->where('akun.deleted_at', null);

        if ($status != "") {
            $query->where('akun.status', $status);
        }

        $data['akun'] = $query->findAll();
        $data['id_sesi'] = $id_sesi;

        return view('admin/account/v_list_partial', $data);
    }

    public function updateStatus() {
        $id = $this->request->getPost('id_akun');
        $status_baru = $this->request->getPost('status');

        if ($this->akunModel->update($id, ['status' => $status_baru])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status akun berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui status.']);
    }

    public function register()
    {
        $data = [
            'title' => 'Registrasi Akun',
            'peran' => $this->peranModel->findAll()
        ];
        return view('admin/account/v_register', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();

        $input['password'] = password_hash(
            'AlManaar' . rand(100, 999),
            PASSWORD_DEFAULT
        );

        $input['status'] = 'aktif';

        if (!$this->akunModel->validate($input)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->akunModel->errors()
                )
                ->with(
                    'error',
                    'Gagal mendaftarkan akun. Silakan periksa kolom yang berwarna merah.'
                );
        }

        $phone = $input['telepon'];

        try {

            $checkNumber =
                $this->whatsappService->checkNumber(
                    $phone
                );


            log_message(
                'debug',
                'CHECK WA PHONE: ' . $phone
            );

            log_message(
                'debug',
                'CHECK WA RESULT: ' .
                json_encode(
                    $checkNumber,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
                )
            );


            $registered =
                $checkNumber['data']['registered']
                ?? false;

            $valid =
                $checkNumber['data']['valid']
                ?? false;

            if (
                !$registered ||
                !$valid
            ) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'errors',
                        [
                            'telepon' =>
                                'Nomor WhatsApp tidak terdaftar atau tidak valid.'
                        ]
                    )
                    ->with(
                        'error',
                        'Gagal mendaftarkan akun. Silakan periksa kolom yang berwarna merah.'
                    );
            }


        } catch (\Throwable $e) {

            log_message(
                'error',
                'WhatsApp check-number error: ' .
                $e->getMessage()
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    [
                        'telepon' =>
                            'Nomor WhatsApp tidak dapat diverifikasi. Silakan coba lagi.'
                    ]
                )
                ->with(
                    'error',
                    'Gagal mendaftarkan akun. Silakan periksa kolom yang berwarna merah.'
                );
        }

        if (
            $this->akunModel->save($input)
        ) {

            $newUserId =
                $this->akunModel->getInsertID();


            /*
            * =====================================================
            * TOKEN AKTIVASI
            * =====================================================
            */

            $token =
                bin2hex(
                    random_bytes(32)
                );


            $this->tokenModel->save([

                'id_akun' =>
                    $newUserId,

                'token' =>
                    $token,

                'expired_at' =>
                    Time::now(
                        'Asia/Jakarta'
                    )
                    ->addDays(1)
                    ->toDateTimeString(),

            ]);


            /*
            * =====================================================
            * EMAIL AKTIVASI
            * =====================================================
            */

            $this->sendActivationEmail(
                $input['email'],
                $input['nama'],
                $token,
                $input['username']
            );


            return redirect()
                ->to('admin/account')
                ->with(
                    'success',
                    'Akun berhasil dibuat dan email aktivasi telah dikirim.'
                );
        }


        /*
        * =========================================================
        * GAGAL SAVE
        * =========================================================
        */

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'errors',
                $this->akunModel->errors()
            )
            ->with(
                'error',
                'Gagal mendaftarkan akun. Silakan periksa kolom yang berwarna merah.'
            );
    }

    private function sendActivationEmail($to, $nama, $token, $username)
    {
        $emailService = \Config\Services::email();
        $resetLink    = base_url('admin/reset-password/' . $token);

        $emailService->setTo($to);
        $emailService->setSubject('Aktivasi Akun Baru - Masjid Al Manaar');
        
        $message = "
            <div style='font-family: sans-serif; color: #333;'>
                <h2>Selamat Datang di Sistem Masjid Al Manaar!</h2>
                <p>Halo <strong>{$nama}</strong>, akun Anda telah berhasil didaftarkan oleh Admin dengan username: <strong>{$username}</strong>.</p>
                <p>Untuk keamanan, silakan buat password Anda sendiri dengan mengklik tombol di bawah ini:</p>
                <div style='margin: 30px 0;'>
                    <a href='{$resetLink}' style='background-color: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Setel Password Anda</a>
                </div>
                <p>Link ini berlaku selama 24 jam.</p>
                <hr style='border-top: 1px solid #eee;'>
                <small>Jika Anda tidak merasa mendaftar, silakan abaikan email ini.</small>
            </div>";
            
        $emailService->setMessage($message);
        $emailService->send();
    }

    /**
     * Menampilkan halaman detail akun & matriks fitur tambahan 
     * (Hanya menampilkan fitur yang BELUM termasuk di dalam Peran Utamanya)
     */
    public function detail($id)
    {
        $akun = $this->akunModel->select('akun.*, peran.nama AS nama_peran, peran.class_color')
            ->join('peran', 'peran.id_peran = akun.id_peran')
            ->where('akun.id_akun', $id)
            ->where('akun.deleted_at', null)
            ->first();

        if (!$akun) {
            return redirect()->to('admin/account')->with('error', 'Data akun tidak ditemukan.');
        }

        // 1. Ambil ID fitur yang sudah dimiliki oleh PERAN UTAMA akun ini
        $peranFiturModel = new \App\Models\PeranFiturModel();
        $roleFeatureIds = $peranFiturModel->where('id_peran', $akun->id_peran)->findColumn('id_fitur') ?? [];

        // 2. Ambil fitur yang BELUM dimiliki oleh peran utama (untuk dijadikan tambahan/kustom)
        $fiturModel = new \App\Models\FiturModel();
        if (!empty($roleFeatureIds)) {
            $allFeatures = $fiturModel->whereNotIn('id_fitur', $roleFeatureIds)->orderBy('kategori', 'ASC')->findAll();
        } else {
            $allFeatures = $fiturModel->orderBy('kategori', 'ASC')->findAll();
        }
        
        // Kelompokkan fitur yang tersisa berdasarkan kategorinya
        $groupedFeatures = [];
        foreach ($allFeatures as $f) {
            $groupedFeatures[$f['kategori']][] = $f;
        }

        // 3. Ambil ID fitur tambahan yang sudah dikhususkan untuk akun ini
        $akunFiturModel = new \App\Models\AkunFiturModel();
        $assignedFeatureIds = $akunFiturModel
            ->where('id_akun', $id)
            ->findColumn('id_fitur') ?? [];

        return view('admin/account/v_detail', [
            'title'              => 'Detail Akun & Fitur Tambahan: ' . $akun->nama,
            'akun'               => $akun,
            'groupedFeatures'    => $groupedFeatures,
            'assignedFeatureIds' => $assignedFeatureIds
        ]);
    }

    /**
     * Menyimpan sinkronisasi fitur tambahan akun secara massal (Matrix Sync dengan Logika OR)
     */
    public function featureSync()
    {
        $idAkun        = $this->request->getPost('id_akun');
        $selectedFitur = $this->request->getPost('id_fitur') ?? []; // Array ID fitur tambahan yang dicentang

        if (empty($idAkun)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Akun tidak valid.'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $fiturModel = new \App\Models\FiturModel();
        $akunFiturModel = new \App\Models\AkunFiturModel();

        // 1. VALIDASI PRASYARAT (LOGIKA OR / ATAU)
        if (!empty($selectedFitur)) {
            $builderPrasyarat = $db->table('fitur_prasyarat');
            
            foreach ($selectedFitur as $idFitur) {
                $prasyaratList = $builderPrasyarat->select('id_prasyarat')
                    ->where('id_fitur', $idFitur)
                    ->where('deleted_at IS NULL')
                    ->get()
                    ->getResultArray();

                $requiredIds = array_column($prasyaratList, 'id_prasyarat');

                if (!empty($requiredIds)) {
                    $intersect = array_intersect($requiredIds, $selectedFitur);

                    if (empty($intersect)) {
                        $missingFeatures = $db->table('fitur')
                            ->select('kategori, nama_fitur')
                            ->whereIn('id_fitur', $requiredIds)
                            ->get()
                            ->getResultArray();
                        
                        $formattedNames = [];
                        foreach ($missingFeatures as $mf) {
                            $formattedNames[] = "<b>{$mf['kategori']}</b> ({$mf['nama_fitur']})";
                        }
                        
                        $namesStr = implode(' <b>ATAU</b> ', $formattedNames);
                        
                        $mainFeature = $fiturModel->find($idFitur);
                        $mainName = $mainFeature ? "<b>{$mainFeature['kategori']}</b> ({$mainFeature['nama_fitur']})" : 'Fitur';

                        return $this->response->setJSON([
                            'status'  => 'error', 
                            'message' => "Gagal! Fitur {$mainName} memerlukan setidaknya salah satu prasyarat berikut:<br><br>{$namesStr}."
                        ])->setStatusCode(400);
                    }
                }
            }
        }

        $db->transStart();

        try {
            $currentActive = $akunFiturModel->where('id_akun', $idAkun)->findAll();
            $currentIds = array_column($currentActive, 'id_fitur');

            $toDelete = array_diff($currentIds, $selectedFitur);
            $toAdd    = array_diff($selectedFitur, $currentIds);

            // Hapus relasi yang tidak dicentang lagi (soft delete)
            if (!empty($toDelete)) {
                $akunFiturModel->where('id_akun', $idAkun)
                    ->whereIn('id_fitur', $toDelete)
                    ->set(['deleted_by' => session()->get('id_akun')])
                    ->delete();
            }

            // Tambahkan atau aktifkan kembali relasi yang dicentang
            foreach ($toAdd as $idFitur) {
                $exists = $akunFiturModel->withDeleted()
                    ->where('id_akun', $idAkun)
                    ->where('id_fitur', $idFitur)
                    ->first();

                if ($exists) {
                    $akunFiturModel->update($exists['id_akun_fitur'], [
                        'deleted_at' => null,
                        'deleted_by' => null,
                        'updated_by' => session()->get('id_akun')
                    ]);
                } else {
                    $akunFiturModel->insert([
                        'id_akun'    => $idAkun,
                        'id_fitur'   => $idFitur,
                        'created_by' => session()->get('id_akun')
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui fitur tambahan akun.']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Fitur tambahan akun berhasil diperbarui!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
