<?php if (empty($donations)): ?>
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data program donasi tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($donations as $row): ?>
        <?php $isClosed = !empty($row['closed_at']); ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800"><?= $row['judul'] ?></span>
                    <?php if (!empty($row['deskripsi'])): ?>
                        <span class="text-xs text-gray-400 line-clamp-1 mt-0.5"><?= strip_tags($row['deskripsi']) ?></span>
                    <?php endif; ?>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 w-fit">
                        <?= $row['jenis_donasi'] ?>
                    </span>
                    <span class="text-xs text-gray-500 font-mono">Akronim: <b><?= $row['akronim_kwitansi'] ?></b></span>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col text-xs gap-1">
                    <span class="text-emerald-600 font-semibold">Masuk: Rp <?= number_format($row['total_pemasukan'] ?? 0, 0, ',', '.') ?></span>
                    <span class="text-red-500 font-semibold">Keluar: Rp <?= number_format($row['total_pengeluaran'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col items-center text-center gap-1">
                    <div class="mt-1">
                        <span class="inline-block px-2.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest <?= $row['status'] == 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $row['status'] == 'aktif' ? 'Publik' : 'Privat' ?>
                        </span>
                    </div>
                    <!-- Indikator Finansial Closed / Terkunci -->
                    <?php if ($isClosed): ?>
                        <span class="text-[10px] text-amber-600 font-semibold italic">Terkunci (Closed: <?= format_indo($row['closed_at'], 'datetime') ?>)</span>
                    <?php endif; ?>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <!-- Tombol Detail selalu ada -->
                    <a href="<?= base_url('admin/donations/detail/' . $row['id_donasi']) ?>" 
                        class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Detail Program">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>
                    <a href="<?= base_url('admin/donations/edit/' . $row['id_donasi']) ?>" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Program">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>

                    <?php if (!$isClosed): ?>
                        <!-- Tombol Edit, Kunci, dan Hapus Hanya Muncul Jika Belum Ditutup (closed_at kosong) -->

                        <button type="button" 
                            class="btn-close-program p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all shadow-sm" title="Tutup / Kunci Program"
                            data-id="<?= $row['id_donasi'] ?>" 
                            data-judul="<?= $row['judul'] ?>">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </button>
                        
                        <button type="button" 
                            class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Program"
                            data-id="<?= $row['id_donasi'] ?>" 
                            data-judul="<?= $row['judul'] ?>">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    <?php else: ?>
                        <!-- Jika sudah ditutup secara finansial -->
                        <span class="text-xs text-gray-400 italic px-2 py-1 bg-gray-50 rounded-lg flex items-center gap-1">
                            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>