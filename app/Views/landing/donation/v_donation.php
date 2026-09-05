<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<style>
    .donation-card {
        opacity: 0;
        transform: translateY(20px);
        animation: donationCardEnter 0.6s ease-out forwards;
    }

    @keyframes donationCardEnter {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .donation-card {
            opacity: 1;
            transform: none;
            animation: none;
        }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 text-sm font-medium">
            <li>
                <a href="<?= base_url() ?>"
                   class="text-gray-700 hover:text-blue-600 flex items-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                    Beranda
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right"
                       class="w-4 h-4 text-gray-400"></i>

                    <span class="ml-2 text-gray-400">
                        Donasi
                    </span>
                </div>
            </li>
        </ol>
    </nav>


    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            DONASI
        </h1>

        <p class="text-gray-500 mt-2 italic">
            Mari bersama mendukung kegiatan dan pengembangan Masjid Al Manaar Slipi.
        </p>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center p-4 text-red-800 border-t-4 border-red-300 bg-red-50 rounded-lg shadow-sm" role="alert">
            <i data-lucide="alert-circle" class="flex-shrink-0 w-5 h-5"></i>
            <div class="ml-3 text-sm font-bold">
                <?= session()->getFlashdata('error') ?>
            </div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if (! session()->get('logged_in')): ?>

        <!-- =========================================================
             BELUM LOGIN
             ========================================================= -->

        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm p-10 md:p-16">
            <div class="flex flex-col items-center text-center">

                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                    <i data-lucide="hand-coins"
                       class="w-12 h-12 text-blue-600"></i>
                </div>

                <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-1 text-sm font-semibold text-amber-700 mb-5">
                    Coming Soon
                </span>

                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Halaman Rekapitulasi Donasi Sedang Dalam Pengembangan
                </h2>

                <p class="max-w-2xl text-gray-600 leading-relaxed">
                    Halaman ini nantinya akan menyajikan informasi mengenai
                    rekapitulasi donasi yang diterima Masjid Al Manaar Slipi
                    secara transparan dan informatif.
                    Saat ini fitur tersebut masih dalam proses pengembangan
                    dan akan segera tersedia.
                </p>

                <div class="mt-8">
                    <a href="<?= base_url() ?>"
                       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-white font-semibold hover:bg-blue-700 transition">

                        <i data-lucide="arrow-left" class="w-4 h-4"></i>

                        Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>


    <?php else: ?>

        <!-- =========================================================
             SUDAH LOGIN
             ========================================================= -->


        <!-- =========================================================
            LEADERBOARD DONASI TERBARU
            ========================================================= -->

        <section class="mb-10">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-2 mb-6">

                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        Donasi Terbaru
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        5 pemasukan donasi terbaru yang telah diterima.
                    </p>
                </div>

            </div>


            <?php if (! empty($donationLeaderboard)): ?>

                <div class="space-y-4">

                    <?php foreach ($donationLeaderboard as $index => $donation): ?>

                        <?php
                            /*
                            * =====================================================
                            * DATA DONATUR
                            * =====================================================
                            */

                            $isSamarkan = strtoupper(
                                trim($donation['samarkan'] ?? '')
                            ) === 'Y';


                            /*
                            * =====================================================
                            * LOKASI
                            * =====================================================
                            */

                            $lokasi = [];

                            if (! empty($donation['rt'])) {
                                $lokasi[] = 'RT ' . $donation['rt'];
                            }

                            if (! empty($donation['rw'])) {
                                $lokasi[] = 'RW ' . $donation['rw'];
                            }

                            // Jika RT/RW tidak tersedia,
                            // tampilkan kelurahan.
                            if (
                                empty($lokasi) &&
                                ! empty($donation['kelurahan'])
                            ) {
                                $lokasi[] = $donation['kelurahan'];
                            }


                            /*
                            * =====================================================
                            * WAKTU
                            * =====================================================
                            */

                            $waktuDonasi = ! empty($donation['waktu'])
                                ? format_indo(
                                    $donation['waktu'],
                                    'full_datetime'
                                )
                                : null;


                            /*
                            * =====================================================
                            * NOMOR RANKING
                            * =====================================================
                            */

                            $ranking = str_pad(
                                $index + 1,
                                2,
                                '0',
                                STR_PAD_LEFT
                            );


                            /*
                            * =====================================================
                            * DELAY ANIMASI
                            * =====================================================
                            */

                            $animationDelay = $index * 100;
                        ?>


                        <!-- =====================================================
                            CARD DONATUR
                            ===================================================== -->

                        <div
                            class="donation-card group"
                            style="animation-delay: <?= $animationDelay ?>ms;"
                        >

                            <div class="relative bg-white border border-gray-100
                                        rounded-3xl shadow-sm
                                        overflow-hidden
                                        transition-all duration-300
                                        hover:-translate-y-1
                                        hover:shadow-lg
                                        hover:border-blue-100">


                                <!-- Garis aksen kiri -->
                                <div class="absolute left-0 top-0 bottom-0
                                            w-1 bg-blue-600
                                            opacity-70
                                            group-hover:opacity-100
                                            transition-opacity">
                                </div>


                                <div class="flex items-center gap-4
                                            px-5 py-5 md:px-6 md:py-6">


                                    <!-- =================================================
                                        RANKING
                                        ================================================= -->

                                    <div class="shrink-0">

                                        <div class="w-12 h-12 md:w-14 md:h-14
                                                    rounded-2xl
                                                    bg-blue-50
                                                    text-blue-600
                                                    flex items-center justify-center
                                                    font-extrabold
                                                    text-sm md:text-base
                                                    transition-all duration-300
                                                    group-hover:bg-blue-600
                                                    group-hover:text-white
                                                    group-hover:scale-105">

                                            <?= $ranking ?>

                                        </div>

                                    </div>


                                    <!-- =================================================
                                        INFORMASI DONATUR
                                        ================================================= -->

                                    <div class="min-w-0 flex-1">

                                        <div class="flex items-center gap-2">

                                            <i data-lucide="user-round"
                                            class="w-4 h-4 text-gray-400 shrink-0">
                                            </i>

                                            <p class="font-bold text-gray-800
                                                    truncate">

                                                <?= $isSamarkan
                                                    ? 'Hamba Allah'
                                                    : esc($donation['nama']) ?>

                                            </p>

                                        </div>


                                        <?php if (! empty($lokasi)): ?>

                                            <div class="flex items-center gap-1.5
                                                        mt-1.5 text-sm text-gray-500">

                                                <i data-lucide="map-pin"
                                                class="w-3.5 h-3.5 shrink-0">
                                                </i>

                                                <span class="truncate">
                                                    <?= esc(
                                                        implode(' / ', $lokasi)
                                                    ) ?>
                                                </span>

                                            </div>

                                        <?php endif; ?>


                                        <?php if ($waktuDonasi): ?>

                                            <div class="flex items-center gap-1.5
                                                        mt-1 text-xs text-gray-400">

                                                <i data-lucide="clock"
                                                class="w-3.5 h-3.5 shrink-0">
                                                </i>

                                                <span>
                                                    <?= esc($waktuDonasi) ?>
                                                </span>

                                            </div>

                                        <?php endif; ?>

                                    </div>


                                    <!-- =================================================
                                        NOMINAL
                                        ================================================= -->

                                    <div class="shrink-0 text-right">

                                        <p class="text-[10px] md:text-xs
                                                uppercase tracking-wider
                                                font-semibold text-gray-400
                                                mb-1">
                                            Donasi
                                        </p>

                                        <p class="text-base md:text-xl
                                                font-extrabold
                                                text-blue-600
                                                whitespace-nowrap">

                                            Rp <?= number_format(
                                                (float) (
                                                    $donation['jumlah'] ?? 0
                                                ),
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>

                <!-- =========================================================
                    EMPTY STATE
                    ========================================================= -->

                <div class="bg-white border border-gray-100 rounded-3xl
                            shadow-sm py-12 px-6 text-center p-5">

                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100
                                flex items-center justify-center mb-4">

                        <i data-lucide="hand-coins"
                        class="w-8 h-8 text-gray-400">
                        </i>

                    </div>

                    <h3 class="font-semibold text-gray-700">
                        Belum ada data donasi
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Belum terdapat pemasukan donasi yang telah diterima.
                    </p>

                </div>

            <?php endif; ?>

        </section>


        <!-- =========================================================
            DONASI AKTIF
            ========================================================= -->

        <section>

            <div class="mb-5">

                <h2 class="text-xl font-bold text-gray-900">
                    Donasi Aktif
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar kegiatan donasi yang saat ini tersedia.
                </p>

            </div>


            <?php if (! empty($activeDonations)): ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <?php foreach ($activeDonations as $donation): ?>

                        <?php
                            $isClosed = ! empty($donation['closed_at']);

                            $statusLabel = $isClosed ? 'Sudah Ditutup' : 'Terbuka';

                            $statusBadgeClass = $isClosed
                                ? 'bg-gray-100 text-gray-500'
                                : 'bg-green-100 text-green-700';

                            $tanggalClosed = $isClosed
                                ? format_indo($donation['closed_at'], 'full_datetime')
                                : null;
                        ?>

                        <div class="h-full bg-white border border-gray-100 rounded-3xl
                                    shadow-sm overflow-hidden
                                    hover:shadow-md transition
                                    <?= $isClosed ? 'opacity-80' : '' ?>">

                            <div class="p-6 flex flex-col h-full">

                                <!-- Status -->
                                <div class="flex items-center justify-between mb-4">

                                    <span class="inline-flex items-center gap-2
                                                rounded-full <?= $statusBadgeClass ?>
                                                px-3 py-1 text-xs font-semibold">

                                        <?= esc($statusLabel) ?>

                                    </span>

                                    <i data-lucide="<?= $isClosed ? 'lock' : 'unlock' ?>" class="w-4 h-4 text-gray-300"></i>

                                </div>


                                <!-- Judul -->
                                <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                    <?= esc($donation['judul']) ?>
                                </h3>

                                <?php if ($isClosed): ?>
                                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="calendar-x" class="w-3.5 h-3.5"></i>
                                        Ditutup pada <?= esc($tanggalClosed) ?>
                                    </p>
                                <?php endif; ?>


                                <!-- Pemasukan -->
                                <div class="mt-6">

                                    <p class="text-xs font-medium uppercase
                                            tracking-wider text-gray-400">
                                        Total Pemasukan
                                    </p>

                                    <p class="text-2xl font-extrabold <?= $isClosed ? 'text-gray-500' : 'text-blue-600' ?> mt-1">
                                        Rp <?= number_format(
                                            (float) $donation['pemasukan'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </p>

                                </div>

                                <div class="mt-auto pt-6 border-t border-gray-100">

                                    <a href="<?= base_url('donasi/detail/' . $donation['id_donasi']) ?>"
                                    class="w-full inline-flex items-center justify-center gap-2
                                            rounded-xl px-4 py-3
                                            text-sm font-semibold text-white
                                            transition bg-blue-600 hover:bg-blue-700">

                                        <i data-lucide="eye" class="w-4 h-4"></i>

                                        Lihat Detail

                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="bg-white border border-gray-100 rounded-3xl
                            shadow-sm py-12 px-6 text-center p-5">

                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100
                                flex items-center justify-center mb-4">

                        <i data-lucide="folder-open"
                        class="w-8 h-8 text-gray-400"></i>

                    </div>

                    <h3 class="font-semibold text-gray-700">
                        Belum ada donasi aktif
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Saat ini belum terdapat kegiatan donasi yang sedang dibuka.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>