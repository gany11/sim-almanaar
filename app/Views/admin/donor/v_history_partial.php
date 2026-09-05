<?php if (empty($donations)): ?>
    <tr>
        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada riwayat transaksi pemasukan atau donasi tercatat.</td>
    </tr>
<?php else: ?>
    <?php foreach ($donations as $row): ?>
        <?php 
            $isEditable = true;

            // 1. Jika terikat ke donasi, cek apakah donasi sudah diclose
            if (!empty($row['id_donasi']) && !empty($row['donasi_closed_at'])) {
                $isEditable = false;
            }

            // 2. Jika TIDAK terikat ke donasi, gunakan siklus Jumat s.d. Kamis malam
            if (empty($row['id_donasi']) && !empty($row['created_at'])) {
                $createdAt = strtotime($row['created_at']);
                
                // Cari hari Jumat dari tanggal data dibuat
                $jumatData = strtotime('friday this week 00:00:00', $createdAt);
                
                // Jika data dibuat di hari Jumat, Sabtu, Minggu, Senin, Selasa, Rabu, atau Kamis, 
                // batas waktu kuncinya adalah Hari Jumat di MINGGU DEPANNYA (+1 minggu / +7 hari).
                if ($createdAt >= $jumatData) {
                    $batasKunci = strtotime('+1 week', $jumatData);
                } else {
                    // (Opsional jaga-jaga) Jika strtotime menangani mingguan dengan acuan berbeda
                    $batasKunci = $jumatData; 
                }

                // Jika waktu sekarang sudah >= Jumat minggu depannya, maka kunci!
                if (time() >= $batasKunci) {
                    $isEditable = false;
                }
            }

            // Tentukan teks informasi untuk konfirmasi hapus berdasarkan klasifikasi data
            $infoHapus = 'Donasi Dicatat Saja';
            if (!empty($row['id_donasi'])) {
                $infoHapus = 'Donasi Kegiatan: ' . ($row['judul_donasi'] ?? '');
            } elseif (!empty($row['id_keuangan'])) {
                $infoHapus = 'Donasi Harian (Kas Keuangan)';
            }
        ?>
        <tr class="hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800 text-sm">
                        <?= !empty($row['tanggal']) 
                            ? format_indo($row['tanggal'], 'full') 
                            : '<span class="text-amber-500 italic">Belum diset</span>' 
                        ?>
                    </span>
                    <span class="text-[11px] text-gray-400 font-mono mt-0.5">
                        <?= !empty($row['no_kwitansi']) ? $row['no_kwitansi'] : 'No. Kwitansi belum ada' ?>
                    </span>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <?php if (!empty($row['id_donasi'])): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600">
                            <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i> Donasi Kegiatan: <?= $row['judul_donasi'] ?>
                        </span>
                        <?php if (!empty($row['keterangan'])): ?>
                            <span class="text-xs text-gray-500 italic"><?= $row['keterangan'] ?></span>
                        <?php endif; ?>

                    <?php elseif (!empty($row['id_keuangan'])): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                            <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Donasi Harian (Kas Keuangan)
                        </span>
                        <?php if (!empty($row['keterangan_keuangan'])): ?>
                            <span class="text-xs text-gray-500 italic"><?= $row['keterangan_keuangan'] ?></span>
                        <?php endif; ?>

                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Donasi Dicatat Saja
                        </span>
                        <?php if (!empty($row['keterangan'])): ?>
                            <span class="text-xs text-gray-500 italic"><?= $row['keterangan'] ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col items-start gap-1.5 text-xs">
                    <span class="text-gray-600 font-medium"><?= $row['metode_pemasukan'] ?? 'Metode belum diatur' ?></span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider whitespace-nowrap <?= $row['status_color'] ?? 'bg-gray-100 text-gray-700' ?>">
                        <?= $row['status_donasi'] ?? 'Tidak Terdefinisi' ?>
                    </span>
                </div>
            </td>

            <td class="px-6 py-4 text-right">
                <span class="font-bold text-gray-900">
                    <?= !empty($row['jumlah']) ? 'Rp ' . number_format($row['jumlah'], 0, ',', '.') : '<span class="text-gray-400 font-normal italic">Belum ditentukan</span>' ?>
                </span>
                <?php if ($row['samarkan'] === 'Y'): ?>
                    <span class="block text-[10px] text-amber-600 italic">Disamarkan (Hamba Allah)</span>
                <?php endif; ?>
            </td>

            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">

                    <!-- Tombol Lihat Bukti -->
                    <?php if (!empty($row['bukti'])): ?>
                        <a
                            href="<?= base_url('uploads/donasi/bukti/' . $row['bukti']) ?>"
                            target="_blank"
                            class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm"
                            title="Lihat Bukti"
                        >
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>


                    <!-- Detail Riwayat -->
                    <button
                        type="button"
                        class="btn-history p-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-700 hover:text-white transition-all shadow-sm"
                        title="Detail Riwayat Status"

                        data-id="<?= $row['id_pemasukan_donasi'] ?>"
                        data-nama="<?= htmlspecialchars($donor['nama'] ?? '', ENT_QUOTES) ?>"
                        data-kwitansi="<?= htmlspecialchars($row['no_kwitansi'] ?? 'No. Kwitansi belum ada', ENT_QUOTES) ?>"

                        data-history="<?= htmlspecialchars(
                            json_encode(
                                $row['histories'] ?? [],
                                JSON_UNESCAPED_UNICODE
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <i data-lucide="history" class="w-4 h-4"></i>
                    </button>


                    <?php if ($isEditable): ?>

                        <!-- Tombol Edit -->
                        <a
                            href="<?= base_url('admin/donation-incomes/edit/' . $row['id_pemasukan_donasi']) ?>"
                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                            title="Edit Data"
                        >
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>


                        <!-- Tombol Hapus -->
                        <button
                            type="button"
                            class="btn-delete-candidate p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                            title="Hapus Data"
                            data-id="<?= $row['id_pemasukan_donasi'] ?>"
                            data-info="<?= htmlspecialchars($infoHapus, ENT_QUOTES) ?>"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>

                    <?php else: ?>

                        <!-- Status terkunci -->
                        <span class="text-[11px] text-gray-400 italic bg-gray-50 px-2.5 py-1 rounded-md border border-gray-100">
                            Terkunci / Ditutup
                        </span>

                        <!-- Toggle Samarkan -->
                        <button
                            type="button"
                            class="btn-toggle-samarkan p-2 rounded-lg transition-all shadow-sm
                                <?= ($row['samarkan'] ?? 'N') === 'Y'
                                    ? 'bg-amber-100 text-amber-700 hover:bg-amber-700 hover:text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-700 hover:text-white'
                                ?>"
                            title="<?= ($row['samarkan'] ?? 'N') === 'Y'
                                ? 'Buka Samaran'
                                : 'Samarkan Donatur'
                            ?>"
                            data-id="<?= $row['id_pemasukan_donasi'] ?>"
                            data-samarkan="<?= ($row['samarkan'] ?? 'N') === 'Y' ? 'Y' : 'N' ?>"
                            data-nama="<?= htmlspecialchars($donor['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <i
                                data-lucide="<?= ($row['samarkan'] ?? 'N') === 'Y' ? 'eye-off' : 'eye' ?>"
                                class="w-4 h-4"
                            ></i>
                        </button>

                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>