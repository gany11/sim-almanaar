<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Ringkasan Operasional</h2>
        <p class="text-sm text-gray-500">Selamat datang di Panel SIM Al-Manaar.</p>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div id="flash-error" data-message="<?= session()->getFlashdata('error') ?>"></div>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?php if (!empty($summaryKeuangan)): ?>
            <?php foreach ($summaryKeuangan as $k) : ?>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 <?= !empty($k['class_color']) ? $k['class_color'] : 'bg-blue-50 text-blue-600' ?> rounded-2xl">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?= $k['kategori'] ?></span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Rp <?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?></h3>
                <p class="text-[10px] text-gray-400 mt-2 italic">Total Saldo Saat Ini</p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-4 text-center py-10 bg-gray-50 rounded-3xl border-2 border-dashed">
                <p class="text-gray-400 text-sm">Belum ada data keuangan yang tersedia.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800 italic uppercase text-xs tracking-widest">Visualisasi Kas</h3>
                </div>
                <div class="h-[350px]">
                    <canvas id="lineChartKeuangan"></canvas>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="newspaper" class="w-5 h-5 text-blue-600"></i> Publikasi Terbaru
                </h3>
                <div class="grid grid-col-1 md:grid-cols-2 gap-4">
                    <?php foreach ($publikasi as $p) : ?>
                        <div class="bg-white p-4 rounded-2xl border border-gray-50 flex items-center gap-4 hover:shadow-md transition-all cursor-pointer group">
                            <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                                <?php 
                                    // Tentukan folder berdasarkan id_jenis_publikasi (1=Berita, 2=Artikel)
                                    $folder = ($p['id_jenis_publikasi'] == 1) ? 'berita' : 'artikel';
                                    $path = base_url("uploads/{$folder}/" . $p['sampul']);
                                ?>
                                <img src="<?= $path ?>" 
                                    onerror="this.src='https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=200&h=200&auto=format&fit=crop'" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                            </div>
                            <div>
                                <span class="px-2 py-0.5 <?= $p['kategori_color'] ?? 'bg-blue-100 text-blue-600' ?> text-[9px] font-bold uppercase rounded-md">
                                    <?= $p['jenis_publikasi'] ?>
                                </span>
                                <h4 class="text-sm font-bold text-gray-800 line-clamp-1 mt-1"><?= $p['judul'] ?></h4>
                                <p class="text-[10px] text-gray-400 mt-1"><?= date('d M Y', strtotime($p['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if(empty($publikasi)): ?>
                    <div class="bg-white p-4 rounded-2xl border border-gray-50 text-center py-10">
                        <p class="text-xs text-gray-400">Belum ada publikasi aktif</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm h-full">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2 italic uppercase text-xs tracking-widest justify-center">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i> Agenda Mendatang
                </h3>
                <div class="space-y-6">
                    <?php foreach ($agenda as $a) : ?>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center justify-center w-12 h-12 bg-blue-600 text-white rounded-xl shrink-0 shadow-lg shadow-blue-100">
                                <span class="text-xs font-bold leading-none"><?= date('d', strtotime($a['waktu_mulai'])) ?></span>
                                <span class="text-[8px] uppercase font-bold"><?= date('M', strtotime($a['waktu_mulai'])) ?></span>
                            </div>
                            
                            <div class="overflow-hidden flex-1">
                                <span class="px-2 py-0.5 <?= $a['kategori_color'] ?? 'bg-gray-100 text-gray-600' ?> text-[8px] font-bold uppercase rounded-md mb-1 inline-block">
                                    <?= $a['nama_kategori'] ?>
                                </span>
                                
                                <h4 class="text-sm font-bold text-gray-800 leading-tight">
                                    <?= $a['judul'] ? $a['judul'] : '' ?>
                                    <?php if($a['judul'] && $a['tema']): ?> <span class="text-gray-400 font-normal">|</span> <?php endif; ?>
                                    <span class="text-blue-600 italic"><?= $a['tema'] ?></span>
                                </h4>

                                <div class="flex flex-col gap-1 mt-1">
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-medium">
                                        <i data-lucide="clock" class="w-3 h-3"></i> 
                                        <?= date('H:i', strtotime($a['waktu_mulai'])) ?> - <?= $a['waktu_selesai'] ? date('H:i', strtotime($a['waktu_selesai'])) : 'Selesai' ?>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-500">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-red-400"></i> 
                                        <span class="truncate"><?= $a['tempat'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($agenda)): ?>
                        <div class="text-center py-10">
                            <p class="text-xs text-gray-400">Belum ada agenda terdaftar</p>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('admin/agenda') ?>" class="block w-full text-center py-3 mt-8 bg-gray-50 text-gray-500 rounded-2xl text-xs font-bold hover:bg-gray-100 transition-all uppercase tracking-widest">
                    Lihat Semua Agenda
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctxLine = document.getElementById('lineChartKeuangan');
        
        // Data dari PHP
        const totalData = <?= json_encode($chartDataTotal) ?>;
        const detailData = <?= json_encode($chartDetail) ?>;
        
        // 1. Ambil Labels (Bulan) dari data total
        const labels = totalData.map(item => item.bulan);
        
        // 2. Siapkan Datasets
        const datasets = [];

        // Dataset A: Total Pemasukan (Garis Tebal)
        datasets.push({
            label: 'Total Saldo (Global)',
            data: totalData.map(item => item.masuk),
            borderColor: '#10b981',
            backgroundColor: '#10b98110',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        });

        // Dataset B: Per Kategori (Neto / Saldo per bulan)
        const colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#6366f1']; // Variasi warna
        let colorIdx = 0;

        for (const [kategori, values] of Object.entries(detailData)) {
            // Map data agar sesuai dengan bulan yang ada di label
            const dataPoint = labels.map(bulan => {
                const found = values.find(v => v.bulan === bulan);
                return found ? found.neto : 0;
            });

            datasets.push({
                label: `Saldo ${kategori}`,
                data: dataPoint,
                borderColor: colors[colorIdx % colors.length],
                borderDash: [5, 5], // Garis putus-putus untuk detail kategori
                borderWidth: 2,
                fill: false,
                tension: 0.4
            });
            colorIdx++;
        }

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['Jan', 'Feb', 'Mar'],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { boxWidth: 12, usePointStyle: true, font: { size: 11 } }
                    },
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
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>