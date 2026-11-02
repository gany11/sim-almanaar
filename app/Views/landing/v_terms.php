<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
        
        <div class="border-b border-gray-100 pb-6 mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Ketentuan Penggunaan</span>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 uppercase tracking-tight mt-3">Syarat & Ketentuan Layanan</h1>
            <p class="text-xs text-gray-400 mt-1">Terakhir diperbarui: <?= date('d F Y') ?></p>
        </div>

        <div class="space-y-8 text-gray-600 text-sm md:text-base leading-relaxed">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">1. Ketentuan Umum</h2>
                <p>
                    Dengan mengakses dan menggunakan website resmi Masjid Al-Manaar Slipi, Anda dianggap telah memahami dan menyetujui seluruh Syarat & Ketentuan yang berlaku di dalam halaman ini. Jika Anda tidak setuju, mohon untuk tidak melanjutkan penggunaan situs ini.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">2. Hak Kekayaan Intelektual</h2>
                <p>
                    Seluruh konten yang terdapat di dalam website ini, termasuk namun tidak terbatas pada teks, logo, desain grafis, foto kegiatan, artikel dakwah, serta jadwal sholat dan tarawih, merupakan hak milik Masjid Al-Manaar Slipi atau pihak ketiga yang memberikan izin. Dilarang menyalin atau mendistribusikan ulang tanpa izin tertulis.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">3. Penggunaan yang Diizinkan</h2>
                <p>Anda setuju untuk menggunakan situs ini hanya untuk tujuan yang sah dan tidak melanggar hukum, dengan ketentuan:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Tidak merusak, melumpuhkan, atau membebani server dan sistem keamanan website.</li>
                    <li>Tidak menyebarkan informasi palsu atau konten yang mengandung unsur SARA dan kebencian melalui fasilitas interaksi di situs ini.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">4. Tautan ke Situs Eksternal</h2>
                <p>
                    Situs web kami mungkin menyediakan tautan ke situs pihak ketiga untuk kenyamanan informasi. Kami tidak bertanggung jawab atas isi, kebijakan privasi, atau praktik dari situs eksternal tersebut.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">5. Perubahan Ketentuan</h2>
                <p>
                    Pengurus Masjid Al-Manaar Slipi berhak sewaktu-waktu mengubah atau memperbarui syarat dan ketentuan ini tanpa pemberitahuan sebelumnya. Pengguna disarankan untuk memeriksa halaman ini secara berkala.
                </p>
            </section>
        </div>

        <div class="mt-12 pt-6 border-t border-gray-100 flex justify-between items-center">
            <a href="<?= base_url() ?>" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-xs flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
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