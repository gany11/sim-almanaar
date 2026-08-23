<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Agenda Rutin</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola jadwal pola kegiatan rutin, penugasan SDM, dan lokasi acara masjid.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [3,5])): ?>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= base_url('admin/agenda-rutin/create') ?>" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                    <span>Tambah Agenda Rutin</span>
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

    <!-- INFORMASI SISTEM GENERATE AGENDA -->
    <div class="mb-6 p-5 bg-blue-50/80 border border-blue-100 rounded-2xl flex items-start gap-4 shadow-sm mx-4 md:mx-0">
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <i data-lucide="info" class="w-5 h-5"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-blue-800">Informasi Otomatisasi Sistem</h4>
            <p class="text-xs text-blue-600 mt-1.5 leading-relaxed">
                Data pola agenda rutin dengan status <span class="font-bold uppercase bg-blue-200/50 px-1.5 py-0.5 rounded">Aktif</span> di halaman ini akan diproses secara otomatis oleh sistem. Sistem akan men-generate jadwal ini menjadi daftar <strong>Agenda Harian</strong> setiap <strong>tanggal 20</strong> untuk jadwal kegiatan di <strong>bulan berikutnya</strong>.
            </p>
        </div>
    </div>

    <!-- Filter Kategori & Status -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 mx-4 md:mx-0">
        <div class="flex flex-wrap items-end gap-4">
            <!-- Filter Kategori -->
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Kategori</label>
                <select id="filter-kategori" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id_kategori_agenda'] ?>"><?= $cat['nama_kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Filter Status -->
            <div class="w-full md:w-48">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Status</label>
                <select id="filter-status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="pasif">Tidak Aktif</option>
                </select>
            </div>

            <!-- Tombol Reset -->
            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table id="tableAgendaRutin" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Waktu / Pola</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Info Agenda Rutin</th>
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

    <!-- Modal Detail Agenda Rutin -->
    <div x-data="{ open: false, id: '', category: '', theme: '', title: '', time: '', loc: '', speaker: '', desc: '' }" 
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
                            <i data-lucide="clock" class="w-4 h-4 text-blue-600 mt-1"></i>
                            <div class="text-xs text-gray-600">
                                <p class="font-bold">Waktu & Pola</p>
                                <p x-html="time"></p> <!-- Menggunakan x-html jika ada tag br dari view list -->
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
                    <div class="pt-4 mt-2 border-t border-gray-100 flex justify-end gap-2">
                        <a :href="'<?= base_url('admin/agenda-rutin/edit/') ?>' + id" 
                           class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm font-medium text-sm">
                            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit
                        </a>
                        
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
            const tableId = '#tableAgendaRutin';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

            $('#load-data').html('<tr><td colspan="6" class="text-center py-20 text-gray-400">Memuat jadwal agenda rutin...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/agenda-rutin/list') ?>",
                type: "POST",
                data: { 
                    id_kategori_agenda: $('#filter-kategori').val(),
                    status: $('#filter-status').val(), // <-- TAMBAHAN PARAMETER STATUS
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
                                if (window.reinitIcons) window.reinitIcons();
                                else if (typeof lucide !== 'undefined') lucide.createIcons();
                            }
                        });
                    }
                    if (window.reinitIcons) window.reinitIcons();
                }
            });
        };

        loadData();

        // MENDETEKSI PERUBAHAN PADA KEDUA FILTER
        $('#filter-kategori, #filter-status').on('change', loadData); 
        
        // RESET KEDUA FILTER
        $('#btn-reset-filter').on('click', function() {
            $('#filter-kategori').val("");
            $('#filter-status').val("");
            loadData();
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const tema = $(this).data('tema');

            window.Swal.fire({
                title: 'Hapus Agenda Rutin?',
                html: `Yakin ingin menghapus agenda rutin:<br><b>${tema}</b>`,
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
                        url: "<?= base_url('admin/agenda-rutin/delete') ?>",
                        type: "POST",
                        data: {
                            id_agenda_rutin: id,
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
                                loadData(); // Refresh DataTable
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>