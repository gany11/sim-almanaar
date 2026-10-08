<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($role) ? 'Edit Peran' : 'Tambah Peran Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Kelola nama peran dan penyesuaian kelas warna badge.</p>
        </div>
        <?php if(can_access('peran.read')): ?>
            <a href="<?= base_url('admin/roles') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
            </a>
        <?php endif; ?>
    </div>

    <!-- Alert Error Global -->
    <?php if (session()->has('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold flex items-center gap-3 mx-4 md:mx-0 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span><?= session('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <form action="<?= isset($role) ? base_url('admin/roles/update/'.$role['id_peran']) : base_url('admin/roles/save') ?>" method="post" class="p-8">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Nama Peran -->
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Peran</label>
                    <input type="text" name="nama" value="<?= old('nama', $role['nama'] ?? '') ?>" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['nama']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm"
                        placeholder="Contoh: Supervisor Keuangan">
                    <?php if (isset(session('errors')['nama'])) : ?>
                        <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['nama'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Warna Badge / Class Color -->
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">CSS Class / Warna Badge</label>
                    <input type="text" name="class_color" value="<?= old('class_color', $role['class_color'] ?? '') ?>" 
                        class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['class_color']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm font-mono"
                        placeholder="Contoh: bg-purple-100 text-purple-700">
                    <?php if (isset(session('errors')['class_color'])) : ?>
                        <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['class_color'] ?></p>
                    <?php endif; ?>
                    <p class="text-[11px] text-gray-400 mt-1">Gunakan kombinasi class Tailwind CSS untuk pewarnaan label badge.</p>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end gap-4">
                <a href="<?= base_url('admin/roles') ?>" 
                    class="px-6 py-4 border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold rounded-2xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN PERAN
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>