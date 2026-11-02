<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
        
        <div class="border-b border-gray-100 pb-6 mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Legal & Keamanan</span>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 uppercase tracking-tight mt-3">Kebijakan Privasi</h1>
            <p class="text-xs text-gray-400 mt-1">Terakhir diperbarui: <?= date('d F Y') ?></p>
        </div>

        <div class="space-y-8 text-gray-600 text-sm md:text-base leading-relaxed">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">1. Pendahuluan</h2>
                <p>
                    Masjid Al-Manaar Slipi berkomitmen untuk melindungi dan menghargai privasi para jamaah serta pengunjung situs web kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat mengakses platform digital kami.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">2. Informasi yang Kami Kumpulkan</h2>
                <p>Kami dapat mengumpulkan informasi berikut:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Data interaksi dan navigasi halaman web melalui *cookie* atau log sistem.</li>
                    <li>Informasi yang Anda masukkan secara sukarela saat mengisi formulir kontak, konfirmasi donasi, atau pendaftaran kegiatan (seperti nama, nomor telepon, dan alamat email).</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">3. Penggunaan Informasi</h2>
                <p>Informasi yang terkumpul digunakan untuk:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Mengelola layanan administrasi, laporan keuangan, dan jadwal kegiatan masjid.</li>
                    <li>Mengirimkan informasi pengumuman penting, jadwal tarawih, atau kegiatan dakwah.</li>
                    <li>Meningkatkan kualitas layanan dan pengalaman pengguna di situs web.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">4. Keamanan Data</h2>
                <p>
                    Kami menerapkan langkah-langkah keamanan administratif, teknis, dan fisik yang wajar untuk melindungi data pribadi Anda dari akses yang tidak sah, pengungkapan, atau perubahan.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">5. Hubungi Kami</h2>
                <p>
                    Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan menghubungi pengurus Masjid Al-Manaar Slipi melalui kontak resmi yang tersedia.
                </p>
            </section>
        </div>

        <div class="mt-12 pt-6 border-t border-gray-100 flex justify-between items-center">
            <a href="<?= base_url() ?>" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-xs flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"><span></i> Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
<?= $this->endSection() ?>