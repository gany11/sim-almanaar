<?php

namespace App\Controllers\Admin;

use App\Models\FiturModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FeatureController extends BaseController
{
    protected $fiturModel;

    public function __construct()
    {
        $this->fiturModel = new FiturModel();
    }

    public function index()
    {
        // Mengambil daftar kategori unik (distinct) untuk pilihan filter di view
        $categories = $this->fiturModel->select('kategori')->distinct()->orderBy('kategori', 'ASC')->findColumn('kategori');

        return view('admin/features/v_index', [
            'title'      => 'Manajemen Fitur',
            'categories' => $categories ?? []
        ]);
    }

    public function list()
    {
        $kategori = $this->request->getPost('kategori');
        $jenis    = $this->request->getPost('jenis');
        $maintenance = $this->request->getPost('maintenance');
        
        $builder = $this->fiturModel;

        if (!empty($kategori)) {
            $builder->where('kategori', $kategori);
        }

        if (!empty($jenis)) {
            $builder->where('jenis', $jenis);
        }

        if ($maintenance !== null && $maintenance !== '') {
            $builder->where('is_maintenance', $maintenance);
        }

        $data['features'] = $builder->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/features/v_list_partial', $data);
    }

    public function create()
    {
        $categories = $this->fiturModel->select('kategori')->distinct()->findColumn('kategori');

        return view('admin/features/v_form', [
            'title'      => 'Tambah Fitur Baru',
            'categories' => $categories ?? []
        ]);
    }

    public function edit($id)
    {
        $feature = $this->fiturModel->find($id);
        if (!$feature) {
            return redirect()->to('admin/features')->with('error', 'Data fitur tidak ditemukan.');
        }

        $categories = $this->fiturModel->select('kategori')->distinct()->findColumn('kategori');

        return view('admin/features/v_form', [
            'title'      => 'Edit Fitur',
            'feature'    => $feature,
            'categories' => $categories ?? []
        ]);
    }

    public function save() 
    { 
        return $this->_store(); 
    }

    public function update($id) 
    { 
        $feature = $this->fiturModel->find($id);
        if (!$feature) {
            return redirect()->to('admin/features')->with('error', 'Data fitur tidak ditemukan.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null)
    {
        $errors = [];

        // Validasi kode fitur (unik, abaikan ID yang sedang diedit jika mode edit)
        $kodeUnik = $id ? "required|is_unique[fitur.kode_fitur,id_fitur,{$id}]" : 'required|is_unique[fitur.kode_fitur]';

        $rules = [
            'kode_fitur' => [
                'label'  => 'Kode Fitur',
                'rules'  => $kodeUnik,
                'errors' => [
                    'required'   => 'Kode fitur wajib diisi.',
                    'is_unique'  => 'Kode fitur sudah terdaftar, gunakan kode lain.'
                ]
            ],
            'kategori' => [
                'label'  => 'Kategori',
                'rules'  => 'required',
                'errors' => ['required' => 'Kategori wajib diisi atau dipilih.']
            ],
            'nama_fitur' => [
                'label'  => 'Nama Fitur',
                'rules'  => 'required',
                'errors' => ['required' => 'Nama fitur wajib diisi.']
            ],
            'jenis' => [
                'label'  => 'Jenis Akses',
                'rules'  => 'required|in_list[public,auth,hybrid]',
                'errors' => [
                    'required' => 'Jenis akses wajib dipilih.',
                    'in_list'  => 'Pilihan jenis akses tidak valid.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $dataFeature = [
                'kode_fitur' => $this->request->getPost('kode_fitur'),
                'kategori'   => $this->request->getPost('kategori'),
                'nama_fitur' => $this->request->getPost('nama_fitur'),
                'jenis'      => $this->request->getPost('jenis'),
                'is_maintenance' => $this->request->getPost('is_maintenance') ? 1 : 0,
                'deskripsi'  => $this->request->getPost('deskripsi'),
            ];

            if ($id) {
                $dataFeature['updated_by'] = session()->get('id_akun');
                $this->fiturModel->update($id, $dataFeature);
            } else {
                $dataFeature['created_by'] = session()->get('id_akun');
                $this->fiturModel->insert($dataFeature);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data fitur.');
            }

            return redirect()->to('admin/features')->with('success', 'Data fitur berhasil disimpan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id_fitur');
        $feature = $this->fiturModel->find($id);

        if (!$feature) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data fitur tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Catat akun yang melakukan penghapusan sebelum soft delete
        $this->fiturModel->update($id, [
            'deleted_by' => session()->get('id_akun') 
        ]);

        // Proses soft delete
        if ($this->fiturModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data fitur berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data fitur.'
        ])->setStatusCode(500);
    }

    /**
     * Menampilkan halaman detail fitur beserta daftar prasyaratnya
     */
    public function detail($id)
    {
        $feature = $this->fiturModel->find($id);
        if (!$feature) {
            return redirect()->to('admin/features')->with('error', 'Data fitur tidak ditemukan.');
        }

        // Ambil ID fitur yang sudah terdaftar sebagai prasyarat untuk fitur ini
        $prasyaratModel = new \App\Models\FiturPrasyaratModel();
        $existingPrerequisites = $prasyaratModel->where('id_fitur', $id)->findColumn('id_prasyarat') ?? [];

        // Gabungkan id_fitur utama itu sendiri dan id_prasyarat yang sudah ada agar tidak muncul di pilihan
        $excludedIds = array_merge([$id], $existingPrerequisites);

        // Ambil fitur lain yang BELUM menjadi prasyarat
        $availableFeatures = $this->fiturModel->whereNotIn('id_fitur', $excludedIds)->findAll();

        return view('admin/features/v_detail', [
            'title'             => 'Detail Fitur: ' . $feature['nama_fitur'],
            'feature'           => $feature,
            'availableFeatures' => $availableFeatures
        ]);
    }

    /**
     * Menampilkan data tabel parsial prasyarat fitur (untuk AJAX/DataTables)
     */
    public function prerequisiteList($idFitur)
    {
        $db = \Config\Database::connect();
        
        // Ambil data prasyarat yang terhubung dengan fitur ini beserta informasi detail fitur syaratnya
        $prerequisites = $db->table('fitur_prasyarat fp')
            ->select('fp.id_fitur_prasyarat, f.id_fitur, f.kode_fitur, f.nama_fitur, f.kategori, f.jenis')
            ->join('fitur f', 'f.id_fitur = fp.id_prasyarat')
            ->where('fp.id_fitur', $idFitur)
            ->where('fp.deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $data['prerequisites'] = $prerequisites;
        $data['id_fitur'] = $idFitur;

        return view('admin/features/v_prerequisite_list_partial', $data);
    }

    /**
     * Menyimpan relasi prasyarat baru
     */
    public function prerequisiteSave()
    {
        $idFitur   = $this->request->getPost('id_fitur');
        $idSyarat  = $this->request->getPost('id_prasyarat'); // ID fitur yang dijadikan syarat

        if (empty($idFitur) || empty($idSyarat)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data prasyarat tidak lengkap.'
            ])->setStatusCode(400);
        }

        // Validasi pencegahan: Tidak boleh memilih diri sendiri
        if ($idFitur == $idSyarat) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sebuah fitur tidak dapat menjadi prasyarat bagi dirinya sendiri.'
            ])->setStatusCode(400);
        }

        $prasyaratModel = new \App\Models\FiturPrasyaratModel();

        // Cek apakah relasi sudah ada sebelumnya (termasuk yang soft delete)
        $exists = $prasyaratModel->withDeleted()
            ->where('id_fitur', $idFitur)
            ->where('id_prasyarat', $idSyarat)
            ->first();

        if ($exists) {
            if ($exists['deleted_at'] !== null) {
                // Jika sebelumnya pernah dihapus (soft delete), aktifkan kembali
                $prasyaratModel->update($exists['id_fitur_prasyarat'], [
                    'deleted_at' => null,
                    'updated_by' => session()->get('id_akun')
                ]);
                return $this->response->setJSON([
                    'status'  => 'success', 
                    'message' => 'Prasyarat berhasil ditambahkan kembali.'
                ]);
            }
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Fitur ini sudah terdaftar sebagai prasyarat sebelumnya.'
            ])->setStatusCode(400);
        }

        // Simpan baru
        $prasyaratModel->insert([
            'id_fitur'     => $idFitur,
            'id_prasyarat' => $idSyarat,
            'created_by'   => session()->get('id_akun')
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Prasyarat fitur berhasil ditambahkan.'
        ]);
    }

    /**
     * Menghapus relasi prasyarat (Soft Delete)
     */
    public function prerequisiteDelete()
    {
        $idPrasyaratRelasi = $this->request->getPost('id_fitur_prasyarat');
        
        $prasyaratModel = new \App\Models\FiturPrasyaratModel();
        $relation = $prasyaratModel->find($idPrasyaratRelasi);

        if (!$relation) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data relasi prasyarat tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $prasyaratModel->update($idPrasyaratRelasi, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($prasyaratModel->delete($idPrasyaratRelasi)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Prasyarat fitur berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus prasyarat fitur.'
        ])->setStatusCode(500);
    }

    public function toggleMaintenance()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses tidak sah.']);
        }

        $idFitur = $this->request->getPost('id_fitur');
        $fitur = $this->fiturModel->find($idFitur);

        if (!$fitur) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data fitur tidak ditemukan.']);
        }

        // PERBAIKAN: Gunakan format array ['is_maintenance'] karena model mengembalikan array
        $currentStatus = $fitur['is_maintenance'] ?? 0;
        $newStatus = ($currentStatus == 1) ? 0 : 1;

        $update = $this->fiturModel->update($idFitur, [
            'is_maintenance' => $newStatus,
            'updated_by'     => session()->get('id_akun')
        ]);

        if ($update) {
            $statusText = $newStatus == 1 ? 'Maintenance' : 'Aktif';
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => "Status fitur berhasil diubah menjadi {$statusText}.",
                'new_status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengubah status maintenance.']);
    }
}