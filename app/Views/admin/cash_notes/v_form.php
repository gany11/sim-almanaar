<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="cashNoteForm()">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($cashNote) ? 'Edit Catatan Keuangan' : 'Catat Keep Cash Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Formulir draf kas sementara sebelum dimasukkan ke buku kas utama.</p>
        </div>
        <?php if(can_access('catatan.keuangan.read')): ?>
            <a href="<?= base_url('admin/cash-notes') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
            </a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <form action="<?= isset($cashNote) ? base_url('admin/cash-notes/update/'.$cashNote['id_catatan']) : base_url('admin/cash-notes/save') ?>" method="post" class="p-8">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                <!-- Kolom Kiri: Tanggal & Status -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Tanggal Catatan</label>
                        <input type="date" name="tanggal" max="<?= date('Y-m-d') ?>" value="<?= old('tanggal', $cashNote['tanggal'] ?? date('Y-m-d')) ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['tanggal']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer">
                        <?php if (isset(session('errors')['tanggal'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['tanggal'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Status Keep Cash</label>
                        <select name="status" class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['status']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer">
                            <option value="tersedia" <?= old('status', $cashNote['status'] ?? 'tersedia') === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="sudah_digunakan" <?= old('status', $cashNote['status'] ?? '') === 'sudah_digunakan' ? 'selected' : '' ?>>Sudah Digunakan</option>
                            <option value="dibatalkan" <?= old('status', $cashNote['status'] ?? '') === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                        <?php if (isset(session('errors')['status'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['status'] ?></p>
                        <?php endif; ?>
                    </div> -->
                </div>

                <!-- Kolom Kanan: Nominal & Keterangan -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Nominal Dana</label>
                        <div class="flex items-center bg-white border <?= isset(session('errors')['nominal']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                            <div class="bg-gray-50 px-5 py-3.5 border-r border-gray-100">
                                <span class="text-gray-400 font-bold">Rp</span>
                            </div>
                            <input type="text" x-model="displayNominal" @input="formatNominal" 
                                class="w-full px-5 py-3.5 outline-none text-gray-700 font-medium"
                                placeholder="0">
                            <input type="hidden" name="nominal" x-model="rawNominal">
                        </div>
                        <?php if (isset(session('errors')['nominal'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['nominal'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Keterangan / Catatan</label>
                        <textarea name="keterangan" rows="4" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['keterangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50 resize-none"
                            placeholder="Tuliskan keterangan asal atau peruntukan keep cash..."><?= old('keterangan', $cashNote['keterangan'] ?? '') ?></textarea>
                        <?php if (isset(session('errors')['keterangan'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['keterangan'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end gap-3">
                <a href="<?= base_url('admin/cash-notes') ?>" class="px-6 py-4 rounded-2xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN CATATAN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function cashNoteForm() {
        return {
            rawNominal: "<?= old('nominal', $cashNote['nominal'] ?? '0') ?>",
            displayNominal: "",
            
            init() { 
                this.updateDisplay(); 
            },
            
            formatNominal(e) {
                let val = e.target.value.replace(/\D/g, "");
                this.rawNominal = val;
                this.updateDisplay();
            },
            
            updateDisplay() {
                if (!this.rawNominal || this.rawNominal === "0") { 
                    this.displayNominal = ""; 
                    return; 
                }
                this.displayNominal = new Intl.NumberFormat('id-ID').format(this.rawNominal);
            }
        }
    }
</script>
<?= $this->endSection() ?>