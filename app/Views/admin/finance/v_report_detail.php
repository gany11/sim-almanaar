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

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden max-w-5xl mx-auto" style="font-family: 'Times New Roman', Times, serif;">
        <div class="p-8 md:p-12">
            
            <div class="text-center mb-2 border-b-2 border-gray-100 pb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-tight">
                    Informasi Masjid Al-Manaar Sepekan
                </h1>
            </div>

            <div id="cetak" class="space-y-12">
                <div class="group-container">
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
                            <tbody>
                                <?php if (empty($agendas)): ?>
                                    <tr><td colspan="5" class="border border-black py-4 text-center text-gray-400 italic">Belum ada jadwal kegiatan untuk pekan ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($agendas as $a): ?>
                                        <tr class="align-top">
                                            <td class="border border-black px-3 py-2 text-center">
                                                <strong><?= format_indo($a['waktu_mulai'], 'full') ?></strong><br>
                                                <span class="text-xs italic text-gray-600"><?= format_hijriah($a['waktu_mulai']) ?></span>
                                            </td>
                                            <td class="border border-black px-3 py-2 text-center uppercase font-medium"><?= $a['nama_kategori'] ?></td>
                                            <td class="border border-black px-3 py-2 text-center">
                                                <?= ($a['ket_mulai'] ?: date('H:i', strtotime($a['waktu_mulai']))) ?> s/d 
                                                <?= ($a['ket_selesai'] ?: ($a['waktu_selesai'] ? date('H:i', strtotime($a['waktu_selesai'])) : 'Selesai')) ?>
                                            </td>
                                            <td class="border border-black px-3 py-2">
                                                <?php foreach ($a['pengisi'] as $p): ?>
                                                    <div class="mb-1"><span class="font-bold uppercase text-[10px]"><?= $p['peran'] ?>:</span> <?= $p['nama'] ?></div>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class="border border-black px-3 py-2 italic font-serif"><?= (!empty($a['tema']) && !empty($a['judul'])) ? esc($a['tema'])." (".esc($a['judul']).")" : (esc($a['tema']) ?: esc($a['judul'])) ?></td>
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
                            II. <?= $report['judul']?>
                        </h3>
                    </div>

                    <!-- Gunakan Flexbox untuk menyejajarkan Tabel dan TTD -->
                    <div class="flex-container" style="display: flex; gap: 20px; align-items: stretch;">
                        
                        <!-- Tabel Keuangan (Lebar diatur via CSS Print) -->
                        <div class="table-side" style="flex: 1;">
                            <table class="w-full border-collapse border-2 border-black font-serif text-[11pt]">
                                <tbody>
                                    <?php 
                                    $grandTotalSeluruhnya = 0;
                                    foreach ($grouped as $key => $data): 
                                        $grandTotalSeluruhnya += $data['saldo_akhir'];
                                        $tglAwal = date('d/m/y', strtotime($report['started_at'] . ' -1 day'));
                                        $tglAkhir = date('d/m/y', strtotime($report['ended_at']));
                                    ?>
                                        <!-- Konten Tabel Anda Tetap Sama Seperti Sebelumnya -->
                                        <tr class="bg-gray-100 font-bold">
                                            <td class="border-2 border-black p-1 uppercase">
                                                <?= $key ?>. <?= $data['judul'] ?> s/d Tgl. <?= $tglAwal ?>
                                            </td>
                                            <td class="border-2 border-black p-1 text-right w-48">
                                                Rp <?= number_format($data['saldo_awal'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php foreach ($data['summary'] as $row): ?>
                                            <tr>
                                                <td class="border-2 border-black p-1 pl-6 italic">
                                                    - <?= $row['keterangan'] ?> 
                                                    <?php if(!empty($row['alokasi'])): ?>
                                                        (<?= implode(', ', $row['alokasi']) ?>)
                                                    <?php endif; ?>
                                                    <?= $row['jenis'] == 'pemasukan' ? '(+)' : '(-)' ?>
                                                </td>
                                                <td class="border-2 border-black p-1 text-right">
                                                    Rp <?= number_format($row['total'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-gray-200 font-bold uppercase">
                                            <td class="border-2 border-black p-1">
                                                TOTAL SALDO <?= $data['judul'] ?> s/d Tgl. <?= $tglAkhir ?>
                                            </td>
                                            <td class="border-2 border-black p-1 text-right">
                                                Rp <?= number_format($data['saldo_akhir'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="bg-gray-300 font-bold uppercase text-[12pt]">
                                        <td class="border-2 border-black p-2 text-center">
                                            TOTAL KAS MASJID AL-MANAAR (SELURUHNYA)
                                        </td>
                                        <td class="border-2 border-black p-2 text-right">
                                            Rp <?= number_format($grandTotalSeluruhnya, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Kolom Tanda Tangan (Sembunyi di Layar, Muncul di Cetak) -->
                        <div class="ttd-side" style="width: 25%; flex-direction: column; justify-content: space-between; font-family: 'Times New Roman', serif;">
                            <div style="text-align: center; margin-top: 10px;">
                                <p>Jakarta, <?= format_indo(date('Y-m-d'), 'date') ?></p>
                                <p style="margin-bottom: 60px;">Sekertaris,</p>
                                <p class="font-bold">( ............................ )</p>
                            </div>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <p style="margin-bottom: 60px;">Bendahara,</p>
                                <p class="font-bold">( ............................ )</p>
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
                    <p>SIM Al-Manaar Slipi</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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