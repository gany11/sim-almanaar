<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10 space-y-6" id="donation-detail-wrapper">
    <!-- Header -->
    <div class="flex justify-between items-center px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $donation['judul'] ?></h2>
            <p class="text-sm text-gray-500">Akronim Kwitansi: <b class="font-mono text-blue-600"><?= $donation['akronim_kwitansi'] ?></b></p>
        </div>
        <a href="<?= base_url('admin/donations') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
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

    <!-- Banner Keterangan Jika closed_at terisi -->
    <?php if (!empty($donation['closed_at'])): ?>
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3 mx-4 md:mx-0 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-800">Program Donasi Ini Telah Ditutup & Dikunci Secara Finansial</h4>
                <p class="text-xs text-amber-700 mt-0.5">
                    Total dana pemasukan dan pengeluaran sudah dikunci permanen pada tanggal: <b><?= format_indo($donation['closed_at'], 'full_datetime') ?></b>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ringkasan Info Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mx-4 md:mx-0">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">Total Pemasukan</p>
                <h4 class="text-lg font-bold text-gray-800" id="card-total-pemasukan">Rp <?= number_format($donation['total_pemasukan'] ?? 0, 0, ',', '.') ?></h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">Total Pengeluaran</p>
                <h4 class="text-lg font-bold text-gray-800" id="card-total-pengeluaran">Rp <?= number_format($donation['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">Saldo Akhir</p>
                <h4 class="text-lg font-bold text-blue-600" id="card-saldo-akhir">Rp <?= number_format(($donation['total_pemasukan'] ?? 0) - ($donation['total_pengeluaran'] ?? 0), 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Panggil Partials -->
    <div id="wrapper-incomes">
        <?= view('admin/donation/partials/v_incomes', ['incomes' => $incomes]) ?>
    </div>

    <div id="wrapper-candidates">
        <?= view('admin/donation/partials/v_candidate_donors', [
            'candidateDonors' => $candidateDonors, 
            'donation' => $donation, 
            'statusCounts' => $statusCounts ?? []
        ]) ?>
    </div>

    <div id="wrapper-expenses">
        <?= view('admin/donation/partials/v_expenses', ['expenses' => $expenses, 'donation' => $donation]) ?>
    </div>

    <!-- GLOBAL MODALS -->
    <?= view('admin/donation/partials/v_history_modal') ?>


    <?= view('admin/donation/partials/v_update_status_modal') ?>
</div>

<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('historyModal', () => ({

            open: false,

            nama: '',

            kwitansi: '',

            items: [],


            get orderedItems() {
                // Database diasumsikan DESC:
                // terbaru -> terlama
                //
                // Timeline ditampilkan:
                // terlama -> terbaru

                return [...this.items].reverse();
            }

        }));

    });
</script>

<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('updateStatusModal', () => ({

        open: false,

        updateId: null,

        updateName: '',

        allowedStatuses: [],

        pengurusList: <?= json_encode(
            array_values(
                array_filter(
                    array_map(
                        fn($p) => trim($p['nama_pengurus'] ?? ''),
                        $pengurusList ?? []
                    )
                )
            ),
            JSON_UNESCAPED_UNICODE |
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_QUOT |
            JSON_HEX_AMP
        ) ?>,

        form: {
            id_status_donasi: '',
            waktu: '',
            nama_pengurus: ''
        },

        async init() {

            window.addEventListener(
                'open-update-status',
                (event) => {

                    this.updateId = event.detail.id;
                    this.updateName = event.detail.name;

                    this.openModal();

                }
            );

        },

        async openModal() {

            this.form = {
                id_status_donasi: '',
                waktu: '',
                nama_pengurus: '<?= esc(
                    session()->get('nama') ?? 'Administrator',
                    'js'
                ) ?>'
            };

            this.allowedStatuses = [];

            try {

                const response = await fetch(
                    "<?= base_url('admin/donation-donors/get-status-options/') ?>" +
                    this.updateId,
                    {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }
                );

                const data = await response.json();

                if (!response.ok || data.status !== 'success') {

                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: data.message ||
                            'Tidak dapat memuat data status.'
                    });

                    return;
                }

                this.allowedStatuses =
                    data.allowed_statuses || [];

                this.form.waktu =
                    data.default_waktu || '';

                this.open = true;

                this.$nextTick(() => {
                    this.initPengurusSelect();
                });

            } catch (error) {

                console.error(error);

                Swal.fire(
                    'Error',
                    'Gagal menghubungi server.',
                    'error'
                );
            }
        },

        initPengurusSelect() {

            const select = $('#select-pengurus');

            if (!select.length || !$.fn.select2) {
                return;
            }

            if (select.hasClass('select2-hidden-accessible')) {
                select.select2('destroy');
            }

            select.empty();

            this.pengurusList.forEach(nama => {

                if (!nama) return;

                select.append(
                    new Option(
                        nama,
                        nama,
                        false,
                        nama === this.form.nama_pengurus
                    )
                );

            });

            select.select2({
                tags: true,
                placeholder:
                    'Pilih atau ketik nama pengurus...',
                allowClear: true,
                width: '100%',
                dropdownParent:
                    $('#modal-update-status')
            });

            select
                .off('change.updateStatus')
                .on('change.updateStatus', () => {

                    this.form.nama_pengurus =
                        select.val() || '';

                });

            select
                .val(this.form.nama_pengurus)
                .trigger('change');

        },

        close() {

            this.open = false;

            const select = $('#select-pengurus');

            if (
                select.length &&
                select.hasClass('select2-hidden-accessible')
            ) {
                select.select2('destroy');
            }

        },

        async submit() {

            if (!this.form.id_status_donasi) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih status baru terlebih dahulu.'
                });
                return;
            }

            // Jika status 4 = Pencatatan Dana
            if (Number(this.form.id_status_donasi) === 4) {

                window.location.href =
                    "<?= base_url('admin/donation-incomes/record/') ?>" +
                    this.updateId;

                return;
            }

            const formData = new FormData();

            formData.append(
                'id_status_donasi',
                this.form.id_status_donasi
            );

            formData.append(
                'waktu',
                this.form.waktu || ''
            );

            formData.append(
                'nama_pengurus',
                this.form.nama_pengurus || ''
            );

            formData.append(
                "<?= csrf_token() ?>",
                "<?= csrf_hash() ?>"
            );

            try {

                const response = await fetch(
                    "<?= base_url('admin/donation-donors/update-status/') ?>" +
                    this.updateId,
                    {
                        method: 'POST',

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },

                        body: formData
                    }
                );

                const responseText = await response.text();

                console.log('HTTP STATUS:', response.status);
                console.log('RESPONSE SERVER:', responseText);

                let data;

                try {

                    data = JSON.parse(responseText);

                } catch (jsonError) {

                    console.error(
                        'Response bukan JSON:',
                        responseText
                    );

                    this.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'Response Server Tidak Valid',
                        text: 'Server tidak mengembalikan response JSON yang valid.'
                    });

                    return;
                }


                // ==========================================
                // SERVER ERROR
                // ==========================================

                if (!response.ok || data.status !== 'success') {

                    this.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message ||
                            'Gagal memperbarui status.'
                    });

                    return;
                }


                // ==========================================
                // SUCCESS
                // ==========================================

                this.close();

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message ||
                        'Status donasi berhasil diperbarui.',
                    timer: 1200,
                    showConfirmButton: false
                });

                // Reload partial
                if (typeof window.reloadPartials === 'function') {
                    window.reloadPartials();
                } else {
                    console.error('window.reloadPartials() tidak tersedia.');
                }

            } catch (error) {

                console.error(
                    'ERROR UPDATE STATUS:',
                    error
                );

                this.close();

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghubungi server.'
                });
            }
        }

    }));

});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const $ = window.jQuery;
        const donationId = "<?= $donation['id_donasi'] ?>";

        // Inisialisasi ikon Lucide
        if (window.lucide) {
            lucide.createIcons({
                icons: lucide.icons
            });
        }

        // ==========================================
        // RELOAD PARTIALS
        // ==========================================

        window.reloadPartials = function() {

            $.ajax({

                url:
                    "<?= base_url('admin/donations/partial-detail/') ?>" +
                    donationId,

                type: "GET",

                dataType: "json",

                success: function(res) {

                    console.log('PARTIAL RELOAD:', res);

                    $('#wrapper-incomes')
                        .html(res.incomesHtml);

                    $('#wrapper-candidates')
                        .html(res.candidatesHtml);

                    $('#wrapper-expenses')
                        .html(res.expensesHtml);

                    $('#card-total-pemasukan')
                        .text(res.totalPemasukan);

                    $('#card-total-pengeluaran')
                        .text(res.totalPengeluaran);

                    $('#card-saldo-akhir')
                        .text(res.saldoAkhir);


                    // Recreate Lucide
                    if (window.lucide) {

                        lucide.createIcons({
                            icons: lucide.icons
                        });

                    }
                },

                error: function(xhr) {

                    console.error(
                        'GAGAL RELOAD PARTIAL:',
                        xhr.status,
                        xhr.responseText
                    );

                }

            });

        };

        // 1. Script Tombol Hapus Pengeluaran (Menggunakan Event Delegation)
        $(document).on('click', '.btn-delete-expense', function() {
            const id = $(this).data('id');
            const ket = $(this).data('keterangan');

            window.Swal.fire({
                title: 'Hapus Pengeluaran?',
                html: `Yakin ingin menghapus catatan pengeluaran:<br><b>"${ket}"</b>`,
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
                        url: "<?= base_url('admin/donation-expenses/delete') ?>",
                        type: "POST",
                        data: {
                            id_pengeluaran_donasi: id,
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
                        error: function() {
                            Swal.fire(
                                'Error', 'Gagal menghubungi server.', 'error'
                            ).then(() => {
                                if (typeof reloadPartials === 'function') {
                                    reloadPartials();
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            });
        });

        // 2. Script Tombol Hapus Income / Pemasukan Donasi (Baru ditambahkan)
        $(document).on('click', '.btn-delete-income', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            window.Swal.fire({
                title: 'Hapus Pemasukan Donasi?',
                html: `Yakin ingin menghapus data pemasukan dari:<br><b>"${nama}"</b>`,
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
                        error: function() {
                            Swal.fire(
                                'Error', 'Gagal menghubungi server.', 'error'
                            ).then(() => {
                                if (typeof reloadPartials === 'function') {
                                    reloadPartials();
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            });
        });

        // 3. Script Tombol Hapus List Donatur / Calon Donatur (Menggunakan Event Delegation)
        $(document).on('click', '.btn-delete-candidate', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            window.Swal.fire({
                title: 'Hapus List Donatur?',
                html: `Yakin ingin menghapus data donatur:<br><b>"${nama}"</b>`,
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
                        error: function() {
                            Swal.fire(
                                'Error', 'Gagal menghubungi server.', 'error'
                            ).then(() => {
                                if (typeof reloadPartials === 'function') {
                                    reloadPartials();
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            });
        });

        // =========================================================
        // TOGGLE SAMARKAN NOMINAL PEMASUKAN DONASI
        // =========================================================

        function escapeHtml(text) {
            return $('<div>').text(text ?? '').html();
        }

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

            // ==========================================
            // Tentukan status saat ini
            // ==========================================

            const isCurrentlyMasked = currentSamarkan === 'Y';

            // Jika Y -> N
            // Jika N -> Y
            const newSamarkan = isCurrentlyMasked ? 'N' : 'Y';

            const actionText = isCurrentlyMasked
                ? 'menampilkan'
                : 'menyamarkan';

            const confirmText = isCurrentlyMasked
                ? 'Nama donatur akan ditampilkan kembali.'
                : 'Nama donatur akan disamarkan menjadi Hamba Allah.';


            // ==========================================
            // KONFIRMASI
            // ==========================================

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


                // ==========================================
                // DISABLE TOMBOL
                // ==========================================

                button.prop('disabled', true);


                // ==========================================
                // AJAX
                // ==========================================

                $.ajax({

                    url: "<?= base_url('admin/donation-incomes/toggle-samarkan') ?>",

                    type: "POST",

                    dataType: "json",

                    data: {

                        id_pemasukan_donasi: id,

                        // Kirim status BARU
                        samarkan: newSamarkan,

                        "<?= csrf_token() ?>":
                            "<?= csrf_hash() ?>"
                    },


                    // ======================================
                    // SUCCESS
                    // ======================================

                    success: function(res) {

                        if (res.status === 'success') {

                            Swal.fire({

                                icon: 'success',

                                title: 'Berhasil!',

                                text: res.message,

                                timer: 1200,

                                showConfirmButton: false

                            }).then(() => {

                                // Refresh partial v_incomes
                                // agar nama/icon/status langsung berubah
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


                    // ======================================
                    // ERROR
                    // ======================================

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
                            xhr.status,
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
<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('candidateTable', () => ({

            selectedFilter: 'all',
            searchQuery: '',

            // Data nama pengurus dari Controller detail()
            pengurusList: <?= json_encode(
                array_values(
                    array_filter(
                        array_map(
                            fn($p) => trim($p['nama_pengurus'] ?? ''),
                            $pengurusList ?? []
                        )
                    )
                ),
                JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
            ) ?>,

            // ==========================================
            // FILTER
            // ==========================================
            matchesFilter(status, text) {
                const matchStatus =
                    this.selectedFilter === 'all' ||
                    status === this.selectedFilter;

                const matchSearch =
                    text.toLowerCase()
                        .includes(this.searchQuery.toLowerCase());

                return matchStatus && matchSearch;
            },
        }));

    });
</script>
<?= $this->endSection() ?>