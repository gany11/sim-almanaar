<?php

namespace App\Controllers\Admin;

use App\Models\SdmModel;
use App\Services\WhatsAppService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SdmController extends BaseController
{
    protected SdmModel $sdmModel;
    protected WhatsAppService $whatsappService;

    public function __construct()
    {
        $this->sdmModel = new SdmModel();
        $this->whatsappService = new WhatsAppService();
    }

    public function index()
    {
        return view('admin/sdm/v_index', [
            'title' => 'Manajemen SDM / Petugas'
        ]);
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('admin/sdm');
        }

        $data['sdm'] = $this->sdmModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/sdm/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/sdm/v_form', [
            'title' => 'Tambah Data SDM Baru'
        ]);
    }

    public function edit($id)
    {
        $sdm = $this->sdmModel->find($id);
        if (!$sdm) {
            return redirect()->to('admin/sdm')->with('error', 'Data SDM tidak ditemukan.');
        }

        return view('admin/sdm/v_form', [
            'title' => 'Edit Data SDM',
            'sdm'   => $sdm
        ]);
    }

    public function save() 
    { 
        return $this->_store(); 
    }

    public function update($id) 
    { 
        $sdm = $this->sdmModel->find($id);
        if (!$sdm) {
            return redirect()->to('admin/sdm')->with('error', 'Data SDM tidak ditemukan.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null)
    {
        $errors = [];

        $rules = [
            'nama' => [
                'label' => 'Nama SDM',
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required'   => 'Nama SDM harus diisi.',
                    'min_length' => 'Nama SDM minimal 3 karakter.',
                    'max_length' => 'Nama SDM maksimal 255 karakter.',
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email|max_length[100]',
                'errors' => [
                    'valid_email' => 'Format email tidak valid.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                ]
            ],
            'telepon' => [
                'label' => 'Nomor Telepon',
                'rules' => 'permit_empty|min_length[10]|max_length[20]',
                'errors' => [
                    'min_length' => 'Nomor telepon minimal 10 digit.',
                    'max_length' => 'Nomor telepon maksimal 20 digit.',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
        }

        $phone = $this->request->getPost('telepon');

        // Validasi nomor WhatsApp menggunakan WhatsAppService jika nomor diisi
        if (!empty($phone) && empty($errors['telepon'])) {
            try {
                $checkNumber = $this->whatsappService->checkNumber($phone);

                log_message('debug', 'CHECK WA PHONE: ' . $phone);
                log_message('debug', 'CHECK WA RESULT: ' . json_encode($checkNumber, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                $registered = $checkNumber['data']['registered'] ?? false;
                $valid      = $checkNumber['data']['valid'] ?? false;

                if (!$registered || !$valid) {
                    $errors['telepon'] = 'Nomor WhatsApp tidak terdaftar atau tidak valid.';
                }
            } catch (\Throwable $e) {
                log_message('error', 'WhatsApp check-number error: ' . $e->getMessage());
                $errors['telepon'] = 'Nomor WhatsApp tidak dapat diverifikasi saat ini.';
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $dataSdm = [
                'nama'    => $this->request->getPost('nama'),
                'email'   => $this->request->getPost('email'),
                'telepon' => $phone,
                'alamat'  => $this->request->getPost('alamat'),
            ];

            if ($id) {
                $dataSdm['updated_by'] = session()->get('id_akun');
                $this->sdmModel->update($id, $dataSdm);
            } else {
                $dataSdm['created_by'] = session()->get('id_akun');
                $this->sdmModel->insert($dataSdm);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data SDM ke database.');
            }

            return redirect()->to('admin/sdm')->with('success', 'Data SDM berhasil disimpan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Akses ditolak.'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('id_sdm');
        $sdm = $this->sdmModel->find($id);

        if (!$sdm) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data SDM tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();

        // Pengecekan apakah SDM masih terikat di tabel sdm_agenda
        $cekRelasi = $db->table('sdm_agenda')
            ->where('id_sdm', $id)
            ->countAllResults();

        if ($cekRelasi > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => '<b>Gagal Menghapus!</b> Data SDM ini tidak dapat dihapus karena memiliki agenda atau penugasan terkait.'
            ]);
        }

        // Catat user yang menghapus data
        $this->sdmModel->update($id, [
            'deleted_by' => session()->get('id_akun') 
        ]);

        // Proses soft delete
        if ($this->sdmModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data SDM berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data SDM.'
        ])->setStatusCode(500);
    }
}