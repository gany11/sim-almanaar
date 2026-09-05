<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Program Donasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola program penggalangan dana, proposal, laporan, dan status publikasi.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= base_url('admin/donations/create') ?>" 
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                <span>Tambah Donasi</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm mx-4 md:mx-0">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm mx-4 md:mx-0">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 mx-4 md:mx-0">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-48">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Status Publikasi</label>
                <select id="filter-status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Publik</option>
                    <option value="pasif">Privat</option>
                </select>
            </div>
            
            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <!-- Tabel Data Donasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table id="tableDonations" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Program / Judul</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Jenis & Akronim</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Akumulasi Dana (Rp)</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Status Publikasi</th>
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
            const tableId = '#tableDonations';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

            $('#load-data').html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat program donasi...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/donations/list') ?>",
                type: "POST",
                data: { 
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
                            columnDefs: [{ targets: [4], orderable: false }],
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

        $('#filter-status').on('change', loadData);
        $('#btn-reset-filter').on('click', function() {
            $('#filter-status').val("");
            loadData();
        });

        // Tombol Close / Kunci Program Donasi
        $(document).on('click', '.btn-close-program', function() {
            const id = $(this).data('id');
            const judul = $(this).data('judul');

            window.Swal.fire({
                title: 'Tutup Program Donasi?',
                html: `Aksi ini akan mengunci total pemasukan & pengeluaran program:<br><b>${judul}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tutup Program!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('admin/donations/close') ?>",
                        type: "POST",
                        data: {
                            id_donasi: id,
                            "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadData();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            // Tangkap pesan error JSON yang dikirim dari server (misal status 400/500)
                            let errorMessage = 'Gagal menghubungi server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Gagal Menutup Program', errorMessage, 'error');
                        }
                    });
                }
            });
        });

        // Tombol Hapus Program Donasi
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const judul = $(this).data('judul');

            window.Swal.fire({
                title: 'Hapus Program Donasi?',
                html: `Yakin ingin menghapus program:<br><b>${judul}</b>`,
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
                        url: "<?= base_url('admin/donations/delete') ?>",
                        type: "POST",
                        data: {
                            id_donasi: id,
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
                                loadData();
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