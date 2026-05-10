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

        <?php 
        $grandTotal = 0;
        foreach ($grouped as $kode => $g): 
            $grandTotal += $g['saldo_akhir'];
        ?>
            <div style="margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid black; padding-bottom: 2px; margin-bottom: 8px; font-style: italic; text-transform: uppercase;">
                    <span><?= $kode ?>. <?= $g['judul'] ?></span>
                </div>

                <div style="display: flex; justify-content: space-between; border: 1px solid black; padding: 6px 15px; margin-bottom: 8px; font-weight: bold; font-style: italic; font-size: 10pt;">
                    <span>SALDO AWAL S/D <?= date('d/m/Y', strtotime($start . ' -1 day')) ?></span>
                    <span>RP <?= number_format($g['saldo_awal'], 0, ',', '.') ?></span>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11pt;">
                    <tbody>
                    <?php if (empty($g['items'])): ?>
                        <tr><td colspan="4" style="font-style: italic; text-align: center; padding: 10px;">--- Tidak ada transaksi pada periode ini ---</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($g['items'] as $item): ?>
                            <tr>
                                <td style="width: 30px; padding: 4px 0;"><?= $no++ ?>.</td>
                                <td style="text-transform: uppercase; padding: 4px 0;"><?= mb_strimwidth($item['keterangan'], 0, 60, "...") ?> (<?= date('d/m/y', strtotime($item['created_at'])) ?>)</td>
                                <td style="text-align: right; font-style: italic; font-size: 9pt; width: 120px; padding: 4px 0;">(<?= $item['jenis'] == 'pemasukan' ? '+' : '-' ?>)</td>
                                <td style="text-align: right; font-weight: bold; width: 150px; padding: 4px 0;">RP <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>

                <div style="border-top: 1px solid black; padding-top: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 10pt; font-style: italic; margin-bottom: 2px;">
                        <span>Total Pemasukan Periode Ini (+)</span>
                        <span>RP <?= number_format($g['total_masuk'], 0, ',', '.') ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10pt; font-style: italic; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 4px;">
                        <span>Total Pengeluaran Periode Ini (-)</span>
                        <span>RP <?= number_format($g['total_keluar'], 0, ',', '.') ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: bold;">
                        <span>TOTAL SALDO <?= strtoupper($g['judul']) ?></span>
                        <span style="border-bottom: 4px double black;">RP <?= number_format($g['saldo_akhir'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 30px; border: 2px solid black; padding: 15px; display: flex; justify-content: space-between; font-weight: bold; background-color: #f9f9f9;">
            <span style="text-transform: uppercase;">TOTAL KAS KESELURUHAN (A+B+C+D)</span>
            <span style="font-size: 14pt; font-style: italic; text-decoration: underline;">RP <?= number_format($grandTotal, 0, ',', '.') ?></span>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest gap-4">
            <p>Waktu Perolehan Data: <?= date('d/m/Y H:i:s') ?> WIB</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.flatpickr !== "undefined") {
        window.flatpickr("#range_date", {
            mode: "range",
            dateFormat: "d/m/Y",
            maxDate: "today",
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

// Fungsi cetak tetap menggunakan var dan string concatenation agar aman di iframe
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
    doc.write('table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }');
    doc.write('.flex { display: flex; justify-content: space-between; }');
    doc.write('* { color: black !important; -webkit-print-color-adjust: exact; }');
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