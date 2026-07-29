<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 px-4">
    <div class="mb-6 no-print">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Laporan Bulanan</h2>
                <p class="text-sm text-gray-500 italic">Bulan <?= esc($bulan_txt) ?></p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="<?= base_url('admin/finance/report/monthly') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                <button type="button" onclick="printReport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <div id="cetak" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12 mx-auto report-container <?= $isDraft ? 'draft-watermark' : '' ?>">       
        <div class="text-center mb-8 pb-4 border-b-2 border-black">
            <h1 style="font-size: 18pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 5pt;">Laporan Keuangan Bulanan Masjid Al-Manaar</h1>
            <p style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">BULAN: <?= esc($bulan_txt) ?></p>
        </div>

        <div class="mb-10 overflow-x-auto relative z-10">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border-2 border-black text-[11pt]">
                    
                    <thead>
                        <tr class="text-white bg-blue-600">
                            <th rowspan="3" class="border-2 border-black p-2 text-center uppercase w-64">Keterangan Alokasi Dana</th>
                            <th colspan="<?= (count($saldo_awal) * 2) + 1 ?>" class="border-2 border-black p-2 text-center uppercase">
                                Rincian Kas Per Kategori
                            </th>
                        </tr>
                        <tr class="text-white bg-blue-600">
                            <?php foreach($saldo_awal as $sa): ?>
                                <th colspan="2" class="border-2 border-black p-2 text-center"><?= $sa['kategori'] ?></th>
                            <?php endforeach; ?>
                            <th rowspan="2" class="border-2 border-black p-2 text-center">Total Kas</th>
                        </tr>
                        <tr class="text-white bg-blue-600">
                            <?php foreach($saldo_awal as $sa): ?>
                                <th class="border-2 border-black p-1 text-center">Masuk</th>
                                <th class="border-2 border-black p-1 text-center">Keluar</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($isDraft): ?>
                            <div class="watermark-layer">
                                <?php for($i=0;$i<24;$i++): ?>
                                    <span>DRAF</span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <tr class="text-white bg-blue-600 font-bold">
                            <td class="border-2 border-black p-2 italic">SALDO AWAL (Akumulasi Sebelum Periode)</td>
                            <?php $total_awal_global = 0; foreach($saldo_awal as $sa): 
                                $total_awal_global += $sa['saldo']; 
                            ?>
                                <td colspan="2" class="border-2 border-black p-2 text-right">Rp<?= number_format($sa['saldo'], 0, ',', '.') ?></td>
                            <?php endforeach; ?>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_awal_global, 0, ',', '.') ?></td>
                        </tr>

                        <?php 
                        $footer_masuk = []; 
                        $footer_keluar = []; 
                        foreach($alokasi as $al): 
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
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="<?= (count($saldo_awal) * 2) + 2 ?>" class="border-2 border-black p-2 uppercase"><?= $al['nama_alokasi'] ?></td>
                            </tr>
                            
                            <?php foreach($detailsWithData as $dt): ?>
                                <tr>
                                    <td class="border-2 border-black p-2 pl-6 italic"><?= $dt['detail_alokasi'] ?></td>
                                    <?php 
                                    $row_total_neto = 0;
                                    foreach($saldo_awal as $sa): 
                                        $masuk = $mapped_transaksi[$dt['id_detail_alokasi']][$sa['id_kategori_keuangan']]['pemasukan'] ?? 0;
                                        $keluar = $mapped_transaksi[$dt['id_detail_alokasi']][$sa['id_kategori_keuangan']]['pengeluaran'] ?? 0;
                                        
                                        $row_total_neto += ($masuk - $keluar);
                                        $footer_masuk[$sa['id_kategori_keuangan']] = ($footer_masuk[$sa['id_kategori_keuangan']] ?? 0) + $masuk;
                                        $footer_keluar[$sa['id_kategori_keuangan']] = ($footer_keluar[$sa['id_kategori_keuangan']] ?? 0) + $keluar;
                                    ?>
                                        <td class="border-2 border-black p-1 text-right"><?= $masuk > 0 ? number_format($masuk, 0, ',', '.') : '-' ?></td>
                                        <td class="border-2 border-black p-1 text-right"><?= $keluar > 0 ? number_format($keluar, 0, ',', '.') : '-' ?></td>
                                    <?php endforeach; ?>
                                    <td class="border-2 border-black p-2 text-right font-bold"><?= number_format($row_total_neto, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; endforeach; ?>

                        <tr class="text-white bg-blue-400 font-bold">
                            <td class="border-2 border-black p-2">TOTAL MUTASI BULAN INI</td>
                            <?php $total_mutasi_global = 0; foreach($saldo_awal as $sa): 
                                $m = $footer_masuk[$sa['id_kategori_keuangan']] ?? 0;
                                $k = $footer_keluar[$sa['id_kategori_keuangan']] ?? 0;
                                $neto_kolom = $m - $k;
                                $total_mutasi_global += $neto_kolom;
                            ?>
                                <td class="border-2 border-black p-1 text-right"><?= ($m ? number_format($m, 0, ',', '.') : '-') ?></td>
                                <td class="border-2 border-black p-1 text-right"><?= ($k ? number_format($k, 0, ',', '.') : '-') ?></td>
                            <?php endforeach; ?>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_mutasi_global, 0, ',', '.') ?></td>
                        </tr>

                        <tr class="text-white bg-blue-600 font-bold">
                            <td class="border-2 border-black p-2">TOTAL SALDO AKHIR</td>
                            <?php $total_akhir_global = 0; foreach($saldo_awal as $sa): 
                                $akhir_kolom = $sa['saldo'] + ($footer_masuk[$sa['id_kategori_keuangan']] ?? 0) - ($footer_keluar[$sa['id_kategori_keuangan']] ?? 0);
                                $total_akhir_global += $akhir_kolom;
                            ?>
                                <td colspan="2" class="border-2 border-black p-2 text-right text-base">Rp<?= number_format($akhir_kolom, 0, ',', '.') ?></td>
                            <?php endforeach; ?>
                            <td class="border-2 border-black p-2 text-right text-lg">Rp<?= number_format($total_akhir_global, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10 w-full relative z-10">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                <div style="text-align: center; flex: 1; min-width: 200px; max-width: 300px;">
                    <br class="no-print"/> <p style="margin-bottom: 0;">Ketua DKM,</p>
                    <div style="height: 70px;"></div> 
                    <p style="font-weight: bold; border-bottom: 1px solid black; display: inline-block; min-width: 180px; padding-bottom: 2px;">
                        ( ............................................ )
                    </p>
                </div>
                <div style="text-align: center; flex: 1; min-width: 200px; max-width: 300px;">
                    <p style="margin-bottom: 5px;">Jakarta, <?= format_indo(date('Y-m-d'), "full_date") ?></p>
                    <p style="margin-bottom: 0;">Bendahara,</p>
                    <div style="height: 70px;"></div>
                    <p style="font-weight: bold; border-bottom: 1px solid black; display: inline-block; min-width: 180px; padding-bottom: 2px;">
                        ( ............................................ )
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 print-page-break relative z-10">
            <div class="text-center mb-6 border-b-2 border-black pb-2 header-print-only hidden">
                <h2 style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">Lampiran: Rincian Transaksi</h2>
                <p style="font-size: 10pt; font-weight: bold;">BULAN: <?= esc($bulan_txt) ?></p>
            </div>

            <h2 class="text-title-rincian" style="font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 10pt; border-left: 4px solid black; padding-left: 10pt;">
                Rincian Transaksi
            </h2>
            
            <div class="mb-10 overflow-x-auto">
                <table class="w-full border-collapse border-2 border-black text-[10pt]">
                    <thead>
                        <tr class="text-white bg-blue-600">
                            <th class="border-2 border-black p-2 text-center w-36">Kategori Kas</th>
                            <th class="border-2 border-black p-2 text-center w-32">Tanggal</th>
                            <th class="border-2 border-black p-2 text-center">Keterangan / Alokasi</th>
                            <th class="border-2 border-black p-2 text-center w-36">Debet (Masuk)</th>
                            <th class="border-2 border-black p-2 text-center w-36">Kredit (Keluar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($isDraft): ?>
                            <div class="watermark-layer">
                                <?php for($i=0;$i<24;$i++): ?>
                                    <span>DRAF</span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <?php 
                        $total_masuk_all = 0;
                        $total_keluar_all = 0;

                        if (empty($detail_transaksi)): ?>
                            <tr>
                                <td colspan="5" class="border-2 border-black p-10 text-center italic text-gray-500">
                                    Tidak ada catatan transaksi pada periode ini.
                                </td>
                            </tr>
                        <?php else: 
                            foreach ($detail_transaksi as $item): 
                                if ($item['jenis'] == 'pemasukan') $total_masuk_all += $item['jumlah'];
                                else $total_keluar_all += $item['jumlah'];
                        ?>
                            <tr>
                                <td class="border-2 border-black p-2 text-center">
                                    <span style="text-transform: uppercase; font-weight: bold;"><?= $item['kategori'] ?></span>
                                </td>
                                <td class="border-2 border-black p-2 text-center whitespace-nowrap">
                                    <?= format_indo($item['tanggal'], 'full') ?>
                                    <div style="font-size: 8pt; color: #666;">Tanggal Catat Sistem: <?= format_indo($item['created_at'], 'datetime') ?></div>
                                </td>
                                <td class="border-2 border-black p-2">
                                    <div class="font-bold"><?= $item['keterangan'] ?></div>
                                    <div style="font-size: 9pt; color: #444; text-transform: uppercase;">
                                        Alokasi: <?= $item['alokasi'] ?> | <?= $item['detail_alokasi'] ?>
                                    </div>
                                </td>
                                <td class="border-2 border-black p-2 text-right">
                                    <?= $item['jenis'] == 'pemasukan' ? 'Rp' . number_format($item['jumlah'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="border-2 border-black p-2 text-right">
                                    <?= $item['jenis'] == 'pengeluaran' ? 'Rp' . number_format($item['jumlah'], 0, ',', '.') : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <?php if (!empty($detail_transaksi)): ?>
                    <tfoot>
                        <tr class="text-white bg-blue-600 font-bold">
                            <td colspan="3" class="border-2 border-black p-2 text-right uppercase">Total Mutasi Keseluruhan</td>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_masuk_all, 0, ',', '.') ?></td>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_keluar_all, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest gap-4">
            <p>Waktu Perolehan Data: <?= format_indo(date('Y-m-d H:i:s'), 'datetime') ?> WIB</p>
        </div>
    </div>
</div>

<style>
    .report-container{
        position:relative;
        overflow:hidden;
    }

    .watermark-layer{
        position: absolute;
        inset: 0;
        z-index: 1;

        display: flex;
        flex-wrap: wrap;
        align-content: space-evenly;
        justify-content: space-evenly;

        pointer-events: none;
        user-select: none;
    }

    .watermark-layer span{
        width: 220px;
        text-align: center;

        font-size: 42px;
        font-weight: bold;
        color: rgba(220,38,38,.08);

        transform: rotate(-35deg);
    }

    .report-container>*:not(.watermark-layer){
        position: relative;
        z-index: 2;
    }
</style>

<script>
function printReport() {
    var printContents = document.getElementById('cetak').outerHTML;
    var iframe = document.createElement('iframe');
    
    iframe.style.position = 'fixed';
    iframe.style.bottom = '0';
    iframe.style.right = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    
    document.body.appendChild(iframe);
    var doc = iframe.contentWindow.document;

    doc.open();
    doc.write('<html><head><title>Cetak Laporan Bulanan</title>');
    doc.write('<style>');
    doc.write('@page { size: A3 portrait; margin: 15mm; }');
    doc.write('body { font-family: "Times New Roman", serif; font-size: 11pt; line-height: 1.2; color: black; background: white; padding: 0; margin: 0; position: relative; }');
    
    /* CSS Tabled Border */
    doc.write('table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 2px solid black; }');
    doc.write('tr { page-break-inside: avoid !important; break-inside: avoid !important; }');
    doc.write('th, td { border: 1px solid black; padding: 6px; }');
    doc.write('tfoot{display:table-row-group;}');

    
    /* CSS Utility Classes yang terpakai di elemen */
    doc.write('.flex { display: flex; justify-content: space-between; }');
    doc.write('.text-right { text-align: right; }');
    doc.write('.text-center { text-align: center; }');
    doc.write('.font-bold { font-weight: bold; }');
    doc.write('.italic { font-style: italic; }');
    doc.write('.uppercase { text-transform: uppercase; }');
    
    /* CSS Pemisah Halaman (Page Break) */
    doc.write('.print-page-break { page-break-before: always; break-before: page; margin-top: 0; padding-top: 0; }');
    
    /* Logic memunculkan Header Lampiran saat di print */
    doc.write('.header-print-only { display: block !important; margin-top: 10px; }');
    doc.write('.text-title-rincian { display: none !important; }');
    doc.write('.hidden { display: none; }');
    
    /* Watermark CSS untuk Print */
    doc.write('.watermark-layer { display: none !important; }');
        
    <?php if ($isDraft): ?>

        doc.write('.report-container{position:relative;}');

        doc.write('.draft-watermark::before{');
        doc.write('content:"DRAF";');
        doc.write('position:fixed;');
        doc.write('top:50%;');
        doc.write('left:50%;');
        doc.write('transform:translate(-50%,-50%) rotate(-35deg);');

        doc.write('font-size:150px;');
        doc.write('font-weight:bold;');
        doc.write('letter-spacing:10px;');

        doc.write('color:rgba(220,38,38,.10);');

        doc.write('white-space:nowrap;');
        doc.write('pointer-events:none;');

        doc.write('z-index:-1;');
        doc.write('-webkit-print-color-adjust:exact;');
        doc.write('print-color-adjust:exact;');
        doc.write('}');

        doc.write('.report-container > *{');
        doc.write('position:relative;');
        doc.write('z-index:1;');
        doc.write('}');

    <?php endif; ?>
    
    /* Seting warna tegas printer */
    doc.write('* { color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
    doc.write('</style></head><body>');
    doc.write(printContents);
    doc.write('</body></html>');
    doc.close();

    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            document.body.removeChild(iframe);
        }, 500);
    }, 500);
}
</script>
<?= $this->endSection() ?>