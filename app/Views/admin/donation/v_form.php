<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($donation) ? 'Edit Program Donasi' : 'Buat Program Donasi Baru' ?></h2>
            <p class="text-sm text-gray-500">Kelola informasi detail kegiatan, jenis donasi, proposal, dan laporan.</p>
        </div>
        <a href="<?= base_url('admin/donations') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <form action="<?= isset($donation) ? base_url('admin/donations/update/'.$donation['id_donasi']) : base_url('admin/donations/save') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Informasi Utama -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="info" class="w-5 h-5 text-blue-500"></i> Informasi Program
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Program (Wajib)</label>
                            <input type="text" name="judul" value="<?= old('judul', $donation['judul'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['judul']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Contoh: Pembangunan Menara Masjid & Renovasi Tempat Wudhu">
                            <?php if (isset(session('errors')['judul'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['judul'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Jenis Donasi (Wajib)</label>
                            <select name="jenis_donasi" class="w-full px-5 py-3 rounded-2xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <?php foreach(['Zakat', 'Infak', 'Sedekah', 'Wakaf', 'Donasi Umum'] as $j): ?>
                                    <option value="<?= $j ?>" <?= (old('jenis_donasi', $donation['jenis_donasi'] ?? 'Donasi Umum') == $j) ? 'selected' : '' ?>><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Akronim Kwitansi (Wajib, Unik)</label>
                            <input type="text" name="akronim_kwitansi" value="<?= old('akronim_kwitansi', $donation['akronim_kwitansi'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['akronim_kwitansi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Contoh: MMNR">
                            <?php if (isset(session('errors')['akronim_kwitansi'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['akronim_kwitansi'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">
                            Deskripsi Lengkap
                        </label>

                        <textarea name="deskripsi"
                            class="editor"
                        ><?= old('deskripsi', $donation['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Dokumen & Status -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="file-text" class="w-5 h-5 text-indigo-500"></i> Dokumen & Status
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">File Proposal (Opsional - Format PDF)</label>
                        <input type="file" name="proposal" accept="application/pdf" class="w-full px-4 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <?php if (!empty($donation['proposal'] ?? '')): ?>
                            <p class="text-[11px] text-gray-400 mt-1">File saat ini: <a href="<?= base_url('uploads/donasi/proposal/' . $donation['proposal']) ?>" target="_blank" class="text-blue-600 underline">Lihat Proposal</a></p>
                        <?php endif; ?>
                        <?php if (isset(session('errors')['proposal'])) : ?>
                            <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['proposal'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">File Laporan (Opsional - Format PDF)</label>
                        <input type="file" name="laporan" accept="application/pdf" class="w-full px-4 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <?php if (!empty($donation['laporan'] ?? '')): ?>
                            <p class="text-[11px] text-gray-400 mt-1">File saat ini: <a href="<?= base_url('uploads/donasi/laporan/' . $donation['laporan']) ?>" target="_blank" class="text-blue-600 underline">Lihat Laporan</a></p>
                        <?php endif; ?>
                        <?php if (isset(session('errors')['laporan'])) : ?>
                            <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['laporan'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Status Toggle -->
                    <div class="flex items-center justify-between p-2">
                        <div>
                            <p class="text-sm font-bold text-gray-700">Status Publikasi</p>
                            <p class="text-[11px] text-gray-500">Aktif atau pasif.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="aktif" class="sr-only peer" 
                                <?= old('status', $donation['status'] ?? 'aktif') === 'aktif' ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>
                    <?php if (isset(session('errors')['status'])) : ?>
                        <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['status'] ?></p>
                    <?php endif; ?>

                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-3 active:scale-95">
                        <i data-lucide="save" class="w-5 h-5"></i> Simpan Program Donasi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>