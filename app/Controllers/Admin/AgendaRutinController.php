<?php

namespace App\Controllers\Admin;

use App\Models\AgendaRutinModel;
use App\Models\SdmModel;
use App\Models\KategoriAgendaModel;
use App\Models\KategoriSdmModel;
use App\Models\KeteranganWaktuModel;
use App\Models\SdmAgendaModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AgendaRutinController extends BaseController
{
    protected $agendaRutinModel;
    protected $sdmModel;
    protected $katAgendaModel;
    protected $katSdmModel;
    protected $ketWaktuModel;
    protected $sdmAgendaModel;

    public function __construct()
    {
        $this->agendaRutinModel = new AgendaRutinModel();
        $this->sdmModel       = new SdmModel();
        $this->katAgendaModel = new KategoriAgendaModel();
        $this->katSdmModel    = new KategoriSdmModel();
        $this->ketWaktuModel  = new KeteranganWaktuModel();
        $this->sdmAgendaModel = new SdmAgendaModel();
    }

    public function index()
    {
        return view('admin/agenda-routine/v_index', [
            'title'      => 'Manajemen Agenda Rutin',
            'categories' => $this->katAgendaModel->findAll()
        ]);
    }

    public function list()
    {
        $id_kategori = $this->request->getPost('id_kategori_agenda');
        $status      = $this->request->getPost('status');
        
        $builder = $this->agendaRutinModel->select('agenda_rutin.*, kategori_agenda.nama_kategori, kategori_agenda.class_color')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda_rutin.id_kategori_agenda');

        if (!empty($id_kategori)) {
            $builder->where('agenda_rutin.id_kategori_agenda', $id_kategori);
        }

        if (!empty($status)) {
            $builder->where('agenda_rutin.status', $status);
        }

        $agendaRaw = $builder->orderBy('agenda_rutin.waktu_mulai', 'ASC')->findAll();

        $agendaFinal = [];
        foreach ($agendaRaw as $row) {
            // Ambil SDM berdasarkan id_agenda_rutin
            $pengisi = $this->sdmAgendaModel->select('sdm.nama')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->where('id_agenda_rutin', $row['id_agenda_rutin'])
                ->findAll();
            
            $row['nama_pengisi'] = !empty($pengisi) ? array_column($pengisi, 'nama') : [];
            $agendaFinal[] = $row;
        }

        $data['agenda_rutin'] = $agendaFinal;
        return view('admin/agenda-routine/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/agenda-routine/v_form', [
            'title'      => 'Tambah Agenda Rutin Baru',
            'categories' => $this->katAgendaModel->findAll(),
            'sdm_roles'  => $this->katSdmModel->findAll(),
            'times'      => $this->ketWaktuModel->findAll(),
            'all_sdm'    => $this->sdmModel->findAll(),
        ]);
    }

    public function edit($id)
    {
        $agenda_rutin = $this->agendaRutinModel->find($id);
        if (!$agenda_rutin) return redirect()->to('admin/agenda-rutin')->with('error', 'Agenda rutin tidak ditemukan.');

        // Ambil relasi SDM yang sudah ada khusus untuk agenda rutin ini
        $currentSdm = $this->sdmAgendaModel->where('id_agenda_rutin', $id)->findAll();

        return view('admin/agenda-routine/v_form', [
            'title'        => 'Edit Agenda Rutin',
            'agenda_rutin' => $agenda_rutin,
            'currentSdm'   => $currentSdm,
            'categories'   => $this->katAgendaModel->findAll(),
            'sdm_roles'    => $this->katSdmModel->findAll(),
            'times'        => $this->ketWaktuModel->findAll(),
            'all_sdm'      => $this->sdmModel->findAll(),
        ]);
    }

    public function save() { return $this->_store(); }
    public function update($id) { return $this->_store($id); }

    private function _store($id = null)
    {
        // 1. Ambil aturan validasi dari model
        $rules = $this->agendaRutinModel->validationRules;
        $errors = [];

        // Validasi Model (Kategori, Tema, Waktu Mulai, dll)
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
        }

        // 2. Validasi Custom (Waktu Selesai)
        $waktu_mulai = $this->request->getPost('waktu_mulai');
        $waktu_selesai = $this->request->getPost('waktu_selesai');

        if (!empty($waktu_mulai)) {
            if (empty($waktu_selesai)) {
                // Jika waktu selesai kosong, otomatis set +1 Jam dari waktu mulai
                $waktu_selesai = date('H:i', strtotime($waktu_mulai . ' +1 hour'));
            } else {
                // Pastikan waktu selesai lebih dari (setelah) waktu mulai
                if (strtotime($waktu_selesai) <= strtotime($waktu_mulai)) {
                    $errors['waktu_selesai'] = 'Waktu selesai tidak boleh kurang dari atau sama dengan waktu mulai.';
                }
            }
        }

        // 3. Validasi Custom (SDM)
        $sdmIds   = $this->request->getPost('sdm_id') ?? [];
        $sdmRoles = $this->request->getPost('sdm_role') ?? [];

        // Cek jika array benar-benar kosong ATAU hanya ada 1 baris tapi KEDUANYA (nama & peran) kosong
        if (empty($sdmIds) || (count($sdmIds) === 1 && empty($sdmIds[0]) && empty($sdmRoles[0]))) {
            $errors['sdm'] = 'Minimal harus ada satu pengisi.';
        } else {
            foreach ($sdmIds as $i => $sdmId) {
                // Jika salah satu (nama atau peran) kosong pada baris manapun
                if (empty($sdmId) || empty($sdmRoles[$i])) {
                    $errors['sdm'] = 'Semua pengisi beserta perannya wajib dilengkapi.';
                    break; 
                }
            }
        }

        // dd([$sdmIds, $sdmRoles]);

        // 4. Cek Jika Ada Error
        // Jika ada error (baik dari Model maupun Custom), kembalikan ke form
        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 5. Siapkan Data Agenda Rutin
            // Looping dikonversi dari array ke comma-separated string (misal: '1,2,3')
            $loopingHari   = $this->request->getPost('looping_hari') ? implode(',', $this->request->getPost('looping_hari')) : null;
            $loopingMinggu = $this->request->getPost('looping_minggu') ? implode(',', $this->request->getPost('looping_minggu')) : null;

            $dataAgendaRutin = [
                'id_kategori_agenda'          => $this->request->getPost('id_kategori_agenda'),
                'tema'                        => $this->request->getPost('tema'), // Wajib
                'judul'                       => $this->request->getPost('judul') ?: null, // Opsional
                'deskripsi'                   => $this->request->getPost('deskripsi') ?: null,
                'tempat'                      => $this->request->getPost('tempat') ?: null,
                'waktu_mulai'                 => $waktu_mulai,
                'waktu_selesai'               => $waktu_selesai, // Sudah divalidasi & diisi otomatis jika kosong
                'id_keterangan_waktu_mulai'   => $this->request->getPost('id_keterangan_waktu_mulai') ?: null,
                'id_keterangan_waktu_selesai' => $this->request->getPost('id_keterangan_waktu_selesai') ?: null,
                'status'                      => $this->request->getPost('status') ? 'aktif' : 'pasif',
                'looping_hari'                => $loopingHari,
                'looping_minggu'              => $loopingMinggu,
            ];

            if ($id) {
                $dataAgendaRutin['updated_by'] = session()->get('id_akun');
                $this->agendaRutinModel->update($id, $dataAgendaRutin);
                $agendaRutinId = $id;

                // Hapus data pengisi/SDM lama terkait agenda rutin ini
                $this->sdmAgendaModel->where('id_agenda_rutin', $id)->delete();
            } else {
                $dataAgendaRutin['created_by'] = session()->get('id_akun');
                $this->agendaRutinModel->insert($dataAgendaRutin);
                $agendaRutinId = $this->agendaRutinModel->getInsertID();
            }

            // 6. Proses Penyimpanan SDM / Pengisi
            foreach ($sdmIds as $index => $sdmIdInput) {
                if (empty($sdmIdInput)) continue;

                $idSdm = null;
                $idKategoriSdm = !empty($sdmRoles[$index]) ? $sdmRoles[$index] : null;

                // Jika input bukan angka (berarti user mengetik nama baru via Select2 Tags)
                if (!is_numeric($sdmIdInput)) {
                    $newSdmData = [
                        'nama'       => $sdmIdInput,
                        'created_by' => session()->get('id_akun')
                    ];
                    $this->sdmModel->insert($newSdmData);
                    $idSdm = $this->sdmModel->getInsertID();
                } else {
                    $idSdm = $sdmIdInput;
                }

                // Masukkan relasi SDM dengan Agenda Rutin
                if ($idSdm && $idKategoriSdm) {
                    $this->sdmAgendaModel->insert([
                        'id_agenda'       => null, 
                        'id_agenda_rutin' => $agendaRutinId,
                        'id_sdm'          => $idSdm,
                        'id_kategori_sdm' => $idKategoriSdm
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data agenda rutin.');
            }

            return redirect()->to('admin/agenda-rutin')->with('success', 'Agenda rutin berhasil disimpan!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id_agenda_rutin');
        $agenda_rutin = $this->agendaRutinModel->find($id);

        if (!$agenda_rutin) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data agenda rutin tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Catat user yang menghapus data
        $this->agendaRutinModel->update($id, [
            'deleted_by' => session()->get('id_akun') 
        ]);

        // Proses soft delete
        if ($this->agendaRutinModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Agenda rutin berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus agenda rutin.'
        ])->setStatusCode(500);
    }
}
