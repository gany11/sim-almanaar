<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-16">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Fitur Tambahan Akun: <span class="text-blue-600"><?= esc($akun->nama) ?></span></h2>
            <p class="text-sm text-gray-500 mt-1">Username: <span class="font-mono font-medium text-gray-700">@<?= esc($akun->username) ?></span> | Peran Utama: <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= esc($akun->class_color) ?>"><?= esc($akun->nama_peran) ?></span></p>
        </div>
        <?php if (can_access('akun.read')): ?>
            <a href="<?= base_url('admin/account') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Akun
            </a>
        <?php endif; ?>
    </div>

    <?php if (can_access('akun.sinkronisasi.add.fitur')): ?>
        <!-- Informasi Singkat -->
        <div class="mx-4 mb-6 bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-center gap-3 text-blue-800 text-sm">
            <i data-lucide="info" class="w-5 h-5 shrink-0 text-blue-600"></i>
            <span>Fitur yang sudah termasuk di dalam Peran Utama (<b><?= esc($akun->nama_peran) ?></b>) otomatis disembunyikan. Daftar di bawah hanya menampilkan fitur tambahan (kustom) di luar peran utama.</span>
        </div>

        <form id="formAccountFeatures">
            <?= csrf_field() ?>
            <input type="hidden" name="id_akun" value="<?= $akun->id_akun ?>">

            <div class="space-y-6 px-4">
                <?php if (empty($groupedFeatures)): ?>
                    <div class="bg-white rounded-2xl p-8 text-center text-gray-400 border border-gray-100">
                        Belum ada data fitur yang terdaftar di sistem.
                    </div>
                <?php else: ?>
                    <?php foreach ($groupedFeatures as $kategori => $features): ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Header Kategori & Tombol Pilih Semua -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-800 uppercase tracking-wider text-xs flex items-center gap-2">
                                    <i data-lucide="folder" class="w-4 h-4 text-blue-600"></i> <?= esc($kategori) ?>
                                </h3>
                                <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-gray-600 hover:text-blue-600">
                                    <input type="checkbox" class="select-all-category rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4" data-category="<?= md5($kategori) ?>">
                                    Pilih Semua
                                </label>
                            </div>

                            <!-- Daftar Fitur dalam Kategori (Toggle Switch) -->
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($features as $f): ?>
                                    <?php $isChecked = in_array($f['id_fitur'], $assignedFeatureIds); ?>
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-all">
                                        <div class="pr-3">
                                            <div class="text-sm font-semibold text-gray-800"><?= esc($f['nama_fitur']) ?></div>
                                            <div class="text-[11px] font-mono text-blue-600"><?= esc($f['kode_fitur']) ?></div>
                                        </div>
                                        
                                        <!-- Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="id_fitur[]" value="<?= $f['id_fitur'] ?>" 
                                                class="sr-only peer feature-checkbox category-<?= md5($kategori) ?>" 
                                                <?= $isChecked ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tombol Aksi Simpan Mengambang di Bawah -->
            <div class="sticky bottom-4 z-50 mt-8 px-4 flex justify-end">
                <button type="submit" id="btnSaveAccountFeatures" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN FITUR TAMBAHAN AKUN
                </button>
            </div>
        </form>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-500">
            <i data-lucide="shield-off" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
            <p class="text-gray-500 text-center">Anda tidak memiliki akses untuk menambahkan fitur tambahan akun.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const $ = window.jQuery;

    // Logika "Pilih Semua" per Kategori
    $('.select-all-category').on('change', function() {
        const categoryClass = '.category-' + $(this).data('category');
        const isChecked = $(this).is(':checked');
        $(categoryClass).prop('checked', isChecked).trigger('change');
    });

    // Update status checkbox "Pilih Semua" jika ada perubahan manual pada item di dalamnya
    $('.feature-checkbox').on('change', function() {
        const classes = $(this).attr('class').split(' ');
        const catClass = classes.find(c => c.startsWith('category-'));
        if (catClass) {
            const hash = catClass.replace('category-', '');
            const total = $('.' + catClass).length;
            const checkedTotal = $('.' + catClass + ':checked').length;
            
            $('[data-category="' + hash + '"]').prop('checked', total === checkedTotal);
        }
    });

    // Inisialisasi status awal checkbox "Pilih Semua" saat halaman dimuat
    $('.select-all-category').each(function() {
        const categoryClass = '.category-' + $(this).data('category');
        const total = $(categoryClass).length;
        const checkedTotal = $(categoryClass + ':checked').length;
        if (total > 0 && total === checkedTotal) {
            $(this).prop('checked', true);
        }
    });

    // Submit Form Matrix Sync via AJAX
    $('#formAccountFeatures').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Menyimpan Perubahan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?= base_url('admin/account/feature/sync') ?>",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>