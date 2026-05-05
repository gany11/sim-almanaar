<footer class="bg-gray-900  text-white  pt-16 pb-8 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            
            <!-- BRAND -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-xl">
                        <i data-lucide="mosque" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-white font-black text-xl tracking-tighter uppercase">
                        Al-Manaar <span class="text-blue-600">Slipi</span>
                    </span>
                </div>
                <p class="text-sm  text-white  leading-relaxed italic">
                    Menjadi pusat kegiatan ibadah dan sosial yang transparan, modern, dan memberikan manfaat luas bagi jamaah di wilayah Slipi dan sekitarnya.
                </p>
            </div>

            <!-- NAVIGASI -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-6 border-l-4 border-blue-600 pl-3">
                    Navigasi
                </h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li>
                        <a href="<?= base_url() ?>" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('agenda') ?>" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Jadwal Kegiatan
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('keuangan') ?>" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Laporan Kas
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('berita') ?>" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Berita Masjid
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('artikel') ?>" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Artikel Dakwah
                        </a>
                    </li>
                </ul>
            </div>

            <!-- KONTAK -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-6 border-l-4 border-blue-600 pl-3">
                    Kontak Kami
                </h4>
                <ul class="space-y-4 text-sm leading-relaxed">
                    <li class="flex gap-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-blue-600 shrink-0"></i>
                        <span>Jl. Slipi I No.15A 7, RT.7/RW.2, Slipi, Palmerah, Jakarta Barat</span>
                    </li>
                    <li class="flex gap-3">
                        <i data-lucide="mail" class="w-5 h-5 text-blue-600 shrink-0"></i>
                        <span>info@almanaar-slipi.id</span>
                    </li>
                    <li class="flex gap-3">
                        <i data-lucide="phone" class="w-5 h-5 text-blue-600 shrink-0"></i>
                        <span>(021) 1234 5678</span>
                    </li>
                </ul>
            </div>

            <!-- MAP -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-6 border-l-4 border-blue-600 pl-3">
                    Lokasi
                </h4>
                <div class="rounded-2xl overflow-hidden grayscale hover:grayscale-0 transition-all duration-500 border border-gray-700 h-32 shadow-lg">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d247.90941436110037!2d106.80115990199546!3d-6.190831655764628!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f6916957addb%3A0x6b37d2db767833be!2sMasjid%20Al-Mannaar!5e0!3m2!1sid!2sid!4v1777985201848!5m2!1sid!2sid" 
                        class="w-full h-full border-0" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- COPYRIGHT -->
        <div class="pt-8 text-white border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] md:text-xs font-bold uppercase tracking-widest">
            <p>
                &copy; <?= date('Y') ?> 
                <strong>DKM AL Manaar Slipi</strong>. All rights reserved.
            </p>
            <div class="flex gap-6">
                <p class="hover:text-gray transition-colors">Privacy Policy</p>
                <p class="hover:text-gray transition-colors">Terms of Service</p>
            </div>
        </div>
    </div>
</footer>