<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Masjid Al-Manaar Slipi</title>
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <?= vite('public/js/landing.js') ?>
</head>
<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen">

    <!-- Konten Utama Halaman 404 -->
    <div class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center text-center md:text-left">
            
            <!-- Kolom Kiri: Ilustrasi GIF / Gambar -->
            <div class="flex items-center justify-center">
                <img src="<?= base_url('assets/images/maintenance.gif') ?>" alt="Halaman Tidak Ditemukan" class="w-full max-w-xs md:max-w-sm h-auto object-contain">
            </div>

            <!-- Kolom Kanan: Teks & Tombol Aksi -->
            <div class="flex flex-col justify-center">
                <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-2">404</h1>
                <h2 class="text-xl font-bold text-gray-700 mb-3">Halaman Tidak Ditemukan</h2>

                <p class="text-sm text-gray-500 leading-relaxed mb-8">
                    <?php if (ENVIRONMENT !== 'production' && !empty($message)) : ?>
                        <?= nl2br(esc($message)) ?>
                    <?php else : ?>
                        Maaf, halaman yang Anda cari tidak ditemukan, telah dihapus, atau alamat URL salah.
                    <?php endif; ?>
                </p>

                <!-- Tombol Aksi Kembali -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <a href="javascript:history.back()" class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                    </a>
                    <a href="<?= base_url() ?>" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all text-sm flex items-center justify-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Beranda
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Inisialisasi Ikon Lucide -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>