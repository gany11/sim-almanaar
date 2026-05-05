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
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Middle Section: Tabel Transaksi Terkini -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Transaksi Laporan Terkini</h3>
                <p id="current-title" class="text-sm text-gray-500">
                    <?= $current_report ? format_indo($current_report['started_at'], 'full') . ' - ' . format_indo($current_report['ended_at'], 'full') : 'Tidak ada periode laporan aktif' ?>
                </p>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto w-full custom-scrollbar">
                <table class="w-full text-sm text-left min-w-[600px]">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-right">Masuk</th>
                            <th class="px-6 py-4 text-right">Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($transactions)): ?>
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Belum ada transaksi pada periode ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach($transactions as $t): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500"><?= format_indo($t['created_at'], 'full_datetime') ?></td>
                            <td class="px-6 py-4 font-medium text-gray-700"><?= $t['keterangan'] ?></td>
                            <td class="px-6 py-4 text-right text-green-600 font-bold whitespace-nowrap">
                                <?= $t['jenis'] == 'pemasukan' ? 'Rp'.number_format($t['jumlah'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right text-red-600 font-bold whitespace-nowrap">
                                <?= $t['jenis'] == 'pengeluaran' ? 'Rp'.number_format($t['jumlah'], 0, ',', '.') : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Tabel Riwayat Laporan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Laporan Keuangan</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto w-full custom-scrollbar">
                <table class="w-full text-sm text-left min-w-[500px]">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Judul Laporan</th>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($history_reports)): ?>
                            <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada riwayat laporan yang diarsipkan.</td></tr>
                        <?php endif; ?>
                        <?php foreach($history_reports as $h): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-700 uppercase"><?= $h['judul'] ?></td>
                            <td class="px-6 py-4 text-gray-500 italic">
                                <?= format_indo($h['started_at'], 'full') ?> - <?= format_indo($h['ended_at'], 'full') ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= base_url('keuangan/'.date('Ymd', strtotime($h['started_at'])).'/'.date('Ymd', strtotime($h['ended_at'])).'/'.$h['id_laporan_mingguan']) ?>" 
                                class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-bold transition-colors">
                                    Detail <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-3 text-gray-600">
                <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5"></i>
                <p class="text-sm italic">
                    Membutuhkan data laporan keuangan yang lebih lama? Silakan hubungi 
                    <span class="font-bold text-gray-800">Bendahara/Pengurus Masjid</span> 
                    secara langsung di kantor sekretariat.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. INISIALISASI CHART ---
        const ctxLine = document.getElementById('lineChartKeuangan');
        if (ctxLine) {
            const totalData = <?= json_encode($chartDataTotal ?? []) ?>;
            const detailData = <?= json_encode($chartDetail ?? []) ?>;
            const labels = totalData.length > 0 ? totalData.map(item => item.bulan) : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
            
            const datasets = [];
            datasets.push({
                label: 'Total Saldo (Global)',
                data: totalData.map(item => item.masuk),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4
            });

            const colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#6366f1'];
            let colorIdx = 0;
            for (const [kategori, values] of Object.entries(detailData)) {
                const dataPoint = labels.map(bulan => {
                    const found = values.find(v => v.bulan === bulan);
                    return found ? parseFloat(found.neto) : 0;
                });
                datasets.push({
                    label: `Neto ${kategori}`,
                    data: dataPoint,
                    borderColor: colors[colorIdx % colors.length],
                    borderDash: [5, 5], 
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
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
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + value.toLocaleString('id-ID') } }
                    }
                }
            });
        }

        // --- 2. INISIALISASI DATATABLE (STATIS) ---
        if (typeof DataTable !== 'undefined') {
            
            new DataTable('#tableRoutine', {
                responsive: true,
                paging: false,
                info: false,
                dom: 'rt',
                columnDefs: [{ targets: [2, 3], orderable: false }]
            });

            new DataTable('#tableHistory', {
                responsive: true,
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: "Cari Laporan:",
                    lengthMenu: "Tampilkan _MENU_ data", 
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ laporan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 laporan",
                    infoFiltered: "(disaring dari _MAX_ total laporan)",
                    emptyTable: "Belum ada riwayat laporan.",
                    zeroRecords: "Laporan tidak ditemukan."
                },
                dom: '<"flex flex-col md:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"ip>',
                drawCallback: function() {
                    if (window.reinitIcons) window.reinitIcons();
                    else if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>