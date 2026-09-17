<!DOCTYPE html>
<html lang="id" class="h-full w-full">

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

    <!--
        CATATAN KONVERSI TAILWIND
        =========================================================
        1. Hampir semua styling di bawah pakai utility class
           Tailwind (line-clamp-*, tabular-nums, aspect-square,
           portrait:/landscape: variant — semuanya bawaan core
           Tailwind v3.3+/v3.4+, tidak perlu plugin tambahan).
        2. Warna abu-abu & biru di desain lama kebetulan 1:1 sama
           dengan palet slate-*/blue-* Tailwind, jadi tidak pakai
           hex custom sama sekali.
        3. Satu-satunya CSS custom yang tersisa adalah @keyframes
           untuk marquee ticker & efek berkedip (Adzan + titik dua
           jam), karena Tailwind tidak bisa mendefinisikan keyframe
           custom lewat class saja tanpa edit tailwind.config.
        4. Bagian Agenda dan QR Donasi punya markup GANDA (versi
           landscape + versi portrait), ditoggle lewat class
           `portrait:hidden` / `hidden portrait:block`. Ini karena
           Swiper butuh jumlah/slide DOM yang beda untuk "2 agenda
           per slide" (landscape) vs "1 agenda per slide" (portrait)
           — tidak bisa dicapai dari satu set slide yang sama hanya
           dengan CSS.
    -->
    <style>
        @keyframes tv-ticker-scroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        @keyframes tv-blink {
            0%, 50%  { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
    </style>
</head>

<body class="h-full w-full m-0 p-0 overflow-hidden bg-slate-50 font-sans text-slate-900">

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
    | Data baru (donasi, ayat/hadis, pengumuman, subuh besok)
    |--------------------------------------------------------------------------
    | Lihat penjelasan bentuk data di percakapan sebelumnya. Semua
    | diberi fallback aman agar view tidak error kalau controller
    | belum mengirim.
    */

    $donasi     = $donasi ?? [];
    $ayatHadis  = $ayatHadis ?? [];
    $pengumuman = $pengumuman ?? [];

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

    /*
    |--------------------------------------------------------------------------
    | Tanggal saldo
    |--------------------------------------------------------------------------
    | Semua kategori keuangan pakai tanggal_penghitungan yang sama,
    | jadi cukup diambil sekali dan ditampilkan di bawah judul
    | "Saldo Keuangan", bukan diulang di tiap kartu.
    */

    $financeDate = null;

    foreach (($finance ?? []) as $financeItem) {
        if (!empty($financeItem['tanggal_penghitungan'])) {
            $financeDate = $financeItem['tanggal_penghitungan'];
            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: render 1 kartu agenda
    |--------------------------------------------------------------------------
    | Dipakai di dua tempat (grid 2 kolom untuk landscape, dan
    | mini-swiper 1 kartu/slide untuk portrait) supaya logic
    | pengolahan datanya tidak perlu ditulis dua kali.
    */

    $renderAgendaCard = function (array $agenda, bool $spanFull = false) {

        $kategoriId = (int) ($agenda['id_kategori_agenda'] ?? 0);
        $kategori   = trim($agenda['nama_kategori'] ?? '');
        $judul      = trim($agenda['judul'] ?? '');
        $tema       = trim($agenda['tema'] ?? '');

        if ($judul !== '') {
            $agendaTitle = $judul;
        } elseif ($tema !== '' && $tema !== '-' && mb_strlen($tema) > 3) {
            $agendaTitle = $tema;
        } else {
            $agendaTitle = '-';
        }

        $mulai = !empty($agenda['waktu_mulai'])
            ? date('H:i', strtotime($agenda['waktu_mulai']))
            : null;

        $selesai = !empty($agenda['waktu_selesai'])
            ? date('H:i', strtotime($agenda['waktu_selesai']))
            : null;

        $ketMulai   = trim($agenda['ket_mulai'] ?? '');
        $ketSelesai = trim($agenda['ket_selesai'] ?? '');

        $waktuMulaiDisplay   = $ketMulai ?: $mulai;
        $waktuSelesaiDisplay = $ketSelesai ?: $selesai;

        $pengisi       = $agenda['pengisi'] ?? [];
        $jumlahPengisi = count($pengisi);
        $pengisiTampil = array_slice($pengisi, 0, 2);
        $sisaPengisi   = max(0, $jumlahPengisi - 2);

        $spanClass = $spanFull ? ' col-span-2' : '';

        ob_start();
        ?>
        <article class="min-w-0 min-h-0 flex flex-col justify-center p-[18px] rounded-[14px] bg-slate-50 border border-slate-200<?= $spanClass ?>">

            <span class="inline-block self-start mb-[9px] px-[11px] py-[3px] rounded-full bg-blue-50 text-blue-600 text-[10px] font-extrabold">
                <?= esc($kategori !== '' ? $kategori : 'Agenda') ?>
            </span>

            <h3 class="m-0 mb-[9px] text-[clamp(14px,1.1vw,20px)] font-extrabold leading-snug text-slate-900 line-clamp-3 break-words">
                <?= esc($agendaTitle) ?>
            </h3>

            <?php if ($kategoriId !== 1 && ($waktuMulaiDisplay || $waktuSelesaiDisplay)): ?>

                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-700">

                    <?php if ($waktuMulaiDisplay): ?>
                        <?= esc($waktuMulaiDisplay) ?>
                    <?php endif; ?>

                    <?php if ($waktuMulaiDisplay && $waktuSelesaiDisplay): ?>
                        <span class="text-slate-400 font-medium">—</span>
                    <?php endif; ?>

                    <?php if ($waktuSelesaiDisplay): ?>
                        <?= esc($waktuSelesaiDisplay) ?>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <?php if (!empty($pengisiTampil)): ?>

                <div class="mt-[7px] text-[10px] leading-relaxed text-slate-600">

                    <?php foreach ($pengisiTampil as $pengisiItem): ?>

                        <div class="whitespace-nowrap overflow-hidden text-ellipsis">

                            <?php if (!empty($pengisiItem['peran'])): ?>
                                <strong class="text-slate-900"><?= esc($pengisiItem['peran']) ?>:</strong>
                            <?php endif; ?>

                            <?= esc($pengisiItem['nama'] ?? '-') ?>

                        </div>

                    <?php endforeach; ?>

                    <?php if ($sisaPengisi > 0): ?>

                        <div class="mt-0.5 text-[9px] font-semibold text-slate-400">
                            + <?= $sisaPengisi ?> pengisi lainnya
                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </article>
        <?php
        return ob_get_clean();
    };
    ?>

    <!-- =========================================================
         ROOT GRID
         rows: [konten utama] [bar jadwal sholat] [ticker]
    ========================================================== -->

    <div class="h-screen w-screen grid grid-rows-[minmax(0,1fr)_auto_44px] bg-slate-50">

        <!-- =====================================================
             BARIS KONTEN UTAMA
             Landscape: 70% / 30% berdampingan.
             Portrait : ditumpuk (kiri di atas, kanan di bawah).
        ====================================================== -->

        <div class="min-h-0 min-w-0 grid grid-cols-[70%_30%] portrait:flex portrait:flex-col">

            <!-- =============================================
                 KOLOM KIRI (70% / full width saat portrait)
            ============================================== -->

            <main class="min-h-0 min-w-0 grid grid-rows-[57px_minmax(0,1fr)_126px] gap-[9px] p-[14px] pr-[7px] portrait:flex portrait:flex-col portrait:pr-[14px] portrait:flex-1">

                <header class="min-h-[69px] portrait:min-h-[52px] flex items-center gap-[12px] portrait:gap-[8px] px-[11px] portrait:px-[8px] py-[6px] portrait:py-[4px] flex-wrap">

                    <img
                        src="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>"
                        alt="Logo Masjid Al-Manaar Slipi"
                        class="w-[48px] h-[48px] portrait:w-[36px] portrait:h-[36px] object-contain flex-shrink-0">

                    <div class="min-w-0 flex-1">

                        <h1 class="m-0 text-[clamp(18px,1.5vw,26px)] portrait:text-[clamp(14px,4.2vw,20px)] font-extrabold leading-tight text-slate-800">
                            Masjid Al-Manaar
                        </h1>

                        <p class="mt-1 text-[clamp(9px,0.68vw,13px)] portrait:text-[clamp(8px,2.6vw,11px)] font-medium text-slate-500">
                            <?= esc($alamatMasjid ?? 'Slipi, Jakarta Barat') ?>
                        </p>

                    </div>

                    <div class="flex-shrink-0 min-w-[195px] portrait:min-w-[96px] flex flex-col items-end justify-center pl-[15px] portrait:pl-[8px] border-l border-slate-200">

                        <div
                            id="tv-header-date"
                            class="text-[clamp(12px,0.94vw,18px)] portrait:text-[clamp(9px,2.8vw,13px)] font-extrabold leading-tight text-slate-700 whitespace-nowrap portrait:whitespace-normal portrait:text-right">
                            -
                        </div>

                        <div
                            id="tv-header-hijriah"
                            class="mt-1 text-[clamp(11px,0.79vw,15px)] portrait:text-[clamp(8px,2.3vw,11px)] font-semibold leading-tight text-slate-500 whitespace-nowrap portrait:whitespace-normal portrait:text-right">
                            <?= esc($sholat['hijriah'] ?? '-') ?>
                        </div>

                    </div>

                </header>

                <!-- =========================================
                     HERO MEGA CAROUSEL (Hero → Agenda → Saldo)
                     Portrait: tinggi dibatasi (34vh) supaya tidak
                     menghabiskan hampir seluruh layar.
                ========================================== -->

                <section class="relative min-h-0 min-w-0 overflow-hidden rounded-[15px] bg-white border border-slate-200 shadow-[0_8px_24px_rgba(15,23,42,0.06)] portrait:h-[34vh] portrait:max-h-[34vh] portrait:flex-none">

                    <div
                        id="tv-hero-carousel"
                        class="tv-hero-swiper swiper w-full h-full">

                        <div class="swiper-wrapper">

                            <!-- ============ SLIDE: HERO GAMBAR ============ -->

                            <?php foreach (($carousels ?? []) as $carousel): ?>

                                <div class="swiper-slide">
                                    <div class="w-full h-full overflow-hidden">

                                        <img
                                            src="<?= base_url('uploads/carousel/' . $carousel['file']) ?>"
                                            alt=""
                                            class="block w-full h-full object-cover">

                                    </div>
                                </div>

                            <?php endforeach; ?>


                            <!-- ============ SLIDE: AGENDA ============
                                 Landscape: judul + grid 2 kolom (2 agenda/slide)
                                 Portrait : judul + mini-swiper (1 agenda/slide)
                            ==================================================== -->

                            <?php foreach ($agendaChunks as $chunk): ?>

                                <div class="swiper-slide">
                                    <div class="w-full h-full flex flex-col gap-[10px] p-[23px]">

                                        <h3 class="px-1 text-[clamp(14px,1.1vw,20px)] font-extrabold text-center text-slate-900">
                                            Agenda Hari Ini
                                        </h3>

                                        <!-- Landscape: 2 kolom -->
                                        <div class="flex-1 min-h-0 grid grid-cols-2 gap-[14px] portrait:hidden">

                                            <?php foreach ($chunk as $agenda): ?>
                                                <?= $renderAgendaCard($agenda, count($chunk) === 1) ?>
                                            <?php endforeach; ?>

                                        </div>

                                        <!-- Portrait: 1 agenda per slide (mini-swiper) -->
                                        <div class="hidden portrait:block flex-1 min-h-0">

                                            <div class="tv-agenda-mini-swiper swiper h-full">
                                                <div class="swiper-wrapper">

                                                    <?php foreach ($chunk as $agenda): ?>
                                                        <div class="swiper-slide">
                                                            <?= $renderAgendaCard($agenda, true) ?>
                                                        </div>
                                                    <?php endforeach; ?>

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            <?php endforeach; ?>

                            <?php if (empty($agendaChunks)): ?>

                                <div class="swiper-slide">
                                    <div class="w-full h-full flex flex-col gap-[10px] p-[23px]">
                                        <h3 class="px-1 text-[clamp(14px,1.1vw,20px)] font-extrabold text-center text-slate-900">
                                            Agenda Hari Ini
                                        </h3>
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-[15px]">
                                            Tidak ada agenda hari ini.
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>


                            <!-- ============ SLIDE: SALDO (judul + tanggal + grid 2x2 / slide) ============ -->

                            <?php foreach ($financeChunks as $chunk): ?>

                                <div class="swiper-slide">
                                    <div class="w-full h-full flex flex-col gap-[10px] p-[23px]">

                                        <div class="flex flex-col items-center gap-0.5">

                                            <h3 class="px-1 text-[clamp(14px,1.1vw,20px)] font-extrabold text-center text-slate-900">
                                                Saldo Keuangan
                                            </h3>

                                            <?php if ($financeDate): ?>

                                                <div class="text-[clamp(8px,0.8vw,10px)] text-slate-400 text-center">
                                                    Data Sampai
                                                    <?= esc(format_indo($financeDate, 'full')) ?>
                                                </div>

                                            <?php endif; ?>

                                        </div>

                                        <div class="flex-1 min-h-0 grid grid-cols-2 grid-rows-2 gap-[14px]">

                                        <?php foreach ($chunk as $item): ?>

                                            <div class="min-w-0 min-h-0 flex flex-col items-center justify-center text-center p-[14px] rounded-[14px] bg-gradient-to-br from-blue-50 to-white border border-slate-200">

                                                <div class="mb-1.5 text-[clamp(11px,0.90vw,13px)] font-bold text-slate-600">
                                                    <?= esc($item['kategori'] ?? 'Keuangan') ?>
                                                </div>

                                                <div class="text-[clamp(17px,1.6vw,26px)] font-black leading-snug text-slate-900 break-words">
                                                    Rp <?= number_format(
                                                        (float) ($item['saldo'] ?? 0),
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) ?>
                                                </div>

                                            </div>

                                        <?php endforeach; ?>

                                        </div>

                                    </div>
                                </div>

                            <?php endforeach; ?>

                        </div>

                        <div class="tv-hero-pagination swiper-pagination !absolute !bottom-[9px] z-10"></div>

                    </div>


                    <!-- =====================
                        WAKTUNYA ADZAN (overlay, 15 detik, berkedip)
                        Otomatis mengikuti tinggi section .tv-hero di atas
                        (termasuk pembatasan tinggi saat portrait), karena
                        posisinya absolute inset-0 relatif ke section itu.
                    ====================== -->

                    <div
                        id="adzan-countdown"
                        class="hidden absolute inset-0 z-20 flex items-center justify-center p-[23px] portrait:p-[10px] bg-white">

                        <div class="w-full max-w-[540px] flex flex-col items-center justify-center text-center [animation:tv-blink_1s_linear_infinite]">

                            <div class="mb-1.5 text-[clamp(11px,1vw,17px)] portrait:text-[clamp(9px,2.8vw,13px)] font-extrabold tracking-[0.12em] text-slate-500">
                                WAKTUNYA ADZAN
                            </div>

                            <div
                                id="adzan-prayer"
                                class="text-[clamp(32px,3.75vw,65px)] portrait:text-[clamp(22px,7vw,40px)] font-black leading-none text-blue-700">
                                -
                            </div>

                        </div>

                    </div>


                    <!-- =====================
                        IQAMAH COUNTDOWN (overlay)
                        Sama seperti Adzan: otomatis ikut tinggi hero.
                    ====================== -->

                    <div
                        id="iqamah-countdown"
                        class="hidden absolute inset-0 z-20 flex items-center justify-center p-[23px] portrait:p-[10px] bg-white">

                        <div class="w-full max-w-[540px] flex flex-col items-center justify-center text-center">

                            <div class="mb-1.5 text-[clamp(11px,1vw,17px)] portrait:text-[clamp(9px,2.8vw,13px)] font-extrabold tracking-[0.12em] text-slate-500">
                                MENUJU IQAMAH
                            </div>

                            <div
                                id="iqamah-prayer"
                                class="mb-2 text-[clamp(32px,3.75vw,65px)] portrait:text-[clamp(22px,7vw,40px)] font-black leading-none text-blue-700">
                                -
                            </div>

                            <div
                                id="iqamah-time"
                                class="text-[clamp(42px,5.25vw,90px)] portrait:text-[clamp(28px,9vw,52px)] font-black leading-none tracking-[0.04em] tabular-nums text-slate-900">
                                00:00
                            </div>

                            <div
                                id="iqamah-message"
                                class="mt-[13px] portrait:mt-[6px] text-[clamp(10px,0.75vw,15px)] portrait:text-[clamp(8px,2.4vw,11px)] font-medium text-slate-500">
                                Silakan bersiap untuk melaksanakan sholat
                            </div>

                        </div>

                    </div>

                </section>


                <!-- =========================================
                     AYAT / HADIS
                     Portrait: flex-1 supaya mengisi ruang yang
                     dibebaskan dari pembatasan tinggi hero.
                ========================================== -->

                <section class="min-h-0 min-w-0 overflow-hidden rounded-[17px] bg-white border border-slate-200 shadow-[0_8px_24px_rgba(15,23,42,0.06)] portrait:flex-1">

                    <div class="tv-ayat-swiper swiper w-full h-full">

                        <div class="swiper-wrapper">

                            <?php if (!empty($ayatHadis)): ?>

                                <?php foreach ($ayatHadis as $item): ?>

                                    <div class="swiper-slide">
                                        <div class="w-full h-full flex items-center px-[21px] py-[15px]">

                                            <div class="min-w-0 w-full">

                                                <?php if (!empty($item['arab'])): ?>

                                                    <p class="m-0 mb-2 [direction:rtl] font-serif text-[clamp(15px,1.28vw,23px)] leading-[1.9] text-slate-900 line-clamp-2">
                                                        <?= esc($item['arab']) ?>
                                                    </p>

                                                <?php endif; ?>

                                                <p class="m-0 text-[clamp(11px,0.79vw,14px)] font-medium leading-relaxed text-slate-700 line-clamp-2">
                                                    <?= esc($item['text'] ?? '') ?>
                                                </p>

                                                <?php if (!empty($item['source'])): ?>

                                                    <div class="mt-1.5 text-[10px] font-bold text-slate-400">
                                                        <?= esc($item['source']) ?>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="swiper-slide">
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[12px]">
                                        Belum ada ayat/hadis yang ditambahkan.
                                    </div>
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </section>

            </main>


            <!-- =============================================
                 KOLOM KANAN
                 Landscape: 30% lebar, ditumpuk vertikal.
                 Portrait : full width, di bawah kiri, horizontal.
            ============================================== -->

            <aside class="min-h-0 min-w-0 grid grid-rows-[0.85fr_1.15fr_1fr] gap-[9px] p-[14px] pl-[7px] portrait:grid-rows-none portrait:grid-cols-3 portrait:h-[135px] portrait:pl-[14px] portrait:pt-0">

                <!-- ============ JAM ============ -->

                <section class="min-h-0 min-w-0 flex flex-col items-center justify-center p-[12px] rounded-[18px] bg-slate-50 border border-slate-200 shadow-[0_8px_24px_rgba(15,23,42,0.06)]">

                    <div class="flex items-baseline justify-center text-[clamp(36px,3.75vw,66px)] portrait:text-[clamp(22px,8vw,36px)] font-black tracking-wide leading-[0.95] tabular-nums text-blue-700">
                        <span id="tv-clock-hour">--</span><span class="[animation:tv-blink_1s_linear_infinite]">:</span><span id="tv-clock-minute">--</span>
                    </div>

                </section>


                <!-- ============ QR DONASI ============
                     Landscape: grid 2 kolom (QR | Info Bank).
                     Portrait : slider (QR lalu Info Bank bergantian),
                     karena 2 kolom jadi terlalu sempit di layar sempit.
                ================================================== -->

                <section class="min-h-0 min-w-0 overflow-hidden bg-white border border-slate-200 rounded-[18px] shadow-[0_8px_24px_rgba(15,23,42,0.06)]">

                    <!-- ===== Landscape: 2 kolom ===== -->
                    <div class="h-full portrait:hidden grid grid-cols-[42%_58%] gap-[12px] p-[12px]">

                        <!-- KOLOM KIRI: QR -->
                        <div class="min-w-0 min-h-0 flex items-center justify-center">

                            <?php if (!empty($donasi['qr_image'])): ?>

                                <img
                                    src="<?= base_url('uploads/donasi/' . $donasi['qr_image']) ?>"
                                    alt="QR Donasi"
                                    class="w-full max-w-[145px] aspect-square object-contain">

                            <?php else: ?>

                                <div class="w-full aspect-square max-w-[145px] flex items-center justify-center rounded-xl bg-slate-50 border border-dashed border-slate-200">
                                    <span class="text-[9px] text-slate-400 text-center leading-tight">
                                        QR belum tersedia
                                    </span>
                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- KOLOM KANAN: INFORMASI BANK -->
                        <div class="min-w-0 min-h-0 flex flex-col justify-center gap-[6px]">

                            <div class="text-[clamp(11px,0.9vw,14px)] font-extrabold text-slate-900 leading-tight">
                                Donasi
                            </div>

                            <?php if (!empty($donasi['bank'])): ?>

                                <div class="min-w-0">
                                    <div class="text-[7px] font-semibold uppercase tracking-[0.08em] text-slate-400 leading-none mb-[3px]">
                                        Bank
                                    </div>

                                    <div class="text-[clamp(10px,0.85vw,13px)] font-black text-blue-700 leading-tight break-words">
                                        <?= esc($donasi['bank']) ?>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <?php if (!empty($donasi['norek'])): ?>

                                <div class="min-w-0">
                                    <div class="text-[7px] font-semibold uppercase tracking-[0.08em] text-slate-400 leading-none mb-[3px]">
                                        No. Rekening
                                    </div>

                                    <div class="text-[clamp(10px,0.85vw,13px)] font-black text-slate-800 leading-tight break-all">
                                        <?= esc($donasi['norek']) ?>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <?php if (!empty($donasi['nama'])): ?>

                                <div class="min-w-0">
                                    <div class="text-[7px] font-semibold uppercase tracking-[0.08em] text-slate-400 leading-none mb-[3px]">
                                        Atas Nama
                                    </div>

                                    <div class="text-[clamp(9px,0.78vw,12px)] font-bold text-slate-700 leading-snug break-words">
                                        <?= esc($donasi['nama']) ?>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <div class="pt-[3px] text-[clamp(7px,0.65vw,9px)] text-slate-500 leading-snug">
                                <?= esc($donasi['keterangan'] ?? 'Scan untuk berdonasi') ?>
                            </div>

                        </div>

                    </div>


                    <!-- ===== Portrait: slider QR ↔ Info Bank ===== -->
                    <div class="hidden portrait:flex h-full flex-col">

                        <div class="tv-donasi-swiper swiper flex-1 min-h-0">
                            <div class="swiper-wrapper">

                                <!-- Slide 1: QR -->
                                <div class="swiper-slide flex items-center justify-center p-[10px]">

                                    <?php if (!empty($donasi['qr_image'])): ?>

                                        <img
                                            src="<?= base_url('uploads/donasi/' . $donasi['qr_image']) ?>"
                                            alt="QR Donasi"
                                            class="w-full max-w-[150px] aspect-square object-contain">

                                    <?php else: ?>

                                        <div class="w-full aspect-square max-w-[150px] flex items-center justify-center rounded-xl bg-slate-50 border border-dashed border-slate-200">
                                            <span class="text-[9px] text-slate-400 text-center leading-tight px-2">
                                                QR belum tersedia
                                            </span>
                                        </div>

                                    <?php endif; ?>

                                </div>

                                <!-- Slide 2: Info Bank -->
                                <div class="swiper-slide flex flex-col items-center justify-center gap-[5px] p-[12px] text-center">

                                    <div class="text-[clamp(11px,3.2vw,15px)] font-extrabold text-slate-900">
                                        Donasi
                                    </div>

                                    <?php if (!empty($donasi['bank'])): ?>

                                        <div>
                                            <div class="text-[8px] font-semibold uppercase tracking-[0.08em] text-slate-400">
                                                Bank
                                            </div>
                                            <div class="text-[clamp(11px,3.2vw,15px)] font-black text-blue-700">
                                                <?= esc($donasi['bank']) ?>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php if (!empty($donasi['norek'])): ?>

                                        <div>
                                            <div class="text-[8px] font-semibold uppercase tracking-[0.08em] text-slate-400">
                                                No. Rekening
                                            </div>
                                            <div class="text-[clamp(11px,3.2vw,15px)] font-black text-slate-800">
                                                <?= esc($donasi['norek']) ?>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php if (!empty($donasi['nama'])): ?>

                                        <div>
                                            <div class="text-[8px] font-semibold uppercase tracking-[0.08em] text-slate-400">
                                                Atas Nama
                                            </div>
                                            <div class="text-[clamp(10px,2.8vw,13px)] font-bold text-slate-700">
                                                <?= esc($donasi['nama']) ?>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <div class="pt-[2px] text-[clamp(8px,2.3vw,10px)] text-slate-500">
                                        <?= esc($donasi['keterangan'] ?? 'Scan untuk berdonasi') ?>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="tv-donasi-pagination swiper-pagination !static !mt-0 !mb-1"></div>

                    </div>

                </section>


                <!-- ============ COUNTDOWN ============
                     Portrait: font countdown-prayer & countdown-time
                     dikecilkan supaya tidak meluber dari kolom sempit.
                ================================================== -->

                <section class="min-h-0 min-w-0 flex flex-col items-center justify-center text-center p-[14px] portrait:p-[8px] rounded-[18px] bg-gradient-to-br from-blue-50 to-white border border-blue-200 shadow-[0_8px_24px_rgba(37,99,235,0.08)]">

                    <div
                        id="tv-countdown-label"
                        class="text-[clamp(9px,1vw,14px)] portrait:text-[clamp(8px,2.4vw,11px)] font-bold text-slate-500">
                        Memuat...
                    </div>

                    <div
                        id="tv-countdown-prayer"
                        class="text-[clamp(17px,1.28vw,26px)] portrait:text-[clamp(13px,3.4vw,20px)] font-black text-blue-700">
                        -
                    </div>

                    <div
                        id="tv-countdown-status"
                        class="mt-1 text-[clamp(9px,0.64vw,12px)] portrait:text-[clamp(7px,2vw,9px)] text-slate-600">

                    </div>

                    <div
                        id="tv-countdown-time"
                        class="mt-[9px] portrait:mt-[4px] text-[clamp(24px,1.95vw,39px)] portrait:text-[clamp(16px,4.6vw,26px)] font-black leading-none tracking-[0.04em] tabular-nums text-slate-900">
                        00:00:00
                    </div>

                </section>

            </aside>

        </div>


        <!-- =====================================================
             BAR JADWAL SHOLAT (FULL WIDTH)
        ====================================================== -->

        <div class="min-w-0 px-[14px] pb-[9px]">

            <div class="min-w-0 grid grid-cols-7 bg-white border border-slate-200 rounded-[15px] shadow-[0_8px_24px_rgba(15,23,42,0.06)] overflow-hidden">

                <?php foreach ($prayerList as $name => $time): ?>

                    <div
                        class="tv-prayer-bar-item relative min-w-0 flex flex-col items-center justify-center gap-1 px-1 py-[11px] border-r border-slate-100 last:border-r-0"
                        data-prayer="<?= esc($name) ?>"
                        data-time="<?= esc($time ?? '') ?>">

                        <span class="tv-prayer-bar-accent hidden absolute top-0 left-0 right-0 h-[3px] bg-blue-600"></span>

                        <span class="tv-prayer-bar-icon w-[26px] h-[26px] flex items-center justify-center rounded-full bg-slate-100 text-slate-500">
                            <i data-lucide="<?= esc($prayerIcons[$name] ?? 'clock') ?>" class="w-[13px] h-[13px]"></i>
                        </span>

                        <span class="tv-prayer-bar-name text-[clamp(12px,1.2vw,30px)] font-bold text-slate-500">
                            <?= esc($name) ?>
                        </span>

                        <span class="tv-prayer-bar-time text-[clamp(12px,1.2vw,30px)] font-black tabular-nums text-slate-900">
                            <?= esc($time ?? '--:--') ?>
                        </span>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- =====================================================
             TICKER PENGUMUMAN (FULL WIDTH)
             Konten diisi lewat JS: "Pesan [logo] Pesan [logo] ..."
        ====================================================== -->

        <div class="w-full flex items-center bg-white border-t border-slate-200 overflow-hidden">

            <div
                id="tv-ticker-track"
                class="flex items-center whitespace-nowrap will-change-transform [animation:tv-ticker-scroll_20s_linear_infinite]"
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
            ====================================================== */

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

            const clockHour = document.getElementById('tv-clock-hour');
            const clockMinute = document.getElementById('tv-clock-minute');
            const clockDate = document.getElementById('tv-header-date');

            const countdownLabel = document.getElementById('tv-countdown-label');
            const countdownPrayer = document.getElementById('tv-countdown-prayer');
            const countdownStatus = document.getElementById('tv-countdown-status');
            const countdownTime = document.getElementById('tv-countdown-time');


            /* =====================================================
               PRAYER DATA
            ====================================================== */

            const PRAYER_NAME_MAP = {
                imsak: 'Imsak',
                subuh: 'Subuh',
                terbit: 'Terbit',
                dzuhur: 'Dzuhur',
                ashar: 'Ashar',
                maghrib: 'Maghrib',
                isya: 'Isya'
            };

            const TIME_FORMAT = /^\d{1,2}:\d{2}/;

            // Semua 7 waktu — dipakai HANYA untuk menandai bar mana yang aktif
            const allPrayers = Object.entries(window.TV_DATA.sholat || {})
                .filter(([key, time]) => {
                    return PRAYER_NAME_MAP[key] &&
                        typeof time === 'string' &&
                        TIME_FORMAT.test(time);
                })
                .map(([key, time]) => {
                    const [hour, minute] = time.split(':').map(Number);
                    return { name: PRAYER_NAME_MAP[key], time, hour, minute };
                })
                .sort((a, b) => (a.hour * 60 + a.minute) - (b.hour * 60 + b.minute));

            // Hanya 5 waktu utama — dipakai untuk countdown & target iqamah
            const countdownPrayers = allPrayers.filter(
                prayer => prayer.name !== 'Imsak' && prayer.name !== 'Terbit'
            );

            /* =========================================================
            ADZAN + IQAMAH COUNTDOWN
            ========================================================= */

            const adzanElement = document.getElementById('adzan-countdown');
            const adzanPrayerElement = document.getElementById('adzan-prayer');

            const iqamahElement = document.getElementById('iqamah-countdown');
            const iqamahPrayerElement = document.getElementById('iqamah-prayer');
            const iqamahTimeElement = document.getElementById('iqamah-time');

            const ADZAN_DURATION_MS = 15 * 1000;

            const adzanAudio = new Audio(
                '<?= base_url('assets/alarm/beeping-alarm-sound.mp3') ?>'
            );

            adzanAudio.preload = 'auto';

            let lastAdzanPrayer = null;

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

                const totalSeconds = Math.floor(milliseconds / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                return pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
            }


            function getPrayerDate(now, prayer) {

                const date = new Date(now);

                date.setHours(prayer.hour, prayer.minute, 0, 0);

                return date;
            }


            /* =====================================================
               CLOCK
            ====================================================== */

            function updateClock(now) {

                clockHour.textContent = pad(now.getHours());
                clockMinute.textContent = pad(now.getMinutes());

                const dayNames = {
                    Sunday: 'Ahad',
                    Monday: 'Senin',
                    Tuesday: 'Selasa',
                    Wednesday: 'Rabu',
                    Thursday: 'Kamis',
                    Friday: 'Jumat',
                    Saturday: 'Sabtu'
                };

                const dayName = now.toLocaleDateString('en-US', {
                    weekday: 'long'
                });

                const dateText = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });

                clockDate.textContent = `${dayNames[dayName]}, ${dateText}`;
            }


            /* =====================================================
               ACTIVE PRAYER (toggle class Tailwind, bukan CSS custom)
            ====================================================== */

            function resetPrayerItem(item) {

                item.classList.remove('bg-blue-50');

                const accent = item.querySelector('.tv-prayer-bar-accent');
                if (accent) accent.classList.add('hidden');

                const icon = item.querySelector('.tv-prayer-bar-icon');
                if (icon) {
                    icon.classList.remove('bg-blue-600', 'text-white');
                    icon.classList.add('bg-slate-100', 'text-slate-500');
                }

                const name = item.querySelector('.tv-prayer-bar-name');
                if (name) {
                    name.classList.remove('text-blue-700');
                    name.classList.add('text-slate-500');
                }

                const time = item.querySelector('.tv-prayer-bar-time');
                if (time) {
                    time.classList.remove('text-blue-700');
                    time.classList.add('text-slate-900');
                }
            }

            function activatePrayerItem(item) {

                item.classList.add('bg-blue-50');

                const accent = item.querySelector('.tv-prayer-bar-accent');
                if (accent) accent.classList.remove('hidden');

                const icon = item.querySelector('.tv-prayer-bar-icon');
                if (icon) {
                    icon.classList.remove('bg-slate-100', 'text-slate-500');
                    icon.classList.add('bg-blue-600', 'text-white');
                }

                const name = item.querySelector('.tv-prayer-bar-name');
                if (name) {
                    name.classList.remove('text-slate-500');
                    name.classList.add('text-blue-700');
                }

                const time = item.querySelector('.tv-prayer-bar-time');
                if (time) {
                    time.classList.remove('text-slate-900');
                    time.classList.add('text-blue-700');
                }
            }

            function updateActivePrayer(now) {

                document
                    .querySelectorAll('.tv-prayer-bar-item')
                    .forEach(resetPrayerItem);

                let activePrayer = null;

                allPrayers.forEach(prayer => {

                    const prayerDate = getPrayerDate(now, prayer);

                    if (now >= prayerDate) {
                        activePrayer = prayer;
                    }

                });

                if (activePrayer) {

                    const activeElement = document.querySelector(
                        `[data-prayer="${activePrayer.name}"]`
                    );

                    if (activeElement) {
                        activatePrayerItem(activeElement);
                    }

                }

            }


            /* =====================================================
               NEXT PRAYER
            ====================================================== */

            function getNextPrayer(now) {

                for (const prayer of countdownPrayers) {

                    const prayerDate = getPrayerDate(now, prayer);

                    if (prayerDate > now) {
                        return { prayer, date: prayerDate };
                    }

                }

                /*
                 * Semua sholat hari ini sudah lewat → target Subuh besok.
                 * Pakai window.TV_DATA.subuhBesok (jam Subuh besok yang
                 * sebenarnya dari controller) kalau tersedia, kalau
                 * belum baru fallback ke jam Subuh hari ini.
                 */

                const firstPrayer = countdownPrayers[0];

                if (!firstPrayer) {
                    return null;
                }

                const tomorrow = new Date(now);

                tomorrow.setDate(tomorrow.getDate() + 1);

                const subuhBesok = window.TV_DATA.subuhBesok;

                let targetHour = firstPrayer.hour;
                let targetMinute = firstPrayer.minute;
                let targetTimeLabel = firstPrayer.time;

                if (subuhBesok && TIME_FORMAT.test(subuhBesok)) {

                    const [besokHour, besokMinute] =
                        subuhBesok.split(':').map(Number);

                    targetHour = besokHour;
                    targetMinute = besokMinute;
                    targetTimeLabel = subuhBesok;

                }

                tomorrow.setHours(targetHour, targetMinute, 0, 0);

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

                const next = getNextPrayer(now);

                if (!next) {

                    countdownLabel.textContent = 'Jadwal Sholat';
                    countdownPrayer.textContent = '-';
                    countdownStatus.textContent = 'Jadwal belum tersedia';
                    countdownTime.textContent = '00:00:00';

                    return;
                }

                const difference = next.date.getTime() - now.getTime();

                countdownLabel.textContent = 'Menuju Waktu Sholat';
                countdownPrayer.textContent = next.prayer.name + ' (' + next.prayer.time + ')';
                // countdownStatus.textContent = 'Waktu ' + next.prayer.time;
                countdownTime.textContent = formatCountdown(difference);

            }

            function getPrayerDateTime(now, prayerName) {

                const prayerKey = prayerName.toLowerCase();
                const prayerTime = window.TV_DATA.sholat?.[prayerKey];

                if (!prayerTime || !TIME_FORMAT.test(prayerTime)) {
                    return null;
                }

                const [hour, minute] = prayerTime.split(':').map(Number);

                const prayerDate = new Date(now);

                prayerDate.setHours(hour, minute, 0, 0);

                return prayerDate;
            }

            function getActiveIqamahPhase(now) {

                const isFriday = now.getDay() === 5;

                for (const prayerName of iqamahPrayers) {

                    if (isFriday && prayerName === 'Dzuhur') {
                        continue;
                    }

                    const durationMinutes = Number(iqamahSettings[prayerName] || 0);

                    if (durationMinutes <= 0) {
                        continue;
                    }

                    const prayerDate = getPrayerDateTime(now, prayerName);

                    if (!prayerDate) {
                        continue;
                    }

                    const adzanEnd = new Date(
                        prayerDate.getTime() + ADZAN_DURATION_MS
                    );

                    const iqamahEnd = new Date(
                        prayerDate.getTime() + durationMinutes * 60 * 1000
                    );

                    /*
                    * 15 detik pertama setelah masuk waktu sholat
                    * → fase "Waktunya Adzan" (berkedip).
                    */
                    if (now >= prayerDate && now < adzanEnd) {

                        return {
                            phase: 'adzan',
                            prayer: prayerName
                        };
                    }

                    /*
                    * Setelah 15 detik itu sampai durasi iqamah
                    * habis → fase countdown iqamah seperti biasa.
                    */
                    if (now >= adzanEnd && now < iqamahEnd) {

                        const remaining = iqamahEnd.getTime() - now.getTime();

                        return {
                            phase: 'iqamah',
                            prayer: prayerName,
                            remaining,
                            duration: durationMinutes
                        };
                    }
                }

                return null;
            }

            function formatIqamahCountdown(milliseconds) {

                const totalSeconds = Math.max(0, Math.floor(milliseconds / 1000));
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            function updateIqamah(now) {

                const active = getActiveIqamahPhase(now);

                if (!active) {
                    adzanElement.classList.add('hidden');
                    iqamahElement.classList.add('hidden');
                    lastAdzanPrayer = null;
                    return;
                }

                if (active.phase === 'adzan') {

                    iqamahElement.classList.add('hidden');
                    adzanElement.classList.remove('hidden');

                    adzanPrayerElement.textContent = active.prayer;

                    if (lastAdzanPrayer !== active.prayer) {

                        lastAdzanPrayer = active.prayer;

                        adzanAudio.currentTime = 0;

                        adzanAudio.play()
                            .then(() => {
                                console.log(
                                    '[ADZAN] Alarm berhasil diputar:',
                                    active.prayer
                                );
                            })
                            .catch(error => {
                                console.warn(
                                    '[ADZAN] Alarm gagal diputar:',
                                    error
                                );
                            });
                    }

                    return;
                }

                adzanElement.classList.add('hidden');
                iqamahElement.classList.remove('hidden');

                iqamahPrayerElement.textContent = active.prayer;

                iqamahTimeElement.textContent =
                    formatIqamahCountdown(active.remaining);
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
            ====================================================== */

            function buildTicker() {

                const track = document.getElementById('tv-ticker-track');

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

                        span.className = 'px-[18px] text-[11px] font-semibold text-slate-800';
                        span.textContent = text;

                        track.appendChild(span);

                        if (logoSrc) {

                            const img = document.createElement('img');

                            img.className = 'w-[18px] h-[18px] object-contain flex-shrink-0 opacity-80';
                            img.src = logoSrc;
                            img.alt = '';

                            track.appendChild(img);

                        }

                    });

                }

                track.innerHTML = '';

                const minWidth = window.innerWidth * 2;

                let guard = 0;

                while (track.scrollWidth < minWidth && guard < 50) {
                    appendOneRound();
                    guard += 1;
                }

                /*
                 * Gandakan sekali lagi persis (bukan tambah item baru)
                 * supaya animasi translateX(-50%) meloncat mulus.
                 */

                track.innerHTML = track.innerHTML + track.innerHTML;

                const trackWidth = track.scrollWidth / 2;
                const pixelsPerSecond = 90;
                const duration = Math.max(12, trackWidth / pixelsPerSecond);

                track.style.animationDuration = duration + 's';

            }

            buildTicker();

            window.addEventListener('resize', buildTicker);

        });
    </script>

</body>

</html>