<?php

namespace App\Controllers;

use App\Models\AkunModel;
use App\Models\TokenModel;
use CodeIgniter\I18n\Time;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ForgotController extends BaseController
{
    protected $akunModel;
    protected $tokenModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
        $this->tokenModel = new TokenModel();
    }

    public function index()
    {
        $data['sitekey'] = RECAPTCHA_SITE_KEY;
        return view('admin/auth/v_forgot', $data);
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');

        $secretKey = RECAPTCHA_SECRET_KEY;

        $verifyURL = "https://www.google.com/recaptcha/api/siteverify";
        $response = file_get_contents($verifyURL . "?secret={$secretKey}&response={$recaptchaResponse}");
        $responseData = json_decode($response);

        if (!$responseData->success) {
            return redirect()->back()->with('error', 'Verifikasi reCAPTCHA gagal!');
        }

        $user = $this->akunModel->getUserByEmail($email);

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar di sistem kami.');
        }

        if ($this->tokenModel->checkActiveToken($user->id_akun)) {
            return redirect()->back()->with('error', 'Link reset sudah dikirim sebelumnya. Cek folder Inbox/Spam email Anda.');
        }

        $token = bin2hex(random_bytes(32));
        $this->tokenModel->save([
            'id_akun'    => $user->id_akun,
            'token'      => $token,
            'expired_at' => Time::now('Asia/Jakarta')->addMinutes(60)->toDateTimeString(),
        ]);

        $emailService = \Config\Services::email();
        $resetLink = base_url('admin/reset-password/' . $token);

        $emailService->setTo($email);
        $emailService->setSubject('Permintaan Reset Password - Masjid Al Manaar');
        
        $message = $this->templateEmailReset($user->nama, $resetLink);
        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } else {
            $data = $emailService->printDebugger(['headers']);
            print_r($data);
            die();
            return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }
    }

    public function resetPassword($token)
    {
        $tokenData = $this->tokenModel->validateToken($token);

        if (!$tokenData) {
            return redirect()->to('admin/forgot-password')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        $data = [
            'sitekey' => RECAPTCHA_SITE_KEY,
            'token'   => $token
        ];
        return view('admin/auth/v_reset', $data);
    }

    public function updatePassword()
    {
        $tokenStr = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $pass_confirm = $this->request->getPost('pass_confirm');
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');

        $secretKey = RECAPTCHA_SECRET_KEY;

        $verifyURL = "https://www.google.com/recaptcha/api/siteverify";
        $response = file_get_contents($verifyURL . "?secret={$secretKey}&response={$recaptchaResponse}");
        $responseData = json_decode($response);

        if (!$responseData->success) {
            return redirect()->back()->with('error', 'Verifikasi reCAPTCHA gagal!');
        }

        if ($password !== $pass_confirm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        $token = $this->tokenModel->validateToken($tokenStr);

        if (!$token) {
            return redirect()->to('admin/forgot-password')->with('error', 'Token kadaluarsa, silakan minta link baru.');
        }

        $this->akunModel->update($token->id_akun, [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

       $this->tokenModel->update($token->id_token, [
            'used_at' => Time::now('Asia/Jakarta')->toDateTimeString()
        ]);

        $user = $this->akunModel->find($token->id_akun); 
    
        if ($user) {
            $this->sendNotificationSuccess($user->email, $user->nama);
        }

        return redirect()->to('admin/login')->with('success', 'Password berhasil diperbarui. Silakan login.');
    }

    private function templateEmailReset($nama, $link)
    {
        return "
        <div style='font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
            <h2 style='color: #2563eb;'>Reset Password</h2>
            <p>Halo <strong>{$nama}</strong>,</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda di <strong>Sistem Masjid Al Manaar</strong>.</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$link}' style='background-color: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Atur Ulang Password</a>
            </div>
            <p>Link ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <small style='color: #777;'>Email ini dikirim secara otomatis oleh sistem, mohon tidak membalas.</small>
        </div>";
    }

    private function sendNotificationSuccess($email, $nama)
    {
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Password Anda Berhasil Diubah');
        $emailService->setMessage("
            <div style='font-family: sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #16a34a;'>Berhasil!</h2>
                <p>Halo <strong>{$nama}</strong>,</p>
                <p>Password akun Anda telah berhasil diubah pada " . date('d M Y H:i') . " WIB.</p>
                <p>Jika Anda tidak merasa melakukan perubahan ini, segera hubungi Admin IT Masjid Al Manaar.</p>
            </div>
        ");
        $emailService->send();
    }
}
