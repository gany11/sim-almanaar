<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="donorForm()">
    <div class="mb-6 flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($pemasukan) ? 'Edit List Donatur' : 'Tambah List Donatur Baru' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Program: <b class="text-blue-600"><?= $donasi['judul'] ?></b></p>
        </div>
        <a href="<?= base_url('admin/donations/detail/' . $donasi['id_donasi']) ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke detail
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
        <form action="<?= isset($pemasukan) ? base_url('admin/donation-donors/update/'.$pemasukan['id_pemasukan_donasi']) : base_url('admin/donation-donors/save') ?>" method="post" class="p-8">
            <?= csrf_field() ?>
            <input type="hidden" name="id_donasi" value="<?= $donasi['id_donasi'] ?>">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Pilih atau Ketik Nama Donatur</label>
                        <div class="<?= isset(session('errors')['id_donatur']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                            <select name="id_donatur" class="w-full select2-dynamic">
                                <option value="">-- Pilih atau Ketik Nama Baru --</option>
                                <?php 
                                    $currentDonaturId = old('id_donatur', $pemasukan['id_donatur'] ?? '');
                                    $donaturIds = array_column($donaturList, 'id_donatur');
                                ?>
                                <?php foreach($donaturList as $dt): ?>
                                    <option value="<?= $dt['id_donatur'] ?>" <?= $currentDonaturId == $dt['id_donatur'] ? 'selected' : '' ?>>
                                        [<?= $dt['noreg'] ?>] <?= $dt['nama'] ?> <?= !empty($dt['telepon']) ? '(' . $dt['telepon'] . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                                
                                <?php if (!empty($donaturSelected) && !in_array($donaturSelected['id_donatur'], $donaturIds)): ?>
                                    <option value="<?= $donaturSelected['id_donatur'] ?>" selected>
                                        [<?= $donaturSelected['noreg'] ?>] <?= $donaturSelected['nama'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php if (isset(session('errors')['id_donatur'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['id_donatur'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Anda bisa mengetik nama langsung jika donatur belum terdaftar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nomor Kwitansi</label>
                        <?php 
                            // Pisahkan akronim dari nomor kwitansi yang tersimpan (misal: "AK-001" menjadi "001")
                            $rawKwitansi = old('no_kwitansi', $pemasukan['no_kwitansi'] ?? '');
                            $akronimPref = $donasi['akronim_kwitansi'] ?? 'ALMR';
                            $nomorSaja = str_replace($akronimPref . '-', '', $rawKwitansi);
                        ?>
                        <div class="flex items-center bg-white border <?= isset(session('errors')['no_kwitansi']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                            <div class="bg-gray-50 px-4 py-3.5 border-r border-gray-100">
                                <span class="text-gray-500 font-bold font-mono text-sm"><?= $akronimPref ?>-</span>
                            </div>
                            <input type="text" name="no_kwitansi" value="<?= $nomorSaja ?>" 
                                class="w-full px-4 py-3.5 outline-none text-gray-700 font-mono text-sm bg-white" 
                                placeholder="001 (Kosongkan jika otomatis)">
                        </div>
                        <?php if (isset(session('errors')['no_kwitansi'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['no_kwitansi'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Cukup ketik nomor urutnya saja (contoh: 001). Kosongkan untuk generate otomatis.</p>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Status Donasi Awal</label>
                        <select name="id_status_donasi" class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 outline-none select2-basic">
                            <?php foreach($statuses as $st): ?>
                                <option value="<?= $st['id_status_donasi'] ?>" <?= old('id_status_donasi', $pemasukan['id_status_donasi'] ?? 1) == $st['id_status_donasi'] ? 'selected' : '' ?>>
                                    <?= $st['status_donasi'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset(session('errors')['id_status_donasi'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['id_status_donasi'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Waktu Status / Proses</label>
                        <input type="datetime-local" name="waktu" value="<?= old('waktu', isset($histori['waktu']) ? date('Y-m-d\TH:i', strtotime($histori['waktu'])) : date('Y-m-d\TH:i')) ?>" 
                            class="w-full px-5 py-3.5 rounded-2xl border <?= isset(session('errors')['waktu']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 text-sm">
                        <?php if (isset(session('errors')['waktu'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['waktu'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Dikosongkan akan otomatis menggunakan waktu saat ini.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Pengurus</label>
                        <select name="nama_pengurus" class="w-full select2-dynamic">
                            <?php 
                                $currentPengurus = old('nama_pengurus', $histori['nama_pengurus'] ?? session()->get('nama') ?? 'Administrator');
                            ?>
                            <option value="<?= $currentPengurus ?>" selected><?= $currentPengurus ?></option>
                            <?php foreach($pengurusList as $p): ?>
                                <?php if (!empty($p['nama_pengurus']) && $p['nama_pengurus'] !== $currentPengurus): ?>
                                    <option value="<?= $p['nama_pengurus'] ?>"><?= $p['nama_pengurus'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset(session('errors')['nama_pengurus'])) : ?>
                            <p class="text-xs text-red-500 mt-2 font-bold"><?= session('errors')['nama_pengurus'] ?></p>
                        <?php endif; ?>
                        <p class="text-[11px] text-gray-400 mt-1">Bisa dipilih dari riwayat atau diketik baru.</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-50 flex justify-end gap-4">
                <a href="<?= base_url('admin/donations/detail/' . $donasi['id_donasi']) ?>" 
                    class="px-6 py-4 border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold rounded-2xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-3">
                    <i data-lucide="save" class="w-5 h-5"></i> SIMPAN LIST DONATUR
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
                placeholder: "Pilih atau ketik baru...",
                allowClear: true,
                width: '100%'
            });
        }
    });

    function donorForm() {
        return {}
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