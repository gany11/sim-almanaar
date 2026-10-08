<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto pb-10">

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="mb-6">

        <h2 class="text-2xl font-bold text-gray-700">
            WhatsApp Gateway
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Kelola koneksi WhatsApp Gateway, grup, dan antrean pesan.
        </p>

    </div>


    <!-- ===================================================== -->
    <!-- STATUS CARD -->
    <!-- ===================================================== -->

    <div
        id="status-card"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
    >

        <!-- ================================================= -->
        <!-- HEADER STATUS -->
        <!-- ================================================= -->

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


        <!-- ================================================= -->
        <!-- NOMOR WHATSAPP -->
        <!-- ================================================= -->

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


        <!-- ================================================= -->
        <!-- STATUS PESAN / QUEUE -->
        <!-- ================================================= -->

        <div class="mb-6">

            <div class="flex items-center justify-between mb-3">

                <div>

                    <h3 class="text-base font-bold text-gray-700">
                        Status Pesan
                    </h3>

                    <p class="text-xs text-gray-400 mt-0.5">
                        Status antrean pesan WhatsApp
                    </p>

                </div>


                <div
                    id="queue-last-update"
                    class="text-xs text-gray-400"
                >
                    Memuat...
                </div>

            </div>


            <!-- ================================================= -->
            <!-- QUEUE GRID -->
            <!-- ================================================= -->

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">

                <!-- PENDING -->

                <div
                    class="rounded-xl border border-amber-100 bg-amber-50 p-4"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-amber-600"
                            >
                                Pending
                            </p>

                            <p
                                id="queue-pending"
                                class="text-2xl font-bold text-amber-700 mt-1"
                            >
                                0
                            </p>

                        </div>


                        <div
                            class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center"
                        >

                            <i
                                data-lucide="clock-3"
                                class="w-4 h-4"
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- PROCESSING -->

                <div
                    class="rounded-xl border border-blue-100 bg-blue-50 p-4"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-blue-600"
                            >
                                Processing
                            </p>

                            <p
                                id="queue-processing"
                                class="text-2xl font-bold text-blue-700 mt-1"
                            >
                                0
                            </p>

                        </div>


                        <div
                            class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"
                        >

                            <i
                                data-lucide="loader"
                                class="w-4 h-4"
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- SENT -->

                <div
                    class="rounded-xl border border-emerald-100 bg-emerald-50 p-4"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-emerald-600"
                            >
                                Sent
                            </p>

                            <p
                                id="queue-sent"
                                class="text-2xl font-bold text-emerald-700 mt-1"
                            >
                                0
                            </p>

                        </div>


                        <div
                            class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"
                        >

                            <i
                                data-lucide="check-circle"
                                class="w-4 h-4"
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- FAILED -->

                <div
                    class="rounded-xl border border-red-100 bg-red-50 p-4"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-red-600"
                            >
                                Failed
                            </p>

                            <p
                                id="queue-failed"
                                class="text-2xl font-bold text-red-700 mt-1"
                            >
                                0
                            </p>

                        </div>


                        <div
                            class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center"
                        >

                            <i
                                data-lucide="x-circle"
                                class="w-4 h-4"
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- TOTAL -->

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 p-4"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-gray-500"
                            >
                                Total
                            </p>

                            <p
                                id="queue-total"
                                class="text-2xl font-bold text-gray-700 mt-1"
                            >
                                0
                            </p>

                        </div>


                        <div
                            class="w-9 h-9 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center"
                        >

                            <i
                                data-lucide="messages-square"
                                class="w-4 h-4"
                            ></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- DAFTAR GRUP WHATSAPP -->
        <!-- ================================================= -->

        <div
            id="groups-container"
            class="hidden mb-6 pt-6 border-t border-gray-100"
        >

            <!-- GROUP HEADER -->

            <div class="flex items-center justify-between mb-4">

                <div>

                    <h3 class="text-base font-bold text-gray-700">
                        Grup WhatsApp
                    </h3>

                    <p class="text-xs text-gray-400 mt-0.5">
                        Daftar grup yang diikuti oleh akun WhatsApp.
                    </p>

                </div>


                <div class="flex items-center gap-2">

                    <span
                        id="groups-total"
                        class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold"
                    >
                        0 Grup
                    </span>


                    <button
                        type="button"
                        id="btn-refresh-groups"
                        class="w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition-all flex items-center justify-center"
                        title="Refresh grup"
                    >

                        <i
                            data-lucide="refresh-cw"
                            class="w-4 h-4"
                        ></i>

                    </button>

                </div>

            </div>


            <!-- GROUP LOADING -->

            <div
                id="groups-loading"
                class="hidden text-center py-8 text-sm text-gray-400"
            >

                <div class="flex justify-center mb-3">

                    <i
                        data-lucide="loader-2"
                        class="w-6 h-6 animate-spin"
                    ></i>

                </div>

                Memuat daftar grup...

            </div>


            <!-- GROUP ERROR -->

            <div
                id="groups-error"
                class="hidden rounded-xl border border-red-100 bg-red-50 p-4"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0"
                    >

                        <i
                            data-lucide="alert-circle"
                            class="w-4 h-4"
                        ></i>

                    </div>


                    <div>

                        <p class="text-sm font-bold text-red-700">
                            Gagal memuat grup
                        </p>

                        <p
                            id="groups-error-text"
                            class="text-xs text-red-600 mt-1"
                        >
                            -
                        </p>

                    </div>

                </div>

            </div>


            <!-- GROUP EMPTY -->

            <div
                id="groups-empty"
                class="hidden text-center py-8"
            >

                <div class="flex justify-center mb-3">

                    <div
                        class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center"
                    >

                        <i
                            data-lucide="messages-square"
                            class="w-6 h-6"
                        ></i>

                    </div>

                </div>


                <p class="text-sm font-semibold text-gray-500">
                    Tidak ada grup WhatsApp.
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Akun WhatsApp belum tergabung dalam grup.
                </p>

            </div>


            <!-- GROUP LIST -->

            <div
                id="groups-list"
                class="hidden grid grid-cols-1 md:grid-cols-2 gap-3"
            ></div>

        </div>


        <!-- ================================================= -->
        <!-- QR CODE -->
        <!-- ================================================= -->

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


        <!-- ================================================= -->
        <!-- DISCONNECTED -->
        <!-- ================================================= -->

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


            <button
                type="button"
                id="btn-refresh-status"
                class="mx-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all flex items-center gap-2"
            >

                <i
                    data-lucide="refresh-cw"
                    class="w-4 h-4"
                ></i>

                <span>
                    Refresh Status
                </span>

            </button>

        </div>


        <!-- ================================================= -->
        <!-- LOGOUT -->
        <!-- ================================================= -->

        <?php if (can_access('whatsapp.logout')): ?>
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
        <?php endif; ?>
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        * =====================================================
        * URL CONFIGURATION
        * =====================================================
        */
        const statusUrl = "<?= base_url('admin/whatsapp/status') ?>";
        const qrUrl = "<?= base_url('admin/whatsapp/qr') ?>";
        const logoutUrl = "<?= base_url('admin/whatsapp/logout') ?>";
        const groupsUrl = "<?= base_url('admin/whatsapp/groups') ?>";

        /*
        * =====================================================
        * STATE MANAGEMENT
        * =====================================================
        */
        let statusPolling = null;
        let queuePolling = null;
        let isLoadingStatus = false;
        let isLoadingQueue = false;
        let currentConnected = false;
        let currentPending = 0;

        let countdownTimer = null;
        let currentQR = null;
        let groupsLoaded = false;

        /*
        * =====================================================
        * UI HELPERS
        * =====================================================
        */
        function show(selector) {
            document.querySelector(selector)?.classList.remove('hidden');
        }

        function hide(selector) {
            document.querySelector(selector)?.classList.add('hidden');
        }

        function setLoading(button, loading) {
            if (!button) return;
            button.disabled = loading;
            if (loading) {
                button.classList.add('opacity-70', 'cursor-not-allowed');
            } else {
                button.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }

        function refreshIcons() {
            if (window.reinitIcons) {
                window.reinitIcons();
            } else if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function setStatusBadge(status, connected) {
            const badge = document.getElementById('status-badge');
            if (!badge) return;

            if (connected) {
                badge.innerHTML = `<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Terhubung</span>`;
                return;
            }

            if (status === 'connecting') {
                badge.innerHTML = `<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Menghubungkan</span>`;
                return;
            }

            badge.innerHTML = `<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Tidak Terhubung</span>`;
        }

        function formatPhone(phone) {
            if (!phone) return '-';
            phone = String(phone);
            if (phone.startsWith('62')) {
                phone = '0' + phone.substring(2);
            }
            const groups = phone.match(/.{1,4}/g);
            return groups ? groups.join(' ') : phone;
        }

        /*
        * =====================================================
        * QR COUNTDOWN
        * =====================================================
        */
        function stopCountdown() {
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
        }

        function startCountdown(seconds) {
            stopCountdown();
            let remaining = seconds;
            const element = document.getElementById('qr-countdown');
            if (!element) return;

            element.textContent = `QR berlaku sekitar ${remaining} detik`;

            countdownTimer = setInterval(function () {
                remaining--;
                if (remaining <= 0) {
                    stopCountdown();
                    element.textContent = 'QR telah kedaluwarsa. Menunggu status...';
                    return;
                }
                element.textContent = `QR berlaku sekitar ${remaining} detik`;
            }, 1000);
        }

        /*
        * =====================================================
        * QUEUE MANAGEMENT
        * =====================================================
        */
        function updateQueue(queue) {
            if (!queue) {
                queue = { pending: 0, processing: 0, sent: 0, failed: 0, total: 0 };
            }

            const pending = Number(queue.pending || 0);
            const processing = Number(queue.processing || 0);
            const sent = Number(queue.sent || 0);
            const failed = Number(queue.failed || 0);
            const total = Number(queue.total || 0);

            currentPending = pending;

            document.getElementById('queue-pending').textContent = pending;
            document.getElementById('queue-processing').textContent = processing;
            document.getElementById('queue-sent').textContent = sent;
            document.getElementById('queue-failed').textContent = failed;
            document.getElementById('queue-total').textContent = total;

            const now = new Date();
            document.getElementById('queue-last-update').textContent = 'Diperbarui ' + now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }

        /*
        * =====================================================
        * GROUP UTILITIES & RENDERING
        * =====================================================
        */
        function escapeHtml(value) {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function clearGroups() {
            groupsLoaded = false;
            const list = document.getElementById('groups-list');
            if (list) list.innerHTML = '';
            hide('#groups-container');
        }

        async function loadGroups(force = false) {
            try {
                show('#groups-container');
                show('#groups-loading');
                hide('#groups-empty');
                hide('#groups-list');

                const response = await fetch(groupsUrl, {
                    method: 'GET',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Jika error maintenance (503), reload halaman
                if (response.status === 503) {
                    window.location.reload();
                    return;
                }

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal mengambil daftar grup.');
                }

                const groups = Array.isArray(result.data) ? result.data : [];
                const total = document.getElementById('groups-total');
                if (total) total.textContent = `${groups.length} Grup`;

                const list = document.getElementById('groups-list');
                if (!list) return;

                if (groups.length === 0) {
                    hide('#groups-loading');
                    show('#groups-empty');
                    return;
                }

                list.innerHTML = groups.map(function (group, index) {
                    const subject = escapeHtml(group.subject || group.name || 'Tanpa Nama');
                    const id = escapeHtml(group.id || group.groupId || group.jid || '-');
                    const size = Number(group.size || 0);

                    return `
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-sm transition">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 shrink-0 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <i data-lucide="messages-square" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-700 truncate" title="${subject}">${subject}</p>
                                        <p class="text-xs text-gray-400 mt-0.5 break-all">${id}</p>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-xs text-gray-400">Anggota</p>
                                    <p class="text-sm font-bold text-gray-700">${size}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                hide('#groups-loading');
                show('#groups-list');
                refreshIcons();

            } catch (error) {
                console.error('Load groups error:', error);
                
                // Jika network error / gagal total, cek jika ingin reload atau tampilkan error box
                hide('#groups-loading');
                const total = document.getElementById('groups-total');
                if (total) total.textContent = '0 Grup';

                const list = document.getElementById('groups-list');
                if (list) {
                    list.innerHTML = `
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-red-700">Gagal memuat grup</p>
                                    <p class="text-xs text-red-500 mt-1">${escapeHtml(error.message)}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    show('#groups-list');
                }
                refreshIcons();
            }
        }

        /*
        * =====================================================
        * POLLING CONTROL
        * =====================================================
        */
        function stopStatusPolling() {
            if (statusPolling) {
                clearTimeout(statusPolling);
                statusPolling = null;
            }
        }

        function stopQueuePolling() {
            if (queuePolling) {
                clearTimeout(queuePolling);
                queuePolling = null;
            }
        }

        async function loadQueue() {
            if (isLoadingQueue) return null;
            isLoadingQueue = true;

            try {
                const response = await fetch(statusUrl, { cache: 'no-store' });

                if (response.status === 503) {
                    window.location.reload();
                    return null;
                }

                const result = await response.json();
                if (!result.success || !result.data) return null;

                const data = result.data;
                updateQueue(data.queue);
                currentConnected = data.connected === true;
                return data;

            } catch (error) {
                console.error('Queue polling error:', error);
                return null;
            } finally {
                isLoadingQueue = false;
            }
        }

        function scheduleQueuePolling(connected, pending) {
            stopQueuePolling();
            let interval = !connected ? 2000 : (Number(pending) > 0 ? 5000 : 60000);

            queuePolling = setTimeout(async function () {
                queuePolling = null;
                const data = await loadQueue();
                if (data) {
                    scheduleQueuePolling(data.connected === true, Number(data.queue?.pending || 0));
                } else {
                    scheduleQueuePolling(false, 0);
                }
            }, interval);
        }

        async function loadQR() {
            try {
                const response = await fetch(qrUrl, { cache: 'no-store' });
                if (response.status === 503) {
                    window.location.reload();
                    return false;
                }

                const result = await response.json();
                if (result.success && result.qr) {
                    if (currentQR !== result.qr) {
                        currentQR = result.qr;
                        document.getElementById('qr-image').src = result.qr;
                        show('#qr-container');
                        hide('#disconnected-container');
                        startCountdown(50);
                    }
                    return true;
                }

                hide('#qr-container');
                currentQR = null;
                return false;
            } catch (error) {
                console.error('QR error:', error);
                return false;
            }
        }

        function scheduleStatusPolling(connected) {
            stopStatusPolling();
            const interval = connected ? 60000 : 2000;

            statusPolling = setTimeout(async function () {
                statusPolling = null;
                await loadStatus();
            }, interval);
        }

        async function loadStatus() {
            if (isLoadingStatus) return;
            isLoadingStatus = true;

            try {
                const response = await fetch(statusUrl, { cache: 'no-store' });

                if (response.status === 503) {
                    window.location.reload();
                    return;
                }

                const result = await response.json();

                if (!result.success || !result.data) {
                    currentConnected = false;
                    clearGroups();
                    scheduleStatusPolling(false);
                    scheduleQueuePolling(false, 0);
                    return;
                }

                const data = result.data;
                updateQueue(data.queue);

                currentConnected = data.connected === true;
                currentPending = Number(data.queue?.pending || 0);

                if (currentConnected) {
                    stopCountdown();
                    currentQR = null;

                    hide('#qr-container');
                    hide('#disconnected-container');
                    show('#logout-container');
                    show('#phone-container');

                    document.getElementById('phone-number').textContent = formatPhone(data.phone);
                    document.getElementById('status-text').textContent = 'WhatsApp sedang terhubung.';
                    setStatusBadge(data.status, true);

                    await loadGroups();

                    scheduleStatusPolling(true);
                    scheduleQueuePolling(true, currentPending);
                    return;
                }

                clearGroups();
                hide('#logout-container');
                hide('#phone-container');

                document.getElementById('status-text').textContent = 'WhatsApp belum terhubung.';
                setStatusBadge(data.status, false);

                if (data.qrAvailable === true) {
                    await loadQR();
                    hide('#disconnected-container');
                } else {
                    stopCountdown();
                    currentQR = null;
                    hide('#qr-container');
                    show('#disconnected-container');
                }

                scheduleStatusPolling(false);
                scheduleQueuePolling(false, currentPending);

            } catch (error) {
                console.error('Status error:', error);
                currentConnected = false;
                clearGroups();
                scheduleStatusPolling(false);
                scheduleQueuePolling(false, 0);
            } finally {
                isLoadingStatus = false;
            }
        }

        /*
        * =====================================================
        * EVENT LISTENERS (REFRESH & LOGOUT)
        * =====================================================
        */
        const refreshButton = document.getElementById('btn-refresh-status');
        if (refreshButton) {
            refreshButton.addEventListener('click', async function () {
                const button = this;
                const originalHTML = button.innerHTML;

                setLoading(button, true);
                button.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Memeriksa...`;
                refreshIcons();

                stopStatusPolling();
                stopQueuePolling();

                try {
                    await loadStatus();
                } finally {
                    button.innerHTML = originalHTML;
                    setLoading(button, false);
                    refreshIcons();
                }
            });
        }

        const refreshGroupsButton = document.getElementById('btn-refresh-groups');
        if (refreshGroupsButton) {
            refreshGroupsButton.addEventListener('click', async function () {
                if (!currentConnected) return;
                await loadGroups(true);
            });
        }

        const logoutButton = document.getElementById('btn-logout');
        if (logoutButton) {
            logoutButton.addEventListener('click', async function () {
                const button = this;

                const result = await Swal.fire({
                    title: 'Logout WhatsApp?',
                    text: 'Perangkat WhatsApp akan diputus dan Anda perlu menautkan kembali menggunakan QR.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                });

                if (!result.isConfirmed) return;

                setLoading(button, true);

                try {
                    stopStatusPolling();
                    stopQueuePolling();

                    const response = await fetch(logoutUrl, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                        }
                    });

                    if (response.status === 503) {
                        let errText = 'Fitur sedang dalam pemeliharaan (Maintenance).';
                        try {
                            const errJson = await response.json();
                            if (errJson.message) errText = errJson.message;
                        } catch (e) {}
                        throw new Error(errText);
                    }

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal logout WhatsApp.');
                    }

                    stopCountdown();
                    currentQR = null;
                    currentConnected = false;
                    groupsLoaded = false;
                    clearGroups();

                    await Swal.fire({
                        icon: 'success',
                        title: 'Logout Berhasil',
                        text: 'WhatsApp berhasil diputus. Sistem akan mencari QR baru.',
                        timer: 1800,
                        showConfirmButton: false
                    });

                    await loadStatus();

                } catch (error) {
                    console.error('Logout error:', error);

                    // KHUSUS LOGOUT: Tampilkan alert SweetAlert saja TANPA reload halaman
                    Swal.fire({
                        icon: 'error',
                        title: 'Logout Gagal',
                        text: error.message || 'Gagal menghubungi WhatsApp Gateway.'
                    });

                    scheduleStatusPolling(currentConnected);
                    scheduleQueuePolling(currentConnected, currentPending);

                } finally {
                    setLoading(button, false);
                }
            });
        }

        /*
        * =====================================================
        * INITIALIZATION
        * =====================================================
        */
        loadStatus();
        refreshIcons();

    });
</script>

<?= $this->endSection() ?>