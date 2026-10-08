<?= $this->extend($layout ?? 'layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <!-- Kontainer utama dengan layout responsif: 1 kolom di mobile, 2 kolom horizontal (Grid) di layar lebar/laptop -->
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center text-center md:text-left">
        
        <!-- Kolom Kiri (Laptop/Lebar) / Atas (Mobile): Ilustrasi GIF Maintenance -->
        <div class="flex items-center justify-center">
            <img src="<?= base_url('assets/images/maintenance.gif') ?>" alt="Sedang Pemeliharaan" class="w-full max-w-xs md:max-w-sm h-auto object-contain">
        </div>

        <!-- Kolom Kanan (Laptop/Lebar) / Bawah (Mobile): Teks dan Tombol Aksi -->
        <div class="flex flex-col justify-center">
            <!-- Judul -->
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 mb-3"><?= esc($title ?? 'Sedang Pemeliharaan') ?></h1>

            <!-- Pesan Dinamis -->
            <p class="text-sm text-gray-500 leading-relaxed mb-8">
                <?= $message ?>
            </p>

            <!-- Tombol Aksi Kembali -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                <a href="javascript:history.back()" class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                <?php if ($layout === 'layout/landing/main'): ?>
                    <a href="<?= base_url() ?>" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all text-sm flex items-center justify-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Beranda
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Inisialisasi ikon Lucide jika diperlukan pada view parsial ini -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
<?= $this->endSection() ?>