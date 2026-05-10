<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 px-4">
    <div class="mb-6 no-print">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Laporan Periodik</h2>
                <p class="text-sm text-gray-500 italic">Filter data berdasarkan waktu pembuatan (Created At).</p>
            </div>
            <a href="<?= base_url('admin/finance/routine') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    
            <div class="flex items-end justify-between gap-4">
                
                <form action="" method="GET" 
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end flex-1">
                    
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Rentang Waktu
                        </label>
                        
                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-4 py-1.5 gap-3 
                                    focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 
                                    transition-all cursor-pointer">

                            <div class="flex items-center justify-center text-gray-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            
                            <input type="text" id="range_date" 
                                class="flex-1 bg-transparent border-none py-2 text-sm outline-none cursor-pointer text-gray-700 font-medium" 
                                placeholder="Pilih Tanggal" readonly>
                        </div>

                        <input type="hidden" name="start_date" id="start_date" value="<?= $start ?>">
                        <input type="hidden" name="end_date" id="end_date" value="<?= $end ?>">
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl 
                            font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg h-[46px]">
                        <i data-lucide="filter" class="w-4 h-4"></i> 
                        Filter Data
                    </button>

                </form>

                <div class="flex justify-end">
                    <button type="button" onclick="printReport()" 
                        class="w-auto whitespace-nowrap bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl 
                            font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg h-[46px]">
                        <i data-lucide="printer" class="w-4 h-4"></i> 
                        Cetak Laporan
                    </button>
                </div>

            </div>

        </div>
    </div>

    <div id="cetak" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12 max-w-5xl mx-auto" style="font-family: 'Times New Roman', Times, serif; color: black;">
        <div class="text-center mb-8 pb-4 border-b-2 border-black">
            <h1 style="font-size: 18pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 5pt;">Laporan Keuangan Periodik Masjid Al-Manaar</h1>
            <p style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">PERIODE: <?= date('d/m/Y', strtotime($start)) ?> s/d <?= date('d/m/Y', strtotime($end)) ?></p>
        </div>

        <!-- Bagian 1: Ringkasan Laporan (Matrix Kategori) -->
        <div class="mb-10 overflow-x-auto">
            <table class="w-full border-collapse border-2 border-black text-[11pt]">
                <thead>
                    <tr class="text-white bg-blue-600">
                        <th rowspan="3" class="border-2 border-black p-2 text-center uppercase w-64">Keterangan Alokasi Dana</th>
                        <th colspan="<?= (count($categories) * 2) + 1 ?>" class="border-2 border-black p-2 text-center uppercase">
                            Rincian Kas Per Kategori
                        </th>
                    </tr>
                    <tr class="text-white bg-blue-600">
                        <?php foreach($categories as $kat): ?>
                            <th colspan="2" class="border-2 border-black p-2 text-center"><?= $kat['kategori'] ?></th>
                        <?php endforeach; ?>
                        <th rowspan="2" class="border-2 border-black p-2 text-center">NETO</th>
                    </tr>
                    <tr class="text-white bg-blue-600">
                        <?php foreach($categories as $kat): ?>
                            <th class="border-2 border-black p-1 text-center">Masuk</th>
                            <th class="border-2 border-black p-1 text-center">Keluar</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal -->
                    <tr class="text-white bg-blue-600 font-bold">
                        <td class="border-2 border-black p-2 italic">SALDO AWAL (Akumulasi Sebelum Periode)</td>
                        <?php $total_awal_global = 0; foreach($categories as $kat): 
                            $s_awal = $saldo_awal[$kat['id_kategori_keuangan']] ?? 0;
                            $total_awal_global += $s_awal; 
                        ?>
                            <td colspan="2" class="border-2 border-black p-2 text-right">Rp<?= number_format($s_awal, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_awal_global, 0, ',', '.') ?></td>
                    </tr>

                    <!-- Looping Alokasi -->
                    <?php 
                    $footer_masuk = []; 
                    $footer_keluar = []; 
                    foreach($alokasi as $al): 
                        $detailsWithData = [];
                        foreach($al['details'] as $dt) {
                            $hasTransaction = false;
                            foreach($categories as $kat) {
                                if (isset($mapped_transaksi[$dt['id_detail_alokasi']][$kat['id_kategori_keuangan']])) {
                                    $hasTransaction = true; break;
                                }
                            }
                            if ($hasTransaction) $detailsWithData[] = $dt;
                        }

                        if (!empty($detailsWithData)): 
                    ?>
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="<?= (count($categories) * 2) + 2 ?>" class="border-2 border-black p-2 uppercase"><?= $al['nama_alokasi'] ?></td>
                        </tr>
                        
                        <?php foreach($detailsWithData as $dt): ?>
                            <tr>
                                <td class="border-2 border-black p-2 pl-6 italic"><?= $dt['detail_alokasi'] ?></td>
                                <?php 
                                $row_total_neto = 0;
                                foreach($categories as $kat): 
                                    $masuk = $mapped_transaksi[$dt['id_detail_alokasi']][$kat['id_kategori_keuangan']]['pemasukan'] ?? 0;
                                    $keluar = $mapped_transaksi[$dt['id_detail_alokasi']][$kat['id_kategori_keuangan']]['pengeluaran'] ?? 0;
                                    
                                    $row_total_neto += ($masuk - $keluar);
                                    $footer_masuk[$kat['id_kategori_keuangan']] = ($footer_masuk[$kat['id_kategori_keuangan']] ?? 0) + $masuk;
                                    $footer_keluar[$kat['id_kategori_keuangan']] = ($footer_keluar[$kat['id_kategori_keuangan']] ?? 0) + $keluar;
                                ?>
                                    <td class="border-2 border-black p-1 text-right"><?= $masuk > 0 ? number_format($masuk, 0, ',', '.') : '-' ?></td>
                                    <td class="border-2 border-black p-1 text-right"><?= $keluar > 0 ? number_format($keluar, 0, ',', '.') : '-' ?></td>
                                <?php endforeach; ?>
                                <td class="border-2 border-black p-2 text-right font-bold"><?= number_format($row_total_neto, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>

                    <!-- Total Mutasi Periode Ini -->
                    <tr class="text-white bg-blue-400 font-bold">
                        <td class="border-2 border-black p-2">TOTAL MUTASI PERIODE INI</td>
                        <?php $total_mutasi_global = 0; foreach($categories as $kat): 
                            $m = $footer_masuk[$kat['id_kategori_keuangan']] ?? 0;
                            $k = $footer_keluar[$kat['id_kategori_keuangan']] ?? 0;
                            $neto_kolom = $m - $k;
                            $total_mutasi_global += $neto_kolom;
                        ?>
                            <td class="border-2 border-black p-1 text-right"><?= ($m ? number_format($m, 0, ',', '.') : '-') ?></td>
                            <td class="border-2 border-black p-1 text-right"><?= ($k ? number_format($k, 0, ',', '.') : '-') ?></td>
                        <?php endforeach; ?>
                        <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_mutasi_global, 0, ',', '.') ?></td>
                    </tr>

                    <!-- Saldo Akhir -->
                    <tr class="text-white bg-blue-600 font-bold">
                        <td class="border-2 border-black p-2">TOTAL SALDO AKHIR (S/D <?= date('d/m/Y', strtotime($end)) ?>)</td>
                        <?php $total_akhir_global = 0; foreach($categories as $kat): 
                            $akhir_kolom = ($saldo_awal[$kat['id_kategori_keuangan']] ?? 0) + ($footer_masuk[$kat['id_kategori_keuangan']] ?? 0) - ($footer_keluar[$kat['id_kategori_keuangan']] ?? 0);
                            $total_akhir_global += $akhir_kolom;
                        ?>
                            <td colspan="2" class="border-2 border-black p-2 text-right text-base">Rp<?= number_format($akhir_kolom, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td class="border-2 border-black p-2 text-right text-lg">Rp<?= number_format($total_akhir_global, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bagian 2: Tanda Tangan (Opsional untuk Laporan Resmi) -->
        <div class="mt-10 w-full">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                
                <div style="text-align: center; flex: 1; min-width: 200px; max-width: 300px;">
                    <br class="no-print"/> <p style="margin-bottom: 0;">Ketua DKM,</p>
                    <div style="height: 70px;"></div> 
                    <p style="font-weight: bold; border-bottom: 1px solid black; display: inline-block; min-width: 180px; padding-bottom: 2px;">
                        ( ............................................ )
                    </p>
                </div>

                <div style="text-align: center; flex: 1; min-width: 200px; max-width: 300px;">
                    <p style="margin-bottom: 5px;">Jakarta, <?= format_indo(date('Y-m-d')) ?></p>
                    <p style="margin-bottom: 0;">Bendahara,</p>
                    <div style="height: 70px;"></div>
                    <p style="font-weight: bold; border-bottom: 1px solid black; display: inline-block; min-width: 180px; padding-bottom: 2px;">
                        ( ............................................ )
                    </p>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Detail Transaksi Periodik -->
        <div class="mt-8">
            <h2 style="font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 10pt; border-left: 4px solid black; padding-left: 10pt;">
                Rincian Transaksi
            </h2>
            
            <div class="mb-10 overflow-x-auto">
                <table class="w-full border-collapse border-2 border-black text-[10pt]">
                    <thead>
                        <tr class="text-white bg-blue-600">
                            <th class="border-2 border-black p-2 text-center w-32">Tanggal</th>
                            <th class="border-2 border-black p-2 text-center w-40">Kategori Kas</th>
                            <th class="border-2 border-black p-2 text-center">Keterangan / Alokasi</th>
                            <th class="border-2 border-black p-2 text-center w-36">Debet (Masuk)</th>
                            <th class="border-2 border-black p-2 text-center w-36">Kredit (Keluar)</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <td class="border-2 border-black p-2 text-center whitespace-nowrap">
                                    <?= format_indo($item['tanggal'], 'full') ?>
                                    <div style="font-size: 8pt; color: #666;">Tanggal Catat Sistem:<?= format_indo($item['created_at'], 'full') ?></div>
                                </td>
                                <td class="border-2 border-black p-2 text-center">
                                    <span style="text-transform: uppercase; font-weight: bold;"><?= $item['kategori'] ?></span>
                                </td>
                                <td class="border-2 border-black p-2">
                                    <div class="font-bold"><?= $item['keterangan'] ?></div>
                                    <div style="font-size: 9pt; color: #444; text-transform: uppercase;">
                                        Alokasi: <?= $item['detail_alokasi'] ?>
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
                    <tfoot>
                        <tr class="text-white bg-blue-600 font-bold">
                            <td colspan="3" class="border-2 border-black p-2 text-right uppercase">Total Mutasi Keseluruhan</td>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_masuk_all, 0, ',', '.') ?></td>
                            <td class="border-2 border-black p-2 text-right">Rp<?= number_format($total_keluar_all, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest gap-4">
            <p>Waktu Perolehan Data: <?= format_indo(date('Y-m-d H:i:s'), 'datetime') ?> WIB</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.flatpickr !== "undefined") {
        const getLastThursday = () => {
            let d = new Date();
            let day = d.getDay();
            
            let diff = (day <= 4) ? (day + 3) : (day - 4);
            
            d.setDate(d.getDate() - diff);
            return d;
        };

        window.flatpickr("#range_date", {
            mode: "range",
            dateFormat: "d/m/Y",
            maxDate: getLastThursday(),
            locale: "id",
            defaultDate: ["<?= date('d/m/Y', strtotime($start)) ?>", "<?= date('d/m/Y', strtotime($end)) ?>"],
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const start = instance.formatDate(selectedDates[0], "Y-m-d");
                    const end = instance.formatDate(selectedDates[1], "Y-m-d");
                    document.getElementById('start_date').value = start;
                    document.getElementById('end_date').value = end;
                }
            }
        });
    }
});


function printReport() {
    var printContents = document.getElementById('cetak').innerHTML;
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
    doc.write('<html><head><title>Cetak Laporan</title>');
    doc.write('<style>');
    doc.write('@page { size: 330mm 215mm; margin: 15mm; }');
    doc.write('body { font-family: "Times New Roman", serif; font-size: 11pt; line-height: 1.2; color: black; background: white; padding: 0; margin: 0; }');
    
    // TAMBAHKAN CSS BORDER DI SINI
    doc.write('table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 2px solid black; }');
    doc.write('th, td { border: 1px solid black; padding: 6px; }');
    
    doc.write('.flex { display: flex; justify-content: space-between; }');
    doc.write('.text-right { text-align: right; }');
    doc.write('.text-center { text-align: center; }');
    doc.write('.font-bold { font-weight: bold; }');
    
    // Memastikan printer mencetak warna/border dengan tegas
    doc.write('* { color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
    doc.write('.flex-signature { display: flex !important; justify-content: space-between !important; width: 100% !important; }');
    doc.write('.sig-box { text-align: center !important; width: 40% !important; }'); // Lebar box TTD saat dicetak
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