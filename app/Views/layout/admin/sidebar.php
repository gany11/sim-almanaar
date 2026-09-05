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
                <a href="<?= base_url('admin/dashboard') ?>"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                <?= url_is('admin/dashboard') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                    <i data-lucide="gauge" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Manajemen Publikasi -->
            <?php if (in_array(session()->get('id_peran'), [2, 3])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white">Manajemen Publikasi</span>
                </li>

                <li x-data="{ open: <?= url_is('admin/news*') ? 'true' : 'false' ?> }">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/news*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="newspaper" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Berita</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/news') ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/news') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Berita</span>
                        </a>

                        <?php if (in_array(session()->get('id_peran'), [3])): ?>
                            <a href="<?= base_url('admin/news/create') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/news/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Berita</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>

                <li x-data="{ open: <?= url_is('admin/article*') ? 'true' : 'false' ?> }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/article*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Artikel</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/article') ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/article') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Artikel</span>
                        </a>

                        <?php if (in_array(session()->get('id_peran'), [3])): ?>
                            <a href="<?= base_url('admin/article/create') ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/article/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Artikel</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>

            <!-- ===================== MANAJEMEN AGENDA ===================== -->
            <?php if (in_array(session()->get('id_peran'), [2, 3, 5])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">Manajemen Agenda</span>
                </li>

                <!-- Menu 1: Agenda Biasa / Insidental -->
                <li x-data="{ open: <?= (url_is('admin/agenda') || url_is('admin/agenda/*')) && !url_is('admin/routine-agenda*') ? 'true' : 'false' ?> }">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= (url_is('admin/agenda') || url_is('admin/agenda/*')) && !url_is('admin/routine-agenda*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar-days" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Agenda Umum</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/agenda'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/agenda') || url_is('admin/agenda/edit/*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Agenda</span>
                        </a>

                        <?php if (in_array(session()->get('id_peran'), [3, 5])): ?>
                            <a href="<?= base_url('admin/agenda/create'); ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/agenda/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Agenda</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>

                <!-- Menu 2: Agenda Rutin -->
                <li x-data="{ open: <?= url_is('admin/routine-agenda*') ? 'true' : 'false' ?> }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/routine-agenda*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Agenda Rutin</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/routine-agenda'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/routine-agenda') || url_is('admin/routine-agenda/edit/*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Agenda Rutin</span>
                        </a>

                        <?php if (in_array(session()->get('id_peran'), [3, 5])): ?>
                            <a href="<?= base_url('admin/routine-agenda/create'); ?>"
                            class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                            <?= url_is('admin/routine-agenda/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Agenda Rutin</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Keuangan -->
            <?php if (in_array(session()->get('id_peran'), [2,3,4])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white">Keuangan</span>
                </li>

                <?php if (in_array(session()->get('id_peran'), [4])): ?>
                    <li>
                        <a href="<?= base_url('admin/finance/data') ?>" 
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/finance/data*') ? 'bg-white text-blue-600 shadow-lg font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Pengolahan Data</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (in_array(session()->get('id_peran'), [2,3,4])): ?>
                    <li x-data="{ open: <?= url_is('admin/finance/report*') ? 'true' : 'false' ?> }">
                        <button @click="open = !open"
                            class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                            <?= url_is('admin/finance/report*') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            
                            <div class="flex items-center gap-3">
                                <i data-lucide="file-pie-chart" class="w-5 h-5"></i>
                                <span class="text-sm font-medium">Laporan</span>
                            </div>

                            <i data-lucide="chevron-right" 
                            class="w-4 h-4 transition-transform duration-300" 
                            :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                        </button>

                        <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                            <a href="<?= base_url('admin/finance/report/weekly') ?>"
                                class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                                <?= url_is('admin/finance/report/weekly*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Laporan Mingguan</span>
                            </a>

                            <?php if (in_array(session()->get('id_peran'), [4])): ?>
                                <a href="<?= base_url('admin/finance/report/monthly') ?>"
                                    class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                                    <?= url_is('admin/finance/report/monthly*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                    <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Laporan Bulanan</span>
                                </a>
                                <a href="<?= base_url('admin/finance/report/periodic') ?>"
                                    class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                                    <?= url_is('admin/finance/report/periodic') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                    <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Laporan Periodik</span>
                                </a>
                                <a href="<?= base_url('admin/finance/report/chart') ?>"
                                    class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                                    <?= url_is('admin/finance/report/chart') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                                    <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Grafik Statistik</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <!-- <li>
                        <a href="<?= base_url('admin/finance/report/periodic') ?>" 
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= url_is('admin/finance/report/periodic*') ? 'bg-white text-blue-600 shadow-lg font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="file-pie-chart" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Laporan Periodik</span>
                        </a>
                    </li> -->
                <?php endif; ?>
            <?php endif; ?>

            <!-- ===================== MANAJEMEN DONASI & DONATUR ===================== -->
            <?php if (in_array(session()->get('id_peran'), [4])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">Manajemen Donasi</span>
                </li>

                <!-- Menu 1: Donatur -->
                <li x-data="{ open: <?= (url_is('admin/donors') || url_is('admin/donors/*')) ? 'true' : 'false' ?> }">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= (url_is('admin/donors') || url_is('admin/donors/*')) ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Donatur</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/donors'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/donors') || url_is('admin/donors/edit/*') || url_is('admin/donors/detail/*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Donatur</span>
                        </a>

                        <a href="<?= base_url('admin/donors/create'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/donors/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Donatur</span>
                        </a>
                    </div>
                </li>

                <!-- Menu 2: Program Donasi -->
                <li x-data="{ open: <?= (url_is('admin/donations') || url_is('admin/donations/*')) ? 'true' : 'false' ?> }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg transition-all duration-200 
                        <?= (url_is('admin/donations') || url_is('admin/donations/*')) ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        
                        <div class="flex items-center gap-3">
                            <i data-lucide="heart-handshake" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Program Donasi</span>
                        </div>

                        <i data-lucide="chevron-right" 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="open ? 'rotate-90 text-blue-600' : 'text-current'"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="mt-2 space-y-1">
                        <a href="<?= base_url('admin/donations'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/donations') || url_is('admin/donations/edit/*') || url_is('admin/donations/detail/*') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="circle" class="w-2 h-2"></i> <span class="text-sm">Daftar Donasi</span>
                        </a>

                        <a href="<?= base_url('admin/donations/create'); ?>"
                        class="flex items-center gap-3 ml-4 px-4 py-2 rounded-lg transition-all duration-200 
                        <?= url_is('admin/donations/create') ? 'bg-white text-blue-600 font-bold' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                            <i data-lucide="plus" class="w-2 h-2"></i> <span class="text-sm">Tambah Donasi</span>
                        </a>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Manajemen Akun -->
            <?php if (in_array(session()->get('id_peran'), [1, 2])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">Manajemen Akun</span>
                </li>

                <li>
                    <a href="<?= base_url('admin/account'); ?>" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                    <?= url_is('admin/account') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        <i data-lucide="users-round" class="w-5 h-5"></i>
                        <span class="text-sm font-medium">Daftar Akun</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('admin/account/register'); ?>" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 
                    <?= url_is('admin/account/register') ? 'bg-white text-blue-600 shadow-lg' : 'text-white hover:bg-white hover:text-blue-600' ?>">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span class="text-sm font-medium">Registrasi Akun</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Manajemen WA -->
            <?php if (in_array(session()->get('id_peran'), [1])): ?>
                <li class="pt-4 pb-1 px-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">Koneksi WhatsApp</span>
                </li>

                <li>
                    <a href="<?= base_url('admin/whatsapp'); ?>"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200
                        <?= url_is('admin/whatsapp*') 
                            ? 'bg-white text-blue-600 shadow-lg' 
                            : 'text-white hover:bg-white hover:text-blue-600' ?>">

                        <i data-lucide="message-circle" class="w-5 h-5"></i>

                        <span class="text-sm font-medium">
                            Koneksi WhatsApp
                        </span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>