<div id="source-report-title" 
     data-title="<?= $current_report['judul'] ?? 'Belum Ada Transaksi Pekan Ini' ?>" 
     data-id="<?= $current_report['id_laporan_mingguan'] ?? '' ?>"
     data-note='<?= $current_report['catatan'] ?? "" ?>'>
</div>

<div id="source-summary-data" class="hidden">
    <?php foreach ($summaryKeuangan as $k): ?>
        <div class="summary-item" 
             data-kategori="<?= $k['kategori'] ?>" 
             data-saldo="<?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?>">
        </div>
    <?php endforeach; ?>
</div>

<table id="source-routine-data" class="hidden">
    <tbody>
        <?php if (empty($routine)): ?>
            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Tidak ada transaksi pekan ini.</td></tr>
        <?php else: ?>
            <?php foreach ($routine as $row): ?>
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase <?= $row['class_color'] ?>">
                            <?= $row['kategori'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 leading-tight"><?= $row['keterangan'] ?></span>
                            <div class="mt-1.5 space-y-0.5">
                                <span class="text-[9px] text-blue-400 block italic">
                                    <i class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mr-1"></i>
                                    Dibuat: <?= format_indo($row['created_at'], 'full_datetime') ?> oleh <span class=""><?= $row['creator_name'] ?? 'Sistem' ?></span>
                                </span>
                                <?php if ($row['updated_at'] && $row['updated_by']): ?>
                                <span class="text-[9px] text-blue-400 block italic">
                                    <i class="inline-block w-1.5 h-1.5 bg-blue-400 rounded-full mr-1"></i>
                                    Terakhir diubah: <?= format_indo($row['updated_at'], 'full_datetime') ?> oleh <span class=""><?= $row['editor_name'] ?? 'User' ?></span>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-black <?= $row['jenis'] === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' ?>">
                            <?= $row['jenis'] === 'pemasukan' ? '+' : '-' ?> Rp <?= number_format($row['jumlah'], 0, ',', '.') ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            <?php if($row['bukti']): ?>
                                <a href="<?= base_url('uploads/keuangan/'.$row['bukti']) ?>" target="_blank" class="p-2 bg-amber-50 text-amber-600 rounded-lg shadow-sm hover:bg-amber-600 hover:text-white transition-all" title="Lihat Bukti PDF">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (in_array(session()->get('id_peran'), [4])): ?>
                                <a href="<?= base_url('admin/finance/routine/edit/' . $row['id_keuangan']) ?>" class="p-2 bg-blue-50 text-blue-600 rounded-lg shadow-sm hover:bg-blue-600 hover:text-white transition-all" title="Edit Transaksi">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <button type="button" class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg shadow-sm hover:bg-red-600 hover:text-white transition-all" data-id="<?= $row['id_keuangan'] ?>" data-judul="<?= $row['keterangan'] ?>" title="Hapus Transaksi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<table id="source-history-data" class="hidden">
    <tbody>
        <?php if (empty($history)): ?>
            <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada riwayat laporan.</td></tr>
        <?php else: ?>
            <?php foreach ($history as $h): ?>
                <?php 
                    $canEdit = (time() <= strtotime($h['ended_at'] . ' +30 days')); 
                ?>
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-700 block"><?= $h['judul'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-xs text-gray-500 line-clamp-1 italic"><?= $h['catatan'] ? strip_tags($h['catatan']) : '-' ?></div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <?php if (in_array(session()->get('id_peran'), [2,3,4])): ?>
                                <a href="<?= base_url('admin/finance/report/detail/' . $h['id_laporan_mingguan']) ?>" class="p-2 bg-gray-50 text-gray-600 rounded-lg shadow-sm hover:bg-gray-600 hover:text-white transition-all" title="Lihat Rincian">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (in_array(session()->get('id_peran'), [4])): ?>
                                <?php if($canEdit): ?>
                                    <a href="<?= base_url('admin/finance/report/edit-note/' . $h['id_laporan_mingguan']) ?>" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg shadow-sm hover:bg-emerald-600 hover:text-white transition-all" title="Edit Catatan">
                                        <i data-lucide="message-square-more" class="w-4 h-4"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>