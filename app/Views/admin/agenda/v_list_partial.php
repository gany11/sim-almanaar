<?php if (empty($agenda)): ?>
    <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data agenda tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($agenda as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800"><?= $row['tema'] ?></span>
                    <?php if ($row['judul']): ?>
                        <span class="text-[10px] text-gray-400 italic tracking-tight line-clamp-1">"<?= $row['judul'] ?>"</span>
                    <?php endif; ?>
                </div>
            </td>

            <td class="px-6 py-4">
                <span class="px-3 py-1 <?= $row['class_color'] ?> rounded-full text-[10px] font-bold uppercase whitespace-nowrap">
                    <?= $row['nama_kategori'] ?>
                </span>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <?php if (!empty($row['nama_pengisi'])): ?>
                        <?php foreach ($row['nama_pengisi'] as $p): ?>
                            <div class="flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                <span class="text-xs text-gray-600 font-medium"><?= $p ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-xs text-gray-300 italic">Belum ada pengisi</span>
                    <?php endif; ?>
                </div>
            </td>

            <td class="px-6 py-4">
                <div class="flex flex-col text-xs text-gray-500 gap-1">
                    <span class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3 h-3"></i> 
                        <?= date('d/m/Y', strtotime($row['waktu_mulai'])) ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i> 
                        <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?>
                    </span>
                </div>
            </td>

            <td class="px-6 py-4">
                <span class="text-[11px] text-gray-500 line-clamp-2 w-32" title="<?= $row['tempat'] ?>">
                    <?= $row['tempat'] ?>
                </span>
            </td>

            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <a href="<?= base_url('admin/agenda/edit/' . $row['id_agenda']) ?>" 
                       class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                    
                    <?php if (in_array(session()->get('id_peran'), [1, 3])): ?>
                    <button type="button" 
                        class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                        data-id="<?= $row['id_agenda'] ?>" 
                        data-tema="<?= $row['tema'] ?>">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>