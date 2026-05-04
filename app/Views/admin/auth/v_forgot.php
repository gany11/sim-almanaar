<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lupa Password | SIM Al Manaar Slipi</title>
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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-black/40 p-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            
            <div class="p-8 text-center pb-6">
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                    Reset Password
                </h1>
                <div class="pt-3 overflow-hidden whitespace-nowrap">
                    <marquee><p class="login-box-msg"> Masukkan email Anda untuk menerima tauatan pengaturan ulang password.</p></marquee>
                </div>
            </div>

            <div class="p-8">
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

                <form action="<?= base_url('admin/forgot-password/save'); ?>" method="post" class="space-y-5">
                    <?= csrf_field() ?>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                            Alamat Email
                        </label>
                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-blue-600 transition-all">
                            <input type="email" name="email" id="email" required
                                class="block flex-1 border-0 bg-transparent py-3 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                                placeholder="nama@email.com">
                            <span class="flex items-center pr-4 text-gray-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-center py-2">
                        <div class="g-recaptcha" data-sitekey="<?= $sitekey ?>"></div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition-all active:scale-[0.98]">
                        Kirim Link Reset
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