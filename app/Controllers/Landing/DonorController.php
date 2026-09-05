<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\DonaturModel;
use App\Models\TokenPinDonaturModel;
use App\Models\PemasukanDonasiModel;
use App\Models\DonasiModel;
use App\Services\WhatsAppService;

class DonorController extends BaseController
{
    protected DonaturModel $donaturModel;
    protected TokenPinDonaturModel $tokenPinModel;
    protected PemasukanDonasiModel $pemasukanDonasiModel;
    protected DonasiModel $donasiModel;
    protected WhatsAppService $whatsappService;

    public function __construct()
    {
        $this->donaturModel    = new DonaturModel();
        $this->tokenPinModel   = new TokenPinDonaturModel();
        $this->pemasukanDonasiModel = new PemasukanDonasiModel();
        $this->donasiModel   = new DonasiModel();
        $this->whatsappService = new WhatsAppService();
    }


    /**
     * ============================================================
     * INDEX / DASHBOARD
     * GET /donatur
     * ============================================================
     */
    public function index()
    {
        $idDonatur = session()->get('id_donatur');

        /*
         * Belum login
         */
        if (!$idDonatur) {
            return view('landing/donor/v_login', [
                'title'   => 'Login Donatur',
                'sitekey' => RECAPTCHA_SITE_KEY,
            ]);
        }


        /*
         * Sudah login
         */
        $donatur = $this->donaturModel
                        ->select([
                            'noreg',
                            'nama',
                            'email',
                            'telepon',
                            'alamat',
                            'rt',
                            'rw',
                            'kelurahan',
                        ])
                        ->find($idDonatur);

        if (!$donatur) {

            session()->remove('id_donatur');

            return redirect()
                ->to(base_url('donatur'))
                ->with('error', 'Sesi donatur tidak valid. Silakan login kembali.');
        }

        $rekomendasiDonasi = $this->donasiModel->getPublicDonationRecommendations($idDonatur);

        $donations = $this->pemasukanDonasiModel->getDonationsByDonor($idDonatur);

        return view('landing/donor/v_dashboard', [
            'title'             => 'Dashboard Donatur',
            'donatur'           => $donatur,
            'rekomendasiDonasi' => $rekomendasiDonasi,
            'donations'         => $donations,
        ]);
    }


    /**
     * ============================================================
     * ACCESS VIA TOKEN
     * GET /donatur/{noreg}/{token}
     * ============================================================
     */
    public function access(string $noreg, string $token)
    {
        /*
         * Jika sudah login, tidak perlu menggunakan access token lagi.
         */
        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        /*
         * Cari donatur berdasarkan noreg + token
         */
        $donatur = $this->donaturModel
                        ->select([
                            'id_donatur',
                            'pin',
                        ])
                        ->where('noreg', $noreg)
                        ->where('token', $token)
                        ->first();


        /*
         * Token tidak valid
         */
        if (!$donatur) {
            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'Tautan akses tidak valid atau sudah tidak tersedia.'
                );
        }


        /*
         * PIN belum dibuat
         *
         * Tampilkan form pembuatan PIN.
         */
        if (empty($donatur['pin'])) {

            return view('landing/donor/v_set_pin', [
                'title'   => 'Buat PIN Donatur',
                'donatur' => $donatur,
                'noreg'   => $noreg,
                'token'   => $token,
                'sitekey' => RECAPTCHA_SITE_KEY,
            ]);
        }


        /*
         * PIN sudah ada.
         *
         * Jangan langsung login.
         * Tampilkan form PIN.
         */
        return view('landing/donor/v_access_pin', [
            'title'   => 'Masuk Donatur',
            'donatur' => $donatur,
            'noreg'   => $noreg,
            'token'   => $token,
            'sitekey' => RECAPTCHA_SITE_KEY,
        ]);
    }


    /**
     * ============================================================
     * LOGIN MANUAL
     * POST /donatur/login
     * ============================================================
     */
    public function login()
    {
        /*
         * Jika sudah login, tidak perlu login lagi.
         */
        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        /*
         * reCAPTCHA
         */
        if (!$this->verifyRecaptcha()) {
            return redirect()
                ->to(base_url('donatur'))
                ->withInput()
                ->with(
                    'error',
                    'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
                );
        }


        $noreg = trim((string) $this->request->getPost('noreg'));
        $pin   = trim((string) $this->request->getPost('pin'));


        /*
         * Validasi dasar
         */
        if ($noreg === '' || $pin === '') {
            return redirect()
                ->to(base_url('donatur'))
                ->withInput()
                ->with(
                    'error',
                    'Nomor registrasi dan PIN wajib diisi.'
                );
        }


        /*
         * Cari donatur
         */
        $donatur = $this->donaturModel
                        ->select([
                            'id_donatur',
                            'pin',
                        ])
                        ->where('noreg', $noreg)
                        ->first();


        /*
         * Jangan memberi informasi terlalu spesifik.
         */
        if (!$donatur || empty($donatur['pin'])) {
            return redirect()
                ->to(base_url('donatur'))
                ->withInput()
                ->with(
                    'error',
                    'Nomor registrasi atau PIN tidak valid.'
                );
        }


        /*
         * Verifikasi PIN
         */
        if (!password_verify($pin, $donatur['pin'])) {
            return redirect()
                ->to(base_url('donatur'))
                ->withInput()
                ->with(
                    'error',
                    'Nomor registrasi atau PIN tidak valid.'
                );
        }


        /*
         * Login berhasil
         */
        session()->regenerate(true);

        session()->set([
            'id_donatur' => $donatur['id_donatur'],
        ]);


        return redirect()
            ->to(base_url('donatur'))
            ->with('success', 'Berhasil masuk ke akun donatur.');
    }


    /**
     * ============================================================
     * SET PIN PERTAMA KALI
     * POST /donatur/pin
     * ============================================================
     */
    public function setPin()
    {
        /*
         * Endpoint ini tidak membutuhkan session.
         *
         * Identitas diperoleh dari noreg + token.
         */

        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        /*
         * reCAPTCHA
         */
        if (!$this->verifyRecaptcha()) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
                );
        }


        $noreg          = trim((string) $this->request->getPost('noreg'));
        $token          = trim((string) $this->request->getPost('token'));
        $pin            = trim((string) $this->request->getPost('pin'));
        $pinConfirmation = trim(
            (string) $this->request->getPost('pin_confirmation')
        );


        /*
         * Validasi identitas
         */
        $donatur = $this->donaturModel
                        ->select([
                            'id_donatur',
                            'pin',
                        ])
                        ->where('noreg', $noreg)
                        ->where('token', $token)
                        ->first();


        if (!$donatur) {

            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'Tautan akses tidak valid atau sudah tidak tersedia.'
                );
        }


        /*
         * Kalau PIN ternyata sudah ada,
         * jangan boleh overwrite melalui access token.
         */
        if (!empty($donatur['pin'])) {

            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'PIN sudah dibuat. Silakan login menggunakan PIN Anda.'
                );
        }


        /*
         * PIN harus 6 digit
         */
        if (!preg_match('/^\d{6}$/', $pin)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'PIN harus terdiri dari 6 digit angka.'
                );
        }


        /*
         * Konfirmasi PIN
         */
        if ($pin !== $pinConfirmation) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Konfirmasi PIN tidak sama.'
                );
        }


        /*
         * Hash PIN
         */
        $pinHash = password_hash(
            $pin,
            PASSWORD_DEFAULT
        );


        /*
         * Simpan PIN
         */
        $updated = $this->donaturModel->update(
            $donatur['id_donatur'],
            [
                'pin' => $pinHash,
            ]
        );


        if (!$updated) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'PIN gagal dibuat. Silakan coba lagi.'
                );
        }


        /*
         * PIN berhasil dibuat.
         *
         * Langsung login karena identitas sudah
         * dibuktikan menggunakan noreg + token.
         */
        session()->regenerate(true);

        session()->set([
            'id_donatur' => $donatur['id_donatur'],
        ]);


        return redirect()
            ->to(base_url('donatur'))
            ->with(
                'success',
                'PIN berhasil dibuat. Selamat datang.'
            );
    }


    /**
     * ============================================================
     * LUPA PIN
     * GET /donatur/lupa-pin
     * ============================================================
     */
    public function forgotPin()
    {
        /*
         * Kalau sudah login, tidak perlu lupa PIN.
         */
        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        return view('landing/donor/v_forgot_pin', [
            'title'   => 'Lupa PIN',
            'sitekey' => RECAPTCHA_SITE_KEY,
        ]);
    }


    /**
     * ============================================================
     * REQUEST RESET PIN
     * POST /donatur/lupa-pin
     * ============================================================
     */
    public function requestResetPin()
    {
        /*
         * Tidak boleh dilakukan ketika sudah login.
         */
        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        /*
         * reCAPTCHA
         */
        if (!$this->verifyRecaptcha()) {

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->withInput()
                ->with(
                    'error',
                    'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
                );
        }


        $noreg = trim((string) $this->request->getPost('noreg'));


        if ($noreg === '') {

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->withInput()
                ->with(
                    'error',
                    'Nomor registrasi wajib diisi.'
                );
        }


        /*
         * Cari donatur
         */
        $donatur = $this->donaturModel
                        ->select([
                            'id_donatur',
                            'nama',
                            'telepon',
                        ])
                        ->where('noreg', $noreg)
                        ->first();


        /*
         * Pesan dibuat generik.
         */
        if (!$donatur) {

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'success',
                    'Jika data ditemukan, tautan reset PIN akan dikirim melalui WhatsApp.'
                );
        }


        /*
         * ========================================================
         * CEK TOKEN RESET AKTIF
         * ========================================================
         */
        $now = date('Y-m-d H:i:s');

        $activeToken = $this->tokenPinModel
            ->select('id_token_pin')
            ->where('id_donatur', $donatur['id_donatur'])
            ->where('used_at IS NULL', null, false)
            ->where('expired_at >', $now)
            ->orderBy('id_token_pin', 'DESC')
            ->first();


        /*
         * JIKA MASIH ADA TOKEN AKTIF
         */
        if ($activeToken) {

            /*
             * Token di database adalah hash.
             *
             * Kita tidak bisa mendapatkan kembali
             * token plaintext dari hash.
             *
             * Oleh karena itu ada 2 pilihan:
             *
             * 1. Simpan plaintext token -> TIDAK disarankan.
             * 2. Jangan membuat token baru dan minta user
             *    menggunakan link yang sebelumnya dikirim.
             *
             * Kita pilih nomor 2.
             */

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'success',
                    'Tautan reset PIN sebelumnya masih aktif. Silakan periksa pesan WhatsApp Anda.'
                );
        }


        /*
         * Pastikan nomor WhatsApp tersedia
         */
        $telepon = trim((string) ($donatur['telepon'] ?? ''));

        if ($telepon === '') {

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'error',
                    'Nomor WhatsApp Anda belum terdaftar. Silakan hubungi pengurus.'
                );
        }


        /*
         * Generate token plaintext
         */
        $plainToken = bin2hex(
            random_bytes(32)
        );


        /*
         * Simpan hash token
         */
        $tokenHash = hash(
            'sha256',
            $plainToken
        );


        $createdAt = date(
            'Y-m-d H:i:s'
        );

        $expiredAt = date(
            'Y-m-d H:i:s',
            strtotime('+30 minutes')
        );


        $inserted = $this->tokenPinModel->insert([
            'id_donatur' => $donatur['id_donatur'],
            'token'      => $tokenHash,
            'created_at' => $createdAt,
            'expired_at' => $expiredAt,
            'used_at'    => null,
        ]);


        if (!$inserted) {

            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'error',
                    'Gagal membuat permintaan reset PIN.'
                );
        }


        /*
         * URL reset
         */
        $resetUrl = base_url(
            'donatur/reset-pin/' . $plainToken
        );


        $namaDonatur = trim(
            (string) $donatur['nama']
        );


        /*
         * Pesan WhatsApp
         */
        $message =
            "Assalamu'alaikum Wr. Wb.,\n\n" .
            "*Yth. Bpk/Ibu. " . $namaDonatur . "*,\n\n" .
            "Kami menerima permintaan untuk mengatur ulang PIN akun donatur Anda.\n\n" .
            "*Tautan Reset PIN:*\n" .
            $resetUrl . "\n\n" .
            "Tautan ini berlaku selama *30 menit* dan hanya dapat digunakan satu kali.\n\n" .
            "Jika Anda tidak merasa melakukan permintaan reset PIN, " .
            "silakan abaikan pesan ini.\n\n" .
            "Jazakumullah khairan katsiran.\n\n" .
            "Wassalamu'alaikum Wr. Wb.";


        /*
         * Kirim WhatsApp
         */
        try {

            $result = $this->whatsappService->send(
                phone: $telepon,
                message: $message
            );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Gagal mengirim WhatsApp reset PIN: ' .
                $e->getMessage()
            );

            /*
             * Token sudah dibuat tetapi WA gagal.
             *
             * Token masih aktif sehingga request berikutnya
             * tidak akan membuat token baru.
             *
             * Untuk kasus ini token perlu dibatalkan.
             */
            $this->tokenPinModel->update(
                $inserted,
                [
                    'used_at' => date('Y-m-d H:i:s'),
                ]
            );


            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'error',
                    'Gagal mengirim tautan reset PIN melalui WhatsApp. Silakan coba lagi.'
                );
        }


        /*
         * Cek response gateway
         */
        if (
            !is_array($result) ||
            (($result['status'] ?? '') === 'error')
        ) {

            log_message(
                'error',
                'WhatsApp reset PIN gagal: ' .
                json_encode($result)
            );


            /*
             * Batalkan token karena pesan gagal dikirim.
             */
            $this->tokenPinModel->update(
                $inserted,
                [
                    'used_at' => date('Y-m-d H:i:s'),
                ]
            );


            return redirect()
                ->to(base_url('donatur/lupa-pin'))
                ->with(
                    'error',
                    'Gagal mengirim tautan reset PIN melalui WhatsApp. Silakan coba lagi.'
                );
        }


        return redirect()
            ->to(base_url('donatur/lupa-pin'))
            ->with(
                'success',
                'Tautan reset PIN telah dikirim melalui WhatsApp.'
            );
    }


    /**
     * ============================================================
     * RESET PIN
     * GET /donatur/reset-pin/{token}
     * ============================================================
     */
    public function resetPin(string $token)
    {
        /*
         * Kalau sudah login, arahkan ke dashboard.
         */

        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        $tokenHash = hash(
            'sha256',
            $token
        );


        $dataToken = $this->tokenPinModel
            ->select([
                'id_donatur',
            ])
            ->where('token', hash('sha256', $token))
            ->where('used_at IS NULL', null, false)
            ->where('expired_at >', date('Y-m-d H:i:s'))
            ->first();


        if (!$dataToken) {

            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'Tautan reset PIN tidak valid, sudah digunakan, atau sudah kedaluwarsa.'
                );
        }


        $donatur = $this->donaturModel
                        ->select([
                            'nama',
                            'noreg',
                        ])
                        ->find($dataToken['id_donatur']);


        if (!$donatur) {

            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'Data donatur tidak ditemukan.'
                );
        }


        return view('landing/donor/v_reset_pin', [
            'title'   => 'Reset PIN',
            'token'   => $token,
            'nama'    => $donatur['nama'],
            'noreg'   => $donatur['noreg'],
            'sitekey' => RECAPTCHA_SITE_KEY,
        ]);
    }


    /**
     * ============================================================
     * UPDATE PIN
     * POST /donatur/reset-pin/{token}
     * ============================================================
     */
    public function updatePin(string $token)
    {
        /*
         * Kalau sudah login, tidak perlu reset melalui token.
         */
        if (session()->get('id_donatur')) {
            return redirect()->to(base_url('donatur'));
        }


        /*
         * reCAPTCHA
         */
        if (!$this->verifyRecaptcha()) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
                );
        }


        $pin = trim(
            (string) $this->request->getPost('pin')
        );

        $pinConfirmation = trim(
            (string) $this->request->getPost('pin_confirmation')
        );


        /*
         * Validasi PIN
         */
        if (!preg_match('/^\d{6}$/', $pin)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'PIN harus terdiri dari 6 digit angka.'
                );
        }


        if ($pin !== $pinConfirmation) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Konfirmasi PIN tidak sama.'
                );
        }


        /*
         * Cari token
         */
        $tokenHash = hash(
            'sha256',
            $token
        );


        $dataToken = $this->tokenPinModel
            ->select([
                'id_token_pin',
                'id_donatur',
            ])
            ->where('token', hash('sha256', $token))
            ->where('used_at IS NULL', null, false)
            ->where('expired_at >', date('Y-m-d H:i:s'))
            ->first();


        if (!$dataToken) {

            return redirect()
                ->to(base_url('donatur'))
                ->with(
                    'error',
                    'Tautan reset PIN tidak valid, sudah digunakan, atau sudah kedaluwarsa.'
                );
        }


        /*
         * Hash PIN
         */
        $pinHash = password_hash(
            $pin,
            PASSWORD_DEFAULT
        );


        $db = \Config\Database::connect();

        $db->transStart();


        /*
         * Update PIN
         */
        $this->donaturModel->update(
            $dataToken['id_donatur'],
            [
                'pin' => $pinHash,
            ]
        );


        /*
         * Tandai token sudah digunakan
         */
        $this->tokenPinModel->update(
            $dataToken['id_token_pin'],
            [
                'used_at' => date('Y-m-d H:i:s'),
            ]
        );


        $db->transComplete();


        if ($db->transStatus() === false) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Gagal mengubah PIN. Silakan coba lagi.'
                );
        }


        /*
         * Setelah reset berhasil:
         *
         * TIDAK langsung membuat session.
         *
         * User diarahkan ke login dan memasukkan
         * noreg + PIN baru.
         */
        return redirect()
            ->to(base_url('donatur'))
            ->with(
                'success',
                'PIN berhasil diubah. Silakan login menggunakan PIN baru Anda.'
            );
    }


    /**
     * ============================================================
     * LOGOUT
     * GET /donatur/logout
     * ============================================================
     */
    public function logout()
    {
        session()->remove('id_donatur');

        return redirect()
            ->to(base_url('donatur'))
            ->with(
                'success',
                'Anda telah keluar dari akun donatur.'
            );
    }


    /**
     * ============================================================
     * VERIFY RECAPTCHA
     * ============================================================
     */
    protected function verifyRecaptcha(): bool
    {
        $recaptchaResponse = trim(
            (string) $this->request->getPost('g-recaptcha-response')
        );


        if ($recaptchaResponse === '') {
            return false;
        }


        $secretKey = RECAPTCHA_SECRET_KEY;


        $client = service('curlrequest', [
            'timeout'         => 10,
            'connect_timeout' => 5,
            'http_errors'     => false,
        ]);


        try {

            $response = $client->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'form_params' => [
                        'secret'   => $secretKey,
                        'response' => $recaptchaResponse,
                        'remoteip' => $this->request->getIPAddress(),
                    ],
                ]
            );


            $responseData = json_decode(
                $response->getBody()
            );


            return !empty($responseData->success);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'reCAPTCHA verification error: ' .
                $e->getMessage()
            );

            return false;
        }
    }
}