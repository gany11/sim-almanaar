<!-- ===================== SIDEBAR ===================== -->
<div class="flex flex-col h-screen bg-blue-600 border-r border-gray-200 sticky top-0">
    <div class="p-6 flex flex-col items-center justify-center bg-blue-600 shadow-md">
        <span class="text-xs uppercase tracking-widest text-white font-semibold opacity-80">
            Masjid Al Manaar
        </span>
        <h1 class="text-xl font-bold text-white mt-1">
            <?= session()->get('nama_peran') ?? 'Administrator' ?>
        </h1>
    </div>

    <nav class="flex-1 overflow-y-auto min-h-0 py-4 px-3 custom-scrollbar">
        <ul class="space-y-1">
            
            <li>
                <a href="<?= base_url('dashboard') ?>"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                <?= url_is('dashboard') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                    <i data-lucide="gauge" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Manajemen Publikasi -->
            <?php if (in_array(session()->get('id_peran'), [1, 2, 3])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white">Manajemen Publikasi</span>
                </li>

                <li x-data="{ open: <?= url_is('admin/berita*') ? 'true' : 'false' ?> }">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/berita*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="newspaper" class="w-5 h-5"></i>
                            <span class="font-medium">Berita</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <?php if (in_array(session()->get('id_peran'), [1, 2, 3])): ?>
                            <a href="<?= base_url('admin/berita') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/berita') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Berita</span>
                            </a>
                        <?php endif; ?>

                        <?php if (in_array(session()->get('id_peran'), [1, 3])): ?>
                            <a href="<?= base_url('admin/berita/tambah') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/berita/tambah') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Berita</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>

                <li x-data="{ open: <?= url_is('artikel*') ? 'true' : 'false' ?> }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('artikel*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            <span class="font-medium">Artikel</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <?php if (in_array(session()->get('id_peran'), [1, 2, 3])): ?>
                            <a href="<?= base_url('artikel') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('artikel') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Artikel</span>
                            </a>
                        <?php endif; ?>

                        <?php if (in_array(session()->get('id_peran'), [1, 3])): ?>
                            <a href="<?= base_url('artikel/create') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('artikel/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Artikel</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>


            <li class="pt-4 pb-1 px-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white">ZIS</span>
            </li>
            <li>
                <a href="#" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                <?= url_is('zis*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">Form Input ZIS</span>
                </a>
            </li>

            <li class="pt-4 pb-1 px-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white">Santunan</span>
            </li>
            <li>
                <a href="#" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                <?= url_is('donatur*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                    <i data-lucide="heart" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">Donatur</span>
                </a>
            </li>

            <!-- Manajemen Akun -->
            <?php if (in_array(session()->get('id_peran'), [1, 2])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">Manajemen Akun</span>
                </li>

                <li>
                    <a href="<?= base_url('admin/akun'); ?>" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                    <?= url_is('admin/akun') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        <i data-lucide="users-round" class="w-5 h-5"></i>
                        <span class="text-sm font-medium">Daftar Akun</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('admin/akun/registrasi'); ?>" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                    <?= url_is('admin/akun/registrasi') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span class="text-sm font-medium">Registrasi Akun</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>