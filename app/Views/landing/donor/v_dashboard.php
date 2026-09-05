<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-gray-50 pb-16" 
     x-data="{ 
         openModal: false, 
         selectedDonation: null,
         orderedItems: [] 
     }">

    <!-- Main Content Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Header Card -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                
                <!-- Welcome text -->
                <div class="flex items-center gap-4 min-w-0">
                    <div class="min-w-0">
                        <p class="text-xs text-emerald-600 font-semibold tracking-wider uppercase">Portal Donatur</p>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 truncate mt-0.5">
                            Assalamu'alaikum, <?= esc($donatur['nama']) ?>
                        </h1>
                        <p class="text-xs text-gray-400 mt-1">Selamat datang di halaman akun donatur Anda.</p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center shrink-0">
                    <a href="<?= base_url('donatur/logout') ?>"
                            onclick="event.preventDefault(); confirmLogout('<?= base_url('donatur/logout') ?>');"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-red-100 bg-red-50 text-red-600 text-xs sm:text-sm font-semibold hover:bg-red-100 transition shadow-sm">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Keluar</span>
                    </a>
                </div>

            </div>
        </div>


        <!-- Grid Konten Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- KOLOM KIRI: Data Donatur -->
            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    
                    <!-- Card Header Accent -->
                    <div class="bg-emerald-600 px-6 py-6 p-4 text-white">
                        <div class="flex items-center justify-between">
                            <h2 class="font-bold text-lg flex items-center gap-2">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                                Data Donatur
                            </h2>
                        </div>
                    </div>

                    <!-- Detail Data -->
                    <div class="p-6 space-y-4">

                        <!-- Nomor Registrasi -->
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                <i data-lucide="id-card" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Nomor Registrasi</p>
                                <p class="text-sm font-semibold text-gray-800 mt-0.5"><?= esc($donatur['noreg']) ?></p>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Nama Lengkap</p>
                                <p class="text-sm font-semibold text-gray-800 mt-0.5"><?= esc($donatur['nama']) ?></p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Email</p>
                                <p class="text-sm font-medium text-gray-700 mt-0.5 break-all">
                                    <?= !empty($donatur['email']) ? esc($donatur['email']) : '<span class="text-gray-400 italic">Belum diatur</span>' ?>
                                </p>
                            </div>
                        </div>

                        <!-- Telepon / WhatsApp -->
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Nomor WhatsApp</p>
                                <p class="text-sm font-medium text-gray-700 mt-0.5">
                                    <?= !empty($donatur['telepon']) ? esc($donatur['telepon']) : '<span class="text-gray-400 italic">Belum diatur</span>' ?>
                                </p>
                            </div>
                        </div>

                        <!-- Alamat Lengkap -->
                        <?php
                        $alamatParts = [];
                        if (!empty($donatur['alamat'])) $alamatParts[] = $donatur['alamat'];
                        if (!empty($donatur['rt'])) $alamatParts[] = 'RT ' . $donatur['rt'];
                        if (!empty($donatur['rw'])) $alamatParts[] = 'RW ' . $donatur['rw'];
                        if (!empty($donatur['kelurahan'])) $alamatParts[] = $donatur['kelurahan'];
                        $alamatLengkap = implode(', ', $alamatParts);
                        ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-gray-500 mt-0.5">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Alamat</p>
                                <p class="text-sm font-medium text-gray-700 mt-0.5 leading-relaxed">
                                    <?= $alamatLengkap !== '' ? esc($alamatLengkap) : '<span class="text-gray-400 italic">Alamat belum diisi</span>' ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Info Box Bantuan -->
                <div class="bg-emerald-50 border border-emerald-100 rounded-3xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shrink-0 shadow-sm text-emerald-600">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-emerald-900">Butuh Bantuan?</h4>
                            <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
                                Jika ada ketidaksesuaian data profil atau pertanyaan seputar donasi, silakan hubungi pengurus Masjid Al-Manaar.
                            </p>
                        </div>
                    </div>
                </div>

            </div>


            <!-- KOLOM KANAN: Rekomendasi Donasi Aktif & Riwayat Donasi -->
            <div class="lg:col-span-2 space-y-8">

                <!-- SECTION 1: Rekomendasi / Kampanye Donasi yang Masih Dibuka -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="sparkles" class="w-5 h-5 text-emerald-600"></i>
                            Rekomendasi Donasi Aktif
                        </h2>
                        <a href="<?= base_url('donasi') ?>" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition flex items-center gap-1">
                            Lihat Semua <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <?php if (!empty($rekomendasiDonasi)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($rekomendasiDonasi as $item): ?>
                                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-base line-clamp-1"><?= esc($item['judul']) ?></h3>
                                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-2 leading-relaxed">
                                            <?= esc($item['deskripsi']) ?>
                                        </p>
                                    </div>
                                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                                        <span class="text-xs text-gray-400 font-medium">Status: <strong class="text-emerald-600">Dibuka</strong></span>
                                        <a href="<?= base_url('donasi/detail/' . $item['id_donasi']) ?>" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition">
                                            Donasi
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State jika tidak ada rekomendasi -->
                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
                            <p class="text-sm font-medium">Belum ada rekomendasi donasi baru saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- SECTION 2: Riwayat Donasi -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                                    
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i data-lucide="history" class="w-5 h-5 text-emerald-600"></i>
                                Riwayat Donasi Anda
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">Catatan transaksi dan partisipasi kebaikan Anda.</p>
                        </div>
                    </div>

                    <!-- Wrapper dengan padding agar tabel tidak mepet border luar card -->
                    <div class="p-4 sm:p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[600px]">
                                <thead>
                                    <tr class="bg-gray-50/75 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400 font-semibold">
                                        <th class="py-3.5 px-4 sm:px-6">Tanggal</th>
                                        <th class="py-3.5 px-4 sm:px-6">Keterangan / Program</th>
                                        <th class="py-3.5 px-4 sm:px-6">Jumlah & Metode</th>
                                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    
                                    <?php if (!empty($donations)): ?>
                                        <?php foreach ($donations as $row): ?>
                                            <tr class="hover:bg-gray-50/50 transition">
                                                <!-- Tanggal -->
                                                <td class="py-4 px-4 sm:px-6 text-gray-600 font-medium whitespace-nowrap">
                                                    <?= esc($row['tanggal_formatted']) ?>
                                                </td>

                                                <!-- Keterangan / Program -->
                                                <td class="py-4 px-4 sm:px-6 text-gray-800 font-semibold">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border <?= esc($row['kategori_color']) ?>">
                                                            <?= esc($row['kategori_label']) ?>
                                                        </span>
                                                    </div>
                                                    <?= esc($row['keterangan_tampilan']) ?>
                                                    <?php if(!empty($row['no_kwitansi'])): ?>
                                                        <span class="block text-xs font-normal text-gray-400 mt-0.5">Kwitansi: <?= esc($row['no_kwitansi']) ?></span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Jumlah & Metode Pemasukan -->
                                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                                    <div class="text-emerald-600 font-bold">
                                                        Rp <?= number_format($row['jumlah'], 0, ',', '.') ?>
                                                    </div>
                                                    <div class="text-xs text-gray-400 font-medium mt-0.5">
                                                        <?= esc($row['metode_pemasukan'] ?? '-') ?>
                                                    </div>
                                                </td>

                                                <!-- Aksi (Hanya Tombol Ikon Status) -->
                                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                                    <button 
                                                        @click="selectedDonation = <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>; orderedItems = selectedDonation.histories || []; openModal = true;"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl transition shadow-sm border <?= esc($row['status_color'] ?? 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200') ?>"
                                                        title="Status: <?= esc($row['status_donasi'] ?? 'Tercatat') ?> (Klik untuk lihat riwayat)">
                                                        <i data-lucide="history" class="w-4 h-4"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="py-10 text-center text-gray-400">
                                                <div class="flex flex-col items-center justify-center space-y-2">
                                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                                                        <i data-lucide="receipt" class="w-6 h-6"></i>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-600">Belum ada riwayat donasi tercatat.</p>
                                                    <p class="text-xs text-gray-400">Mari salurkan donasi pertama Anda melalui program di atas.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>


    <!-- MODAL TIMELINE HISTORI STATUS -->
    <div x-show="openModal" 
         x-init="$watch('openModal', value => { document.body.style.overflow = value ? 'hidden' : '' })"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         x-transition.opacity>
        
        <div @click.away="openModal = false" 
             class="bg-white rounded-3xl shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
             x-transition>
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 shrink-0">
                <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-emerald-600"></i>
                    Riwayat Status Donasi
                </h3>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body (Timeline Histori Status Saja) -->
            <div class="p-6 overflow-y-auto flex-1">
                <div>
                    <h4 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-4">Linimasa Perubahan Status</h4>

                    <div class="overflow-y-auto min-h-0">
                        <template x-if="orderedItems.length === 0">
                            <div class="text-center py-8 text-gray-400 italic text-sm">
                                Belum ada riwayat status tercatat.
                            </div>
                        </template>

                        <template x-if="orderedItems.length > 0">
                            <div class="overflow-x-auto pb-4">
                                <div class="min-w-max px-4 pt-4">
                                    <div class="relative">
                                        <!-- GARIS TIMELINE -->
                                        <div class="absolute top-4 left-4 right-4 h-0.5 bg-emerald-200"></div>

                                        <!-- TIMELINE ITEM -->
                                        <div class="relative flex justify-between gap-10">
                                            <template x-for="(h, index) in orderedItems" :key="h.id_histori_status_donasi || index">
                                                <div class="relative w-44 flex-shrink-0 text-center">
                                                    <!-- BULLET -->
                                                    <div class="relative z-10 mx-auto w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-sm border-2 border-white"
                                                         x-text="index + 1"></div>

                                                    <!-- STATUS -->
                                                    <div class="mt-4">
                                                        <span class="inline-block max-w-full px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase leading-tight"
                                                              :class="h.class_color || 'bg-gray-100 text-gray-700'"
                                                              x-text="h.status_donasi || 'Tidak Terdefinisi'"></span>
                                                    </div>

                                                    <!-- TANGGAL -->
                                                    <div class="mt-3">
                                                        <p class="text-[11px] text-gray-400 font-mono leading-relaxed"
                                                           x-text="h.waktu_formatted || h.waktu || '-'"></p>
                                                    </div>

                                                    <!-- PENGURUS -->
                                                    <div class="mt-2">
                                                        <p class="text-[10px] text-gray-400 leading-relaxed">Dicatat oleh</p>
                                                        <p class="text-[11px] font-semibold text-gray-700 leading-relaxed mt-0.5"
                                                           x-text="h.nama_pengurus || '-'"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end shrink-0">
                <button @click="openModal = false" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>


<script>
function confirmLogout(logoutUrl) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Keluar dari Akun?',
            text: 'Anda akan keluar dari sesi portal donatur saat ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
                cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = logoutUrl;
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin keluar?')) {
            window.location.href = logoutUrl;
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

<?= $this->endSection() ?>