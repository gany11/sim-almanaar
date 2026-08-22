<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 py-10">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 text-sm font-medium">
            <li>
                <a href="<?= base_url() ?>" class="text-gray-700 hover:text-blue-600 flex items-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                    Beranda
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <a href="<?= base_url('keuangan') ?>" class="ml-2 text-gray-700 hover:text-blue-600 flex items-center">
                        Keuangan
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <span class="ml-2 text-gray-400">Detail Laporan</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight">
            Laporan Keuangan Bulan <?= esc($bulan_txt) ?>
        </h1>
        <p class="text-gray-500 mt-2 italic">
            Waktu Perolehan Data: <?= format_indo(date('Y-m-d H:i:s'), 'datetime') ?> WIB
        </p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 report-container">
        <?php if($isDraft): ?>
            <div class="watermark-layer">
                <?php for($i=0;$i<24;$i++): ?>
                    <span>DRAF</span>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-[10px]">
                <thead class="text-white bg-blue-600">
                    <tr>
                        <th rowspan="3" class="border border-gray-300 p-2 text-center uppercase w-64">Laporan Dana Keuangan Masjid</th>
                        <th colspan="<?= (count($saldo_awal) * 2) + 1 ?>" class="border border-gray-300 p-2 text-center uppercase"><?= $bulan_txt ?></th>
                    </tr>
                    <tr>
                        <?php foreach($saldo_awal as $sa): ?>
                            <th colspan="2" class="border border-gray-300 p-2 text-center"><?= $sa['kategori'] ?></th>
                        <?php endforeach; ?>
                        <th rowspan="2" class="border border-gray-300 p-2 text-center">TOTAL KAS</th>
                    </tr>
                    <tr>
                        <?php foreach($saldo_awal as $sa): ?>
                            <th class="border border-gray-300 p-1 text-center bg-blue-700">Masuk</th>
                            <th class="border border-gray-300 p-1 text-center bg-blue-800">Keluar</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal -->
                    <tr class="bg-gray-50 font-bold">
                        <td class="border border-gray-300 p-2">SALDO AWAL (Akumulasi)</td>
                        <?php $total_awal_global = 0; foreach($saldo_awal as $sa): $total_awal_global += $sa['saldo']; ?>
                            <td colspan="2" class="border border-gray-300 p-2 text-right">Rp<?= number_format($sa['saldo'], 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td class="border border-gray-300 p-2 text-right text-blue-600">Rp<?= number_format($total_awal_global, 0, ',', '.') ?></td>
                    </tr>

                    <!-- Looping Alokasi -->
                    <?php 
                    $footer_masuk = []; 
                    $footer_keluar = []; 
                    foreach($alokasi as $al): 
                        // Filter detail yang hanya punya transaksi
                        $detailsWithData = [];
                        foreach($al['details'] as $dt) {
                            $hasTransaction = false;
                            foreach($saldo_awal as $sa) {
                                if (isset($mapped_transaksi[$dt['id_detail_alokasi']][$sa['id_kategori_keuangan']])) {
                                    $hasTransaction = true; break;
                                }
                            }
                            if ($hasTransaction) $detailsWithData[] = $dt;
                        }

                        if (!empty($detailsWithData)): 
                    ?>
                        <tr class="bg-gray-100 font-bold text-gray-700">
                            <td colspan="<?= (count($saldo_awal) * 2) + 2 ?>" class="border border-gray-300 p-2 uppercase"><?= $al['nama_alokasi'] ?></td>
                        </tr>
                        
                        <?php foreach($detailsWithData as $dt): ?>
                            <tr>
                                <td class="border border-gray-300 p-2 pl-6 italic"><?= $dt['detail_alokasi'] ?></td>
                                <?php 
                                $row_total_neto = 0;
                                foreach($saldo_awal as $sa): 
                                    $masuk = $mapped_transaksi[$dt['id_detail_alokasi']][$sa['id_kategori_keuangan']]['pemasukan'] ?? 0;
                                    $keluar = $mapped_transaksi[$dt['id_detail_alokasi']][$sa['id_kategori_keuangan']]['pengeluaran'] ?? 0;
                                    
                                    $row_total_neto += ($masuk - $keluar);
                                    $footer_masuk[$sa['id_kategori_keuangan']] = ($footer_masuk[$sa['id_kategori_keuangan']] ?? 0) + $masuk;
                                    $footer_keluar[$sa['id_kategori_keuangan']] = ($footer_keluar[$sa['id_kategori_keuangan']] ?? 0) + $keluar;
                                ?>
                                    <td class="border border-gray-300 p-1 text-right text-green-600"><?= $masuk > 0 ? number_format($masuk, 0, ',', '.') : '-' ?></td>
                                    <td class="border border-gray-300 p-1 text-right text-red-600"><?= $keluar > 0 ? number_format($keluar, 0, ',', '.') : '-' ?></td>
                                <?php endforeach; ?>
                                <td class="border border-gray-300 p-2 text-right font-bold"><?= number_format($row_total_neto, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>

                    <!-- Total Transaksi Bulan Ini -->
                    <tr class="bg-blue-50 font-bold">
                        <td class="border border-gray-300 p-2">TOTAL MUTASI BULAN INI</td>
                        <?php $total_mutasi_global = 0; foreach($saldo_awal as $sa): 
                            $m = $footer_masuk[$sa['id_kategori_keuangan']] ?? 0;
                            $k = $footer_keluar[$sa['id_kategori_keuangan']] ?? 0;
                            $neto_kolom = $m - $k;
                            $total_mutasi_global += $neto_kolom;
                        ?>
                            <td class="border border-gray-300 p-1 text-right text-green-700"><?= ($m ? number_format($m, 0, ',', '.') : '-') ?></td>
                            <td class="border border-gray-300 p-1 text-right text-red-700"><?= ($k ? number_format($k, 0, ',', '.') : '-') ?></td>
                        <?php endforeach; ?>
                        <td class="border border-gray-300 p-2 text-right text-blue-800">Rp<?= number_format($total_mutasi_global, 0, ',', '.') ?></td>
                    </tr>

                    <!-- Saldo Akhir -->
                    <tr class="text-white bg-blue-600 font-black">
                        <td class="border border-gray-300 p-2">TOTAL SALDO AKHIR</td>
                        <?php $total_akhir_global = 0; foreach($saldo_awal as $sa): 
                            $akhir_kolom = $sa['saldo'] + ($footer_masuk[$sa['id_kategori_keuangan']] ?? 0) - ($footer_keluar[$sa['id_kategori_keuangan']] ?? 0);
                            $total_akhir_global += $akhir_kolom;
                        ?>
                            <td colspan="2" class="border border-gray-300 p-2 text-right text-sm">Rp<?= number_format($akhir_kolom, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td class="border border-gray-300 p-2 text-right text-base">Rp<?= number_format($total_akhir_global, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bagian 2: Detail Transaksi -->
    <?php if (!empty(session()->get('id_peran'))): ?>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 report-container">
            <?php if($isDraft): ?>
                <div class="watermark-layer">
                    <?php for($i=0;$i<24;$i++): ?>
                        <span>DRAF</span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            <div class="p-6 border-b border-gray-100 font-bold text-gray-800 uppercase italic flex justify-between items-center">
                <span>Rincian Transaksi Bulanan</span>
                <span class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-500 normal-case italic font-normal">
                    * Menampilkan transaksi hingga batas tutup buku
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="text-white bg-blue-600 uppercase text-[10px] font-black">
                        <tr>
                            <th class="border border-blue-500 px-4 py-3 text-center w-40">Kategori Kas</th>
                            <th class="border border-blue-500 px-4 py-3 text-center w-44">Tanggal</th>
                            <th class="border border-blue-500 px-4 py-3 text-center">Keterangan / Alokasi</th>
                            <th class="border border-blue-500 px-4 py-3 text-center w-36">Debet (Masuk)</th>
                            <th class="border border-blue-500 px-4 py-3 text-center w-36">Kredit (Keluar)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (empty($detail_transaksi)): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center italic text-gray-400 bg-gray-50">
                                    Tidak ada catatan transaksi pada periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $total_m = 0; 
                            $total_k = 0; 
                            foreach($detail_transaksi as $dt): 
                                if ($dt['jenis'] == 'pemasukan') $total_m += $dt['jumlah'];
                                else $total_k += $dt['jumlah'];
                            ?>
                                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                                    <td class="px-4 py-4 text-center border-x border-gray-100">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?= $dt['class_color'] ?>">
                                            <?= $dt['kategori'] ?>
                                        </span>
                                    </td>    
                                    <td class="px-4 py-4 text-center border-x border-gray-100">
                                        <span class="block font-bold text-gray-800"><?= format_indo($dt['tanggal'], 'full') ?></span>
                                        <span class="text-[9px] text-gray-400">Tercatat: <?= format_indo($dt['created_at'], 'full_datetime') ?></span>
                                    </td>
                                    <td class="px-4 py-4 border-x border-gray-100">
                                        <span class="font-bold text-gray-800 block leading-tight"><?= $dt['keterangan'] ?></span>
                                        <span class="text-[10px] text-blue-500 uppercase font-medium"><?= $dt['alokasi'].' | '.$dt['detail_alokasi'] ?></span>
                                    </td>
                                    <td class="px-4 py-4 text-right border-x border-gray-100 font-bold text-green-600 bg-green-50/30">
                                        <?= $dt['jenis'] == 'pemasukan' ? 'Rp' . number_format($dt['jumlah'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="px-4 py-4 text-right border-x border-gray-100 font-bold text-red-600 bg-red-50/30">
                                        <?= $dt['jenis'] == 'pengeluaran' ? 'Rp' . number_format($dt['jumlah'], 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($detail_transaksi)): ?>
                    <tfoot class="bg-gray-50 font-black text-gray-800">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right uppercase tracking-tighter">Total Mutasi Transaksi</td>
                            <td class="px-4 py-3 text-right border-x border-gray-200 text-green-700">
                                Rp<?= number_format($total_m, 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3 text-right border-x border-gray-200 text-red-700">
                                Rp<?= number_format($total_k, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td colspan="3" class="px-4 py-3 text-right uppercase tracking-tighter text-blue-800">Total Kas (Masuk - Keluar)</td>
                            <td colspan="2" class="px-4 py-3 text-center text-blue-900 text-base">
                                Rp<?= number_format($total_m - $total_k, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Catatan Penutup -->
    <div class="mt-12 p-8 bg-gray-900 rounded-3xl text-center text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-lg font-bold mb-2">Transparansi Data</h3>
            <p class="text-gray-400 text-sm max-w-xl mx-auto leading-relaxed">
                Data di atas merupakan laporan resmi Masjid Al Manaar Slipi. Segala bentuk pertanyaan atau klarifikasi mengenai rincian transaksi dapat diajukan kepada Bendahara Masjid pada jam kerja.
            </p>
        </div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
</div>
<style>
    .report-container{
        position:relative;
        overflow:hidden;
    }

    .watermark-layer{
        position:absolute;
        inset:0;

        display:flex;
        flex-wrap:wrap;
        justify-content:space-evenly;
        align-content:space-evenly;

        pointer-events:none;
        user-select:none;

        z-index:2;
    }

    .watermark-layer span{
        width:220px;
        text-align:center;

        font-size:42px;
        font-weight:bold;

        color:rgba(220,38,38,.08);

        transform:rotate(-35deg);
    }

    .report-container>*:not(.watermark-layer){
        position:relative;
        z-index:1;
    }
</style>
<?= $this->endSection() ?>