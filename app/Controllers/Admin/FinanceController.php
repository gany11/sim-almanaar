<?php

namespace App\Controllers\Admin;

use App\Models\KeuanganModel;
use App\Models\KategoriKeuanganModel;
use App\Models\LaporanMingguanModel;

// Iterasi 2
use App\Models\AlokasiModel;
use App\Models\DetailAlokasiModel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FinanceController extends BaseController
{
    protected $keuanganModel;
    protected $kategoriModel;
    protected $laporanModel;
    // Iterasi 2
    protected $alokasiModel;
    protected $detailAlokasiModel;


    public function __construct() {
        $this->keuanganModel = new KeuanganModel();
        $this->kategoriModel = new KategoriKeuanganModel();
        $this->laporanModel  = new LaporanMingguanModel();
        // Iterasi 2
        $this->alokasiModel  = new AlokasiModel();
        $this->detailAlokasiModel  = new DetailAlokasiModel();
    }

    public function index() {
        return view('admin/finance/v_index', [
            'title'    => 'Keuangan Rutin',
            'kategori' => $this->kategoriModel->findAll(),
            'summaryKeuangan' => $this->keuanganModel->getSummaryPerKategori()
        ]);
    }

    public function list() {
        $now = date('Y-m-d H:i:s');
        
        $currentReport = $this->laporanModel
            ->where('started_at <=', $now)
            ->where('ended_at >=', $now)
            ->first();

        $builder = $this->keuanganModel->select('keuangan.*, kategori_keuangan.kategori, kategori_keuangan.class_color, creator.nama as creator_name, editor.nama as editor_name')
                    ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
                    ->join('akun as creator', 'creator.id_akun = keuangan.created_by', 'left')
                    ->join('akun as editor', 'editor.id_akun = keuangan.updated_by', 'left');

        if ($currentReport) {
            $builder->where('keuangan.created_at >=', $currentReport['started_at'])
                    ->where('keuangan.created_at <=', $currentReport['ended_at']);
        } else {
            $builder->where('keuangan.id_keuangan', 0); 
        }

        $cat  = $this->request->getPost('id_kategori_keuangan');
        $type = $this->request->getPost('jenis');
        if ($cat) $builder->where('keuangan.id_kategori_keuangan', $cat);
        if ($type) $builder->where('keuangan.jenis', $type);

        $data['current_report'] = $currentReport;
        $data['routine'] = $builder->orderBy('keuangan.created_at', 'ASC')->findAll();

        $data['summaryKeuangan'] = $this->keuanganModel->getSummaryPerKategori();

        $data['history'] = $this->laporanModel
            ->where('ended_at <', $now)
            ->orderBy('ended_at', 'DESC')
            ->findAll();

        return view('admin/finance/v_list_partial', $data);
    }

    public function create() {
        return view('admin/finance/v_form', [
            'title'    => 'Tambah Transaksi',
            'kategori' => $this->kategoriModel->findAll(),
            // Iterasi 2 - Ambil data alokasi untuk pilihan di form
            'alokasi'  => $this->alokasiModel->findAll()
        ]);
    }

    public function edit($id) {
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return redirect()->to('admin/finance/routine')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return redirect()->to('admin/finance/routine')->with('error', 'Akses ditolak! Transaksi pada laporan yang sudah lewat tidak dapat diubah.');
        }

        return view('admin/finance/v_form', [
            'title'    => 'Edit Transaksi',
            'keuangan' => $keuangan,
            'kategori' => $this->kategoriModel->findAll(),
            // Iterasi 2 - Ambil data alokasi untuk pilihan di form
            'alokasi'  => $this->alokasiModel->findAll(),
            // Iterasi 2 - Kirim detail alokasi yang relevan dengan alokasi yang sudah terpilih sebelumnya
            'current_detail' => $this->detailAlokasiModel->where('id_alokasi', $this->_getIdAlokasiFromDetail($keuangan['id_detail_alokasi']))->findAll()
        ]);
    }

    public function save() { 
        return $this->_store(); 
    }

    public function update($id) { 
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return redirect()->to('admin/finance/routine')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return redirect()->to('admin/finance/routine')->with('error', 'Akses ditolak! Transaksi pada laporan yang sudah lewat tidak dapat diubah.');
        }

        return $this->_store($id); 
    }

    private function _store($id = null) {
        $file = $this->request->getFile('bukti');

        if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
            return redirect()->back()->withInput()->with('errors', [
                'bukti' => 'Ukuran file terlalu besar (melebihi batas limit server).'
            ]);
        }

        $rules = $this->keuanganModel->validationKeuangan;

        
        $jumlahRaw = $this->request->getPost('jumlah');
        $jumlahBeforeDecimal = explode('.', $jumlahRaw)[0];
        $jumlahClean = str_replace('.', '', $jumlahBeforeDecimal);
        // $jumlahClean = str_replace('.', '', $jumlahRaw ?? '0');
        $_POST['jumlah'] = $jumlahClean;
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldData = $id ? $this->keuanganModel->find($id) : null;
        $fileName = $oldData ? $oldData['bukti'] : null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move('uploads/keuangan', $fileName);
            
            if ($oldData && $oldData['bukti'] && file_exists('uploads/keuangan/' . $oldData['bukti'])) {
                @unlink('uploads/keuangan/' . $oldData['bukti']);
            }
        }

        $nowTime = date('Y-m-d H:i:s');
        $this->_ensureReportExists($nowTime);

        $saveData = [
            'id_kategori_keuangan' => $this->request->getPost('id_kategori_keuangan'),
            'id_detail_alokasi'    => $this->request->getPost('id_detail_alokasi'), // Iterasi 2 - Field Baru
            'tanggal'              => $this->request->getPost('tanggal'),
            'jumlah'               => $jumlahClean,
            'jenis'                => $this->request->getPost('jenis'),
            'keterangan'           => $this->request->getPost('keterangan'),
            'pic'                  => $this->request->getPost('pic'), // Iterasi 2 - Field Baru
            'method_input'         => 'manual', // Iterasi 2 - Paksa manual karena lewat form
            'bukti'                => $fileName,
        ];

        $currentUserId = session()->get('id_akun');

        if (!$id) {
            $saveData['created_by'] = $currentUserId;
            $this->keuanganModel->insert($saveData);
            $msg = 'Data transaksi berhasil dicatat.';
        } else {
            $saveData['updated_by'] = $currentUserId;
            $this->keuanganModel->update($id, $saveData);
            $msg = 'Perubahan transaksi berhasil disimpan.';
        }

        return redirect()->to('admin/finance/routine')->with('success', $msg);
    }

    private function _ensureReportExists($time) {
        $check = $this->laporanModel->where('started_at <=', $time)->where('ended_at >=', $time)->first();
        if (!$check) {
            // Hitung Jumat Terdekat (Start)
            $start = date('Y-m-d 00:00:00', strtotime('last friday', strtotime($time)));
            if (date('N', strtotime($time)) == 5) $start = date('Y-m-d 00:00:00', strtotime($time));
            
            $end = date('Y-m-d 23:59:59', strtotime($start . ' +6 days'));
            
            // Hitung Pekan ke- (P1, P2, dst)
            $dayOfMonth = date('j', strtotime($start));
            $weekNum    = ceil($dayOfMonth / 7);
            $monthName = format_indo($start, 'month_only');
        
            $dateStartIndo = format_indo($start, 'full_date');
            $dateEndIndo   = format_indo($end, 'full_date');
            
            $title = "Laporan Keuangan {$dateStartIndo} - {$dateEndIndo} (P{$weekNum} {$monthName})";

            $this->laporanModel->insert([
                'judul'      => $title,
                'started_at' => $start,
                'ended_at'   => $end,
                'created_by' => session()->get('id_akun')
            ]);
        }
    }

    private function _isCurrentPeriod($createdAt) {
        $now = date('Y-m-d H:i:s');
        $currentReport = $this->laporanModel
            ->where('started_at <=', $now)
            ->where('ended_at >=', $now)
            ->first();

        if (!$currentReport) return false;

        return ($createdAt >= $currentReport['started_at'] && $createdAt <= $currentReport['ended_at']);
    }

    public function delete() {
        $id = $this->request->getPost('id_keuangan');
        $keuangan = $this->keuanganModel->find($id);

        if (!$keuangan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        if (!$this->_isCurrentPeriod($keuangan['created_at'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi lama tidak boleh dihapus demi integritas laporan.']);
        }

        $this->keuanganModel->update($id, ['deleted_by' => session()->get('id_akun')]);
        $this->keuanganModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
    }

    // Iterasi 2
    private function _getIdAlokasiFromDetail($idDetail) {
        $detail = $this->detailAlokasiModel->find($idDetail);
        return $detail ? $detail['id_alokasi'] : null;
    }

    public function getDetailAlokasi()
    {
        $idAlokasi = $this->request->getPost('id_alokasi');
        $details = $this->detailAlokasiModel->where('id_alokasi', $idAlokasi)->findAll();

        return $this->response->setJSON($details);
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file->isValid()) return redirect()->to('admin/finance/routine')->with('error', 'File tidak valid.');

        $reader = new Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $dataRaw = $spreadsheet->getActiveSheet()->toArray();

        $dataToInsert = [];
        $importErrors = [];
        $skippedCount = 0;

        // 1. Tentukan Rentang Duplikasi (Jumat - Kamis Pekan Ini)
        $startOfWeek = date('Y-m-d 00:00:00', strtotime('last friday', strtotime('tomorrow')));
        $endOfWeek   = date('Y-m-d 23:59:59', strtotime('next thursday', strtotime('yesterday')));

        // Mulai iterasi dari baris ke-5 (Index 4)
        for ($i = 4; $i < count($dataRaw); $i++) {
            $row = $dataRaw[$i];
            
            // Skip jika baris benar-benar kosong
            if (empty(array_filter($row))) continue;

            $rowNumber = $i + 1;

            // 2. Validasi Input Dasar
            $lineErrors = [];
            if (empty($row[0])) $lineErrors[] = "Tanggal kosong";
            if (empty($row[1])) $lineErrors[] = "Kategori Kas kosong";
            if (empty($row[2])) $lineErrors[] = "Jenis Transaksi kosong";
            if (empty($row[3]) || strlen($row[3]) < 5) $lineErrors[] = "Keterangan minimal 5 karakter";
            if (empty($row[4]) && $row[4] !== "0") $lineErrors[] = "Nominal kosong";
            if (empty($row[6])) $lineErrors[] = "Detail Alokasi kosong";

            if (!empty($lineErrors)) {
                $importErrors[] = "Baris {$rowNumber}: " . implode(', ', $lineErrors);
                continue;
            }

            // 3. Konversi Tanggal
            $tanggal = \DateTime::createFromFormat('m/d/Y', $row[0]);

            if (!$tanggal) {
                $tanggal = \DateTime::createFromFormat('d/m/Y', $row[0]);
            }

            if (!$tanggal) {
                $importErrors[] = "Baris {$rowNumber}: Format tanggal '{$row[0]}' tidak dikenali.";
                continue;
            }

            // Reset waktu agar perbandingan murni tanggal (mencegah error 'melebihi hari ini')
            $tanggal->setTime(0, 0, 0); 
            $finalDate = $tanggal->format('Y-m-d H:i:s');

            $todayObj = new \DateTime();
            $todayObj->setTime(0, 0, 0);

            if ($tanggal > $todayObj) {
                $importErrors[] = "Baris {$rowNumber}: Tanggal '{$row[0]}' tidak boleh melebihi hari ini.";
                continue;
            }
            
            // --- LOGIKA NOMINAL BARU (LEBIH FLEXIBLE) ---
            $nominalRaw = (string)$row[4];
            
            // 1. Cek apakah ada koma yang berfungsi sebagai desimal (format Indo: 31.111,00)
            if (strpos($nominalRaw, ',') !== false) {
                $parts = explode(',', $nominalRaw);
                if (isset($parts[1]) && strlen(trim($parts[1])) <= 2) {
                    $nominalRaw = $parts[0];
                }
            }
            
            // 2. Hapus semua karakter yang BUKAN angka.
            $cleanNominal = preg_replace('/[^0-9]/', '', $nominalRaw);

            $jenis = strtolower($row[2]);

            $kat = $this->kategoriModel->where('kategori', $row[1])->first();
            $det = $this->detailAlokasiModel->where('detail_alokasi', $row[6])->first();

            if (!$kat) { $importErrors[] = "Baris {$rowNumber}: Kategori '{$row[1]}' tidak ada"; continue; }
            if (!$det) { $importErrors[] = "Baris {$rowNumber}: Detail Alokasi '{$row[6]}' tidak ada"; continue; }

            // 4. Cek Duplikasi
            $isExist = $this->keuanganModel->where([
                'id_kategori_keuangan' => $kat['id_kategori_keuangan'],
                'tanggal'              => $finalDate,
                'jumlah'               => $cleanNominal,
                'jenis'                => $jenis,
                'keterangan'           => $row[3],
            ])->where('created_at >=', $startOfWeek)
            ->where('created_at <=', $endOfWeek)
            ->countAllResults();

            if ($isExist > 0) {
                $skippedCount++;
                continue; 
            }

            $dataToInsert[] = [
                'id_kategori_keuangan' => $kat['id_kategori_keuangan'],
                'id_detail_alokasi'    => $det['id_detail_alokasi'],
                'tanggal'              => $finalDate,
                'jenis'                => $jenis,
                'keterangan'           => $row[3],
                'jumlah'               => $cleanNominal,
                'pic'                  => $row[7] ?? null,
                'method_input'         => 'import',
                'created_by'           => session()->get('id_akun'),
            ];
        }

        // 5. Eksekusi
        if (!empty($importErrors)) {
            return redirect()->to('admin/finance/routine')->with('error_list', $importErrors);
        }

        if (!empty($dataToInsert)) {
            $nowTime = date('Y-m-d H:i:s');
            $this->_ensureReportExists($nowTime);

            $this->keuanganModel->insertBatch($dataToInsert);
            
            $msg = count($dataToInsert) . " transaksi baru berhasil diimport.";
            if ($skippedCount > 0) $msg .= " ({$skippedCount} data lama dilewati).";
            
            // dd($dataToInsert);
            return redirect()->to('admin/finance/routine')->with('success', $msg);
        }

        return redirect()->to('admin/finance/routine')->with('error', 'Tidak ada transaksi baru yang diimport. Data mungkin sudah terdaftar di sistem pada pekan ini.');
    }
}
