<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0 mb-8" id="section-candidates" x-data="candidateTable()">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 pb-4 border-b border-gray-50 gap-4">
        <div>
            <h3 class="font-bold text-gray-700 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-amber-500"></i> List Donatur / Donasi Tertunda
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Kelola daftar calon donatur dan status tindak lanjutnya.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold bg-amber-50 text-amber-700 px-3 py-1.5 rounded-xl">
                Total: <?= !empty($candidateDonors) ? count($candidateDonors) : 0 ?> Orang
            </span>
            
            <?php if (empty($donation['closed_at'])): ?>
                <a href="<?= base_url('admin/donation-donors/create/' . $donation['id_donasi']) ?>" 
                    class="flex items-center gap-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white px-4 py-2 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> 
                    <span>Tambah List Donatur</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringkasan Count by Status & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 bg-gray-50/70 p-4 rounded-2xl border border-gray-100">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-1">Status:</span>
            
            <button @click="selectedFilter = 'all'" 
                type="button"
                :class="selectedFilter === 'all' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-600/30 font-extrabold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-bold'"
                class="px-3.5 py-1.5  p-3 rounded-xl text-xs transition-all">
                Semua (<?= !empty($candidateDonors) ? count($candidateDonors) : 0 ?>)
            </button>

            <?php if (!empty($statusCounts)): ?>
                <?php foreach ($statusCounts as $stName => $stCount): ?>
                    <button @click="selectedFilter = '<?= $stName ?>'" 
                        type="button"
                        :class="selectedFilter === '<?= $stName ?>' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-600/30 font-extrabold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-bold'"
                        class="px-3.5 py-1.5 p-3 rounded-xl text-xs transition-all">
                        <?= $stName ?> (<?= $stCount ?>)
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="w-full md:w-64">
            <input type="text" x-model="searchQuery" placeholder="Cari nama / noreg..." 
                class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Donatur & Kontak</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase text-center">No. Kwitansi</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase">Status Terkini & Riwayat</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-50">
                <?php if (empty($candidateDonors)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Tidak ada data calon donatur yang tertunda.</td></tr>
                <?php else: ?>
                    <?php foreach ($candidateDonors as $cand): ?>
                        <tr 
                            x-show="(selectedFilter === 'all' || '<?= $cand['status_donasi'] ?? 'Menunggu' ?>' === selectedFilter) && 
                                    ('<?= strtolower($cand['nama_donatur'] . ' ' . $cand['noreg_donatur']) ?>'.includes(searchQuery.toLowerCase()))"
                        >
                            <!-- Kolom Donatur & Kontak -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 font-mono font-bold">[<?= $cand['noreg_donatur'] ?? '-' ?>]</span>
                                    <span class="font-bold text-xs text-gray-800"><?= $cand['nama_donatur'] ?></span>
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1 flex flex-col gap-1">
                                    <?php if (!empty($cand['telepon_donatur'])): ?>
                                        <div class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3 h-3 shrink-0"></i> <span><?= $cand['telepon_donatur'] ?></span></div>
                                    <?php endif; ?>
                                    <?php if (!empty(trim($cand['alamat_donatur'] ?? ''))): ?>
                                        <div class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3 h-3 shrink-0"></i> <span><?= trim($cand['alamat_donatur']) ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Kolom Kwitansi -->
                            <td class="px-6 py-4 text-xs font-mono font-bold text-gray-600 text-center">
                                <?= $cand['no_kwitansi'] ?? '-' ?>
                            </td>

                            <!-- Kolom Status & Riwayat -->
                            <td class="px-6 py-4 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $cand['status_color'] ?? 'bg-gray-100 text-gray-700' ?>">
                                        <?= $cand['status_donasi'] ?? 'Menunggu' ?>
                                    </span>
                                    
                                    <?php 
                                        $formattedHistories = [];
                                        foreach ($cand['histories'] as $h) {
                                           $formattedHistories[] = [
                                                'id_histori_status_donasi' => $h['id_histori_status_donasi'],
                                                'status_donasi'           => $h['status_donasi'],
                                                'class_color'             => $h['class_color'] ?? 'bg-gray-100 text-gray-700',
                                                'waktu_formatted'         => format_indo($h['waktu'], 'full_datetime'),
                                                'nama_pengurus'           => $h['nama_pengurus'] ?? 'Administrator'
                                            ];
                                        }
                                    ?>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-history-modal', {
                                            namaDonatur: <?= htmlspecialchars(
                                                json_encode($cand['nama_donatur'] ?? '-'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>,

                                            kwitansi: <?= htmlspecialchars(
                                                json_encode($cand['no_kwitansi'] ?? '-'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>,

                                            histories: <?= htmlspecialchars(
                                                json_encode($formattedHistories),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        })"
                                        class="p-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-700 hover:text-white transition-all shadow-sm"
                                        title="Lihat Riwayat Status"
                                    >
                                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <?php 
                                    $lastH = $cand['histories'][0] ?? null;
                                    $waktuFormatted = $lastH ? format_indo($lastH['waktu'], 'full_datetime') : '-';
                                    $pengurusText = $lastH['nama_pengurus'] ?? 'Administrator';
                                ?>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Oleh <span class="font-semibold text-gray-700"><?= $pengurusText ?></span> pada <?= $waktuFormatted ?>
                                </div>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="px-6 py-4 text-xs text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <?php if (empty($donation['closed_at'])): ?>
                                        <?php if (!in_array((int)($cand['id_status_donasi'] ?? 1), [4, 5, 6])): ?>
                                            <button
                                                type="button"
                                                @click="$dispatch('open-update-status', {
                                                    id: <?= (int) $cand['id_pemasukan_donasi'] ?>,
                                                    name: <?= htmlspecialchars(
                                                        json_encode($cand['nama_donatur'] ?? '-'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                })"
                                                class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all shadow-sm"
                                                title="Update Status"
                                            >
                                                <i data-lucide="git-pull-request" class="w-4 h-4"></i>
                                            </button>
                                            <a href="<?= base_url('admin/donation-donors/edit/' . $cand['id_pemasukan_donasi']) ?>" 
                                                class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Data Donatur">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" 
                                            class="btn-delete-candidate p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Data"
                                            data-id="<?= $cand['id_pemasukan_donasi'] ?>" 
                                            data-nama="<?= htmlspecialchars($cand['nama_donatur']) ?>">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="italic text-gray-400 text-[11px]">Terkunci</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 1rem !important;
        border: 1px solid #e5e7eb !important;
        height: 52px !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 12px !important;
        background-color: #f9fafb !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px !important;
    }
</style>