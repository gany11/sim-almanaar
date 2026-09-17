<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($title ?? 'TV Display | SIM Al-Manaar') ?></title>

    <link
        rel="icon"
        type="image/x-icon"
        href="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>">

    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <?= vite('public/js/landing.js') ?>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #f8fafc;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            color: #0f172a;
        }

        /* =========================================================
           ROOT GRID
           rows: [konten utama] [bar jadwal sholat] [ticker]
        ========================================================= */

        .tv-container {
            width: 100vw;
            height: 100vh;

            display: grid;
            grid-template-rows:
                minmax(0, 1fr)
                auto
                58px;

            background: #f8fafc;
        }

        /* =========================================================
           BARIS KONTEN UTAMA (70% / 30%)
        ========================================================= */

        .tv-main {
            min-width: 0;
            min-height: 0;

            display: grid;
            grid-template-columns: 70% 30%;
            gap: 0;
        }

        /* =========================================================
           KOLOM KIRI (70%)
        ========================================================= */

        .tv-left {
            min-width: 0;
            min-height: 0;

            display: grid;

            grid-template-rows:
                76px
                minmax(0, 1fr)
                168px;

            gap: 12px;

            padding: 18px;
            padding-right: 9px;
        }

        .tv-masjid-header {
            min-height: 92px;

            display: flex;
            align-items: center;

            gap: 16px;
            padding: 8px 14px 12px;
        }

        .tv-masjid-logo {
            width: 64px;
            height: 64px;

            object-fit: contain;
            flex-shrink: 0;
        }

        .tv-masjid-info {
            min-width: 0;
            flex: 1;
        }

        .tv-masjid-info h1 {
            margin: 0;

            font-size: clamp(24px, 2vw, 34px);
            font-weight: 800;
            line-height: 1.1;

            color: #1e293b;
        }

        .tv-masjid-info p {
            margin: 5px 0 0;

            font-size: clamp(12px, 0.9vw, 17px);
            font-weight: 500;

            color: #64748b;
        }

        .tv-date-info {
            flex-shrink: 0;

            min-width: 260px;

            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;

            padding-left: 20px;

            border-left: 1px solid #e2e8f0;
        }

        .tv-header-date {
            font-size: clamp(16px, 1.25vw, 24px);
            font-weight: 800;
            line-height: 1.2;

            color: #334155;

            white-space: nowrap;
        }

        .tv-header-hijriah {
            margin-top: 5px;

            font-size: clamp(14px, 1.05vw, 20px);
            font-weight: 600;
            line-height: 1.2;

            color: #64748b;

            white-space: nowrap;
        }

        /* =========================================================
           HERO MEGA CAROUSEL
           (Hero gambar → Agenda(2 kolom) → Saldo(2x2), 1 carousel)
        ========================================================= */

        .tv-hero {
            position: relative;
            min-width: 0;
            min-height: 0;
            overflow: hidden;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .tv-hero-swiper {
            width: 100%;
            height: 100%;
        }

        .tv-hero-swiper .swiper-wrapper,
        .tv-hero-swiper .swiper-slide {
            width: 100%;
            height: 100%;
        }

        /* ---- slide: gambar ---- */

        .tv-hero-slide-image {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .tv-hero-slide-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ---- slide: agenda (grid 2 kolom) ---- */

        .tv-hero-slide-agenda {
            width: 100%;
            height: 100%;

            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;

            padding: 30px;
        }

        .tv-hero-agenda-card {
            min-width: 0;
            min-height: 0;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 24px;

            border-radius: 18px;

            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .tv-hero-agenda-card.tv-span-2 {
            grid-column: span 2;
        }

        .tv-hero-agenda-card-category {
            display: inline-block;

            align-self: flex-start;

            margin-bottom: 12px;

            padding: 5px 14px;

            border-radius: 999px;

            background: #eff6ff;
            color: #2563eb;

            font-size: 13px;
            font-weight: 800;
        }

        .tv-hero-agenda-card-title {
            margin: 0 0 12px;

            font-size: clamp(19px, 1.5vw, 27px);
            font-weight: 800;
            line-height: 1.25;

            color: #0f172a;

            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
            overflow-wrap: anywhere;
        }

        .tv-hero-agenda-card-time {
            display: flex;
            align-items: center;
            gap: 6px;

            font-size: 15px;
            font-weight: 700;

            color: #334155;
        }

        .tv-hero-agenda-card-time-separator {
            color: #94a3b8;
            font-weight: 500;
        }

        .tv-hero-agenda-card-speaker {
            margin-top: 10px;

            font-size: 13px;
            line-height: 1.45;

            color: #475569;
        }

        .tv-hero-agenda-card-speaker > div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tv-hero-agenda-card-speaker strong {
            color: #0f172a;
        }

        .tv-hero-agenda-card-more-speaker {
            margin-top: 3px;

            font-size: 12px;
            font-weight: 600;

            color: #94a3b8;
        }

        .tv-hero-agenda-empty {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #94a3b8;
            font-size: 20px;
        }

        /* ---- slide: saldo (grid 2x2) ---- */

        .tv-hero-slide-finance {
            width: 100%;
            height: 100%;

            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 18px;

            padding: 30px;
        }

        .tv-hero-finance-card {
            min-width: 0;
            min-height: 0;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 18px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #ffffff
                );

            border: 1px solid #e2e8f0;
        }

        .tv-hero-finance-card-category {
            margin-bottom: 8px;

            font-size: clamp(14px, 1vw, 17px);
            font-weight: 700;

            color: #475569;
        }

        .tv-hero-finance-card-saldo {
            font-size: clamp(22px, 2.1vw, 34px);
            font-weight: 900;

            color: #0f172a;

            line-height: 1.15;

            word-break: break-word;
        }

        .tv-hero-finance-card-date {
            margin-top: 10px;

            font-size: clamp(10px, 0.75vw, 12px);
            color: #94a3b8;
        }

        .tv-hero-pagination {
            position: absolute !important;
            bottom: 12px !important;
            z-index: 10;
        }

        /* =========================================================
           IQAMAH OVERLAY (menutupi seluruh area hero)
        ========================================================= */

        .tv-iqamah {
            position: absolute;
            inset: 0;
            z-index: 20;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px;

            background: #ffffff;
        }

        .tv-iqamah.hidden {
            display: none;
        }

        .tv-iqamah-content {
            width: 100%;
            max-width: 720px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;
        }

        .tv-iqamah-label {
            margin-bottom: 8px;

            font-size: clamp(14px, 1.1vw, 22px);
            font-weight: 800;
            letter-spacing: 0.12em;

            color: #64748b;
        }

        .tv-iqamah-prayer {
            margin-bottom: 12px;

            font-size: clamp(42px, 5vw, 86px);
            font-weight: 900;
            line-height: 1;

            color: #1d4ed8;
        }

        .tv-iqamah-time {
            font-variant-numeric: tabular-nums;

            font-size: clamp(56px, 7vw, 120px);
            font-weight: 900;
            line-height: 1;

            letter-spacing: 0.04em;

            color: #0f172a;
        }

        .tv-iqamah-message {
            margin-top: 18px;

            font-size: clamp(13px, 1vw, 20px);
            font-weight: 500;

            color: #64748b;
        }

        /* =========================================================
           AYAT / HADIS
        ========================================================= */

        .tv-ayat {
            min-width: 0;
            min-height: 0;

            overflow: hidden;

            background: white;

            border: 1px solid #e2e8f0;
            border-radius: 22px;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .tv-ayat-swiper {
            width: 100%;
            height: 100%;
        }

        .tv-ayat-swiper .swiper-wrapper,
        .tv-ayat-swiper .swiper-slide {
            height: 100%;
        }

        .tv-ayat-slide {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;

            padding: 20px 28px;
        }

        .tv-ayat-inner {
            min-width: 0;
            width: 100%;
        }

        .tv-ayat-arab {
            margin: 0 0 8px;

            direction: rtl;

            font-family: 'Traditional Arabic', 'Scheherazade New', 'Amiri', serif;

            font-size: clamp(20px, 1.7vw, 30px);
            line-height: 1.9;

            color: #0f172a;

            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        .tv-ayat-text {
            margin: 0;

            font-size: clamp(14px, 1.05vw, 18px);
            font-weight: 500;
            line-height: 1.5;

            color: #334155;

            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        .tv-ayat-source {
            margin-top: 8px;

            font-size: 13px;
            font-weight: 700;

            color: #94a3b8;
        }

        .tv-ayat-empty {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #94a3b8;
            font-size: 15px;
        }

        /* =========================================================
           KOLOM KANAN (30%)
        ========================================================= */

        .tv-right {
            min-width: 0;
            min-height: 0;

            display: grid;

            grid-template-rows:
                minmax(0, 0.85fr)
                minmax(0, 1.15fr)
                minmax(0, 1fr);

            gap: 12px;

            padding: 18px;
            padding-left: 9px;
        }

        /* ---- Jam ---- */

        .tv-clock {
            min-width: 0;
            min-height: 0;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            padding: 16px;

            border-radius: 24px;

            background: #f8fafc;
            border: 1px solid #e2e8f0;

            color: #0f172a;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .tv-clock-time {
            font-size: clamp(48px, 5vw, 88px);
            font-weight: 900;

            letter-spacing: 0.02em;
            line-height: 0.95;

            font-variant-numeric: tabular-nums;

            color: #1d4ed8;
        }

        /* ---- QR Donasi ---- */

        .tv-qr {
            min-width: 0;
            min-height: 0;

            overflow: hidden;

            display: flex;
            flex-direction: column;

            background: white;

            border: 1px solid #e2e8f0;
            border-radius: 24px;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .tv-qr-header {
            padding: 14px 20px 10px;

            font-size: clamp(15px, 1vw, 20px);
            font-weight: 800;
            text-align: center;

            color: #0f172a;
        }

        .tv-qr-body {
            flex: 1;
            min-height: 0;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 8px 20px 18px;
        }

        .tv-qr-image-wrap {
            width: 100%;
            max-width: 220px;
            aspect-ratio: 1 / 1;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;

            overflow: hidden;
        }

        .tv-qr-image-wrap img {
            width: 100%;
            aspect-ratio: 1 / 1;
            height: auto;
            object-fit: contain;
        }

        .tv-qr-placeholder {
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
            padding: 0 16px;
        }

        .tv-qr-bank {
            margin-top: 4px;

            font-size: clamp(13px, 0.9vw, 16px);
            font-weight: 700;

            color: #1d4ed8;

            text-align: center;
        }

        .tv-qr-note {
            font-size: clamp(11px, 0.75vw, 13px);
            color: #64748b;

            text-align: center;
        }

        /* ---- Countdown ---- */

        .tv-countdown {
            min-width: 0;
            min-height: 0;

            display: flex;
            flex-direction: column;

            align-items: center;
            justify-content: center;

            padding: 18px;

            border-radius: 24px;

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #ffffff
                );

            border: 1px solid #bfdbfe;

            text-align: center;

            box-shadow:
                0 8px 24px rgba(37, 99, 235, 0.08);
        }

        .tv-countdown-label {
            font-size: clamp(12px, 0.9vw, 18px);

            font-weight: 700;

            color: #64748b;
        }

        .tv-countdown-prayer {
            margin-top: 5px;

            font-size: clamp(23px, 1.7vw, 34px);

            font-weight: 900;

            color: #1d4ed8;
        }

        .tv-countdown-status {
            margin-top: 5px;

            font-size: clamp(12px, 0.85vw, 16px);

            color: #475569;
        }

        .tv-countdown-time {
            margin-top: 12px;

            font-size: clamp(32px, 2.6vw, 52px);

            font-weight: 900;

            line-height: 1;

            letter-spacing: 0.04em;

            color: #0f172a;

            font-variant-numeric: tabular-nums;
        }

        /* =========================================================
           BAR JADWAL SHOLAT (FULL WIDTH, DIPERBAGUS)
        ========================================================= */

        .tv-prayer-bar-wrap {
            min-width: 0;

            padding: 0 18px 12px;
        }

        .tv-prayer-bar {
            min-width: 0;

            display: grid;
            grid-template-columns: repeat(7, 1fr);

            background: white;

            border: 1px solid #e2e8f0;
            border-radius: 20px;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);

            overflow: hidden;
        }

        .tv-prayer-bar-item {
            position: relative;

            min-width: 0;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 6px;

            padding: 14px 6px;

            border-right: 1px solid #f1f5f9;
        }

        .tv-prayer-bar-item:last-child {
            border-right: none;
        }

        .tv-prayer-bar-icon {
            width: 34px;
            height: 34px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 999px;

            background: #f1f5f9;
            color: #64748b;
        }

        .tv-prayer-bar-icon svg {
            width: 18px;
            height: 18px;
        }

        .tv-prayer-bar-name {
            font-size: clamp(12px, 0.85vw, 15px);
            font-weight: 700;

            color: #64748b;
        }

        .tv-prayer-bar-time {
            font-size: clamp(16px, 1.2vw, 25px);
            font-weight: 900;

            font-variant-numeric: tabular-nums;

            color: #0f172a;
        }

        .tv-prayer-bar-item.active {
            background: #eff6ff;
        }

        .tv-prayer-bar-item.active::before {
            content: '';

            position: absolute;
            top: 0;
            left: 0;
            right: 0;

            height: 4px;

            background: #2563eb;
        }

        .tv-prayer-bar-item.active .tv-prayer-bar-icon {
            background: #2563eb;
            color: #ffffff;
        }

        .tv-prayer-bar-item.active .tv-prayer-bar-name,
        .tv-prayer-bar-item.active .tv-prayer-bar-time {
            color: #1d4ed8;
        }

        /* =========================================================
           TICKER PENGUMUMAN (FULL WIDTH, PESAN [LOGO] PESAN [LOGO])
        ========================================================= */

        .tv-ticker {
            min-width: 0;
            width: 100%;

            display: flex;
            align-items: center;

            background: white;

            border-top: 1px solid #e2e8f0;

            overflow: hidden;
        }

        .tv-ticker-track {
            display: flex;
            align-items: center;

            white-space: nowrap;

            will-change: transform;

            animation: tv-ticker-scroll linear infinite;
        }

        .tv-ticker-item {
            padding: 0 24px;

            font-size: 15px;
            font-weight: 600;

            color: #1e293b;
        }

        .tv-ticker-sep {
            width: 24px;
            height: 24px;

            object-fit: contain;

            flex-shrink: 0;

            opacity: 0.85;
        }

        @keyframes tv-ticker-scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1200px) {
            .tv-main {
                grid-template-columns: 68% 32%;
            }

            .tv-left {
                grid-template-rows: 64px minmax(0, 1fr) 150px;
            }
        }
    </style>
</head>

<body>

    <?php
    /*
    |--------------------------------------------------------------------------
    | Helper data
    |--------------------------------------------------------------------------
    */

    $sholat = $sholat ?? [];

    $prayerList = [
        'Imsak'   => $sholat['imsak'] ?? null,
        'Subuh'   => $sholat['subuh'] ?? null,
        'Terbit'  => $sholat['terbit'] ?? null,
        'Dzuhur'  => $sholat['dzuhur'] ?? null,
        'Ashar'   => $sholat['ashar'] ?? null,
        'Maghrib' => $sholat['maghrib'] ?? null,
        'Isya'    => $sholat['isya'] ?? null,
    ];

    /*
    |--------------------------------------------------------------------------
    | Ikon untuk setiap waktu sholat (lucide icon names)
    |--------------------------------------------------------------------------
    */

    $prayerIcons = [
        'Imsak'   => 'moon-star',
        'Subuh'   => 'sunrise',
        'Terbit'  => 'sun',
        'Dzuhur'  => 'sun',
        'Ashar'   => 'cloud-sun',
        'Maghrib' => 'sunset',
        'Isya'    => 'moon',
    ];

    /*
    |--------------------------------------------------------------------------
    | Data baru (donasi, ayat/hadis, pengumuman)
    |--------------------------------------------------------------------------
    |
    | Belum ada di controller lama, sehingga diberi fallback aman
    | agar view tidak error selama backend belum mengirim datanya.
    |
    | $donasi = [
    |     'qr_image'   => 'nama-file-qr.png', // relatif ke uploads/donasi/
    |     'bank'       => 'BSI 123 456 7890 a.n. Masjid Al-Manaar',
    |     'keterangan' => 'Scan untuk berdonasi',
    | ];
    |
    | $ayatHadis = [
    |     [
    |         'type'   => 'ayat', // atau 'hadis'
    |         'arab'   => '...',
    |         'text'   => 'terjemahan / isi hadis',
    |         'source' => 'QS. Al-Baqarah: 183',
    |     ],
    |     ...
    | ];
    |
    | $pengumuman = [
    |     'Kajian rutin ba\'da Maghrib bersama Ust. ...',
    |     'Info donasi renovasi masjid ...',
    | ];
    |
    */

    $donasi     = $donasi ?? [];
    $ayatHadis  = $ayatHadis ?? [];
    $pengumuman = $pengumuman ?? [];

    /*
    |--------------------------------------------------------------------------
    | Subuh BESOK (untuk countdown lintas hari)
    |--------------------------------------------------------------------------
    |
    | $sholat hanya berisi jadwal HARI INI. Begitu waktu Isya lewat,
    | target countdown berikutnya adalah Subuh besok — dan jam Subuh
    | bisa berbeda beberapa menit dari Subuh hari ini.
    |
    | Idealnya controller juga mengirim jadwal besok, contoh:
    |
    |   $sholatBesok = [
    |       'subuh' => '04:29',
    |       ...
    |   ];
    |
    | Jika $sholatBesok belum dikirim, kita fallback memakai jam Subuh
    | hari ini sebagai PERKIRAAN (selisihnya biasanya cuma 1-2 menit,
    | tapi sebaiknya tetap dilengkapi di controller untuk akurasi penuh).
    */

    $sholatBesok = $sholatBesok ?? [];
    $subuhBesok  = $sholatBesok['subuh'] ?? ($sholat['subuh'] ?? null);

    if (empty($pengumuman)) {
        $pengumuman = [
            'Selamat datang di Masjid Al-Manaar Slipi.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Kelompokkan agenda (2 per slide) & saldo (4 per slide, grid 2x2)
    |--------------------------------------------------------------------------
    */

    $agendaChunks  = array_chunk($agendas ?? [], 2);
    $financeChunks = array_chunk($finance ?? [], 4);
    ?>

    <div class="tv-container">

        <!-- =====================================================
             BARIS KONTEN UTAMA
        ====================================================== -->

        <div class="tv-main">

            <!-- =================================================
                 KOLOM KIRI (70%)
            ================================================== -->

            <main class="tv-left">

                <header class="tv-masjid-header">

                    <img
                        src="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>"
                        alt="Logo Masjid Al-Manaar Slipi"
                        class="tv-masjid-logo">

                    <div class="tv-masjid-info">

                        <h1>Masjid Al-Manaar Slipi</h1>

                        <p>
                            <?= esc($alamatMasjid ?? 'Slipi, Jakarta Barat') ?>
                        </p>

                    </div>

                    <div class="tv-date-info">

                        <div
                            id="tv-header-date"
                            class="tv-header-date">
                            -
                        </div>

                        <div
                            id="tv-header-hijriah"
                            class="tv-header-hijriah">
                            <?= esc($sholat['hijriah'] ?? '-') ?>
                        </div>

                    </div>

                </header>

                <!-- =============================================
                     HERO MEGA CAROUSEL (Hero → Agenda → Saldo)
                ============================================== -->

                <section class="tv-hero">

                    <div
                        id="tv-hero-carousel"
                        class="swiper tv-hero-swiper">

                        <div class="swiper-wrapper">

                            <!-- ============ SLIDE: HERO GAMBAR ============ -->

                            <?php foreach (($carousels ?? []) as $carousel): ?>

                                <div class="swiper-slide">
                                    <div class="tv-hero-slide-image">

                                        <img
                                            src="<?= base_url('uploads/carousel/' . $carousel['file']) ?>"
                                            alt="">

                                    </div>
                                </div>

                            <?php endforeach; ?>


                            <!-- ============ SLIDE: AGENDA (2 kolom / slide) ============ -->

                            <?php foreach ($agendaChunks as $chunk): ?>

                                <div class="swiper-slide">
                                    <div class="tv-hero-slide-agenda">

                                        <?php foreach ($chunk as $agenda): ?>

                                            <?php
                                            $kategoriId = (int) (
                                                $agenda['id_kategori_agenda'] ?? 0
                                            );

                                            $kategori = trim(
                                                $agenda['nama_kategori'] ?? ''
                                            );

                                            $judul = trim(
                                                $agenda['judul'] ?? ''
                                            );

                                            $tema = trim(
                                                $agenda['tema'] ?? ''
                                            );

                                            if ($judul !== '') {

                                                $agendaTitle = $judul;

                                            } elseif (
                                                $tema !== '' &&
                                                $tema !== '-' &&
                                                mb_strlen($tema) > 3
                                            ) {

                                                $agendaTitle = $tema;

                                            } else {

                                                $agendaTitle = '-';

                                            }

                                            $mulai = !empty($agenda['waktu_mulai'])
                                                ? date(
                                                    'H:i',
                                                    strtotime($agenda['waktu_mulai'])
                                                )
                                                : null;

                                            $selesai = !empty($agenda['waktu_selesai'])
                                                ? date(
                                                    'H:i',
                                                    strtotime($agenda['waktu_selesai'])
                                                )
                                                : null;

                                            $ketMulai = trim(
                                                $agenda['ket_mulai'] ?? ''
                                            );

                                            $ketSelesai = trim(
                                                $agenda['ket_selesai'] ?? ''
                                            );

                                            $waktuMulaiDisplay   = $ketMulai ?: $mulai;
                                            $waktuSelesaiDisplay = $ketSelesai ?: $selesai;

                                            $pengisi = $agenda['pengisi'] ?? [];

                                            $jumlahPengisi = count($pengisi);

                                            $pengisiTampil = array_slice(
                                                $pengisi,
                                                0,
                                                2
                                            );

                                            $sisaPengisi = max(
                                                0,
                                                $jumlahPengisi - 2
                                            );

                                            /*
                                            * Jika dalam 1 slide cuma ada 1 agenda
                                            * (sisa ganjil), lebarkan kartu jadi
                                            * penuh 2 kolom supaya tetap seimbang.
                                            */

                                            $cardSpanClass = (
                                                count($chunk) === 1
                                            ) ? ' tv-span-2' : '';
                                            ?>

                                            <article class="tv-hero-agenda-card<?= $cardSpanClass ?>">

                                                <span class="tv-hero-agenda-card-category">
                                                    <?= esc(
                                                        $kategori !== ''
                                                            ? $kategori
                                                            : 'Agenda'
                                                    ) ?>
                                                </span>

                                                <h3 class="tv-hero-agenda-card-title">
                                                    <?= esc($agendaTitle) ?>
                                                </h3>

                                                <?php if (
                                                    $kategoriId !== 1 &&
                                                    ($waktuMulaiDisplay || $waktuSelesaiDisplay)
                                                ): ?>

                                                    <div class="tv-hero-agenda-card-time">

                                                        <?php if ($waktuMulaiDisplay): ?>
                                                            <?= esc($waktuMulaiDisplay) ?>
                                                        <?php endif; ?>

                                                        <?php if (
                                                            $waktuMulaiDisplay &&
                                                            $waktuSelesaiDisplay
                                                        ): ?>
                                                            <span class="tv-hero-agenda-card-time-separator">—</span>
                                                        <?php endif; ?>

                                                        <?php if ($waktuSelesaiDisplay): ?>
                                                            <?= esc($waktuSelesaiDisplay) ?>
                                                        <?php endif; ?>

                                                    </div>

                                                <?php endif; ?>

                                                <?php if (!empty($pengisiTampil)): ?>

                                                    <div class="tv-hero-agenda-card-speaker">

                                                        <?php foreach ($pengisiTampil as $pengisiItem): ?>

                                                            <div>

                                                                <?php if (!empty($pengisiItem['peran'])): ?>
                                                                    <strong><?= esc($pengisiItem['peran']) ?>:</strong>
                                                                <?php endif; ?>

                                                                <?= esc($pengisiItem['nama'] ?? '-') ?>

                                                            </div>

                                                        <?php endforeach; ?>

                                                        <?php if ($sisaPengisi > 0): ?>

                                                            <div class="tv-hero-agenda-card-more-speaker">
                                                                + <?= $sisaPengisi ?> pengisi lainnya
                                                            </div>

                                                        <?php endif; ?>

                                                    </div>

                                                <?php endif; ?>

                                            </article>

                                        <?php endforeach; ?>

                                    </div>
                                </div>

                            <?php endforeach; ?>

                            <?php if (empty($agendaChunks)): ?>

                                <div class="swiper-slide">
                                    <div class="tv-hero-agenda-empty">
                                        Tidak ada agenda hari ini.
                                    </div>
                                </div>

                            <?php endif; ?>


                            <!-- ============ SLIDE: SALDO (grid 2x2 / slide) ============ -->

                            <?php foreach ($financeChunks as $chunk): ?>

                                <div class="swiper-slide">
                                    <div class="tv-hero-slide-finance">

                                        <?php foreach ($chunk as $item): ?>

                                            <div class="tv-hero-finance-card">

                                                <div class="tv-hero-finance-card-category">
                                                    <?= esc($item['kategori'] ?? 'Keuangan') ?>
                                                </div>

                                                <div class="tv-hero-finance-card-saldo">
                                                    Rp <?= number_format(
                                                        (float) ($item['saldo'] ?? 0),
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) ?>
                                                </div>

                                                <?php if (!empty($item['tanggal_penghitungan'])): ?>

                                                    <div class="tv-hero-finance-card-date">
                                                        Data per
                                                        <?= esc(
                                                            format_indo(
                                                                $item['tanggal_penghitungan'],
                                                                'full_datetime'
                                                            )
                                                        ) ?>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>

                            <?php endforeach; ?>

                        </div>

                        <div class="swiper-pagination tv-hero-pagination"></div>

                    </div>


                    <!-- =========================
                        IQAMAH COUNTDOWN (overlay)
                    ========================== -->

                    <div
                        id="iqamah-countdown"
                        class="tv-iqamah hidden">

                        <div class="tv-iqamah-content">

                            <div class="tv-iqamah-label">
                                MENUJU IQAMAH
                            </div>

                            <div
                                id="iqamah-prayer"
                                class="tv-iqamah-prayer">
                                -
                            </div>

                            <div
                                id="iqamah-time"
                                class="tv-iqamah-time">
                                00:00
                            </div>

                            <div
                                id="iqamah-message"
                                class="tv-iqamah-message">
                                Silakan bersiap untuk melaksanakan sholat
                            </div>

                        </div>

                    </div>

                </section>


                <!-- =============================================
                     AYAT / HADIS
                ============================================== -->

                <section class="tv-ayat">

                    <div class="swiper tv-ayat-swiper">

                        <div class="swiper-wrapper">

                            <?php if (!empty($ayatHadis)): ?>

                                <?php foreach ($ayatHadis as $item): ?>

                                    <?php
                                    $tipe = strtolower($item['type'] ?? 'ayat');
                                    $tag  = $tipe === 'hadis' ? 'Hadis' : 'Ayat Al-Qur\'an';
                                    ?>

                                    <div class="swiper-slide">
                                        <div class="tv-ayat-slide">

                                            <div class="tv-ayat-inner">

                                                <?php if (!empty($item['arab'])): ?>

                                                    <p class="tv-ayat-arab">
                                                        <?= esc($item['arab']) ?>
                                                    </p>

                                                <?php endif; ?>

                                                <p class="tv-ayat-text">
                                                    <?= esc($item['text'] ?? '') ?>
                                                </p>

                                                <?php if (!empty($item['source'])): ?>

                                                    <div class="tv-ayat-source">
                                                        <?= esc($item['source']) ?>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="swiper-slide">
                                    <div class="tv-ayat-empty">
                                        Belum ada ayat/hadis yang ditambahkan.
                                    </div>
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </section>

            </main>


            <!-- =================================================
                 KOLOM KANAN (30%)
            ================================================== -->

            <aside class="tv-right">

                <!-- ============ JAM ============ -->

                <section class="tv-clock">

                    <div
                        id="tv-clock-time"
                        class="tv-clock-time">
                        --:--
                    </div>

                </section>


                <!-- ============ QR DONASI ============ -->

                <section class="tv-qr">

                    <div class="tv-qr-header">
                        QR Donasi
                    </div>

                    <div class="tv-qr-body">

                        <div class="tv-qr-image-wrap">

                            <?php if (!empty($donasi['qr_image'])): ?>

                                <img
                                    src="<?= base_url('uploads/donasi/' . $donasi['qr_image']) ?>"
                                    alt="QR Donasi">

                            <?php else: ?>

                                <div class="tv-qr-placeholder">
                                    QR belum tersedia
                                </div>

                            <?php endif; ?>

                        </div>

                        <?php if (!empty($donasi['bank'])): ?>

                            <div class="tv-qr-bank">
                                <?= esc($donasi['bank']) ?>
                            </div>

                        <?php endif; ?>

                        <div class="tv-qr-note">
                            <?= esc($donasi['keterangan'] ?? 'Scan untuk berdonasi') ?>
                        </div>

                    </div>

                </section>


                <!-- ============ COUNTDOWN ============ -->

                <section class="tv-countdown">

                    <div
                        id="tv-countdown-label"
                        class="tv-countdown-label">
                        Memuat...
                    </div>

                    <div
                        id="tv-countdown-prayer"
                        class="tv-countdown-prayer">
                        -
                    </div>

                    <div
                        id="tv-countdown-status"
                        class="tv-countdown-status">
                        -
                    </div>

                    <div
                        id="tv-countdown-time"
                        class="tv-countdown-time">
                        00:00:00
                    </div>

                </section>

            </aside>

        </div>


        <!-- =====================================================
             BAR JADWAL SHOLAT (FULL WIDTH)
        ====================================================== -->

        <div class="tv-prayer-bar-wrap">

            <div
                class="tv-prayer-bar"
                id="tv-prayer-bar">

                <?php foreach ($prayerList as $name => $time): ?>

                    <div
                        class="tv-prayer-bar-item"
                        data-prayer="<?= esc($name) ?>"
                        data-time="<?= esc($time ?? '') ?>">

                        <span class="tv-prayer-bar-icon">
                            <i data-lucide="<?= esc($prayerIcons[$name] ?? 'clock') ?>"></i>
                        </span>

                        <span class="tv-prayer-bar-name">
                            <?= esc($name) ?>
                        </span>

                        <span class="tv-prayer-bar-time">
                            <?= esc($time ?? '--:--') ?>
                        </span>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- =====================================================
             TICKER PENGUMUMAN (FULL WIDTH)
             Konten diisi lewat JS: "Pesan [logo] Pesan [logo] ..."
             agar selalu penuh selebar layar & scroll mulus.
        ====================================================== -->

        <div class="tv-ticker">

            <div
                id="tv-ticker-track"
                class="tv-ticker-track"
                data-logo="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>">
            </div>

        </div>

    </div>


    <!-- =========================================================
         DATA DARI SERVER
    ========================================================== -->

    <script>
        window.TV_DATA = <?= json_encode([
            'serverTime' => $serverTime,
            'sholat'     => $sholat,
            'iqamah'     => $iqamah,
            'agendas'    => $agendas,
            'carousels'  => $carousels,
            'finance'    => $finance,
            'donasi'     => $donasi,
            'ayatHadis'  => $ayatHadis,
            'pengumuman' => $pengumuman,
            'subuhBesok' => $subuhBesok,
        ]) ?>;
    </script>


    <!-- =========================================================
         TV CLOCK + COUNTDOWN + TICKER
    ========================================================== -->

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* =====================================================
               SERVER CLOCK
            ====================================================== */

            const tvServerTime = new Date(
                window.TV_DATA.serverTime.replace(' ', 'T')
            );

            const tvClientStart = Date.now();

            function getTvNow() {
                return new Date(
                    tvServerTime.getTime() +
                    (Date.now() - tvClientStart)
                );
            }

            /* =====================================================
            AUTO RELOAD SETIAP PERGANTIAN HARI
            ===================================================== */

            let lastReloadDate = getTvNow().toLocaleDateString('id-ID');

            function checkMidnightReload() {

                const now = getTvNow();

                const currentDate = now.toLocaleDateString('id-ID');

                if (currentDate !== lastReloadDate) {

                    lastReloadDate = currentDate;

                    window.location.reload();
                }
            }


            /* =====================================================
               ELEMENTS
            ====================================================== */

            const clockTime = document.getElementById(
                'tv-clock-time'
            );

            const clockDate = document.getElementById(
                'tv-header-date'
            );

            const countdownLabel = document.getElementById(
                'tv-countdown-label'
            );

            const countdownPrayer = document.getElementById(
                'tv-countdown-prayer'
            );

            const countdownStatus = document.getElementById(
                'tv-countdown-status'
            );

            const countdownTime = document.getElementById(
                'tv-countdown-time'
            );


            /* =====================================================
               PRAYER DATA
               ---------------------------------------------------
               PERBAIKAN BUG:
               Sebelumnya nama sholat dibandingkan dengan
               'Imsak' / 'Terbit' (huruf besar), padahal key asli
               di window.TV_DATA.sholat semuanya huruf kecil
               ('imsak', 'terbit', dst) dan juga berisi key lain
               yang bukan waktu sholat (mis. 'hijriah'). Akibatnya
               Imsak tidak pernah ter-exclude, dan field non-waktu
               ikut diproses sebagai "sholat" sehingga jam
               berikutnya jadi NaN/Invalid Date.

               Sekarang hanya key waktu sholat yang valid saja
               yang diproses, dan dicocokkan lewat pemetaan nama
               eksplisit + validasi format "HH:MM".
            ====================================================== */

            const PRAYER_NAME_MAP = {
                subuh: 'Subuh',
                terbit: 'Terbit',
                dzuhur: 'Dzuhur',
                ashar: 'Ashar',
                maghrib: 'Maghrib',
                isya: 'Isya'
            };

            const TIME_FORMAT = /^\d{1,2}:\d{2}/;

            const prayers = Object.entries(
                window.TV_DATA.sholat || {}
            )
                .filter(([key, time]) => {
                    return PRAYER_NAME_MAP[key] &&
                        typeof time === 'string' &&
                        TIME_FORMAT.test(time);
                })
                .map(([key, time]) => {

                    const [hour, minute] =
                        time.split(':').map(Number);

                    return {
                        name: PRAYER_NAME_MAP[key],
                        time,
                        hour,
                        minute
                    };

                });


            /*
             * Hanya sholat yang digunakan sebagai target countdown.
             * Terbit tidak menjadi target countdown (imsak juga
             * sudah otomatis tidak ikut karena tidak ada di
             * PRAYER_NAME_MAP).
             */

            const countdownPrayers = prayers.filter(
                prayer => prayer.name !== 'Terbit'
            );

            /* =========================================================
            IQAMAH COUNTDOWN
            ========================================================= */

            const iqamahElement = document.getElementById('iqamah-countdown');
            const iqamahPrayerElement = document.getElementById('iqamah-prayer');
            const iqamahTimeElement = document.getElementById('iqamah-time');

            const iqamahSettings = window.TV_DATA.iqamah || {};

            const iqamahPrayers = [
                'Subuh',
                'Dzuhur',
                'Ashar',
                'Maghrib',
                'Isya'
            ];


            /* =====================================================
               FORMAT
            ====================================================== */

            function pad(value) {
                return String(value).padStart(2, '0');
            }


            function formatCountdown(milliseconds) {

                if (milliseconds < 0) {
                    milliseconds = 0;
                }

                const totalSeconds = Math.floor(
                    milliseconds / 1000
                );

                const hours = Math.floor(
                    totalSeconds / 3600
                );

                const minutes = Math.floor(
                    (totalSeconds % 3600) / 60
                );

                const seconds = totalSeconds % 60;

                return (
                    pad(hours) +
                    ':' +
                    pad(minutes) +
                    ':' +
                    pad(seconds)
                );
            }


            function getPrayerDate(now, prayer) {

                const date = new Date(now);

                date.setHours(
                    prayer.hour,
                    prayer.minute,
                    0,
                    0
                );

                return date;
            }


            /* =====================================================
               CLOCK
            ====================================================== */

            function updateClock() {
                const now = getTvNow();

                const hours = pad(now.getHours());
                const minutes = pad(now.getMinutes());

                clockTime.textContent = `${hours}:${minutes}`;

                const dateText = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });

                clockDate.textContent = dateText;
            }


            /* =====================================================
               ACTIVE PRAYER
            ====================================================== */

            function updateActivePrayer(now) {

                document
                    .querySelectorAll('.tv-prayer-bar-item')
                    .forEach(item => {

                        item.classList.remove('active');

                    });


                let activePrayer = null;

                countdownPrayers.forEach(prayer => {

                    const prayerDate =
                        getPrayerDate(now, prayer);

                    if (now >= prayerDate) {
                        activePrayer = prayer;
                    }

                });


                if (activePrayer) {

                    const activeElement =
                        document.querySelector(
                            `[data-prayer="${activePrayer.name}"]`
                        );

                    if (activeElement) {
                        activeElement.classList.add('active');
                    }

                }

            }


            /* =====================================================
               NEXT PRAYER
            ====================================================== */

            function getNextPrayer(now) {

                for (const prayer of countdownPrayers) {

                    const prayerDate =
                        getPrayerDate(now, prayer);

                    if (prayerDate > now) {
                        return {
                            prayer,
                            date: prayerDate
                        };
                    }

                }


                /*
                 * Jika semua sholat hari ini sudah lewat (mis. sudah
                 * lewat Isya), target berikutnya adalah waktu Subuh
                 * besok.
                 *
                 * Jam Subuh besok TIDAK SAMA dengan jam Subuh hari
                 * ini (bisa geser beberapa menit), jadi kita pakai
                 * window.TV_DATA.subuhBesok jika controller sudah
                 * mengirimkannya. Kalau belum tersedia, fallback ke
                 * jam Subuh hari ini sebagai perkiraan sementara.
                 */

                const firstPrayer =
                    countdownPrayers[0];

                if (!firstPrayer) {
                    return null;
                }


                const tomorrow =
                    new Date(now);

                tomorrow.setDate(
                    tomorrow.getDate() + 1
                );


                const subuhBesok = window.TV_DATA.subuhBesok;

                let targetHour = firstPrayer.hour;
                let targetMinute = firstPrayer.minute;
                let targetTimeLabel = firstPrayer.time;

                if (
                    subuhBesok &&
                    TIME_FORMAT.test(subuhBesok)
                ) {

                    const [besokHour, besokMinute] =
                        subuhBesok.split(':').map(Number);

                    targetHour = besokHour;
                    targetMinute = besokMinute;
                    targetTimeLabel = subuhBesok;

                }


                tomorrow.setHours(
                    targetHour,
                    targetMinute,
                    0,
                    0
                );


                return {
                    prayer: {
                        name: firstPrayer.name,
                        time: targetTimeLabel,
                        hour: targetHour,
                        minute: targetMinute
                    },
                    date: tomorrow
                };

            }


            /* =====================================================
               COUNTDOWN
            ====================================================== */

            function updateCountdown(now) {

                const next =
                    getNextPrayer(now);

                if (!next) {

                    countdownLabel.textContent =
                        'Jadwal Sholat';

                    countdownPrayer.textContent =
                        '-';

                    countdownStatus.textContent =
                        'Jadwal belum tersedia';

                    countdownTime.textContent =
                        '00:00:00';

                    return;
                }


                const difference =
                    next.date.getTime() -
                    now.getTime();


                countdownLabel.textContent =
                    'Menuju Waktu Sholat';


                countdownPrayer.textContent =
                    next.prayer.name;


                countdownStatus.textContent =
                    'Waktu ' + next.prayer.time;


                countdownTime.textContent =
                    formatCountdown(difference);

            }

            function getPrayerDateTime(now, prayerName) {

                const prayerKey = prayerName.toLowerCase();

                const prayerTime =
                    window.TV_DATA.sholat?.[prayerKey];

                if (!prayerTime || !TIME_FORMAT.test(prayerTime)) {
                    return null;
                }

                const [hour, minute] = prayerTime
                    .split(':')
                    .map(Number);

                const prayerDate = new Date(now);

                prayerDate.setHours(
                    hour,
                    minute,
                    0,
                    0
                );

                return prayerDate;
            }

            function getActiveIqamah(now) {

                const isFriday = now.getDay() === 5;

                for (const prayerName of iqamahPrayers) {

                    /*
                    * Jumat:
                    * Dzuhur tidak memiliki iqamah.
                    */
                    if (isFriday && prayerName === 'Dzuhur') {
                        continue;
                    }

                    const durationMinutes =
                        Number(iqamahSettings[prayerName] || 0);

                    if (durationMinutes <= 0) {
                        continue;
                    }

                    const prayerDate =
                        getPrayerDateTime(now, prayerName);

                    if (!prayerDate) {
                        continue;
                    }

                    const iqamahEnd = new Date(
                        prayerDate.getTime() +
                        durationMinutes * 60 * 1000
                    );

                    /*
                    * Aktif hanya setelah masuk waktu sholat
                    * sampai countdown iqamah selesai.
                    */
                    if (
                        now >= prayerDate &&
                        now < iqamahEnd
                    ) {

                        const remaining =
                            iqamahEnd.getTime() -
                            now.getTime();

                        return {
                            prayer: prayerName,
                            remaining,
                            duration: durationMinutes
                        };
                    }
                }

                return null;
            }

            function formatIqamahCountdown(milliseconds) {

                const totalSeconds = Math.max(
                    0,
                    Math.floor(milliseconds / 1000)
                );

                const minutes =
                    Math.floor(totalSeconds / 60);

                const seconds =
                    totalSeconds % 60;

                return (
                    String(minutes).padStart(2, '0') +
                    ':' +
                    String(seconds).padStart(2, '0')
                );
            }

            function updateIqamah(now) {

                const activeIqamah =
                    getActiveIqamah(now);

                /*
                * Tidak sedang iqamah
                * → kembali ke hero carousel.
                */
                if (!activeIqamah) {

                    iqamahElement.classList.add('hidden');

                    return;
                }

                /*
                * Sedang iqamah
                * → sembunyikan hero carousel
                * → tampilkan countdown.
                */
                iqamahElement.classList.remove('hidden');

                iqamahPrayerElement.textContent =
                    activeIqamah.prayer;

                iqamahTimeElement.textContent =
                    formatIqamahCountdown(
                        activeIqamah.remaining
                    );
            }

            /* =====================================================
               MAIN UPDATE
            ====================================================== */

            function updateTv() {

                const now = getTvNow();

                updateClock(now);

                updateActivePrayer(now);

                updateCountdown(now);

                updateIqamah(now);

            }


            updateTv();

            setInterval(() => {

                updateTv();

                checkMidnightReload();

            }, 1000);


            /* =====================================================
               TICKER: "Pesan [logo] Pesan [logo] ..."
               ---------------------------------------------------
               Dibangun lewat JS (bukan PHP) supaya:
               1. Selalu penuh selebar layar walau isi pengumuman
                  sedikit (diulang sampai >= 2x lebar layar).
               2. Loop -50% selalu mulus (isi digandakan persis
                  1x lagi di akhir).
            ====================================================== */

            function buildTicker() {

                const track = document.getElementById(
                    'tv-ticker-track'
                );

                if (!track) {
                    return;
                }

                const items = (
                    window.TV_DATA.pengumuman &&
                    window.TV_DATA.pengumuman.length
                )
                    ? window.TV_DATA.pengumuman
                    : ['Selamat datang di Masjid Al-Manaar Slipi.'];

                const logoSrc = track.dataset.logo || '';

                function appendOneRound() {

                    items.forEach(text => {

                        const span = document.createElement('span');

                        span.className = 'tv-ticker-item';
                        span.textContent = text;

                        track.appendChild(span);


                        if (logoSrc) {

                            const img = document.createElement('img');

                            img.className = 'tv-ticker-sep';
                            img.src = logoSrc;
                            img.alt = '';

                            track.appendChild(img);

                        }

                    });

                }


                track.innerHTML = '';

                const minWidth = window.innerWidth * 2;

                let guard = 0;

                while (
                    track.scrollWidth < minWidth &&
                    guard < 50
                ) {

                    appendOneRound();

                    guard += 1;

                }


                /*
                 * Gandakan sekali lagi persis (bukan tambah item baru)
                 * supaya animasi translateX(-50%) meloncat mulus
                 * dari salinan pertama ke salinan kedua.
                 */

                track.innerHTML = track.innerHTML + track.innerHTML;


                const trackWidth = track.scrollWidth / 2;

                const pixelsPerSecond = 90;

                const duration = Math.max(
                    12,
                    trackWidth / pixelsPerSecond
                );

                track.style.animationDuration = duration + 's';

            }

            buildTicker();

            window.addEventListener('resize', buildTicker);

        });
    </script>

</body>

</html>