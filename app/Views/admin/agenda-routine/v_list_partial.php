<?php if (empty($agenda_rutin)): ?>
    <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data agenda rutin tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php 
        // Mapping nama hari singkat
        $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu']; 
    ?>
    <?php foreach ($agenda_rutin as $row): ?>
        <?php
            // Logika Looping Hari
            $hariArr = !empty($row['looping_hari']) ? explode(',', $row['looping_hari']) : [];
            
            if (empty($hariArr)) {
                $teksHari = 'Hari belum diatur';
            } elseif (count($hariArr) === 7) {
                $teksHari = 'Setiap Hari';
            } else {
                $hariTeksArr = array_map(function($h) use ($namaHari) { return $namaHari[$h] ?? ''; }, $hariArr);
                $teksHari = implode(', ', $hariTeksArr);
            }

            // Logika Looping Minggu/Pekan
            $mingguArr = !empty($row['looping_minggu']) ? explode(',', $row['looping_minggu']) : [];
            
            if (empty($mingguArr)) {
                $teksMinggu = 'Pekan belum diatur';
            } elseif (count($mingguArr) === 5) {
                $teksMinggu = 'Setiap Pekan';
            } else {
                $teksMinggu = 'Pekan ' . implode(', ', $mingguArr);
            }
        ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex flex-col text-xs text-gray-500 gap-1.5">
                    <!-- Menampilkan Pola Hari dan Pekan -->
                    <span class="flex items-center gap-1.5 font-semibold text-blue-700">
                        <i data-lucide="repeat" class="w-3.5 h-3.5"></i> 
                        <?= $teksHari ?> (<?= $teksMinggu ?>)
                    </span>
                    <!-- Menampilkan Jam -->
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> 
                        <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= !empty($row['waktu_selesai']) ? date('H:i', strtotime($row['waktu_selesai'])) : 'Selesai' ?>
                    </span>
                </div>
            </td>
            
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800"><?= $row['tema'] ?></span>
                    <?php if ($row['judul']): ?>
                        <span class="text-[10px] text-gray-400 italic tracking-tight line-clamp-1">"<?= $row['judul'] ?>"</span>
                    <?php endif; ?>
                    
                    <!-- Indikator Status Aktif/Pasif -->
                    <?php if(isset($row['status'])): ?>
                        <div class="mt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest <?= $row['status'] == 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $row['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif' ?>
                            </span>
                        </div>
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
                <span class="text-[11px] text-gray-500 line-clamp-2 w-32" title="<?= $row['tempat'] ?>">
                    <?= $row['tempat'] ?>
                </span>
            </td>

            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <?php if (in_array(session()->get('id_peran'), [3,5])): ?>
                        <!-- Ganti URL ke admin/agenda-rutin -->
                        <a href="<?= base_url('admin/agenda-rutin/edit/' . $row['id_agenda_rutin']) ?>" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        
                        <!-- Ganti data-id menjadi id_agenda_rutin -->
                        <button type="button" 
                            class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                            data-id="<?= $row['id_agenda_rutin'] ?>" 
                            data-tema="<?= $row['tema'] ?>">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>