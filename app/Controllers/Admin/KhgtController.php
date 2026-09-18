<?php

namespace App\Controllers\Admin;

use App\Models\KhgtModel;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KhgtController extends BaseController
{
    protected $khgtModel;

    public function __construct()
    {
        $this->khgtModel = new KhgtModel();
    }

    public function index()
    {
        return view('admin/khgt/v_index', [
            'title' => 'Manajemen Kalender Hijriah Global Tunggal (KHGT)'
        ]);
    }

    public function list()
    {
        // FullCalendar mengirimkan parameter 'start' dan 'end' secara otomatis
        $start = $this->request->getGet('start');
        $end   = $this->request->getGet('end');

        $builder = $this->khgtModel;

        if (!empty($start) && !empty($end)) {
            $builder->where('masehi >=', date('Y-m-d', strtotime($start)))
                    ->where('masehi <=', date('Y-m-d', strtotime($end)));
        }

        $khgtData = $builder->findAll();

        $bulanHijriah = [
            1 => "Muharram", "Safar", "Rabi'ul Awal", "Rabi'ul Akhir",
            "Jumadil Awal", "Jumadil Akhir", "Rajab", "Sya'ban",
            "Ramadhan", "Syawal", "Dzulqa'dah", "Dzulhijjah"
        ];

        $events = [];
        foreach ($khgtData as $row) {
            $tgl = (int) $row['hijriah_tanggal'];
            $bln = (int) $row['hijriah_bulan'];
            $thn = (int) $row['hijriah_tahun'];
            
            $namaBulan = $bulanHijriah[$bln] ?? '';
            $hijriahString = "{$tgl} {$namaBulan} {$thn} H";

            $masehiIndo = format_indo($row['masehi'], 'full'); 

            $events[] = [
                'id'          => $row['masehi'],
                'title'       => $hijriahString,
                'start'       => $row['masehi'],
                'allDay'      => true,
                'backgroundColor' => '#2563eb',
                'borderColor'     => '#2563eb',
                'extendedProps' => [
                    'masehi'        => $row['masehi'],
                    'masehi_indo'   => $masehiIndo,
                    'hijriah_tanggal' => $tgl,
                    'hijriah_bulan'   => $bln,
                    'hijriah_tahun'   => $thn
                ]
            ];
        }

        return $this->response->setJSON($events);
    }

    public function sync()
    {
        $domainApi = env('API_KHGT_DOMAIN');
        if (empty($domainApi)) {
            $errorMsg = 'Domain API KHGT belum dikonfigurasi di file .env (API_KHGT_DOMAIN).';
            log_message('error', '[KHGT Sync Error]: ' . $errorMsg);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMsg
            ])->setStatusCode(500);
        }

        $currentYear = date('Y');
        $apiUrl = rtrim($domainApi, '/') . "/api/calendar/{$currentYear}?type=masehi";

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($apiUrl, [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 45
            ]);

            if ($response->getStatusCode() !== 200) {
                $errorMsg = 'Gagal terhubung ke server API (Status: ' . $response->getStatusCode() . ')';
                log_message('error', '[KHGT Sync Error]: ' . $errorMsg . ' | URL: ' . $apiUrl);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ])->setStatusCode(500);
            }

            $body = json_decode($response->getBody(), true);
            
            // Sesuai format balasan baku RESTful API Anda
            if (!isset($body['success']) || $body['success'] !== true || empty($body['data'])) {
                $errorMsg = $body['message'] ?? 'Struktur data dari API tidak valid atau kosong.';
                log_message('error', '[KHGT Sync Error]: ' . $errorMsg);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }

            $monthsData = $body['data'];
            $db = \Config\Database::connect();
            $db->transStart();

            $syncedCount = 0;
            $skippedCount = 0;
            $userId = session()->get('id_akun');

            $mappingBulanHijriah = [
                "muharam"       => 1,
                "safar"         => 2,
                "rabiul awal"   => 3,
                "rabi'ul awal"  => 3,
                "rabiul akhir"  => 4,
                "rabi'ul akhir" => 4,
                "jumadil awal"  => 5,
                "jumadil akhir" => 6,
                "rajab"         => 7,
                "syaban"        => 8,
                "sya'ban"       => 8,
                "ramadhan"      => 9,
                "syawal"        => 10,
                "zulkaidah"     => 11,
                "dzulqa'dah"    => 11,
                "zulhijah"      => 12,
                "dzulhijjah"    => 12
            ];

            foreach ($monthsData as $monthBlock) {
                $hijriYear = $monthBlock['hijri_year'] ?? null;
                $daysArray = $monthBlock['days'] ?? [];

                if (empty($daysArray) || !is_array($daysArray)) {
                    continue;
                }

                foreach ($daysArray as $day) {
                    $gYear   = (int) ($day['gregorian_year'] ?? 0);
                    // Karena gregorian_month mulai dari index 0 (0 = Januari), maka perlu ditambah 1
                    $gMonth  = (int) ($day['gregorian_month'] ?? 0) + 1; 
                    $gDate   = (int) ($day['gregorian_date'] ?? 0);
                    $tglHijri = $day['hijri_date'] ?? null;

                    if (!$gYear || !$gDate || !$tglHijri || !$hijriYear) {
                        continue;
                    }

                    // Validasi tanggal masehi untuk mencegah error tanggal fiktif (misal: 31 November)
                    if (!checkdate($gMonth, $gDate, $gYear)) {
                        log_message('warning', "[KHGT Sync]: Tanggal Masehi tidak valid dari API -> Tahun: {$gYear}, Bulan: {$gMonth}, Hari: {$gDate}");
                        continue; 
                    }

                    $masehi = sprintf('%04d-%02d-%02d', $gYear, $gMonth, $gDate);

                    $namaBulanString = strtolower(trim($monthBlock['hijri_month'] ?? $monthBlock['hijri_month_name'] ?? ''));
                    $blnHijriah = $mappingBulanHijriah[$namaBulanString] ?? null;

                    if (!$blnHijriah) {
                        continue; 
                    }

                    $thnHijriah = (int) $hijriYear;
                    $tglHijriah = (int) $tglHijri;

                    // Cek duplikat kombinasi Hijriah pada tanggal Masehi yang lain
                    $existsHijriah = $this->khgtModel
                        ->where('hijriah_tanggal', $tglHijriah)
                        ->where('hijriah_bulan', $blnHijriah)
                        ->where('hijriah_tahun', $thnHijriah)
                        ->where('masehi !=', $masehi)
                        ->first();

                    if ($existsHijriah) {
                        $skippedCount++;
                        continue; 
                    }

                    $existingMasehi = $this->khgtModel->find($masehi);

                    $data = [
                        'masehi'          => $masehi,
                        'hijriah_tanggal' => $tglHijriah,
                        'hijriah_bulan'   => $blnHijriah,
                        'hijriah_tahun'   => $thnHijriah,
                    ];

                    if ($existingMasehi) {
                        $data['updated_by'] = $userId;
                        $this->khgtModel->update($masehi, $data);
                    } else {
                        $data['created_by'] = $userId;
                        $this->khgtModel->insert($data);
                    }

                    $syncedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                log_message('error', '[KHGT DB Transaction Error]: ' . json_encode($dbError));

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data sinkronisasi ke database. Periksa log sistem.'
                ])->setStatusCode(500);
            }

            $message = "Berhasil menyinkronkan $syncedCount data.";
            if ($skippedCount > 0) {
                $message .= " ($skippedCount data dilewati karena duplikat Hijriah).";
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', '[KHGT Sync Exception]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}