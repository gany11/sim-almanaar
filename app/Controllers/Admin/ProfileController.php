<?php

namespace App\Controllers\Admin;

use App\Models\AkunModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Services\WhatsAppService;

class ProfileController extends BaseController
{
    protected $akunModel;
    protected WhatsAppService $whatsappService;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
        $this->whatsappService = new WhatsAppService();
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
        $id_akun =
            session()->get('id_akun');

        $oldAccount =
            $this->akunModel
                ->find($id_akun);


        if (!$oldAccount) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Data akun tidak ditemukan.'
                );
        }

        $data = [

            'id_akun' =>
                $id_akun,

            'nama' =>
                trim(
                    $this->request
                        ->getPost('nama')
                ),

            'email' =>
                trim(
                    $this->request
                        ->getPost('email')
                ),

            'telepon' =>
                trim(
                    $this->request
                        ->getPost('telepon')
                ),

            'username' =>
                trim(
                    $this->request
                        ->getPost('username')
                ),

        ];

        if (
            !$this->akunModel
                ->validate($data)
        ) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->akunModel
                        ->errors()
                )
                ->with(
                    'error',
                    'Profil gagal diperbarui. Silakan periksa kolom yang berwarna merah.'
                );
        }

        $oldPhone =
            trim(
                (string) (
                    $oldAccount->telepon
                    ?? ''
                )
            );


        $newPhone =
            $data['telepon'];


        $phoneChanged =
            $oldPhone !== $newPhone;

        if ($phoneChanged) {

            try {

                $checkNumber =
                    $this->whatsappService
                        ->checkNumber(
                            $newPhone
                        );


                // log_message(
                //     'debug',
                //     'CHECK WA PHONE: ' .
                //     $newPhone
                // );


                // log_message(
                //     'debug',
                //     'CHECK WA RESULT: ' .
                //     json_encode(
                //         $checkNumber,
                //         JSON_UNESCAPED_UNICODE |
                //         JSON_UNESCAPED_SLASHES
                //     )
                // );


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
                            'Profil gagal diperbarui. Silakan periksa kolom telepon.'
                        );
                }


            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'CHECK WA ERROR: ' .
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
                        'Profil gagal diperbarui. Silakan periksa kolom telepon.'
                    );
            }
        }


        if (
            $this->akunModel
                ->save($data)
        ) {

            session()->set([

                'nama' =>
                    $data['nama'],

                'telepon' =>
                    $data['telepon'],

                'username' =>
                    $data['username'],

            ]);


            return redirect()
                ->back()
                ->with(
                    'success',
                    'Profil berhasil diperbarui.'
                );
        }

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'errors',
                $this->akunModel
                    ->errors()
            )
            ->with(
                'error',
                'Profil gagal diperbarui. Silakan periksa kolom yang berwarna merah.'
            );
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
