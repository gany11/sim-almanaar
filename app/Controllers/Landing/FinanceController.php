<?php

namespace App\Controllers\Landing;

use App\Models\KeuanganModel;
use App\Models\LaporanMingguanModel;
// Iterasi - 2
use App\Models\KategoriKeuanganModel;
use App\Models\AlokasiModel;
use App\Models\DetailAlokasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FinanceController extends BaseController
{
    protected $keuanganModel;
    protected $laporanModel;
    // Iterasi - 2
    protected $kategoriModel;
    protected $alokasiModel;
    protected $detailAlokasiModel;

    public function __construct()
    {
        $this->keuanganModel = new KeuanganModel();
        $this->laporanModel = new LaporanMingguanModel();
        // Iterasi - 2
        $this->kategoriModel = new KategoriKeuanganModel();
        $this->alokasiModel  = new AlokasiModel();
        $this->detailAlokasiModel  = new DetailAlokasiModel();
    }
    
    public function index()
    {
        // $db     = \Config\Database::connect();
        $now    = date('Y-m-d H:i:s');

        // 1. Ringkasan Kategori (Card Saldo)
        // $data['summary_categories'] = $this->keuanganModel->getSummaryPerKategori();
        // Iterasi - 2
        $data['summary_categories'] = $this->keuanganModel->getSummaryPerKategori(false);

        // 2. Data Chart Total (Pemasukan vs Pengeluaran)
        // $data['chartDataTotal']  = $this->keuanganModel->getChartTotal();
        // Iterasi - 2
        $data['chartDataTotal']  = $this->keuanganModel->getChartTotal(6, false);

        // 2. Ambil Chart Detail per Kategori
        // $kategoriList = $db->table('kategori_keuangan')->get()->getResultArray();
        // Iterasi - 2
        $kategoriList = $this->kategoriModel->get()->getResultArray();
        $chartDetail  = [];
        foreach ($kategoriList as $kat) {
            // $chartDetail[$kat['kategori']] = $this->keuanganModel->getNetoByKategori($kat['id_kategori_keuangan']);
            // Iterasi - 2
            $chartDetail[$kat['kategori']] = $this->keuanganModel->getNetoByKategori($kat['id_kategori_keuangan'], 6, false);

        }
        $data['chartDetail'] = $chartDetail;

        // // 3. Laporan Saat Ini & Transaksi Terkini
        // $currentReport = $this->laporanModel
        //     ->where('started_at <=', $now)
        //     ->where('ended_at >=', $now)
        //     ->first();

        // $builder = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, kategori_keuangan.class_color')
        //             ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan');

        // if ($currentReport) {
        //     $builder->where('keuangan.created_at >=', $currentReport['started_at'])
        //             ->where('keuangan.created_at <=', $currentReport['ended_at']);
        // } else {
        //     $builder->where('keuangan.id_keuangan', 0); 
        // }

        // $data['current_report'] = $currentReport;
        // $data['transactions'] = $builder->orderBy('keuangan.created_at', 'ASC')->findAll();

        // // 4. Riwayat Laporan Keuangan Rutin (Tabel Bawah)
        // $threeMonthsAgo = date('Y-m-d H:i:s', strtotime('-3 months'));

        // $data['history_reports'] = $this->laporanModel
        //     ->where('ended_at <', $now)
        //     ->where('ended_at >=', $threeMonthsAgo)
        //     ->orderBy('ended_at', 'DESC')
        //     ->findAll();
            
        // Iterasi 2
        $endDate = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
        $data['years'] = $this->keuanganModel->select("YEAR(created_at) as year")
                         ->where("created_at <=", $endDate)
                         ->where("deleted_at", null)
                         ->groupBy("year")
                         ->orderBy("year", "DESC")
                         ->findAll();

        $data['title'] = "Laporan Keuangan";
        return view('landing/finance/v_finance', $data);
    }

    // public function detail($tglAwal, $tglAkhir, $id)
    // {
    //     $report = $this->laporanModel
    //         ->where('id_laporan_mingguan', $id)
    //         ->where("DATE_FORMAT(started_at, '%Y%m%d')", $tglAwal)
    //         ->where("DATE_FORMAT(ended_at, '%Y%m%d')", $tglAkhir)
    //         ->first();
        
    //     if (!$report || $report['ended_at'] > date('Y-m-d H:i:s')) {
    //         return redirect()->to('keuangan')->with('error', 'Laporan tidak tersedia.');
    //     }

    //     $transactions = $this->keuanganModel
    //         ->where('created_at >=', $report['started_at'])
    //         ->where('created_at <=', $report['ended_at'])
    //         ->where('deleted_at', null)
    //         ->findAll();

    //     $mapping = [
    //         'A' => ['name' => 'Kas Yatim', 'ids' => [3]],
    //         'B' => ['name' => 'Kas PKU', 'ids' => [4]],
    //         'C' => ['name' => 'Kas Masjid', 'ids' => [1]],
    //         'D' => ['name' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
    //     ];

    //     $groupedData = [];
    //     foreach ($mapping as $key => $map) {
    //         $items = array_filter($transactions, function($t) use ($map) {
    //             return in_array($t['id_kategori_keuangan'], $map['ids']);
    //         });

    //         $saldoAwalMasuk = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pemasukan')
    //             ->where('created_at <', $report['started_at'])
    //             ->where('deleted_at', null)
    //             ->get()->getRow()->jumlah ?? 0;

    //         $saldoAwalKeluar = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pengeluaran')
    //             ->where('created_at <', $report['started_at'])
    //             ->where('deleted_at', null)
    //             ->get()->getRow()->jumlah ?? 0;

    //         $realSaldoAwal = $saldoAwalMasuk - $saldoAwalKeluar;

    //         $totalMasuk = 0;
    //         $totalKeluar = 0;
    //         foreach ($items as $item) {
    //             if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
    //             else $totalKeluar += $item['jumlah'];
    //         }

    //         $groupedData[$key] = [
    //             'judul'       => $map['name'],
    //             'saldo_awal'  => $realSaldoAwal,
    //             'items'       => $items,
    //             'total_masuk' => $totalMasuk,
    //             'total_keluar'=> $totalKeluar,
    //             'saldo_akhir' => ($realSaldoAwal + $totalMasuk) - $totalKeluar
    //         ];
    //     }

    //     return view('landing/finance/v_finance_detail', [
    //         'title'   => 'Detail Laporan: ' . $report['judul'],
    //         'report'  => $report,
    //         'grouped' => $groupedData
    //     ]);
    // }

    /**
     * Iterasi 2 - Method baru untuk melayani request AJAX
     */
    public function getMonthlyReportAjax()
    {
        try {
            $selectedYear = $this->request->getGet('tahun') ?? date('Y');
            $currentMonth = (int)date('m');
            $currentYear  = (int)date('Y');

            if ($selectedYear == $currentYear) {
                $endDate = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
            } else {
                $endDate = $selectedYear . '-12-31 23:59:59';
            }

            $dbData = $this->keuanganModel->select("MONTH(created_at) as bulan_num")
                            ->where("YEAR(created_at)", $selectedYear)
                            ->where("created_at <=", $endDate)
                            ->where("deleted_at", null)
                            ->groupBy("bulan_num")
                            ->orderBy("bulan_num", "DESC")
                            ->findAll();

            $monthlyData = [];

            foreach ($dbData as $row) {
                $m = (int)$row['bulan_num'];
                
                $status = ($selectedYear == $currentYear && $m == $currentMonth) ? 'On Process' : 'Final';
                
                $dateString = $selectedYear . '-' . sprintf('%02d', $m) . '-01';
                
                $monthlyData[] = [
                    'bulan_num'  => $m,
                    'bulan_name' => format_indo($dateString, 'month_year'),
                    'bulan_short'=> substr(format_indo($dateString, 'month_only'), 0, 3),
                    'tahun'      => $selectedYear,
                    'status'     => $status,
                    'url'        => base_url("keuangan/$selectedYear/$m")
                ];
            }

            return $this->response->setJSON($monthlyData);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function detail($tahun, $bulan)
    {
        $now = date('Y-m-d H:i:s');
        $currentMonth = date('m');
        $currentYear  = date('Y');

        // Menentukan Batas Akhir Transaksi (End Date)
        // Jika melihat bulan ini, batasi sampai hari Kamis terakhir jam 23:59:59
        if ($tahun == $currentYear && $bulan == $currentMonth) {
            $endDate = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
        } else {
            $endDate = date('Y-m-t', strtotime("$tahun-$bulan-01")) . ' 23:59:59';
        }

        // Saldo awal selalu dihitung dari sebelum tanggal 1 bulan terpilih
        $targetDate = "$tahun-" . sprintf('%02d', $bulan) . "-01 00:00:00";
        
        // 1. Cek apakah ada transaksi di periode tersebut (sampai batas Kamis jika bulan ini)
        $cekTransaksi = $this->keuanganModel->where("created_at <=", $endDate)
                                            ->where("YEAR(created_at)", $tahun)
                                            ->where("MONTH(created_at)", $bulan)
                                            ->countAllResults();
        
        if ($cekTransaksi == 0) {
            return redirect()->to('keuangan')->with('error', 'Laporan tidak tersedia atau belum ada transaksi hingga batas tutup buku.');
        }

        // 2. Hitung Saldo Awal (Semua transaksi SEBELUM bulan ini)
        $data['saldo_awal'] = $this->kategoriModel->select('kategori_keuangan.kategori, kategori_keuangan.id_kategori_keuangan')
            ->select('(COALESCE(SUM(CASE WHEN keu.jenis = "pemasukan" AND keu.created_at < "'.$targetDate.'" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) - 
                    COALESCE(SUM(CASE WHEN keu.jenis = "pengeluaran" AND keu.created_at < "'.$targetDate.'" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0)) as saldo')
            ->join('keuangan keu', 'keu.id_kategori_keuangan = kategori_keuangan.id_kategori_keuangan', 'left')
            ->groupBy('kategori_keuangan.id_kategori_keuangan')
            ->findAll();

        // 3. Ambil Data Alokasi & Details
        $alokasi = $this->alokasiModel->orderBy('urutan', 'ASC')->get()->getResultArray();
        foreach ($alokasi as &$al) {
            $al['details'] = $this->detailAlokasiModel
                                ->where('id_alokasi', $al['id_alokasi'])
                                ->orderBy('id_detail_alokasi', 'ASC')
                                ->get()->getResultArray();
        }
        $data['alokasi'] = $alokasi;

        // 4. Mapped Transaksi dengan Filter End Date
        $rawTransaksi = $this->keuanganModel->select('id_detail_alokasi, id_kategori_keuangan, jenis, SUM(jumlah) as total')
            ->where("created_at <=", $endDate)
            ->where("YEAR(created_at)", $tahun)
            ->where("MONTH(created_at)", $bulan)
            ->groupBy('id_detail_alokasi, id_kategori_keuangan, jenis')
            ->findAll();

        $mapped = [];
        foreach ($rawTransaksi as $rt) {
            $mapped[$rt['id_detail_alokasi']][$rt['id_kategori_keuangan']][$rt['jenis']] = $rt['total'];
        }
        $data['mapped_transaksi'] = $mapped;

        // 5. Detail Transaksi (Tabel Bawah) dengan Filter End Date
        $data['detail_transaksi'] = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, detail_alokasi.detail_alokasi')
            ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
            ->join('detail_alokasi', 'detail_alokasi.id_detail_alokasi = keuangan.id_detail_alokasi')
            ->where("keuangan.created_at <=", $endDate)
            ->where("YEAR(keuangan.created_at)", $tahun)
            ->where("MONTH(keuangan.created_at)", $bulan)
            ->orderBy('keuangan.created_at', 'ASC')
            ->findAll();

        $data['bulan_txt'] = format_indo($targetDate, 'month_year');
        $data['tgl_akhir_laporan'] = $endDate;
        $data['title'] = "Detail Laporan " . $data['bulan_txt'];
        
        return view('landing/finance/v_finance_detail', $data);
    }
}
