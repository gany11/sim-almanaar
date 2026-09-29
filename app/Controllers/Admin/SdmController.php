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

    public function detail($id)
    {
        $sdm = $this->sdmModel->find($id);

        if (!$sdm) {
            return redirect()
                ->to('admin/sdm')
                ->with('error', 'Data SDM tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $agendas = $db->table('sdm_agenda sa')
            ->select('
                sa.id_pengisi_agenda,
                sa.id_agenda,
                sa.id_sdm,
                sa.id_kategori_sdm,

                a.id_kategori_agenda,
                a.tema,
                a.judul,
                a.deskripsi,
                a.tempat,
                a.waktu_mulai,
                a.waktu_selesai,
                a.method,

                ka.nama_kategori,
                ka.class_color AS kategori_class_color,

                ks.kategori AS kategori_sdm,
                ks.class_color AS kategori_sdm_class_color,

                kw_mulai.keterangan AS keterangan_waktu_mulai,
                kw_mulai.class_color AS waktu_mulai_class_color,

                kw_selesai.keterangan AS keterangan_waktu_selesai,
                kw_selesai.class_color AS waktu_selesai_class_color
            ')
            ->join(
                'agenda a',
                'a.id_agenda = sa.id_agenda',
                'inner'
            )
            ->join(
                'kategori_agenda ka',
                'ka.id_kategori_agenda = a.id_kategori_agenda',
                'left'
            )
            ->join(
                'kategori_sdm ks',
                'ks.id_kategori_sdm = sa.id_kategori_sdm',
                'left'
            )
            ->join(
                'keterangan_waktu kw_mulai',
                'kw_mulai.id_keterangan_waktu = a.id_keterangan_waktu_mulai',
                'left'
            )
            ->join(
                'keterangan_waktu kw_selesai',
                'kw_selesai.id_keterangan_waktu = a.id_keterangan_waktu_selesai',
                'left'
            )
            ->where('sa.id_sdm', $id)
            ->where('a.deleted_at IS NULL', null, false)
            ->orderBy('a.waktu_mulai', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/sdm/v_detail', [
            'title'   => 'Detail SDM',
            'sdm'     => $sdm,
            'agendas' => $agendas
        ]);
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

        // Cek agenda biasa yang masih aktif
        $cekAgenda = $db->table('sdm_agenda sa')
            ->join(
                'agenda a',
                'a.id_agenda = sa.id_agenda',
                'inner'
            )
            ->where('sa.id_sdm', $id)
            ->where('a.deleted_at IS NULL', null, false)
            ->countAllResults();

        // Cek agenda rutin yang masih aktif
        $cekAgendaRutin = $db->table('sdm_agenda sa')
            ->join(
                'agenda_rutin ar',
                'ar.id_agenda_rutin = sa.id_agenda_rutin',
                'inner'
            )
            ->where('sa.id_sdm', $id)
            ->where('ar.deleted_at IS NULL', null, false)
            ->countAllResults();

        $cekRelasi = $cekAgenda + $cekAgendaRutin;

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

    public function getAlternativeSdm()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $idSdmDihapus = $this->request->getPost('id_sdm');
        
        // Ambil data SDM lain selain yang akan dihapus
        $altSdm = $this->sdmModel->where('id_sdm !=', $idSdmDihapus)->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $altSdm
        ]);
    }

    public function replaceAndDelete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $idLama = $this->request->getPost('id_sdm_lama');
        $idBaru = $this->request->getPost('id_sdm_baru');

        if (empty($idLama) || empty($idBaru)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data SDM tidak lengkap.']);
        }

        $sdmLama = $this->sdmModel->find($idLama);
        if (!$sdmLama) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data SDM tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $sdmBaru = $this->sdmModel->find($idBaru);
        if (!$sdmBaru) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data SDM pengganti tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $namaLama = $sdmLama['nama'];
        $namaBaru = $sdmBaru['nama'];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Pindahkan relasi agenda biasa yang masih aktif
            $db->table('sdm_agenda sa')
                ->join('agenda a', 'a.id_agenda = sa.id_agenda', 'inner')
                ->where('sa.id_sdm', $idLama)
                ->where('a.deleted_at IS NULL', null, false)
                ->set('sa.id_sdm', $idBaru)
                ->update();

            // Pindahkan relasi agenda rutin yang masih aktif
            $db->table('sdm_agenda sa')
                ->join('agenda_rutin ar', 'ar.id_agenda_rutin = sa.id_agenda_rutin', 'inner')
                ->where('sa.id_sdm', $idLama)
                ->where('ar.deleted_at IS NULL', null, false)
                ->set('sa.id_sdm', $idBaru)
                ->update();

            // 2. Catat user yang menghapus
            $this->sdmModel->update($idLama, [
                'deleted_by' => session()->get('id_akun')
            ]);

            // 3. Lakukan soft delete pada SDM lama
            $this->sdmModel->delete($idLama);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses pemindahan tugas dan penghapusan.']);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Seluruh penugasan dari <b>{$namaLama}</b> telah dialihkan ke <b>{$namaBaru}</b> dan data SDM lama berhasil dihapus."
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}