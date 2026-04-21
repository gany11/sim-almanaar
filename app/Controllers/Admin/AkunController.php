<?php

namespace App\Controllers\Admin;

use App\Models\AkunModel;
use App\Models\PeranModel;
use App\Models\TokenModel;
use CodeIgniter\I18n\Time;


use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AkunController extends BaseController
{
    protected $akunModel;
    protected $peranModel;
    protected $tokenModel;

    public function __construct()
    {
        $this->akunModel  = new AkunModel();
        $this->peranModel = new PeranModel();
        $this->tokenModel = new TokenModel();
    }

    public function index() {
        return view('admin/akun/v_index', ['title' => 'Manajemen Akun']);
    }

    public function list() {
        $status = $this->request->getPost('status');
        $id_sesi = session()->get('id_akun');

        $query = $this->akunModel->select('akun.*, peran.nama AS nama_peran')
                                 ->join('peran', 'peran.id_peran = akun.id_peran');

        if ($status != "") {
            $query->where('akun.status', $status);
        }

        $data['akun'] = $query->findAll();
        $data['id_sesi'] = $id_sesi;

        return view('admin/akun/v_list_partial', $data);
    }

    public function updateStatus() {
        $id = $this->request->getPost('id_akun');
        $status_baru = $this->request->getPost('status');

        if ($this->akunModel->update($id, ['status' => $status_baru])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status akun berhasil diperbarui.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui status.']);
    }

    public function registrasi()
    {
        $data = [
            'title' => 'Registrasi Akun',
            'peran' => $this->peranModel->findAll()
        ];
        return view('admin/akun/v_registrasi', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();

        $input['password'] = password_hash('AlManaar' . rand(100, 999), PASSWORD_DEFAULT);
        $input['status']   = 'aktif';

        if ($this->akunModel->save($input)) {
            $newUserId = $this->akunModel->getInsertID();

            $token = bin2hex(random_bytes(32));
            $this->tokenModel->save([
                'id_akun'    => $newUserId,
                'token'      => $token,
                'expired_at' => Time::now('Asia/Jakarta')->addDays(1)->toDateTimeString(),
            ]);

            $this->sendActivationEmail($input['email'], $input['nama'], $token, $input['username']);

            return redirect()->to('admin/akun')->with('sukses', 'Akun berhasil dibuat dan email aktivasi telah dikirim.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->akunModel->errors())
                ->with('error', 'Gagal mendaftarkan akun. Silakan periksa kolom yang berwarna merah.');
        }
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
}
