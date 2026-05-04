<!-- ===================== MAIN ===================== -->
<?= $this->include('layout/admin/head') ?>

<body 
    x-data="{ sidebarOpen: window.innerWidth >= 768 }" 
    @resize.window="sidebarOpen = window.innerWidth >= 768"
    class="bg-gray-100 font-sans antialiased overflow-x-hidden">

    <aside 
        :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-0 -translate-x-full md:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 bg-blue-600 border-r border-gray-200 transition-all duration-300 ease-in-out shadow-sm h-screen overflow-hidden">
        
        <div class="w-64 h-full">
            <?= $this->include('layout/admin/sidebar') ?>
        </div>
    </aside>

    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 md:hidden"
        x-cloak>
    </div>

    <div 
        :class="sidebarOpen ? 'md:pl-64' : 'md:pl-0'"
        class="flex flex-col min-h-screen transition-all duration-300 ease-in-out">
        
        <nav class="sticky top-0 z-30 bg-white border-b shadow-sm">
            <?= $this->include('layout/admin/navbar') ?>
        </nav>

        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>

        <?= $this->include('layout/admin/footer') ?>
    </div>

</body>
</html>