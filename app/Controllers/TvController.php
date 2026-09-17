<?php

namespace App\Controllers;

use App\Models\CarouselModel;
use App\Models\KeuanganModel;
use App\Models\AgendaModel;
use App\Models\SdmAgendaModel;
use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;

class TvController extends BaseController
{
    protected $timezone = 'Asia/Jakarta';

    protected $latitude  = -6.190834662826311;
    protected $longitude = 106.80125993207903;

    public function index()
    {
        $carouselModel = new CarouselModel();
        $keuanganModel = new KeuanganModel();
        $agendaModel   = new AgendaModel();
        $sdmAgendaModel = new SdmAgendaModel();

        $timezone = new \DateTimeZone($this->timezone);

        $now = new \DateTimeImmutable('now', $timezone);

        /*
         * ==========================================================
         * JADWAL SHALAT
         * ==========================================================
         */

        $pt = new PrayerTimes(
            Method::METHOD_SINGAPORE
        );

        $times = $pt->getTimesForToday(
            $this->latitude,
            $this->longitude,
            $this->timezone
        );

        $date = $now->format('Y-m-d');

        $sholat = [
            'date'     => $date,
            'hijriah'  => format_hijriah($date),

            'imsak'    => $times['Imsak'] ?? null,
            'subuh'    => $times['Fajr'] ?? null,
            'terbit'   => $times['Sunrise'] ?? null,
            'dzuhur'   => $times['Dhuhr'] ?? null,
            // 'ashar'    => $times['Asr'] ?? null,
            'ashar'    => '14:44',
            'maghrib'  => $times['Maghrib'] ?? null,
            'isya'     => $times['Isha'] ?? null,
        ];


        /*
         * ==========================================================
         * AGENDA HARI INI
         * ==========================================================
         */

        $start = $now->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $end   = $now->setTime(23, 59, 59)->format('Y-m-d H:i:s');

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
            ->where('waktu_mulai >=', $start)
            ->where('waktu_mulai <=', $end)
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
                ->where(
                    'id_agenda',
                    $agenda['id_agenda']
                )
                ->findAll();

            $agenda['pengisi'] = $pengisi;
        }

        unset($agenda);


        /*
         * ==========================================================
         * CAROUSEL
         * ==========================================================
         */

        $carousels = $carouselModel->getActiveCarousel();


        /*
         * ==========================================================
         * KEUANGAN
         * ==========================================================
         */

        $finance = $keuanganModel->getSummaryPerKategori(false);

        /*
        * ==========================================================
        * JADWAL SHALAT BESOK (untuk countdown lintas hari malam ini)
        * ==========================================================
        */

        $tomorrow = $now->modify('+1 day');

        // getTimes() butuh objek \DateTime (bukan DateTimeImmutable),
        // dan tidak menerima $timezone terpisah — timezone diambil dari
        // objek DateTime itu sendiri.
        $tomorrowDate = new \DateTime(
            $tomorrow->format('Y-m-d'),
            $timezone
        );

        $timesBesok = $pt->getTimes(
            $tomorrowDate,
            $this->latitude,
            $this->longitude
        );

        $sholatBesok = [
            'subuh' => $timesBesok['Fajr'] ?? null,
        ];

        /*
        * ==========================================================
        * QR DONASI
        * ==========================================================
        */

        $donasi = [
            'bank'       => 'BSI',
            'norek'      => '123 456 7890',
            'nama'       => 'Masjid Al-Manaar',
            'keterangan' => 'Scan untuk berdonasi',
        ];


        /*
         * ==========================================================
         * DATA VIEW
         * ==========================================================
         */

        $data = [
            'title' => 'TV Display | SIM Al-Manaar',

            'serverTime' => $now->format('Y-m-d H:i:s'),

            'sholat' => $sholat,

            'sholatBesok' => $sholatBesok,

            'agendas' => $agendas,

            'carousels' => $carousels,

            'finance' => $finance,

            'donasi' => $donasi,

            'iqamah' => [
                            'Subuh'   => 10,
                            'Dzuhur'  => 10,
                            'Ashar'   => 1,
                            'Maghrib' => 10,
                            'Isya'    => 10,
                        ],
            
            // =========================================================
            // AYAT AL-QUR'AN & HADIS
            // =========================================================
            'ayatHadis' => [
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
                [
                    'arab'   => 'إِنَّمَا الْأَعْمَالُ بِالنِّيَّاتِ',
                    'text'   => 'Sesungguhnya setiap amalan tergantung pada niatnya.',
                    'source' => 'HR. Bukhari dan Muslim',
                ],
                [
                    'arab'   => 'خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ',
                    'text'   => 'Sebaik-baik kalian adalah orang yang mempelajari Al-Qur’an dan mengajarkannya.',
                    'source' => 'HR. Bukhari',
                ],
            ],
            'pengumuman' => [
                'Selamat datang di Masjid Al-Manaar Slipi.',
                'Harap matikan atau silent handphone selama berada di dalam masjid.',
                'Mari menjaga kebersihan dan ketertiban lingkungan masjid.',
                'Shalat berjamaah tepat waktu, insyaAllah mendapatkan keberkahan.',
            ],
        ];

        // dd($data);

        return view('tv/index', $data);
    }
}