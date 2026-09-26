<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('layout/landing/head') ?>
</head>
<!-- Tambahkan overflow-hidden di sini agar halaman terkunci sejak awal -->
<body class="bg-gray-50 flex flex-col min-h-screen overflow-hidden">

    <!-- LOADING SCREEN DENGAN Z-[9999] -->
    <div id="loading-screen" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white transition-opacity duration-500 ease-out">
        <div class="relative flex items-center justify-center">
            <!-- Lingkaran luar berputar halus -->
            <div class="absolute -inset-3 rounded-full border-4 border-emerald-100 border-t-emerald-600 animate-spin"></div>
            
            <!-- Logo Masjid Al Manaar Slipi dengan efek denyut -->
            <img src="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>" 
                 alt="Logo Al Manaar" 
                 class="h-16 w-16 object-contain animate-pulse relative z-10">
        </div>
        <p class="mt-6 text-sm font-medium text-gray-500 animate-pulse tracking-wide">Memuat SIM Al Manaar Slipi...</p>
    </div>

    <?= view('layout/landing/navbar') ?>

    <main class="flex-grow">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('layout/landing/footer') ?>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>