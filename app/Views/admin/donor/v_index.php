<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Donatur</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data profil donatur, kontak, dan token akses riwayat privat.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= base_url('admin/donors/create') ?>" 
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all active:scale-95">
                <i data-lucide="user-plus" class="w-4 h-4"></i> 
                <span>Tambah Donatur</span>
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

    <!-- Tabel Data Donatur -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0">
        <div class="overflow-x-auto">
            <table id="tableDonors" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nama Donatur</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kontak (Email / Telepon)</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Alamat</th>
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
            const tableId = '#tableDonors';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

            $('#load-data').html('<tr><td colspan="4" class="text-center py-20 text-gray-400">Memuat data donatur...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/donors/list') ?>",
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
                            ordering: true,
                            order: [[0, 'asc']],
                            columnDefs: [{ targets: [3], orderable: false }],
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

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            window.Swal.fire({
                title: 'Hapus Donatur?',
                html: `Yakin ingin menghapus data donatur:<br><b>${nama}</b>`,
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
                        url: "<?= base_url('admin/donors/delete') ?>",
                        type: "POST",
                        data: {
                            id_donatur: id,
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