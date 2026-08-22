<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-700">
            WhatsApp Gateway
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Kelola koneksi WhatsApp Gateway sistem.
        </p>
    </div>


    <div
        id="status-card"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
    >

        <div class="flex items-center justify-between mb-6">

            <div>
                <h3 class="text-lg font-bold text-gray-700">
                    Status WhatsApp
                </h3>

                <p
                    id="status-text"
                    class="text-sm text-gray-500 mt-1"
                >
                    Memeriksa koneksi...
                </p>
            </div>

            <div id="status-badge">

                <span
                    class="px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500"
                >
                    Memuat
                </span>

            </div>

        </div>


        <!-- NOMOR WHATSAPP -->

        <div
            id="phone-container"
            class="hidden mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100"
        >

            <div class="flex items-center gap-3">

                <div
                    class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center"
                >
                    <i
                        data-lucide="smartphone"
                        class="w-5 h-5"
                    ></i>
                </div>

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-widest text-blue-500"
                    >
                        Nomor WhatsApp
                    </p>

                    <p
                        id="phone-number"
                        class="text-lg font-bold text-gray-700"
                    >
                        -
                    </p>

                </div>

            </div>

        </div>


        <!-- QR -->

        <div
            id="qr-container"
            class="hidden text-center"
        >

            <div class="mb-4">

                <h3 class="font-bold text-gray-700">
                    Hubungkan WhatsApp
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Scan QR menggunakan WhatsApp
                </p>

            </div>

            <div
                class="inline-flex p-4 bg-white border border-gray-200 rounded-2xl shadow-sm"
            >

                <img
                    id="qr-image"
                    src=""
                    alt="WhatsApp QR"
                    class="w-[300px] h-[300px]"
                >

            </div>

            <p
                id="qr-countdown"
                class="mt-4 text-sm font-semibold text-gray-500"
            >
                QR berlaku sekitar 50 detik
            </p>

        </div>


        <!-- DISCONNECTED -->

        <div
            id="disconnected-container"
            class="hidden text-center py-10"
        >

            <div class="flex justify-center mb-4">

                <div
                    class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center"
                >

                    <i
                        data-lucide="wifi-off"
                        class="w-8 h-8"
                    ></i>

                </div>

            </div>

            <h3 class="font-bold text-gray-700">
                Belum Terhubung
            </h3>

            <p class="text-sm text-gray-500 mt-1 mb-6">
                WhatsApp belum terhubung atau QR belum tersedia.
            </p>


            <!-- REFRESH STATUS -->

            <button
                type="button"
                id="btn-refresh-status"
                class="mx-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all flex items-center gap-2"
            >

                <i
                    data-lucide="refresh-cw"
                    class="w-4 h-4"
                ></i>

                <span>Refresh Status</span>

            </button>

        </div>


        <!-- LOGOUT -->

        <div
            id="logout-container"
            class="hidden mt-6 pt-6 border-t border-gray-100"
        >

            <button
                type="button"
                id="btn-logout"
                class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm transition-all flex items-center gap-2"
            >

                <i
                    data-lucide="log-out"
                    class="w-4 h-4"
                ></i>

                Logout WhatsApp

            </button>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const statusUrl =
        "<?= base_url('admin/whatsapp/status') ?>";

    const qrUrl =
        "<?= base_url('admin/whatsapp/qr') ?>";

    const logoutUrl =
        "<?= base_url('admin/whatsapp/logout') ?>";


    let countdownTimer = null;

    let currentQR = null;


    /*
     * Helper
     */

    function show(selector) {

        document
            .querySelector(selector)
            ?.classList
            .remove('hidden');

    }


    function hide(selector) {

        document
            .querySelector(selector)
            ?.classList
            .add('hidden');

    }


    function setLoading(button, loading) {

        if (!button) {
            return;
        }

        button.disabled = loading;

        if (loading) {

            button.classList.add(
                'opacity-70',
                'cursor-not-allowed'
            );

        } else {

            button.classList.remove(
                'opacity-70',
                'cursor-not-allowed'
            );

        }

    }


    /*
     * STATUS BADGE
     */

    function setStatusBadge(connected) {

        const badge =
            document.getElementById('status-badge');


        if (connected) {

            badge.innerHTML = `
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                    Terhubung
                </span>
            `;

        } else {

            badge.innerHTML = `
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                    Tidak Terhubung
                </span>
            `;

        }

    }


    /*
     * FORMAT NOMOR
     *
     * 6281234567890
     * menjadi
     * 0812 3456 7890
     */

    function formatPhone(phone) {

        if (!phone) {
            return '-';
        }


        phone = String(phone);


        if (phone.startsWith('62')) {

            phone =
                '0' +
                phone.substring(2);

        }


        const groups =
            phone.match(/.{1,4}/g);


        return groups
            ? groups.join(' ')
            : phone;

    }


    /*
     * STOP COUNTDOWN
     */

    function stopCountdown() {

        if (countdownTimer) {

            clearInterval(
                countdownTimer
            );

            countdownTimer = null;

        }

    }


    /*
     * START COUNTDOWN
     */

    function startCountdown(seconds) {

        stopCountdown();


        let remaining = seconds;


        const element =
            document.getElementById(
                'qr-countdown'
            );


        element.textContent =
            `QR berlaku sekitar ${remaining} detik`;


        countdownTimer =
            setInterval(async () => {

                remaining--;


                if (remaining <= 0) {

                    stopCountdown();


                    element.textContent =
                        'QR telah kedaluwarsa. Memeriksa status...';


                    /*
                     * Jangan langsung
                     * start countdown baru.
                     *
                     * Cek status Node.js.
                     */

                    await loadStatus();

                    return;

                }


                element.textContent =
                    `QR berlaku sekitar ${remaining} detik`;

            }, 1000);

    }


    /*
     * LOAD QR
     */

    async function loadQR() {

        try {

            const response =
                await fetch(
                    qrUrl,
                    {
                        cache: 'no-store'
                    }
                );


            const result =
                await response.json();


            if (
                result.success &&
                result.qr
            ) {

                /*
                 * Hindari QR yang sama
                 * memulai countdown ulang.
                 */

                if (
                    currentQR !== result.qr
                ) {

                    currentQR =
                        result.qr;


                    document
                        .getElementById(
                            'qr-image'
                        )
                        .src =
                        result.qr;


                    show('#qr-container');

                    hide(
                        '#disconnected-container'
                    );


                    startCountdown(50);

                }

                return true;

            }


            /*
             * QR tidak tersedia
             */

            currentQR = null;

            hide('#qr-container');

            show(
                '#disconnected-container'
            );


            return false;

        } catch (error) {

            console.error(
                'QR error:',
                error
            );

            return false;

        }

    }


    /*
     * LOAD STATUS
     */

    async function loadStatus() {

        try {

            const response =
                await fetch(
                    statusUrl,
                    {
                        cache: 'no-store'
                    }
                );


            const result =
                await response.json();


            if (!result.success) {

                return;

            }


            const data =
                result.data;


            /*
             * TERHUBUNG
             */

            if (data.connected) {

                stopCountdown();

                currentQR = null;


                hide(
                    '#qr-container'
                );


                hide(
                    '#disconnected-container'
                );


                show(
                    '#logout-container'
                );


                show(
                    '#phone-container'
                );


                document
                    .getElementById(
                        'phone-number'
                    )
                    .textContent =
                    formatPhone(
                        data.phone
                    );


                document
                    .getElementById(
                        'status-text'
                    )
                    .textContent =
                    'WhatsApp sedang terhubung.';


                setStatusBadge(true);


                return;

            }


            /*
             * DISCONNECTED
             */

            hide(
                '#logout-container'
            );


            hide(
                '#phone-container'
            );


            document
                .getElementById(
                    'status-text'
                )
                .textContent =
                'WhatsApp belum terhubung.';


            setStatusBadge(false);


            /*
             * QR TERSEDIA
             */

            if (data.qrAvailable) {

                await loadQR();

                return;

            }


            /*
             * QR TIDAK TERSEDIA
             */

            stopCountdown();

            currentQR = null;


            hide(
                '#qr-container'
            );


            show(
                '#disconnected-container'
            );

        } catch (error) {

            console.error(
                'Status error:',
                error
            );

        }

    }


    /*
     * REFRESH STATUS MANUAL
     */

    document
        .getElementById(
            'btn-refresh-status'
        )
        .addEventListener(
            'click',
            async function () {

                const button = this;


                setLoading(
                    button,
                    true
                );


                const originalHTML =
                    button.innerHTML;


                button.innerHTML = `
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    Memeriksa...
                `;


                if (window.reinitIcons) {
                    window.reinitIcons();
                }


                try {

                    await loadStatus();


                    /*
                     * Beri sedikit jeda
                     * supaya perubahan UI
                     * terlihat jelas.
                     */

                    await new Promise(
                        resolve =>
                            setTimeout(
                                resolve,
                                300
                            )
                    );


                } finally {

                    button.innerHTML =
                        originalHTML;


                    setLoading(
                        button,
                        false
                    );


                    if (window.reinitIcons) {
                        window.reinitIcons();
                    }

                }

            }
        );


    /*
     * LOGOUT
     */

    document
        .getElementById(
            'btn-logout'
        )
        .addEventListener(
            'click',
            async function () {

                const button = this;


                const result =
                    await Swal.fire({

                        title:
                            'Logout WhatsApp?',

                        text:
                            'Perangkat WhatsApp akan diputus dan Anda perlu menautkan kembali menggunakan QR.',

                        icon:
                            'warning',

                        showCancelButton:
                            true,

                        confirmButtonColor:
                            '#dc2626',

                        cancelButtonColor:
                            '#6b7280',

                        confirmButtonText:
                            'Ya, Logout',

                        cancelButtonText:
                            'Batal',

                        reverseButtons:
                            true

                    });


                if (!result.isConfirmed) {

                    return;

                }


                setLoading(
                    button,
                    true
                );


                try {

                    const response =
                        await fetch(
                            logoutUrl,
                            {
                                method: 'POST',

                                headers: {

                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                    '<?= csrf_header() ?>':
                                        '<?= csrf_hash() ?>'

                                }
                            }
                        );


                    const data =
                        await response.json();


                    if (
                        !response.ok ||
                        !data.success
                    ) {

                        throw new Error(
                            data.message ||
                            'Gagal logout WhatsApp.'
                        );

                    }


                    stopCountdown();

                    currentQR = null;


                    await Swal.fire({

                        icon:
                            'success',

                        title:
                            'Logout Berhasil',

                        text:
                            'WhatsApp berhasil diputus. Sistem akan mencari QR baru.',

                        timer:
                            1800,

                        showConfirmButton:
                            false

                    });


                    /*
                     * Setelah logout,
                     * Node.js perlu waktu
                     * membuat QR baru.
                     *
                     * Cek beberapa kali.
                     */

                    let foundQR = false;


                    for (
                        let i = 0;
                        i < 10;
                        i++
                    ) {

                        await new Promise(
                            resolve =>
                                setTimeout(
                                    resolve,
                                    1000
                                )
                        );


                        await loadStatus();


                        const qrContainer =
                            document
                                .getElementById(
                                    'qr-container'
                                );


                        if (
                            !qrContainer
                                .classList
                                .contains(
                                    'hidden'
                                )
                        ) {

                            foundQR = true;

                            break;

                        }

                    }


                    if (!foundQR) {

                        await Swal.fire({

                            icon:
                                'info',

                            title:
                                'QR Belum Tersedia',

                            text:
                                'Silakan tekan Refresh Status untuk memeriksa kembali.',

                            confirmButtonText:
                                'OK'

                        });

                    }


                } catch (error) {

                    console.error(
                        'Logout error:',
                        error
                    );


                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Logout Gagal',

                        text:
                            error.message ||
                            'Gagal menghubungi WhatsApp Gateway.'

                    });

                } finally {

                    setLoading(
                        button,
                        false
                    );

                }

            }
        );


    /*
     * CEK PERTAMA
     */

    loadStatus();


    /*
     * POLLING STATUS
     *
     * Setiap 60 detik.
     */

    setInterval(
        loadStatus,
        60000
    );


    /*
     * Inisialisasi icon
     */

    if (window.reinitIcons) {

        window.reinitIcons();

    } else if (
        typeof lucide !== 'undefined'
    ) {

        lucide.createIcons();

    }

});

</script>

<?= $this->endSection() ?>