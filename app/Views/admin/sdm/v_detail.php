<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10">

    <!-- ============================================================
         HEADER
    ============================================================ -->
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Detail Data SDM / Petugas
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Lihat informasi kontak, data diri, dan riwayat penugasan agenda SDM.
            </p>
        </div>

        <?php if (can_access('sdm.update') || can_access('sdm.read')): ?>
            <div class="flex items-center gap-4">
                <?php if (can_access('sdm.update')): ?>
                    <a href="<?= base_url('admin/sdm/edit/' . $sdm['id_sdm']) ?>"
                        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">

                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        Edit SDM
                    </a>
                <?php endif; ?>

                <?php if(can_access('sdm.read')): ?>
                    <a href="<?= base_url('admin/sdm') ?>"
                    class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Kembali ke daftar
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>


    <!-- ============================================================
         PROFIL SDM
    ============================================================ -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0 mb-6">

        <!-- Header Profile -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-6">

            <div class="flex flex-col md:flex-row md:items-center gap-5">

                <!-- Avatar -->
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/20 shrink-0">
                    <i data-lucide="user-round" class="w-10 h-10 text-white"></i>
                </div>

                <div class="text-white">
                    <h3 class="text-2xl font-bold">
                        <?= esc($sdm['nama']) ?>
                    </h3>
                </div>

            </div>

        </div>


        <!-- Informasi Profile -->
        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Email -->
                <div class="flex items-start gap-3">

                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Email
                        </p>

                        <p class="text-sm text-gray-700 mt-1 break-words">
                            <?= esc($sdm['email'] ?: '-') ?>
                        </p>
                    </div>

                </div>


                <!-- Telepon -->
                <div class="flex items-start gap-3">

                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Telepon / WhatsApp
                        </p>

                        <p class="text-sm text-gray-700 mt-1 break-words">
                            <?= esc($sdm['telepon'] ?: '-') ?>
                        </p>
                    </div>

                </div>


                <!-- Alamat -->
                <div class="flex items-start gap-3">

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Alamat
                        </p>

                        <p class="text-sm text-gray-700 mt-1 break-words">
                            <?= esc($sdm['alamat'] ?: '-') ?>
                        </p>
                    </div>

                </div>


                <!-- Total Agenda -->
                <div class="flex items-start gap-3">

                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Total Agenda
                        </p>

                        <p class="text-lg font-bold text-gray-800 mt-0.5">
                            <?= count($agendas) ?>
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================
         DAFTAR AGENDA
    ============================================================ -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mx-4 md:mx-0">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">

            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-blue-600"></i>
                    Riwayat Agenda
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar agenda yang melibatkan SDM ini.
                </p>
            </div>

            <div class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                <?= count($agendas) ?> Agenda
            </div>

        </div>


        <?php if (!empty($agendas)): ?>

            <!--
                GRID:
                Mobile  : 1 kolom
                Tablet  : 2 kolom
                Desktop : 4 kolom
            -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <?php foreach ($agendas as $item): ?>

                    <?php
                    /*
                     * ========================================================
                     * DATA DASAR
                     * ========================================================
                     */

                    $idKategoriAgenda = (int) ($item['id_kategori_agenda'] ?? 0);

                    $namaKategori = $item['nama_kategori'] ?? 'Agenda';

                    $kategoriClass = $item['kategori_class_color']
                        ?? 'bg-blue-100 text-blue-700';

                    $kategoriSdm = $item['kategori_sdm'] ?? '';

                    $kategoriSdmClass = $item['kategori_sdm_class_color']
                        ?? 'bg-gray-100 text-gray-700';


                    /*
                     * ========================================================
                     * TANGGAL
                     * ========================================================
                     */

                    $tanggal = !empty($item['waktu_mulai'])
                        ? format_indo($item['waktu_mulai'], 'full')
                        : '-';


                    /*
                     * ========================================================
                     * JAM
                     * ========================================================
                     */

                    $jamMulai = !empty($item['waktu_mulai'])
                        ? date('H:i', strtotime($item['waktu_mulai']))
                        : '';

                    $jamSelesai = !empty($item['waktu_selesai'])
                        ? date('H:i', strtotime($item['waktu_selesai']))
                        : '';


                    /*
                     * ========================================================
                     * KETERANGAN WAKTU
                     *
                     * Kategori 1:
                     *     hanya jam mulai
                     *
                     * Selain kategori 1:
                     *     utamakan keterangan waktu.
                     * ========================================================
                     */

                    $keteranganMulai = trim(
                        $item['keterangan_waktu_mulai'] ?? ''
                    );

                    $keteranganSelesai = trim(
                        $item['keterangan_waktu_selesai'] ?? ''
                    );


                    if ($idKategoriAgenda === 1) {

                        // Kategori 1 hanya menampilkan jam mulai.
                        $displayWaktu = $jamMulai ? $jamMulai .' WIB': '-';

                    } else {

                        // Selain kategori 1, utamakan keterangan waktu.
                        if ($keteranganMulai && $keteranganSelesai) {

                            $displayWaktu =
                                $keteranganMulai .
                                ' - ' .
                                $keteranganSelesai;

                        } elseif ($keteranganMulai) {

                            $displayWaktu = 
                                $keteranganMulai.
                                ' - ' .
                                ($jamSelesai ? $jamSelesai .' WIB': '-');

                        } elseif ($keteranganSelesai) {

                            $displayWaktu = 
                                ($jamMulai ? $jamMulai .' WIB': '-') .
                                ' - ' .
                                $keteranganSelesai;

                        } elseif ($jamMulai && $jamSelesai) {

                            // Fallback jika tidak ada keterangan waktu.
                            $displayWaktu =
                                $jamMulai .
                                ' - ' .
                                $jamSelesai . ' WIB';

                        } elseif ($jamMulai) {

                            $displayWaktu = $jamMulai . ' WIB';

                        } else {

                            $displayWaktu = '-';
                        }
                    }


                    /*
                     * ========================================================
                     * DATA MODAL
                     * ========================================================
                     */

                    $modalData = [
                        'id'                => $item['id_agenda'],
                        'category'          => $namaKategori,
                        'categoryClass'     => $kategoriClass,
                        'categorySdm'       => $kategoriSdm,
                        'categorySdmClass'  => $kategoriSdmClass,
                        'theme'             => $item['tema'] ?? '',
                        'title'             => $item['judul'] ?? '',
                        'date'              => $tanggal,
                        'time'              => $displayWaktu,
                        'loc'               => $item['tempat'] ?? '-',
                        'desc'              => $item['deskripsi'] ?? '',
                        'waktu_mulai'       => $item['waktu_mulai'] ?? '',
                    ];
                    ?>


                    <!-- ====================================================
                         CARD AGENDA
                    ==================================================== -->
                    <div
                        class="group bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                        onclick='openSdmAgendaModal(<?= json_encode($modalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                    >

                        <!-- Garis warna kategori -->
                        <div class="h-1.5 <?= esc($kategoriClass) ?>"></div>

                        <div class="p-5">

                            <!-- KATEGORI -->
                            <div class="flex items-center justify-between gap-2 mb-4">

                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide <?= esc($kategoriClass) ?>">
                                    <?= esc($namaKategori) ?>
                                </span>

                                <i
                                    data-lucide="external-link"
                                    class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors"
                                ></i>

                            </div>


                            <!-- TANGGAL -->
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">

                                <i
                                    data-lucide="calendar"
                                    class="w-4 h-4 text-blue-500 shrink-0"
                                ></i>

                                <span>
                                    <?= esc($tanggal) ?>
                                </span>

                            </div>


                            <!-- Tema/Judul -->
                            <h4 class="font-bold text-gray-800 text-sm leading-relaxed line-clamp-2 min-h-[40px]">
                                <?= esc($item['judul'] ?: $item['tema'] ?: '-') ?>
                            </h4>


                            <!-- INFO -->
                            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2.5">

                                <!-- Waktu -->
                                <div class="flex items-start gap-2">

                                    <i
                                        data-lucide="clock"
                                        class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0"
                                    ></i>

                                    <span class="text-xs text-gray-500 line-clamp-2">
                                        <?= esc($displayWaktu) ?>
                                    </span>

                                </div>


                                <!-- Tempat -->
                                <div class="flex items-start gap-2">

                                    <i
                                        data-lucide="map-pin"
                                        class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0"
                                    ></i>

                                    <span class="text-xs text-gray-500 line-clamp-1">
                                        <?= esc($item['tempat'] ?: '-') ?>
                                    </span>

                                </div>

                            </div>


                            <!-- KATEGORI SDM -->
                            <?php if (!empty($kategoriSdm)): ?>

                                <div class="mt-4">

                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold <?= esc($kategoriSdmClass) ?>"
                                    >

                                        <i
                                            data-lucide="user-round"
                                            class="w-3 h-3"
                                        ></i>

                                        <?= esc($kategoriSdm) ?>

                                    </span>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <!-- EMPTY STATE -->
            <div class="text-center py-16">

                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">

                    <i
                        data-lucide="calendar-x"
                        class="w-8 h-8 text-gray-300"
                    ></i>

                </div>

                <h4 class="font-bold text-gray-500">
                    Belum Ada Agenda
                </h4>

                <p class="text-sm text-gray-400 mt-1">
                    SDM ini belum memiliki penugasan agenda.
                </p>

            </div>

        <?php endif; ?>

    </div>


    <!-- ================================================================
         MODAL DETAIL AGENDA
    ================================================================= -->
    <div
        id="sdmAgendaModal"
        class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >

        <div
            id="sdmAgendaModalContent"
            class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl"
            onclick="event.stopPropagation()"
        >

            <!-- HEADER MODAL -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">

                <span
                    id="modalAgendaCategory"
                    class="px-3 py-1 text-[10px] font-bold uppercase rounded-full"
                >
                    Agenda
                </span>

                <button
                    type="button"
                    onclick="closeSdmAgendaModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

            </div>


            <!-- CONTENT -->
            <div class="p-6 space-y-5">

                <!-- TEMA / JUDUL -->
                <div>

                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Tema / Judul
                    </p>

                    <h3 class="text-xl font-bold text-gray-900 mt-1 leading-relaxed">

                        <span id="modalAgendaTheme"></span>

                        <span
                            id="modalAgendaTitle"
                            class="text-gray-500 font-medium"
                        ></span>

                    </h3>

                </div>


                <!-- WAKTU & TEMPAT -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">

                    <!-- WAKTU -->
                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">

                            <i
                                data-lucide="calendar"
                                class="w-4 h-4 text-blue-600"
                            ></i>

                        </div>

                        <div class="text-xs text-gray-600">

                            <p class="font-bold text-gray-700 mb-0.5">
                                Waktu
                            </p>

                            <p id="modalAgendaDate"></p>

                            <p
                                id="modalAgendaTime"
                                class="font-medium text-gray-700 mt-0.5"
                            ></p>

                        </div>

                    </div>


                    <!-- TEMPAT -->
                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">

                            <i
                                data-lucide="map-pin"
                                class="w-4 h-4 text-blue-600"
                            ></i>

                        </div>

                        <div class="text-xs text-gray-600">

                            <p class="font-bold text-gray-700 mb-0.5">
                                Tempat
                            </p>

                            <p id="modalAgendaLocation"></p>

                        </div>

                    </div>

                </div>


                <!-- KATEGORI SDM -->
                <div id="modalAgendaSdmWrapper" class="pt-1 hidden">

                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Peran / Kategori SDM
                    </p>

                    <span
                        id="modalAgendaSdmCategory"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold"
                    >
                        <i
                            data-lucide="user-round"
                            class="w-3.5 h-3.5"
                        ></i>

                        <span id="modalAgendaSdmText"></span>
                    </span>

                </div>


                <!-- DESKRIPSI -->
                <div class="pt-4 border-t border-gray-100">

                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Deskripsi
                    </p>

                    <div
                        id="modalAgendaDescription"
                        class="text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none"
                    ></div>

                </div>


                <!-- FOOTER -->
                <div class="pt-4 border-t border-gray-100 flex justify-between items-center gap-3">

                    <button
                        type="button"
                        onclick="closeSdmAgendaModal()"
                        class="ml-auto px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all"
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
/**
 * ================================================================
 * MODAL DETAIL AGENDA SDM
 * ================================================================
 */

function openSdmAgendaModal(data) {

    const modal = document.getElementById('sdmAgendaModal');

    if (!modal) return;


    // ------------------------------------------------------------
    // KATEGORI AGENDA
    // ------------------------------------------------------------

    const category = document.getElementById('modalAgendaCategory');

    category.textContent = data.category || 'Agenda';

    category.className =
        'px-3 py-1 text-[10px] font-bold uppercase rounded-full ' +
        (data.categoryClass || 'bg-blue-100 text-blue-700');


    // ------------------------------------------------------------
    // TEMA
    // ------------------------------------------------------------

    document.getElementById('modalAgendaTheme').textContent =
        data.theme || '-';


    // ------------------------------------------------------------
    // JUDUL
    // ------------------------------------------------------------

    const titleElement =
        document.getElementById('modalAgendaTitle');

    if (data.title) {

        titleElement.textContent =
            ' (' + data.title + ')';

        titleElement.classList.remove('hidden');

    } else {

        titleElement.textContent = '';

        titleElement.classList.add('hidden');

    }


    // ------------------------------------------------------------
    // TANGGAL
    // ------------------------------------------------------------

    document.getElementById('modalAgendaDate').textContent =
        data.date || '-';


    // ------------------------------------------------------------
    // WAKTU
    // ------------------------------------------------------------

    document.getElementById('modalAgendaTime').textContent =
        data.time || '-';


    // ------------------------------------------------------------
    // TEMPAT
    // ------------------------------------------------------------

    document.getElementById('modalAgendaLocation').textContent =
        data.loc || '-';


    // ------------------------------------------------------------
    // KATEGORI SDM
    // ------------------------------------------------------------

    const sdmWrapper =
        document.getElementById('modalAgendaSdmWrapper');

    const sdmCategory =
        document.getElementById('modalAgendaSdmCategory');

    const sdmText =
        document.getElementById('modalAgendaSdmText');

    if (data.categorySdm) {

        sdmWrapper.classList.remove('hidden');

        sdmCategory.className =
            'inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold ' +
            (data.categorySdmClass || 'bg-gray-100 text-gray-700');

        sdmText.textContent =
            data.categorySdm;

    } else {

        sdmWrapper.classList.add('hidden');

    }


    // ------------------------------------------------------------
    // DESKRIPSI
    // ------------------------------------------------------------

    const description =
        document.getElementById('modalAgendaDescription');

    if (data.desc) {

        /*
         * Deskripsi berasal dari database dan kemungkinan
         * mengandung HTML dari TinyMCE.
         *
         * Karena sebelumnya modal agenda menggunakan x-html,
         * di sini kita juga mempertahankan HTML tersebut.
         */
        description.innerHTML = data.desc;

    } else {

        description.innerHTML =
            '<span class="text-gray-400">Tidak ada deskripsi.</span>';

    }


    // ------------------------------------------------------------
    // TAMPILKAN MODAL
    // ------------------------------------------------------------

    modal.classList.remove('hidden');

    modal.classList.add('flex');

    document.body.classList.add('overflow-hidden');


    // ------------------------------------------------------------
    // ICON
    // ------------------------------------------------------------

    setTimeout(() => {

        if (window.reinitIcons) {

            window.reinitIcons();

        } else if (typeof lucide !== 'undefined') {

            lucide.createIcons();

        }

    }, 50);
}


/**
 * ================================================================
 * TUTUP MODAL
 * ================================================================
 */

function closeSdmAgendaModal() {

    const modal =
        document.getElementById('sdmAgendaModal');

    if (!modal) return;

    modal.classList.add('hidden');

    modal.classList.remove('flex');

    document.body.classList.remove('overflow-hidden');
}


/**
 * ================================================================
 * KLIK BACKDROP
 * ================================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    const modal =
        document.getElementById('sdmAgendaModal');

    if (!modal) return;


    modal.addEventListener('click', function (event) {

        if (event.target === modal) {

            closeSdmAgendaModal();

        }

    });


    // ------------------------------------------------------------
    // ESC UNTUK MENUTUP MODAL
    // ------------------------------------------------------------

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            if (!modal.classList.contains('hidden')) {

                closeSdmAgendaModal();

            }

        }

    });


    // ------------------------------------------------------------
    // INIT LUCIDE
    // ------------------------------------------------------------

    if (window.reinitIcons) {

        window.reinitIcons();

    } else if (typeof lucide !== 'undefined') {

        lucide.createIcons();

    }

});
</script>

<?= $this->endSection() ?>