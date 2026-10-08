<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Fitur</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar fitur aplikasi, kategori modul, dan jenis hak akses.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [1])): ?>
            <a href="<?= base_url('admin/features/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Fitur
            </a>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mx-4 mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mx-4 mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="mx-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <!-- Filter Kategori -->
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Kategori</label>
                <select id="filter-kategori" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= esc($cat) ?>"><?= esc($cat) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Filter Jenis Akses -->
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Jenis Akses</label>
                <select id="filter-jenis" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Jenis</option>
                    <option value="public">Public (Tanpa Login)</option>
                    <option value="auth">Auth (Perlu Login)</option>
                    <option value="hybrid">Hybrid (Keduanya)</option>
                </select>
            </div>
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Maintenance</label>
                <select id="filter-maintenance" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="0">Aktif</option>
                    <option value="1">Maintenance</option>
                </select>
            </div>

            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="mx-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tableFeature" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kode Fitur</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nama Fitur</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Jenis</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-data" class="text-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const $ = window.jQuery;
    const DataTable = window.DataTable;

    const loadData = () => {
        const tableId = '#tableFeature';
        if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

        $('#load-data').html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data...</td></tr>');

        $.ajax({
            url: "<?= base_url('admin/features/list') ?>",
            type: "POST",
            data: { 
                kategori: $('#filter-kategori').val(),
                jenis: $('#filter-jenis').val(),
                maintenance: $('#filter-maintenance').val(),
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            success: function(response) {
                $('#load-data').html(response);
                if ($('#load-data').find('td[colspan]').length === 0) {
                    new DataTable(tableId, {
                        responsive: false,
                        pageLength: 10,
                        columnDefs: [{ targets: [4, 5], orderable: false }],
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
    $('#filter-kategori, #filter-jenis, #filter-maintenance').on('change', loadData);
    
    $('#btn-reset-filter').on('click', function() {
        $('#filter-kategori').val("");
        $('#filter-jenis').val("");
        $('#filter-maintenance').val("");
        loadData();
    });

    // Fungsi Hapus dengan SweetAlert2 & Penanganan Response Controller
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');

        window.Swal.fire({
            title: 'Hapus Fitur?',
            text: 'Apakah Anda yakin ingin menghapus data fitur ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();

                $.ajax({
                    url: "<?= base_url('admin/features/delete') ?>",
                    type: "POST",
                    data: {
                        id_fitur: id,
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
                            Swal.fire('Gagal', res.message || 'Gagal menghapus data fitur.', 'error');
                            loadData(); 
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Terjadi kesalahan sistem saat menghapus fitur.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errMsg, 'error');
                        loadData(); 
                    }
                });
            }
        });
    });

    // Fungsi Ubah Status Maintenance dengan konfirmasi SweetAlert
    $(document).on('click', '.btn-toggle-maintenance', function() {
        const id = $(this).data('id');
        const currentStatus = $(this).attr('title'); // atau deteksi status aktif/maintenance

        window.Swal.fire({
            title: 'Ubah Status Maintenance?',
            text: 'Apakah Anda yakin ingin mengubah status pemeliharaan fitur ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.Swal.showLoading();

                $.ajax({
                    url: "<?= base_url('admin/features/toggle-maintenance') ?>",
                    type: "POST",
                    data: {
                        id_fitur: id,
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            window.Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadData(); 
                        } else {
                            window.Swal.fire('Gagal', res.message || 'Gagal mengubah status maintenance.', 'error');
                            loadData(); 
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Terjadi kesalahan sistem saat mengubah status.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        window.Swal.fire('Error', errMsg, 'error');
                        loadData(); 
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>