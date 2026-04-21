<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Edit Berita</h2>
        <a href="<?= base_url('admin/berita') ?>" class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="<?= base_url('admin/berita/update/' . $berita['id_publikasi']); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Berita</label>
                <input type="text" name="judul" value="<?= old('judul', $berita['judul']) ?>" 
                    class="w-full px-4 py-3 rounded-xl border <?= isset(session('errors')['judul']) ? 'border-red-500' : 'border-gray-300' ?> outline-none focus:ring-2 focus:ring-blue-500">
                <?php if (isset(session('errors')['judul'])) : ?>
                    <p class="text-[11px] text-red-500 mt-1"><?= session('errors')['judul'] ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ganti Sampul (Opsional)</label>
                    <input type="file" name="sampul" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampul Saat Ini</label>
                    <img src="<?= base_url('uploads/berita/' . $berita['sampul']) ?>" class="h-20 rounded-lg shadow-sm border">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konten Berita</label>
                <textarea name="deskripsi" rows="8" class="w-full px-4 py-3 rounded-xl border <?= isset(session('errors')['deskripsi']) ? 'border-red-500' : 'border-gray-300' ?> outline-none focus:ring-2 focus:ring-blue-500"><?= old('deskripsi', $berita['deskripsi']) ?></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="submit" class="px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>