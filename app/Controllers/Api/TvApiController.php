<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CarouselModel;
use App\Models\KeuanganModel;
use App\Models\AgendaModel;
use App\Models\SdmAgendaModel;
use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;

class TvApiController extends BaseController
{
    private float $latitude = -6.190834662826311;
    private float $longitude = 106.80125993207903;
    private string $timezone = 'Asia/Jakarta';

    /**
     * Get Jadwal Sholat 1 Bulan Penuh (Support Custom Lat & Long)
     */
    public function getPrayer(){
        $request = $this->request;

        $month = (int) ($request->getGet('month') ?? date('m'));
        $year  = (int) ($request->getGet('year') ?? date('Y'));

        // Ambil parameter lat & long dari URL, gunakan nilai default jika kosong/tidak valid
        $latitude  = $request->getGet('lat') !== null ? (float) $request->getGet('lat') : $this->latitude;
        $longitude = $request->getGet('long') !== null ? (float) $request->getGet('long') : $this->longitude;

        if ($month < 1 || $month > 12) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Bulan tidak valid.'
                ]);
        }

        if ($year < 2020 || $year > 2100) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Tahun tidak valid.'
                ]);
        }

        $pt = new PrayerTimes(
            Method::METHOD_SINGAPORE
        );

        $daysInMonth = cal_days_in_month(
            CAL_GREGORIAN,
            $month,
            $year
        );

        $jadwal = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {

            $date = new \DateTimeImmutable(
                sprintf('%04d-%02d-%02d', $year, $month, $day),
                new \DateTimeZone($this->timezone)
            );

            $dateTimeObj = new \DateTime(
                $date->format('Y-m-d'),
                new \DateTimeZone($this->timezone)
            );

            // Menggunakan variabel $latitude dan $longitude yang dinamis
            $times = $pt->getTimes(
                $dateTimeObj,
                $latitude,
                $longitude
            );

            $jadwal[] = [
                'date' => $date->format('Y-m-d'),
                'date_format' => format_indo($date->format('Y-m-d')),
                'hijriah' => format_hijriah($date->format('Y-m-d')),
                'imsak' => $times['Imsak'] ?? null,
                'subuh' => $times['Fajr'] ?? null,
                'terbit' => $times['Sunrise'] ?? null,
                'dzuhur' => $times['Dhuhr'] ?? null,
                'ashar' => $times['Asr'] ?? null,
                'maghrib' => $times['Maghrib'] ?? null,
                'isya' => $times['Isha'] ?? null,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'timezone' => $this->timezone,
            'location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
            'data' => $jadwal,
        ]);
    }

    /**
     * Get Semua Agenda dalam Rentang Seminggu Kedepan (Digabung, Filter tanggal di sisi klien)
     */
    public function getJadwal(){
        $agendaModel    = new AgendaModel();
        $sdmAgendaModel = new SdmAgendaModel();
        $timezone       = new \DateTimeZone($this->timezone);
        $now            = new \DateTimeImmutable('now', $timezone);

        // Rentang waktu dari hari ini pukul 00:00:00 sampai 6 hari ke depan (total 7 hari) pukul 23:59:59
        $startWeek = $now->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $endWeek   = $now->modify('+6 days')->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        // Query database untuk rentang waktu seminggu ke depan
        $agendas = $agendaModel
            ->select(
                'agenda.*,
                kategori_agenda.nama_kategori,
                kategori_agenda.class_color,
                kw_mulai.keterangan as ket_mulai,
                kw_selesai.keterangan as ket_selesai'
            )
            ->join(
                'kategori_agenda',
                'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda'
            )
            ->join(
                'keterangan_waktu as kw_mulai',
                'kw_mulai.id_keterangan_waktu = agenda.id_keterangan_waktu_mulai',
                'left'
            )
            ->join(
                'keterangan_waktu as kw_selesai',
                'kw_selesai.id_keterangan_waktu = agenda.id_keterangan_waktu_selesai',
                'left'
            )
            ->where('waktu_mulai >=', $startWeek)
            ->where('waktu_mulai <=', $endWeek)
            ->where('agenda.deleted_at', null)
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        foreach ($agendas as &$agenda) {
            $pengisi = $sdmAgendaModel
                ->select('sdm.nama, kategori_sdm.kategori as peran')
                ->join(
                    'sdm',
                    'sdm.id_sdm = sdm_agenda.id_sdm'
                )
                ->join(
                    'kategori_sdm',
                    'kategori_sdm.id_kategori_sdm = sdm_agenda.id_kategori_sdm'
                )
                ->where('id_agenda', $agenda['id_agenda'])
                ->findAll();

            $agenda['pengisi'] = $pengisi;
            $agenda['waktu_mulai_format'] = format_indo($agenda['waktu_mulai'], 'full_datetime');
            $agenda['waktu_selesai_format'] = format_indo($agenda['waktu_selesai'], 'full_datetime');
        }
        unset($agenda);

        // Mengembalikan seluruh list agenda dalam satu array 'data'
        return $this->response->setJSON([
            'success'     => true,
            'server_time' => $now->format('Y-m-d H:i:s'),
            'data'        => $agendas,
        ]);
    }

    /**
     * Get Ringkasan Saldo / Keuangan dengan Tanggal Penghitungan Global
     */
    public function getSaldo(){
        $keuanganModel = new KeuanganModel();
        $finance = $keuanganModel->getSummaryPerKategori(false);

        $finance = array_map(function ($finance) {
            if (!empty($finance['tanggal_penghitungan'])) {
                $finance['tanggal_penghitungan_format'] = format_indo($finance['tanggal_penghitungan']);
            } else {
                $finance['tanggal_penghitungan_format'] = null;
            }

            if (!empty($finance['saldo'])) {
                $finance['saldo_format'] = 'Rp ' . number_format(
                    (float) ($finance['saldo'] ?? 0), 0, ',', '.'
                );
            } else {
                $finance['saldo_format'] = null;
            }
            
            return $finance;
        }, $finance);

        // Mengambil tanggal_penghitungan dari data pertama (jika ada), atau fallback ke tanggal hari ini
        $tanggalPenghitungan = !empty($finance[0]['tanggal_penghitungan']) 
            ? $finance[0]['tanggal_penghitungan'] 
            : date('Y-m-d');

        return $this->response->setJSON([
            'success'              => true,
            'tanggal_penghitungan' => $tanggalPenghitungan,
            'tanggal_penghitungan_format' => format_indo($tanggalPenghitungan),
            'data'                 => $finance,
        ]);
    }

    /**
     * Get Data Carousel Aktif dengan Full URL File
     */
    public function getCarousel(){
        $carouselModel = new CarouselModel();
        $carousels = $carouselModel->getActiveCarousel();

        $carousels = array_map(function ($carousel) {
            // Cek apakah file tidak kosong
            if (!empty($carousel['file'])) {
                // Menambahkan key baru 'file_url' atau menimpa 'file' sesuai kebutuhan
                $carousel['file_url'] = base_url('uploads/carousel/' . $carousel['file']);
            } else {
                $carousel['file_url'] = null;
            }
            
            return $carousel;
        }, $carousels);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $carousels,
        ]);
    }

    /**
     * Get Data Statis / Informasi Umum TV (Iqamah, Ayat/Hadis, Pengumuman, Donasi, dll)
     */
    public function getInfo(){
        $timezone = new \DateTimeZone($this->timezone);
        $now = new \DateTimeImmutable('now', $timezone);

        return $this->response->setJSON([
            'success'     => true,
            'server_time' => $now->format('Y-m-d H:i:s'),
            'data'        => [
                // Pilihan Tema menggunakan angka
                'theme' => 1,

                // Durasi jeda Iqamah (dalam menit)
                'iqamah' => [
                    'Subuh'   => 10,
                    'Dzuhur'  => 10,
                    'Ashar'   => 10,
                    'Maghrib' => 10,
                    'Isya'    => 10,
                ],

                // Daftar Ayat Al-Qur'an & Hadis
                'ayat_hadis' => [
                    [
                        'arab'   => 'إِنَّ مَعَ الْعُسْرِ يُسْرًا',
                        'text'   => 'Sesungguhnya bersama kesulitan ada kemudahan.',
                        'source' => 'QS. Al-Insyirah: 6',
                    ],
                    [
                        'arab'   => 'فَاذْكُرُونِي أَذْكُرْكُمْ',
                        'text'   => 'Maka ingatlah kepada-Ku, niscaya Aku akan mengingatmu.',
                        'source' => 'QS. Al-Baqarah: 152',
                    ],
                ],

                // Teks Pengumuman Biasa / Running Text
                'pengumuman' => [
                    'Selamat datang di Masjid Al-Manaar Slipi.',
                    'Harap matikan atau silent handphone selama berada di dalam masjid.',
                    'Mari menjaga kebersihan dan ketertiban lingkungan masjid.',
                ],

                // Pengumuman Khusus Berbasis Slider / Card
                'pengumuman_slider' => [
                    [
                        'title'   => 'Kajian Rutin Ba\'da Maghrib',
                        'content' => 'Malam ini kajian kitab Riyadhus Shalihin bersama Ustadz Dr. H. Ahmad, M.Ag.',
                        'category'=> 'Kajian Islam',
                    ],
                    [
                        'title'   => 'Laporan Kas Masjid',
                        'content' => 'Saldo kas infak minggu ini Rp 12.500.000.',
                        'category'=> 'Keuangan',
                    ],
                ],

                // Informasi Donasi (Mendukung Tipe Bank & Image/QR Code)
                'donasi' => [
                    [
                        'type'  => 'bank',
                        'title' => 'Rekening Operasional Masjid',
                        'bank'  => 'BSI',
                        'norek' => '123 456 7890',
                        'nama'  => 'Masjid Al-Manaar',
                    ],
                    [
                        'type'      => 'image',
                        'title'     => 'QRIS Donasi Digital',
                        'image_url' => base_url('uploads/qris_masjid.png'),
                    ],
                ],
            ],
        ]);
    }
}