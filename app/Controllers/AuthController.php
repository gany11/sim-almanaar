<?php

namespace App\Controllers;

use App\Models\AkunModel;
use App\Models\LogLoginModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $akunModel;
    protected $logLoginModel;

    public function __construct()
    {
        $this->akunModel     = new AkunModel();
        $this->logLoginModel = new LogLoginModel();
    }

    public function index()
    {
        $data['sitekey'] = RECAPTCHA_SITE_KEY;
        return view('admin/auth/v_login', $data);
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');

        $secretKey = RECAPTCHA_SECRET_KEY;
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
        $responseData = json_decode($response);

        if (!$responseData->success) {
            return redirect()->back()->with('error', 'Verifikasi reCAPTCHA gagal!');
        }

        $user = $this->akunModel->getLoginData($username);

        if ($user) {
            if (password_verify($password, $user->password)) {
                
                if ($user->status !== 'aktif') {
                    return redirect()->back()->with('error', 'Akun Anda tidak aktif. Silakan hubungi admin.');
                }

                $returnUrl = session()->get('redirect_after_login');

                // Simpan log login ke database
                $now = date('Y-m-d H:i:s');
                $idLog = $this->logLoginModel->insert([
                    'id_akun'    => $user->id_akun,
                    'login_at'   => $now,
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => (string) $this->request->getUserAgent(),
                    'status'     => 'aktif'
                ]);

                $sessionData = [
                    'id_akun'         => $user->id_akun,
                    'id_peran'        => $user->id_peran,
                    'nama_peran'      => $user->nama_peran,
                    'nama'            => $user->nama,
                    'logged_in'       => true,
                    'id_log'          => $idLog,          // Simpan ID log untuk update saat logout
                    'last_checked_at' => time()           // Penanda waktu cek berkala (1 jam)
                ];
                session()->set($sessionData);

                session()->remove('redirect_after_login');

                if ($returnUrl) {
                    return redirect()->to($returnUrl);
                }
                return redirect()->to('admin/dashboard');
            } else {
                return redirect()->back()->with('error', 'Username atau password yang Anda masukkan salah!');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau password yang Anda masukkan salah!');
        }
    }

    public function logout()
    {
        $idLog = session()->get('id_log');

        if ($idLog) {
            // Perbarui status log menjadi logout dan catat waktu logout
            $this->logLoginModel->update($idLog, [
                'logout_at' => date('Y-m-d H:i:s'),
                'status'    => 'logout'
            ]);
        }

        session()->destroy();
        return redirect()->to('admin/login')->with('success', 'Berhasil logout.');
    }
}