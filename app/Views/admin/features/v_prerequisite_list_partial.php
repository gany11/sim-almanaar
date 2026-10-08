<?php if (empty($prerequisites)): ?>
    <tr>
        <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada prasyarat yang ditambahkan untuk fitur ini.</td>
    </tr>
<?php else: ?>
    <?php foreach ($prerequisites as $row): ?>
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
            </td>
            <td class="px-6 py-4 text-center">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                    <?= esc($row['jenis']) ?>
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <button type="button" 
                    class="btn-delete-prerequisite p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                    data-id="<?= $row['id_fitur_prasyarat'] ?>" title="Hapus Prasyarat">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>