<?php

namespace App\Controllers\Admin;

use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BeritaController extends BaseController
{
    protected $publikasiModel;

    public function __construct() {
        $this->publikasiModel = new PublikasiModel();
    }

    public function index() {
        return view('admin/berita/index', ['title' => 'Daftar Berita']);
    }

    public function create() {
        return view('admin/berita/form_tambah', ['title' => 'Tambah Berita']);
    }

    public function save() {
        if (!$this->validate($this->publikasiModel->validationBerita)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileSampul = $this->request->getFile('sampul');
        $namaSampul = ($fileSampul && $fileSampul->isValid()) ? $fileSampul->getRandomName() : 'default.jpg';
        if ($namaSampul !== 'default.jpg') $fileSampul->move('uploads/berita', $namaSampul);

        $this->publikasiModel->save([
            'id_jenis_publikasi' => 1,
            'judul'      => $this->request->getPost('judul'),
            'slug'       => url_title($this->request->getPost('judul'), '-', true),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'sampul'     => $namaSampul,
            'created_by' => session()->get('id_user')
        ]);

        return redirect()->to('admin/berita')->with('success', 'Berita berhasil ditambah.');
    }

    public function edit($id) {
        $berita = $this->publikasiModel->find($id);
        if (!$berita) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('admin/berita/form_edit', [
            'title'  => 'Edit Berita',
            'berita' => $berita
        ]);
    }

    public function update($id) {
        $beritaLama = $this->publikasiModel->find($id);
        
        if (!$this->validate($this->publikasiModel->validationBerita)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileSampul = $this->request->getFile('sampul');
        $namaSampul = $beritaLama['sampul'];

        if ($fileSampul && $fileSampul->isValid()) {
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('uploads/berita', $namaSampul);
            if ($beritaLama['sampul'] != 'default.jpg' && file_exists('uploads/berita/' . $beritaLama['sampul'])) {
                unlink('uploads/berita/' . $beritaLama['sampul']);
            }
        }

        $this->publikasiModel->update($id, [
            'judul'      => $this->request->getPost('judul'),
            'slug'       => url_title($this->request->getPost('judul'), '-', true),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'sampul'     => $namaSampul,
            'updated_by' => session()->get('id_user')
        ]);

        return redirect()->to('admin/berita')->with('success', 'Berita berhasil diupdate.');
    }

    public function delete($id) {
        $berita = $this->publikasiModel->find($id);
        if ($berita) {
            // Karena pakai SoftDeletes, file tidak harus dihapus sekarang. 
            // Tapi jika ingin benar-benar hilang:
            $this->publikasiModel->delete($id);
            return redirect()->to('admin/berita')->with('success', 'Berita berhasil dihapus.');
        }
    }
}
