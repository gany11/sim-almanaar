<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tulis Berita Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Buat konten berita terbaru untuk dipublikasikan ke website.</p>
        </div>
        <a href="<?= base_url('admin/berita') ?>" class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="<?= base_url('admin/berita/save'); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 gap-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Berita</label>
                        <div class="relative">
                            <input type="text" name="judul" value="<?= old('judul') ?>" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl border <?= isset(session('errors')['judul']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" 
                                placeholder="Masukkan judul berita yang menarik">
                            <span class="absolute right-3 top-3.5 text-gray-400">
                                <i data-lucide="heading" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <?php if (isset(session('errors')['judul'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['judul'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Sampul</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="image-plus" class="w-10 h-10 text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">Klik untuk upload atau drag and drop</p>
                                    <p class="text-xs text-gray-400">PNG, JPG atau JPEG (Max. 2MB)</p>
                                </div>
                                <input type="file" name="sampul" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        <?php if (isset(session('errors')['sampul'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['sampul'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konten Berita</label>
                        <textarea name="deskripsi" id="editor" rows="10" 
                            class="w-full px-4 py-3 rounded-xl border <?= isset(session('errors')['deskripsi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="Tuliskan isi berita di sini..."><?= old('deskripsi') ?></textarea>
                        <?php if (isset(session('errors')['deskripsi'])) : ?>
                            <p class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i> <?= session('errors')['deskripsi'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl flex items-start gap-3">
                    <div class="p-2 bg-amber-500 rounded-lg text-white">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900">Privasi Publikasi</h4>
                        <p class="text-xs text-amber-700 leading-relaxed mt-0.5">
                            Berita yang disimpan akan langsung berstatus <b>Published</b> dan dapat dilihat oleh publik di halaman depan website.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="reset" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
                        Batalkan
                    </button>
                    <button type="submit" class="px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Terbitkan Berita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>