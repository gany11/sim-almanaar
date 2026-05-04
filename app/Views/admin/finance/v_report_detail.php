<?= $this->extend('layout/admin/main') ?>


<?= $this->section('content') ?>

<div class="container mx-auto pb-10 px-4 md:px-0">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pratinjau Laporan</h2>
            <p class="text-sm text-gray-500 italic"><?= $report['judul'] ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/finance/routine') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <button onclick="printReport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak
            </button>
        </div>
    </div>

    <div id='cetak' class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden max-w-5xl mx-auto" style="font-family: 'Times New Roman', Times, serif;">
        <div class="p-8 md:p-12">
            
            <div class="text-center mb-10 border-b-2 border-gray-100 pb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-tight">
                    Laporan Keuangan Sepekan Masjid Al-Manaar
                </h1>
                <div class="inline-block mt-2 px-4 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider">
                    Periode: <?= date('d/m/Y', strtotime($report['started_at'])) ?> s/d <?= date('d/m/Y', strtotime($report['ended_at'])) ?>
                </div>
            </div>

            <div class="space-y-12">
                <?php 
                    $grandTotalSeluruhnya = 0;
                    foreach ($grouped as $kode => $g): 
                        $grandTotalSeluruhnya += $g['saldo_akhir'];
                    ?>
                    <div class="group-container">
                        <div class="border-b border-gray-200 pb-2 mb-4">
                            <h3 class="text-base font-bold text-gray-800 uppercase italic">
                                <?= $kode ?>. <?= $g['judul'] ?>
                            </h3>
                        </div>

                        <div class="flex justify-between items-center bg-gray-50 border border-gray-100 px-4 py-2 mb-4 rounded-xl font-bold italic">
                            <span class="text-sm text-gray-500 uppercase">
                                Saldo Awal s/d <?= date('d/m/Y', strtotime($report['started_at'] . ' -1 day')) ?>
                            </span>
                            <span class="text-sm text-gray-700">
                                Rp <?= number_format($g['saldo_awal'], 0, ',', '.') ?>
                            </span>
                        </div>

                        <div class="overflow-x-auto mb-4">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-gray-50">
                                    <?php if (empty($g['items'])): ?>
                                        <tr><td class="py-4 text-center text-gray-400 italic">--- Tidak ada transaksi pada periode ini ---</td></tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($g['items'] as $item): ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-2 w-8 text-gray-400"><?= $no++ ?>.</td>
                                            <td class="py-2 text-gray-700 uppercase">
                                                <?php 
                                                    $ket = $item['keterangan'];
                                                    echo mb_strimwidth($ket, 0, 45, "..."); 
                                                ?>
                                                (<?= date('d/m/Y', strtotime($item['tanggal'])) ?>)
                                            </td>
                                            <td class="py-2 text-right italic text-gray-400 text-xs w-32">
                                                <?= $item['jenis'] == 'pemasukan' ? '(+)' : '(-)' ?>
                                            </td>
                                            <td class="py-2 text-right text-gray-800 w-40">
                                                Rp <?= number_format($item['jumlah'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-between items-center border-t border-black pt-3 mt-2">
                            <span class="text-sm font-bold text-gray-800 uppercase italic">
                                Total Saldo <?= $g['judul'] ?> s/d <?= date('d/m/Y', strtotime($report['ended_at'])) ?>
                            </span>
                            <span class="text-sm font-bold text-gray-900 border-b-2 border-double border-gray-900 pb-0.5">
                                Rp <?= number_format($g['saldo_akhir'], 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-12 border-2 border-black p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-4 rounded-3xl">
                    <div class="text-center md:text-left">
                        <h4 class="text-black font-bold text-sm uppercase italic">
                            Total Kas Masjid Al-Manaar (A+B+C)
                        </h4>
                    </div>
                    <div class="text-2xl md:text-3xl font-bold text-black italic tracking-tighter">
                        Rp <?= number_format($grandTotalSeluruhnya, 0, ',', '.') ?>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest gap-4">
                    <p>Waktu Perolehan Data: <?= date('d/m/Y H:i:s') ?> WIB</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printReport() {
        const printContents = document.getElementById('cetak').innerHTML;
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        const doc = iframe.contentWindow.document;

        doc.open();
        doc.write(`
            <html>
                <head>
                    <style>
                        @page {
                            size: 330mm 215mm; /* F4 Landscape */
                            margin: 10mm 15mm;
                        }
                        
                        body {
                            font-family: "Times New Roman", Times, serif !important;
                            color: black !important;
                            background: white !important;
                            font-size: 12pt !important;
                            line-height: 1.1 !important;
                        }

                        .print-wrapper { width: 100%; }

                        #cetak, .rounded-3xl, .shadow-sm, .max-w-5xl, .p-8, .p-12, .space-y-12 {
                            padding: 0 !important;
                            margin: 0 !important;
                            max-width: 100% !important;
                            border: none !important;
                            box-shadow: none !important;
                        }

                        table { 
                            width: 100% !important; 
                            border-collapse: collapse !important; 
                            table-layout: fixed !important; 
                            border: 1.5pt solid black !important; /* Border luar tebal */
                            margin-bottom: 5pt;
                        }

                        td, th { 
                            border: 0.5pt solid black !important; /* Garis antar sel */
                            padding: 2pt 6pt !important;
                            vertical-align: middle !important;
                            word-wrap: break-word;
                        }

                        .col-keterangan { width: 75%; text-align: left; }
                        .col-nominal { width: 25%; text-align: right; font-weight: bold; }

                        .row-header { background-color: #f3f4f6 !important; font-weight: bold; -webkit-print-color-adjust: exact; }
                        .row-total { font-weight: bold; background-color: #e5e7eb !important; -webkit-print-color-adjust: exact; }
                        .italic-text { font-style: italic; }

                        .report-title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; margin-bottom: 5pt; }
                        .report-period { font-size: 10pt; font-weight: bold; text-align: center; margin-bottom: 15pt; }
                    </style>
                </head>
                <body>
                    <div class="print-wrapper">
                        <div class="report-title">LAPORAN KEUANGAN SEPEKAN MASJID AL-MANAAR PERIODE: <?= date('d/m/Y', strtotime($report['started_at'])) ?> s/d <?= date('d/m/Y', strtotime($report['ended_at'])) ?></div>

                        <table>
                            <?php 
                            $grandTotal = 0;
                            foreach ($grouped as $kode => $g): 
                                $grandTotal += $g['saldo_akhir'];
                            ?>
                                <tr class="row-header">
                                    <td class="col-keterangan"><?= $kode ?>. <?= strtoupper($g['judul']) ?> s/d Tgl. <?= date('d/m/y', strtotime($report['started_at'] . ' -1 day')) ?></td>
                                    <td class="col-nominal">Rp <?= number_format($g['saldo_awal'], 0, ',', '.') ?></td>
                                </tr>

                                <?php if (!empty($g['items'])): ?>
                                    <?php foreach ($g['items'] as $item): ?>
                                        <tr>
                                            <td class="col-keterangan italic-text">
                                                <?php 
                                                    $ket = $item['keterangan'];
                                                    echo mb_strimwidth($ket, 0, 45, "..."); 
                                                ?> 
                                                (<?= date('d/m/y', strtotime($item['tanggal'])) ?>) (<?= $item['jenis'] == 'pemasukan' ? '+' : '-' ?>)
                                            </td>
                                            <td class="col-nominal">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <tr class="row-total text-blue-800">
                                    <td class="col-keterangan"><?= $kode ?>. TOTAL SALDO <?= strtoupper($g['judul']) ?> s/d Tgl. <?= date('d/m/y', strtotime($report['ended_at'])) ?></td>
                                    <td class="col-nominal">Rp <?= number_format($g['saldo_akhir'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr class="row-total" style="font-size: 11pt; border-top: 2pt solid black !important;">
                                <td class="col-keterangan uppercase">TOTAL KAS YATIM/PKU - KAS MASJID - KAS PERAWATAN</td>
                                <td class="col-nominal" style="font-size: 12pt;">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
                            </tr>
                        </table>

                        <div style="font-style: italic; font-size: 8pt; margin-top: 5pt;">
                            Waktu Perolehan Data: <?= date('d/m/Y H:i:s') ?> WIB
                        </div>
                    </div>

                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(() => { window.frameElement.remove(); }, 100);
                        };
                    <\/script>
                </body>
            </html>
        `);
        doc.close();
    }
</script>
<?= $this->endSection() ?>