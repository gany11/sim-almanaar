<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= esc($feature['nama_fitur']) ?></h2>
            <p class="text-sm text-gray-500 mt-1">Kode: <span class="font-mono font-semibold text-blue-600"><?= esc($feature['kode_fitur']) ?></span> | Kategori: <?= esc($feature['kategori']) ?></p>
        </div>
        <a href="<?= base_url('admin/features') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Fitur
        </a>
    </div>

    <!-- Informasi Utama Fitur -->
    <div class="mx-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kategori Modul</span>
                <span class="text-sm font-semibold text-gray-700"><?= esc($feature['kategori']) ?></span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis Akses</span>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase bg-blue-100 text-blue-700">
                    <?= esc($feature['jenis']) ?>
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Deskripsi</span>
                <span class="text-sm text-gray-600"><?= !empty($feature['deskripsi']) ? esc($feature['deskripsi']) : '-' ?></span>
            </div>
        </div>
    </div>

    <!-- Bagian Manajemen Prasyarat (Syarat Induk) -->
    <div class="mx-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Prasyarat / Syarat Induk Fitur</h3>
                <p class="text-xs text-gray-500 mt-0.5">Fitur-fitur yang wajib dimiliki terlebih dahulu agar fitur ini dapat diakses.</p>
            </div>
            
            <!-- Tombol Pemicu Modal Tambah Prasyarat -->
            <button type="button" id="openAddPrerequisiteModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Syarat
            </button>
        </div>

        <div class="overflow-x-auto">
            <table id="tablePrerequisite" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kode Fitur Syarat</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nama Fitur Syarat</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Jenis</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-prerequisite-data" class="text-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Prasyarat -->
<div id="addPrerequisiteModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-800">Tambah Prasyarat Fitur</h3>
            <button type="button" id="closePrerequisiteModal" class="text-gray-400 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="formAddPrerequisite">
            <input type="hidden" id="modal_id_fitur" name="id_fitur" value="<?= $feature['id_fitur'] ?>">
            
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Pilih fitur yang harus dipenuhi terlebih dahulu (sebagai syarat induk) untuk fitur <b class="text-gray-900"><?= esc($feature['nama_fitur']) ?></b>:
                </p>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Pilih Fitur Syarat</label>
                    <select id="modal_id_prasyarat"
                            name="id_prasyarat"
                            required
                            class="w-full select2-modal">
                        <option value="">-- Cari & Pilih Fitur Syarat --</option>
                        <?php foreach ($availableFeatures as $af): ?>
                            <option value="<?= $af['id_fitur'] ?>">[<?= esc($af['id_fitur']) ?> - <?= esc($af['kode_fitur']) ?>] <?= esc($af['nama_fitur']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" id="cancelPrerequisiteModal" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-100 transition-all text-sm">Simpan Syarat</button>
            </div>
        </form>
    </div>
</div>

<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 1rem !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 10px !important;
        background-color: #fff !important;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__rendered {
        line-height: 46px !important;
        color: #374151 !important;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__arrow {
        height: 46px !important;
    }

    /* Memastikan kotak pencarian (search box) muncul dengan baik di dalam dropdown modal */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        padding: 8px 12px !important;
        outline: none !important;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const $ = window.jQuery;
    const DataTable = window.DataTable;
    const fiturId = '<?= $feature['id_fitur'] ?>';

    const loadPrerequisites = () => {
        const tableId = '#tablePrerequisite';
        if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

        $('#load-prerequisite-data').html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data prasyarat...</td></tr>');

        $.ajax({
            url: "<?= base_url('admin/features/prerequisite/list/') ?>" + fiturId,
            type: "POST",
            data: { 
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            success: function(response) {
                $('#load-prerequisite-data').html(response);
                if ($('#load-prerequisite-data').find('td[colspan]').length === 0) {
                    new DataTable(tableId, {
                        responsive: false,
                        pageLength: 5, // Mengatur default tampilan menjadi 5 baris
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]], // Pilihan dropdown length
                        columnDefs: [{ targets: [5], orderable: false }],
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

    loadPrerequisites();

    // Fungsi Membuka Modal
    function openModal() {
        const $select = $('#modal_id_prasyarat');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.val('').trigger('change');

        // Tampilkan modal dan kunci background agar tidak bisa di-scroll
        $('#addPrerequisiteModal').removeClass('hidden');
        $('body').addClass('overflow-hidden');

        // Inisialisasi Select2 dengan dropdownParent agar search box berfungsi di dalam modal
        $select.select2({
            dropdownParent: $('#addPrerequisiteModal'),
            placeholder: "-- Cari & Pilih Fitur Syarat --",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });
    }

    // Fungsi Menutup Modal
    function closeModal() {
        $('#addPrerequisiteModal').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $('#openAddPrerequisiteModal').on('click', openModal);
    $('#closePrerequisiteModal, #cancelPrerequisiteModal').on('click', closeModal);

    // Menutup modal ketika area latar belakang (backdrop) di luar kotak modal diklik
    $('#addPrerequisiteModal').on('click', function(e) {
        // Cek apakah yang diklik adalah elemen pembungkus utama modal itu sendiri (bukan isinya)
        if ($(e.target).is('#addPrerequisiteModal')) {
            closeModal();
        }
    });

    // Submit Form Tambah Prasyarat
    $('#formAddPrerequisite').on('submit', function(e) {
        e.preventDefault();

        const idPrasyarat = $('#modal_id_prasyarat').val();
        if (!idPrasyarat) {
            Swal.fire('Perhatian', 'Silakan pilih fitur syarat terlebih dahulu.', 'warning');
            return;
        }

        closeModal();
        Swal.showLoading();

        $.ajax({
            url: "<?= base_url('admin/features/prerequisite/save') ?>",
            type: "POST",
            data: {
                id_fitur: fiturId,
                id_prasyarat: idPrasyarat,
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
                    loadPrerequisites();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Hapus Prasyarat
    $(document).on('click', '.btn-delete-prerequisite', function() {
        const idRelasi = $(this).data('id');

        window.Swal.fire({
            title: 'Hapus Prasyarat?',
            text: 'Fitur syarat ini tidak lagi menjadi prasyarat untuk fitur utama.',
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
                    url: "<?= base_url('admin/features/prerequisite/delete') ?>",
                    type: "POST",
                    data: {
                        id_fitur_prasyarat: idRelasi,
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
                            loadPrerequisites();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data prasyarat.', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>