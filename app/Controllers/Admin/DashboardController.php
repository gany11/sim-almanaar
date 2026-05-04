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

    // public function index()
    // {
    //     $db = \Config\Database::connect();

    //     // 1. Query Keuangan (Saldo)
    //     $summaryKeuangan = $db->table('kategori_keuangan k')
    //         ->select('k.kategori, k.class_color, 
    //             COALESCE(SUM(CASE WHEN keu.jenis = "pemasukan" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) - 
    //             COALESCE(SUM(CASE WHEN keu.jenis = "pengeluaran" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) as saldo')
    //         ->join('keuangan keu', 'keu.id_kategori_keuangan = k.id_kategori_keuangan', 'left')
    //         ->groupBy('k.id_kategori_keuangan')
    //         ->get()->getResultArray();

    //     // 2. Query Chart Keseluruhan
    //     $chartData = $db->query("
    //         SELECT 
    //             DATE_FORMAT(created_at, '%M') as bulan,
    //             SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) as masuk,
    //             SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as keluar
    //         FROM keuangan
    //         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    //         AND deleted_at IS NULL
    //         GROUP BY MONTH(created_at), bulan
    //         ORDER BY created_at ASC
    //     ")->getResultArray();

    //     $kategoriKeuangan = $db->table('kategori_keuangan')->get()->getResultArray();

    //     $detailChart = [];
    //     foreach ($kategoriKeuangan as $kat) {
    //         $monthly = $db->query("
    //             SELECT 
    //                 DATE_FORMAT(created_at, '%M') as bulan,
    //                 SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) - 
    //                 SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as neto
    //             FROM keuangan
    //             WHERE id_kategori_keuangan = {$kat['id_kategori_keuangan']}
    //             AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    //             AND deleted_at IS NULL
    //             GROUP BY MONTH(created_at), bulan
    //             ORDER BY created_at ASC
    //         ")->getResultArray();
            
    //         $detailChart[$kat['kategori']] = $monthly;
    //     }

    //     // 3. Query Publikasi
    //     $publikasi = $db->table('publikasi p')
    //         ->select('p.*, j.jenis_publikasi, j.class_color as kategori_color')
    //         ->join('jenis_publikasi j', 'j.id_jenis_publikasi = p.id_jenis_publikasi', 'left')
    //         ->where('p.deleted_at', null)
    //         ->where('p.status', 'aktf')
    //         ->orderBy('p.created_at', 'DESC')
    //         ->limit(4)
    //         ->get()->getResultArray();

    //     // 4. Query Agenda
    //     $agenda = $db->table('agenda a')
    //         ->select('a.*, k.nama_kategori, k.class_color as kategori_color')
    //         ->join('kategori_agenda k', 'k.id_kategori_agenda = a.id_kategori_agenda', 'left')
    //         ->where('a.deleted_at', null)
    //         ->where('a.waktu_mulai >=', date('Y-m-d H:i:s'))
    //         ->orderBy('a.waktu_mulai', 'ASC')
    //         ->limit(5)
    //         ->get()->getResultArray();
        

    //     $data = [
    //         'summaryKeuangan' => $summaryKeuangan,
    //         'chartDataTotal'  => $chartData,
    //         'chartDetail'     => $detailChart,
    //         'publikasi'       => $publikasi,
    //         'agenda'          => $agenda
    //     ];

    //     return view('admin/v_dashboard', $data);
    // }
}
