<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10" x-data="agendaForm()">
    <div class="mb-6 flex justify-between items-center px-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= isset($agenda) ? 'Edit Agenda' : 'Buat Agenda Baru' ?></h2>
            <p class="text-sm text-gray-500">Kelola jadwal kegiatan dan penugasan SDM masjid.</p>
        </div>
        <a href="<?= base_url('admin/agenda') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar agenda
        </a>
    </div>

    <form action="<?= isset($agenda) ? base_url('admin/agenda/update/'.$agenda['id_agenda']) : base_url('admin/agenda/save') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="info" class="w-5 h-5 text-blue-500"></i> Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Tema Kegiatan (Opsional)</label>
                            <input type="text" name="tema" value="<?= old('tema', $agenda['tema'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['tema']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Contoh: Kajian Rutin.">
                            <p class="text-xs text-emerald-700 mt-2">Untuk Agenda Sholat Jum'at Dapat Dikosongkan / (-) Bila Belum Ada Tema.</p>
                            <?php if (isset(session('errors')['tema'])) : ?>
                                <p class="text-xs text-red-500 mt-2"><?= session('errors')['tema'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Materi (Opsional)</label>
                            <input type="text" name="judul" value="<?= old('judul', $agenda['judul'] ?? '') ?>" 
                                class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                placeholder="Judul spesifik pembahasan...">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Kategori Agenda</label>
                            <div class="<?= isset(session('errors')['id_kategori_agenda']) ? 'border-red-500 ring-1 ring-red-500 rounded-2xl' : '' ?>">
                                <select name="id_kategori_agenda" class="w-full px-5 py-3 rounded-2xl border border-gray-200 outline-none select2-basic">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?= $c['id_kategori_agenda'] ?>" <?= (old('id_kategori_agenda', $agenda['id_kategori_agenda'] ?? '') == $c['id_kategori_agenda']) ? 'selected' : '' ?>>
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
                        <textarea name="deskripsi" class="editor"><?= old('deskripsi', $agenda['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>

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

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2 border-b pb-4">
                        <i data-lucide="map-pin" class="w-5 h-5 text-red-500"></i> Lokasi & Waktu
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Tempat</label>
                        <textarea name="tempat" rows="2" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all"><?= old('tempat', $agenda['tempat'] ?? 'Ruang Utama Masjid Al-Manaar Slipi') ?></textarea>
                    </div>

                    <hr class="border-gray-50">

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" value="<?= isset($agenda) ? date('Y-m-d\TH:i', strtotime($agenda['waktu_mulai'])) : old('waktu_mulai') ?>" 
                            class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['waktu_mulai']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset(session('errors')['waktu_mulai'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['waktu_mulai'] ?></p>
                        <?php endif; ?>
                        
                        <select name="id_keterangan_waktu_mulai" class="mt-2 w-full px-4 py-2 text-xs rounded-xl bg-gray-50 border-none outline-none">
                            <option value="">-- Ket. Waktu Mulai (Opsional) --</option>
                            <?php foreach($times as $t): ?>
                                <option value="<?= $t['id_keterangan_waktu'] ?>" <?= (old('id_keterangan_waktu_mulai', $agenda['id_keterangan_waktu_mulai'] ?? '') == $t['id_keterangan_waktu']) ? 'selected' : '' ?>><?= $t['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Waktu Selesai (Opsional / Otomatis) </label>
                        <input type="datetime-local" name="waktu_selesai" value="<?= isset($agenda) ? date('Y-m-d\TH:i', strtotime($agenda['waktu_selesai'])) : old('waktu_selesai') ?>" 
                            class="w-full px-5 py-3 rounded-2xl border <?= isset(session('errors')['waktu_selesai']) ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' ?> outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset(session('errors')['waktu_selesai'])) : ?>
                            <p class="text-xs text-red-500 mt-2"><?= session('errors')['waktu_selesai'] ?></p>
                        <?php endif; ?>

                        <select name="id_keterangan_waktu_selesai" class="mt-2 w-full px-4 py-2 text-xs rounded-xl bg-gray-50 border-none outline-none">
                            <option value="">-- Ket. Waktu Selesai (Opsional) --</option>
                            <?php foreach($times as $t): ?>
                                <option value="<?= $t['id_keterangan_waktu'] ?>" <?= (old('id_keterangan_waktu_selesai', $agenda['id_keterangan_waktu_selesai'] ?? '') == $t['id_keterangan_waktu']) ? 'selected' : '' ?>><?= $t['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-3 active:scale-95">
                        <i data-lucide="save" class="w-5 h-5"></i> Simpan Agenda
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