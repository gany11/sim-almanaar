<?= $this->extend('layout/landing/main') ?>

<?= $this->section('content') ?>

<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    input::-webkit-contacts-auto-fill-button,
    input::-webkit-credentials-auto-fill-button {
        visibility: hidden;
        pointer-events: none;
        position: absolute;
        right: 0;
    }
</style>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-4xl">

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Menggunakan Grid: 1 Kolom di Mobile, 2 Kolom (md:grid-cols-2) mulai dari Medium/Desktop -->
            <div class="grid grid-cols-1 md:grid-cols-2">

                <!-- SISI KIRI / HEADER -->
                <div class="px-6 sm:px-10 pt-10 pb-8 md:py-16 flex flex-col justify-center items-center md:items-start text-center md:text-left bg-gray-50/50 md:border-r border-gray-100">

                    <div class="w-16 h-16 mb-5 rounded-2xl
                                bg-emerald-50
                                flex items-center justify-center">

                        <i data-lucide="key-round"
                           class="w-8 h-8 text-emerald-600"></i>

                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        Buat PIN Donatur
                    </h1>

                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        Buat PIN 6 digit untuk mengamankan akses akun donatur Anda.
                    </p>

                    <!-- Flash Message Error -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded shadow-sm flex items-center gap-3 text-left w-full">
                            <i data-lucide="circle-alert" class="w-5 h-5 flex-shrink-0"></i>
                            <span>
                                <?= esc(session()->getFlashdata('error')) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Flash Message Success -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="mt-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded shadow-sm flex items-center gap-3 text-left w-full">
                            <i data-lucide="circle-check" class="w-5 h-5 flex-shrink-0"></i>
                            <span>
                                <?= esc(session()->getFlashdata('success')) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- SISI KANAN / FORM -->
                <div class="p-6 sm:px-10 md:p-12 flex flex-col justify-center">

                    <form action="<?= base_url('donatur/pin') ?>"
                          method="post"
                          class="space-y-5">

                        <?= csrf_field() ?>

                        <!-- Noreg (Hidden) -->
                        <input type="hidden" name="noreg" value="<?= esc($noreg) ?>">

                        <!-- Token (Hidden) -->
                        <input type="hidden" name="token" value="<?= esc($token) ?>">

                        <div class="space-y-4">

                            <!-- PIN Baru (6 Digit Angka dengan Hide/Unhide) -->
                            <div x-data="{ showPin: false }">
                                <label for="pin"
                                       class="block text-sm font-semibold leading-6 text-gray-700 mb-1">
                                    PIN Baru
                                </label>

                                <div class="flex w-full rounded-xl shadow-sm ring-1 ring-inset ring-gray-200 focus-within:ring-2 focus-within:ring-inset focus-within:ring-emerald-600 transition-all duration-200 overflow-hidden bg-gray-50/50 focus-within:bg-white">

                                    <!-- PIN Input -->
                                    <input
                                        :type="showPin ? 'text' : 'password'"
                                        name="pin"
                                        id="pin"
                                        inputmode="numeric"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        autocomplete="new-password"
                                        required
                                        autofocus
                                        class="min-w-0 flex-1 border-0 bg-transparent py-3 pl-4 pr-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none font-medium"
                                        placeholder="Masukkan 6 digit PIN">

                                    <!-- Eye Button -->
                                    <button
                                        type="button"
                                        @click="showPin = !showPin; $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                        class="shrink-0 flex items-center justify-center px-3 text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none"
                                        aria-label="Tampilkan atau sembunyikan PIN">

                                        <i :data-lucide="showPin ? 'eye-off' : 'eye'" class="w-5 h-5"></i>

                                    </button>

                                    <!-- Lock Icon -->
                                    <span class="shrink-0 flex items-center justify-center px-3 text-gray-400 border-l border-gray-100">
                                        <i data-lucide="lock-keyhole" class="w-5 h-5"></i>
                                    </span>

                                </div>

                                <p class="mt-1 text-xs text-gray-400">
                                    Gunakan tepat 6 digit angka.
                                </p>
                            </div>

                            <!-- Konfirmasi PIN (6 Digit Angka dengan Hide/Unhide) -->
                            <div x-data="{ showConfirm: false }">
                                <label for="pin_confirmation"
                                       class="block text-sm font-semibold leading-6 text-gray-700 mb-1">
                                    Konfirmasi PIN
                                </label>

                                <div class="flex w-full rounded-xl shadow-sm ring-1 ring-inset ring-gray-200 focus-within:ring-2 focus-within:ring-inset focus-within:ring-emerald-600 transition-all duration-200 overflow-hidden bg-gray-50/50 focus-within:bg-white">

                                    <!-- Confirmation Input -->
                                    <input
                                        :type="showConfirm ? 'text' : 'password'"
                                        name="pin_confirmation"
                                        id="pin_confirmation"
                                        inputmode="numeric"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        autocomplete="new-password"
                                        required
                                        class="min-w-0 flex-1 border-0 bg-transparent py-3 pl-4 pr-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none font-medium"
                                        placeholder="Ulangi 6 digit PIN">

                                    <!-- Eye Button -->
                                    <button
                                        type="button"
                                        @click="showConfirm = !showConfirm; $nextTick(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); })"
                                        class="shrink-0 flex items-center justify-center px-3 text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none"
                                        aria-label="Tampilkan atau sembunyikan konfirmasi PIN">

                                        <i :data-lucide="showConfirm ? 'eye-off' : 'eye'" class="w-5 h-5"></i>

                                    </button>

                                    <!-- Shield Icon -->
                                    <span class="shrink-0 flex items-center justify-center px-3 text-gray-400 border-l border-gray-100">
                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    </span>

                                </div>
                            </div>

                        </div>

                        <!-- reCAPTCHA -->
                        <div class="flex justify-center overflow-hidden pt-1 scale-95 sm:scale-100 origin-center">
                            <div class="g-recaptcha"
                                 data-sitekey="<?= esc($sitekey) ?>">
                            </div>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full py-3.5
                                   rounded-xl
                                   bg-emerald-600
                                   text-white
                                   font-semibold
                                   hover:bg-emerald-700
                                   active:bg-emerald-800
                                   transition
                                   flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan PIN
                        </button>

                        <!-- Info Box -->
                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5"></i>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    PIN digunakan untuk login ke akun donatur.
                                    Jangan berikan PIN Anda kepada orang lain.
                                </p>
                            </div>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

<?= $this->endSection() ?>