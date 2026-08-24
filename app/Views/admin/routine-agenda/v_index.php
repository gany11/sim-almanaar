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
                <a href="<?= base_url('admin/routine-agenda/create') ?>" 
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
                url: "<?= base_url('admin/routine-agenda/list') ?>",
                type: "POST",
                data: { 
                    id_kategori_agenda: $('#filter-kategori').val(),
                    status: $('#filter-status').val(),
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
                        url: "<?= base_url('admin/routine-agenda/delete') ?>",
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