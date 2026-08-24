<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<?php
    // Menyiapkan data old / existing untuk looping agar Alpine.js bisa membacanya
    $oldHari   = old('looping_hari') ? old('looping_hari') : (isset($agenda_rutin['looping_hari']) && $agenda_rutin['looping_hari'] ? explode(',', $agenda_rutin['looping_hari']) : []);
    $oldMinggu = old('looping_minggu') ? old('looping_minggu') : (isset($agenda_rutin['looping_minggu']) && $agenda_rutin['looping_minggu'] ? explode(',', $agenda_rutin['looping_minggu']) : []);
?>
<div class="container mx-auto pb-10" x-data="agendaForm()">
    <div class="mb-6 flex justify-between items-center px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($agenda_rutin) ? 'Edit Agenda Rutin' : 'Buat Agenda Rutin Baru' ?></h2>
            <p class="text-sm text-gray-500">Kelola jadwal kegiatan rutin dan penugasan SDM masjid.</p>
        </div>
        <a href="<?= base_url('admin/routine-agenda') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <form action="<?= isset($agenda_rutin) ? base_url('admin/routine-agenda/update/'.$agenda_rutin['id_agenda_rutin']) : base_url('admin/routine-agenda/save') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Informasi Dasar & SDM -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="info" class="w-5 h-5 text-blue-500"></i> Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Tema Kegiatan (Wajib)</label>
                            <input type="text" name="tema" value="<?= old('tema', $agenda_rutin['tema'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['tema']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Contoh: Kajian Fiqih Rutin / TPA Sore">
                            <?php if (isset(session('errors')['tema'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['tema'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Materi (Opsional)</label>
                            <input type="text" name="judul" value="<?= old('judul', $agenda_rutin['judul'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['judul']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Judul spesifik (jika sudah ada)...">
                            <?php if (isset(session('errors')['judul'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['judul'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Kategori Agenda</label>
                            <div class="<?= isset(session('errors')['id_kategori_agenda']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                                <select name="id_kategori_agenda" class="w-full px-5 py-3 rounded-2xl border border-gray-200 outline-none select2-basic">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?= $c['id_kategori_agenda'] ?>" <?= (old('id_kategori_agenda', $agenda_rutin['id_kategori_agenda'] ?? '') == $c['id_kategori_agenda']) ? 'selected' : '' ?>>
                                            <?= $c['nama_kategori'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if (isset(session('errors')['id_kategori_agenda'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['id_kategori_agenda'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" class="editor"><?= old('deskripsi', $agenda_rutin['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Penugasan SDM -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <div class="flex justify-between items-center border-b pb-4">
                        <h3 class="font-bold text-gray-700 flex items-center gap-2">
                            <i data-lucide="users" class="w-5 h-5 text-emerald-500"></i> Penugasan SDM / Pengisi
                        </h3>
                        <button type="button" @click="addSdm()" class="text-xs font-bold bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl hover:bg-emerald-600 hover:text-white transition-all">
                            + Tambah Pengisi
                        </button>
                    </div>
                    <?php if (isset(session('errors')['sdm'])): ?>
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5"></i>
                                <p class="text-sm text-red-700">
                                    <?= session('errors')['sdm'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-4">
                         <template x-for="(item, index) in sdmList" :key="item._key">
                            <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 relative" x-init="initSelect2()">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nama SDM</label>
                                    <select :name="'sdm_id['+index+']'" class="sdm-select w-full" :data-index="index">
                                        <option value="">Cari atau Ketik Nama Baru</option>
                                        <?php foreach($all_sdm as $s): ?>
                                            <option value="<?= $s['id_sdm'] ?>" :selected="item.id_sdm == '<?= $s['id_sdm'] ?>'"><?= $s['nama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="w-full md:w-48">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Peran / Kategori</label>
                                    <select :name="'sdm_role['+index+']'" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none text-sm" x-model="item.id_kategori_sdm">
                                        <option value="">-- Pilih Peran --</option>
                                        <?php foreach($sdm_roles as $r): ?>
                                            <option value="<?= $r['id_kategori_sdm'] ?>"><?= $r['kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="button" @click="removeSdm(index)" class="self-end md:mb-1 p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Lokasi, Waktu & Rutinitas -->
            <div class="space-y-6">
                <!-- Waktu & Pola -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="clock" class="w-5 h-5 text-red-500"></i> Lokasi & Waktu
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Tempat</label>
                        <textarea name="tempat" rows="2" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all"><?= old('tempat', $agenda_rutin['tempat'] ?? 'Ruang Utama Masjid Al-Manaar Slipi') ?></textarea>
                    </div>

                    <hr class="border-gray-50">

                    <!-- Waktu -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" value="<?= isset($agenda_rutin['waktu_mulai']) ? date('H:i', strtotime($agenda_rutin['waktu_mulai'])) : old('waktu_mulai') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['waktu_mulai']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> outline-none focus:ring-2 focus:ring-blue-500">
                            <?php if (isset(session('errors')['waktu_mulai'])) : ?>
                                <p class="text-[10px] text-red-500 mt-1"><?= session('errors')['waktu_mulai'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" value="<?= isset($agenda_rutin['waktu_selesai']) && !empty($agenda_rutin['waktu_selesai']) ? date('H:i', strtotime($agenda_rutin['waktu_selesai'])) : old('waktu_selesai') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['waktu_selesai']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> outline-none focus:ring-2 focus:ring-blue-500">
                            
                            <!-- Tambahan Teks Bantuan -->
                            <p class="text-[10px] font-medium text-blue-600 mt-2">
                                * Jika dikosongkan maka otomatis +1 Jam dari Waktu Mulai.
                            </p>
                            
                            <?php if (isset(session('errors')['waktu_selesai'])) : ?>
                                <p class="text-xs text-red-500 mt-1"><?= session('errors')['waktu_selesai'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Keterangan Waktu -->
                    <div class="space-y-2">
                        <select name="id_keterangan_waktu_mulai" class="w-full px-4 py-2 text-xs rounded-xl bg-gray-50 border-none outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Ket. Waktu Mulai (Opsional) --</option>
                            <?php foreach($times as $t): ?>
                                <option value="<?= $t['id_keterangan_waktu'] ?>" <?= (old('id_keterangan_waktu_mulai', $agenda_rutin['id_keterangan_waktu_mulai'] ?? '') == $t['id_keterangan_waktu']) ? 'selected' : '' ?>><?= $t['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="id_keterangan_waktu_selesai" class="w-full px-4 py-2 text-xs rounded-xl bg-gray-50 border-none outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Ket. Waktu Selesai (Opsional) --</option>
                            <?php foreach($times as $t): ?>
                                <option value="<?= $t['id_keterangan_waktu'] ?>" <?= (old('id_keterangan_waktu_selesai', $agenda_rutin['id_keterangan_waktu_selesai'] ?? '') == $t['id_keterangan_waktu']) ? 'selected' : '' ?>><?= $t['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr class="border-gray-50">

                    <!-- Pola Looping -->
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <i data-lucide="repeat" class="w-5 h-5 text-indigo-500"></i> Pola Rutinitas
                    </h3>
                    
                    <!-- Looping Hari -->
                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-semibold text-gray-600">Hari Pelaksanaan</label>
                            <label class="flex items-center gap-2 text-[11px] font-bold text-blue-600 cursor-pointer">
                                <input type="checkbox" x-model="setiapHari" @change="toggleSemuaHari()" class="rounded text-blue-600 focus:ring-blue-500">
                                Setiap Hari
                            </label>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="(hariName, index) in listHari" :key="index">
                                <label class="flex items-center gap-2 text-xs text-gray-700 bg-white px-3 py-2 rounded-xl border border-gray-200 cursor-pointer hover:border-blue-300 transition-colors">
                                    <input type="checkbox" name="looping_hari[]" :value="index + 1" x-model="hari" @change="checkHari()" class="rounded text-blue-600 focus:ring-blue-500">
                                    <span x-text="hariName"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Looping Minggu -->
                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-semibold text-gray-600">Pekan Pelaksanaan</label>
                            <label class="flex items-center gap-2 text-[11px] font-bold text-blue-600 cursor-pointer">
                                <input type="checkbox" x-model="setiapPekan" @change="toggleSemuaPekan()" class="rounded text-blue-600 focus:ring-blue-500">
                                Setiap Pekan
                            </label>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="pekan in [1,2,3,4,5]" :key="pekan">
                                <label class="flex items-center gap-2 text-xs text-gray-700 bg-white px-3 py-2 rounded-xl border border-gray-200 cursor-pointer hover:border-blue-300 transition-colors">
                                    <input type="checkbox" name="looping_minggu[]" :value="pekan" x-model="minggu" @change="checkPekan()" class="rounded text-blue-600 focus:ring-blue-500">
                                    <span x-text="'Pekan ' + pekan"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <hr class="border-gray-50">

                    <!-- Status Toggle -->
                    <div class="flex items-center justify-between p-2">
                        <div>
                            <p class="text-sm font-bold text-gray-700">Status Agenda</p>
                            <p class="text-[11px] text-gray-500">Tentukan apakah rutinitas ini aktif.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="aktif" class="sr-only peer" 
                                <?= old('status', $agenda_rutin['status'] ?? 'aktif') === 'aktif' ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>
                    <?php if (isset(session('errors')['status'])) : ?>
                        <p class="text-[10px] text-red-500 mt-2"><?= session('errors')['status'] ?></p>
                    <?php endif; ?>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="w-full py-4 mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-3 active:scale-95">
                        <i data-lucide="save" class="w-5 h-5"></i> Simpan Agenda Rutin
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
    $sdmOld = [];

    if (old('sdm_id')) {
        foreach (old('sdm_id') as $i => $id) {
            $sdmOld[] = [
                'id_sdm' => $id,
                'id_kategori_sdm' => old('sdm_role')[$i] ?? ''
            ];
        }
    }
?>

<script>
    function agendaForm() {
        return {
            // Setup untuk Alpine.js SDM List
            sdmList: <?= json_encode(
                !empty($sdmOld)
                    ? $sdmOld
                    : ($currentSdm ?? [['id_sdm'=>'','id_kategori_sdm'=>'']])
            ) ?>.map(item => ({ ...item, _key: Math.random().toString(36).substring(2, 9) })),
            
            // Setup untuk Looping Hari dan Pekan
            listHari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            hari: <?= json_encode(array_map('strval', $oldHari)) ?>,
            minggu: <?= json_encode(array_map('strval', $oldMinggu)) ?>,
            setiapHari: false,
            setiapPekan: false,

            init() {
                this.checkHari();
                this.checkPekan();
            },

            // Logika Looping Hari
            toggleSemuaHari() {
                if (this.setiapHari) {
                    this.hari = ['1', '2', '3', '4', '5', '6', '7'];
                } else {
                    this.hari = [];
                }
            },
            checkHari() {
                this.setiapHari = this.hari.length === 7;
            },

            // Logika Looping Pekan
            toggleSemuaPekan() {
                if (this.setiapPekan) {
                    this.minggu = ['1', '2', '3', '4', '5'];
                } else {
                    this.minggu = [];
                }
            },
            checkPekan() {
                this.setiapPekan = this.minggu.length === 5;
            },

            // Logika SDM
           addSdm() {
                this.sdmList.push({
                    id_sdm: '', 
                    id_kategori_sdm: '', 
                    _key: Math.random().toString(36).substring(2, 9) // <-- Tambahkan ini
                });
                this.$nextTick(() => { 
                    if (window.reinitIcons) window.reinitIcons(); 
                });
            },
            
            removeSdm(index) {
                if(this.sdmList.length > 1) this.sdmList.splice(index, 1);
            },

            initSelect2() {
                this.$nextTick(() => {
                    if (window.reinitIcons) window.reinitIcons();

                    $('.sdm-select').each((i, el) => {
                        if (!$(el).hasClass("select2-hidden-accessible")) {
                            $(el).select2({
                                tags: true,
                                placeholder: "Cari atau Ketik Nama Baru",
                                width: '100%'
                            }).on('change', (e) => {
                                let index = $(e.target).data('index');
                                this.sdmList[index].id_sdm = e.target.value;
                            });
                        }
                    });
                });
            }
        }
    }
</script>

<style>
    .select2-container--default .select2-selection--single {
        border-radius: 1rem !important;
        border: 1px solid #e5e7eb !important;
        height: 48px !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>
<?= $this->endSection() ?>