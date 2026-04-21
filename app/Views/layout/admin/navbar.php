<!-- ===================== NAVBAR ===================== -->
<nav class="bg-white border-b shadow-sm px-4 py-3 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h1 class="font-semibold text-lg">Admin Panel</h1>
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2">
            <img src="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>" class="w-8 h-8 rounded-full">
            <span class="text-sm"><?= session()->get('nama') ?? 'Administrator' ?></span>
        </button>

        <div x-show="open" 
            @click.outside="open = false"
            x-transition
            class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">
            <a href="<?= base_url('admin/profil'); ?>" class="block px-4 py-2 text-sm hover:bg-gray-100">Profil Akun</a>
            <button onclick="confirmLogout()" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                Logout
            </button>
        </div>
    </div>
</nav>