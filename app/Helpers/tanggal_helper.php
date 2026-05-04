<?php

use App\Models\KhgtModel;


if (!function_exists('format_indo')) {
    function format_indo($date, $style = 'full')
    {
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') return '-';

        $hariArr = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanArr = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($date);
        $hari      = $hariArr[date('w', $timestamp)];
        $tgl       = date('d', $timestamp);
        $bln_num   = date('m', $timestamp);
        $bln_name  = $bulanArr[(int)date('m', $timestamp)];
        $thn       = date('Y', $timestamp);
        $waktu     = date('H:i', $timestamp);

        switch ($style) {
            case 'full':
                return "$hari, $tgl $bln_name $thn";
            case 'full_datetime':
                return "$hari, $tgl $bln_name $thn ($waktu)";
            case 'datetime':
                return "$tgl $bln_name $thn $waktu";
            case 'slash_day':
                return "$hari, $tgl / $bln_num / $thn";
            case 'slash':
                return "$tgl / $bln_num / $thn";
            case 'time':
                return $waktu;
            default:
                return "$tgl $bln_name $thn";
        }
    }
}

if (!function_exists('format_hijriah')) {
    /**
     * Mengambil tanggal Hijriah dari tabel KHGT menggunakan Model
     */
    function format_hijriah($date)
    {
        // 1. Validasi input: jika kosong atau tanggal nol, langsung return strip
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return '-';
        }

        // 2. Normalisasi format masehi (ambil Y-m-d saja)
        $masehi = date('Y-m-d', strtotime($date));

        // 3. Panggil Model Khgt
        $khgtModel = new KhgtModel();
        $data = $khgtModel->where('masehi', $masehi)->first();

        // 4. Jika data tidak ditemukan di tabel KHGT, return strip
        if (!$data) {
            return '-';
        }

        // 5. Olah data hijriah (Asumsi format di DB: YYYY-MM-DD)
        $parts = explode('-', $data['hijriah']);
        if (count($parts) !== 3) {
            return $data['hijriah'];
        }

        $thn = $parts[0];
        $bln = (int)$parts[1];
        $tgl = (int)$parts[2];

        $bulanHijriah = [
            1 => "Muharram", "Safar", "Rabi'ul Awal", "Rabi'ul Akhir",
            "Jumadil Awal", "Jumadil Akhir", "Rajab", "Sya'ban",
            "Ramadhan", "Syawal", "Dzulqa'dah", "Dzulhijjah"
        ];

        // 6. Return hasil akhir
        return $tgl . ' ' . $bulanHijriah[$bln] . ' ' . $thn . ' H';
    }
}