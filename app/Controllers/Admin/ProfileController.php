<?php

namespace App\Controllers\Admin;

use App\Models\AkunModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    protected $akunModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
    }

    public function index()
    {
        $id_akun = session()->get('id_akun');
        $data = [
            'title' => 'Profil Akun',
            'user'  => $this->akunModel->find($id_akun)
        ];
        return view('admin/account/v_profile', $data);
    }

    public function updateProfile()
    {
        $id_akun = session()->get('id_akun');
        $data = [
            'id_akun' => $id_akun,
            'nama'    => $this->request->getPost('nama'),
            'email'   => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'username'=> $this->request->getPost('username'),
        ];

        if ($this->akunModel->save($data)) {
            session()->set('nama', $data['nama']);
            return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->akunModel->errors());
        }
    }

    public function updatePassword()
    {
        $id_akun = session()->get('id_akun');
        $pass_lama = $this->request->getPost('pass_lama');
        $pass_baru = $this->request->getPost('pass_baru');
        $pass_conf = $this->request->getPost('pass_conf');

        $user = $this->akunModel->find($id_akun);

        if (!password_verify($pass_lama, $user->password)) {
            return redirect()->back()->with('error_pass', 'Password lama Anda salah.');
        }

        if ($pass_baru !== $pass_conf) {
            return redirect()->back()->with('error_pass', 'Konfirmasi password baru tidak cocok.');
        }

        if ($this->akunModel->update($id_akun, ['password' => password_hash($pass_baru, PASSWORD_DEFAULT)])) {
            return redirect()->to('admin/logout');
        }

        return redirect()->back()->with('error_pass', 'Gagal memperbarui password.');
    }
}
