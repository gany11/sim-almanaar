<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10 max-w-5xl">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Profil</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6 border-b pb-4">
            <i data-lucide="user" class="w-6 h-6 text-blue-600"></i>
            <h3 class="text-lg font-bold">Informasi Pribadi</h3>
        </div>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/profile/update') ?>" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="<?= old('username', $user->username) ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                <?php if(isset(session('errors')['username'])): ?><p class="text-xs text-red-500 mt-1"><?= session('errors')['username'] ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= old('nama', $user->nama) ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                <?php if(isset(session('errors')['nama'])): ?><p class="text-xs text-red-500 mt-1"><?= session('errors')['nama'] ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="<?= old('email', $user->email) ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                <?php if(isset(session('errors')['email'])): ?><p class="text-xs text-red-500 mt-1"><?= session('errors')['email'] ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                <input type="number" name="telepon" value="<?= old('telepon', $user->telepon) ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                <?php if(isset(session('errors')['telepon'])): ?><p class="text-xs text-red-500 mt-1"><?= session('errors')['telepon'] ?></p><?php endif; ?>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">Simpan Profil</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center gap-3 mb-6 border-b pb-4">
            <i data-lucide="shield-check" class="w-6 h-6 text-red-600"></i>
            <h3 class="text-lg font-bold">Keamanan & Password</h3>
        </div>

        <?php if (session()->getFlashdata('success_pass')) : ?>
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl flex items-center gap-3 italic">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span><?= session()->getFlashdata('success_pass') ?></span>
            </div>
        <?php elseif (session()->getFlashdata('error_pass')) : ?>
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span><?= session()->getFlashdata('error_pass') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/profile/update-password') ?>" method="post" class="space-y-6">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Lama</label>
                    <input type="password" name="pass_lama" required class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-red-500 transition-all" placeholder="Masukkan password saat ini">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="pass_baru" required minlength="8" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-red-500 transition-all" placeholder="Min. 8 karakter">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="pass_conf" required class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-red-500 transition-all" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-red-600 text-white font-bold rounded-xl shadow-lg hover:bg-red-700 transition-all active:scale-95">Update Keamanan Akun</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>