<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="imagePreview()">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($berita) ? 'Edit Berita' : 'Tulis Berita Baru' ?></h2>
            <p class="text-sm text-gray-500">Gunakan form ini untuk mengelola konten berita publik.</p>
        </div>
        <a href="<?= base_url('admin/news') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar berita
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= isset($berita) ? base_url('admin/news/update/'.$berita['id_publikasi']) : base_url('admin/news/save') ?>" method="post" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3">Gambar Sampul</label>
                        <div class="relative group w-full h-72 rounded-3xl border-2 border-dashed border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                            <div x-show="imageUrl" class="absolute inset-0 w-full h-full">
                                <img :src="imageUrl" class="w-full h-full object-cover">
                            </div>
                            
                            <div x-show="!imageUrl" class="text-center text-gray-400">
                                <i data-lucide="image" class="w-12 h-12 mx-auto mb-2 opacity-20"></i>
                                <p class="text-xs">Belum ada foto</p>
                            </div>

                            <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer text-white">
                                <input type="file" name="sampul" class="sr-only" @change="fileChosen" accept="image/png, image/jpeg, image/jpg">
                                <i data-lucide="camera" class="w-6 h-6 mb-1"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Ubah Foto</span>
                            </label>
                        </div>
                        <?php if (isset(session('errors')['sampul'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['sampul'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-700">Status Publikasi</p>
                                <p class="text-[11px] text-gray-500">Tentukan apakah berita aktif.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="aktif" class="sr-only peer" 
                                    <?= old('status', $berita['status'] ?? 'aktif') === 'aktif' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                        <?php if (isset(session('errors')['status'])) : ?>
                            <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['status'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Berita</label>
                        <input type="text" name="judul" value="<?= old('judul', $berita['judul'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['judul']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-300"
                            placeholder="Masukkan judul berita...">
                        <?php if (isset(session('errors')['judul'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['judul'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Isi Berita</label>
                        <div class="<?= isset(session('errors')['deskripsi']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl overflow-hidden' : '' ?>">
                            <textarea name="deskripsi" class="editor"><?= old('deskripsi', $berita['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <?php if (isset(session('errors')['deskripsi'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['deskripsi'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> 
                    <?= isset($berita) ? 'Simpan Perubahan' : 'Terbitkan Berita' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<script>
function imagePreview() {
    return {
        // Fix: Gunakan variabel PHP untuk inisialisasi awal
        imageUrl: "<?= (isset($berita) && !empty($berita['sampul']) && $berita['sampul'] != 'default.jpg') ? base_url('uploads/berita/'.$berita['sampul']) : '' ?>",
        
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validasi sederhana tipe file di sisi client
            if (!file.type.match('image.*')) {
                alert('Tolong pilih file gambar!');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => { 
                this.imageUrl = e.target.result; 
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>

<style>
    .tox-tinymce {
        border-radius: 1.25rem !important;
        border: 1px solid #e5e7eb !important;
    }
</style>
<?= $this->endSection() ?>