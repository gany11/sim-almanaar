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
                            <span class="font-bold text-gray-800 leading-tight"><?= $row['keterangan'] ?> (<?= format_indo($row['tanggal'], 'slash') ?>)</span>
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
                            <!-- Tombol Lihat Bukti: Direktori dinamis berdasarkan asal data -->
                            <?php if (!empty($row['bukti'])): ?>
                                <?php 
                                    // Jika memiliki id_pemasukan_donasi, arahkan ke folder donasi/bukti, jika tidak ke keuangan
                                    $folderBukti = !empty($row['id_pemasukan_donasi']) ? 'uploads/donasi/bukti/' : 'uploads/keuangan/';
                                ?>
                                <a href="<?= base_url($folderBukti . $row['bukti']) ?>" target="_blank" class="p-2 bg-amber-50 text-amber-600 rounded-lg shadow-sm hover:bg-amber-600 hover:text-white transition-all" title="Lihat Bukti">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Tombol Edit & Hapus: Sembunyikan jika ada id_pemasukan_donasi -->
                            <?php if (empty($row['id_pemasukan_donasi']) && in_array(session()->get('id_peran'), [4])): ?>
                                <a href="<?= base_url('admin/finance/data/edit/' . $row['id_keuangan']) ?>" class="p-2 bg-blue-50 text-blue-600 rounded-lg shadow-sm hover:bg-blue-600 hover:text-white transition-all" title="Edit Transaksi">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <button type="button" class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg shadow-sm hover:bg-red-600 hover:text-white transition-all" data-id="<?= $row['id_keuangan'] ?>" data-judul="<?= $row['keterangan'] ?>" title="Hapus Transaksi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?= base_url('admin/donation-incomes/edit/' . $row['id_pemasukan_donasi']) ?>" 
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Pemasukan Donasi">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                
                                <button type="button" 
                                    class="btn-delete-candidate p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Pemasukan Donasi"
                                    data-id="<?= $row['id_pemasukan_donasi'] ?>" 
                                    data-info="<?= htmlspecialchars($row['keterangan'] ?? 'Pemasukan Donasi', ENT_QUOTES) ?>">
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