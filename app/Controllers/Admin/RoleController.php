<?php

namespace App\Controllers\Admin;

use App\Models\PeranModel;
use App\Models\PeranFiturModel;
use App\Models\FiturModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class RoleController extends BaseController
{
    protected $peranModel;
    protected $peranFiturModel;
    protected $fiturModel;

    public function __construct()
    {
        $this->peranModel      = new PeranModel();
        $this->peranFiturModel = new PeranFiturModel();
        $this->fiturModel      = new FiturModel();
    }

    public function index()
    {
        return view('admin/roles/v_index', [
            'title' => 'Manajemen Peran'
        ]);
    }

    public function list()
    {
        $data['roles'] = $this->peranModel->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/roles/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/roles/v_form', [
            'title' => 'Tambah Peran Baru'
        ]);
    }

    public function edit($id)
    {
        $role = $this->peranModel->find($id);
        if (!$role) {
            return redirect()->to('admin/roles')->with('error', 'Data peran tidak ditemukan.');
        }

        return view('admin/roles/v_form', [
            'title' => 'Edit Peran',
            'role'  => $role
        ]);
    }

    public function save() 
    { 
        return $this->_store(); 
    }

    public function update($id) 
    { 
        $role = $this->peranModel->find($id);
        if (!$role) {
            return redirect()->to('admin/roles')->with('error', 'Data peran tidak ditemukan.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null)
    {
        $rules = [
            'nama' => [
                'label'  => 'Nama Peran',
                'rules'  => 'required',
                'errors' => ['required' => 'Nama peran wajib diisi.']
            ],
            'class_color' => [
                'label'  => 'Warna Badge',
                'rules'  => 'required',
                'errors' => ['required' => 'Warna badge wajib diisi.']
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataPeran = [
            'nama'        => $this->request->getPost('nama'),
            'class_color' => $this->request->getPost('class_color'),
        ];

        if ($id) {
            $dataPeran['updated_by'] = session()->get('id_akun');
            $this->peranModel->update($id, $dataPeran);
        } else {
            $dataPeran['created_by'] = session()->get('id_akun');
            $this->peranModel->insert($dataPeran);
        }

        return redirect()->to('admin/roles')->with('success', 'Data peran berhasil disimpan!');
    }

    /**
     * Menampilkan halaman detail peran dengan matriks fitur per kategori
     */
    public function detail($id)
    {
        $role = $this->peranModel->find($id);
        if (!$role) {
            return redirect()->to('admin/roles')->with('error', 'Data peran tidak ditemukan.');
        }

        // Ambil seluruh fitur diurutkan berdasarkan kategori
        $allFeatures = $this->fiturModel->where('jenis', 'auth')->orderBy('kategori', 'ASC')->findAll();
        
        // Kelompokkan fitur berdasarkan kategorinya
        $groupedFeatures = [];
        foreach ($allFeatures as $f) {
            $groupedFeatures[$f['kategori']][] = $f;
        }

        // Ambil ID fitur yang sudah dimiliki oleh peran ini
        $assignedFeatureIds = $this->peranFiturModel
            ->where('id_peran', $id)
            ->findColumn('id_fitur') ?? [];

        return view('admin/roles/v_detail', [
            'title'              => 'Detail Peran & Akses: ' . $role['nama'],
            'role'               => $role,
            'groupedFeatures'    => $groupedFeatures,
            'assignedFeatureIds' => $assignedFeatureIds
        ]);
    }

    /**
     * Menyimpan sinkronisasi fitur peran secara massal (Matrix Sync) dengan Validasi Prasyarat (Logika OR)
     */
    /**
     * Menyimpan sinkronisasi fitur peran secara massal (Matrix Sync) dengan Validasi Prasyarat (Logika OR) & Format Kategori (Nama Fitur)
     */
    public function featureSync()
    {
        $idPeran       = $this->request->getPost('id_peran');
        $selectedFitur = $this->request->getPost('id_fitur') ?? [];

        if (empty($idPeran)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Peran tidak valid.'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // 1. VALIDASI PRASYARAT (LOGIKA OR / ATAU)
        if (!empty($selectedFitur)) {
            $builderPrasyarat = $db->table('fitur_prasyarat');
            
            foreach ($selectedFitur as $idFitur) {
                // Ambil daftar ID prasyarat untuk fitur ini
                $prasyaratList = $builderPrasyarat->select('id_prasyarat')
                    ->where('id_fitur', $idFitur)
                    ->where('deleted_at IS NULL')
                    ->get()
                    ->getResultArray();

                $requiredIds = array_column($prasyaratList, 'id_prasyarat');

                // Jika fitur ini memiliki prasyarat
                if (!empty($requiredIds)) {
                    // Cek apakah setidaknya ADA SATU (OR) dari requiredIds yang ada di dalam $selectedFitur
                    $intersect = array_intersect($requiredIds, $selectedFitur);

                    if (empty($intersect)) {
                        // Ambil Kategori dan Nama Fitur syarat untuk format: Kategori (Nama Fitur)
                        $missingFeatures = $db->table('fitur')
                            ->select('kategori, nama_fitur')
                            ->whereIn('id_fitur', $requiredIds)
                            ->get()
                            ->getResultArray();
                        
                        // Buat format string: Kategori (Nama Fitur)
                        $formattedNames = [];
                        foreach ($missingFeatures as $mf) {
                            $formattedNames[] = "<b>{$mf['kategori']}</b> ({$mf['nama_fitur']})";
                        }
                        
                        $namesStr = implode(' <b>ATAU</b> ', $formattedNames);
                        
                        // Ambil informasi fitur utama yang gagal
                        $mainFeature = $this->fiturModel->find($idFitur);
                        $mainName = $mainFeature ? "<b>{$mainFeature['kategori']}</b> ({$mainFeature['nama_fitur']})" : 'Fitur';

                        return $this->response->setJSON([
                            'status'  => 'error', 
                            'message' => "Gagal! Fitur {$mainName} memerlukan setidaknya salah satu prasyarat berikut:<br><br>{$namesStr}."
                        ])->setStatusCode(400);
                    }
                }
            }
        }

        $db->transStart();

        try {
            // Ambil daftar fitur yang saat ini aktif untuk peran ini
            $currentActive = $this->peranFiturModel->where('id_peran', $idPeran)->findAll();
            $currentIds = array_column($currentActive, 'id_fitur');

            // Tentukan mana yang harus dihapus (uncheck) dan mana yang harus ditambahkan/diaktifkan
            $toDelete = array_diff($currentIds, $selectedFitur);
            $toAdd    = array_diff($selectedFitur, $currentIds);

            // Hapus relasi yang tidak dicentang lagi (soft delete)
            if (!empty($toDelete)) {
                $this->peranFiturModel->where('id_peran', $idPeran)
                    ->whereIn('id_fitur', $toDelete)
                    ->set(['deleted_by' => session()->get('id_akun')])
                    ->delete();
            }

            // Tambahkan atau aktifkan kembali relasi yang dicentang
            foreach ($toAdd as $idFitur) {
                $exists = $this->peranFiturModel->withDeleted()
                    ->where('id_peran', $idPeran)
                    ->where('id_fitur', $idFitur)
                    ->first();

                if ($exists) {
                    $this->peranFiturModel->update($exists['id_peran_fitur'], [
                        'deleted_at' => null,
                        'deleted_by' => null,
                        'updated_by' => session()->get('id_akun')
                    ]);
                } else {
                    $this->peranFiturModel->insert([
                        'id_peran'   => $idPeran,
                        'id_fitur'   => $idFitur,
                        'created_by' => session()->get('id_akun')
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui hak akses fitur.']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Hak akses fitur berhasil diperbarui!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id_peran');
        $role = $this->peranModel->find($id);

        if (!$role) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data peran tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Catat akun yang melakukan penghapusan sebelum soft delete
        $this->peranModel->update($id, [
            'deleted_by' => session()->get('id_akun') 
        ]);

        if ($this->peranModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data peran berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus data peran.'
        ])->setStatusCode(500);
    }
}
