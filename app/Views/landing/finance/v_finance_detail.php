<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>
<div class="max-w-5xl mx-auto px-4 py-10">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="<?= base_url() ?>" class="inline-flex items-center text-gray-700 hover:text-blue-600">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                    Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <a href="<?= base_url('keuangan') ?>" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                        Keuangan
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    <span class="ml-1 text-gray-400 md:ml-2 truncate max-w-[150px] md:max-w-none">
                        Detail Laporan
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight"><?= esc($report['judul']) ?></h1>
    </div>

    <!-- Grouped Kas Sections -->
    <div class="space-y-10">
        <?php foreach ($grouped as $key => $group): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Kas -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-blue-800"><?= $group['judul'] ?></h2>
                <div class="text-right">
                    <span class="text-xs text-gray-400 uppercase tracking-widest block">Saldo Akhir</span>
                    <span class="text-lg font-black text-gray-900">Rp <?= number_format($group['saldo_akhir'], 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="p-6">
                <!-- Saldo Awal Info -->
                <div class="flex justify-between items-center mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <span class="text-blue-700 font-medium">Saldo Awal Periode</span>
                    <span class="font-bold text-blue-800 text-lg">Rp <?= number_format($group['saldo_awal'], 0, ',', '.') ?></span>
                </div>

                <!-- Table Transaksi -->
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-100 uppercase text-[10px] tracking-wider font-bold">
                                <th class="pb-3">Keterangan / Deskripsi</th>
                                <th class="pb-3 text-right">Penerimaan</th>
                                <th class="pb-3 text-right">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(empty($group['items'])): ?>
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-400 italic font-light">Tidak ada mutasi pada kas ini selama periode laporan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($group['items'] as $item): ?>
                                <tr>
                                    <td class="py-3 pr-4 text-gray-700 leading-snug"><?= esc($item['keterangan']) ?></td>
                                    <td class="py-3 text-right text-green-600 whitespace-nowrap">
                                        <?= $item['jenis'] == 'pemasukan' ? 'Rp '.number_format($item['jumlah'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="py-3 text-right text-red-500 whitespace-nowrap">
                                        <?= $item['jenis'] == 'pengeluaran' ? 'Rp '.number_format($item['jumlah'], 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Kas: Rekapitulasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Penerimaan (+)</span>
                            <span class="font-bold text-green-600">Rp <?= number_format($group['total_masuk'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Pengeluaran (-)</span>
                            <span class="font-bold text-red-500">Rp <?= number_format($group['total_keluar'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end justify-center">
                        <span class="text-[10px] text-gray-400 uppercase font-bold">Neto Saldo Kas Ini</span>
                        <span class="text-2xl font-black text-gray-900 leading-none">Rp <?= number_format($group['saldo_akhir'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Catatan Penutup -->
    <div class="mt-12 p-8 bg-gray-900 rounded-3xl text-center text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-lg font-bold mb-2">Transparansi Umat</h3>
            <p class="text-gray-400 text-sm max-w-xl mx-auto leading-relaxed">
                Data di atas merupakan laporan resmi Masjid Al Manaar Slipi. Segala bentuk pertanyaan atau klarifikasi mengenai rincian transaksi dapat diajukan kepada Bendahara Masjid pada jam kerja.
            </p>
        </div>
        <!-- Dekorasi latar belakang (Opsional) -->
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
</div>
<?= $this->endSection() ?>