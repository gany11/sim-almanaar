<?php

namespace App\Controllers\Admin;

use App\Models\KeuanganModel;
use App\Models\PublikasiModel;
use App\Models\AgendaModel;
use CodeIgniter\Database\Config;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        // Inisialisasi Model
        $keuanganModel  = new KeuanganModel();
        $publikasiModel = new PublikasiModel();
        $agendaModel    = new AgendaModel();
        $db         = \Config\Database::connect();

        // 1. Ambil Summary & Chart Total
        $summaryKeuangan = $keuanganModel->getSummaryPerKategori();
        $chartDataTotal  = $keuanganModel->getChartTotal();

        // 2. Ambil Chart Detail per Kategori
        $kategoriList = $db->table('kategori_keuangan')->get()->getResultArray();
        $chartDetail  = [];
        foreach ($kategoriList as $kat) {
            $chartDetail[$kat['kategori']] = $keuanganModel->getNetoByKategori($kat['id_kategori_keuangan']);
        }

        // 3. Ambil Publikasi & Agenda dari Model
        $data = [
            'summaryKeuangan' => $summaryKeuangan,
            'chartDataTotal'  => $chartDataTotal,
            'chartDetail'     => $chartDetail,
            'publikasi'       => $publikasiModel->getTerbaru(4),
            'agenda'          => $agendaModel->getAgendaMendatang(5)
        ];

        return view('admin/v_dashboard', $data);
    }
}
