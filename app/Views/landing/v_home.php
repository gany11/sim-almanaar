<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<section class="relative overflow-hidden bg-gray-900">
    <div class="swiper hero-swiper w-full h-[400px] md:h-[600px]">
        <div class="swiper-wrapper">
            <?php if (empty($carousels)): ?>
                <div class="swiper-slide relative">
                    <img src="<?= base_url('uploads/carousel/Latar Al Manaar.png') ?>" class="w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 flex items-center justify-center text-center p-4">
                        <h2 class="text-white text-3xl md:text-5xl font-black uppercase tracking-tighter">Selamat Datang di <br>Masjid Al-Manaar Slipi</h2>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($carousels as $c): ?>
                <div class="swiper-slide">
                    <img src="<?= base_url('uploads/carousel/' . $c['file']) ?>" class="w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 flex items-center justify-center text-center p-4">
                        <h2 class="text-white text-3xl md:text-5xl font-black uppercase tracking-tighter">Selamat Datang di <br>Masjid Al-Manaar Slipi</h2>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 -mt-16 relative z-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <?php foreach ($finance as $f): ?>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
            
            <div class="absolute top-0 left-0 right-0 h-1.5 <?= !empty($f['class_color']) ? str_replace('text', 'bg', $f['class_color']) : 'bg-blue-600' ?> opacity-20"></div>

            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 rounded-2xl transition-transform group-hover:scale-110 duration-300 <?= !empty($f['class_color']) ? $f['class_color'] . ' bg-opacity-10' : 'bg-blue-50 text-blue-600' ?>">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
                <span class="text-[20px] font-black text-gray-400 uppercase tracking-widest leading-tight" title="<?= $f['kategori'] ?>">
                    <?= $f['kategori'] ?>
                </span>
            </div>

            <div class="flex items-baseline gap-1">
                <span class="text-xs font-bold text-gray-400">Rp</span>
                <h3 class="text-2xl font-black text-gray-900 tracking-tighter leading-none">
                    <?= number_format($f['saldo'] ?? 0, 0, ',', '.') ?>
                </h3>
            </div>

            <div class="flex justify-between items-center mt-4">
                <p class="text-[10px] text-gray-400 italic">Total Saldo Saat Ini</p>
                <div class="w-2 h-2 rounded-full animate-pulse <?= !empty($f['class_color']) ? str_replace('text', 'bg', $f['class_color']) : 'bg-blue-600' ?>"></div>
            </div>

            <div class="absolute bottom-0 left-0 h-1 w-0 group-hover:w-full <?= !empty($f['class_color']) ? str_replace('text', 'bg', $f['class_color']) : 'bg-blue-600' ?> transition-all duration-500"></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 py-20 grid grid-cols-1 lg:grid-cols-12 gap-12">
    
    <aside class="lg:col-span-4">
        <div class="sticky top-24">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-8 w-1.5 bg-blue-600 rounded-full shadow-sm shadow-blue-200"></div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Agenda <span class="text-blue-600">Mendatang</span></h2>
            </div>
            
            <div class="space-y-4">
                <?php if(empty($agendas)): ?>
                    <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-8 text-center">
                        <i data-lucide="calendar-off" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                        <p class="text-gray-400 italic text-sm">Belum ada agenda mendatang dalam waktu dekat.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($agendas as $a): ?>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-100 transition-all duration-300 group">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center justify-center w-12 h-14 bg-blue-600 text-white rounded-xl shrink-0 shadow-lg shadow-blue-100 group-hover:scale-105 transition-transform">
                                <span class="text-lg font-black leading-none"><?= date('d', strtotime($a['waktu_mulai'])) ?></span>
                                <span class="text-[8px] uppercase font-bold tracking-widest mt-1 opacity-80"><?= date('M', strtotime($a['waktu_mulai'])) ?></span>
                            </div>
                            
                            <div class="overflow-hidden flex-1">
                                <span class="px-2 py-0.5 <?= $a['class_color'] ?? 'bg-gray-100 text-gray-600' ?> text-[8px] font-bold uppercase rounded-md mb-1.5 inline-block tracking-tighter">
                                    <?= $a['nama_kategori'] ?>
                                </span>
                                
                                <h4 class="text-sm font-bold text-gray-800 leading-tight mb-2">
                                    <?= $a['judul'] ?: '' ?>
                                    <?php if($a['judul'] && $a['tema']): ?> 
                                        <span class="text-gray-300 font-light mx-1">|</span> 
                                    <?php endif; ?>
                                    <span class="text-blue-600 italic font-semibold"><?= $a['tema'] ?></span>
                                </h4>

                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-medium italic">
                                        <i data-lucide="clock" class="w-3 h-3 text-blue-400"></i> 
                                        <span>
                                            <?= date('H:i', strtotime($a['waktu_mulai'])) ?> 
                                            <?= $a['waktu_selesai'] ? ' - ' . date('H:i', strtotime($a['waktu_selesai'])) : ' s/d Selesai' ?> WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-500 font-medium">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-red-400"></i> 
                                        <span class="truncate"><?= $a['tempat'] ?? 'Masjid Al-Manaar' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <a href="<?= base_url('agenda') ?>" class="group flex items-center justify-center gap-2 w-full py-3.5 bg-gray-50 hover:bg-blue-600 text-gray-500 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 mt-6 border border-gray-100">
                    <i data-lucide="calendar" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                    Lihat Semua Kalender
                </a>
            </div>
        </div>
    </aside>

    <main class="lg:col-span-8 space-y-16">
        
        <section>
            <div class="flex justify-between items-end mb-8 border-b border-gray-100 pb-4">
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Berita <span class="text-blue-600">Kegiatan</span></h2>
                <a href="<?= base_url('berita') ?>" class="text-blue-600 text-xs font-bold uppercase tracking-widest hover:underline">Semua Berita</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($latest_news as $n): ?>
                <div class="group cursor-pointer" onclick="location.href='<?= base_url('berita/'.date('Ymd', strtotime($n['created_at'])).'/'.$n['slug']) ?>'">
                    <div class="relative h-48 rounded-3xl overflow-hidden mb-4 shadow-lg">
                        <img src="<?= base_url('uploads/berita/'.$n['sampul']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest"><?= format_indo($n['created_at']) ?></span>
                    <h3 class="font-bold text-gray-900 mt-1 group-hover:text-blue-600 transition-colors line-clamp-2 uppercase italic leading-tight"><?= $n['judul'] ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <div class="flex justify-between items-end mb-8 border-b border-gray-100 pb-4">
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Artikel <span class="text-blue-600">Dakwah</span></h2>
                <a href="<?= base_url('artikel') ?>" class="text-blue-600 text-xs font-bold uppercase tracking-widest hover:underline">Semua Artikel</a>
            </div>
            <div class="space-y-6">
                <?php foreach($latest_article as $a): ?>
                <div class="flex gap-6 items-center group cursor-pointer" onclick="location.href='<?= base_url('artikel/'.date('Ymd', strtotime($a['created_at'])).'/'.$a['slug']) ?>'">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl overflow-hidden flex-shrink-0 shadow-md">
                        <img src="<?= base_url('uploads/artikel/'.$a['sampul']) ?>" class="w-full h-full object-cover group-hover:rotate-3 group-hover:scale-110 transition-all duration-500">
                    </div>
                    <div>
                        <span class="bg-blue-50 text-blue-600 text-[9px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter mb-2 inline-block">Artikel</span>
                        <h3 class="font-bold text-gray-900 md:text-lg group-hover:text-blue-600 transition-colors line-clamp-2 uppercase italic leading-tight"><?= $a['judul'] ?></h3>
                        <p class="text-gray-400 text-xs mt-2 italic"><?= format_indo($a['created_at']) ?> • Oleh <?= $a['sumber_penulis'] ?? 'Admin' ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>
</div>

<?= $this->endSection() ?>