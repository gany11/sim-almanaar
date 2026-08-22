<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 text-sm font-medium">
            <li>
                <a href="<?= base_url() ?>" class="text-gray-700 hover:text-blue-600 flex items-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                    Beranda
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <span class="ml-2 text-gray-400">Donasi</span>
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

    <!-- Coming Soon Card -->
    <div class="bg-white border border-gray-100 rounded-3xl shadow-sm p-10 md:p-16">
        <div class="flex flex-col items-center text-center">

            <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                <i data-lucide="hand-coins" class="w-12 h-12 text-blue-600"></i>
            </div>

            <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-1 text-sm font-semibold text-amber-700 mb-5">
                Coming Soon
            </span>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Halaman Rekapitulasi Donasi Sedang Dalam Pengembangan
            </h2>

            <p class="max-w-2xl text-gray-600 leading-relaxed">
                Halaman ini nantinya akan menyajikan informasi mengenai rekapitulasi donasi
                yang diterima Masjid Al Manaar Slipi secara transparan dan informatif.
                Saat ini fitur tersebut masih dalam proses pengembangan dan akan segera
                tersedia.
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

</div>

<?= $this->endSection() ?>