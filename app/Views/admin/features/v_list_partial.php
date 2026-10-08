<?php if (empty($features)): ?>
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data fitur tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($features as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4 text-center">
                <span class="font-mono font-semibold text-xs text-gray-600 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-100">
                    <?= esc($row['id_fitur']) ?>
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="font-mono font-semibold text-xs text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100">
                    <?= esc($row['kode_fitur']) ?>
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2.5 py-1 rounded-md">
                    <?= esc($row['kategori']) ?>
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-800"><?= esc($row['nama_fitur']) ?></div>
                <?php if (!empty($row['deskripsi'])): ?>
                    <div class="text-xs text-gray-400 mt-0.5 line-clamp-1"><?= esc($row['deskripsi']) ?></div>
                <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-center">
                <?php 
                    $jenisBadge = 'bg-gray-100 text-gray-700';
                    if ($row['jenis'] === 'public') {
                        $jenisBadge = 'bg-emerald-100 text-emerald-700';
                    } elseif ($row['jenis'] === 'auth') {
                        $jenisBadge = 'bg-blue-100 text-blue-700';
                    } elseif ($row['jenis'] === 'hybrid') {
                        $jenisBadge = 'bg-purple-100 text-purple-700';
                    }
                ?>
                <div class="flex flex-col items-center gap-1">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase <?= $jenisBadge ?>">
                        <?= esc($row['jenis']) ?>
                    </span>
                    <?= $row['is_maintenance'] ? '<span class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-md">Maintenance</span>' : '<span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded-md">Aktif</span>' ?>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                    <?php if (in_array(session()->get('id_peran'), [1])): ?>
                        <a href="<?= base_url('admin/features/detail/' . $row['id_fitur']) ?>" 
                        class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Detail & Prasyarat">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>

                        <button type="button" 
                            class="btn-toggle-maintenance p-2 <?= $row['is_maintenance'] ? 'bg-amber-50 text-amber-600 hover:bg-amber-600' : 'bg-green-50 text-green-600 hover:bg-green-600' ?> hover:text-white transition-all rounded-lg shadow-sm"
                            data-id="<?= $row['id_fitur'] ?>" 
                            title="<?= $row['is_maintenance'] ? 'Ubah ke Aktif' : 'Ubah ke Maintenance' ?>">
                            <i data-lucide="<?= $row['is_maintenance'] ? 'shield-alert' : 'shield-check' ?>" class="w-4 h-4"></i>
                        </button>

                        <a href="<?= base_url('admin/features/edit/' . $row['id_fitur']) ?>" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        
                        <button type="button" 
                            class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                            data-id="<?= $row['id_fitur'] ?>" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>