<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen SDM / Petugas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data sumber daya manusia, pengisi acara, dan kontak petugas.</p>
        </div>
        <?php if (can_access('sdm.create')): ?>
            <a href="<?= base_url('admin/sdm/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah SDM Baru
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
            <table id="tableSdm" class="w-full text-left border-collapse">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Nama Lengkap</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Telepon / WA</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest">Alamat</th>
                        <th class="px-6 py-4 text-xs font-bold text-white uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-data" class="text-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ganti SDM & Hapus -->
<div id="replaceSdmModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-800">Alihkan Penugasan & Hapus SDM</h3>
            <button type="button" id="closeReplaceModal" class="text-gray-400 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="formReplaceSdm">
            <input type="hidden" id="modal_id_sdm_lama" name="id_sdm_lama">
            
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    <b id="modal_nama_lama" class="text-gray-900"></b> masih memiliki agenda atau penugasan aktif. Silakan pilih SDM pengganti untuk mengalihkan seluruh tugas tersebut sebelum dihapus:
                </p>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Pilih SDM Pengganti</label>
                    <select id="modal_id_sdm_baru"
                            name="id_sdm_baru"
                            required
                            class="w-full select2-modal">
                        <option value="">-- Pilih SDM Pengganti --</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" id="cancelReplaceModal" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-100 transition-all text-sm">Alihkan & Hapus</button>
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
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const $ = window.jQuery;
        const DataTable = window.DataTable;

        const loadData = () => {
            const tableId = '#tableSdm';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().clear().destroy();

            $('#load-data').html('<tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/sdm/list') ?>",
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
                            columnDefs: [{ targets: [4], orderable: false }],
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
                },
                error: function(xhr) {
                    // Jika fitur sedang maintenance (503), reload halaman untuk menampilkan view maintenance
                    if (xhr.status === 503) {
                        window.location.reload();
                        return;
                    }

                    console.error(xhr.responseText);
                    $('#load-data').html('<tr><td colspan="5" class="text-center py-10 text-red-500">Gagal memuat data SDM.</td></tr>');
                }
            });
        };

        loadData();

        // Handler Tombol Hapus Utama
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            window.Swal.fire({
                title: 'Hapus Data SDM?',
                html: `Apakah Anda yakin ingin menghapus data SDM:<br><b>${nama}</b>`,
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
                        url: "<?= base_url('admin/sdm/delete') ?>",
                        type: "POST",
                        data: {
                            id_sdm: id,
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
                                if (res.message.includes('tidak dapat dihapus karena memiliki agenda')) {
                                    openReplaceModal(id, nama);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Menghapus',
                                        html: res.message,
                                        confirmButtonColor: '#2563eb'
                                    });
                                    loadData();
                                }
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

        function openReplaceModal(idSdmLama, namaLama) {
            $('#modal_id_sdm_lama').val(idSdmLama);
            $('#modal_nama_lama').text(namaLama);

            const $select = $('#modal_id_sdm_baru');

            // Jika sebelumnya sudah menjadi Select2, hancurkan terlebih dahulu
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Reset option
            $select.html('<option value="">-- Pilih SDM Pengganti --</option>');

            // Tampilkan modal terlebih dahulu
            $('#replaceSdmModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');

            // Ambil SDM alternatif
            $.ajax({
                url: "<?= base_url('admin/sdm/alternative') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    id_sdm: idSdmLama,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(res) {

                    if (res.status === 'success') {

                        // Tambahkan option biasa
                        res.data.forEach(function(item) {
                            $select.append(
                                $('<option>', {
                                    value: item.id_sdm,
                                    text: item.nama
                                })
                            );
                        });

                        // INISIALISASI SELECT2 SETELAH OPTION SELESAI
                        $select.select2({
                            dropdownParent: $('#replaceSdmModal'),
                            placeholder: "-- Pilih SDM Pengganti --",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil daftar SDM pengganti.'
                    });
                }
            });
        }

        // Fungsi untuk Menutup Modal
        function closeReplaceModal() {
            $('#replaceSdmModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#closeReplaceModal, #cancelReplaceModal').on('click', function() {
            closeReplaceModal();
        });

        // Submit Form Penggantian SDM dan Hapus
        $('#formReplaceSdm').on('submit', function(e) {
            e.preventDefault();

            const idLama = $('#modal_id_sdm_lama').val();
            const idBaru = $('#modal_id_sdm_baru').val();

            if (!idBaru) {
                Swal.fire('Perhatian', 'Silakan pilih SDM pengganti terlebih dahulu.', 'warning');
                return;
            }

            closeReplaceModal();
            Swal.showLoading();

            $.ajax({
                url: "<?= base_url('admin/sdm/replace-delete') ?>",
                type: "POST",
                data: {
                    id_sdm_lama: idLama,
                    id_sdm_baru: idBaru,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadData();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>