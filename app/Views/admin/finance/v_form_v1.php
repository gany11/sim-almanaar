<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="financeForm()">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($keuangan) ? 'Edit Transaksi' : 'Catat Transaksi Baru' ?></h2>
            <p class="text-sm text-gray-500">Formulir arus kas harian/mingguan Masjid Al-Manaar.</p>
        </div>
        <a href="<?= base_url('admin/finance/routine') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar keuangan rutin
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="<?= isset($keuangan) ? base_url('admin/finance/routine/update/'.$keuangan['id_keuangan']) : base_url('admin/finance/routine/save') ?>" method="post" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Kategori Kas</label>
                        <select name="id_kategori_keuangan" class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['id_kategori_keuangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer">
                            <option value="">Pilih Kategori...</option>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k['id_kategori_keuangan'] ?>" <?= old('id_kategori_keuangan', $keuangan['id_kategori_keuangan'] ?? '') == $k['id_kategori_keuangan'] ? 'selected' : '' ?>>
                                    <?= $k['kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset(session('errors')['id_kategori_keuangan'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['id_kategori_keuangan'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Jenis Transaksi</label>
                        <select name="jenis" class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['jenis']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer">
                            <option value="pemasukan" <?= old('jenis', $keuangan['jenis'] ?? '') === 'pemasukan' ? 'selected' : '' ?>>Pemasukan (Uang Masuk)</option>
                            <option value="pengeluaran" <?= old('jenis', $keuangan['jenis'] ?? '') === 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran (Uang Keluar)</option>
                        </select>
                        <?php if (isset(session('errors')['jenis'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['jenis'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Tanggal Nota</label>
                        <input type="date" name="tanggal" value="<?= old('tanggal', $keuangan['tanggal'] ?? date('Y-m-d')) ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['tanggal']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm appearance-none cursor-pointer">
                        <?php if (isset(session('errors')['tanggal'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['tanggal'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Keterangan / Peruntukan</label>
                        <input type="text" name="keterangan" value="<?= old('keterangan', $keuangan['keterangan'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['keterangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50"
                            placeholder="Tuliskan detail peruntukan dana...">
                        <?php if (isset(session('errors')['keterangan'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['keterangan'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Nominal Transaksi</label>
                        <div class="flex items-center bg-white border <?= isset(session('errors')['jumlah']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                            <div class="bg-gray-50 px-5 py-3.5 border-r border-gray-100">
                                <span class="text-gray-400 font-bold">Rp</span>
                            </div>
                            <input type="text" x-model="displayJumlah" @input="formatJumlah" 
                                class="w-full px-5 py-3.5 outline-none text-gray-700 font-medium"
                                placeholder="0">
                            <input type="hidden" name="jumlah" x-model="rawJumlah">
                        </div>
                        <?php if (isset(session('errors')['jumlah'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['jumlah'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-blue-50/50 p-6 rounded-3xl border <?= isset(session('errors')['bukti']) ? 'border-red-500 ring-1 ring-red-500' : 'border-blue-100/50' ?>">
                        <label class="block text-sm font-bold text-blue-900 mb-1">Lampiran Dokumen (Opsional)</label>
                        <p class="text-xs text-blue-600/70 mb-4 tracking-tight">Unggah kuitansi atau laporan dalam format PDF (Maks. 5MB).</p>
                        
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-700 rounded-xl cursor-pointer hover:bg-blue-600 hover:text-white transition-all font-semibold text-sm shadow-sm active:scale-95">
                                <i data-lucide="file-up" class="w-4 h-4"></i>
                                Pilih File PDF
                                <input type="file" name="bukti" class="sr-only" accept="application/pdf" 
                                    @change="fileName = $event.target.files[0].name">
                            </label>

                            <span class="text-xs text-gray-400 italic truncate max-w-[250px]">
                                <template x-if="fileName">
                                    <span class="text-blue-600 font-bold" x-text="'Terpilih: ' + fileName"></span>
                                </template>
                                
                                <template x-if="!fileName">
                                    <span>
                                        <?php if(isset($keuangan['bukti']) && $keuangan['bukti']): ?>
                                            File saat ini: <a href="<?= base_url('uploads/keuangan/'.$keuangan['bukti']) ?>" target="_blank" class="text-blue-600 underline font-bold">Lihat PDF</a>
                                        <?php else: ?>
                                            Tidak ada file dipilih
                                        <?php endif; ?>
                                    </span>
                                </template>
                            </span>
                        </div>
                        
                        <?php if (isset(session('errors')['bukti'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['bukti'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN DATA
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function financeForm() {
        return {
            rawJumlah: "<?= old('jumlah', $keuangan['jumlah'] ?? '0') ?>",
            displayJumlah: "",
            fileName: "",
            init() { this.updateDisplay(); },
            formatJumlah(e) {
                let val = e.target.value.replace(/\D/g, "");
                this.rawJumlah = val;
                this.updateDisplay();
            },
            updateDisplay() {
                if (!this.rawJumlah || this.rawJumlah === "0") { this.displayJumlah = ""; return; }
                this.displayJumlah = new Intl.NumberFormat('id-ID').format(this.rawJumlah);
            }
        }
    }
</script>
<?= $this->endSection() ?>