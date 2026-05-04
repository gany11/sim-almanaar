<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Catatan Laporan</h2>
            <p class="text-sm text-gray-500"><?= $report['judul'] ?></p>
        </div>
        <a href="<?= base_url('admin/finance/routine') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar keuangan rutin
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= base_url('admin/finance/report/update-note') ?>" method="post" class="p-8">
            <?= csrf_field() ?>
            <input type="hidden" name="id_laporan" value="<?= $report['id_laporan_mingguan'] ?>">

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-3 text-center uppercase tracking-widest italic">Catatan Penjelasan Bendahara</label>
                <textarea name="catatan" class="editor"><?= old('catatan', $report['catatan'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-50">
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> 
                    Simpan Catatan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->endSection() ?>