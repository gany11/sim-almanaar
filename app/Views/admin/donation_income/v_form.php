<?php
/**
 * Simpan sebagai:
 * app/Views/admin/donation_income/v_form.php
 *
 * Form terpusat untuk pemasukan donasi.
 *
 * Source:
 *   record        -> donatur & donasi FIX
 *   donor_record  -> donatur FIX, pilih donasi / kas / tercatat
 *   create        -> donasi FIX, pilih calon donatur
 *   edit          -> semua field bisa diedit sesuai aturan
 */

$lockDonatur = in_array($source, ['record', 'donor_record', 'edit'], true);
$lockDonasi = in_array($source, ['record', 'create', 'edit'], true);
$lockMetode = false;
$isEdit = ($source === 'edit');

$showHistori = in_array($source, ['donor_record', 'create'], true);
$prefillNominal = ($source === 'edit');
$prefillKwitansi = in_array($source, ['record', 'edit'], true);

/*
|--------------------------------------------------------------------------
| Akronim Kwitansi
|--------------------------------------------------------------------------
*/

$akronimAwal = old('akronim_kwitansi', $donasi['akronim_kwitansi'] ?? $akronimDefault);

/*
|--------------------------------------------------------------------------
| Nomor Kwitansi
|--------------------------------------------------------------------------
| Database:
| ALMNR-001
|
| Yang ditampilkan:
| 001
|--------------------------------------------------------------------------
*/

$rawNoKwitansi = $pemasukan['no_kwitansi'] ?? '';
$nomorAwal = $prefillKwitansi ? preg_replace( '/^' . preg_quote($akronimAwal, '/') . '-/', '', $rawNoKwitansi) : '';
$nomorAwal = old('no_kwitansi', $nomorAwal);

/*
|--------------------------------------------------------------------------
| Form Action
|--------------------------------------------------------------------------
*/

$formAction = $pemasukan ? base_url('admin/donation-incomes/update/' . $pemasukan['id_pemasukan_donasi']) : base_url('admin/donation-incomes/save');

/*
|--------------------------------------------------------------------------
| Title
|--------------------------------------------------------------------------
*/

$titleMap = [
    'record'       => 'Rekam Pencatatan Dana',
    'donor_record' => 'Catat Pemasukan Donatur',
    'create'       => 'Tambah Pemasukan Donasi',
    'edit'         => 'Edit Pemasukan Donasi',
];

$subtitleMap = [
    'record'       => 'Lengkapi data finansial untuk pencatatan yang sudah menunggu.',
    'donor_record' => 'Catat pemasukan baru atas nama donatur ini.',
    'create'       => 'Tambahkan donatur baru ke dalam program donasi ini.',
    'edit'         => 'Perbarui data pemasukan donasi yang sudah tercatat.',
];

$backUrl = base_url('admin/donations'); // Default fallback

switch ($source) {
    case 'record':
    case 'create':
        $backUrl = base_url('admin/donations/detail/' . ($pemasukan['id_donasi'] ?? $donasi['id_donasi'] ?? ''));
        break;
        
    case 'donor_record':
        $backUrl = base_url('admin/donors/detail/' . ($donatur['id_donatur'] ?? ''));
        break;
        
    case 'edit':
        // Jika memiliki id_donasi kembalikan ke detail donasi, jika tidak (kas/tercatat) ke detail donatur
        if (!empty($pemasukan['id_donasi'])) {
            $backUrl = base_url('admin/donations/detail/' . $pemasukan['id_donasi']);
        } else {
            $backUrl = base_url('admin/donors/detail/' . ($pemasukan['id_donatur'] ?? $donatur['id_donatur'] ?? ''));
        }
        break;
}

$currentType = old('donation_type', 'donasi');
?>

<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
?>

<?php if (!empty($errors)): ?>
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-start gap-3">
            <i data-lucide="circle-alert"
               class="w-5 h-5 text-red-600 mt-0.5 shrink-0"></i>

            <div>
                <h3 class="font-semibold text-red-800">
                    Terdapat kesalahan pada formulir
                </h3>

                <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container mx-auto pb-10" x-data="donationIncomeForm()" x-init="init()">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <?= esc($titleMap[$source] ?? 'Pemasukan Donasi') ?>
            </h2>
            <p class="text-sm text-gray-500">
                <?= esc($subtitleMap[$source] ?? '') ?>
            </p>
        </div>

        <a href="<?= $backUrl ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <!-- =========================================================
         FORM CARD
    ========================================================== -->

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>
            <input type="hidden" name="source" value="<?= esc($source) ?>">

            <!-- =====================================================
                 GRID UTAMA
            ====================================================== -->

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- =================================================
                     KOLOM KIRI
                ================================================== -->

                <div class="space-y-6">

                    <!-- =================================================
                        DONATUR
                    ================================================== -->

                    <div>

                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">
                            Donatur
                        </label>


                        <?php if ($lockDonatur): ?>

                            <!-- =================================================
                                DONATUR FIX
                                record / donor_record / edit
                            ================================================== -->

                            <input type="hidden" name="id_donatur" value="<?= esc($donatur['id_donatur'] ?? '') ?>">

                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 flex items-center gap-2 font-semibold">

                                <i data-lucide="user-check" class="w-4 h-4 text-gray-400"></i>
                                <?= esc($donatur['nama'] ?? '-') ?>

                            </div>

                            <p class="text-[11px] text-gray-400 mt-1">
                                Donatur sudah ditentukan dari sumber halaman ini.
                            </p>

                        <?php else: ?>

                            <!-- =================================================
                                PILIH DONATUR
                                create
                            ================================================== -->

                            <select name="id_donatur" id="donaturSelect" @change="checkTeleponDonatur($event)"
                                class="w-full select2-dynamic px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['id_donatur'])
                                    ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200'?> bg-gray-50 text-sm">

                                <option value="">
                                    Pilih atau Cari Calon Donatur...
                                </option>

                                <?php foreach ($calonDonatur as $d): ?>

                                    <option
                                        value="<?= esc($d['id_donatur']) ?>" data-telepon="<?= esc(trim($d['telepon'] ?? '')) ?>"
                                        data-nama="<?= esc($d['nama'] ?? '') ?>" data-kwitansi="<?= esc($d['no_kwitansi_aktif'] ?? '') ?>"
                                        <?= old('id_donatur') == $d['id_donatur'] ? 'selected' : '' ?>
                                    >
                                        [<?= esc($d['noreg']) ?>]
                                        <?= esc($d['nama']) ?>

                                        <?php if (!empty($d['telepon'])): ?>
                                            (<?= esc($d['telepon']) ?>)
                                        <?php endif; ?>

                                        <?php if (!empty($d['no_kwitansi_aktif'])): ?>
                                            — Terdaftar aktif (<?= esc($d['no_kwitansi_aktif']) ?>)
                                        <?php endif; ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>

                            <p class="text-[11px] text-gray-400 mt-1">
                                Semua donatur ditampilkan. Jika donatur yang dipilih sudah terdaftar aktif
                                di program ini, nomor kwitansi lama akan dipakai kembali secara otomatis.
                            </p>

                        <?php endif; ?>

                        <!-- ==========================================================
                            ALERT TELEPON

                            Berlaku:
                            record
                            donor_record
                            create
                            edit
                        =========================================================== -->

                        <?php if (in_array($source,['record', 'donor_record', 'create', 'edit'],true)): ?>

                            <div x-show="showPhoneAlert" x-cloak class="mt-3 p-4 rounded-2xl border flex items-start gap-3"
                                :class="hasTelepon ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200'">

                                <div
                                    class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                                    :class="
                                        hasTelepon
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-amber-100 text-amber-700'
                                    "
                                >

                                    <i data-lucide="phone" x-show="hasTelepon" x-cloak class="w-4 h-4"></i>
                                    <i data-lucide="phone-off" x-show="!hasTelepon" x-cloak class="w-4 h-4"></i>
                                </div>

                                <div
                                    class="text-xs space-y-1"
                                    :class="
                                        hasTelepon
                                            ? 'text-green-800'
                                            : 'text-amber-800'
                                    "
                                >

                                    <!-- =================================================
                                        ADA TELEPON
                                    ================================================== -->

                                    <template x-if="hasTelepon">

                                        <div>

                                            <p class="font-bold">
                                                Nomor Telepon Tersedia
                                            </p>

                                            <p>

                                                Donatur

                                                <b
                                                    x-text="selectedDonaturName"
                                                ></b>

                                                memiliki nomor telepon

                                                <span
                                                    class="font-mono font-semibold"
                                                    x-text="selectedTelepon"
                                                ></span>.

                                            </p>

                                            <p class="mt-1">

                                                Sistem akan mengirimkan notifikasi
                                                WhatsApp terkait transaksi ini
                                                setelah data berhasil disimpan.

                                            </p>

                                        </div>

                                    </template>


                                    <!-- =================================================
                                        TIDAK ADA TELEPON
                                    ================================================== -->

                                    <template x-if="!hasTelepon">

                                        <div>

                                            <p class="font-bold">
                                                Nomor Telepon Tidak Tersedia
                                            </p>

                                            <p>

                                                Donatur

                                                <b
                                                    x-text="selectedDonaturName"
                                                ></b>

                                                belum memiliki nomor telepon
                                                yang tersimpan.

                                            </p>

                                            <p class="mt-1">

                                                Notifikasi WhatsApp tidak dapat
                                                dikirimkan secara otomatis untuk
                                                transaksi ini.

                                            </p>

                                        </div>

                                    </template>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                            ERROR DONATUR
                        ================================================== -->

                        <?php if (isset(session('errors')['id_donatur'])): ?>

                            <p class="text-xs text-red-500 mt-2 font-bold">

                                <?= session('errors')['id_donatur'] ?>

                            </p>

                        <?php endif; ?>

                    </div>

                    <!-- =================================================
                         TANGGAL
                    ================================================== -->

                    <?php $lockTanggal = ($source === 'edit'); ?>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">
                            Tanggal
                        </label>

                        <?php if ($lockTanggal): ?>
                            <input type="hidden" name="tanggal" value="<?= esc($pemasukan['tanggal'] ?? date('Y-m-d')) ?>">
                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 flex items-center gap-2 font-semibold">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                <?= esc(date('d F Y', strtotime($pemasukan['tanggal'] ?? 'now'))) ?>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Tanggal tidak dapat diubah saat mengedit.</p>
                        <?php else: ?>
                            <input type="date" name="tanggal" max="<?= date('Y-m-d') ?>"
                                value="<?= old('tanggal', $pemasukan['tanggal'] ?? date('Y-m-d')) ?>"
                                class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['tanggal']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm">
                        <?php endif; ?>

                        <?php if (isset(session('errors')['tanggal'])): ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['tanggal'] ?></p>
                        <?php endif; ?>
                    </div>



                    <!-- =================================================
                         METODE PEMASUKAN
                    ================================================== -->

                    <div>

                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">
                            Metode Pemasukan
                        </label>


                        <?php if ($lockMetode): ?>

                            <input
                                type="hidden"
                                name="id_metode_pemasukan"
                                value="<?= esc($pemasukan['id_metode_pemasukan'] ?? '') ?>"
                            >

                            <?php

                            $metodeAktif = array_values(
                                array_filter(
                                    $metodeList,
                                    fn($m) =>
                                        $m['id_metode_pemasukan'] ==
                                        ($pemasukan['id_metode_pemasukan'] ?? null)
                                )
                            );

                            $metodeAktif = $metodeAktif[0] ?? null;

                            ?>

                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 font-semibold">

                                <?= esc($metodeAktif['metode_pemasukan'] ?? '-') ?>

                            </div>


                        <?php else: ?>

                            <select
                                name="id_metode_pemasukan"
                                class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['id_metode_pemasukan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer"
                            >

                                <option value="">
                                    Pilih Metode...
                                </option>

                                <?php foreach ($metodeList as $m): ?>
                                    <option
                                        value="<?= $m['id_metode_pemasukan'] ?>"
                                        <?= (old('id_metode_pemasukan', $pemasukan['id_metode_pemasukan'] ?? '') == $m['id_metode_pemasukan']) ? 'selected' : '' ?>
                                    >
                                        <?= esc($m['metode_pemasukan']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                        <?php endif; ?>


                        <?php if (isset(session('errors')['id_metode_pemasukan'])): ?>

                            <p class="text-xs text-red-500 mt-2 font-bold">
                                <?= session('errors')['id_metode_pemasukan'] ?>
                            </p>

                        <?php endif; ?>

                    </div>



                    <!-- =================================================
                         SAMARKAN
                    ================================================== -->

                    <div class="flex items-center justify-between p-2">

                        <div>

                            <p class="text-sm font-bold text-gray-700">
                                Samarkan Donatur
                            </p>

                            <p class="text-[11px] text-gray-500">
                                Sembunyikan nama donatur di tampilan publik.
                            </p>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <?php
                                $samarkanValue = old(
                                    'samarkan',
                                    ($pemasukan['samarkan'] ?? 'N') === 'Y'
                                        ? 'ya'
                                        : ''
                                );
                            ?>

                            <input
                                type="checkbox"
                                name="samarkan"
                                value="ya"
                                class="sr-only peer"
                                <?= $samarkanValue === 'ya' ? 'checked' : '' ?>
                            >

                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all">
                            </div>

                        </label>

                    </div>

                </div>



                <!-- =================================================
                     KOLOM KANAN
                ================================================== -->

                <div class="lg:col-span-2 space-y-6">


                    <!-- =================================================
                         PROGRAM DONASI
                    ================================================== -->

                    <?php if ($lockDonasi): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Program Donasi</label>
                            <input type="hidden" name="id_donasi" value="<?= esc($donasi['id_donasi'] ?? '') ?>">
                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 flex items-center gap-2 font-semibold">
                                <i data-lucide="heart-handshake" class="w-4 h-4 text-gray-400"></i>
                                <?= esc($donasi['judul'] ?? '-') ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- JENIS PENCATATAN -->
                    <?php if ($source === 'donor_record'): ?>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Jenis Pencatatan</label>
                            <div class="grid grid-cols-3 gap-3">
                                <?php
                                $typeOptions = [
                                    'donasi'   => ['label' => 'Donasi', 'icon' => 'heart-handshake'],
                                    'kas'      => ['label' => 'Kas', 'icon' => 'wallet'],
                                    'tercatat' => ['label' => 'Tercatat', 'icon' => 'notebook-pen'],
                                ];
                                $currentType = old('donation_type', $jenisPencatatan ?? 'donasi');
                                ?>
                                <?php foreach ($typeOptions as $val => $opt): ?>
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="donation_type" value="<?= $val ?>"
                                            x-model="donationType" @change="onDonationTypeChange()"
                                            class="sr-only" <?= $currentType === $val ? 'checked' : '' ?>>
                                        <div class="w-full min-h-[90px] p-3 rounded-2xl border flex flex-col items-center justify-center gap-2 transition-all duration-200"
                                            :class="donationType === '<?= $val ?>' ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-100' : 'bg-gray-50 border-gray-200 text-gray-500 hover:border-blue-300 hover:bg-blue-50'">
                                            <i data-lucide="<?= $opt['icon'] ?>" class="w-5 h-5"></i>
                                            <span class="text-xs font-bold"><?= $opt['label'] ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Program Donasi picker, hanya untuk donor_record + tipe donasi -->
                        <div x-show="donationType === 'donasi'" x-cloak>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Program Donasi</label>
                            <select name="id_donasi" id="donasiSelect" @change="onDonasiTerpilih($event)"
                                    class="w-full select2-basic px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['id_donasi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> bg-gray-50 text-sm">
                                <option value="">Pilih Program Donasi...</option>
                                <?php foreach ($donasiTersedia as $dn): ?>
                                    <option value="<?= $dn['id_donasi'] ?>"
                                            data-akronim="<?= esc($dn['akronim_kwitansi'] ?? $akronimDefault) ?>"
                                            data-kwitansi="<?= esc($dn['no_kwitansi_aktif'] ?? '') ?>"
                                            <?= old('id_donasi') == $dn['id_donasi'] ? 'selected' : '' ?>>
                                        <?= esc($dn['judul']) ?>
                                        <?php if (!empty($dn['no_kwitansi_aktif'])): ?>
                                            — Sudah terdaftar (<?= esc($dn['no_kwitansi_aktif']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Hanya menampilkan program donasi yang belum dikunci.</p>
                            <?php if (isset(session('errors')['id_donasi'])): ?>
                                <p class="text-xs text-red-500 mt-2 font-bold"><?= esc(session('errors')['id_donasi']) ?></p>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($source === 'edit'): ?>

                        <?php
                        $typeLabelMap = [
                            'donasi'   => ['label' => 'Donasi', 'icon' => 'heart-handshake'],
                            'kas'      => ['label' => 'Kas', 'icon' => 'wallet'],
                            'tercatat' => ['label' => 'Tercatat', 'icon' => 'notebook-pen'],
                        ];
                        $typeInfo = $typeLabelMap[$jenisPencatatan] ?? $typeLabelMap['donasi'];
                        ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Jenis Pencatatan</label>
                            <input type="hidden" name="donation_type" value="<?= esc($jenisPencatatan) ?>">
                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 flex items-center gap-2 font-semibold">
                                <i data-lucide="<?= $typeInfo['icon'] ?>" class="w-4 h-4 text-gray-400"></i>
                                <?= esc($typeInfo['label']) ?>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Jenis pencatatan tidak dapat diubah saat mengedit.</p>
                        </div>

                    <?php endif; ?>

                    <!-- KAS — muncul untuk donor_record DAN edit -->
                    <?php if (in_array($source, ['donor_record', 'edit'], true)): ?>
                        <div x-show="donationType === 'kas'" x-cloak class="space-y-6 bg-gray-50/70 p-6 rounded-3xl border border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Kategori Kas</label>
                                    <select name="id_kategori_keuangan"
                                            class="w-full px-4 py-3 rounded-2xl border <?= isset(session('errors')['id_kategori_keuangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> bg-white text-sm">
                                        <option value="">Pilih...</option>
                                        <?php foreach ($kategoriKeuanganList as $k): ?>
                                            <option value="<?= $k['id_kategori_keuangan'] ?>"
                                                <?= old('id_kategori_keuangan', $keuangan['id_kategori_keuangan'] ?? '') == $k['id_kategori_keuangan'] ? 'selected' : '' ?>>
                                                <?= esc($k['kategori']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset(session('errors')['id_kategori_keuangan'])): ?>
                                        <p class="text-xs text-red-500 mt-2 font-bold"><?= esc(session('errors')['id_kategori_keuangan']) ?></p>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Alokasi</label>
                                    <select
                                        name="id_alokasi" id="alokasiSelect" @change="onAlokasiManualChange($event.target.value)"
                                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-sm"
                                    >
                                        <option value="">Pilih Alokasi...</option>
                                        <?php
                                        $selectedAlokasi = old('id_alokasi', $keuangan['id_alokasi'] ?? '');
                                        ?>
                                        <?php foreach ($alokasiList as $a): ?>
                                            <option value="<?= $a['id_alokasi'] ?>"
                                                <?= $selectedAlokasi == $a['id_alokasi'] ? 'selected' : '' ?>>
                                                <?= esc($a['nama_alokasi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Detail Alokasi</label>
                                    <select
                                        name="id_detail_alokasi"
                                        x-model="selectedDetailAlokasi"
                                        x-ref="detailAlokasiSelect"
                                        class="w-full px-4 py-3 rounded-2xl border <?= isset(session('errors')['id_detail_alokasi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> bg-white text-sm"
                                    >
                                        <option value="">Pilih Detail...</option>
                                        <template x-for="item in alokasiDetails" :key="item.id_detail_alokasi">
                                            <option :value="item.id_detail_alokasi" x-text="item.detail_alokasi"></option>
                                        </template>
                                    </select>
                                    <?php if (isset(session('errors')['id_detail_alokasi'])): ?>
                                        <p class="text-xs text-red-500 mt-2 font-bold"><?= esc(session('errors')['id_detail_alokasi']) ?></p>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- KETERANGAN — muncul untuk donor_record DAN edit, jenis kas/tercatat -->
                    <?php if (in_array($source, ['donor_record', 'edit'], true)): ?>
                        <div x-show="donationType === 'kas' || donationType === 'tercatat'" x-cloak>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">
                                <span x-show="donationType === 'kas'" x-cloak>Keterangan / Peruntukan Kas</span>
                                <span x-show="donationType === 'tercatat'" x-cloak>Keterangan Pencatatan</span>
                            </label>
                            <input type="text" name="keterangan" x-model="keteranganShared"
                                value="<?= old('keterangan', $pemasukan['keterangan'] ?? ($keuangan['keterangan'] ?? '')) ?>"
                                class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['keterangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> bg-gray-50 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                :placeholder="donationType === 'kas' ? 'Tuliskan peruntukan dana kas...' : 'Tuliskan keterangan pencatatan...'">
                            <?php if (isset(session('errors')['keterangan'])): ?>
                                <p class="text-xs text-red-500 mt-2 font-bold"><?= esc(session('errors')['keterangan']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- =================================================
                         KWITANSI
                    ================================================== -->

                    <?php $lockKwitansi = ($source === 'edit'); ?>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nomor Kwitansi</label>

                        <?php if ($lockKwitansi): ?>
                            <input type="hidden" name="akronim_kwitansi" value="<?= esc($akronimAwal) ?>">
                            <input type="hidden" name="no_kwitansi" value="<?= esc($nomorAwal) ?>">
                            <div class="px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-sm text-gray-700 flex items-center gap-2 font-mono font-semibold">
                                <i data-lucide="receipt" class="w-4 h-4 text-gray-400"></i>
                                <?= esc($rawNoKwitansi ?: '-') ?>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Nomor kwitansi tidak dapat diubah saat mengedit.</p>
                        <?php else: ?>
                            <div class="flex items-center bg-white border <?= isset(session('errors')['no_kwitansi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">

                            <div class="bg-gray-50 px-4 py-3.5 border-r border-gray-100">

                                <span
                                    class="text-gray-500 font-bold font-mono text-sm"
                                    x-text="(akronimKwitansi || '<?= esc($akronimDefault) ?>') + '-'"
                                ></span>

                            </div>

                            <input
                                type="text"
                                name="no_kwitansi"
                                x-model="nomorKwitansi"
                                class="w-full px-4 py-3.5 outline-none text-gray-700 font-mono text-sm bg-white"
                                placeholder="001 (Kosongkan jika otomatis)"
                            >

                            <input
                                type="hidden"
                                name="akronim_kwitansi"
                                x-model="akronimKwitansi"
                            >

                        </div>
                        <?php endif; ?>

                        <?php if (isset(session('errors')['no_kwitansi'])): ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['no_kwitansi'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- =================================================
                         JUMLAH
                    ================================================== -->

                    <div>

                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">
                            Nominal Donasi
                        </label>

                        <div class="flex items-center bg-white border <?= isset(session('errors')['jumlah']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">

                            <div class="bg-gray-50 px-5 py-3.5 border-r border-gray-100">

                                <span class="text-gray-400 font-bold">
                                    Rp
                                </span>

                            </div>

                            <input
                                type="text"
                                x-model="displayJumlah"
                                @input="formatJumlah"
                                class="w-full px-5 py-3.5 outline-none text-gray-700 font-medium"
                                placeholder="0"
                            >

                            <input
                                type="hidden"
                                name="jumlah"
                                x-model="rawJumlah"
                            >

                        </div>


                        <?php if (isset(session('errors')['jumlah'])): ?>

                            <p class="text-xs text-red-500 mt-2 font-bold">
                                <?= session('errors')['jumlah'] ?>
                            </p>

                        <?php endif; ?>

                    </div>



                    <!-- =================================================
                         BUKTI
                    ================================================== -->

                    <div class="bg-blue-50/50 p-6 rounded-3xl border <?= isset(session('errors')['bukti']) ? 'border-red-500 ring-1 ring-red-500' : 'border-blue-100/50' ?>">

                        <label class="block text-sm font-bold text-blue-900 mb-1">
                            Lampiran Bukti (Opsional)
                        </label>

                        <p class="text-xs text-blue-600/70 mb-4 tracking-tight">
                            Unggah kuitansi atau bukti transfer dalam format PDF atau Gambar (Maks. 5MB).
                        </p>


                        <div class="flex items-center gap-4">

                            <label class="flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-700 rounded-xl cursor-pointer hover:bg-blue-600 hover:text-white transition-all font-semibold text-sm shadow-sm active:scale-95">

                                <input
                                    type="file"
                                    name="bukti"
                                    class="sr-only"
                                    accept="application/pdf,image/*"
                                    @change="fileName = $event.target.files[0]?.name || ''"
                                >

                                <i data-lucide="file-up" class="w-4 h-4"></i>

                                Pilih File (PDF/Gambar)

                            </label>


                            <span class="text-xs text-gray-400 italic truncate max-w-[250px]">

                                <template x-if="fileName">

                                    <span
                                        class="text-blue-600 font-bold"
                                        x-text="'Terpilih: ' + fileName"
                                    ></span>

                                </template>


                                <template x-if="!fileName">

                                    <span>

                                        <?php if (!empty($pemasukan['bukti'])): ?>

                                            File saat ini:

                                            <a
                                                href="<?= base_url('uploads/donasi/bukti/' . $pemasukan['bukti']) ?>"
                                                target="_blank"
                                                class="text-blue-600 underline font-bold"
                                            >
                                                Lihat File
                                            </a>

                                        <?php else: ?>

                                            Tidak ada file dipilih

                                        <?php endif; ?>

                                    </span>

                                </template>

                            </span>

                        </div>


                        <?php if (isset(session('errors')['bukti'])): ?>

                            <p class="text-xs text-red-500 mt-2 font-bold">
                                <?= session('errors')['bukti'] ?>
                            </p>

                        <?php endif; ?>

                    </div>



                    <!-- =================================================
                         WAKTU PENERIMAAN & PENGURUS
                    ================================================== -->

                    <?php if ($showHistori): ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/70 p-6 rounded-3xl border border-gray-100">

                            <!-- Waktu -->

                            <div>

                                <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">
                                    Waktu Penerimaan
                                </label>

                                <input
                                    type="datetime-local"
                                    name="waktu_penerimaan"
                                    value="<?= old(
                                        'waktu_penerimaan',
                                        date('Y-m-d\TH:i')
                                    ) ?>"
                                    class="w-full px-4 py-3 rounded-2xl border <?= isset(session('errors')['waktu_penerimaan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> bg-white text-sm"
                                >

                                <?php if (isset(session('errors')['waktu_penerimaan'])): ?>

                                    <p class="text-xs text-red-500 mt-2 font-bold">
                                        <?= session('errors')['waktu_penerimaan'] ?>
                                    </p>

                                <?php endif; ?>

                            </div>



                            <!-- Pengurus -->

                            <div>

                                <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">
                                    Nama Pengurus
                                </label>

                                <?php
                                $currentPengurus = old(
                                    'nama_pengurus',
                                    session()->get('nama') ?? 'Administrator'
                                );
                                ?>

                                <select
                                    name="nama_pengurus"
                                    class="w-full select2-dynamic"
                                >

                                    <option
                                        value="<?= esc($currentPengurus) ?>"
                                        selected
                                    >
                                        <?= esc($currentPengurus) ?>
                                    </option>

                                    <?php foreach ($pengurusList as $p): ?>

                                        <?php if (
                                            !empty($p['nama_pengurus']) &&
                                            $p['nama_pengurus'] !== $currentPengurus
                                        ): ?>

                                            <option value="<?= esc($p['nama_pengurus']) ?>">
                                                <?= esc($p['nama_pengurus']) ?>
                                            </option>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </select>


                                <?php if (isset(session('errors')['nama_pengurus'])): ?>

                                    <p class="text-xs text-red-500 mt-2 font-bold">
                                        <?= session('errors')['nama_pengurus'] ?>
                                    </p>

                                <?php endif; ?>

                                <p class="text-[11px] text-gray-400 mt-1">
                                    Bisa dipilih dari riwayat atau diketik baru.
                                </p>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>



            <!-- =========================================================
                 SUBMIT
            ========================================================== -->

            <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end">

                <button
                    type="submit"
                    class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3"
                >

                    <i data-lucide="save" class="w-5 h-5"></i>

                    SIMPAN DATA

                </button>

            </div>

        </form>

    </div>

</div>



<!-- =============================================================
     JAVASCRIPT
============================================================= -->

<script>

    /*
    |--------------------------------------------------------------------------
    | SELECT2 (dengan retry)
    |--------------------------------------------------------------------------
    | jQuery/Select2 di beberapa setup bundler (Vite/Rollup) di-load lewat
    | chunk async, jadi bisa saja belum siap tepat saat DOMContentLoaded
    | terpicu. Daripada gagal total sekali cek, kita retry beberapa kali
    | dengan jeda singkat sebelum benar-benar menyerah.
    |--------------------------------------------------------------------------
    */

    function initSelect2WhenReady(retries = 30) {

        if (window.jQuery && $.fn.select2) {

            $('.select2-basic').select2({
                width: '100%'
            });

            $('.select2-dynamic').select2({
                placeholder: 'Pilih atau Cari Calon Donatur...',
                allowClear: true,
                width: '100%'
            });

            return;

        }

        if (retries > 0) {

            setTimeout(
                () => initSelect2WhenReady(retries - 1),
                100
            );

            return;

        }

        console.warn(
            'jQuery/Select2 gagal dimuat setelah beberapa kali percobaan. ' +
            'Cek apakah chunk jquery/select2 memang ter-load di halaman ini.'
        );

    }

    document.addEventListener(
        'DOMContentLoaded',
        () => initSelect2WhenReady()
    );


    function donationIncomeForm() {

        return {

            /*
            |--------------------------------------------------------------------------
            | SOURCE
            |--------------------------------------------------------------------------
            */

            source: "<?= esc($source) ?>",


            /*
            |--------------------------------------------------------------------------
            | JENIS PENCATATAN
            |--------------------------------------------------------------------------
            */

            donationType: "<?= esc(
                old(
                    'donation_type',
                    $jenisPencatatan ?? 'donasi'
                )
            ) ?>",


            /*
            |--------------------------------------------------------------------------
            | TELEPON DONATUR
            |--------------------------------------------------------------------------
            */

            showPhoneAlert: false,

            hasTelepon: false,

            selectedTelepon: '',

            selectedDonaturName: '',


            /*
            |--------------------------------------------------------------------------
            | KWITANSI
            |--------------------------------------------------------------------------
            */

            akronimKwitansi:
                "<?= esc($akronimAwal) ?>",

            nomorKwitansi:
                "<?= esc($nomorAwal) ?>",


            /*
            |--------------------------------------------------------------------------
            | KETERANGAN
            |--------------------------------------------------------------------------
            */

            keteranganShared: "<?= esc(old('keterangan', $pemasukan['keterangan'] ?? ($keuangan['keterangan'] ?? ''))) ?>",


            /*
            |--------------------------------------------------------------------------
            | NOMINAL
            |--------------------------------------------------------------------------
            */

            rawJumlah:
                "<?= esc(
                    old(
                        'jumlah',
                        $prefillNominal
                            ? ($pemasukan['jumlah'] ?? '0')
                            : '0'
                    )
                ) ?>",

            displayJumlah: '',


            /*
            |--------------------------------------------------------------------------
            | UPLOAD
            |--------------------------------------------------------------------------
            */

            fileName: '',


            /*
            |--------------------------------------------------------------------------
            | ALOKASI
            |--------------------------------------------------------------------------
            */

            alokasiDetails: [],

            selectedDetailAlokasi: "<?= old('id_detail_alokasi', $keuangan['id_detail_alokasi'] ?? '') ?>",


            /*
            |--------------------------------------------------------------------------
            | INIT
            |--------------------------------------------------------------------------
            */

            init() {

                /*
                |--------------------------------------------------------------------------
                | NOMINAL
                |--------------------------------------------------------------------------
                */

                this.updateDisplayJumlah();


                /*
                |--------------------------------------------------------------------------
                | DONATUR FIX
                |
                | record
                | donor_record
                | edit
                |--------------------------------------------------------------------------
                */

                <?php if (
                    in_array(
                        $source,
                        ['record', 'donor_record', 'edit'],
                        true
                    )
                    && !empty($donatur)
                ): ?>

                    this.selectedDonaturName =
                        <?= json_encode(
                            $donatur['nama'] ?? ''
                        ) ?>;

                    this.selectedTelepon =
                        <?= json_encode(
                            trim(
                                $donatur['telepon'] ?? ''
                            )
                        ) ?>;

                    this.hasTelepon =
                        this.selectedTelepon !== '';

                    /*
                    |--------------------------------------------------------------------------
                    | PENTING
                    |
                    | Alert tetap muncul walaupun nomor kosong.
                    |--------------------------------------------------------------------------
                    */

                    this.showPhoneAlert = true;

                <?php endif; ?>


                /*
                |--------------------------------------------------------------------------
                | DONATUR SELECT2
                |
                | create
                |--------------------------------------------------------------------------
                */

                <?php if ($source === 'create'): ?>

                    this.initDonaturSelect();

                <?php endif; ?>


                /*
                |--------------------------------------------------------------------------
                | PROGRAM DONASI SELECT2 (tipe "donasi")
                |
                | donor_record
                |--------------------------------------------------------------------------
                */

                <?php if ($source === 'donor_record'): ?>

                    this.initDonasiSelect();

                <?php endif; ?>

                /*
                |--------------------------------------------------------------------------
                | CATATAN IKON
                |--------------------------------------------------------------------------
                | Tidak perlu lagi memanggil lucide.createIcons() manual di sini.
                | Semua ikon (termasuk ikon telepon/phone-off yang dulunya di-swap
                | lewat :data-lucide dinamis) sekarang dirender sebagai <i data-lucide>
                | STATIS sejak awal render, lalu di-toggle tampil/sembunyi via x-show.
                | Konversi <i> -> <svg> cukup dilakukan SEKALI oleh init global di
                | layout (layout/admin/main), dan itu sudah cukup untuk elemen yang
                | ada di HTML awal meski sedang disembunyikan lewat x-cloak/x-show.
                |
                | Memanggil lucide.createIcons() lagi di sini berisiko error kalau
                | window.lucide di proyek ini adalah build "core" (bukan build CDN
                | penuh) yang mewajibkan objek {icons} eksplisit, contoh:
                |   import { createIcons, icons } from 'lucide';
                |   createIcons({ icons });
                |--------------------------------------------------------------------------
                */

                <?php
                $initialAlokasi = old('id_alokasi', $keuangan['id_alokasi'] ?? '');
                ?>

                <?php if (in_array($source, ['donor_record', 'edit'], true) && $initialAlokasi !== ''): ?>
                    this.$nextTick(() => {
                        this.fetchDetailAlokasi(<?= json_encode((string) $initialAlokasi) ?>);
                    });
                <?php endif; ?>
            },


            /*
            |--------------------------------------------------------------------------
            | INIT SELECT2 DONATUR
            |--------------------------------------------------------------------------
            */

            initDonaturSelect(retries = 30) {

                const select = $('#donaturSelect');

                if (!select.length) {
                    return;
                }

                // Select2 menambahkan class ini setelah .select2() selesai dipasang
                if (!select.hasClass('select2-hidden-accessible')) {

                    if (retries > 0) {

                        setTimeout(
                            () => this.initDonaturSelect(retries - 1),
                            100
                        );

                    } else {

                        console.warn(
                            'Select2 untuk #donaturSelect tidak siap ' +
                            'setelah beberapa kali percobaan.'
                        );

                    }

                    return;
                }

                this.$nextTick(() => {


                    /*
                    |--------------------------------------------------------------------------
                    | HAPUS EVENT LAMA
                    |--------------------------------------------------------------------------
                    */

                    select.off('.donationIncome');


                    /*
                    |--------------------------------------------------------------------------
                    | SETIAP PERUBAHAN SELECT2
                    |--------------------------------------------------------------------------
                    */

                    select.on(
                        'change.donationIncome',
                        () => {

                            const value = select.val();


                            /*
                            |--------------------------------------------------------------------------
                            | KEMBALI KE OPTION KOSONG
                            |--------------------------------------------------------------------------
                            */

                            if (!value) {

                                this.resetPhoneAlert();

                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | AMBIL OPTION
                            |--------------------------------------------------------------------------
                            */

                            const option = select
                                .find('option:selected')
                                .get(0);


                            if (!option) {

                                this.resetPhoneAlert();

                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | CEK TELEPON
                            |--------------------------------------------------------------------------
                            */

                            this.triggerTeleponCheck(
                                option
                            );

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SELECT2 CLEAR
                    |--------------------------------------------------------------------------
                    */

                    select.on(
                        'select2:clear.donationIncome',
                        () => {

                            this.resetPhoneAlert();

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SELECT2 UNSELECT
                    |--------------------------------------------------------------------------
                    */

                    select.on(
                        'select2:unselect.donationIncome',
                        () => {

                            setTimeout(() => {

                                if (!select.val()) {

                                    this.resetPhoneAlert();

                                }

                            }, 0);

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | VALUE AWAL
                    |--------------------------------------------------------------------------
                    */

                    const initialValue = select.val();


                    if (!initialValue) {

                        this.resetPhoneAlert();

                        return;
                    }


                    const initialOption = select
                        .find('option:selected')
                        .get(0);


                    if (initialOption) {

                        this.triggerTeleponCheck(
                            initialOption
                        );

                    }

                });
            },


            /*
            |--------------------------------------------------------------------------
            | INIT SELECT2 PROGRAM DONASI (donor_record, tipe "donasi")
            |
            | Sama seperti initDonaturSelect(): Select2 memicu perubahan lewat event
            | jQuery, bukan native DOM 'change' yang didengar Alpine @change, jadi
            | bindingnya harus lewat jQuery .on('change') juga.
            |
            | Ditambah retry-wait karena Select2 diinisialisasi secara async lewat
            | initSelect2WhenReady() (lihat bagian atas script), jadi ada kemungkinan
            | Alpine init() ini jalan LEBIH DULU sebelum $('.select2-basic').select2()
            | benar-benar selesai dipasang.
            |--------------------------------------------------------------------------
            */

            initDonasiSelect(retries = 30) {

                const select = $('#donasiSelect');

                if (!select.length) {
                    return;
                }

                // Select2 menambahkan class ini setelah .select2() selesai dipasang
                if (!select.hasClass('select2-hidden-accessible')) {

                    if (retries > 0) {

                        setTimeout(
                            () => this.initDonasiSelect(retries - 1),
                            100
                        );

                    } else {

                        console.warn(
                            'Select2 untuk #donasiSelect tidak siap ' +
                            'setelah beberapa kali percobaan.'
                        );

                    }

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | HAPUS EVENT LAMA
                |--------------------------------------------------------------------------
                */

                select.off('.donationIncome');


                /*
                |--------------------------------------------------------------------------
                | SETIAP PERUBAHAN SELECT2
                |--------------------------------------------------------------------------
                */

                select.on(
                    'change.donationIncome',
                    () => {

                        const option = select
                            .find('option:selected')
                            .get(0);

                        this.applyDonasiSelection(option);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SELECT2 CLEAR
                |--------------------------------------------------------------------------
                */

                select.on(
                    'select2:clear.donationIncome',
                    () => {

                        this.applyDonasiSelection(null);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | VALUE AWAL (mis. setelah gagal validasi & old() terisi)
                |--------------------------------------------------------------------------
                */

                const initialValue = select.val();

                if (initialValue) {

                    const initialOption = select
                        .find('option:selected')
                        .get(0);

                    this.applyDonasiSelection(initialOption);

                }

            },

            /*
            |--------------------------------------------------------------------------
            | CEK TELEPON DONATUR
            |--------------------------------------------------------------------------
            */

            checkTeleponDonatur(event) {

                const select =
                    event.target;


                if (
                    !select ||
                    !select.value
                ) {

                    this.resetPhoneAlert();

                    return;

                }


                const option =
                    select.selectedOptions[0];


                this.triggerTeleponCheck(
                    option
                );

            },


            /*
            |--------------------------------------------------------------------------
            | TRIGGER CEK TELEPON
            |--------------------------------------------------------------------------
            */

            triggerTeleponCheck(option) {

                if (!option) {

                    this.resetPhoneAlert();

                    return;
                }


                const value = option.value;


                /*
                |--------------------------------------------------------------------------
                | JIKA OPTION KOSONG
                |--------------------------------------------------------------------------
                */

                if (!value || value === '') {

                    this.resetPhoneAlert();

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | NAMA
                |--------------------------------------------------------------------------
                */

                this.selectedDonaturName =
                    (option.dataset.nama || '').trim();


                /*
                |--------------------------------------------------------------------------
                | TELEPON
                |--------------------------------------------------------------------------
                */

                this.selectedTelepon =
                    (option.dataset.telepon || '').trim();


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                this.hasTelepon =
                    this.selectedTelepon !== '';

                this.showPhoneAlert = true;


                /*
                |--------------------------------------------------------------------------
                | KWITANSI AKTIF (khusus source = create)
                |
                | Kalau donatur yang dipilih ternyata SUDAH terdaftar aktif di program
                | donasi ini, pakai lagi nomor kwitansi lamanya (bukan generate baru).
                |--------------------------------------------------------------------------
                */

                if (this.source === 'create') {

                    this.applyKwitansiFromDataset(
                        option.dataset.kwitansi,
                        "<?= esc($akronimAwal) ?>"
                    );

                }
            },


            /*
            |--------------------------------------------------------------------------
            | RESET ALERT
            |--------------------------------------------------------------------------
            */

            resetPhoneAlert() {

                this.showPhoneAlert = false;
                this.hasTelepon = false;
                this.selectedTelepon = '';
                this.selectedDonaturName = '';

                // Kembalikan kwitansi ke default program ini saat donatur dikosongkan
                if (this.source === 'create') {
                    this.akronimKwitansi = "<?= esc($akronimAwal) ?>";
                    this.nomorKwitansi = '';
                }

            },


            /*
            |--------------------------------------------------------------------------
            | TERAPKAN KWITANSI DARI data-kwitansi (format tersimpan: "AKRONIM-NOMOR")
            |
            | Kalau raw kosong -> belum ada registrasi aktif, kembalikan ke akronim
            | default/fallback dan kosongkan nomor (nanti di-generate otomatis saat
            | disimpan bila tetap kosong).
            |--------------------------------------------------------------------------
            */

            applyKwitansiFromDataset(rawKwitansi, fallbackAkronim) {

                const raw = (rawKwitansi || '').trim();

                if (raw === '') {
                    this.akronimKwitansi = fallbackAkronim;
                    this.nomorKwitansi = '';
                    return;
                }

                const parts = raw.split('-');
                const nomor = parts.pop();
                const akronim = parts.join('-') || fallbackAkronim;

                this.akronimKwitansi = akronim;
                this.nomorKwitansi = nomor;

            },


            /*
            |--------------------------------------------------------------------------
            | JENIS PENCATATAN
            |--------------------------------------------------------------------------
            */

            onDonationTypeChange() {

                if (
                    this.source !==
                    'donor_record'
                ) {

                    return;

                }


                if (
                    this.donationType === 'kas' ||
                    this.donationType === 'tercatat'
                ) {

                    this.akronimKwitansi =
                        "<?= esc(
                            $akronimDefault
                        ) ?>";

                } else {

                    this.akronimKwitansi = '';

                }


                this.nomorKwitansi = '';

            },


            /*
            |--------------------------------------------------------------------------
            | PROGRAM DONASI DIPILIH
            |--------------------------------------------------------------------------
            */

            onDonasiTerpilih(event) {

                if (
                    this.source !==
                    'donor_record'
                ) {

                    return;

                }

                const option =
                    event.target.selectedOptions[0];

                this.applyDonasiSelection(option);

            },


            /*
            |--------------------------------------------------------------------------
            | TERAPKAN PILIHAN PROGRAM DONASI (akronim + kwitansi aktif jika ada)
            |
            | Dipanggil dari 2 jalur:
            | 1) @change Alpine biasa (fallback, tidak akan terpicu kalau elemen-nya
            |    sudah jadi widget Select2 -- lihat catatan di bindDonasiSelect())
            | 2) binding jQuery .on('change') dari bindDonasiSelect(), yang MEMANG
            |    diperlukan karena Select2 memicu event lewat jQuery, bukan lewat
            |    native addEventListener('change') yang didengar Alpine @change.
            |--------------------------------------------------------------------------
            */

            applyDonasiSelection(option) {

                if (this.donationType !== 'donasi') {
                    return;
                }

                if (!option || !option.value) {
                    this.akronimKwitansi = '';
                    this.nomorKwitansi = '';
                    return;
                }

                const fallbackAkronim =
                    option.dataset.akronim ||
                    "<?= esc($akronimDefault) ?>";

                this.applyKwitansiFromDataset(
                    option.dataset.kwitansi,
                    fallbackAkronim
                );

            },


            /*
            |--------------------------------------------------------------------------
            | FORMAT NOMINAL
            |--------------------------------------------------------------------------
            */

            formatJumlah(event) {

                let value =
                    event.target.value
                        .replace(/\D/g, '');


                this.rawJumlah =
                    value;


                this.updateDisplayJumlah();

            },


            /*
            |--------------------------------------------------------------------------
            | DISPLAY NOMINAL
            |--------------------------------------------------------------------------
            */

            updateDisplayJumlah() {

                if (
                    !this.rawJumlah ||
                    this.rawJumlah === '0'
                ) {

                    this.displayJumlah = '';

                    return;

                }


                this.displayJumlah =
                    new Intl.NumberFormat(
                        'id-ID'
                    ).format(
                        this.rawJumlah
                    );

            },


            /*
            |--------------------------------------------------------------------------
            | DETAIL ALOKASI
            |--------------------------------------------------------------------------
            */

            onAlokasiManualChange(idAlokasi) {
                this.selectedDetailAlokasi = '';
                this.fetchDetailAlokasi(idAlokasi);
            },

            async fetchDetailAlokasi(idAlokasi) {

                if (!idAlokasi) {
                    this.alokasiDetails = [];
                    this.selectedDetailAlokasi = '';
                    return;
                }

                try {
                    const response = await fetch(
                        "<?= base_url('admin/finance/get-detail-alokasi') ?>",
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: 'id_alokasi=' + encodeURIComponent(idAlokasi) +
                                '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
                        }
                    );

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    this.alokasiDetails = await response.json();

                    /*
                    |--------------------------------------------------------------------------
                    | FORCE RE-SYNC SELECT VALUE
                    |--------------------------------------------------------------------------
                    | x-model memasang efek reaktif hanya bergantung pada `selectedDetailAlokasi`,
                    | BUKAN pada `alokasiDetails`. Karena <option> baru muncul belakangan
                    | (setelah fetch async selesai), efek x-model yang lama sudah tidak
                    | pernah re-run untuk mencocokkan ulang <option> yang baru ter-render.
                    |
                    | Solusinya: tunggu DOM selesai update ($nextTick), lalu set langsung
                    | .value pada elemen <select> secara manual -- ini tidak melanggar
                    | x-model karena kita hanya menyamakan tampilan DOM dengan data yang
                    | SUDAH benar di this.selectedDetailAlokasi. Kalau user nanti ganti
                    | manual, native 'change' event tetap akan disinkronkan balik oleh
                    | x-model seperti biasa.
                    |--------------------------------------------------------------------------
                    */

                    this.$nextTick(() => {
                        if (this.$refs.detailAlokasiSelect) {
                            this.$refs.detailAlokasiSelect.value = this.selectedDetailAlokasi;
                        }
                    });

                } catch (error) {
                    console.error('Gagal mengambil data detail alokasi:', error);
                }
            }
        };

    }

</script>



<!-- =============================================================
     STYLE
============================================================= -->

<style>

[x-cloak] {
    display: none !important;
}


/*
|--------------------------------------------------------------------------
| Select2
|--------------------------------------------------------------------------
*/

.select2-container--default
.select2-selection--single {

    border-radius: 1rem !important;

    border: 1px solid #e5e7eb !important;

    height: 52px !important;

    display: flex !important;

    align-items: center !important;

    padding-left: 12px !important;

    background-color: #f9fafb !important;

}


.select2-container--default
.select2-selection--single
.select2-selection__arrow {

    height: 50px !important;

}



/*
|--------------------------------------------------------------------------
| Select2 Focus
|--------------------------------------------------------------------------
*/

.select2-container--default.select2-container--focus
.select2-selection--single {

    border-color: #3b82f6 !important;

    box-shadow:
        0 0 0 2px
        rgba(59, 130, 246, 0.15) !important;

}



/*
|--------------------------------------------------------------------------
| Jenis Pencatatan
|--------------------------------------------------------------------------
*/

label:has(input.peer:checked) {
    transform: translateY(-1px);
}


</style>


<?= $this->endSection() ?>