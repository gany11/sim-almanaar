<?php if (empty($artikel)): ?>
    <tr>
        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data artikel tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($artikel as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <img src="<?= base_url('uploads/artikel/' . $row['sampul']) ?>" 
                        class="w-16 aspect-video rounded-lg object-cover border border-gray-100 shadow-sm bg-gray-50"
                        alt="<?= $row['judul'] ?>">

                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 line-clamp-1 leading-tight"><?= $row['judul'] ?></span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter">ID: #<?= $row['id_publikasi'] ?></span>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="text-xs text-gray-500"><?= format_indo($row['created_at'], 'full') ?><br><?= format_hijriah($row['created_at']) ?></span>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase <?= $row['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <a href="<?= base_url('admin/article/edit/' . $row['id_publikasi']) ?>" 
                       class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                    
                    <?php if (in_array(session()->get('id_peran'), [1, 3])): ?>
                    <button type="button" 
                        class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                        data-id="<?= $row['id_publikasi'] ?>" 
                        data-judul="<?= $row['judul'] ?>">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>