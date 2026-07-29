<?php

namespace App\Controllers\Admin;

use App\Models\KeuanganModel;
use App\Models\KategoriKeuanganModel;
use App\Models\LaporanMingguanModel;

// Iterasi 2
use App\Models\AlokasiModel;
use App\Models\DetailAlokasiModel;
use App\Models\SdmAgendaModel;
use App\Models\AgendaModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    protected $keuanganModel;
    protected $kategoriModel;
    protected $laporanModel;
    // Iterasi 2
    protected $alokasiModel;
    protected $detailAlokasiModel;
    protected $sdmAgendaModel;
    protected $agendaModel;

    public function __construct() {
        $this->keuanganModel = new KeuanganModel();
        $this->kategoriModel = new KategoriKeuanganModel();
        $this->laporanModel  = new LaporanMingguanModel();
        // Iterasi 2
        $this->alokasiModel  = new AlokasiModel();
        $this->detailAlokasiModel  = new DetailAlokasiModel();
        $this->sdmAgendaModel = new SdmAgendaModel();
        $this->agendaModel    = new AgendaModel();
    }

    // public function detail($id)
    // {
    //     $report = $this->laporanModel->find($id);
    //     if (!$report) return redirect()->to('admin/finance/report/weekly')->with('error', 'Laporan tidak ditemukan.');
        
    //     $hariIni = date('Y-m-d');
    //     if ($report['ended_at'] > $hariIni) {
    //         return redirect()->to('admin/finance/report/weekly')->with('error', 'Laporan pekan berjalan belum dapat dilihat detailnya sampai periode berakhir.');
    //     }

    //     // 1. Ambil data saldo awal (Saldo akhir dari laporan sebelumnya)
    //     $lastReport = $this->laporanModel->where('ended_at <', $report['started_at'])
    //                                     ->orderBy('ended_at', 'DESC')
    //                                     ->first();

    //     // 2. Ambil transaksi periode ini
    //     $transactions = $this->keuanganModel
    //         ->where('created_at >=', $report['started_at'])
    //         ->where('created_at <=', $report['ended_at'])
    //         ->findAll();

    //     // 3. Strukturkan data sesuai permintaan (A, B, C)
    //     $mapping = [
    //         'A' => ['name' => 'Kas Yatim & PKU', 'ids' => [3, 4]],
    //         'B' => ['name' => 'Kas Masjid', 'ids' => [1]],
    //         'C' => ['name' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
    //     ];

    //     $groupedData = [];
    //     foreach ($mapping as $key => $map) {
    //         // Filter transaksi yang masuk ke grup ini
    //         $items = array_filter($transactions, function($t) use ($map) {
    //             return in_array($t['id_kategori_keuangan'], $map['ids']);
    //         });

    //         // Hitung saldo awal (Logika: Total semua transaksi kategori tsb sebelum started_at laporan ini)
    //         $saldoAwal = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pemasukan')
    //             ->where('created_at <', $report['started_at'])
    //             ->get()->getRow()->jumlah ?? 0;

    //         $pengeluaranAwal = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pengeluaran')
    //             ->where('created_at <', $report['started_at'])
    //             ->get()->getRow()->jumlah ?? 0;

    //         $realSaldoAwal = $saldoAwal - $pengeluaranAwal;

    //         $totalMasuk = 0;
    //         $totalKeluar = 0;
    //         foreach ($items as $item) {
    //             if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
    //             else $totalKeluar += $item['jumlah'];
    //         }

    //         $groupedData[$key] = [
    //             'judul' => $map['name'],
    //             'saldo_awal' => $realSaldoAwal,
    //             'items' => $items,
    //             'total_masuk' => $totalMasuk,
    //             'total_keluar' => $totalKeluar,
    //             'saldo_akhir' => ($realSaldoAwal + $totalMasuk) - $totalKeluar
    //         ];
    //     }

    //     $nextWeekStart = date('Y-m-d 00:00:00', strtotime($report['ended_at'] . ' +1 day'));
    //     $nextWeekEnd   = date('Y-m-d 23:59:59', strtotime($nextWeekStart . ' +6 days'));

    //     $agendaModel = new \App\Models\AgendaModel();
    //     $agendas = $agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kw_mulai.keterangan as ket_mulai, kw_selesai.keterangan as ket_selesai')
    //         ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda')
    //         ->join('keterangan_waktu as kw_mulai', 'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai', 'left')
    //         ->join('keterangan_waktu as kw_selesai', 'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai', 'left')
    //         ->where('waktu_mulai >=', $nextWeekStart)
    //         ->where('waktu_mulai <=', $nextWeekEnd)
    //         ->where('agenda.deleted_at', null)
    //         ->orderBy('waktu_mulai', 'ASC')
    //         ->findAll();

    //     // Ambil Pengisi/SDM untuk setiap agenda
    //     $sdmAgendaModel = new \App\Models\SdmAgendaModel();
    //     foreach ($agendas as &$a) {
    //         $a['pengisi'] = $sdmAgendaModel->select('sdm.nama, kategori_sdm.kategori as peran')
    //             ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
    //             ->join('kategori_sdm', 'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm')
    //             ->where('id_agenda', $a['id_agenda'])
    //             ->findAll();
    //     }

    //     return view('admin/finance/report/v_report_weekly_detail', [
    //         'title'  => 'Pratinjau Laporan Rutin',
    //         'report' => $report,
    //         'grouped' => $groupedData,
    //         'agendas' => $agendas,
    //         'nextWeekStart' => $nextWeekStart,
    //         'nextWeekEnd'   => $nextWeekEnd
    //     ]);
    // }

    // public function periodic()
    // {
    //     $start = $this->request->getGet('start_date') ?? date('Y-m-01');
    //     $end   = $this->request->getGet('end_date') ?? date('Y-m-d');

    //     // Dipisah menjadi A, B, C, D agar Yatim dan PKU berdiri sendiri
    //     $mapping = [
    //         'A' => ['judul' => 'Kas Yatim', 'ids' => [3]],
    //         'B' => ['judul' => 'Kas PKU', 'ids' => [4]],
    //         'C' => ['judul' => 'Kas Masjid', 'ids' => [1]],
    //         'D' => ['judul' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
    //     ];

    //     $groupedData = [];
    //     foreach ($mapping as $key => $map) {
    //         // Gunakan created_at untuk saldo awal
    //         $pemasukanLalu = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pemasukan')
    //             ->where('created_at <', $start . ' 00:00:00')->first()['jumlah'] ?? 0;

    //         $pengeluaranLalu = $this->keuanganModel->selectSum('jumlah')
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('jenis', 'pengeluaran')
    //             ->where('created_at <', $start . ' 00:00:00')->first()['jumlah'] ?? 0;

    //         $saldoAwal = $pemasukanLalu - $pengeluaranLalu;

    //         // Ambil Transaksi berdasarkan created_at
    //         $items = $this->keuanganModel
    //             ->whereIn('id_kategori_keuangan', $map['ids'])
    //             ->where('created_at >=', $start . ' 00:00:00')
    //             ->where('created_at <=', $end . ' 23:59:59')
    //             ->orderBy('created_at', 'ASC')
    //             ->findAll();

    //         $totalMasuk = 0;
    //         $totalKeluar = 0;
    //         foreach ($items as $item) {
    //             if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
    //             else $totalKeluar += $item['jumlah'];
    //         }

    //         $groupedData[$key] = [
    //             'judul'        => $map['judul'],
    //             'saldo_awal'   => $saldoAwal,
    //             'items'        => $items,
    //             'total_masuk'  => $totalMasuk,
    //             'total_keluar' => $totalKeluar,
    //             'saldo_akhir'  => ($saldoAwal + $totalMasuk) - $totalKeluar
    //         ];
    //     }

    //     return view('admin/finance/report/v_report_periodic', [
    //         'title'   => 'Laporan Periodik Kas',
    //         'grouped' => $groupedData,
    //         'start'   => $start,
    //         'end'     => $end
    //     ]);
    // }

    public function editNote($id) 
    {
        $report = $this->laporanModel->find($id);
        
        if (!$report) {
            return redirect()->to('admin/finance/report/weekly')->with('error', 'Laporan tidak ditemukan.');
        }

        // Cek validasi 30 hari
        $limit = strtotime($report['ended_at'] . ' +30 days');
        if (time() > $limit) {
            return redirect()->to('admin/finance/report/weekly')->with('error', 'Batas waktu edit catatan (30 hari) sudah berakhir.');
        }

        return view('admin/finance/v_edit_note', [
            'title'  => 'Edit Catatan Laporan',
            'report' => $report
        ]);
    }

    public function updateNote() 
    {
        $id   = $this->request->getPost('id_laporan');
        $note = $this->request->getPost('catatan');

        $this->laporanModel->update($id, [
            'catatan'   => $note,
            'updated_by' => session()->get('id_akun')
        ]);

        return redirect()->to('admin/finance/report/weekly')->with('success', 'Catatan laporan berhasil diperbarui.');
    }

    // Iterasi 2
    public function detail($id)
    {
        $report = $this->laporanModel->find($id);
        if (!$report) return redirect()->to('admin/finance/report/weekly')->with('error', 'Laporan tidak ditemukan.');

        // $hariIni = date('Y-m-d');
        // if ($report['ended_at'] > $hariIni) {
        //     // return redirect()->to('admin/finance/report/weekly')->with('error', 'Laporan pekan berjalan belum dapat dilihat detailnya sampai periode berakhir.');
        // }

        $transactions = $this->keuanganModel
            ->select('keuangan.*, rp.nama_ringkasan_protokol, rp.kalimat_pemasukan, rp.kalimat_pengeluaran, da.detail_alokasi, da.id_ringkasan_protokol')
            ->join('detail_alokasi da', 'da.id_detail_alokasi = keuangan.id_detail_alokasi', 'left')
            ->join('ringkasan_protokol rp', 'rp.id_ringkasan_protokol = da.id_ringkasan_protokol', 'left')
            ->where('keuangan.created_at >=', $report['started_at'])
            ->where('keuangan.created_at <=', $report['ended_at'])
            ->where('keuangan.deleted_at', null)
            ->orderBy('keuangan.jenis', 'ASC')
            ->findAll();

        $mapping = [
            'A' => ['name' => 'Kas Yatim', 'ids' => [3]],
            'B' => ['name' => 'Kas PKU', 'ids' => [4]],
            'C' => ['name' => 'Kas Masjid', 'ids' => [1]],
            'D' => ['name' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
        ];

        $groupedData = [];
        foreach ($mapping as $key => $map) {
            $items = array_filter($transactions, function ($t) use ($map) {
                return in_array($t['id_kategori_keuangan'], $map['ids']);
            });

            // Hitung Saldo Awal (Gunakan alias atau join jika perlu, tapi ini query simpel)
            $pemasukanAwal = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pemasukan')
                ->where('created_at <', $report['started_at'])
                ->where('deleted_at', null)->get()->getRow()->jumlah ?? 0;

            $pengeluaranAwal = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pengeluaran')
                ->where('created_at <', $report['started_at'])
                ->where('deleted_at', null)->get()->getRow()->jumlah ?? 0;

            $realSaldoAwal = $pemasukanAwal - $pengeluaranAwal;

            $summary = [];
            $totalMasuk = 0;
            $totalKeluar = 0;

            foreach ($items as $item) {
                $isGrouped = !empty($item['id_ringkasan_protokol']);
                $groupKey = $isGrouped 
                            ? 'protokol_' . $item['id_ringkasan_protokol'] . '_' . $item['jenis'] 
                            : 'raw_' . $item['id_keuangan'] . '_' . $item['jenis'];
                
                if (!isset($summary[$groupKey])) {
                    $namaJenis = ucfirst($item['jenis']); 
                    if ($isGrouped) {
                        if ($item['jenis'] === 'pemasukan' && !empty($item['kalimat_pemasukan'])) {
                            $teksKeterangan = $item['kalimat_pemasukan'];
                        } elseif ($item['jenis'] === 'pengeluaran' && !empty($item['kalimat_pengeluaran'])) {
                            $teksKeterangan = $item['kalimat_pengeluaran'];
                        } else {
                            $teksKeterangan = $namaJenis . ' ' . $item['nama_ringkasan_protokol'];
                        }
                    } else {
                        $teksKeterangan = $namaJenis . ' ' . $item['keterangan'];
                    }

                    $summary[$groupKey] = [
                        'jenis'      => $item['jenis'],
                        'keterangan' => $teksKeterangan,
                        'alokasi'    => [],
                        'total'      => 0
                    ];
                }

                $summary[$groupKey]['total'] += $item['jumlah'];
                
                if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
                else $totalKeluar += $item['jumlah'];

                if (!empty($item['detail_alokasi']) && !in_array($item['detail_alokasi'], $summary[$groupKey]['alokasi'])) {
                    $summary[$groupKey]['alokasi'][] = $item['detail_alokasi'];
                }
            }

            uasort($summary, function($a, $b) {
                if ($a['jenis'] !== $b['jenis']) {
                    return ($a['jenis'] === 'pemasukan') ? -1 : 1;
                }
                
                return strcmp($a['keterangan'], $b['keterangan']);
            });

            $groupedData[$key] = [
                'judul'       => $map['name'],
                'saldo_awal'  => $realSaldoAwal,
                'summary'     => $summary,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'saldo_akhir' => ($realSaldoAwal + $totalMasuk) - $totalKeluar
            ];
        }

        // Logika Agenda
        $nextWeekStart = date('Y-m-d 00:00:00', strtotime($report['ended_at'] . ' +1 day'));
        $nextWeekEnd   = date('Y-m-d 23:59:59', strtotime($nextWeekStart . ' +6 days'));

        $agendas = $this->agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kw_mulai.keterangan as ket_mulai, kw_selesai.keterangan as ket_selesai')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda')
            ->join('keterangan_waktu as kw_mulai', 'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai', 'left')
            ->join('keterangan_waktu as kw_selesai', 'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai', 'left')
            ->where('waktu_mulai >=', $nextWeekStart)
            ->where('waktu_mulai <=', $nextWeekEnd)
            ->where('agenda.deleted_at', null)
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        foreach ($agendas as &$a) {
            $a['pengisi'] = $this->sdmAgendaModel->select('sdm.nama, kategori_sdm.kategori as peran')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->join('kategori_sdm', 'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm')
                ->where('id_agenda', $a['id_agenda'])
                ->findAll();
        }

        return view('admin/report/v_report_weekly_detail', [
            'title'         => 'Detail Laporan Mingguan',
            'report'        => $report,
            'grouped'       => $groupedData,
            'agendas'       => $agendas,
            'nextWeekStart' => $nextWeekStart,
            'nextWeekEnd'   => $nextWeekEnd
        ]);
    }


    public function periodic()
    {
        $today = date('Y-m-d');

        $defaultEnd = date('Y-m-d', strtotime('last thursday'));

        $end = $this->request->getPost('end_date') ?? $defaultEnd;
        $start = $this->request->getPost('start_date') ?? date('Y-m-01', strtotime($end));
        
        $endTime = $end . ' 23:59:59';
        $startTime = $start . ' 00:00:00';

        // 1. Ambil Semua Kategori Kas untuk Kolom Tabel
        $data['categories'] = $this->kategoriModel->orderBy('id_kategori_keuangan', 'ASC')->findAll();

        // 2. Hitung Saldo Awal (Sebelum $startTime) per Kategori
        $saldoAwal = [];
        foreach ($data['categories'] as $kat) {
            $masukLalu = $this->keuanganModel->selectSum('jumlah')
                ->where('id_kategori_keuangan', $kat['id_kategori_keuangan'])
                ->where('jenis', 'pemasukan')
                ->where('created_at <', $startTime)
                ->where('deleted_at', null)->first()['jumlah'] ?? 0;

            $keluarLalu = $this->keuanganModel->selectSum('jumlah')
                ->where('id_kategori_keuangan', $kat['id_kategori_keuangan'])
                ->where('jenis', 'pengeluaran')
                ->where('created_at <', $startTime)
                ->where('deleted_at', null)->first()['jumlah'] ?? 0;

            $saldoAwal[$kat['id_kategori_keuangan']] = $masukLalu - $keluarLalu;
        }
        $data['saldo_awal'] = $saldoAwal;

        // 3. Ambil Struktur Alokasi & Details
        $alokasi = $this->alokasiModel->orderBy('urutan', 'ASC')->findAll();
        foreach ($alokasi as &$al) {
            $al['details'] = $this->detailAlokasiModel
                ->where('id_alokasi', $al['id_alokasi'])
                ->orderBy('id_detail_alokasi', 'ASC')
                ->findAll();
        }
        $data['alokasi'] = $alokasi;

        // 4. Mapping Transaksi (Masuk & Keluar) dalam Range Periode
        $rawTransaksi = $this->keuanganModel->select('id_detail_alokasi, id_kategori_keuangan, jenis, SUM(jumlah) as total')
            ->where('created_at >=', $startTime)
            ->where('created_at <=', $endTime)
            ->where('deleted_at', null)
            ->groupBy('id_detail_alokasi, id_kategori_keuangan, jenis')
            ->findAll();

        $mapped = [];
        foreach ($rawTransaksi as $rt) {
            $mapped[$rt['id_detail_alokasi']][$rt['id_kategori_keuangan']][$rt['jenis']] = $rt['total'];
        }
        $data['mapped_transaksi'] = $mapped;

        $data['detail_transaksi'] = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, detail_alokasi.detail_alokasi, alokasi.nama_alokasi as alokasi')
            ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
            ->join('detail_alokasi', 'detail_alokasi.id_detail_alokasi = keuangan.id_detail_alokasi')
            ->join('alokasi', 'alokasi.id_alokasi = detail_alokasi.id_alokasi')
            ->where('keuangan.created_at >=', $startTime)
            ->where('keuangan.created_at <=', $endTime)
            ->where('keuangan.deleted_at', null)
            ->orderBy('keuangan.id_kategori_keuangan', 'ASC')
            ->orderBy('DATE(keuangan.created_at)', 'ASC')
            ->orderBy('keuangan.jenis', 'ASC')
            // ->orderBy('keuangan.created_at', 'ASC')
            ->findAll();

        // 5. Data Tambahan untuk View
        $data['title'] = 'Laporan Keuangan Periodik';
        $data['start'] = $start;
        $data['end']   = $end;
        $data['tgl_akhir_laporan'] = $endTime;

        // dd($data);

        return view('admin/report/v_report_periodic', $data);
    }

    // Iterasi 3
    public function weekly() {
        $data['years'] = $this->laporanModel
            ->select('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->findAll();
        $data['title'] = 'Laporan Keuangan Mingguan';

        return view('admin/report/v_report_weekly_list', $data);
    }

    public function getWeeklyHistoryAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $builder = $this->laporanModel->where('YEAR(ended_at)', $tahun);

        $historyData = $builder->orderBy('ended_at', 'DESC')->findAll();

        $id_peran = session()->get('id_peran');

        $response = [];
        foreach ($historyData as $h) {
            // Logika 30 hari untuk edit
            $canEdit = (time() <= strtotime($h['ended_at'] . ' +30 days'));

            $response[] = [
                'id_laporan_mingguan' => $h['id_laporan_mingguan'],
                'judul'               => $h['judul'],
                'catatan'             => $h['catatan'] ? strip_tags($h['catatan']) : '-',
                'can_view'            => in_array($id_peran, [2, 3, 4]),
                'can_edit'            => in_array($id_peran, [4]) && $canEdit,
            ];
        }

        return $this->response->setJSON($response);
    }

    public function monthly() {
        $endDate = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
        $data['years'] = $this->keuanganModel->select("YEAR(created_at) as year")
                         ->where("created_at <=", $endDate)
                         ->where("deleted_at", null)
                         ->groupBy("year")
                         ->orderBy("year", "DESC")
                         ->findAll();
        $data['title'] = 'Laporan Keuangan Bulanan';

        return view('admin/report/v_report_monthly_list', $data);
    }

    public function detailMonthly($tahun, $bulan)
    {
        $now = date('Y-m-d H:i:s');
        $currentYear = date('Y');
        
        $cutOff = date('Y-m-d', strtotime('last thursday'));

        $cutOffMonth = date('m', strtotime($cutOff));
        $cutOffYear  = date('Y', strtotime($cutOff));
        
        $reportMonth = strtotime(sprintf('%04d-%02d-01', $tahun, $bulan));
        $availableMonth = strtotime(date('Y-m-01', strtotime($cutOff)));

        if ($reportMonth > $availableMonth) {
            return redirect()
                ->to('admin/finance/report/monthly')
                ->with('error', 'Belum ada data final bulan ini.');
        }

        // Jika yang dibuka adalah bulan cut-off,
        // tampilkan data sampai tanggal cut-off.
        if ($tahun == $cutOffYear && $bulan == $cutOffMonth) {
            $endDate = $cutOff . ' 23:59:59';
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
            return redirect()->to('admin/finance/report/monthly')->with('error', 'Laporan tidak tersedia atau belum ada transaksi hingga batas tutup buku.');
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
        $data['detail_transaksi'] = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, kategori_keuangan.class_color, detail_alokasi.detail_alokasi, alokasi.nama_alokasi as alokasi')
            ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
            ->join('detail_alokasi', 'detail_alokasi.id_detail_alokasi = keuangan.id_detail_alokasi')
            ->join('alokasi', 'alokasi.id_alokasi = detail_alokasi.id_alokasi')
            ->where("keuangan.created_at <=", $endDate)
            ->where("YEAR(keuangan.created_at)", $tahun)
            ->where("MONTH(keuangan.created_at)", $bulan)
            ->orderBy('keuangan.id_kategori_keuangan', 'ASC')
            ->orderBy('DATE(keuangan.created_at)', 'ASC')
            ->orderBy('keuangan.jenis', 'ASC')
            // ->orderBy('keuangan.created_at', 'ASC')
            ->findAll();

        $data['isDraft'] = (
            (int)$tahun === (int)$cutOffYear &&
            (int)$bulan === (int)$cutOffMonth
        );
        
        $data['bulan_txt'] = format_indo($targetDate, 'month_year');
        $data['tgl_akhir_laporan'] = $endDate;
        $data['title'] = "Detail Laporan " . $data['bulan_txt'];
        return view('admin/report/v_report_monthly_detail', $data);
    }

    public function chart()
    {
        $startDate = date('Y-m-01', strtotime('-5 months'));
        $endDate   = date('Y-m-d H:i:s');

        $data['alokasi'] = $this->alokasiModel
            ->select('alokasi.id_alokasi, alokasi.nama_alokasi')
            ->join('detail_alokasi', 'detail_alokasi.id_alokasi = alokasi.id_alokasi')
            ->join('keuangan', 'keuangan.id_detail_alokasi = detail_alokasi.id_detail_alokasi')
            ->where('keuangan.deleted_at', null)
            ->where('keuangan.created_at >=', $startDate)
            ->where('keuangan.created_at <=', $endDate)
            ->groupBy('alokasi.id_alokasi')
            ->orderBy('alokasi.urutan', 'ASC')
            ->findAll();

        $data['title'] = 'Grafik Statistik';

        return view('admin/report/v_report_chart', $data);
    }

    public function getDetailAlokasi()
    {
        $idAlokasi = $this->request->getGet('id_alokasi');

        $startDate = date('Y-m-01', strtotime('-5 months'));
        $endDate   = date('Y-m-d H:i:s');

        $builder = $this->detailAlokasiModel
            ->select('detail_alokasi.id_detail_alokasi, detail_alokasi.detail_alokasi')
            ->join('keuangan', 'keuangan.id_detail_alokasi = detail_alokasi.id_detail_alokasi')
            ->where('keuangan.deleted_at', null)
            ->where('keuangan.created_at >=', $startDate)
            ->where('keuangan.created_at <=', $endDate);

        if (!empty($idAlokasi)) {
            $builder->where('detail_alokasi.id_alokasi', $idAlokasi);
        }

        $detail = $builder
            ->groupBy('detail_alokasi.id_detail_alokasi')
            ->orderBy('detail_alokasi.detail_alokasi', 'ASC')
            ->findAll();

        return $this->response->setJSON($detail);
    }

    public function getChartData()
    {
        return $this->response->setJSON([

            'line' => $this->chartLine(),

            'kategori_masuk' => $this->pieKategori('pemasukan'),

            'kategori_keluar' => $this->pieKategori('pengeluaran'),

            'alokasi' => $this->pieAlokasi(),

            'detail' => $this->pieDetail(),

        ]);
    }

    private function buildFilter($builder)
    {
        $startDate = date('Y-m-01', strtotime('-5 months'));

        $builder
            ->join('detail_alokasi', 'detail_alokasi.id_detail_alokasi = keuangan.id_detail_alokasi')
            ->where('keuangan.deleted_at', null)
            ->where('keuangan.created_at >=', $startDate);

        $alokasi = $this->request->getGet('alokasi');
        $detail  = $this->request->getGet('detail');

        if (!empty($alokasi)) {
            $builder->where('detail_alokasi.id_alokasi', $alokasi);
        }

        if (!empty($detail)) {
            $builder->where('keuangan.id_detail_alokasi', $detail);
        }

        return $builder;
    }

    private function chartLine()
    {
        $idAlokasi = $this->request->getGet('alokasi');
        $idDetail  = $this->request->getGet('detail');

        $kategoriList = $this->kategoriModel
            ->orderBy('id_kategori_keuangan', 'ASC')
            ->findAll();

        $chart = [];

        foreach ($kategoriList as $kat) {

            $data = $this->keuanganModel->getSaldoPerKategori(
                $kat['id_kategori_keuangan'],
                6,
                true,
                $idAlokasi,
                $idDetail
            );

            $adaTransaksi = false;

            foreach ($data as $row) {
                if ($row['masuk'] != 0 || $row['keluar'] != 0) {
                    $adaTransaksi = true;
                    break;
                }
            }

            if ($adaTransaksi) {
                $chart[$kat['kategori']] = $data;
            }
        }

        return $chart;
    }

    private function pieKategori($jenis)
    {
        $builder = $this->keuanganModel;

        $this->buildFilter($builder);

        return $builder
            ->join(
                'kategori_keuangan',
                'kategori_keuangan.id_kategori_keuangan=keuangan.id_kategori_keuangan'
            )
            ->select("
                kategori_keuangan.kategori,
                SUM(keuangan.jumlah) total,
                COUNT(*) transaksi
            ", false)
            ->where('keuangan.jenis', $jenis)
            ->groupBy('kategori_keuangan.id_kategori_keuangan')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    private function pieAlokasi()
    {
        $builder = $this->keuanganModel;

        $this->buildFilter($builder);

        return $builder
            ->join(
                'alokasi',
                'alokasi.id_alokasi=detail_alokasi.id_alokasi'
            )
            ->select("
                alokasi.nama_alokasi,
                SUM(keuangan.jumlah) total,
                COUNT(*) transaksi
            ", false)
            ->groupBy('alokasi.id_alokasi')
            ->orderBy('total','DESC')
            ->findAll();
    }

    private function pieDetail()
    {
        $builder = $this->keuanganModel;

        $this->buildFilter($builder);

        return $builder
            ->select("
                detail_alokasi.detail_alokasi,
                SUM(keuangan.jumlah) total,
                COUNT(*) transaksi
            ", false)
            ->groupBy('detail_alokasi.id_detail_alokasi')
            ->orderBy('total','DESC')
            ->findAll();
    }
}
