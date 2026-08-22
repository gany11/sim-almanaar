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
                    <span class="ml-2 text-gray-400">Artikel</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            ARTIKEL
        </h1>
        <p class="text-gray-500 mt-2 italic">
            Menambah wawasan dan ilmu melalui tulisan-tulisan bermanfaat.
        </p>
    </div>

    <?php if (empty($article)): ?>
        <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
            <i data-lucide="newspaper" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
            <p class="text-gray-400 italic">Belum ada artikel yang diterbitkan.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($article as $a): ?>
                <article class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="relative h-56 overflow-hidden">
                        <img src="<?= base_url('uploads/artikel/' . $a['sampul']) ?>" 
                                alt="<?= $a['judul'] ?>" 
                                class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            <span><?= format_indo($a['created_at'], 'full') ?></span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 leading-tight">
                            <?php 
                            $dateParam = date('Ymd', strtotime($a['created_at'])); 
                            ?>
                            <a href="<?= base_url('artikel/' . $dateParam . '/' . $a['slug']) ?>">
                                <?= $a['judul'] ?>
                            </a>
                        </h2>
                        <p class="text-gray-500 text-sm mb-6 line-clamp-3 italic leading-relaxed">
                            <?= strip_tags($a['deskripsi']) ?>
                        </p>
                        <div class="mt-auto pt-4 border-t border-gray-50">
                            <a href="<?= base_url('artikel/' . $dateParam . '/' . $a['slug']) ?>" class="text-blue-600 text-xs font-bold uppercase tracking-widest flex items-center gap-2 group">
                                Baca Selengkapnya 
                                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 flex justify-center">
            <?= $pager->links('article', 'custom_tailwind') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>