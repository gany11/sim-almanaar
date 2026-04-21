<?php

namespace App\Controllers;

use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;

class SholatController extends BaseController
{
    public function index()
    {
        // Koordinat (Contoh: Jakarta Barat)
        $latitude  = -6.190834662826311;
        $longitude = 106.80125993207903;
        $timezone  = 'Asia/Jakarta'; // Pastikan format string ini agar tidak error timezone(7)

        // Kalkulasi menggunakan kriteria yang mirip Kemenag RI
        $pt = new PrayerTimes(Method::METHOD_SINGAPORE);
        $times = $pt->getTimesForToday($latitude, $longitude, $timezone);

        $data = [
            'tanggal' => date('d F Y'),
            'jadwal'  => $times
        ];

        return view('sholat_view', $data);
    }
}