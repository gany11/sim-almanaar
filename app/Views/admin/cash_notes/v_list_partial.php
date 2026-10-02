<?php if (empty($cashNotes)): ?>
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data catatan keuangan (keep cash) tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($cashNotes as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <!-- Tanggal -->
            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                <?= format_indo($row['tanggal'], 'full') ?>
            </td>

            <!-- Keterangan, Alasan/Peruntukan & Audit Trail -->
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800 leading-tight"><?= esc($row['keterangan']) ?></span>
                    
                    <!-- Menampilkan Alasan / Peruntukan jika ada -->
                    <?php if (!empty($row['alasan'])): ?>
                        <?php 
                            $isBatal = ($row['status'] === 'dibatalkan');
                            $labelAlasan = $isBatal ? 'Alasan Pembatalan:' : 'Peruntukan:';
                            $badgeColor = $isBatal ? 'text-rose-600 bg-rose-50 border-rose-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100';
                        ?>
                        <div class="mt-1.5 inline-flex flex-col items-start px-2.5 py-1 rounded-lg border <?= $badgeColor ?> text-xs">
                            <span class="font-semibold text-[10px] uppercase tracking-wider opacity-80"><?= $labelAlasan ?></span>
                            <span><?= esc($row['alasan']) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Audit Trail -->
                    <div class="mt-1.5 space-y-0.5">
                        <span class="text-[9px] text-blue-400 block italic">
                            <i class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mr-1"></i>
                            Dibuat: <?= format_indo($row['created_at'], 'full_datetime') ?> oleh <span><?= $row['creator_name'] ?? 'Sistem' ?></span>
                        </span>
                        <?php if (!empty($row['updated_at']) && !empty($row['updated_by'])): ?>
                            <span class="text-[9px] text-blue-400 block italic">
                                <i class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mr-1"></i>
                                Terakhir diubah: <?= format_indo($row['updated_at'], 'full_datetime') ?> oleh <span><?= $row['editor_name'] ?? 'User' ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </td>

            <!-- Nominal -->
            <td class="px-6 py-4">
                <span class="font-black text-gray-800">
                    Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                </span>
            </td>

            <!-- Status (Badge Statis) -->
            <td class="px-6 py-4 text-center">
                <?php 
                    $statusClass = '';
                    $statusLabel = '';
                    if ($row['status'] === 'tersedia') {
                        $statusClass = 'bg-blue-100 text-blue-700';
                        $statusLabel = 'Tersedia';
                    } elseif ($row['status'] === 'sudah_digunakan') {
                        $statusClass = 'bg-emerald-100 text-emerald-700';
                        $statusLabel = 'Sudah Digunakan';
                    } else {
                        $statusClass = 'bg-red-100 text-red-700';
                        $statusLabel = 'Dibatalkan';
                    }
                ?>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase <?= $statusClass ?> shadow-sm whitespace-nowrap inline-block">
                    <?= $statusLabel ?>
                </span>
            </td>

            <!-- Aksi -->
            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <?php if (in_array(session()->get('id_peran'), [4])): ?>
                        <?php if ($row['status'] === 'tersedia'): ?>
                            <button type="button" 
                                    class="btn-open-status-modal p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all shadow-sm"
                                    data-id="<?= $row['id_catatan'] ?>" 
                                    data-status="<?= $row['status'] ?>"
                                    data-keterangan="<?= htmlspecialchars($row['keterangan'], ENT_QUOTES) ?>"
                                    data-alasan="<?= htmlspecialchars($row['alasan'] ?? '', ENT_QUOTES) ?>"
                                    title="Ubah Status">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                            <a href="<?= base_url('admin/cash-notes/edit/' . $row['id_catatan']) ?>" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" 
                               title="Edit Catatan">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- <button type="button" 
                                    class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                    data-id="<?= $row['id_catatan'] ?>" 
                                    title="Hapus Catatan">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>  -->
                           
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>