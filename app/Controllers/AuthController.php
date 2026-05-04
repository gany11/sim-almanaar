<?php

namespace App\Controllers;

use App\Models\AkunModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $akunModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
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

                $sessionData = [
                    'id_akun'    => $user->id_akun,
                    'id_peran'   => $user->id_peran,
                    'nama_peran' => $user->nama_peran,
                    'nama'       => $user->nama,
                    'logged_in'  => true
                ];
                session()->set($sessionData);

                return redirect()->to('admin/dashboard')->with('success', "Selamat datang, {$user->nama}!");
            } else {
                return redirect()->back()->with('error', 'Username atau password yang Anda masukkan salah!');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau password yang Anda masukkan salah!');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('admin/login')->with('success', 'Berhasil logout.');
    }
}
