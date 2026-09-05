<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0" id="section-expenses">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-50">
        <h3 class="font-bold text-gray-700 flex items-center gap-2">
            <i data-lucide="trending-down" class="w-5 h-5 text-red-500"></i> Riwayat Pengeluaran Donasi
        </h3>
        
        <?php if (empty($donation['closed_at'])): ?>
            <a href="<?= base_url('admin/donation-expenses/create/' . $donation['id_donasi']) ?>" 
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold text-xs shadow-md shadow-red-100 transition-all active:scale-95 w-fit">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> 
                <span>Tambah Pengeluaran</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Keterangan</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Qty & Harga Satuan</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase text-right">Sub Total (Rp)</th>
                    <?php if (empty($donation['closed_at'])): ?>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-50">
                <?php if (empty($expenses)): ?>
                    <tr><td colspan="<?= empty($donation['closed_at']) ? 5 : 4 ?>" class="px-6 py-8 text-center text-gray-400 italic">Belum ada pengeluaran tercatat untuk program ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($expenses as $exp): ?>
                        <tr>
                            <td class="px-6 py-4 text-xs font-bold"><?= format_indo($exp['tanggal'], 'full') ?></td>
                            <td class="px-6 py-4 text-xs"><?= $exp['keterangan'] ?></td>
                            <td class="px-6 py-4 text-xs text-gray-500"><?= $exp['jumlah'] . ' ' . $exp['satuan'] ?> @ Rp <?= number_format($exp['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 text-xs font-bold text-right text-red-600">Rp <?= number_format($exp['sub_total'] ?? 0, 0, ',', '.') ?></td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <?php if (!empty($exp['bukti'])): ?>
                                        <a href="<?= base_url('uploads/donasi/bukti/' . $exp['bukti']) ?>" target="_blank" 
                                        class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Lihat Bukti Nota">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (empty($donation['closed_at'])): ?>
                                        <a href="<?= base_url('admin/donation-expenses/edit/' . $exp['id_pengeluaran_donasi']) ?>" 
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Pengeluaran">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        
                                        <button type="button" 
                                            class="btn-delete-expense p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Pengeluaran"
                                            data-id="<?= $exp['id_pengeluaran_donasi'] ?>" 
                                            data-keterangan="<?= htmlspecialchars($exp['keterangan']) ?>">
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
    </div>
</div>