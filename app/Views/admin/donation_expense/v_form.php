<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="expenseForm()">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($expense) ? 'Edit Pengeluaran Donasi' : 'Catat Pengeluaran Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Kelola rincian pengeluaran dana untuk program donasi.</p>
        </div>
        <a href="<?= isset($expense) ? base_url('admin/donations/detail/' . $expense['id_donasi']) : (isset($selectedDonasi) && $selectedDonasi ? base_url('admin/donations/detail/' . $selectedDonasi) : base_url('admin/donations')) ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke detail program
        </a>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold flex items-center gap-3 mx-4 md:mx-0 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span><?= session('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <form action="<?= isset($expense) ? base_url('admin/donation-expenses/update/'.$expense['id_pengeluaran_donasi']) : base_url('admin/donation-expenses/save') ?>" method="post" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Kolom Kiri: Pilih Program Donasi & Tanggal -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Program Donasi</label>
                        
                        <div class="<?= isset(session('errors')['id_donasi']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                            <select name="id_donasi" class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 outline-none select2-basic">
                                <option value="">Pilih Program Donasi...</option>
                                <?php foreach($donations as $d): ?>
                                    <?php 
                                        $isSelect = (isset($expense['id_donasi']) && $expense['id_donasi'] == $d['id_donasi']) ||
                                                    (!isset($expense) && isset($selectedDonasi) && $selectedDonasi == $d['id_donasi']) ||
                                                    old('id_donasi') == $d['id_donasi'];
                                                    
                                        $isLocked = isset($selectedDonasi) && !empty($selectedDonasi);
                                        if ($isLocked && $d['id_donasi'] != $selectedDonasi) continue; 
                                    ?>
                                    <option value="<?= $d['id_donasi'] ?>" <?= $isSelect ? 'selected' : '' ?>>
                                        <?= $d['judul'] ?> (<?= $d['akronim_kwitansi'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (isset(session('errors')['id_donasi'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['id_donasi'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Tanggal Pengeluaran</label>
                        <input type="date" name="tanggal" max="<?= date('Y-m-d') ?>" value="<?= old('tanggal', $expense['tanggal'] ?? date('Y-m-d')) ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['tanggal']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm">
                        <?php if (isset(session('errors')['tanggal'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['tanggal'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Keterangan, Qty, Satuan Select2, Harga Satuan, & Sub Total -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Keterangan / Keperluan</label>
                        <input type="text" name="keterangan" value="<?= old('keterangan', $expense['keterangan'] ?? '') ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['keterangan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50"
                            placeholder="Contoh: Pembelian semen 50 sak & pasir 2 truk...">
                        <?php if (isset(session('errors')['keterangan'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['keterangan'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Jumlah (Kuantitas / Qty)</label>
                            <input type="number" name="jumlah" x-model.number="jumlah" @input="calculateSubtotal" min="1" 
                                class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['jumlah']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50"
                                placeholder="0">
                            <?php if (isset(session('errors')['jumlah'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['jumlah'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Satuan Barang</label>
                            <!-- Select2 Dynamic Tags (Data satuan didapat langsung dari database controller tanpa AJAX) -->
                            <div class="<?= isset(session('errors')['satuan']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                                <select name="satuan" class="w-full select2-dynamic">
                                    <option value="">Pilih atau Ketik Satuan Baru...</option>
                                    <?php 
                                        $currentSatuan = old('satuan', $expense['satuan'] ?? '');
                                        $foundSatuan = false;
                                        foreach($units as $u) {
                                            if ($u['satuan'] == $currentSatuan) $foundSatuan = true;
                                        }
                                    ?>
                                    <?php foreach($units as $u): ?>
                                        <option value="<?= $u['satuan'] ?>" <?= $currentSatuan == $u['satuan'] ? 'selected' : '' ?>><?= $u['satuan'] ?></option>
                                    <?php endforeach; ?>
                                    <?php if (!empty($currentSatuan) && !$foundSatuan): ?>
                                        <option value="<?= $currentSatuan ?>" selected><?= $currentSatuan ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php if (isset(session('errors')['satuan'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['satuan'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Harga Satuan (Rp)</label>
                            <div class="flex items-center bg-white border <?= isset(session('errors')['harga_satuan']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                <div class="bg-gray-50 px-5 py-3.5 border-r border-gray-100">
                                    <span class="text-gray-400 font-bold">Rp</span>
                                </div>
                                <input type="text" x-model="displayHarga" @input="formatHarga" 
                                    class="w-full px-5 py-3.5 outline-none text-gray-700 font-medium"
                                    placeholder="0">
                                <input type="hidden" name="harga_satuan" x-model="rawHarga">
                            </div>
                            <?php if (isset(session('errors')['harga_satuan'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['harga_satuan'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Sub Total Otomatis (Rp)</label>
                            <div class="flex items-center bg-gray-100 border border-gray-200 rounded-2xl overflow-hidden px-5 py-3.5">
                                <span class="text-gray-400 font-bold mr-2">Rp</span>
                                <span class="text-gray-800 font-bold text-base" x-text="displaySubtotal">0</span>
                                <input type="hidden" name="sub_total" x-model="rawSubtotal">
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 p-6 rounded-3xl border <?= isset(session('errors')['bukti']) ? 'border-red-500 ring-1 ring-red-500' : 'border-blue-100/50' ?>">
                        <label class="block text-sm font-bold text-blue-900 mb-1">Bukti Nota / Kwitansi (Opsional)</label>
                        <p class="text-xs text-blue-600/70 mb-4 tracking-tight">Format file PDF atau Gambar (JPG/JPEG/PNG), Maks. 5MB.</p>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-700 rounded-xl cursor-pointer hover:bg-blue-600 hover:text-white transition-all font-semibold text-sm shadow-sm active:scale-95">
                                <input type="file" name="bukti" class="sr-only" accept="application/pdf,image/*" 
                                    @change="fileName = $event.target.files[0].name">
                                <i data-lucide="file-up" class="w-4 h-4"></i>
                                Pilih File
                            </label>

                            <span class="text-xs text-gray-400 italic truncate max-w-[250px]">
                                <template x-if="fileName">
                                    <span class="text-blue-600 font-bold" x-text="'Terpilih: ' + fileName"></span>
                                </template>
                                <template x-if="!fileName">
                                    <span>
                                        <?php if(isset($expense['bukti']) && $expense['bukti']): ?>
                                            File saat ini: <a href="<?= base_url('uploads/donasi/bukti/'.$expense['bukti']) ?>" target="_blank" class="text-blue-600 underline font-bold">Lihat Berkas</a>
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

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end gap-4">
                <a href="<?= isset($expense) ? base_url('admin/donations/detail/' . $expense['id_donasi']) : (isset($selectedDonasi) && $selectedDonasi ? base_url('admin/donations/detail/' . $selectedDonasi) : base_url('admin/donations')) ?>" 
                    class="px-6 py-4 border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold rounded-2xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl shadow-lg shadow-red-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN PENGELUARAN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.jQuery && $.fn.select2) {
            $('.select2-basic').select2({ width: '100%' });
            $('.select2-dynamic').select2({
                tags: true,
                placeholder: "Pilih atau ketik satuan baru...",
                allowClear: true,
                width: '100%'
            });
        }
    });

    function expenseForm() {
        return {
            jumlah: <?= old('jumlah', $expense['jumlah'] ?? 1) ?>,
            rawHarga: "<?= old('harga_satuan', $expense['harga_satuan'] ?? '0') ?>",
            displayHarga: "",
            rawSubtotal: "<?= old('sub_total', $expense['sub_total'] ?? '0') ?>",
            displaySubtotal: "0",
            fileName: "",

            init() {
                if (this.rawHarga && this.rawHarga !== "0") {
                    let parts = this.rawHarga.split('.');
                    this.displayHarga = new Intl.NumberFormat('id-ID').format(parts[0]);
                }
                this.calculateSubtotal();
            },

            formatHarga(e) {
                let val = e.target.value.replace(/\D/g, "");
                this.rawHarga = val;
                if (!val || val === "0") {
                    this.displayHarga = "";
                } else {
                    this.displayHarga = new Intl.NumberFormat('id-ID').format(val);
                }
                this.calculateSubtotal();
            },

            calculateSubtotal() {
                let qty = parseInt(this.jumlah) || 0;
                let price = parseFloat(this.rawHarga) || 0;
                let sub = qty * price;
                
                this.rawSubtotal = sub;
                this.displaySubtotal = new Intl.NumberFormat('id-ID').format(sub);
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