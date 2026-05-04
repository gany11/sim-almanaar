<?php

namespace App\Controllers\Admin;

use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ArticleController extends BaseController
{
    protected $publikasiModel;

    public function __construct() {
        $this->publikasiModel = new PublikasiModel();
    }

    public function index()
    {
        return view('admin/article/v_index', ['title' => 'Manajemen Artikel']);
    }

    public function list()
    {
        $status = $this->request->getPost('status');
        $builder = $this->publikasiModel->where('id_jenis_publikasi', 2);

        if ($status !== '' && $status !== null) {
            $builder->where('status', $status);
        }

        $data['artikel'] = $builder->orderBy('created_at', 'DESC')->findAll();
        return view('admin/article/v_list_partial', $data);
    }

    public function create() {
        return view('admin/article/v_form', ['title' => 'Tambah Artikel']);
    }

    public function edit($id) {
        $artikel = $this->publikasiModel->find($id);
        if (!$artikel) {
            return redirect()->to('admin/article')->with('error', 'Data artikel tidak ditemukan atau sudah dihapus.');
        }

        return view('admin/article/v_form', [
            'title' => 'Edit Artikel',
            'artikel' => $artikel
        ]);
    }

    public function save() {
        return $this->_store();
    }

    public function update($id) {
        $artikel = $this->publikasiModel->find($id);
        if (!$artikel) {
            return redirect()->to('admin/article')->with('error', 'Data artikel tidak ditemukan atau sudah dihapus.');
        }

        return $this->_store($id);
    }

    private function _store($id = null) {
        $rules = $this->publikasiModel->validationArticle;
        $judul = $this->request->getPost('judul');

        $cekJudul = $this->publikasiModel->where([
            'judul' => $judul,
            'id_jenis_publikasi' => 2
        ]);

        if ($id) {
            $cekJudul->where('id_publikasi !=', $id);
        }

        if ($cekJudul->first()) {
            return redirect()->back()->withInput()->with('errors', [
                'judul' => 'Judul artikel sudah digunakan di kategori artikel, silakan gunakan judul lain.'
            ]);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $artikelLama = $id ? $this->publikasiModel->find($id) : null;

        // --- LOGIC HANDLING SAMPUL ---
        $fileSampul = $this->request->getFile('sampul');
        $namaSampul = $artikelLama ? $artikelLama['sampul'] : 'default.png';

        if ($fileSampul && $fileSampul->isValid() && !$fileSampul->hasMoved()) {
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('uploads/artikel', $namaSampul);
            
            if ($artikelLama && $namaSampul != $artikelLama['sampul'] && $artikelLama['sampul'] != 'default.png') {
                @unlink('uploads/artikel/' . $artikelLama['sampul']);
            }
        }

        // --- LOGIC HANDLING LAMPIRAN (PDF) ---
        $fileLampiran = $this->request->getFile('lampiran');
        $namaLampiran = $artikelLama ? $artikelLama['lampiran'] : null;

        if ($fileLampiran && $fileLampiran->isValid() && !$fileLampiran->hasMoved()) {
            $namaLampiran = $fileLampiran->getRandomName();
            $fileLampiran->move('uploads/lampiran', $namaLampiran);
            
            // Hapus lampiran lama jika ada
            if ($artikelLama && $artikelLama['lampiran']) {
                @unlink('uploads/lampiran/' . $artikelLama['lampiran']);
            }
        }

        $data = [
            'id_jenis_publikasi' => 2,
            'judul'              => $this->request->getPost('judul'),
            'slug'               => url_title($this->request->getPost('judul'), '-', true),
            'sumber_penulis'     => $this->request->getPost('sumber_penulis'), // Field baru
            'deskripsi'          => $this->request->getPost('deskripsi'),
            'sampul'             => $namaSampul,
            'lampiran'           => $namaLampiran, // Field baru
            'status'             => $this->request->getPost('status') === 'aktif' ? 'aktif' : 'pasif',
        ];

        $currentUserId = session()->get('id_akun');

        if (!$id) {
            $data['created_by'] = $currentUserId;
            $this->publikasiModel->insert($data);
            $pesan = 'Artikel baru berhasil diterbitkan.';
        } else {
            $data['updated_by'] = $currentUserId;
            $this->publikasiModel->update($id, $data);
            $pesan = 'Perubahan artikel berhasil disimpan.';
        }

        return redirect()->to('admin/article')->with('success', $pesan);
    }

    public function delete() {
        $id = $this->request->getPost('id_publikasi');
        $artikel = $this->publikasiModel->find($id);

        if (!$artikel) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data artikel tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $this->publikasiModel->update($id, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($this->publikasiModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Artikel berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus artikel.'
        ])->setStatusCode(500);
    }
}
