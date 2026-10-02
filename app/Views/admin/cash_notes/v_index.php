<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Catatan Keuangan (Keep Cash / Draf)</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola draf kas sementara sebelum dimasukkan ke buku kas utama.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [4])): ?>
            <a href="<?= base_url('admin/cash-notes/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Keep Cash Baru
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

    <!-- Filter Status -->
    <div class="mx-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Filter Status</label>
                <select id="filter-status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="sudah_digunakan">Sudah Digunakan</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <button id="btn-reset-filter" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="mx-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tableCashNotes" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nominal</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-data" class="text-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ubah Status Custom (Status hanya Digunakan / Dibatalkan + Alasan) -->
<div id="statusModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-800">Ubah Status Keep Cash</h3>
            <button type="button" id="closeStatusModal" class="text-gray-400 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="formUpdateStatus">
            <input type="hidden" id="modal_id_catatan" name="id_catatan">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Keterangan Catatan</label>
                    <p id="modal_keterangan_text" class="text-sm font-bold text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100">-</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Pilih Status Baru</label>
                    <select id="modal_status" name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm cursor-pointer">
                        <option value="sudah_digunakan">Sudah Digunakan</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label id="label_alasan" class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Alasan / Peruntukan</label>
                    <textarea id="modal_alasan" name="alasan" rows="3" required placeholder="Masukkan keterangan alasan atau peruntukan..." class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" id="cancelStatusModal" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-100 transition-all text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const $ = window.jQuery;
    const DataTable = window.DataTable;

    const loadData = () => {
        const tableId = '#tableCashNotes';
        if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

        $('#load-data').html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data...</td></tr>');

        $.ajax({
            url: "<?= base_url('admin/cash-notes/list') ?>",
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
                        columnDefs: [{ targets: [3, 4], orderable: false }],
                        dom: '<"flex flex-col md:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"ip>',
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                            infoEmpty: "Data tidak ditemukan",
                            paginate: { next: "Next", previous: "Prev" }
                        },
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
    $('#filter-status').on('change', loadData);
    $('#btn-reset-filter').on('click', function() {
        $('#filter-status').val("");
        loadData();
    });

    // Buka Modal Ubah Status (Kunci Scroll Body & Atur Label Dinamis)
    $(document).on('click', '.btn-open-status-modal', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const ket = $(this).data('keterangan');
        const alasan = $(this).data('alasan') || '';

        $('#modal_id_catatan').val(id);
        // Default ke sudah_digunakan jika status sebelumnya masih tersedia
        $('#modal_status').val(status === 'tersedia' ? 'sudah_digunakan' : status);
        $('#modal_keterangan_text').text(ket);
        $('#modal_alasan').val(alasan);

        // Ubah label dinamis berdasarkan pilihan status
        updateLabelAlasan($('#modal_status').val());

        $('#statusModal').removeClass('hidden');
        $('body').addClass('overflow-hidden'); // Kunci scroll background
    });

    // Perbarui label alasan saat pilihan status diganti
    $('#modal_status').on('change', function() {
        updateLabelAlasan($(this).val());
    });

    function updateLabelAlasan(val) {
        if (val === 'sudah_digunakan') {
            $('#label_alasan').text('Peruntukan / Penggunaan');
            $('#modal_alasan').attr('placeholder', 'Masukkan rincian peruntukan kas...');
        } else {
            $('#label_alasan').text('Alasan Pembatalan');
            $('#modal_alasan').attr('placeholder', 'Masukkan alasan pembatalan...');
        }
    }

    // Tutup Modal Ubah Status (Buka Kembali Scroll Body)
    $('#closeStatusModal, #cancelStatusModal').on('click', function() {
        $('#statusModal').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Submit Form Ubah Status via AJAX (Pola penanganan error persis tombol delete)
    $('#formUpdateStatus').on('submit', function(e) {
        e.preventDefault();

        const id = $('#modal_id_catatan').val();
        const status = $('#modal_status').val();
        const alasan = $('#modal_alasan').val();

        Swal.showLoading();

        $.ajax({
            url: "<?= base_url('admin/cash-notes/status') ?>",
            type: "POST",
            data: {
                id_catatan: id,
                status: status,
                alasan: alasan,
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            success: function(res) {
                $('#statusModal').addClass('hidden');
                $('body').removeClass('overflow-hidden');

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
                    Swal.fire('Gagal', res.message || 'Gagal memperbarui status.', 'error');
                    loadData();
                }
            },
            error: function(xhr) {
                $('#statusModal').addClass('hidden');
                $('body').removeClass('overflow-hidden');

                let errMsg = 'Terjadi kesalahan sistem saat memperbarui status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errMsg, 'error');
                loadData();
            }
        });
    });

    // Fungsi Hapus Catatan Keuangan dengan SweetAlert2
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');

        window.Swal.fire({
            title: 'Hapus Catatan?',
            text: 'Apakah Anda yakin ingin menghapus catatan keep cash ini?',
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
                    url: "<?= base_url('admin/cash-notes/delete') ?>",
                    type: "POST",
                    data: {
                        id_catatan: id,
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
                            Swal.fire('Gagal', res.message || 'Gagal menghapus catatan.', 'error');
                            loadData(); 
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Terjadi kesalahan sistem saat menghapus data.';
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