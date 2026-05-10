<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-grey-400">Manajemen Akun</h2>
            <p class="text-sm text-gray-500 mt-1">Lihat, nonaktifkan, atau pulihkan akun pengguna sistem.</p>
        </div>
        <a href="<?= base_url('admin/account/register') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Registrasi Baru
        </a>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Status</label>
                <select id="filter-status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="pasif">Nonaktif</option>
                </select>
            </div>
            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tableAkun" class="w-full text-left border-collapse">
                <thead class="bg-blue-600 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Pengguna</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Peran</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Kontak</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Status</th>
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
    const Swal = window.Swal;

    const loadData = () => {
        const tableId = '#tableAkun';
        const tbodyId = '#load-data';

        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().clear().destroy();
        }

        $(tbodyId).empty();
        $(tbodyId).html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data...</td></tr>');

        $.ajax({
            url: "<?= base_url('admin/account/list') ?>",
            type: "POST",
            data: { 
                status: $('#filter-status').val(),
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            success: function(response) {
                $(tbodyId).html(response);
                
                const rowCount = $(tbodyId).find('tr').length;
                const isNoData = $(tbodyId).find('td[colspan]').length > 0;

                if (!isNoData) {
                    new DataTable(tableId, {
                        responsive: false,
                        pageLength: 10,
                        lengthMenu: [5, 10, 25, 50],
                        processing: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                            infoEmpty: "Data tidak ditemukan",
                            paginate: { next: "Next", previous: "Prev" }
                        },
                        columnDefs: [
                            { targets: [3, 4], orderable: false }
                        ],
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

                if (window.reinitIcons) {
                    window.reinitIcons();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                $(tbodyId).html('<tr><td colspan="5" class="text-center py-10 text-red-500">Gagal mengambil data.</td></tr>');
            }
        });
    };

    loadData();

    $('#filter-status').on('change', loadData);

    $('#btn-reset-filter').on('click', function() {
        $('#filter-status').val("");
        loadData();
    });

    $(document).on('click', '.btn-toggle-status', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const currentStatus = $(this).data('status');
        
        const actionText = currentStatus === 'aktif' ? 'Menonaktifkan' : 'Memulihkan';
        const targetStatus = currentStatus === 'aktif' ? 'pasif' : 'aktif';
        const color = currentStatus === 'aktif' ? '#ef4444' : '#22c55e'; // Merah : Hijau

        Swal.fire({
            title: 'Konfirmasi',
            html: `Apakah Anda yakin ingin <b>${actionText}</b> akun <b>${nama}</b>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: color,
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('admin/account/status') ?>",
                    type: "POST",
                    data: {
                        id_akun: id,
                        status: targetStatus,
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    success: function(res) {
                        if(res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadData();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memperbarui status akun.', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>