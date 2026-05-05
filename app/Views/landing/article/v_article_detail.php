<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="bg-white min-h-screen pb-20">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <nav class="flex mb-8 text-sm font-medium">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="<?= base_url() ?>" class="text-gray-700 hover:text-blue-600 flex items-center">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center text-gray-400">
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                    <a href="<?= base_url('artikel') ?>" class="hover:text-blue-600">Artikel</a>
                </li>
                <li class="flex items-center text-gray-400 truncate max-w-[200px] md:max-w-none">
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                    <span><?= $article['judul'] ?></span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <main class="lg:col-span-8">
                <header class="mb-8">
                    <h3 class="text-3xl md:text-5xl font-black text-gray-900 leading-tight mb-6 uppercase tracking-tight">
                        <?= $article['judul'] ?>
                    </h3>
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 border-y border-gray-100 py-4 font-medium italic">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                            <span><?= format_indo($article['created_at'], 'full') ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                            <span><?= $article['nama_admin'] ?? 'Admin Masjid' ?></span>
                        </div>
                        <?php if (!empty($article['sumber_penulis'])): ?>
                        <div class="flex items-center gap-2">
                            <i data-lucide="pen-tool" class="w-4 h-4 text-blue-600"></i>
                            <span>Sumber: <?= $article['sumber_penulis'] ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex items-center gap-2">
                            <i data-lucide="tag" class="w-4 h-4 text-blue-600"></i>
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Artikel</span>
                        </div>
                    </div>
                </header>

                <div class="rounded-3xl overflow-hidden mb-10 shadow-2xl shadow-blue-100">
                    <img src="<?= base_url('uploads/artikel/' . $article['sampul']) ?>" alt="<?= $article['judul'] ?>" class="w-full object-cover">
                </div>

                <div class="prose prose-lg prose-blue max-w-none text-gray-700 leading-relaxed font-serif italic">
                    <?= $article['deskripsi'] ?>
                </div>

                <?php if (!empty($article['lampiran'])): ?>
                <div class="mt-12 space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <i data-lucide="file-text" class="w-6 h-6 text-red-500"></i>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Lampiran Materi (PDF)</h3>
                    </div>
                    
                    <div class="w-full h-[500px] rounded-2xl overflow-hidden border border-gray-200 shadow-inner bg-gray-100">
                        <iframe src="<?= base_url('uploads/lampiran/' . $article['lampiran']) ?>#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-400 italic">Bagikan artikel ini kepada jamaah:</p>
                    <div class="flex gap-4">                        
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode("Assalamualaikum, mari baca artikel terbaru dari Masjid Al-Manaar: \n\n*" . $article['judul'] . "*\n\nSelengkapnya di: " . $current_url) ?>" 
                        target="_blank" title="Bagikan ke WhatsApp"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:shadow-emerald-200 transition-all duration-300 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </main>

            <aside class="lg:col-span-4 space-y-10">
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-blue-600 rounded-full"></span>
                        PUBLIKASI TERBARU
                    </h3>
                    <div class="space-y-6">
                        <?php foreach ($terbaru as $t): ?>
                            <?php 
                                $folder = ($t['id_jenis_publikasi'] == 1) ? 'berita' : 'artikel';
                                $dateParam = date('Ymd', strtotime($t['created_at']));
                            ?>
                            <div class="flex gap-4 group">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0 relative">
                                    <img src="<?= base_url('uploads/' . $folder . '/' . $t['sampul']) ?>" 
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute top-1 left-1">
                                        <span class="<?= $t['kategori_color'] ?> text-[8px] px-1.5 py-0.5 rounded-md font-bold uppercase">
                                            <?= $t['jenis_publikasi'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h4 class="text-sm font-bold text-gray-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="<?= base_url($folder . '/' . $dateParam . '/' . $t['slug']) ?>">
                                            <?= $t['judul'] ?>
                                        </a>
                                    </h4>
                                    <span class="text-[10px] text-gray-400 mt-1 font-medium italic">
                                        <?= format_indo($t['created_at'], 'full') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="bg-blue-600 rounded-3xl p-8 text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-xl font-bold mb-2 tracking-tight uppercase">Ingin Berkontribusi?</h3>
                        <p class="text-blue-100 text-sm mb-6 leading-relaxed italic opacity-90">Kirimkan tulisan atau artikel Islami Anda untuk dipublikasikan di website resmi Masjid Al-Manaar Slipi.</p>
                        
                        <a href="mailto:info@almanaar-slipi.id?subject=Kontribusi Artikel: [Judul Artikel]&body=Assalamu'alaikum Pengurus Masjid Al-Manaar," 
                        class="inline-flex items-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-blue-50 transition-colors">
                            <i data-lucide="mail" class="w-4 h-4"></i> Hubungi Kami
                        </a>
                    </div>
                    <i data-lucide="send" class="absolute -bottom-4 -right-4 w-32 h-32 text-white/10 group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform duration-700"></i>
                </div>
            </aside>
        </div>
    </div>
</div>
<?= $this->endSection() ?>