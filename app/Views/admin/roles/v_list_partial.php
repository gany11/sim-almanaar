<?php if (empty($roles)): ?>
    <tr>
        <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Data peran tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($roles as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-800"><?= esc($row['nama']) ?></div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-md text-xs font-bold <?= esc($row['class_color']) ?>">
                    <?= esc($row['nama']) ?> (<?= esc($row['class_color']) ?>)
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <?php if (can_access('peran.detail') || can_access('peran.update') || can_access('peran.delete')): ?>
                    <div class="flex justify-center gap-2">
                        <?php if (can_access('peran.detail')): ?>
                            <!-- Tombol Detail / Matrix Akses -->
                            <a href="<?= base_url('admin/roles/detail/' . $row['id_peran']) ?>" 
                            class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Atur Akses Fitur">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('peran.update')): ?>
                            <!-- Tombol Edit -->
                            <a href="<?= base_url('admin/roles/edit/' . $row['id_peran']) ?>" 
                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('peran.delete')): ?>
                            <!-- Tombol Hapus -->
                            <button type="button" 
                                class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                data-id="<?= $row['id_peran'] ?>" data-nama="<?= esc($row['nama']) ?>" title="Hapus">
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