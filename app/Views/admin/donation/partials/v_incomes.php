<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0" id="section-incomes">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-50">
        <h3 class="font-bold text-gray-700 flex items-center gap-2 m-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-emerald-500"></i> Riwayat Pemasukan Donasi
        </h3>

        <?php if (empty($donation['closed_at'])): ?>
            <a href="<?= base_url('admin/donation-incomes/create/' . $donation['id_donasi']) ?>" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pemasukan
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Tanggal & Kwitansi</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Donatur</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Metode & Status</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase text-right">Jumlah (Rp)</th>
                    <?php if (empty($donation['closed_at'])): ?>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y p-3 divide-gray-50">
                <?php if (empty($incomes)): ?>
                    <tr>
                        <td colspan="<?= empty($donation['closed_at']) ? '5' : '4' ?>" class="px-6 py-8 text-center text-gray-400 italic">Belum ada pemasukan tercatat untuk program ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($incomes as $inc): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <span class="block font-bold text-xs"><?= format_indo($inc['tanggal'], 'full') ?></span>
                                <span class="font-mono text-[11px] text-gray-400"><?= $inc['no_kwitansi'] ?? '-' ?></span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium">
                                <div class="flex flex-col gap-1">

                                    <span class="text-gray-700 font-semibold">
                                        <?= $inc['nama_donatur'] ?>
                                    </span>

                                    <?php if (($inc['samarkan'] ?? 'N') === 'Y'): ?>
                                        <span class="text-[10px] text-amber-600 italic">
                                            Hamba Allah (Disamarkan)
                                        </span>
                                    <?php endif; ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="block text-gray-600"><?= $inc['metode_pemasukan'] ?></span>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase <?= $inc['status_color'] ?>"><?= $inc['status_donasi'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-right text-emerald-600">Rp <?= number_format($inc['jumlah'] ?? 0, 0, ',', '.') ?></td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Tombol Lihat Bukti (Muncul jika kolom bukti / foto / kwitansi terisi, sesuaikan nama kolom DB jika berbeda misal: $inc['bukti']) -->
                                    <?php if (!empty($inc['bukti'])): ?>
                                        <a href="<?= base_url('uploads/donasi/bukti/' . $inc['bukti']) ?>" target="_blank" 
                                        class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Lihat Bukti Pemasukan">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php
                                        $formattedHistories = [];

                                        foreach ($inc['histories'] ?? [] as $h) {
                                            $formattedHistories[] = [
                                                'id_histori_status_donasi' => $h['id_histori_status_donasi'] ?? null,
                                                'status_donasi'             => $h['status_donasi'] ?? 'Menunggu',
                                                'class_color'               => $h['class_color'] ?? 'bg-gray-100 text-gray-700',
                                                'waktu_formatted'           => !empty($h['waktu'])
                                                    ? format_indo($h['waktu'], 'full_datetime')
                                                    : '-',
                                                'nama_pengurus'             => $h['nama_pengurus'] ?? 'Administrator',
                                            ];
                                        }
                                    ?>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-history-modal', {
                                            namaDonatur: <?= htmlspecialchars(
                                                json_encode($inc['nama_donatur'] ?? '-'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>,

                                            kwitansi: <?= htmlspecialchars(
                                                json_encode($inc['no_kwitansi'] ?? '-'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>,

                                            histories: <?= htmlspecialchars(
                                                json_encode($formattedHistories),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        })"
                                        class="p-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-700 hover:text-white transition-all shadow-sm"
                                        title="Riwayat Status"
                                    >
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <?php if (empty($donation['closed_at'])): ?>

                                        <!-- Tombol Edit -->
                                        <a href="<?= base_url('admin/donation-incomes/edit/' . $inc['id_pemasukan_donasi']) ?>" 
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Data">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        
                                        <!-- Tombol Hapus (Terhubung ke script AJAX yang sudah ada) -->
                                        <button type="button" 
                                            class="btn-delete-income p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Data"
                                            data-id="<?= $inc['id_pemasukan_donasi'] ?>" 
                                            data-nama="<?= $inc['nama_donatur'] ?>">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    <?php else: ?>

                                        <!-- =====================================================
                                            DATA TERKUNCI
                                            ===================================================== -->

                                        <button
                                            type="button"
                                            class="btn-toggle-samarkan p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all shadow-sm"

                                            title="<?= ($inc['samarkan'] ?? 'N') === 'Y'
                                                ? 'Tampilkan Nama Donatur'
                                                : 'Samarkan Nama Donatur'
                                            ?>"

                                            data-id="<?= $inc['id_pemasukan_donasi'] ?>"

                                            data-samarkan="<?= $inc['samarkan'] ?? 'N' ?>"

                                            data-nama="<?= htmlspecialchars(
                                                $inc['nama_donatur'] ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            <i
                                                data-lucide="<?= ($inc['samarkan'] ?? 'N') === 'Y'
                                                    ? 'eye'
                                                    : 'eye-off'
                                                ?>"
                                                class="w-4 h-4"
                                            ></i>
                                        </button>

                                        <!-- Status Terkunci -->
                                        <div class="text-center">
                                            <span
                                                class="text-[11px] text-gray-400 italic bg-gray-50 px-2.5 py-1 rounded-md border border-gray-100 inline-block"
                                            >
                                                Terkunci / Ditutup
                                            </span>
                                        </div>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>