<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10 px-4 md:px-0">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pratinjau Laporan</h2>
            <p class="text-sm text-gray-500 italic"><?= $report['judul'] ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/finance/report/weekly') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <?php if (in_array(session()->get('id_peran'), [3,4])): ?>
                <button onclick="printReport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </button>
            <?php endif; ?> 
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-auto" style="font-family: 'Times New Roman', Times, serif;">
        <div class="p-8 md:p-12">
            
            <div class="text-center mb-2 border-b-2 border-gray-100 pb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-tight">
                    Informasi Masjid Al-Manaar Sepekan
                </h1>
            </div>

            <?php $isDraft = $report['ended_at'] > date('Y-m-d'); ?>
            <div id="cetak" class="space-y-12 report-container <?= $isDraft ? 'draft-watermark' : '' ?>">
                <?php if ($isDraft): ?>
                    <div class="watermark-layer">
                        <?php for($i=0; $i<24; $i++): ?>
                            <span>DRAF</span>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <div class="group-container agenda-section">
                    <div class="border-b-2 border-black pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 uppercase italic">
                            I. Jadwal Kegiatan Sepekan (<?= date('d/m/Y', strtotime($nextWeekStart)) ?> - <?= date('d/m/Y', strtotime($nextWeekEnd)) ?>)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border-2 border-black font-serif">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                                <tr>
                                    <th class="border border-black px-4 py-2 w-1/6">Hari/Tgl</th>
                                    <th class="border border-black px-4 py-2 w-1/6">Kegiatan</th>
                                    <th class="border border-black px-4 py-2 w-1/6">Waktu</th>
                                    <th class="border border-black px-4 py-2 w-1/4">Pengisi/Pengajar</th>
                                    <th class="border border-black px-4 py-2">Tema / Judul</th>
                                </tr>
                            </thead>
                            <?php
                                $groupedAgendas = [];

                                foreach ($agendas as $agenda) {
                                    $tanggal = date('Y-m-d', strtotime($agenda['waktu_mulai']));
                                    $groupedAgendas[$tanggal][] = $agenda;
                                }
                            ?>
                            <tbody>
                                <?php if (empty($groupedAgendas)): ?>

                                    <tr>
                                        <td colspan="5" class="border border-black py-4 text-center text-gray-400 italic">
                                            Belum ada jadwal kegiatan untuk pekan ini.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                <?php foreach ($groupedAgendas as $tanggal => $items): ?>

                                    <?php foreach ($items as $index => $a): ?>

                                    <tr class="align-top">

                                        <?php if ($index == 0): ?>
                                            <td class="border border-black px-3 py-2 text-center"
                                                rowspan="<?= count($items) ?>">
                                                <strong><?= format_indo($tanggal, 'full') ?></strong><br>
                                                <span class="text-xs italic text-gray-600">
                                                    <?= format_hijriah($tanggal) ?>
                                                </span>
                                            </td>
                                        <?php endif; ?>

                                        <td class="border border-black px-3 py-2 text-center uppercase font-medium">
                                            <?= esc($a['nama_kategori']) ?>
                                        </td>

                                        <td class="border border-black px-3 py-2 text-center">

                                            <?php if ($a['id_kategori_agenda'] == 1): ?>

                                                <?= $a['ket_mulai'] ?: 'Pukul '.date('H:i', strtotime($a['waktu_mulai']))." WIB" ?>

                                            <?php else: ?>

                                                <?= $a['ket_mulai'] ?: date('H:i', strtotime($a['waktu_mulai'])) ?>
                                                s/d
                                                <?= $a['ket_selesai'] ?: ($a['waktu_selesai']
                                                    ? date('H:i', strtotime($a['waktu_selesai']))
                                                    : 'Selesai') ?>

                                            <?php endif; ?>

                                        </td>

                                        <td class="border border-black px-3 py-2">
                                            <?php foreach ($a['pengisi'] as $p): ?>
                                                <div class="mb-1">
                                                    <span class="font-bold uppercase text-[10px]">
                                                        <?= esc($p['peran']) ?>:
                                                    </span>
                                                    <?= esc($p['nama']) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>

                                        <td class="border border-black px-3 py-2 italic font-serif">
                                            <?= (!empty($a['tema']) && !empty($a['judul']))
                                                ? esc($a['tema']) . ' (' . esc($a['judul']) . ')'
                                                : (esc($a['tema']) ?: esc($a['judul'])) ?>
                                        </td>

                                    </tr>

                                    <?php endforeach; ?>

                                <?php endforeach; ?>

                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="group-container finance-section">
                    <div class="border-b-2 border-black pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 uppercase italic">
                            II. <?= $report['judul']?>
                        </h3>
                    </div>

                    <div class="flex flex-row overflow-x-auto print-flex gap-6 items-stretch">
                        <div class="table-side w-2/3 flex-none">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border-2 border-black font-serif text-[10pt] md:text-[11pt]">
                                    <tbody>
                                        <?php 
                                        $grandTotalSeluruhnya = 0;
                                        
                                        // Hitung total group untuk mengecek apakah ini group terakhir
                                        $totalGroups = count($grouped);
                                        $loopIndex = 0;

                                        foreach ($grouped as $key => $data): 
                                            $loopIndex++;
                                            $grandTotalSeluruhnya += $data['saldo_akhir'];
                                            $tglAwal = date('d/m/y', strtotime($report['started_at'] . ' -1 day'));
                                            $tglAkhir = date('d/m/y', strtotime($report['ended_at']));
                                        ?>
                                            <tr class="bg-gray-100 font-bold">
                                                <td class="border-2 border-black p-1 uppercase">
                                                    <?= $key ?>. <?= $data['judul'] ?> s/d Tgl. <?= $tglAwal ?>
                                                </td>
                                                <td class="border-2 border-black p-1 text-right w-32 md:w-40">
                                                    Rp<?= number_format($data['saldo_awal'], 0, ',', '.') ?>
                                                </td>
                                            </tr>

                                            <?php if (empty($data['summary'])): ?>
                                                <tr>
                                                    <td class="border-2 border-black p-1 pl-6 italic" colspan="2"> Tidak Ada Pemasukan Maupun Pengeluaran</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($data['summary'] as $row): ?>
                                                    <tr>
                                                        <td class="border-2 border-black p-1 italic pl-6">
                                                            <div class="<?= ($row['jenis'] == 'pengeluaran') ? 'pl-6' : '' ?>">
                                                                - <?= $row['keterangan'] ?> 
                                                            </div>
                                                        </td>
                                                        <td class="border-2 border-black p-1 text-right">
                                                            <?php if ($row['jenis'] == 'pengeluaran'): ?>
                                                                (Rp<?= number_format($row['total'], 0, ',', '.') ?>)
                                                            <?php else: ?>
                                                                Rp<?= number_format($row['total'], 0, ',', '.') ?>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <tr class="bg-gray-200 font-bold uppercase text-sm">
                                                <td class="border-2 border-black p-1 pl-6">
                                                    TOTAL SALDO <?= $data['judul'] ?> s/d Tgl. <?= $tglAkhir ?>
                                                </td>
                                                <td class="border-2 border-black p-1 text-right">
                                                    Rp<?= number_format($data['saldo_akhir'], 0, ',', '.') ?>
                                                </td>
                                            </tr>

                                            <?php if ($loopIndex < $totalGroups): ?>
                                                <tr>
                                                    <td class="border-2 border-black p-1" colspan="2">&nbsp;</td>
                                                </tr>
                                            <?php endif; ?>

                                        <?php endforeach; ?>

                                        <tr class="bg-gray-300 font-bold uppercase text-[12pt]">
                                            <td class="border-2 border-black p-2 pl-6 text-center text-blue-900">
                                                TOTAL KAS SELURUHNYA
                                            </td>
                                            <td class="border-2 border-black p-2 text-right text-blue-900">
                                                Rp<?= number_format($grandTotalSeluruhnya, 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="ttd-side w-1/3 flex-none p-6 font-serif flex flex-col bg-gray-50/30">
                            <div class="text-center">
                                <p class="text-sm mb-1 italic text-gray-600">Jakarta, <?= format_indo(date('Y-m-d'), 'slash') ?></p>
                                <p class="text-lg font-medium">Sekretaris,</p>
                                <div class="h-24 md:h-36"></div> <p class="font-bold underline decoration-1 underline-offset-8">(............................................)</p>
                            </div>
                            
                            <div class="text-center mb-6">
                                <p class="text-lg font-medium">Bendahara,</p>
                                <div class="h-24 md:h-36"></div> <p class="font-bold underline decoration-1 underline-offset-8">(............................................)</p>
                            </div>
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
                    <p>Waktu Perolehan Data: <?= format_indo(date('Y-m-d H:i:s'), 'datetime') ?> WIB</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .report-container{
        position: relative;
        overflow: hidden;
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
        doc.write('<html><head><title>Cetak Laporan</title>');
        doc.write('<style>');
        doc.write('@page { size: 330mm 215mm; margin: 10mm; }');
        doc.write('body { font-family: "Times New Roman", serif; font-size: 11pt; line-height: 1.2; color: black; background: white; padding: 0; margin: 0; }');
        
        // Sembunyikan watermark kecil saat diprint
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
        
        doc.write('table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 2px solid black; }');
        doc.write('th, td { border: 1px solid black; padding: 4px 6px; }');
        
        // (3) PERTAHANKAN PADDING LEFT UNTUK PRINT
        doc.write('.pl-2 { padding-left: 8px !important; }');
        doc.write('.pl-6 { padding-left: 24px !important; }');
        
        doc.write('.print-flex { display: flex !important; flex-direction: row !important; gap: 15px !important; align-items: start !important; }');
        doc.write('.table-side { width: 75% !important; flex: none !important; }');
        doc.write('.ttd-side { width: 25% !important; border: 2px solid black !important; padding: 10px !important; display: block !important; }');
        doc.write('.ttd-side div { margin-bottom: 30px !important; }');
        
        doc.write('.text-right { text-align: right; }');
        doc.write('.text-center { text-align: center; }');
        doc.write('.font-bold { font-weight: bold; }');
        doc.write('.uppercase { text-transform: uppercase; }');
        doc.write('.italic { font-style: italic; }');
        
        doc.write('* { color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
        doc.write('</style></head><body>');
        doc.write(printContents);
        doc.write('</body></html>');
        doc.close();

        setTimeout(function() {
            const agenda = doc.querySelector('.agenda-section');
            const finance = doc.querySelector('.finance-section');

            if (agenda && finance) {

                // Sesuaikan tinggi halaman hasil percobaan
                const PAGE_HEIGHT = 2150;

                const totalHeight =
                    agenda.offsetHeight +
                    finance.offsetHeight;

                if (totalHeight > PAGE_HEIGHT) {
                    finance.style.breakBefore = 'page';
                    finance.style.pageBreakBefore = 'always';
                }
            }
            
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            setTimeout(function() {
                document.body.removeChild(iframe);
            }, 500);
        }, 500);
    }
</script>
<?= $this->endSection() ?>