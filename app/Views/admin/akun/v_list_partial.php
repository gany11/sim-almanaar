<?php if (empty($akun)): ?>
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data akun tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($akun as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800"><?= $row->nama ?></span>
                    <span class="text-xs text-gray-400">@<?= $row->username ?></span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase">
                    <?= $row->nama_peran ?>
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
                <?= $row->email ?>
            </td>
            <td class="px-6 py-4 text-center">
                <?php if ($row->status === 'aktif'): ?>
                    <span class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-[10px] font-bold uppercase">Aktif</span>
                <?php else: ?>
                    <span class="px-2 py-1 rounded-md bg-red-100 text-red-700 text-[10px] font-bold uppercase">Nonaktif</span>
                <?php endif; ?>
            </td>
            <td class="px-6 py-4">
                <div class="flex justify-center">
                    <?php if ($row->id_akun == $id_sesi): ?>
                        <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-tighter">Akun Anda</span>
                    <?php else: ?>
                        <button type="button" 
                            class="btn-toggle-status flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all text-xs font-bold 
                            <?= $row->status === 'aktif' ? 'text-red-500 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' ?>"
                            data-id="<?= $row->id_akun ?>" 
                            data-nama="<?= $row->nama ?>" 
                            data-status="<?= $row->status ?>">
                            
                            <?php if ($row->status === 'aktif'): ?>
                                <i data-lucide="user-x" class="w-4 h-4"></i> Nonaktifkan
                            <?php else: ?>
                                <i data-lucide="user-check" class="w-4 h-4"></i> Pulihkan
                            <?php endif; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>