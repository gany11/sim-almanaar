<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Donatur</h2>
            <p class="text-sm text-gray-500">Informasi profil lengkap dan riwayat kontribusi donasi.</p>
        </div>
        <a href="<?= base_url('admin/donors') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors text-sm font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke daftar
        </a>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm mx-4 md:mx-0">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm mx-4 md:mx-0">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Informasi Profil Card -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mx-4 md:mx-0">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-2xl shrink-0 shadow-inner">
                    <?= strtoupper(substr($donor['nama'], 0, 2)) ?>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800"><?= $donor['nama'] ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Terdaftar sejak: <?= format_indo($donor['created_at'], 'full') ?></p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 text-sm text-gray-600 border-t md:border-t-0 pt-4 md:pt-0 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-100 flex-1 md:flex-none">
                    <i data-lucide="mail" class="w-4 h-4 text-blue-500"></i>
                    <span><?= !empty($donor['email']) ? $donor['email'] : 'Belum Terdaftar' ?></span>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-100 flex-1 md:flex-none">
                    <i data-lucide="phone" class="w-4 h-4 text-emerald-500"></i>
                    <span><?= !empty($donor['telepon']) ? $donor['telepon'] : 'Belum Terdaftar' ?></span>
                </div>
            </div>
        </div>

        <?php 
            $hasAlamat = !empty($donor['alamat']);
            $hasRtRwKel = !empty($donor['rt']) || !empty($donor['rw']) || !empty($donor['kelurahan']);
        ?>

        <?php if ($hasAlamat || $hasRtRwKel): ?>
            <div class="mt-6 pt-6 border-t border-gray-50 flex items-start gap-3">
                <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mt-0.5 shrink-0"></i>
                <div class="text-sm text-gray-600 space-y-1">
                    <?php if ($hasAlamat): ?>
                        <p><?= esc($donor['alamat']) ?></p>
                    <?php endif; ?>

                    <?php if ($hasRtRwKel): ?>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 font-medium">
                            <?php 
                                $parts = [];
                                if (!empty($donor['rt'])) {
                                    $parts[] = 'RT. ' . esc($donor['rt']);
                                }
                                if (!empty($donor['rw'])) {
                                    $parts[] = 'RW. ' . esc($donor['rw']);
                                }
                                if (!empty($donor['kelurahan'])) {
                                    $parts[] = 'Kel. ' . esc($donor['kelurahan']);
                                }
                            ?>

                            <?= implode(' <span class="text-gray-300 mx-1">•</span> ', $parts) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabel Riwayat Donasi -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 mx-4 md:mx-0">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-indigo-500"></i> Riwayat Pemasukan / Donasi
            </h3>
            
            <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                <span id="total-transaksi" class="text-xs font-semibold bg-blue-50 text-blue-600 px-3 py-1.5 rounded-xl">
                    Total Transaksi: <?= count($donations) ?>
                </span>
                <!-- Tombol Tambah Donasi Donatur di Atas Tabel -->
                <a href="<?= base_url('admin/donors/record/' . $donor['id_donatur']) ?>" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Donasi Donatur
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Tanggal & Kwitansi</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Klasifikasi / Skenario</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Metode & Status</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-right">Nominal (Rp)</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="history-table-body" class="text-gray-700 divide-y divide-gray-50">
                    <?= view('admin/donor/v_history_partial', [
                        'donations' => $donations,
                        'donor'     => $donor
                    ]) ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- =========================================================
     MODAL DETAIL RIWAYAT STATUS DONASI
========================================================= -->

<div
    id="historyModal"
    class="fixed inset-0 hidden"
    style="z-index: 2147483647;"
    aria-hidden="true"
>

    <!-- Overlay -->
    <div
        id="historyModalOverlay"
        class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
    ></div>


    <!-- Container -->
    <div class="relative min-h-screen flex items-center justify-center p-4">

        <!-- Modal -->
        <div
            id="historyModalContent"
            class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden transform transition-all"
        >

            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">

                <div>
                    <h3 class="font-bold text-gray-800 text-base">
                        Riwayat Status Donasi
                    </h3>

                    <p class="text-xs text-gray-500 mt-0.5">
                        Donatur:
                        <span
                            id="historyModalNama"
                            class="font-semibold text-gray-700"
                        >
                        </span>

                        <span class="mx-1">•</span>

                        <span
                            id="historyModalKwitansi"
                            class="font-mono"
                        >
                        </span>
                    </p>
                </div>


                <!-- Close -->
                <button
                    type="button"
                    id="closeHistoryModal"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

            </div>


            <!-- Body -->
            <div
                id="historyModalBody"
                class="p-6 overflow-y-auto max-h-[65vh]"
            >

                <!-- Diisi melalui JavaScript -->

            </div>


            <!-- Footer -->
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50 flex justify-end">

                <button
                    type="button"
                    id="closeHistoryModalFooter"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl text-xs transition-colors"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const $ = window.jQuery;
        const donorId = "<?= $donor['id_donatur'] ?? '' ?>";

        // =========================================================
        // MODAL DETAIL RIWAYAT STATUS DONASI
        // =========================================================

        const historyModal = $('#historyModal');
        const historyModalBody = $('#historyModalBody');


        // Format escape HTML
        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }

            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }


        // Render timeline history
        function renderHistory(items) {

            if (!items || items.length === 0) {

                historyModalBody.html(`
                    <div class="text-center py-8 text-gray-400 italic text-sm">
                        Belum ada riwayat status tercatat.
                    </div>
                `);

                return;
            }

            // History dari database DESC:
            // terbaru -> terlama
            // Balik menjadi:
            // terlama -> terbaru
            const orderedItems = [...items].reverse();

            let html = `
                <div class="overflow-x-auto pb-4">

                    <div class="min-w-max px-6 pt-4">

                        <!-- Timeline -->
                        <div class="relative">

                            <!-- Garis horizontal -->
                            <div class="absolute top-4 left-4 right-4 h-0.5 bg-purple-200"></div>

                            <div class="relative flex justify-between gap-8">
            `;

            orderedItems.forEach(function(h, index) {

                const status = escapeHtml(
                    h.status_donasi || 'Tidak Terdefinisi'
                );

                const waktu = escapeHtml(
                    h.waktu_formatted || h.waktu || '-'
                );

                const namaPengurus = escapeHtml(
                    h.nama_pengurus || '-'
                );

                html += `
                    <div class="relative w-40 flex-shrink-0 text-center">

                        <!-- Nomor / titik -->
                        <div class="relative z-10 mx-auto w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs font-bold shadow-sm border-2 border-white">
                            ${index + 1}
                        </div>


                        <!-- Status -->
                        <div class="mt-4">

                            <span
                                class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase leading-tight ${
                                    h.class_color || 'bg-gray-100 text-gray-700'
                                }"
                            >
                                ${status}
                            </span>

                        </div>


                        <!-- Tanggal -->
                        <div class="mt-3">

                            <p class="text-[11px] text-gray-400 font-mono leading-relaxed">
                                ${waktu}
                            </p>

                        </div>


                        <!-- Pengurus -->
                        <div class="mt-2">

                            <p class="text-[10px] text-gray-500 leading-relaxed">
                                Dicatat oleh
                            </p>

                            <p class="text-[11px] font-semibold text-gray-700 leading-relaxed mt-0.5">
                                ${namaPengurus}
                            </p>

                        </div>

                    </div>
                `;
            });

            html += `
                            </div>

                        </div>

                    </div>

                </div>
            `;

            historyModalBody.html(html);
        }


        // Buka modal
        $(document).on('click', '.btn-history', function(e) {

            e.preventDefault();


            const nama = $(this).attr('data-nama') || '-';

            const kwitansi =
                $(this).attr('data-kwitansi') ||
                'No. Kwitansi belum ada';


            let history = [];

            try {

                const rawHistory =
                    $(this).attr('data-history');

                history = rawHistory
                    ? JSON.parse(rawHistory)
                    : [];

            } catch (error) {

                console.error(
                    'Gagal membaca data history:',
                    error
                );

                history = [];
            }


            // Set header modal
            $('#historyModalNama').text(nama);

            $('#historyModalKwitansi').text(kwitansi);


            // Render timeline
            renderHistory(history);


            // Tampilkan modal
            historyModal.removeClass('hidden');

            $('body').addClass('overflow-hidden');


            // Refresh Lucide
            if (window.lucide) {
                lucide.createIcons({
                    icons: lucide.icons
                });
            }

        });


        // Tutup modal
        function closeHistoryModal() {

            historyModal.addClass('hidden');

            $('body').removeClass('overflow-hidden');

        }


        // Tombol X
        $(document).on(
            'click',
            '#closeHistoryModal',
            function() {
                closeHistoryModal();
            }
        );


        // Tombol Tutup
        $(document).on(
            'click',
            '#closeHistoryModalFooter',
            function() {
                closeHistoryModal();
            }
        );


        // Klik overlay
        $(document).on(
            'click',
            '#historyModalOverlay',
            function() {
                closeHistoryModal();
            }
        );


        // ESC
        $(document).on('keydown', function(e) {

            if (e.key === 'Escape') {
                closeHistoryModal();
            }

        });

        // Inisialisasi ikon Lucide
        if (window.lucide) {
            lucide.createIcons({
                icons: lucide.icons
            });
        }

        // Fungsi untuk memperbarui partial views riwayat via AJAX tanpa reload halaman
        function reloadPartials() {
            if (!donorId) return;
            
            $.ajax({
                url: "<?= base_url('admin/donors/detail/') ?>" + donorId,
                type: "GET",
                dataType: "html",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    const newBody = $(response).find('#history-table-body').html();
                    const newTotal = $(response).find('#total-transaksi').text();
                    
                    $('#history-table-body').html(newBody);
                    $('#total-transaksi').text(newTotal);

                    if (window.lucide) {
                        lucide.createIcons({
                            icons: lucide.icons
                        });
                    }
                },
                error: function() {
                    console.warn('Gagal memuat ulang data partial, melakukan reload halaman...');
                    location.reload();
                }
            });
        }

        // Script Tombol Hapus List Donatur / Donasi Donatur (Menggunakan Event Delegation)
        $(document).on('click', '.btn-delete-candidate', function(e) {
            e.preventDefault();
            
            const id = $(this).data('id');
            const info = $(this).data('info'); // Mengambil dari data-info

            if (!id) {
                console.error('ID Pemasukan Donasi tidak ditemukan.');
                return;
            }

            Swal.fire({
                title: 'Hapus Riwayat Donasi?',
                html: `Yakin ingin menghapus catatan:<br><b>"${info}"</b>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('admin/donation-incomes/delete') ?>",
                        type: "POST",
                        data: {
                            id_pemasukan_donasi: id,
                            "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                reloadPartials();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Gagal menghubungi server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-toggle-samarkan', function(e) {

            e.preventDefault();

            const button = $(this);

            const id = button.data('id');
            const currentSamarkan = button.data('samarkan');
            const nama = button.data('nama');

            if (!id) {
                console.error('ID Pemasukan Donasi tidak ditemukan.');
                return;
            }

            const isCurrentlyMasked = currentSamarkan === 'Y';

            const newSamarkan = isCurrentlyMasked ? 'N' : 'Y';

            const actionText = isCurrentlyMasked
                ? 'menampilkan'
                : 'menyamarkan';

            const confirmText = isCurrentlyMasked
                ? 'Nama donatur akan ditampilkan kembali.'
                : 'Nama donatur akan disamarkan menjadi Hamba Allah.';

            Swal.fire({
                title: isCurrentlyMasked
                    ? 'Tampilkan Nama Donatur?'
                    : 'Samarkan Nama Donatur?',

                html: `
                    Apakah Anda yakin ingin <b>${actionText}</b> nama donatur
                    <br>
                    <b>"${escapeHtml(nama)}"</b>?
                    <br><br>
                    <span class="text-sm text-gray-500">
                        ${confirmText}
                    </span>
                `,

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: isCurrentlyMasked
                    ? 'Ya, Tampilkan'
                    : 'Ya, Samarkan',

                cancelButtonText: 'Batal',

                confirmButtonColor: isCurrentlyMasked
                    ? '#4f46e5'
                    : '#d97706',

                cancelButtonColor: '#6b7280',

                reverseButtons: true
            }).then((result) => {

                if (!result.isConfirmed) {
                    return;
                }

                // Disable tombol agar tidak double click
                button.prop('disabled', true);

                $.ajax({

                    url: "<?= base_url('admin/donation-incomes/toggle-samarkan') ?>",

                    type: "POST",

                    dataType: "json",

                    data: {

                        id_pemasukan_donasi: id,

                        samarkan: newSamarkan,

                        "<?= csrf_token() ?>":
                            "<?= csrf_hash() ?>"
                    },

                    success: function(res) {

                        if (res.status === 'success') {

                            Swal.fire({

                                icon: 'success',

                                title: 'Berhasil!',

                                text: res.message,

                                timer: 1200,

                                showConfirmButton: false

                            }).then(() => {

                                reloadPartials();

                            });

                        } else {

                            Swal.fire({

                                icon: 'error',

                                title: 'Gagal',

                                text: res.message ||
                                    'Gagal mengubah status penyamaran.'

                            });

                            button.prop('disabled', false);
                        }
                    },

                    error: function(xhr) {

                        let errorMsg =
                            'Gagal menghubungi server.';

                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {

                            errorMsg =
                                xhr.responseJSON.message;
                        }

                        console.error(
                            'ERROR TOGGLE SAMARKAN:',
                            xhr.responseText
                        );

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: errorMsg

                        });

                        button.prop('disabled', false);
                    }

                });

            });

        });
    });
</script>
<?= $this->endSection() ?>