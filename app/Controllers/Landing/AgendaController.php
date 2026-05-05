<?php

namespace App\Controllers\Landing;

use App\Models\AgendaModel;
use App\Models\SdmAgendaModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AgendaController extends BaseController
{
    protected $agendaModel;
    protected $sdmAgendaModel;

    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
        $this->sdmAgendaModel = new SdmAgendaModel();
    }

    /**
     * Menampilkan Halaman Kalender Utama
     */
    public function index()
    {
        return view('landing/agenda/v_agenda', [
            'title' => 'Agenda Kegiatan'
        ]);
    }

    /**
     * Endpoint API untuk FullCalendar (JSON)
     */
    public function getEvents()
    {
        $start = $this->request->getVar('start') ?? date('Y-m-01');
        $end   = $this->request->getVar('end') ?? date('Y-m-t');

        $builder = $this->agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kategori_agenda.class_color, kw_mulai.keterangan as ket_mulai, kw_selesai.keterangan as ket_selesai')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda')
            ->join('keterangan_waktu as kw_mulai', 'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai', 'left')
            ->join('keterangan_waktu as kw_selesai', 'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai', 'left')
            ->where('waktu_mulai >=', $start)
            ->where('waktu_mulai <=', $end)
            ->where('agenda.deleted_at', null);

        $events = $builder->findAll();

        $result = [];
        foreach ($events as $row) {
            $pengisi = $this->sdmAgendaModel->select('sdm.nama, kategori_sdm.kategori as peran')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->join('kategori_sdm', 'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm')
                ->where('id_agenda', $row['id_agenda'])
                ->findAll();

            $result[] = [
                'id'    => $row['id_agenda'],
                'title' => '[' . $row['nama_kategori'] . '] ' . ($row['tema'] ?: $row['judul']),
                'start' => $row['waktu_mulai'],
                'end'   => $row['waktu_selesai'] ?: $row['waktu_mulai'],
                'className' => $row['class_color'], 
                'extendedProps' => [
                    'nama_kategori' => $row['nama_kategori'],
                    'tema'          => $row['tema'],
                    'judul'         => $row['judul'],
                    'deskripsi'     => $row['deskripsi'],
                    'tempat'        => $row['tempat'],
                    'ket_mulai'     => $row['ket_mulai'],
                    'ket_selesai'   => $row['ket_selesai'],
                    'pengisi'       => $pengisi
                ]
            ];
        }

        return $this->response->setJSON($result);
    }
}
