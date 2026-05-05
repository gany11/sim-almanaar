<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-grey-400">Keuangan Rutin</h2>
            <p class="text-sm text-gray-500 mt-1">Catatan arus kas harian/mingguan Masjid Al-Manaar.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [4])): ?>
            <a href="<?= base_url('admin/finance/routine/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi
            </a>
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
                            <th class="px-6 py-4">Transaksi</th>
                            <th class="px-6 py-4 text-center">Kategori</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="load-routine"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 mb-4">
        <div class="h-8 w-1.5 bg-gray-400 rounded-full"></div>
        <h3 class="font-bold text-gray-800 uppercase tracking-tight italic">Riwayat Laporan Mingguan</h3>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tableHistory" class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Judul Laporan</th>
                        <th class="px-6 py-4 text-center">Catatan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="load-history"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loadData = () => {
            const routineTable = '#tableRoutine';
            const historyTable = '#tableHistory';

            if ($.fn.DataTable.isDataTable(routineTable)) $(routineTable).DataTable().clear().destroy();
            if ($.fn.DataTable.isDataTable(historyTable)) $(historyTable).DataTable().clear().destroy();

            $('#load-routine, #load-history').html('<tr><td colspan="4" class="text-center py-20 text-gray-400 italic">Memuat data transaksi...</td></tr>');

            $.ajax({
                url: "<?= base_url('admin/finance/routine/list') ?>",
                type: "POST",
                data: {
                    id_kategori_keuangan: $('#filter-cat').val(),
                    jenis: $('#filter-type').val(),
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(res) {
                    const $temp = $('<div>').append(res);
                    
                    $('#load-routine').html($temp.find('#source-routine-data tbody').html());
                    $('#load-history').html($temp.find('#source-history-data tbody').html());

                    const source = $temp.find('#source-report-title');
                    $('#current-title').text(source.data('title'));
                    
                    updateActionUI(source.data('id'), source.data('note'));

                    if ($('#load-history').find('td[colspan]').length === 0) {
                    new DataTable(historyTable, {
                        responsive: false,
                        pageLength: 5,
                        ordering: false,
                        lengthMenu: [5, 10, 25, 50],
                        language: {
                            search: "Cari Laporan:",
                            lengthMenu: "Tampilkan _MENU_ data", 
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ laporan",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 laporan",
                            infoFiltered: "(disaring dari _MAX_ total laporan)",
                            emptyTable: "Belum ada riwayat laporan.",
                            zeroRecords: "Laporan tidak ditemukan.",
                            paginate: {
                                next: '<i data-lucide="chevron-right" class="w-4 h-4"></i>',
                                previous: '<i data-lucide="chevron-left" class="w-4 h-4"></i>'
                            }
                        },
                        // PERBAIKAN DI SINI: Tambahkan huruf 'l' sebelum 'f'
                        dom: '<"flex flex-col md:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"ip>'
                    });
                }

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
                        // Cari span saldo berdasarkan nama kategori dan update teksnya
                        $(`.saldo-value[data-cat="${catName}"]`).text(newVal);
                    });
                    
                    if (window.reinitIcons) window.reinitIcons();
                    else if (typeof lucide !== 'undefined') lucide.createIcons();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#load-routine, #load-history').html('<tr><td colspan="4" class="text-center py-10 text-red-400">Gagal memuat data.</td></tr>');
                }
            });
        };

        function updateActionUI(reportId, reportNote) {
            let actionBtn = '';
            if (reportId) {
                actionBtn = `<a href="<?= base_url('admin/finance/report/edit-note') ?>/${reportId}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-bold text-[10px] uppercase tracking-wider shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="message-square-plus" class="w-3.5 h-3.5"></i> Catatan Laporan
                </a>`;
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
                        url: "<?= base_url('admin/finance/routine/delete') ?>",
                        type: "POST",
                        data: {
                            id_keuangan: id, // Sesuaikan primary key db
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