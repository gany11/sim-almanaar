<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 px-4">

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Grafik Statistik</h2>
            <p class="text-sm text-gray-500 italic">
                Visualisasi statistik keuangan (6 bulan terakhir).
            </p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- Alokasi -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    Alokasi
                </label>

                <select
                    id="filter-alokasi"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-emerald-500">

                    <option value="">Semua Alokasi</option>

                    <?php foreach ($alokasi as $item): ?>
                        <option value="<?= esc($item['id_alokasi']) ?>">
                            <?= esc($item['nama_alokasi']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Detail -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    Detail Alokasi
                </label>
                <select id="filter-detail"
                    disabled
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-emerald-500">

                    <option value="">Silakan pilih alokasi terlebih dahulu</option>

                </select>
            </div>

        </div>
    </div>

    <!-- Line Chart -->
    <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 mb-6">

        <div class="px-6 py-5 border-b">
            <h3 id="title-line" class="font-semibold text-gray-800">
                Tren Keuangan
            </h3>
            <p class="text-sm text-gray-500">
                Grafik perkembangan transaksi berdasarkan filter.
            </p>
        </div>

        <div class="p-6">
           <div class="h-[400px] xl:h-[600px]">
                <canvas id="lineChartKeuangan"></canvas>
            </div>
        </div>

    </div>

    <!-- Pie Chart -->
    <div class="grid md:grid-cols-2 gap-6">

        <!-- Chart 1 -->
        <div class="bg-white p-3 rounded-2xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h3 class="font-semibold text-gray-800">
                    Pemasukan Berdasarkan Kategori
                </h3>
            </div>

            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="pieMasukKategori"></canvas>
                </div>
            </div>

        </div>

        <!-- Chart 2 -->
        <div class="bg-white p-3 rounded-2xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h3 class="font-semibold text-gray-800">
                    Pengeluaran Berdasarkan Kategori
                </h3>
            </div>

            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="pieKeluarKategori"></canvas>
                </div>
            </div>

        </div>

        <!-- Chart 3 -->
        <div class="bg-white p-3 rounded-2xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h3 class="font-semibold text-gray-800">
                    Distribusi Berdasarkan Alokasi
                </h3>
            </div>

            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="pieAlokasi"></canvas>
                </div>
            </div>

        </div>

        <!-- Chart 4 -->
        <div class="bg-white p-3 rounded-2xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h3 class="font-semibold text-gray-800">
                    Distribusi Berdasarkan Detail Alokasi
                </h3>
            </div>

            <div class="p-6">
                <div class="h-[320px]">
                    <canvas id="pieDetail"></canvas>
                </div>
            </div>

        </div>

    </div>

</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const colors = [
                '#10b981',
                '#3b82f6',
                '#f59e0b',
                '#8b5cf6',
                '#ef4444',
                '#06b6d4',
                '#14b8a6',
                '#f97316'
            ];

            const alokasi = document.getElementById('filter-alokasi');
            const detail = document.getElementById('filter-detail');
            
            let lineChart;
            let pieMasuk;
            let pieKeluar;
            let pieAlokasi;
            let pieDetail;

            // ====================================
            // LOAD DATA
            // ====================================

            function loadChart() {

                const params = new URLSearchParams({
                    alokasi: alokasi.value,
                    detail: detail.value
                });

                document.getElementById('title-line').textContent =
                (alokasi.value || detail.value)
                    ? 'Tren Netto per Kategori'
                    : 'Tren Saldo per Kategori';


                fetch("<?= site_url('admin/report/chart/chart-data') ?>?" + params.toString())
                    .then(res => res.json())
                    .then(data => {

                        updateLine(data.line);

                        updatePie(
                            pieMasuk,
                            data.kategori_masuk,
                            'kategori',
                            'pieMasukKategori'
                        );

                        updatePie(
                            pieKeluar,
                            data.kategori_keluar,
                            'kategori',
                            'pieKeluarKategori'
                        );

                        updatePie(
                            pieAlokasi,
                            data.alokasi,
                            'nama_alokasi',
                            'pieAlokasi'
                        );

                        updatePie(
                            pieDetail,
                            data.detail,
                            'detail_alokasi',
                            'pieDetail'
                        );

                    });

            }

            // ====================================
            // LINE CHART
            // ====================================

            function updateLine(detailData) {

                let labels = [];

                // Ambil label bulan
                Object.values(detailData).forEach(values => {
                    if (values.length > labels.length) {
                        labels = values.map(item => item.bulan);
                    }
                });

                // Bangun dataset
                const datasets = [];
                let colorIndex = 0;

                Object.entries(detailData).forEach(([kategori, values]) => {

                    datasets.push({

                        label: kategori,

                        data: labels.map(bulan => {

                            const found = values.find(v => v.bulan === bulan);

                            return found ? Number(found.saldo_akhir) : 0;

                        }),

                        extra: labels.map(bulan => {

                            const found = values.find(v => v.bulan === bulan);

                            return found
                                ? {
                                    masuk: Number(found.masuk),
                                    keluar: Number(found.keluar)
                                }
                                : {
                                    masuk: 0,
                                    keluar: 0
                                };

                        }),

                        borderColor: colors[colorIndex % colors.length],
                        backgroundColor: colors[colorIndex % colors.length],
                        borderWidth: 2,
                        tension: 0.35,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6

                    });

                    colorIndex++;

                });

                console.log("Datasets :", datasets);

                // Destroy chart lama
                if (lineChart) {
                    lineChart.destroy();
                }

                // Buat chart baru
                lineChart = new Chart(document.getElementById('lineChartKeuangan'), {

                    type: 'line',

                    data: {
                        labels: labels,
                        datasets: datasets
                    },

                    options: {

                        responsive: true,
                        maintainAspectRatio: false,

                        interaction: {
                            mode: 'nearest',
                            intersect: true
                        },

                        plugins: {

                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 12
                                }
                            },

                            tooltip: {

                                callbacks: {

                                    label(context) {

                                        const extra = context.dataset.extra[context.dataIndex];

                                        const rupiah = value =>
                                            'Rp ' + Number(value).toLocaleString('id-ID');

                                        const isFilter = alokasi.value !== '' || detail.value !== '';
                                        
                                        return [
                                            context.dataset.label,
                                            (isFilter ? 'Netto' : 'Saldo') + ' : ' + rupiah(context.parsed.y),
                                            'Pemasukan : ' + rupiah(extra.masuk),
                                            'Pengeluaran : ' + rupiah(extra.keluar)
                                        ];

                                    }

                                }

                            }

                        },

                        scales: {

                            y: {

                                beginAtZero: false,

                                ticks: {

                                    callback(value) {
                                        return 'Rp ' + Number(value).toLocaleString('id-ID');
                                    }

                                }

                            }

                        }

                    }

                });

            }

            // ====================================
            // PIE CHART
            // ====================================

            function updatePie(chart,data,labelKey,canvasId){

                const labels = data.map(x=>x[labelKey]);
                const values = data.map(x=>x.total);
                const transaksi = data.map(x=>x.transaksi);

                if(!chart){

                    chart = new Chart(document.getElementById(canvasId),{

                        type:'pie',

                        data:{
                            labels:labels,
                            datasets:[{
                                data:values,
                                backgroundColor:colors
                            }]
                        },

                        options:{
                            responsive:true,
                            maintainAspectRatio:false,

                            plugins:{

                                legend:{
                                    position:'bottom'
                                },

                                tooltip: {
                                    callbacks: {
                                        label(context) {

                                            const values = context.dataset.data.map(Number);

                                            const total = values.reduce((a, b) => a + b, 0);

                                            const value = Number(values[context.dataIndex]);

                                            const persen = total > 0
                                                ? ((value / total) * 100).toFixed(1)
                                                : 0;

                                            return [
                                                context.label,
                                                'Nominal : Rp ' + value.toLocaleString('id-ID'),
                                                'Persentase : ' + persen + '%',
                                                'Transaksi : ' + transaksi[context.dataIndex]
                                            ];
                                        }
                                    }
                                }

                            }

                        }

                    });

                }else{

                    chart.data.labels = labels;
                    chart.data.datasets[0].data = values;

                    chart.update();

                }

                switch(canvasId){

                    case 'pieMasukKategori':
                        pieMasuk = chart;
                    break;

                    case 'pieKeluarKategori':
                        pieKeluar = chart;
                    break;

                    case 'pieAlokasi':
                        pieAlokasi = chart;
                    break;

                    case 'pieDetail':
                        pieDetail = chart;
                    break;

                }

            }

            // ====================================
            // FILTER DETAIL
            // ====================================

            alokasi.addEventListener('change', function () {

                if(this.value==''){

                    detail.disabled = true;

                    detail.innerHTML = `
                        <option value="">
                            Silakan pilih alokasi terlebih dahulu
                        </option>
                    `;

                    loadChart();

                    return;

                }

                detail.disabled = false;
                detail.innerHTML = '<option>Memuat...</option>';

                fetch("<?= site_url('admin/report/chart/detail-alokasi') ?>?id_alokasi="+this.value)
                .then(res=>res.json())
                .then(data=>{

                    detail.innerHTML='<option value="">Semua Detail</option>';

                    data.forEach(item=>{

                        detail.innerHTML+=`
                            <option value="${item.id_detail_alokasi}">
                                ${item.detail_alokasi}
                            </option>
                        `;

                    });

                    loadChart();

                });

            });

            detail.addEventListener('change',function(){

                loadChart();

            });

            // ====================================
            // LOAD PERTAMA
            // ====================================

            loadChart();

        });
    </script>
<?= $this->endSection() ?>