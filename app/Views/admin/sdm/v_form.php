<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($sdm) ? 'Edit Data SDM / Petugas' : 'Tambah Data SDM / Petugas Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi kontak, email, dan data diri sumber daya manusia atau petugas.</p>
        </div>
        <a href="<?= base_url('admin/sdm') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <form action="<?= isset($sdm) ? base_url('admin/sdm/update/'.$sdm['id_sdm']) : base_url('admin/sdm/save') ?>" method="post" class="p-8 space-y-6">
            <?= csrf_field() ?>
            
            <?php if (isset(session('errors')['general'])) : ?>
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl text-sm">
                    <?= session('errors')['general'] ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama SDM -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama" value="<?= old('nama', $sdm['nama'] ?? '') ?>" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['nama']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all bg-white" 
                        placeholder="Contoh: Ustadz H. Ahmad Fulan, Lc." required>
                    <?php if (isset(session('errors')['nama'])) : ?>
                        <p class="text-xs text-red-500 mt-1.5"><?= session('errors')['nama'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Email (Opsional)</label>
                    <input type="email" name="email" value="<?= old('email', $sdm['email'] ?? '') ?>" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['email']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all" 
                        placeholder="Contoh: sdm@almanaar.com">
                    <?php if (isset(session('errors')['email'])) : ?>
                        <p class="text-xs text-red-500 mt-1.5"><?= session('errors')['email'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Nomor Telepon / WhatsApp (Opsional)</label>
                    <input type="text" name="telepon" value="<?= old('telepon', $sdm['telepon'] ?? '') ?>" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['telepon']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all" 
                        placeholder="Contoh: 081234567890">
                    <?php if (isset(session('errors')['telepon'])) : ?>
                        <p class="text-xs text-red-500 mt-1.5"><?= session('errors')['telepon'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Alamat Lengkap (Opsional)</label>
                    <textarea name="alamat" rows="3" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['alamat']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all resize-none" 
                        placeholder="Masukkan alamat domisili atau instansi..."><?= old('alamat', $sdm['alamat'] ?? '') ?></textarea>
                    <?php if (isset(session('errors')['alamat'])) : ?>
                        <p class="text-xs text-red-500 mt-1.5"><?= session('errors')['alamat'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex justify-end gap-3">
                <a href="<?= base_url('admin/sdm') ?>" class="px-6 py-3.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> 
                    <?= isset($sdm) ? 'Simpan Perubahan' : 'Simpan Data SDM' ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>