<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
                <li class="inline-flex items-center">
                    <a href="<?= base_url() ?>" class="inline-flex items-center text-gray-700 hover:text-blue-600">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                        Beranda
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        <span class="ml-1 text-gray-400 md:ml-2 truncate max-w-[150px] md:max-w-none">
                            Keuangan
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="text-center">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 uppercase tracking-tight">
                Laporan Keuangan
            </h1>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center p-4 text-red-800 border-t-4 border-red-300 bg-red-50 rounded-lg shadow-sm" role="alert">
            <i data-lucide="alert-circle" class="flex-shrink-0 w-5 h-5"></i>
            <div class="ml-3 text-sm font-bold">
                <?= session()->getFlashdata('error') ?>
            </div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Top Section: Chart & Card Saldo -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="text-blue-600"></i>
                Tren Saldo Kas (6 Bulan Terakhir)
            </h3>
            <div class="h-80">
                <canvas id="lineChartKeuangan"></canvas>
            </div>
        </div>

        <!-- Card Saldo Per Kategori -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="wallet" class="text-blue-600"></i>
                Saldo Kas
            </h3>
            <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
                <?php foreach($summary_categories as $cat): ?>
                <div class="p-4 rounded-lg border-l-4 <?= $cat['class_color'] ?: 'border-blue-500' ?>">
                    <p class="text-xs text-gray-500 uppercase font-semibold"><?= $cat['kategori'] ?></p>
                    <p class="text-lg font-bold text-gray-800">Rp <?= number_format($cat['saldo'], 0, ',', '.') ?></p>
                    <!-- Iterasi 2 -->
                    <p class="text-[9px] text-gray-400 italic mt-1 pt-1 border-t border-gray-200/50">
                        Saldo akhir per: <?= format_indo($cat['tanggal_penghitungan'], 'full') ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Section Laporan Bulanan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Arsip Laporan Bulanan</h3>
                <p class="text-sm text-gray-500">Data terupdate otomatis berdasarkan tahun</p>
            </div>
            
            <!-- Dropdown Tanpa Form Submit -->
            <select id="filter-tahun" class="w-full md:w-40 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl block p-2.5 font-bold">
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
                            <th class="px-6 py-4">Bulan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-report" class="divide-y divide-gray-100">
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
            <div class="flex items-start gap-3 text-gray-500">
                <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0"></i>
                <p class="text-xs italic leading-relaxed">
                    Status <span class="font-bold text-amber-600">On Process</span> berarti bulan berjalan masih menerima transaksi baru. 
                    Data akan berstatus <span class="font-bold text-emerald-600">Final</span> setelah memasuki bulan berikutnya. 
                    Seluruh cut-off mingguan tetap dihitung setiap hari <span class="font-bold text-gray-800">Kamis pukul 23:59 WIB</span>.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // --- 1. INISIALISASI CHART (Line Chart) ---
        // const ctxLine = document.getElementById('lineChartKeuangan');
        // if (ctxLine) {
        //     const totalData = <?= json_encode($chartDataTotal ?? []) ?>;
        //     const detailData = <?= json_encode($chartDetail ?? []) ?>;
        //     const labels = totalData.length > 0 ? totalData.map(item => item.bulan) : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
            
        //     const datasets = [];
        //     datasets.push({
        //         label: 'Total Saldo (Global)',
        //         data: totalData.map(item => item.masuk),
        //         borderColor: '#10b981',
        //         backgroundColor: 'rgba(16, 185, 129, 0.1)',
        //         borderWidth: 3,
        //         fill: true,
        //         tension: 0.4,
        //         pointRadius: 4
        //     });

        //     const colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#6366f1'];
        //     let colorIdx = 0;
        //     for (const [kategori, values] of Object.entries(detailData)) {
        //         const dataPoint = labels.map(bulan => {
        //             const found = values.find(v => v.bulan === bulan);
        //             return found ? parseFloat(found.neto) : 0;
        //         });
        //         datasets.push({
        //             label: `Neto ${kategori}`,
        //             data: dataPoint,
        //             borderColor: colors[colorIdx % colors.length],
        //             borderDash: [5, 5], 
        //             borderWidth: 2,
        //             fill: false,
        //             tension: 0.4,
        //             pointStyle: 'circle'
        //         });
        //         colorIdx++;
        //     }

        //     new Chart(ctxLine, {
        //         type: 'line',
        //         data: { labels: labels, datasets: datasets },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             interaction: { mode: 'index', intersect: false },
        //             plugins: {
        //                 legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, font: { size: 11 } } },
        //                 tooltip: {
        //                     callbacks: {
        //                         label: function(context) {
        //                             let label = context.dataset.label || '';
        //                             if (label) label += ': ';
        //                             if (context.parsed.y !== null) {
        //                                 label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
        //                             }
        //                             return label;
        //                         }
        //                     }
        //                 }
        //             },
        //             scales: {
        //                 y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + value.toLocaleString('id-ID') } }
        //             }
        //         }
        //     });
        // }
        const ctxLine = document.getElementById('lineChartKeuangan');
        if (ctxLine) {
            const detailData = <?= json_encode($chartDetail ?? []) ?>;
            
            // Ambil labels dari salah satu kategori yang memiliki data terbanyak
            let labels = [];
            for (const key in detailData) {
                if (detailData[key].length > labels.length) {
                    labels = detailData[key].map(item => item.bulan);
                }
            }

            const colors = ['#8b5cf6', '#f59e0b', '#ec4899', '#6366f1'];
            const datasets = [];
            let colorIdx = 0;

            for (const [kategori, values] of Object.entries(detailData)) {
                datasets.push({
                    label: kategori,
                    // Mapping data saldo_akhir ke label bulan yang sesuai
                    data: labels.map(bulan => {
                        const found = values.find(v => v.bulan === bulan);
                        return found ? parseFloat(found.saldo_akhir) : 0;
                    }),
                    // Simpan rincian masuk/keluar di properti custom untuk tooltip
                    extra: labels.map(bulan => {
                        const found = values.find(v => v.bulan === bulan);
                        return found ? { masuk: found.masuk, keluar: found.keluar } : { masuk: 0, keluar: 0 };
                    }),
                    borderColor: colors[colorIdx % colors.length],
                    backgroundColor: colors[colorIdx % colors.length],
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false,
                    pointStyle: 'circle'
                });
                colorIdx++;
            }

            new Chart(ctxLine, {
                type: 'line',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed.y;
                                    const extra = context.dataset.extra[context.dataIndex];
                                    const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
                                    
                                    return [
                                        `${context.dataset.label}: ${fmt(val)}`,
                                        ` • Masuk: ${fmt(extra.masuk)}`,
                                        ` • Keluar: ${fmt(extra.keluar)}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            ticks: { callback: (value) => 'Rp ' + value.toLocaleString('id-ID') } 
                        }
                    }
                }
            });
        }

        // --- 2. LOGIKA AJAX TABEL LAPORAN BULANAN ---
        const filterTahun = document.getElementById('filter-tahun');
        const tableBody = document.getElementById('table-body-report');

        if (filterTahun && tableBody) {
            function loadMonthlyData(tahun) {
                tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400">Memuat data...</td></tr>';

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
                                    <a href="${report.url}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 font-bold transition-all group">
                                        Detail Laporan <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                    
                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons({
                            icons: lucide.icons
                        });
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    tableBody.innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-red-500 font-bold">
                        Gagal memuat data: ${error.message}
                    </td></tr>`;
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