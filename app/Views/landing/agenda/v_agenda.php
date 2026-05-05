<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 text-sm font-medium">
            <li>
                <a href="<?= base_url() ?>" class="text-gray-700 hover:text-blue-600 flex items-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i> Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <span class="ml-2 text-gray-400">Agenda Kegiatan</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">AGENDA KEGIATAN</h1>
        <p class="text-gray-500 mt-1 italic">Daftar jadwal kajian, perayaan hari besar, dan kegiatan sosial Masjid Al-Manaar.</p>
    </div>

    <div class="bg-white p-4 md:p-8 rounded-3xl shadow-sm border border-gray-100">
        <div id="calendar-loading" class="hidden text-center py-4 text-blue-600 font-bold animate-pulse">
            Memperbarui Agenda...
        </div>
        
        <div id="calendar" class="min-h-[600px]"></div>
    </div>
</div>

<div x-data="{ open: false, category: '', theme: '', title: '', time: '', loc: '', speaker: '', desc: '' }" 
     @open-agenda.window="
        open = true; 
        category = $event.detail.category;
        theme = $event.detail.theme;
        title = $event.detail.title; 
        time = $event.detail.time; 
        loc = $event.detail.loc;
        speaker = $event.detail.speaker;
        desc = $event.detail.desc;
     ">
    
    <div x-show="open" 
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition.opacity>
        
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl" @click.away="open = false">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase rounded-full" x-text="category"></span>
                <button @click="open = false" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tema / Judul</p>
                    <h3 class="text-xl font-bold text-gray-900">
                        <span x-text="theme"></span> 
                        <template x-if="title">
                            <span class="text-gray-500 font-medium" x-text="' (' + title + ')'"></span>
                        </template>
                    </h3>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-600 mt-1"></i>
                        <div class="text-xs text-gray-600">
                            <p class="font-bold">Waktu</p>
                            <p x-text="time"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 mt-1"></i>
                        <div class="text-xs text-gray-600">
                            <p class="font-bold">Tempat</p>
                            <p x-text="loc"></p>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pengisi / Pengajar</p>
                    <div class="text-sm text-gray-800 font-medium flex flex-col gap-1" x-html="speaker"></div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Deskripsi</p>
                    <div class="text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none" x-html="desc"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && window.FullCalendar) {
    }
});
</script>
<?= $this->endSection() ?>