<?php if (empty($carousel)): ?>
    <tr>
        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data carousel tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($carousel as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <img src="<?= base_url('uploads/carousel/' . $row['file']) ?>" 
                        class="w-24 aspect-video rounded-xl object-cover border border-gray-100 shadow-sm bg-gray-50"
                        alt="Carousel Image">
                    <span class="text-xs text-gray-400 font-mono"><?= $row['file'] ?></span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-xs space-y-1 text-gray-600">
                    <p><span class="font-semibold">Mulai:</span> <?= $row['started_at'] ? date('d M Y, H:i', strtotime($row['started_at'])) : '<span class="italic text-gray-400">Tanpa Batas</span>' ?></p>
                    <p><span class="font-semibold">Selesai:</span> <?= $row['ended_at'] ? date('d M Y, H:i', strtotime($row['ended_at'])) : '<span class="italic text-gray-400">Tanpa Batas</span>' ?></p>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase <?= $row['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                    <?php if (in_array(session()->get('id_peran'), [1])): ?>
                        <a href="<?= base_url('admin/carousel/edit/' . $row['id_carousel']) ?>" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        
                        <button type="button" 
                            class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                            data-id="<?= $row['id_carousel'] ?>" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>