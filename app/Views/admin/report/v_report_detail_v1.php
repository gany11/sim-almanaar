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
            <?php if (in_array(session()->get('id_peran'), [3,4])): ?>
                <button onclick="printReport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </button>
            <?php endif; ?> 
        </div>
    </div>

    <div id='cetak' class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden max-w-5xl mx-auto" style="font-family: 'Times New Roman', Times, serif;">
        <div class="p-8 md:p-12">
            
            <div class="text-center mb-10 border-b-2 border-gray-100 pb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-tight">
                    Informasi Masjid Al-Manaar Sepekan
                </h1>
                <div class="inline-block mt-2 px-4 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider">
                    Periode Laporan: <?= date('d/m/Y', strtotime($report['started_at'])) ?> s/d <?= date('d/m/Y', strtotime($report['ended_at'])) ?>
                </div>
            </div>

            <div class="space-y-12">
                <div class="group-container">
                    <div class="border-b-2 border-black pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 uppercase italic">
                            I. Jadwal Kegiatan Sepekan (<?= date('d/m/Y', strtotime($nextWeekStart)) ?> - <?= date('d/m/Y', strtotime($nextWeekEnd)) ?>)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse border border-gray-300">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 w-1/6">Hari/Tgl</th>
                                    <th class="border border-gray-300 px-4 py-2 w-1/6">Kegiatan</th>
                                    <th class="border border-gray-300 px-4 py-2 w-1/6">Waktu</th>
                                    <th class="border border-gray-300 px-4 py-2 w-1/4">Pengisi/Pengajar</th>
                                    <th class="border border-gray-300 px-4 py-2">Tema / Judul</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($agendas)): ?>
                                    <tr><td colspan="5" class="border border-gray-300 py-4 text-center text-gray-400 italic">Belum ada jadwal kegiatan untuk pekan ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($agendas as $a): ?>
                                        <tr class="align-top">
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                <strong><?= format_indo($a['waktu_mulai'], 'full') ?></strong><br>
                                                <span class="text-xs italic text-gray-600"><?= format_hijriah($a['waktu_mulai']) ?></span>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center uppercase font-medium"><?= $a['nama_kategori'] ?></td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                <?= ($a['ket_mulai'] ?: date('H:i', strtotime($a['waktu_mulai']))) ?> s/d 
                                                <?= ($a['ket_selesai'] ?: ($a['waktu_selesai'] ? date('H:i', strtotime($a['waktu_selesai'])) : 'Selesai')) ?>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2">
                                                <?php foreach ($a['pengisi'] as $p): ?>
                                                    <div class="mb-1"><span class="font-bold uppercase text-[10px]"><?= $p['peran'] ?>:</span> <?= $p['nama'] ?></div>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 italic font-serif"><?= (!empty($a['tema']) && !empty($a['judul'])) ? esc($a['tema'])." (".esc($a['judul']).")" : (esc($a['tema']) ?: esc($a['judul'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="group-container">
                    <div class="border-b-2 border-black pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 uppercase italic">
                            II. Laporan Keuangan Sepekan (Hingga <?= date('d/m/Y', strtotime($report['ended_at'])) ?>)
                        </h3>
                    </div>

                    <?php 
                        $grandTotalSeluruhnya = 0;
                        foreach ($grouped as $kode => $g): 
                            $grandTotalSeluruhnya += $g['saldo_akhir'];
                    ?>
                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-gray-800 uppercase mb-2"><?= $kode ?>. <?= $g['judul'] ?></h4>
                            <div class="flex justify-between items-center bg-gray-50 border border-gray-200 px-4 py-2 mb-2 font-bold italic text-sm">
                                <span>Saldo Awal s/d <?= date('d/m/Y', strtotime($report['started_at'] . ' -1 day')) ?></span>
                                <span>Rp <?= number_format($g['saldo_awal'], 0, ',', '.') ?></span>
                            </div>

                            <table class="w-full text-sm border-collapse border border-gray-200">
                                <tbody>
                                    <?php if (empty($g['items'])): ?>
                                        <tr><td class="py-2 text-center text-gray-400 italic">--- Tidak ada transaksi pada periode ini ---</td></tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($g['items'] as $item): ?>
                                        <tr>
                                            <td class="border-b border-gray-100 py-1.5 px-2 w-8 text-gray-400"><?= $no++ ?>.</td>
                                            <td class="border-b border-gray-100 py-1.5 px-2 uppercase italic text-xs">
                                                <?= mb_strimwidth($item['keterangan'], 0, 60, "...") ?> 
                                                (<?= date('d/m/y', strtotime($item['tanggal'])) ?>) (<?= $item['jenis'] == 'pemasukan' ? '+' : '-' ?>)
                                            </td>
                                            <td class="border-b border-gray-100 py-1.5 px-2 text-right font-bold w-40">
                                                Rp <?= number_format($item['jumlah'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="flex justify-between items-center border-t border-black pt-2 mt-1 font-bold text-sm italic">
                                <span>TOTAL SALDO <?= strtoupper($g['judul']) ?></span>
                                <span class="border-b-2 border-double border-black">Rp <?= number_format($g['saldo_akhir'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-8 border-4 border-black p-4 flex justify-between items-center rounded-xl bg-gray-50">
                        <h4 class="font-bold text-sm uppercase italic">Total Kas Masjid (A+B+C)</h4>
                        <div class="text-2xl font-black italic tracking-tighter">
                            Rp <?= number_format($grandTotalSeluruhnya, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($report['catatan']) && $report['catatan'] != '-'): ?>
                <div class="p-4 border border-black rounded-xl bg-amber-50">
                    <h4 class="text-xs font-black uppercase underline mb-2">Catatan Laporan:</h4>
                    <div class="text-sm italic leading-relaxed prose prose-sm max-w-none">
                        <?= $report['catatan'] ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-10 pt-6 border-t border-gray-200 flex justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest italic">
                    <p>Waktu Perolehan Data: <?= date('d/m/Y H:i:s') ?> WIB</p>
                    <p>SIM Al-Manaar Slipi</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printReport() {
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
                            font-size: 10.5pt !important;
                            line-height: 1.15 !important;
                        }
                        .report-title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; margin-bottom: 2pt; }
                        .section-title { font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 8pt 0 4pt 0; border-bottom: 1.5pt solid black; display: inline-block; }
                        
                        table { 
                            border-collapse: collapse; 
                            margin-bottom: 8pt; 
                            table-layout: fixed;
                            border: 1.5pt solid black;
                        }
                        th, td { 
                            border: 0.5pt solid black; 
                            padding: 2pt 4pt; 
                            vertical-align: top;
                            word-wrap: break-word;
                        }
                        th { background-color: #f0f0f0 !important; -webkit-print-color-adjust: exact; text-transform: uppercase; font-size: 9pt; }
                        
                        .flex-container {
                            display: flex;
                            align-items: flex-start;
                            justify-content: flex-start;
                            gap: 20pt;
                            width: 100%;
                        }

                        /* Kolom Kiri (Tabel & Catatan) */
                        .left-column { width: 75%; }
                        
                        /* Tabel Keuangan */
                        .table-finance { width: 100% !important; }
                        
                        /* Box Catatan Laporan */
                        .report-note-box {
                            margin-top: 10pt;
                            padding: 8pt;
                            border: 1pt solid black;
                            background-color: #fcfcfc;
                            font-size: 9.5pt;
                        }
                        .note-title { font-weight: bold; text-decoration: underline; display: block; margin-bottom: 3pt; font-size: 10pt; }

                        /* Kolom Kanan (Tanda Tangan) */
                        .signature-area { 
                            width: 25%; 
                            display: flex; 
                            flex-direction: column; 
                            align-items: center;
                            text-align: center;
                        }
                        
                        .sig-box {
                            width: 100%;
                            margin-bottom: 30pt;
                        }

                        .sig-name {
                            font-weight: bold;
                            text-decoration: underline;
                            margin-top: 40pt;
                            display: block;
                        }

                        .col-nom { width: 30%; text-align: right; font-weight: bold; }
                        .row-header { background-color: #f0f0f0 !important; font-weight: bold; -webkit-print-color-adjust: exact; }
                        .row-total { font-weight: bold; background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
                        .center { text-align: center; }
                        .print-info { font-style: italic; font-size: 8pt; margin-top: 10pt; border-top: 0.5pt solid #ccc; padding-top: 4pt; width: 100%; }
                    </style>
                </head>
                <body>
                    <div class="report-title">INFORMASI MASJID AL-MANAAR SEPEKAN</div>
                    
                    <!-- I. JADWAL KEGIATAN -->
                    <div class="section-title">I. JADWAL KEGIATAN SEPEKAN (<?= format_indo($nextWeekStart, 'slash') ?> s/d <?= format_indo($nextWeekEnd, 'slash') ?>)</div>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Hari / Tgl</th>
                                <th style="width: 20%;">Kegiatan</th>
                                <th style="width: 20%;">Waktu</th>
                                <th style="width: 25%;">Pengisi / Pengajar</th>
                                <th style="width: 20%;">Tema / Judul</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($agendas)): ?>
                                <tr><td colspan="5" class="center italic">Belum ada jadwal kegiatan untuk pekan ini.</td></tr>
                            <?php else: ?>
                                <?php foreach($agendas as $a): ?>
                                <tr>
                                    <td class="center">
                                        <div style="font-weight: bold;"><?= format_indo($a['waktu_mulai'], 'full') ?></div>
                                        <div style="font-style: italic; font-size: 8.5pt; color: #333;"><?= format_hijriah($a['waktu_mulai']) ?></div>
                                    </td>
                                    <td class="center"><?= $a['nama_kategori'] ?></td>
                                    <td class="center">
                                        <?= ($a['ket_mulai'] ?: date('H:i', strtotime($a['waktu_mulai']))) ?> 
                                        s/d 
                                        <?= ($a['ket_selesai'] ?: ($a['waktu_selesai'] ? date('H:i', strtotime($a['waktu_selesai'])) : 'Selesai')) ?>
                                    </td>
                                    <td>
                                        <?php foreach($a['pengisi'] as $p): ?>
                                            <strong><?= $p['peran'] ?></strong>: <?= $p['nama'] ?><br>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="center">
                                        <?= (!empty($a['tema']) && !empty($a['judul'])) ? esc($a['tema'])." (".esc($a['judul']).")" : (esc($a['tema']) ?: esc($a['judul'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="section-title">II. LAPORAN KEUANGAN SEPEKAN (<?= format_indo($report['started_at'], 'slash') ?> s/d <?= format_indo($report['ended_at'], 'slash') ?>)</div>
                    
                    <div class="flex-container">
                        <!-- KOLOM KIRI: TABEL & CATATAN (75%) -->
                        <div class="left-column">
                            <table class="table-finance">
                                <?php 
                                $grandTotal = 0;
                                foreach ($grouped as $kode => $g): 
                                    $grandTotal += $g['saldo_akhir'];
                                ?>
                                    <tr class="row-header">
                                        <td style="width: 70%;"><?= $kode ?>. <?= strtoupper($g['judul']) ?> s/d Tgl. <?= date('d/m/y', strtotime($report['started_at'] . ' -1 day')) ?></td>
                                        <td class="col-nom">Rp <?= number_format($g['saldo_awal'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php if (!empty($g['items'])): ?>
                                        <?php foreach ($g['items'] as $item): ?>
                                            <tr>
                                                <td style="font-style: italic; padding-left: 10pt; font-size: 9pt;">
                                                    - <?= mb_strimwidth($item['keterangan'], 0, 75, "...") ?> 
                                                    (<?= date('d/m/y', strtotime($item['tanggal'])) ?>) (<?= $item['jenis'] == 'pemasukan' ? '+' : '-' ?>)
                                                </td>
                                                <td style="text-align: right;">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <tr class="row-total">
                                        <td>TOTAL SALDO <?= strtoupper($g['judul']) ?> s/d Tgl. <?= date('d/m/y', strtotime($report['ended_at'])) ?></td>
                                        <td class="col-nom">Rp <?= number_format($g['saldo_akhir'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="row-total" style="font-size: 11pt; border-top: 1.5pt solid black !important;">
                                    <td style="text-align: center;">TOTAL KAS MASJID AL-MANAAR (SELURUHNYA)</td>
                                    <td class="col-nom">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
                                </tr>
                            </table>

                            <!-- CATATAN DARI DATABASE -->
                            <?php if (!empty($report['catatan']) && $report['catatan'] != '-'): ?>
                            <div class="report-note-box">
                                <span class="note-title">Catatan Laporan:</span>
                                <div><?= $report['catatan'] ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- KOLOM KANAN: TANDA TANGAN (25%) -->
                        <div class="signature-area">
                            <div class="sig-box">
                                <span>Bendahara,</span>
                                <span class="sig-name">( ................................ )</span>
                            </div>
                            <div class="sig-box">
                                <span>Sekretaris,</span>
                                <span class="sig-name">( ................................ )</span>
                            </div>
                            
                            <div class="print-info">
                                Waktu Cetak Data:<br>
                                <?= format_indo(date('d/m/Y H:i:s'), 'slash') ?> WIB
                            </div>
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