<?php

namespace App\Controllers\Admin;

use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class NewsController extends BaseController
{
    protected $publikasiModel;

    public function __construct() {
        $this->publikasiModel = new PublikasiModel();
    }

    public function index()
    {
        return view('admin/news/v_index', ['title' => 'Manajemen Berita']);
    }

    public function list()
    {
        $status = $this->request->getPost('status');
        $builder = $this->publikasiModel->where('id_jenis_publikasi', 1);

        if ($status !== '' && $status !== null) {
            $builder->where('status', $status);
        }

        $data['berita'] = $builder->orderBy('created_at', 'DESC')->findAll();
        return view('admin/news/v_list_partial', $data);
    }

    public function create() {
        return view('admin/news/v_form', ['title' => 'Tambah Berita']);
    }

    public function edit($id) {
        $berita = $this->publikasiModel->find($id);
        if (!$berita) {
            return redirect()->to('admin/news')->with('error', 'Gagal menghapus! Data tidak ditemukan.');
        }

        return view('admin/news/v_form', [
            'title' => 'Edit Berita',
            'berita' => $berita
        ]);
    }

    public function save() {
        return $this->_store();
    }

    public function update($id) {
        $berita = $this->publikasiModel->find($id);
        if (!$berita) {
            return redirect()->to('admin/news')->with('error', 'Gagal menghapus! Data tidak ditemukan.');
        }
        
        return $this->_store($id);
    }

    private function _store($id = null) {
        $rules = $this->publikasiModel->validationNews;

        $judul = $this->request->getPost('judul');

        $cekJudul = $this->publikasiModel->where([
            'judul' => $judul,
            'id_jenis_publikasi' => 1
        ]);

        if ($id) {
            $cekJudul->where('id_publikasi !=', $id);
        }

        if ($cekJudul->first()) {
            return redirect()->back()->withInput()->with('errors', [
                'judul' => 'Judul berita sudah digunakan di kategori berita, silakan gunakan judul lain.'
            ]);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $beritaLama = $id ? $this->publikasiModel->find($id) : null;
        $fileSampul = $this->request->getFile('sampul');
        $namaSampul = $beritaLama ? $beritaLama['sampul'] : 'default.png';

        if ($fileSampul && $fileSampul->isValid() && !$fileSampul->hasMoved()) {
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('uploads/berita', $namaSampul);
            
            if ($beritaLama && $namaSampul != $beritaLama['sampul'] && $beritaLama['sampul'] != 'default.png') {
                @unlink('uploads/berita/' . $beritaLama['sampul']);
            }
        }

        $data = [
            'id_jenis_publikasi' => 1,
            'judul'      => $this->request->getPost('judul'),
            'slug'       => url_title($this->request->getPost('judul'), '-', true),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'sampul'     => $namaSampul,
            'status'     => $this->request->getPost('status') === 'aktif' ? 'aktif' : 'pasif',
        ];

        $currentUserId = session()->get('id_akun');

        if (!$id) {
            $data['created_by'] = $currentUserId;
            $this->publikasiModel->insert($data);
            $pesan = 'Berita baru berhasil diterbitkan.';
        } else {
            $data['updated_by'] = $currentUserId;
            $this->publikasiModel->update($id, $data);
            $pesan = 'Perubahan berita berhasil disimpan.';
        }

        return redirect()->to('admin/news')->with('success', $pesan);
    }

    public function delete() {
        $id = $this->request->getPost('id_publikasi');
        $berita = $this->publikasiModel->find($id);

        if (!$berita) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data berita tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Jejak audit sebelum soft delete
        $this->publikasiModel->update($id, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($this->publikasiModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Berita berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data.'
        ])->setStatusCode(500);
    }
}
