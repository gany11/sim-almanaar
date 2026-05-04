<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="imagePreview()">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($artikel) ? 'Edit Artikel' : 'Tulis Artikel Baru' ?></h2>
            <p class="text-sm text-gray-500">Gunakan form ini untuk mengelola konten artikel publik.</p>
        </div>
        <a href="<?= base_url('admin/article') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar artikel
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= isset($artikel) ? base_url('admin/article/update/'.$artikel['id_publikasi']) : base_url('admin/article/save') ?>" method="post" enctype="multipart/form-data" class="p-8">
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
                                <p class="text-[11px] text-gray-500">Tentukan apakah artikel aktif.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="aktif" class="sr-only peer" 
                                    <?= old('status', $artikel['status'] ?? 'aktif') === 'aktif' ? 'checked' : '' ?>>
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
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Artikel</label>
                        <input type="text" name="judul" value="<?= old('judul', $artikel['judul'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['judul']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-300"
                            placeholder="Masukkan judul artikel...">
                        <?php if (isset(session('errors')['judul'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['judul'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Sumber / Penulis</label>
                        <input type="text" name="sumber_penulis" value="<?= old('sumber_penulis', $artikel['sumber_penulis'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['sumber_penulis']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-300"
                            placeholder="Nama penulis atau sumber referensi...">
                        <?php if (isset(session('errors')['sumber_penulis'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['sumber_penulis'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Isi Artikel</label>
                        <div class="<?= isset(session('errors')['deskripsi']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl overflow-hidden' : '' ?>">
                            <textarea name="deskripsi" class="editor"><?= old('deskripsi', $artikel['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <?php if (isset(session('errors')['deskripsi'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['deskripsi'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50" x-data="{ fileName: '' }">
                        <label class="block text-sm font-bold text-blue-900 mb-2">Lampiran Dokumen (Opsional)</label>
                        <p class="text-xs text-blue-600/70 mb-4">Unggah dokumen pendukung dalam format PDF (Maks. 5MB).</p>
                        
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-700 rounded-xl cursor-pointer hover:bg-blue-600 hover:text-white transition-all font-semibold text-sm shadow-sm active:scale-95">
                                <i data-lucide="file-up" class="w-4 h-4"></i>
                                Pilih File PDF
                                <input type="file" name="lampiran" class="sr-only" accept="application/pdf" 
                                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            </label>

                            <span class="text-xs text-gray-400 italic truncate max-w-[250px]">
                                <template x-if="fileName">
                                    <span class="text-blue-600 font-bold" x-text="'Terpilih: ' + fileName"></span>
                                </template>

                                <template x-if="!fileName">
                                    <span>
                                        <?php if(isset($artikel['lampiran']) && $artikel['lampiran']): ?>
                                            File saat ini: <a href="<?= base_url('uploads/lampiran/'.$artikel['lampiran']) ?>" target="_blank" class="text-blue-600 underline font-bold">Lihat PDF</a>
                                        <?php else: ?>
                                            Tidak ada file dipilih
                                        <?php endif; ?>
                                    </span>
                                </template>
                            </span>
                        </div>

                        <?php if (isset(session('errors')['lampiran'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['lampiran'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> 
                    <?= isset($artikel) ? 'Simpan Perubahan' : 'Terbitkan Artikel' ?>
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
        imageUrl: "<?= (isset($artikel) && !empty($artikel['sampul']) && $artikel['sampul'] != 'default.jpg') ? base_url('uploads/artikel/'.$artikel['sampul']) : '' ?>",
        
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
    /* Styling tambahan agar TinyMCE serasi dengan desain */
    .tox-tinymce {
        border-radius: 1.25rem !important;
        border: 1px solid #e5e7eb !important;
    }
</style>
<?= $this->endSection() ?>