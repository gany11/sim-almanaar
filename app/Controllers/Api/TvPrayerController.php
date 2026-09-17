<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;

class TvPrayerController extends BaseController
{
    private float $latitude = -6.190834662826311;
    private float $longitude = 106.80125993207903;
    private string $timezone = 'Asia/Jakarta';

    public function index()
    {
        $request = $this->request;

        $month = (int) ($request->getGet('month') ?? date('m'));
        $year  = (int) ($request->getGet('year') ?? date('Y'));

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

            /*
             * Untuk sementara gunakan kalkulasi harian.
             * Nanti kita sesuaikan dengan method library
             * jika tersedia fungsi getTimesForDate().
             */
            $times = $pt->getTimesForToday(
                $this->latitude,
                $this->longitude,
                $this->timezone
            );

            $jadwal[] = [
                'date' => $date->format('Y-m-d'),
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
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'data' => $jadwal,
        ]);
    }
}