<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="imagePreview()">
    <div class="mb-6 flex justify-between items-center px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($carousel) ? 'Edit Carousel' : 'Tambah Carousel Baru' ?></h2>
            <p class="text-sm text-gray-500">Kelola gambar dan rentang waktu tampil carousel beranda.</p>
        </div>
        <a href="<?= base_url('admin/carousel') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= isset($carousel) ? base_url('admin/carousel/update/'.$carousel['id_carousel']) : base_url('admin/carousel/save') ?>" method="post" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Kolom Kiri: Gambar Carousel & Status -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3">File Gambar Carousel (Wajib)</label>
                        <div class="relative group w-full h-72 rounded-3xl border-2 border-dashed border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                            <div x-show="imageUrl" class="absolute inset-0 w-full h-full">
                                <img :src="imageUrl" class="w-full h-full object-cover">
                            </div>
                            
                            <div x-show="!imageUrl" class="text-center text-gray-400">
                                <i data-lucide="image" class="w-12 h-12 mx-auto mb-2 opacity-20"></i>
                                <p class="text-xs">Belum ada gambar</p>
                            </div>

                            <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer text-white">
                                <input type="file" name="file" class="sr-only" @change="fileChosen" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <i data-lucide="camera" class="w-6 h-6 mb-1"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Ubah Gambar</span>
                            </label>
                        </div>
                        <p class="text-[10px] font-medium text-blue-600 mt-2">
                            * Format: PNG, JPG, JPEG, WEBP. Maksimal 2MB.
                        </p>
                        <?php if (isset(session('errors')['file'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['file'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-700">Status Carousel</p>
                                <p class="text-[11px] text-gray-500">Tentukan apakah carousel aktif.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="aktif" class="sr-only peer" 
                                    <?= old('status', $carousel['status'] ?? 'aktif') === 'aktif' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                        <?php if (isset(session('errors')['status'])) : ?>
                            <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['status'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Pengaturan Rentang Waktu -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50 space-y-4">
                        <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b border-blue-100 pb-3">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i> Periode Penayangan (Opsional)
                        </h3>
                        <p class="text-xs text-gray-500">
                            Kosongkan tanggal mulai atau selesai jika carousel ingin ditampilkan secara permanen tanpa batasan waktu.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Tanggal & Waktu Mulai</label>
                                <input type="datetime-local" name="started_at" value="<?= old('started_at', $carousel['started_at'] ?? '') ?>" 
                                    class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['started_at']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all bg-white">
                                <?php if (isset(session('errors')['started_at'])) : ?>
                                    <p class="text-xs text-red-500 mt-2"><?= session('errors')['started_at'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Tanggal & Waktu Selesai</label>
                                <input type="datetime-local" name="ended_at" value="<?= old('ended_at', $carousel['ended_at'] ?? '') ?>" 
                                    class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['ended_at']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all bg-white">
                                <?php if (isset(session('errors')['ended_at'])) : ?>
                                    <p class="text-xs text-red-500 mt-2"><?= session('errors')['ended_at'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> 
                    <?= isset($carousel) ? 'Simpan Perubahan' : 'Simpan Carousel' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function imagePreview() {
    return {
        imageUrl: "<?= (isset($carousel) && !empty($carousel['file'])) ? base_url('uploads/carousel/'.$carousel['file']) : '' ?>",
        
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.match('image.*')) {
                alert('Tolong pilih file gambar yang valid!');
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
<?= $this->endSection() ?>