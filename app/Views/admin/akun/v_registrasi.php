<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Registrasi Akun Baru</h2>
        <p class="text-sm text-gray-500 mt-1">Sistem akan mengirimkan email aktivasi otomatis kepada pengguna setelah akun dibuat.</p>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="<?= base_url('admin/akun/save'); ?>" method="post" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" name="nama" value="<?= old('nama') ?>" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl border <?= isset(session('errors')['nama']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="Masukkan nama lengkap">
                            <span class="absolute right-3 top-3.5 text-gray-400">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['nama'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['nama'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                        <div class="relative">
                            <input type="text" name="username" value="<?= old('username') ?>" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl border <?= isset(session('errors')['username']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="Contoh: almanaar">
                            <span class="absolute right-3 top-3.5 text-gray-400">
                                <i data-lucide="at-sign" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['username'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['username'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="<?= old('email') ?>" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl border <?= isset(session('errors')['email']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="nama@email.com">
                            <span class="absolute right-3 top-3.5 text-gray-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['email'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['email'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                        <div class="relative">
                            <input type="number" name="telepon" value="<?= old('telepon') ?>" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl border <?= isset(session('errors')['telepon']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="0812xxxxxxxx">
                            <span class="absolute right-3 top-3.5 text-gray-400">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['telepon'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['telepon'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Peran Akun</label>
                        <div class="relative">
                            <select name="id_peran" 
                                class="w-full px-4 py-3 rounded-xl border <?= isset(session('errors')['id_peran']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all appearance-none bg-transparent">
                                <option value="">-- Pilih Peran --</option>
                                <?php foreach($peran as $p): ?>
                                    <option value="<?= $p->id_peran ?>" <?= old('id_peran') == $p->id_peran ? 'selected' : '' ?>>
                                        <?= $p->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute right-3 top-3.5 text-gray-400 pointer-events-none">
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['id_peran'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['id_peran'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg text-white">
                        <i data-lucide="mail-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">Aktivasi Email Otomatis</h4>
                        <p class="text-xs text-blue-700 leading-relaxed mt-0.5">
                            Setelah data disimpan, sistem akan membuat password sementara secara acak dan mengirimkan tautan pembuatan password baru ke alamat email yang didaftarkan.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="reset" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
                        Reset Form
                    </button>
                    <button type="submit" class="px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan & Kirim Undangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>