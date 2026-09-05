<?php if (empty($donors)): ?>
    <tr>
        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data donatur tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($donors as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                        <?= strtoupper(substr($row['nama'], 0, 2)) ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800"><?= $row['nama'] ?></span>
                        <span class="text-[11px] text-gray-400">Nomor Registrasi: <?= !empty($row['noreg']) ? $row['noreg'] : '-' ?></span>
                        <span class="text-[11px] text-gray-400">Terdaftar: <?= date('d M Y', strtotime($row['created_at'])) ?></span>
                    </div>
                </div>
            </td>
            
            <td class="px-6 py-4">
                <div class="flex flex-col text-xs text-gray-600 gap-1">
                    <!-- Sensor Email -->
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> 
                        <?php 
                            $email = $row['email'] ?? '';
                            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                list($username, $domain) = explode('@', $email);
                                $length = strlen($username);
                                
                                if ($length <= 2) {
                                    $maskedUsername = substr($username, 0, 1) . '*';
                                } else {
                                    $start = substr($username, 0, 2);
                                    $starsCount = max(4, $length - 2); 
                                    $stars = str_repeat('*', $starsCount);
                                    $maskedUsername = $start . $stars;
                                }
                                echo $maskedUsername . '@' . $domain;
                            } else {
                                echo '<span class="italic text-gray-300">Tidak ada email</span>';
                            }
                        ?>
                    </span>

                    <!-- Sensor Telepon -->
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i> 
                        <?php 
                            $telepon = trim((string)($row['telepon'] ?? ''));
                            if (!empty($telepon)) {
                                $phoneLength = strlen($telepon);
                                if ($phoneLength > 6) {
                                    $visibleStart = substr($telepon, 0, 4);
                                    $visibleEnd = substr($telepon, -2);
                                    $maskedLength = max(4, $phoneLength - 6);
                                    $maskedPhone = $visibleStart . str_repeat('*', $maskedLength) . $visibleEnd;
                                    echo $maskedPhone;
                                } else {
                                    echo str_repeat('*', $phoneLength);
                                }
                            } else {
                                echo '<span class="italic text-gray-300">Tidak ada telepon</span>';
                            }
                        ?>
                    </span>
                </div>
            </td>

            <td class="px-6 py-4">
                <span class="text-xs text-gray-600 line-clamp-2 max-w-xs" title="<?= $row['alamat'] ?>">
                    <?= !empty($row['alamat']) ? $row['alamat'] : '<span class="italic text-gray-300">Alamat tidak diisi</span>' ?>
                </span>
            </td>

            <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                    <a href="<?= base_url('admin/donors/detail/' . $row['id_donatur']) ?>" 
                        class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Detail Riwayat">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>

                    <a href="<?= base_url('admin/donors/edit/' . $row['id_donatur']) ?>" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Data">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                    
                    <button type="button" 
                        class="btn-delete p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Data"
                        data-id="<?= $row['id_donatur'] ?>" 
                        data-nama="<?= $row['nama'] ?>">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>