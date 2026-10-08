<?php if (empty($akun)): ?>
    <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data akun tidak ditemukan.</td>
    </tr>
<?php else: ?>
    <?php foreach ($akun as $row): ?>
        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
            <!-- Nama & Username -->
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800"><?= esc($row->nama) ?></span>
                    <span class="text-xs text-gray-400">@<?= esc($row->username) ?></span>
                </div>
            </td>

            <!-- Peran -->
            <td class="px-6 py-4">
                <span class="px-3 py-1 <?= esc($row->class_color) ?> rounded-full text-[10px] font-bold uppercase">
                    <?= esc($row->nama_peran) ?>
                </span>
            </td>

            <!-- Email Ter-mask -->
            <td class="px-6 py-4 text-sm text-gray-600">
                <?php 
                    $email = $row->email;
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
                        echo esc($maskedUsername . '@' . $domain);
                    } else {
                        echo esc($email);
                    }
                ?>
            </td>

            <!-- Aktivitas Terakhir (Login / Logout & IP) -->
            <td class="px-6 py-4 text-xs">
                <?php if (!empty($row->login_at)): ?>
                    <div class="flex flex-col space-y-0.5">
                        <span class="text-gray-700 font-medium">
                            <i data-lucide="log-in" class="w-3 h-3 inline text-emerald-500 mr-1"></i>
                            Login: <?= format_indo($row->login_at, 'full_datetime') ?>
                        </span>
                        <?php if (!empty($row->logout_at) && $row->logout_at > $row->login_at): ?>
                            <span class="text-gray-500 italic">
                                <i data-lucide="log-out" class="w-3 h-3 inline text-rose-400 mr-1"></i>
                                Logout: <?= format_indo($row->logout_at, 'full_datetime') ?>
                            </span>
                        <?php else: ?>
                            <span class="text-emerald-600 font-semibold">
                                <span class="inline-block w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1 animate-pulse"></span>
                                Sedang Aktif / Sesi Berjalan
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($row->ip_address)): ?>
                            <span class="text-[10px] text-gray-400 font-mono">
                                IP: <?= esc($row->ip_address) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <span class="text-gray-400 italic">Belum pernah login</span>
                <?php endif; ?>
            </td>

            <!-- Status Akun -->
            <td class="px-6 py-4 text-center">
                <?php if ($row->status === 'aktif'): ?>
                    <span class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-[10px] font-bold uppercase">Aktif</span>
                <?php else: ?>
                    <span class="px-2 py-1 rounded-md bg-red-100 text-red-700 text-[10px] font-bold uppercase">Nonaktif</span>
                <?php endif; ?>
            </td>

            <!-- Aksi -->
            <td class="px-6 py-4">
                <?php if (can_access('akun.detail') || can_access('akun.update.status')): ?>
                    <div class="flex justify-center items-center gap-2">
                        <!-- Tombol Detail & Fitur Tambahan -->
                        <?php if (can_access('akun.detail')): ?>
                            <a href="<?= base_url('admin/account/detail/' . $row->id_akun) ?>" 
                                class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" 
                                title="Detail & Fitur Tambahan">
                                <i data-lucide="shield-plus" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>

                        <!-- Tombol Nonaktifkan / Pulihkan Akun -->
                        <?php if (can_access('akun.update.status')): ?>
                            <?php if ($row->id_akun == $id_sesi): ?>
                                <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-tighter ml-1">Akun Anda</span>
                            <?php else: ?>
                                <button type="button" 
                                    class="btn-toggle-status flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all text-xs font-bold 
                                    <?= $row->status === 'aktif' ? 'text-red-500 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' ?>"
                                    data-id="<?= $row->id_akun ?>" 
                                    data-nama="<?= esc($row->nama) ?>" 
                                    data-status="<?= $row->status ?>">
                                    
                                    <?php if ($row->status === 'aktif'): ?>
                                        <i data-lucide="user-x" class="w-4 h-4"></i> Nonaktifkan
                                    <?php else: ?>
                                        <i data-lucide="user-check" class="w-4 h-4"></i> Pulihkan
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
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