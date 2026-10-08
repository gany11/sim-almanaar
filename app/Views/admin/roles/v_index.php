<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Peran</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola peran pengguna dan konfigurasi hak akses modul sistem.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [1])): ?>
            <a href="<?= base_url('admin/roles/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Peran Baru
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

    <div class="mx-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tableRole" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nama Peran</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Badge Color / Class</th>
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
        const tableId = '#tableRole';
        if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

        $('#load-data').html('<tr><td colspan="3" class="text-center py-20 text-gray-400">Memuat data peran...</td></tr>');

        $.ajax({
            url: "<?= base_url('admin/roles/list') ?>",
            type: "POST",
            data: { 
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            success: function(response) {
                $('#load-data').html(response);
                if ($('#load-data').find('td[colspan]').length === 0) {
                    new DataTable(tableId, {
                        responsive: false,
                        pageLength: 10,
                        columnDefs: [{ targets: [2], orderable: false }],
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

    // Fungsi Hapus Peran dengan SweetAlert2
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        window.Swal.fire({
            title: 'Hapus Peran?',
            html: `Apakah Anda yakin ingin menghapus peran <b>${nama}</b>?`,
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
                    url: "<?= base_url('admin/roles/delete') ?>",
                    type: "POST",
                    data: {
                        id_peran: id,
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
                            Swal.fire('Gagal', res.message || 'Gagal menghapus data peran.', 'error');
                            loadData(); 
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Terjadi kesalahan sistem saat menghapus peran.';
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
});
</script>
<?= $this->endSection() ?>