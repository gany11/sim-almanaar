<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 px-4">
    <div class="mb-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Laporan Bulanan</h2>
                <p class="text-sm text-gray-500 italic">Manajemen laporan keuangan bulanan</p>
            </div>
            </div>

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
                    <h3 class="text-lg font-bold text-gray-800">Arsip Laporan Bulanan</h3>
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
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left min-w-[500px]">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-black">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Bulan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-report" class="divide-y divide-gray-100">
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
        const tableBody = document.getElementById('table-body-report');

        if (filterTahun && tableBody) {
            function loadMonthlyData(tahun) {
                tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">Memuat data...</td></tr>';

                fetch(`<?= base_url('keuangan/getMonthlyReportAjax') ?>?tahun=${tahun}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const errorMsg = data?.error || data?.message || `Error ${response.status}: Akses Ditolak`;
                        throw new Error(errorMsg);
                    }
                    return data;
                })
                .then(data => {
                    tableBody.innerHTML = '';
                    if (!data || data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Tidak ada transaksi pada tahun ini.</td></tr>';
                        return;
                    }

                    data.forEach(report => {
                        const statusHtml = report.status === 'On Process' 
                            ? `<span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full border border-amber-100 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-ping"></span> ON PROCESS
                            </span>`
                            : `<span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full border border-emerald-100 inline-flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> FINAL
                            </span>`;

                        const row = `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold uppercase text-[15px]">
                                            ${report.bulan_num}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 uppercase tracking-tight">${report.bulan_name}</p>
                                            <p class="text-[10px] text-gray-400">Arsip Laporan ${report.tahun}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">${statusHtml}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= base_url('admin/finance/report/monthly') ?>/${report.tahun}/${report.bulan_num}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 font-bold transition-all group">
                                        Detail Laporan <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                    
                    // Render ulang icon dengan menyertakan object icons
                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons({
                            icons: lucide.icons
                        });
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    
                    // Gunakan template literal yang valid tanpa icon lucide agar aman jika terjadi error rendering,
                    // atau render ulang icon setelah error message ditambahkan
                    tableBody.innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-red-500 font-bold flex flex-col items-center gap-2 justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i> Gagal memuat data: ${error.message}
                    </td></tr>`;
                    
                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons({
                            icons: lucide.icons
                        });
                    }
                });
            }

            loadMonthlyData(filterTahun.value);

            filterTahun.addEventListener('change', function() {
                loadMonthlyData(this.value);
            });
        }
    });
</script>
<?= $this->endSection() ?>