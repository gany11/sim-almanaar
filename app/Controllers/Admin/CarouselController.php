<?php

namespace App\Controllers\Admin;

use App\Models\CarouselModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CarouselController extends BaseController
{
    protected $carouselModel;

    public function __construct()
    {
        $this->carouselModel = new CarouselModel();
    }

    public function index()
    {
        return view('admin/carousel/v_index', [
            'title' => 'Manajemen Carousel'
        ]);
    }

    public function list()
    {
        $status = $this->request->getPost('status');
        
        $builder = $this->carouselModel;

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $data['carousel'] = $builder->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/carousel/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/carousel/v_form', [
            'title' => 'Tambah Carousel Baru'
        ]);
    }

    public function edit($id)
    {
        $carousel = $this->carouselModel->find($id);
        if (!$carousel) {
            return redirect()->to('admin/carousel')->with('error', 'Data carousel tidak ditemukan.');
        }

        return view('admin/carousel/v_form', [
            'title'    => 'Edit Carousel',
            'carousel' => $carousel
        ]);
    }

    public function save() 
    { 
        return $this->_store(); 
    }

    public function update($id) 
    { 
        $carousel = $this->carouselModel->find($id);
        if (!$carousel) {
            return redirect()->to('admin/carousel')->with('error', 'Data carousel tidak ditemukan.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null)
    {
        $errors = [];

        $fileValidasi = $id ? 'permit_empty|is_image[file]|mime_in[file,image/png,image/jpg,image/jpeg,image/webp]|max_size[file,2048]' 
                            : 'uploaded[file]|is_image[file]|mime_in[file,image/png,image/jpg,image/jpeg,image/webp]|max_size[file,2048]';

        $rules = [
            'file' => [
                'label' => 'File Gambar',
                'rules' => $fileValidasi,
                'errors' => [
                    'uploaded' => 'File gambar carousel wajib diunggah.',
                    'is_image' => 'File yang diunggah harus berupa gambar.',
                    'mime_in'  => 'Format gambar harus berupa png, jpg, jpeg, atau webp.',
                    'max_size' => 'Ukuran file gambar maksimal 2MB.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
        }

        $startedAt = $this->request->getPost('started_at');
        $endedAt   = $this->request->getPost('ended_at');

        if (!empty($startedAt) && !empty($endedAt)) {
            if (strtotime($endedAt) <= strtotime($startedAt)) {
                $errors['ended_at'] = 'Tanggal selesai tidak boleh kurang dari atau sama dengan tanggal mulai.';
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $fileImage = $this->request->getFile('file');
            $fileName = null;

            // 3. Proses Upload File jika ada file baru yang diunggah
            if ($fileImage && $fileImage->isValid() && !$fileImage->hasMoved()) {
                $fileName = $fileImage->getRandomName();
                $uploadPath = FCPATH . 'uploads/carousel';

                // Buat folder jika belum ada
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $fileImage->move($uploadPath, $fileName);

                // Jika mode Edit dan ada file baru, hapus file lama
                if ($id) {
                    $oldData = $this->carouselModel->find($id);
                    if ($oldData && !empty($oldData['file'])) {
                        $oldFile = $uploadPath . '/' . $oldData['file'];
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                }
            }

            $dataCarousel = [
                'status'     => $this->request->getPost('status') ? 'aktif' : 'pasif',
                'started_at' => !empty($startedAt) ? $startedAt : null,
                'ended_at'   => !empty($endedAt) ? $endedAt : null,
            ];

            if ($fileName) {
                $dataCarousel['file'] = $fileName;
            }

            if ($id) {
                $dataCarousel['updated_by'] = session()->get('id_akun');
                $this->carouselModel->update($id, $dataCarousel);
            } else {
                $dataCarousel['created_by'] = session()->get('id_akun');
                $this->carouselModel->insert($dataCarousel);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data carousel.');
            }

            return redirect()->to('admin/carousel')->with('success', 'Data carousel berhasil disimpan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id_carousel');
        $carousel = $this->carouselModel->find($id);

        if (!$carousel) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data carousel tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Catat user yang menghapus data
        $this->carouselModel->update($id, [
            'deleted_by' => session()->get('id_akun') 
        ]);

        // Proses soft delete
        if ($this->carouselModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data carousel berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data carousel.'
        ])->setStatusCode(500);
    }
}