<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk | SIM Al Manaar Slipi</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png');?>">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">

    <?= vite('public/js/app.js') ?>

    <style>
        body {
            background-image: url('<?= base_url("assets/images/Latar Al Manaar.png") ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
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
</head>
<body class="min-h-screen flex items-center justify-center bg-black/40 p-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            
            <div class="p-8 text-center pb-0">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                    <span class="text-blue-600">SIM</span> Al Manaar
                </h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Sistem Informasi Masjid Al Manaar</p>
                
                <div class="mt-1 py-2 px-4 rounded-lg inline-block">
                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <?= format_indo(date('Y-m-d'), 'full') ?> | <?= format_hijriah(date('Y-m-d')) ?>
                    </div>
                    <div id="time" class="text-xl font-bold text-blue-600"></div>
                </div>
                <div class="overflow-hidden whitespace-nowrap">
                          <marquee><p class="login-box-msg">Selamat Datang! Silahkan masuk untuk memulai sesi Anda!</p></marquee>
                </div>
            </div>

            <div class="p-8 pt-0">
                <?php if(!empty(session()->getFlashdata('error'))): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded shadow-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-lg"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php elseif (!empty(session()->getFlashdata('success'))): ?>
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded shadow-sm flex items-center gap-3">
                        <i class="fas fa-check-circle text-lg"></i>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/login/in'); ?>" method="post" class="space-y-5">
                    <?= csrf_field() ?>

                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-semibold leading-6 text-gray-700 mb-1">
                                Username
                            </label>
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 transition-all duration-200">
                                <input type="text" name="username" id="username" required
                                    pattern="^[a-z0-9]+$"
                                    class="block flex-1 border-0 bg-transparent py-3 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                                    placeholder="Masukkan username" autocomplete="off">
                                <span class="flex items-center pr-4 text-gray-400">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </span>
                            </div>
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-sm font-semibold leading-6 text-gray-700 mb-1">
                                Password
                            </label>
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 transition-all duration-200">
                                <input :type="showPassword ? 'text' : 'password'" 
                                    name="password" id="password" required minlength="8"
                                    class="block flex-1 border-0 bg-transparent py-3 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                                    placeholder="Masukkan password">
                                
                                <button type="button" @click="showPassword = !showPassword" class="flex items-center px-2 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>

                                <span class="flex items-center pr-4 text-gray-400 border-l border-gray-100 ml-1 pl-3">
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center py-2">
                        <div class="g-recaptcha" data-sitekey="<?= $sitekey ?>"></div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
                        Masuk
                    </button>
                    
                    <div class="text-center">
                        <a href="<?= base_url('admin/forgot-password'); ?>" class="text-xs text-blue-600 hover:underline">Lupa password?</a>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 p-6 pt-0 text-center border-t border-gray-100">
                <p class="text-xs text-gray-500 font-medium">
                    &copy; <?= date('Y') ?> <strong>DKM AL Manaar Slipi</strong>. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();

            // 1. Update Waktu (Jam:Menit:Detik)
            // padStart memastikan angka selalu 2 digit (misal: 09:05:01)
            const timeString = [
                now.getHours(),
                now.getMinutes(),
                now.getSeconds()
            ].map(unit => String(unit).padStart(2, '0')).join(':');

            const timeElement = document.getElementById('time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }

            // 2. Update Tanggal Masehi (Hanya jika jam menunjukkan tepat tengah malam)
            // Ini untuk efisiensi agar tidak me-render ulang teks tanggal setiap detik
            if (now.getHours() === 0 && now.getMinutes() === 0 && now.getSeconds() === 0) {
                // Opsional: Anda bisa memicu reload halaman atau fetch ulang tanggal hijriah via AJAX
                // jika ingin tanggalnya berganti otomatis tanpa refresh saat lewat jam 12 malam.
            }
        }

        // Jalankan setiap detik
        setInterval(updateDateTime, 1000);

        // Panggil langsung saat halaman load
        document.addEventListener('DOMContentLoaded', () => {
            updateDateTime();
            // Re-init icons jika menggunakan Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>