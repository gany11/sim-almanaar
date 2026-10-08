<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="featureForm()">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($feature) ? 'Edit Fitur' : 'Tambah Fitur Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi modul, kode, dan jenis akses fitur.</p>
        </div>
        <a href="<?= base_url('admin/features') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <!-- Alert Error Global -->
    <?php if (session()->has('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold flex items-center gap-3 mx-4 md:mx-0 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span><?= session('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <form action="<?= isset($feature) ? base_url('admin/features/update/'.$feature['id_fitur']) : base_url('admin/features/save') ?>" method="post" class="p-8">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <!-- Kategori (Select2 Dynamic) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Kategori Modul</label>
                        <div class="<?= isset(session('errors')['kategori']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                            <select name="kategori" x-model="kategori" id="select-kategori" class="w-full select2-dynamic">
                                <option value="">-- Pilih atau Ketik Kategori Baru --</option>
                                <?php 
                                    $currentKategori = old('kategori', $feature['kategori'] ?? '');
                                ?>
                                <?php if (!empty($categories)) : ?>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= esc($cat) ?>" <?= $currentKategori == $cat ? 'selected' : '' ?>>
                                            <?= esc($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (!empty($currentKategori) && !in_array($currentKategori, $categories ?? [])): ?>
                                    <option value="<?= esc($currentKategori) ?>" selected><?= esc($currentKategori) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php if (isset(session('errors')['kategori'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['kategori'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Pilih kategori yang ada atau ketik untuk membuat baru.</p>
                    </div>

                    <!-- Nama Fitur -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Fitur</label>
                        <input type="text" name="nama_fitur" x-model="namaFitur" value="<?= old('nama_fitur', $feature['nama_fitur'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['nama_fitur']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm"
                            placeholder="Contoh: Export Laporan">
                        <?php if (isset(session('errors')['nama_fitur'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['nama_fitur'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Kode Fitur (Auto Formula Excel-like) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Kode Fitur (Otomatis)</label>
                        <input type="text" name="kode_fitur" :value="generateKode" value="<?= old('kode_fitur', $feature['kode_fitur'] ?? '') ?>" readonly
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 bg-gray-100 text-gray-600 font-mono text-sm outline-none cursor-not-allowed"
                            placeholder="kategori.nama.fitur">
                        <?php if (isset(session('errors')['kode_fitur'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['kode_fitur'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Dihasilkan otomatis berdasarkan kombinasi kategori dan nama fitur.</p>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <!-- Jenis Akses -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Jenis Akses</label>
                        <?php $currentJenis = old('jenis', $feature['jenis'] ?? 'auth'); ?>
                        <select name="jenis" class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 outline-none select2-basic">
                            <option value="auth" <?= $currentJenis == 'auth' ? 'selected' : '' ?>>Auth (Perlu Login)</option>
                            <option value="public" <?= $currentJenis == 'public' ? 'selected' : '' ?>>Public (Tanpa Login)</option>
                            <option value="hybrid" <?= $currentJenis == 'hybrid' ? 'selected' : '' ?>>Hybrid (Keduanya)</option>
                        </select>
                        <?php if (isset(session('errors')['jenis'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['jenis'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Status Maintenance (Baru) -->
                    <div class="p-4 border border-gray-200 rounded-2xl flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Mode Maintenance</label>
                            <p class="text-[11px] text-gray-500">Aktifkan untuk menutup akses fitur ini sementara waktu.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_maintenance" value="1" class="sr-only peer" 
                                <?= old('is_maintenance', $feature['is_maintenance'] ?? 0) == 1 ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="4" 
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm"
                            placeholder="Keterangan singkat mengenai hak akses fitur ini..."><?= old('deskripsi', $feature['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end gap-4">
                <a href="<?= base_url('admin/features') ?>" 
                    class="px-6 py-4 border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold rounded-2xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN FITUR
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.jQuery && $.fn.select2) {
            $('.select2-basic').select2({ width: '100%' });
            
            const $selectDynamic = $('.select2-dynamic');
            $selectDynamic.select2({
                tags: true,
                placeholder: "Pilih atau ketik kategori baru...",
                allowClear: true,
                width: '100%'
            });

            // Sinkronisasi perubahan Select2 ke Alpine.js
            $selectDynamic.on('change', function() {
                const alpineEl = document.querySelector('[x-data]');
                if (alpineEl && alpineEl.__x) {
                    // Trigger pembaruan state alpine
                }
                // Alternatif dispatch event untuk Alpine
                window.dispatchEvent(new CustomEvent('kategori-changed', { detail: $(this).val() }));
            });
        }
    });

    function featureForm() {
        return {
            kategori: '<?= old('kategori', $feature['kategori'] ?? '') ?>',
            namaFitur: '<?= old('nama_fitur', $feature['nama_fitur'] ?? '') ?>',
            init() {
                // Mendengarkan perubahan dari Select2
                window.addEventListener('kategori-changed', (event) => {
                    this.kategori = event.detail;
                });
                
                // Pantau juga perubahan native select2 jika diketik manual
                $('#select-kategori').on('select2:select select2:clear', (e) => {
                    this.kategori = e.target.value;
                });
            },
            get generateKode() {
                let cat = this.kategori ? this.kategori.trim() : '';
                let name = this.namaFitur ? this.namaFitur.trim() : '';
                
                if (!cat && !name) return '';
                
                let combined = (cat ? cat + ' ' : '') + name;
                return combined.toLowerCase().replace(/\s+/g, '.');
            }
        }
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