<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Atur Password | SIM Al Manaar Slipi</title>
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
            
            <div class="p-8 text-center pb-6">
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                    Atur Password
                </h1>
                <p class="text-sm text-gray-500 mt-2 italic">
                    Silakan masukkan password baru Anda.
                </p>
            </div>

            <div class="p-8 pt-0">
                <?php if(!empty(session()->getFlashdata('error'))): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded shadow-sm flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/reset-password/update'); ?>" method="post" class="space-y-5">
                    <?= csrf_field() ?>
                    
                    <input type="hidden" name="token" value="<?= $token ?>">

                    <div class="space-y-4">
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                                Password Baru
                            </label>
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 transition-all">
                                <input :type="show ? 'text' : 'password'" name="password" id="password" required minlength="8"
                                    class="block flex-1 border-0 bg-transparent py-3 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                                    placeholder="Min. 8 karakter">
                                
                                <button type="button" @click="show = !show" class="flex items-center pr-3 text-gray-400 hover:text-blue-600 transition-colors">
                                    <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>
                                
                                <span class="flex items-center pr-4 text-gray-400 border-l border-gray-200 ml-1 pl-3">
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                </span>
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label for="pass_confirm" class="block text-sm font-semibold text-gray-700 mb-1">
                                Konfirmasi Password Baru
                            </label>
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 transition-all">
                                <input :type="show ? 'text' : 'password'" name="pass_confirm" id="pass_confirm" required
                                    class="block flex-1 border-0 bg-transparent py-3 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                                    placeholder="Ulangi password baru">
                                
                                <button type="button" @click="show = !show" class="flex items-center pr-3 text-gray-400 hover:text-blue-600 transition-colors">
                                    <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>

                                <span class="flex items-center pr-4 text-gray-400 border-l border-gray-200 ml-1 pl-3">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center py-2">
                        <div class="g-recaptcha" data-sitekey="<?= $sitekey ?>"></div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition-all active:scale-[0.98]">
                        Simpan Password Baru
                    </button>
                    
                    <div class="text-center">
                        <a href="<?= base_url('admin/login'); ?>" class="text-xs text-blue-600 hover:underline">
                            Kembali ke Halaman Login
                        </a>
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
</body>
</html>