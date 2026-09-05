<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($donor) ? 'Edit Data Donatur' : 'Tambah Donatur Baru' ?></h2>
            <p class="text-sm text-gray-500">Kelola informasi profil dan kontak donatur lembaga.</p>
        </div>
        <a href="<?= base_url('admin/donors') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <form action="<?= isset($donor) ? base_url('admin/donors/update/'.$donor['id_donatur']) : base_url('admin/donors/save') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Informasi Utama Donatur -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="user" class="w-5 h-5 text-blue-500"></i> Informasi Profil Donatur
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Lengkap (Wajib)</label>
                            <input type="text" name="nama" value="<?= old('nama', $donor['nama'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['nama']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Contoh: H. Fulan / PT Berkah Bersama">
                            <?php if (isset(session('errors')['nama'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['nama'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email (Opsional)</label>
                            <input type="email" name="email" value="<?= old('email', $donor['email'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['email']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="contoh@email.com">
                            <?php if (isset(session('errors')['email'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Nomor Telepon / WhatsApp (Opsional)</label>
                            <input type="text" name="telepon" value="<?= old('telepon', $donor['telepon'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['telepon']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="081234567890">
                            <?php if (isset(session('errors')['telepon'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['telepon'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Alamat Lengkap (Opsional)</label>
                        <input type="text" name="alamat" value="<?= old('alamat', $donor['alamat'] ?? '') ?>" 
                            class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['alamat']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="Masukkan alamat domisili donatur...">
                        <?php if (isset(session('errors')['alamat'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['alamat'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Field Tambahan: RT, RW, Kelurahan dengan Select2 Dynamic -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2 border-t border-gray-100">
                        <!-- RT -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">RT</label>
                            <select name="rt" class="select2-dynamic w-full" data-placeholder="Pilih atau ketik RT...">
                                <option value=""></option>
                                <?php 
                                    $oldRt = old('rt', $donor['rt'] ?? '');
                                    if ($oldRt && !in_array($oldRt, $rts ?? [])) {
                                        $rts[] = $oldRt;
                                    }
                                ?>
                                <?php foreach ($rts ?? [] as $rt): ?>
                                    <option value="<?= esc($rt) ?>" <?= ($oldRt === $rt) ? 'selected' : '' ?>><?= esc($rt) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset(session('errors')['rt'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['rt'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- RW -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">RW</label>
                            <select name="rw" class="select2-dynamic w-full" data-placeholder="Pilih atau ketik RW...">
                                <option value=""></option>
                                <?php 
                                    $oldRw = old('rw', $donor['rw'] ?? '');
                                    if ($oldRw && !in_array($oldRw, $rws ?? [])) {
                                        $rws[] = $oldRw;
                                    }
                                ?>
                                <?php foreach ($rws ?? [] as $rw): ?>
                                    <option value="<?= esc($rw) ?>" <?= ($oldRw === $rw) ? 'selected' : '' ?>><?= esc($rw) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset(session('errors')['rw'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['rw'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Kelurahan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Kelurahan</label>
                            <select name="kelurahan" class="select2-dynamic w-full" data-placeholder="Pilih atau ketik Kelurahan...">
                                <option value=""></option>
                                <?php 
                                    $oldKel = old('kelurahan', $donor['kelurahan'] ?? '');
                                    if ($oldKel && !in_array($oldKel, $kelurahans ?? [])) {
                                        $kelurahans[] = $oldKel;
                                    }
                                ?>
                                <?php foreach ($kelurahans ?? [] as $kel): ?>
                                    <option value="<?= esc($kel) ?>" <?= ($oldKel === $kel) ? 'selected' : '' ?>><?= esc($kel) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset(session('errors')['kelurahan'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['kelurahan'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Informasi Sistem & Tombol Simpan -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i> Informasi & Aksi
                    </h3>

                    <div class="p-4 bg-blue-50/80 border border-blue-100 rounded-2xl">
                        <p class="text-xs text-blue-600 leading-relaxed">
                            Pastikan data kontak (email atau telepon) sudah benar agar sistem dapat mengirimkan akses tautan riwayat donasi jika diperlukan.
                        </p>
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-3 active:scale-95">
                        <i data-lucide="save" class="w-5 h-5"></i> Simpan Data Donatur
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.jQuery && $.fn.select2) {
            $('.select2-basic').select2({ width: '100%' });
            $('.select2-dynamic').select2({
                tags: true,
                placeholder: "Pilih atau ketik baru...",
                allowClear: true,
                width: '100%'
            });
        }
    });

    function donorForm() {
        return {}
    }
</script>

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
<?= $this->endSection() ?>