<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<?php
    $isClosed = ! empty($donation['closed_at']);

    $proposalFile = trim($donation['proposal'] ?? '');
    $laporanFile  = trim($donation['laporan'] ?? '');

    $proposalUrl = $proposalFile
        ? base_url('uploads/donasi/proposal/' . $proposalFile)
        : null;

    $laporanUrl = $laporanFile
        ? base_url('uploads/donasi/laporan/' . $laporanFile)
        : null;
?>

<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- =========================================================
         BREADCRUMB
         ========================================================= -->

    <nav class="flex mb-6" aria-label="Breadcrumb">

        <ol class="inline-flex items-center space-x-2 text-sm font-medium">

            <li>
                <a href="<?= base_url() ?>"
                   class="text-gray-700 hover:text-blue-600 flex items-center">

                    <i data-lucide="home"
                       class="w-4 h-4 mr-2">
                    </i>

                    Beranda

                </a>
            </li>

            <li>
                <div class="flex items-center">

                    <i data-lucide="chevron-right"
                       class="w-4 h-4 text-gray-400">
                    </i>

                    <a href="<?= base_url('donasi') ?>"
                       class="ml-2 text-gray-700 hover:text-blue-600">

                        Donasi

                    </a>

                </div>
            </li>

            <li>
                <div class="flex items-center">

                    <i data-lucide="chevron-right"
                       class="w-4 h-4 text-gray-400">
                    </i>

                    <span class="ml-2 text-gray-400">
                        Detail
                    </span>

                </div>
            </li>

        </ol>

    </nav>


    <!-- =========================================================
         HEADER
         ========================================================= -->

    <div class="mb-8">

        <div class="flex flex-col md:flex-row
                    md:items-start md:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 mb-3">

                    <span class="inline-flex items-center gap-2
                                 rounded-full
                                 <?= $isClosed
                                     ? 'bg-gray-100 text-gray-600'
                                     : 'bg-green-100 text-green-700' ?>
                                 px-3 py-1
                                 text-xs font-semibold">

                        <?= $isClosed ? 'Sudah Ditutup' : 'Aktif' ?>

                    </span>

                </div>

                <h1 class="text-3xl md:text-4xl
                           font-extrabold text-gray-900">

                    <?= esc($donation['judul']) ?>

                </h1>

            </div>

        </div>

    </div>


    <!-- =========================================================
         DESKRIPSI
         ========================================================= -->

    <?php if (! empty($donation['deskripsi'])): ?>

        <div class="bg-white border border-gray-100
                    rounded-3xl shadow-sm p-6 md:p-8 mb-6">

            <div class="flex items-center gap-2 mb-4">

                <div class="w-9 h-9 rounded-xl
                            bg-blue-50 text-blue-600
                            flex items-center justify-center">

                    <i data-lucide="file-text"
                    class="w-5 h-5">
                    </i>

                </div>

                <h2 class="text-lg font-bold text-gray-900">
                    Deskripsi
                </h2>

            </div>

            <div class="prose prose-gray max-w-none
                        text-gray-600 leading-relaxed">

                <?= $donation['deskripsi'] ?>

            </div>

        </div>

    <?php endif; ?>

    <!-- =========================================================
         RINGKASAN KEUANGAN
         ========================================================= -->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <!-- Pemasukan -->

        <div class="bg-white border border-gray-100
                    rounded-3xl shadow-sm p-6">

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl
                            bg-green-50 text-green-600
                            flex items-center justify-center">

                    <i data-lucide="trending-up"
                       class="w-5 h-5">
                    </i>

                </div>

                <div>

                    <p class="text-xs uppercase
                              tracking-wider
                              font-semibold text-gray-400">

                        Pemasukan

                    </p>

                    <p class="text-2xl font-extrabold
                              text-green-600 mt-1">

                        Rp <?= number_format(
                            (float) $donation['pemasukan'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </p>

                </div>

            </div>

        </div>


        <!-- Pengeluaran -->

        <div class="bg-white border border-gray-100
                    rounded-3xl shadow-sm p-6">

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl
                            bg-red-50 text-red-600
                            flex items-center justify-center">

                    <i data-lucide="trending-down"
                       class="w-5 h-5">
                    </i>

                </div>

                <div>

                    <p class="text-xs uppercase
                              tracking-wider
                              font-semibold text-gray-400">

                        Pengeluaran

                    </p>

                    <p class="text-2xl font-extrabold
                              text-red-600 mt-1">

                        Rp <?= number_format(
                            (float) $donation['pengeluaran'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         DOKUMEN
         ========================================================= -->

    <?php if (
        (!$isClosed && $proposalUrl) ||
        ($isClosed && $laporanUrl)
    ): ?>

        <div class="bg-white border border-gray-100
                    rounded-3xl shadow-sm p-6 md:p-8 mb-6">

            <div class="flex items-center gap-2 mb-5">

                <div class="w-9 h-9 rounded-xl
                            bg-blue-50 text-blue-600
                            flex items-center justify-center">

                    <i data-lucide="folder-open"
                       class="w-5 h-5">
                    </i>

                </div>

                <h2 class="text-lg font-bold text-gray-900">
                    Dokumen
                </h2>

            </div>


            <div class="flex flex-wrap gap-3">

                <?php if (! $isClosed && $proposalUrl): ?>

                    <a href="<?= esc($proposalUrl) ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2
                              rounded-xl
                              bg-blue-600
                              hover:bg-blue-700
                              px-5 py-3
                              text-sm font-semibold
                              text-white transition">

                        <i data-lucide="file-text"
                           class="w-4 h-4">
                        </i>

                        Lihat Proposal

                        <i data-lucide="external-link"
                           class="w-4 h-4">
                        </i>

                    </a>

                <?php endif; ?>


                <?php if ($isClosed && $laporanUrl): ?>

                    <a href="<?= esc($laporanUrl) ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2
                              rounded-xl
                              bg-blue-600
                              hover:bg-blue-700
                              px-5 py-3
                              text-sm font-semibold
                              text-white transition">

                        <i data-lucide="file-check"
                           class="w-4 h-4">
                        </i>

                        Lihat Laporan

                        <i data-lucide="external-link"
                           class="w-4 h-4">
                        </i>

                    </a>

                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         INFORMASI PENUTUPAN
         ========================================================= -->

    <?php if ($isClosed): ?>

        <div class="bg-gray-50 border border-gray-200
                    rounded-3xl p-6 mb-10">

            <div class="flex items-start gap-4">

                <div class="w-10 h-10 shrink-0
                            rounded-xl
                            bg-gray-200
                            text-gray-600
                            flex items-center justify-center">

                    <i data-lucide="lock"
                       class="w-5 h-5">
                    </i>

                </div>

                <div>

                    <p class="text-sm font-semibold text-gray-700">
                        Donasi telah ditutup
                    </p>

                    <p class="text-sm text-gray-500 mt-1">

                        Ditutup pada
                        <span class="font-medium text-gray-700">

                            <?= esc(
                                format_indo(
                                    $donation['closed_at'],
                                    'full_datetime'
                                )
                            ) ?>

                        </span>

                        <?php if (! empty($donation['closed_by_nama'])): ?>

                            oleh
                            <span class="font-medium text-gray-700">
                                <?= esc($donation['closed_by_nama']) ?>
                            </span>

                        <?php endif; ?>

                    </p>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         DAFTAR PEMASUKAN DONATUR
         ========================================================= -->

    <section>

        <div class="mb-6">

            <h2 class="text-xl font-bold text-gray-900">
                Pemasukan Donasi
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Daftar pemasukan donasi yang telah diterima.
            </p>

        </div>


        <?php if (! empty($donationIncomes)): ?>

            <div class="space-y-4">

                <?php foreach ($donationIncomes as $index => $income): ?>

                    <?php

                        $isSamarkan =
                            strtoupper(
                                trim(
                                    $income['samarkan'] ?? ''
                                )
                            ) === 'Y';

                        $lokasi = [];

                        if (! empty($income['rt'])) {
                            $lokasi[] =
                                'RT ' . $income['rt'];
                        }

                        if (! empty($income['rw'])) {
                            $lokasi[] =
                                'RW ' . $income['rw'];
                        }

                        if (
                            empty($lokasi) &&
                            ! empty($income['kelurahan'])
                        ) {
                            $lokasi[] =
                                $income['kelurahan'];
                        }

                        $waktuDonasi =
                            ! empty($income['waktu'])
                                ? format_indo(
                                    $income['waktu'],
                                    'full_datetime'
                                )
                                : null;
                    ?>


                    <!-- Card Donatur -->

                    <div class="group bg-white
                                border border-gray-100
                                rounded-3xl
                                shadow-sm
                                overflow-hidden
                                transition-all duration-300
                                hover:-translate-y-1
                                hover:shadow-md">

                        <div class="flex items-center
                                    gap-4
                                    px-5 py-5
                                    md:px-6 md:py-6">


                            <!-- Nomor -->

                            <div class="shrink-0">

                                <div class="w-11 h-11
                                            md:w-12 md:h-12
                                            rounded-2xl
                                            bg-blue-50
                                            text-blue-600
                                            flex items-center
                                            justify-center
                                            font-extrabold">

                                    <?= str_pad(
                                        $index + 1,
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ) ?>

                                </div>

                            </div>


                            <!-- Informasi -->

                            <div class="min-w-0 flex-1">

                                <p class="font-bold text-gray-800 truncate">

                                    <?= $isSamarkan
                                        ? 'Hamba Allah'
                                        : esc($income['nama']) ?>

                                </p>


                                <?php if (! empty($lokasi)): ?>

                                    <p class="flex items-center gap-1.5
                                            text-sm text-gray-500
                                            mt-1 truncate">

                                        <i data-lucide="map-pin"
                                        class="w-3.5 h-3.5 shrink-0">
                                        </i>

                                        <span class="truncate">
                                            <?= esc(implode(' / ', $lokasi)) ?>
                                        </span>

                                    </p>

                                <?php endif; ?>


                                <div class="flex flex-wrap
                                            items-center gap-x-8
                                            gap-y-1 mt-1">

                                    <?php if ($waktuDonasi): ?>

                                        <span class="text-xs
                                                     text-gray-400
                                                     flex items-center
                                                     gap-1">

                                            <i data-lucide="clock"
                                               class="w-3.5 h-3.5">
                                            </i>

                                            <?= esc($waktuDonasi) ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (! empty($income['no_kwitansi'])): ?>

                                        <span class="text-xs
                                                     text-gray-400
                                                     flex items-center
                                                     gap-1">

                                            <i data-lucide="receipt"
                                               class="w-3.5 h-3.5">
                                            </i>

                                            <?= esc(
                                                $income['no_kwitansi']
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <!-- Nominal -->

                            <div class="shrink-0 text-right">

                                <p class="text-[10px]
                                          md:text-xs
                                          uppercase
                                          tracking-wider
                                          font-semibold
                                          text-gray-400
                                          mb-1">

                                    Pemasukan

                                </p>

                                <p class="text-base
                                          md:text-xl
                                          font-extrabold
                                          text-blue-600
                                          whitespace-nowrap">

                                    Rp <?= number_format(
                                        (float) (
                                            $income['jumlah'] ?? 0
                                        ),
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </p>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="bg-white
                        border border-gray-100
                        rounded-3xl
                        shadow-sm
                        py-12 px-6
                        text-center">

                <div class="w-16 h-16 mx-auto
                            rounded-full
                            bg-gray-100
                            flex items-center
                            justify-center mb-4">

                    <i data-lucide="hand-coins"
                       class="w-8 h-8 text-gray-400">
                    </i>

                </div>

                <h3 class="font-semibold text-gray-700">
                    Belum ada pemasukan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Belum terdapat pemasukan donasi yang telah diterima.
                </p>

            </div>

        <?php endif; ?>

    </section>

</div>

<?= $this->endSection() ?>