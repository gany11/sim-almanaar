<?php

namespace App\Controllers\Admin;

use App\Models\CatatanKeuanganModel;
use App\Controllers\BaseController;

class CashNoteController extends BaseController
{
    protected CatatanKeuanganModel $catatanKeuanganModel;

    public function __construct()
    {
        $this->catatanKeuanganModel = new CatatanKeuanganModel();
    }

    public function index()
    {
        return view('admin/cash_notes/v_index', [
            'title' => 'Catatan Keuangan (Keep Cash)'
        ]);
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('treasurer/cash-notes');
        }

        $status = $this->request->getPost('status');
        
        $builder = $this->catatanKeuanganModel
            ->select('catatan_keuangan.*, creator.nama as creator_name, editor.nama as editor_name')
            ->join('akun as creator', 'creator.id_akun = catatan_keuangan.created_by', 'left')
            ->join('akun as editor', 'editor.id_akun = catatan_keuangan.updated_by', 'left');

        if (!empty($status)) {
            $builder->where('catatan_keuangan.status', $status);
        }

        $data['cashNotes'] = $builder->orderBy('catatan_keuangan.tanggal', 'DESC')->findAll();

        return view('admin/cash_notes/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/cash_notes/v_form', [
            'title' => 'Tambah Catatan Keuangan Baru'
        ]);
    }

    public function edit($id)
    {
        $cashNote = $this->catatanKeuanganModel->find($id);
        if (!$cashNote) {
            return redirect()->to('treasurer/cash-notes')->with('error', 'Data catatan tidak ditemukan.');
        }

        if ($cashNote['status'] !== 'tersedia') {
            return redirect()->to('treasurer/cash-notes')->with('error', 'Catatan yang sudah digunakan atau dibatalkan tidak dapat diubah.');
        }

        return view('admin/cash_notes/v_form', [
            'title'    => 'Edit Catatan Keuangan',
            'cashNote' => $cashNote
        ]);
    }

    public function save() 
    { 
        return $this->_store(); 
    }

    public function update($id) 
    { 
        $cashNote = $this->catatanKeuanganModel->find($id);
        if (!$cashNote) {
            return redirect()->to('treasurer/cash-notes')->with('error', 'Data catatan tidak ditemukan.');
        }

        if ($cashNote['status'] !== 'tersedia') {
            return redirect()->to('treasurer/cash-notes')->with('error', 'Catatan yang sudah digunakan atau dibatalkan tidak dapat diubah.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null)
    {
        // 1. Tentukan status awal berdasarkan kondisi (Create / Edit)
        $status = 'tersedia';
        if ($id) {
            $cashNote = $this->catatanKeuanganModel->find($id);
            if ($cashNote) {
                $status = $cashNote['status'] ?? 'tersedia';
            }
        }

        // 2. Gabungkan seluruh field (termasuk status) ke dalam satu array $dataInput yang utuh
        $dataInput = [
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'nominal'    => $this->request->getPost('nominal'),
            'status'     => $status,
        ];

        // 3. Jalankan validasi menggunakan model
        if (!$this->catatanKeuanganModel->validate($dataInput)) {
            return redirect()->back()->withInput()->with('errors', $this->catatanKeuanganModel->errors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($id) {
                $dataInput['updated_by'] = session()->get('id_akun');
                $this->catatanKeuanganModel->update($id, $dataInput);
            } else {
                $dataInput['created_by'] = session()->get('id_akun');
                $this->catatanKeuanganModel->insert($dataInput);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan catatan keuangan.');
            }

            return redirect()->to('treasurer/cash-notes')->with('success', 'Catatan keuangan berhasil disimpan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $id          = $this->request->getPost('id_catatan');
        $statusBaru  = $this->request->getPost('status');
        $alasanBaru  = trim($this->request->getPost('alasan'));
        
        $cashNote = $this->catatanKeuanganModel->find($id);

        if (!$cashNote) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.'])->setStatusCode(404);
        }

        if ($cashNote['status'] !== 'tersedia') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Catatan yang sudah digunakan atau dibatalkan tidak dapat diubah.'])->setStatusCode(400);
        }

        // Tentukan aturan validasi untuk status dan alasan
        $validationRules = [
            'status' => [
                'rules'  => 'required|in_list[sudah_digunakan,dibatalkan]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Pilihan status tidak valid.'
                ]
            ],
            'alasan' => [
                'rules'  => 'required|string',
                'errors' => [
                    'required' => 'Alasan atau peruntukan wajib diisi.',
                    'string'   => 'Format alasan tidak valid.'
                ]
            ]
        ];

        $this->validator = \Config\Services::validation();
        $this->validator->setRules($validationRules);

        $dataToValidate = [
            'status' => $statusBaru,
            'alasan' => $alasanBaru
        ];

        if (!$this->validator->run($dataToValidate)) {
            // Ambil pesan error pertama yang ditemukan
            $errors = $this->validator->getErrors();
            $firstError = reset($errors) ?: 'Data tidak valid.';

            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => $firstError
            ]);
        }

        // Proses update ke database
        $updated = $this->catatanKeuanganModel->update($id, [
            'status'     => $statusBaru,
            'alasan'     => $alasanBaru,
            'updated_by' => session()->get('id_akun')
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'status'  => 'success', 
                'message' => 'Status catatan berhasil diperbarui.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error', 
            'message' => 'Gagal memperbarui status.'
        ])->setStatusCode(500);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $id = $this->request->getPost('id_catatan');
        $cashNote = $this->catatanKeuanganModel->find($id);

        if (!$cashNote) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.'])->setStatusCode(404);
        }

        if ($cashNote['status'] !== 'tersedia') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Catatan yang sudah digunakan atau dibatalkan tidak dapat dihapus.'])->setStatusCode(400);
        }

        $this->catatanKeuanganModel->update($id, ['deleted_by' => session()->get('id_akun')]);

        if ($this->catatanKeuanganModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Catatan berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus catatan.'])->setStatusCode(500);
    }
}   