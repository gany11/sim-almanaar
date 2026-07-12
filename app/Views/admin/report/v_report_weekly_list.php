<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 px-4">
    <div class="mb-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Laporan Mingguan</h2>
                <p class="text-sm text-gray-500 italic">Manajemen riwayat laporan keuangan mingguan</p>
            </div>
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Riwayat Laporan Mingguan</h3>
                    <p class="text-sm text-gray-500">Data terupdate otomatis berdasarkan tahun</p>
                </div>
                
                <select id="filter-tahun" class="w-full md:w-40 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl block p-2.5 font-bold outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <?php foreach($years as $y): ?>
                        <option value="<?= $y['year'] ?>" <?= date('Y') == $y['year'] ? 'selected' : '' ?>>
                            Tahun <?= $y['year'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="p-6">
                <div class="w-full">
                    <table id="tableHistory" class="w-full text-sm text-left min-w-[500px]">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-black">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Judul Laporan</th>
                                <th class="px-6 py-4 text-center">Catatan</th>
                                <th class="px-6 py-4 text-center rounded-tr-lg w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="load-history" class="divide-y divide-gray-100">
                            </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterTahun = document.getElementById('filter-tahun');
    const tableBody = document.getElementById('load-history');

    if (filterTahun && tableBody) {
        function loadWeeklyData(tahun) {
            
            // 1. Hancurkan instance DataTables jika sudah ada sebelum merender ulang DOM
            if ($.fn.DataTable.isDataTable('#tableHistory')) {
                $('#tableHistory').DataTable().destroy();
            }

            tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">Memuat data...</td></tr>';

            fetch(`<?= base_url('admin/finance/report/getWeeklyHistoryAjax') ?>?tahun=${tahun}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    throw new Error(data?.error || data?.message || `Error ${response.status}: Akses Ditolak`);
                }
                return data;
            })
            .then(data => {
                tableBody.innerHTML = '';
                
                if (!data || data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada riwayat laporan pada tahun ini.</td></tr>';
                    return;
                }

                // 2. Render baris HTML
                data.forEach(h => {
                    let actionBtns = '';
                    
                    if (h.can_view) {
                        actionBtns += `
                            <a href="<?= base_url('admin/finance/report/weekly') ?>/${h.id_laporan_mingguan}" class="p-2 bg-gray-50 text-gray-600 rounded-lg shadow-sm hover:bg-gray-600 hover:text-white transition-all" title="Lihat Rincian">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>`;
                    }
                    
                    if (h.can_edit) {
                        actionBtns += `
                            <a href="<?= base_url('admin/finance/report/edit-note') ?>/${h.id_laporan_mingguan}" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg shadow-sm hover:bg-emerald-600 hover:text-white transition-all" title="Edit Catatan">
                                <i data-lucide="message-square-more" class="w-4 h-4"></i>
                            </a>`;
                    }

                    const row = `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 block">${h.judul}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-xs text-gray-500 line-clamp-1 italic">${h.catatan}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    ${actionBtns}
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
                
                // 3. Inisialisasi DataTables setelah data selesai dimasukkan ke DOM
                if ($('#load-history').find('td[colspan]').length === 0) {
                    new DataTable('#tableHistory', {
                        scrollX: true,
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
                        dom: '<"flex flex-col md:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"ip>',
                        drawCallback: function() {
                            if (window.reinitIcons) {
                                window.reinitIcons();
                            } else if (typeof lucide !== 'undefined' && lucide.createIcons) {
                                lucide.createIcons({ icons: lucide.icons });
                            }
                        }
                    });
                } else {
                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons({ icons: lucide.icons });
                    }
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                tableBody.innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-red-500 font-bold flex flex-col items-center gap-2 justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i> Gagal memuat data: ${error.message}
                </td></tr>`;
                
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons({ icons: lucide.icons });
                }
            });
        }

        loadWeeklyData(filterTahun.value);

        filterTahun.addEventListener('change', function() {
            loadWeeklyData(this.value);
        });
    }
});
</script>
<?= $this->endSection() ?>