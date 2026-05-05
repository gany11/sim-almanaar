<?php

namespace App\Controllers\Landing;

use App\Models\KeuanganModel;
use App\Models\LaporanMingguanModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FinanceController extends BaseController
{
    protected $keuanganModel;
    protected $laporanModel;

    public function __construct()
    {
        $this->keuanganModel = new KeuanganModel();
        $this->laporanModel = new LaporanMingguanModel();
        
    }
    
    public function index()
    {
        $db     = \Config\Database::connect();
        $now    = date('Y-m-d H:i:s');

        // 1. Ringkasan Kategori (Card Saldo)
        $data['summary_categories'] = $this->keuanganModel->getSummaryPerKategori();

        // 2. Data Chart Total (Pemasukan vs Pengeluaran)
        $data['chartDataTotal']  = $this->keuanganModel->getChartTotal();

        // 2. Ambil Chart Detail per Kategori
        $kategoriList = $db->table('kategori_keuangan')->get()->getResultArray();
        $chartDetail  = [];
        foreach ($kategoriList as $kat) {
            $chartDetail[$kat['kategori']] = $this->keuanganModel->getNetoByKategori($kat['id_kategori_keuangan']);
        }
        $data['chartDetail'] = $chartDetail;

        // 3. Laporan Saat Ini & Transaksi Terkini
        $currentReport = $this->laporanModel
            ->where('started_at <=', $now)
            ->where('ended_at >=', $now)
            ->first();

        $builder = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, kategori_keuangan.class_color')
                    ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan');

        if ($currentReport) {
            $builder->where('keuangan.created_at >=', $currentReport['started_at'])
                    ->where('keuangan.created_at <=', $currentReport['ended_at']);
        } else {
            $builder->where('keuangan.id_keuangan', 0); 
        }

        $data['current_report'] = $currentReport;
        $data['transactions'] = $builder->orderBy('keuangan.created_at', 'ASC')->findAll();

        // 4. Riwayat Laporan Keuangan Rutin (Tabel Bawah)
        $threeMonthsAgo = date('Y-m-d H:i:s', strtotime('-3 months'));

        $data['history_reports'] = $this->laporanModel
            ->where('ended_at <', $now)
            ->where('ended_at >=', $threeMonthsAgo)
            ->orderBy('ended_at', 'DESC')
            ->findAll();
            
        // dd($data);
        $data['title'] = "Laporan Keuangan";
        return view('landing/finance/v_finance', $data);
    }

    public function detail($tglAwal, $tglAkhir, $id)
    {
        $report = $this->laporanModel
            ->where('id_laporan_mingguan', $id)
            ->where("DATE_FORMAT(started_at, '%Y%m%d')", $tglAwal)
            ->where("DATE_FORMAT(ended_at, '%Y%m%d')", $tglAkhir)
            ->first();
        
        if (!$report || $report['ended_at'] > date('Y-m-d H:i:s')) {
            return redirect()->to('keuangan')->with('error', 'Laporan tidak tersedia.');
        }

        $transactions = $this->keuanganModel
            ->where('created_at >=', $report['started_at'])
            ->where('created_at <=', $report['ended_at'])
            ->where('deleted_at', null)
            ->findAll();

        $mapping = [
            'A' => ['name' => 'Kas Yatim', 'ids' => [3]],
            'B' => ['name' => 'Kas PKU', 'ids' => [4]],
            'C' => ['name' => 'Kas Masjid', 'ids' => [1]],
            'D' => ['name' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
        ];

        $groupedData = [];
        foreach ($mapping as $key => $map) {
            $items = array_filter($transactions, function($t) use ($map) {
                return in_array($t['id_kategori_keuangan'], $map['ids']);
            });

            $saldoAwalMasuk = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pemasukan')
                ->where('created_at <', $report['started_at'])
                ->where('deleted_at', null)
                ->get()->getRow()->jumlah ?? 0;

            $saldoAwalKeluar = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pengeluaran')
                ->where('created_at <', $report['started_at'])
                ->where('deleted_at', null)
                ->get()->getRow()->jumlah ?? 0;

            $realSaldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

            $totalMasuk = 0;
            $totalKeluar = 0;
            foreach ($items as $item) {
                if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
                else $totalKeluar += $item['jumlah'];
            }

            $groupedData[$key] = [
                'judul'       => $map['name'],
                'saldo_awal'  => $realSaldoAwal,
                'items'       => $items,
                'total_masuk' => $totalMasuk,
                'total_keluar'=> $totalKeluar,
                'saldo_akhir' => ($realSaldoAwal + $totalMasuk) - $totalKeluar
            ];
        }

        return view('landing/finance/v_finance_detail', [
            'title'   => 'Detail Laporan: ' . $report['judul'],
            'report'  => $report,
            'grouped' => $groupedData
        ]);
    }
}
