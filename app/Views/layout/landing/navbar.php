<nav x-data="{ open: false }" class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2">
                    <img class="h-8 w-auto" src="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>" alt="Logo">
                    <span class="font-bold text-blue-700">Masjid Al Manaar Slipi</span>
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-2">
                <a href="<?= base_url('/') ?>" 
                   class="px-4 py-2 rounded-lg transition-all duration-200 <?= url_is('/') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' ?>">
                    Beranda
                </a>
                
                <a href="<?= base_url('keuangan') ?>" 
                   class="px-4 py-2 rounded-lg transition-all duration-200 <?= url_is('keuangan*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' ?>">
                    Laporan Keuangan
                </a>

                <a href="<?= base_url('agenda') ?>" 
                   class="px-4 py-2 rounded-lg transition-all duration-200 <?= url_is('agenda*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' ?>">
                    Agenda
                </a>

                <a href="<?= base_url('berita') ?>" 
                   class="px-4 py-2 rounded-lg transition-all duration-200 <?= url_is('berita*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' ?>">
                    Berita
                </a>

                <a href="<?= base_url('artikel') ?>" 
                   class="px-4 py-2 rounded-lg transition-all duration-200 <?= url_is('artikel*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' ?>">
                    Artikel
                </a>
            </div>

            <!-- Mobile Button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <i data-lucide="menu" x-show="!open" class="w-6 h-6"></i>
                    <i data-lucide="x" x-show="open" class="w-6 h-6" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         @click.away="open = false" 
         class="md:hidden bg-white border-t border-gray-100 shadow-lg" x-cloak>
        <div class="px-4 pt-2 pb-4 space-y-1">
            <a href="<?= base_url('/') ?>" 
               class="block px-3 py-2.5 rounded-md text-base <?= url_is('/') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                Beranda
            </a>
            
            <a href="<?= base_url('keuangan') ?>" 
               class="block px-3 py-2.5 rounded-md text-base <?= url_is('keuangan*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                Laporan Keuangan
            </a>

            <a href="<?= base_url('agenda') ?>" 
               class="block px-3 py-2.5 rounded-md text-base <?= url_is('agenda*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                Agenda
            </a>

            <a href="<?= base_url('berita') ?>" 
               class="block px-3 py-2.5 rounded-md text-base <?= url_is('berita*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                Berita
            </a>

            <a href="<?= base_url('artikel') ?>" 
               class="block px-3 py-2.5 rounded-md text-base <?= url_is('artikel*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                Artikel
            </a>
        </div>
    </div>
</nav>