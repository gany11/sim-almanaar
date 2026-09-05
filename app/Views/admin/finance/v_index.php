<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="{ openImport: false }">
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Keuangan Rutin</h2>
            <p class="text-sm text-gray-500 mt-1">Catatan arus kas harian/mingguan Masjid Al-Manaar.</p>
        </div>

        <?php if (in_array(session()->get('id_peran'), [4])): ?>
        <div class="flex flex-wrap items-center gap-3">
            <button @click="openImport = true" 
                class="flex items-center gap-2 px-5 py-2.5 bg-white border border-emerald-200 text-emerald-600 rounded-xl font-bold text-sm hover:bg-emerald-50 transition-all active:scale-95 shadow-sm shadow-emerald-50">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> 
                <span>Import Excel</span>
            </button>

            <a href="<?= base_url('admin/finance/data/create') ?>" 
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                <span>Catat Transaksi</span>
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
    <?php if (session()->getFlashdata('error_list')) : ?>
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-2xl shadow-sm">
            <div class="flex items-center gap-2 text-red-700 font-bold mb-3">
                <i data-lucide="list-x" class="w-5 h-5"></i>
                <span class="text-sm">Gagal Import! Periksa Baris Berikut:</span>
            </div>
            
            <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                <ul class="space-y-1.5">
                    <?php foreach (session()->getFlashdata('error_list') as $err) : ?>
                        <li class="flex items-start gap-2 text-xs text-red-600 leading-relaxed">
                            <span class="mt-1 w-1 h-1 rounded-full bg-red-400 shrink-0"></span>
                            <?= $err ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="mt-4 pt-3 border-t border-red-100">
                <p class="text-[10px] text-red-500 italic font-medium">
                    * Sistem tidak memasukkan data apa pun. Silakan perbaiki file Excel Anda dan coba lagi.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tampilkan Summary Saldo Di Sini -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?php if (!empty($summaryKeuangan)): ?>
            <?php foreach ($summaryKeuangan as $k) : ?>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4 mb-4">
                    <!-- Gunakan class_color dari database atau fallback ke blue -->
                    <div class="p-3 <?= !empty($k['class_color']) ? str_replace('text-', 'bg-', $k['class_color']) . ' bg-opacity-10 ' . $k['class_color'] : 'bg-blue-50 text-blue-600' ?> rounded-2xl">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?= $k['kategori'] ?></span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">
                    Rp <span class="saldo-value" data-cat="<?= $k['kategori'] ?>">
                        <?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?>
                    </span>
                </h3>
                <p class="text-[10px] text-gray-400 mt-2 italic">Total Saldo Saat Ini</p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-4 text-center py-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <p class="text-gray-400 text-sm italic">Belum ada data keuangan yang tersedia.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kategori Keuangan</label>
                <select id="filter-cat" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <?php foreach($kategori as $k): ?>
                        <option value="<?= $k['id_kategori_keuangan'] ?>"><?= $k['kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="w-full md:w-64">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis Transaksi</label>
                <select id="filter-type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm appearance-none cursor-pointer">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan">Pemasukan (Uang Masuk)</option>
                    <option value="pengeluaran">Pengeluaran (Uang Keluar)</option>
                </select>
            </div>

            <button id="btn-reset" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
            </button>
        </div>
    </div>

    <div id="section-current">
        <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1.5 bg-blue-600 rounded-full"></div>
                <h3 id="current-title" class="font-bold text-gray-800 uppercase tracking-tight italic">Memuat Judul Laporan...</h3>
            </div>
            <?php if (in_array(session()->get('id_peran'), [4])): ?>
                <div id="current-report-action"></div>
            <?php endif; ?>
        </div>

        <div id="current-report-note" class="mb-6"></div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 mb-12">
            <div class="overflow-x-auto">
                <table id="tableRoutine" class="w-full text-sm text-left">
                    <thead class="text-xs text-white uppercase bg-blue-600">
                        <tr>
                            <th class="px-6 py-4 text-center">Kategori</th>
                            <th class="px-6 py-4">Transaksi</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="load-routine"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="openImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800">Import Data Keuangan</h3>
                <button @click="openImport = false" class="text-gray-400 hover:text-red-500"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <form action="<?= base_url('admin/finance/import-excel') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                    <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider mb-1">Perhatian</p>
                    <p class="text-xs text-amber-600 leading-relaxed">Gunakan template resmi untuk menghindari error. Pastikan nama Kategori dan Detail Alokasi sesuai dengan database.</p>
                </div>
    
                <div 
                    x-data="{ fileName: null }" 
                    class="border-2 border-dashed rounded-2xl p-8 text-center transition-all relative"
                    :class="fileName ? 'border-emerald-400 bg-emerald-50/50' : 'border-gray-200 hover:border-blue-400'"
                >
                    <input type="file" name="file_excel" 
                        class="absolute inset-0 opacity-0 cursor-pointer" 
                        accept=".xlsx, .xls"
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : null"
                    >
                    
                    <template x-if="!fileName">
                        <div>
                            <i data-lucide="upload-cloud" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-gray-500">Klik atau seret file Excel ke sini</p>
                            <p class="text-[10px] text-gray-400 mt-1">Format: .xlsx atau .xls</p>
                        </div>
                    </template>

                    <template x-if="fileName">
                        <div class="flex flex-col items-center animate-in fade-in zoom-in duration-300">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-2">
                                <i data-lucide="file-check" class="w-6 h-6"></i>
                            </div>
                            <p class="text-sm font-bold text-emerald-700 truncate max-w-full px-4" x-text="fileName"></p>
                            <button type="button" @click.stop="fileName = null; $el.closest('form').reset()" class="mt-2 text-[10px] font-bold text-red-500 uppercase hover:underline">Ganti File</button>
                        </div>
                    </template>
                </div>
    
                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">MULAI IMPORT</button>
                    <a href="<?= base_url('assets/templates/Format Import Data Keuangan.xlsx') ?>" class="text-center py-2 text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors">Download Template Excel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loadData = () => {
            const routineTable = '#tableRoutine';

            if ($.fn.DataTable.isDataTable(routineTable)) $(routineTable).DataTable().clear().destroy();

            $('#load-routine').html('<tr><td colspan="4" class="text-center py-20 text-gray-400 italic">Memuat data transaksi...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/finance/data/list') ?>",
                type: "POST",
                data: {
                    id_kategori_keuangan: $('#filter-cat').val(),
                    jenis: $('#filter-type').val(),
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(res) {
                    const $temp = $('<div>').append(res);
                    
                    $('#load-routine').html($temp.find('#source-routine-data tbody').html());

                    const source = $temp.find('#source-report-title');
                    $('#current-title').text(source.data('title'));
                    
                    updateActionUI(source.data('id'), source.data('note'));

                    if ($('#load-routine').find('td[colspan]').length === 0) {
                        new DataTable(routineTable, {
                            responsive: false,
                            paging: false,
                            info: false,
                            orderable: false,
                            columnDefs: [{ targets: [0, 1, 3], orderable: false }],
                            dom: 'rt'
                        });
                    }

                    $temp.find('.summary-item').each(function() {
                        const catName = $(this).data('kategori');
                        const newVal = $(this).data('saldo');
                        $(`.saldo-value[data-cat="${catName}"]`).text(newVal);
                    });
                    
                    if (window.reinitIcons) window.reinitIcons();
                    else if (typeof lucide !== 'undefined') lucide.createIcons();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#load-routine').html('<tr><td colspan="4" class="text-center py-10 text-red-400">Gagal memuat data.</td></tr>');
                }
            });
        };

        function updateActionUI(reportId, reportNote) {
            let actionBtn = '';
            if (reportId) {
                actionBtn = `
                    <div class="flex items-center gap-2">
                        <a href="<?= base_url('admin/finance/report/weekly') ?>/${reportId}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold text-[10px] uppercase tracking-wider shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Preview Laporan
                        </a>
                        <a href="<?= base_url('admin/finance/report/edit-note') ?>/${reportId}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-bold text-[10px] uppercase tracking-wider shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i data-lucide="message-square-plus" class="w-3.5 h-3.5"></i> Catatan Laporan
                        </a>
                    </div>
                `;
            }
            $('#current-report-action').html(actionBtn);

            let noteHtml = '';
            if (reportNote && reportNote.trim() !== "" && reportNote !== "-") {
                noteHtml = `<div class="p-5 bg-amber-50 border border-amber-100 rounded-2xl flex gap-4 shadow-sm mb-6">
                    <div>
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1 italic">Catatan Pekan Ini:</p>
                        <div class="text-sm text-amber-900 leading-relaxed prose prose-sm max-w-none">${reportNote}</div>
                    </div>
                </div>`;
            }
            $('#current-report-note').html(noteHtml);
        }

        loadData();
        $('#filter-cat, #filter-type').on('change', loadData);
        $('#btn-reset').on('click', () => { $('#filter-cat, #filter-type').val(""); loadData(); });

        // 1. Handler Hapus Transaksi Keuangan Murni
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const judul = $(this).data('judul');

            window.Swal.fire({
                title: 'Hapus Transaksi?',
                html: `Apakah Anda yakin ingin menghapus catatan keuangan:<br><b class="text-rose-600">${judul}</b>`,
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
                        url: "<?= base_url('admin/finance/data/delete') ?>",
                        type: "POST",
                        data: {
                            id_keuangan: id,
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

        // 2. Handler Hapus Pemasukan Donasi (.btn-delete-candidate)
        $(document).on('click', '.btn-delete-candidate', function(e) {
            e.preventDefault();
            
            const id = $(this).data('id');
            const info = $(this).data('info');

            if (!id) {
                console.error('ID Pemasukan Donasi tidak ditemukan.');
                return;
            }

            window.Swal.fire({
                title: 'Hapus Riwayat Donasi?',
                html: `Yakin ingin menghapus catatan:<br><b>"${info}"</b>`,
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
                        url: "<?= base_url('admin/donation-incomes/delete') ?>",
                        type: "POST",
                        data: {
                            id_pemasukan_donasi: id,
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
                                loadData(); // Memperbarui tabel keuangan secara instan
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Gagal menghubungi server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>