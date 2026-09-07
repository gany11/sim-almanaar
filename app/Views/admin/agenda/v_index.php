<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="{ openImport: false }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Agenda</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola jadwal kegiatan, penugasan SDM, dan lokasi acara masjid.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [3,5])): ?>
            <!-- <a href="<?= base_url('admin/agenda/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Agenda
            </a> -->
            <div class="flex flex-wrap items-center gap-3">
                <button @click="openImport = true" 
                    class="flex items-center gap-2 px-5 py-2.5 bg-white border border-emerald-200 text-emerald-600 rounded-xl font-bold text-sm hover:bg-emerald-50 transition-all active:scale-95 shadow-sm shadow-emerald-50">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> 
                    <span>Import Excel</span>
                </button>

                <a href="<?= base_url('admin/agenda/create') ?>" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                    <span>Tambah Agenda</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div id="flash-error" data-message="<?= session()->getFlashdata('error') ?>"></div>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error_list')) : ?>
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-2xl shadow-sm">
            <div class="flex items-center gap-2 text-red-700 font-bold mb-3">
                <i data-lucide="list-x" class="w-5 h-5"></i>
                <span class="text-sm">Gagal Import! Periksa Baris Berikut:</span>
            </div>
            
            <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                <ul class="space-y-1.5">
                    <?php foreach (session()->getFlashdata('error_list') as $err) : ?>
                        <li class="flex items-start gap-2 text-xs text-red-600 leading-relaxed">
                            <span class="mt-1 w-1 h-1 rounded-full bg-red-400 shrink-0"></span>
                            <?= $err ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="mt-4 pt-3 border-t border-red-100">
                <p class="text-[10px] text-red-500 italic font-medium">
                    * Sistem tidak memasukkan data apa pun. Silakan perbaiki file Excel Anda dan coba lagi.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 mx-4 md:mx-0">
        <div id="calendar-loading" class="hidden text-center py-4 text-blue-600 font-bold animate-pulse">
            Memperbarui Agenda...
        </div>
        
        <div id="calendar" class="min-h-[600px]"></div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 mx-4 md:mx-0">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Kategori</label>
                <select id="filter-kategori" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id_kategori_agenda'] ?>"><?= $cat['nama_kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table id="tableAgenda" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Info Agenda</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Pengisi / SDM</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Tempat</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-data" class="text-gray-700">
                    </tbody>
            </table>
        </div>
    </div>

    <div x-show="openImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800">Import Data Agenda</h3>
                <button @click="openImport = false" class="text-gray-400 hover:text-red-500"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <form action="<?= base_url('admin/agenda/import-excel') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                    <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider mb-1">Perhatian</p>
                    <p class="text-xs text-amber-600 leading-relaxed">Gunakan template resmi untuk menghindari error. Pastikan sesuai dengan ketentuan yang berlaku.</p>
                </div>
    
                <div 
                    x-data="{ fileName: null }" 
                    class="border-2 border-dashed rounded-2xl p-8 text-center transition-all relative"
                    :class="fileName ? 'border-emerald-400 bg-emerald-50/50' : 'border-gray-200 hover:border-blue-400'"
                >
                    <input type="file" name="file_excel" 
                        class="absolute inset-0 opacity-0 cursor-pointer" 
                        accept=".xlsx, .xls"
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null"
                    >
                    
                    <template x-if="!fileName">
                        <div>
                            <i data-lucide="upload-cloud" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-gray-500">Klik atau seret file Excel ke sini</p>
                            <p class="text-[10px] text-gray-400 mt-1">Format: .xlsx atau .xls</p>
                        </div>
                    </template>

                    <template x-if="fileName">
                        <div class="flex flex-col items-center animate-in fade-in zoom-in duration-300">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-2">
                                <i data-lucide="file-check" class="w-6 h-6"></i>
                            </div>
                            <p class="text-sm font-bold text-emerald-700 truncate max-w-full px-4" x-text="fileName"></p>
                            <button type="button" @click.stop="fileName = null; $el.closest('form').reset()" class="mt-2 text-[10px] font-bold text-red-500 uppercase hover:underline">Ganti File</button>
                        </div>
                    </template>
                </div>
    
                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">MULAI IMPORT</button>
                    <a href="<?= base_url('assets/templates/Format Import Data Agenda.xlsx') ?>" class="text-center py-2 text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors">Download Template Excel</a>
                </div>
            </form>
        </div>
    </div>
    <div x-data="{ 
            open: false, 
            id: '', 
            category: '', 
            theme: '', 
            title: '', 
            time: '', 
            loc: '', 
            speaker: '', 
            desc: '',
            waktu_mulai: '',
            
            // Fungsi untuk mengecek apakah waktu sekarang <= waktu_mulai + 1 jam
            canEditOrDelete() {
                if (!this.waktu_mulai) return false;
                let waktuMulaiMs = new Date(this.waktu_mulai).getTime();
                let batasWaktuMs = waktuMulaiMs + (60 * 60 * 1000); // Tambah 1 jam dalam milidetik
                let waktuSekarangMs = new Date().getTime();
                return waktuSekarangMs <= batasWaktuMs;
            }
        }" 
        @open-agenda.window="
            open = true; 
            id = $event.detail.id;
            category = $event.detail.category;
            theme = $event.detail.theme;
            title = $event.detail.title; 
            time = $event.detail.time; 
            loc = $event.detail.loc;
            speaker = $event.detail.speaker;
            desc = $event.detail.desc;
            waktu_mulai = $event.detail.waktu_mulai;
            
            setTimeout(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
        ">
        
        <div x-show="open" 
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition.opacity
            style="display: none;">
            
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
        
                    <!-- TOMBOL AKSI DI DALAM MODAL -->
                    <?php if (in_array(session()->get('id_peran'), [3,5])): ?>
                    <!-- Tombol hanya dirender PHP jika peran sesuai, lalu dikontrol penampilannya secara dinamis via x-show -->
                    <div class="pt-4 mt-2 border-t border-gray-100 flex justify-end gap-2" x-show="canEditOrDelete()">
                        <!-- Tombol Edit -->
                        <a :href="'<?= base_url('admin/agenda/edit/') ?>' + id" 
                        class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm font-medium text-sm">
                            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit
                        </a>
                        
                        <!-- Tombol Hapus -->
                        <button type="button" 
                            class="btn-delete flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm font-medium text-sm"
                            :data-id="id" 
                            :data-tema="theme"
                            @click="open = false">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                        </button>
                    </div>
                    <?php endif; ?>
        
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const $ = window.jQuery;
        const DataTable = window.DataTable;

        const loadData = () => {
            const tableId = '#tableAgenda';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

            $('#load-data').html('<tr><td colspan="6" class="text-center py-20 text-gray-400">Memuat jadwal agenda...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/agenda/list') ?>",
                type: "POST",
                data: { 
                    id_kategori_agenda: $('#filter-kategori').val(),
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(response) {
                    $('#load-data').html(response);
                    
                    if ($('#load-data').find('td[colspan]').length === 0) {
                        new DataTable(tableId, {
                            responsive: false,
                            pageLength: 10,
                            ordering: false,
                            order: [],
                            columnDefs: [{ targets: [2, 5], orderable: false }],
                            dom: '<"flex flex-col md:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"ip>',
                            drawCallback: function() {
                                if (window.reinitIcons) {
                                    window.reinitIcons();
                                } else if (typeof lucide !== 'undefined') {
                                    lucide.createIcons();
                                }
                            }
                        });
                    }
                    if (window.reinitIcons) window.reinitIcons();
                }
            });
        };

        loadData();

        $('#filter-kategori').on('change', loadData);
        $('#btn-reset-filter').on('click', function() {
            $('#filter-kategori').val("");
            loadData();
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const tema = $(this).data('tema');

            window.Swal.fire({
                title: 'Hapus Agenda?',
                html: `Yakin ingin menghapus agenda:<br><b>${tema}</b>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('admin/agenda/delete') ?>",
                        type: "POST",
                        data: {
                            id_agenda: id,
                            "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                            }
                            refreshAgendaView();
                        },
                        error: function(xhr) {
                            let errorMessage = 'Gagal menghubungi server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire('Aksi Ditolak', errorMessage, 'error');
                            
                            refreshAgendaView();
                        }
                    });
                }
            });
        });

        function refreshAgendaView() {
            if (typeof loadData === 'function') {
                loadData();
            }
            
            // 2. Refresh FullCalendar
            if (window.calendarInstance) {
                window.calendarInstance.refetchEvents();
            } else if (typeof FullCalendar !== 'undefined') {
                const calendarEl = document.getElementById('calendar');
                const cal = FullCalendar.Calendar.getCalendar(calendarEl);
                if (cal) cal.refetchEvents();
            }
        }
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && window.FullCalendar) {
    }
});
</script>
<?= $this->endSection() ?>