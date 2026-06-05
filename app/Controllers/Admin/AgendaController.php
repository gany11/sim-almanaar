<?php

namespace App\Controllers\Admin;

use App\Models\AgendaModel;
use App\Models\SdmModel;
use App\Models\KategoriAgendaModel;
use App\Models\KategoriSdmModel;
use App\Models\KeteranganWaktuModel;
use App\Models\SdmAgendaModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AgendaController extends BaseController
{
    protected $agendaModel;
    protected $sdmModel;
    protected $katAgendaModel;
    protected $katSdmModel;
    protected $ketWaktuModel;
    protected $sdmAgendaModel;

    public function __construct()
    {
        $this->agendaModel    = new AgendaModel();
        $this->sdmModel       = new SdmModel();
        $this->katAgendaModel = new KategoriAgendaModel();
        $this->katSdmModel    = new KategoriSdmModel();
        $this->ketWaktuModel  = new KeteranganWaktuModel();
        $this->sdmAgendaModel = new SdmAgendaModel();
    }

    public function index()
    {
        return view('admin/agenda/v_index', [
            'title'      => 'Manajemen Agenda',
            'categories' => $this->katAgendaModel->findAll()
        ]);
    }

    public function list()
    {
        $id_kategori = $this->request->getPost('id_kategori_agenda');
        
        $builder = $this->agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kategori_agenda.class_color')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda');

        if (!empty($id_kategori)) {
            $builder->where('agenda.id_kategori_agenda', $id_kategori);
        }

        $agendaRaw = $builder->orderBy('agenda.waktu_mulai', 'DESC')->findAll();

        $agendaFinal = [];
        foreach ($agendaRaw as $row) {
            $pengisi = $this->sdmAgendaModel->select('sdm.nama')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->where('id_agenda', $row['id_agenda'])
                ->findAll();
            
            $row['nama_pengisi'] = !empty($pengisi) ? array_column($pengisi, 'nama') : [];
            $agendaFinal[] = $row;
        }

        $data['agenda'] = $agendaFinal;
        return view('admin/agenda/v_list_partial', $data);
    }

    public function create()
    {
        return view('admin/agenda/v_form', [
            'title'      => 'Tambah Agenda Baru',
            'categories' => $this->katAgendaModel->findAll(),
            'sdm_roles'  => $this->katSdmModel->findAll(),
            'times'      => $this->ketWaktuModel->findAll(),
            'all_sdm'    => $this->sdmModel->findAll(),
        ]);
    }

    public function edit($id)
    {
        $agenda = $this->agendaModel->find($id);
        if (!$agenda) return redirect()->to('admin/agenda')->with('error', 'Agenda tidak ditemukan.');

        // Ambil relasi SDM yang sudah ada
        $currentSdm = $this->sdmAgendaModel->where('id_agenda', $id)->findAll();

        return view('admin/agenda/v_form', [
            'title'      => 'Edit Agenda',
            'agenda'     => $agenda,
            'currentSdm' => $currentSdm,
            'categories' => $this->katAgendaModel->findAll(),
            'sdm_roles'  => $this->katSdmModel->findAll(),
            'times'      => $this->ketWaktuModel->findAll(),
            'all_sdm'    => $this->sdmModel->findAll(),
        ]);
    }

    public function save() { return $this->_store(); }
    public function update($id) { return $this->_store($id); }

    protected function _store($id = null)
    {
        $rules = $this->agendaModel->validationRules;
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentUserId = session()->get('id_akun'); // Ambil ID akun dari session
        $startStr = $this->request->getPost('waktu_mulai');
        $endStr   = $this->request->getPost('waktu_selesai');

        if (strtotime($endStr) <= strtotime($startStr)) {
            return redirect()->back()->withInput()->with('errors', ['waktu_selesai' => 'Waktu selesai harus lebih besar dari waktu mulai.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $dataAgenda = [
            'id_kategori_agenda'         => $this->request->getPost('id_kategori_agenda'),
            'tema'                       => $this->request->getPost('tema'),
            'judul'                      => $this->request->getPost('judul'),
            'deskripsi'                  => $this->request->getPost('deskripsi'),
            'tempat'                     => $this->request->getPost('tempat'),
            'waktu_mulai'                => str_replace('T', ' ', $startStr),
            'waktu_selesai'              => str_replace('T', ' ', $endStr),
            'id_keterangan_waktu_mulai'   => $this->request->getPost('id_keterangan_waktu_mulai') ?: null,
            'id_keterangan_waktu_selesai' => $this->request->getPost('id_keterangan_waktu_selesai') ?: null,
        ];

        if ($id) {
            // Logika Update
            $dataAgenda['updated_by'] = $currentUserId;
            $this->agendaModel->update($id, $dataAgenda);
            $agendaID = $id;
        } else {
            // Logika Create
            $dataAgenda['created_by'] = $currentUserId;
            $agendaID = $this->agendaModel->insert($dataAgenda);
        }

        $sdmIds   = $this->request->getPost('sdm_id');
        $sdmRoles = $this->request->getPost('sdm_role');

        if ($agendaID) {
            $this->sdmAgendaModel->where('id_agenda', $agendaID)->delete();
            if ($sdmIds) {
                foreach ($sdmIds as $key => $val) {
                    if (empty($val)) continue;
                    $finalSdmId = $val;

                    if (!is_numeric($val)) {
                        $newSdm = $this->sdmModel->where('nama', $val)->first();
                        if ($newSdm) {
                            $finalSdmId = $newSdm['id_sdm'];
                        } else {
                            $finalSdmId = $this->sdmModel->insert([
                                'nama'       => $val,
                                'created_by' => $currentUserId
                            ]);
                        }
                    }

                    $this->sdmAgendaModel->insert([
                        'id_agenda'       => $agendaID,
                        'id_sdm'          => $finalSdmId,
                        'id_kategori_sdm' => $sdmRoles[$key]
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }

        return redirect()->to('admin/agenda')->with('success', 'Agenda berhasil disimpan!');
    }

    public function delete()
    {
        $id = $this->request->getPost('id_agenda');
        $agenda = $this->agendaModel->find($id);

        if (!$agenda) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data agenda tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $this->agendaModel->update($id, [
            'deleted_by' => session()->get('id_akun')
        ]);

        if ($this->agendaModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Agenda berhasil dihapus.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus agenda.'
        ])->setStatusCode(500);
    }
}
