<?php

namespace App\Controllers\Admin;

use App\Models\KeuanganModel;
use App\Models\KategoriKeuanganModel;
use App\Models\LaporanMingguanModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    protected $keuanganModel;
    protected $kategoriModel;
    protected $laporanModel;

    public function __construct() {
        $this->keuanganModel = new KeuanganModel();
        $this->kategoriModel = new KategoriKeuanganModel();
        $this->laporanModel  = new LaporanMingguanModel();
    }

    public function detail($id)
    {
        $report = $this->laporanModel->find($id);
        if (!$report) return redirect()->to('admin/finance/routine')->with('error', 'Laporan tidak ditemukan.');
        
        $hariIni = date('Y-m-d');
        if ($report['ended_at'] > $hariIni) {
            return redirect()->to('admin/finance/routine')->with('error', 'Laporan pekan berjalan belum dapat dilihat detailnya sampai periode berakhir.');
        }

        // 1. Ambil data saldo awal (Saldo akhir dari laporan sebelumnya)
        $lastReport = $this->laporanModel->where('ended_at <', $report['started_at'])
                                        ->orderBy('ended_at', 'DESC')
                                        ->first();

        // 2. Ambil transaksi periode ini
        $transactions = $this->keuanganModel
            ->where('created_at >=', $report['started_at'])
            ->where('created_at <=', $report['ended_at'])
            ->findAll();

        // 3. Strukturkan data sesuai permintaan (A, B, C)
        $mapping = [
            'A' => ['name' => 'Kas Yatim & PKU', 'ids' => [3, 4]],
            'B' => ['name' => 'Kas Masjid', 'ids' => [1]],
            'C' => ['name' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
        ];

        $groupedData = [];
        foreach ($mapping as $key => $map) {
            // Filter transaksi yang masuk ke grup ini
            $items = array_filter($transactions, function($t) use ($map) {
                return in_array($t['id_kategori_keuangan'], $map['ids']);
            });

            // Hitung saldo awal (Logika: Total semua transaksi kategori tsb sebelum started_at laporan ini)
            $saldoAwal = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pemasukan')
                ->where('created_at <', $report['started_at'])
                ->get()->getRow()->jumlah ?? 0;

            $pengeluaranAwal = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pengeluaran')
                ->where('created_at <', $report['started_at'])
                ->get()->getRow()->jumlah ?? 0;

            $realSaldoAwal = $saldoAwal - $pengeluaranAwal;

            $totalMasuk = 0;
            $totalKeluar = 0;
            foreach ($items as $item) {
                if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
                else $totalKeluar += $item['jumlah'];
            }

            $groupedData[$key] = [
                'judul' => $map['name'],
                'saldo_awal' => $realSaldoAwal,
                'items' => $items,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'saldo_akhir' => ($realSaldoAwal + $totalMasuk) - $totalKeluar
            ];
        }

        $nextWeekStart = date('Y-m-d 00:00:00', strtotime($report['ended_at'] . ' +1 day'));
        $nextWeekEnd   = date('Y-m-d 23:59:59', strtotime($nextWeekStart . ' +6 days'));

        $agendaModel = new \App\Models\AgendaModel();
        $agendas = $agendaModel->select('agenda.*, kategori_agenda.nama_kategori, kw_mulai.keterangan as ket_mulai, kw_selesai.keterangan as ket_selesai')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda')
            ->join('keterangan_waktu as kw_mulai', 'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai', 'left')
            ->join('keterangan_waktu as kw_selesai', 'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai', 'left')
            ->where('waktu_mulai >=', $nextWeekStart)
            ->where('waktu_mulai <=', $nextWeekEnd)
            ->where('agenda.deleted_at', null)
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        // Ambil Pengisi/SDM untuk setiap agenda
        $sdmAgendaModel = new \App\Models\SdmAgendaModel();
        foreach ($agendas as &$a) {
            $a['pengisi'] = $sdmAgendaModel->select('sdm.nama, kategori_sdm.kategori as peran')
                ->join('sdm', 'sdm.id_sdm = sdm_agenda.id_sdm')
                ->join('kategori_sdm', 'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm')
                ->where('id_agenda', $a['id_agenda'])
                ->findAll();
        }

        return view('admin/finance/v_report_detail', [
            'title'  => 'Pratinjau Laporan Rutin',
            'report' => $report,
            'grouped' => $groupedData,
            'agendas' => $agendas,
            'nextWeekStart' => $nextWeekStart,
            'nextWeekEnd'   => $nextWeekEnd
        ]);
    }

    public function periodic()
    {
        $start = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end   = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Dipisah menjadi A, B, C, D agar Yatim dan PKU berdiri sendiri
        $mapping = [
            'A' => ['judul' => 'Kas Yatim', 'ids' => [3]],
            'B' => ['judul' => 'Kas PKU', 'ids' => [4]],
            'C' => ['judul' => 'Kas Masjid', 'ids' => [1]],
            'D' => ['judul' => 'Kas Perawatan & Renovasi', 'ids' => [2]],
        ];

        $groupedData = [];
        foreach ($mapping as $key => $map) {
            // Gunakan created_at untuk saldo awal
            $pemasukanLalu = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pemasukan')
                ->where('created_at <', $start . ' 00:00:00')->first()['jumlah'] ?? 0;

            $pengeluaranLalu = $this->keuanganModel->selectSum('jumlah')
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('jenis', 'pengeluaran')
                ->where('created_at <', $start . ' 00:00:00')->first()['jumlah'] ?? 0;

            $saldoAwal = $pemasukanLalu - $pengeluaranLalu;

            // Ambil Transaksi berdasarkan created_at
            $items = $this->keuanganModel
                ->whereIn('id_kategori_keuangan', $map['ids'])
                ->where('created_at >=', $start . ' 00:00:00')
                ->where('created_at <=', $end . ' 23:59:59')
                ->orderBy('created_at', 'ASC')
                ->findAll();

            $totalMasuk = 0;
            $totalKeluar = 0;
            foreach ($items as $item) {
                if ($item['jenis'] == 'pemasukan') $totalMasuk += $item['jumlah'];
                else $totalKeluar += $item['jumlah'];
            }

            $groupedData[$key] = [
                'judul'        => $map['judul'],
                'saldo_awal'   => $saldoAwal,
                'items'        => $items,
                'total_masuk'  => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'saldo_akhir'  => ($saldoAwal + $totalMasuk) - $totalKeluar
            ];
        }

        return view('admin/finance/v_report_periodic', [
            'title'   => 'Laporan Periodik Kas',
            'grouped' => $groupedData,
            'start'   => $start,
            'end'     => $end
        ]);
    }

    public function editNote($id) 
    {
        $report = $this->laporanModel->find($id);
        
        if (!$report) {
            return redirect()->to('admin/finance/routine')->with('error', 'Laporan tidak ditemukan.');
        }

        // Cek validasi 30 hari
        $limit = strtotime($report['ended_at'] . ' +30 days');
        if (time() > $limit) {
            return redirect()->to('admin/finance/routine')->with('error', 'Batas waktu edit catatan (30 hari) sudah berakhir.');
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

        return redirect()->to('admin/finance/routine')->with('success', 'Catatan laporan berhasil diperbarui.');
    }
}
