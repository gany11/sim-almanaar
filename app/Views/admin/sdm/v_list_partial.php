<?php if (empty($sdm)): ?>
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data SDM atau petugas tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($sdm as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4 font-bold text-gray-800">
                <?= esc($row['nama']) ?>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
                <?= esc($row['email'] ?? '-') ?>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
                <?= esc($row['telepon'] ?? '-') ?>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                <?= esc($row['alamat'] ?? '-') ?>
            </td>
            <td class="px-6 py-4">
                <?php if (can_access('sdm.detail') || can_access('sdm.update') || can_access('sdm.delete')): ?>
                    <div class="flex justify-center gap-2">

                        <?php if (can_access('sdm.detail')): ?>
                            <!-- Detail -->
                            <a href="<?= base_url('admin/sdm/detail/' . $row['id_sdm']) ?>" 
                            class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-600 hover:text-white transition-all shadow-sm"
                            title="Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('sdm.update')): ?>
                            <!-- Edit -->
                            <a href="<?= base_url('admin/sdm/edit/' . $row['id_sdm']) ?>" 
                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                            title="Edit">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('sdm.delete')): ?>
                            <!-- Hapus -->
                            <button type="button" 
                                    class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                    data-id="<?= $row['id_sdm'] ?>" 
                                    data-nama="<?= esc($row['nama']) ?>"
                                    title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-gray-500 text-sm">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>