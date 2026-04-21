<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Sholat</title>
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">
        <div class="bg-emerald-600 p-6 text-center">
            <h2 class="text-2xl font-bold text-white mb-1">Jadwal Sholat</h2>
            <p class="text-emerald-100 text-sm">Jakarta Barat & Sekitarnya</p>
            <div class="mt-4 inline-block bg-emerald-800 text-emerald-50 px-4 py-1 rounded-full text-sm font-medium">
                <?= $tanggal ?>
            </div>
        </div>

        <div class="p-6">
            <ul class="space-y-3">
                <li class="flex justify-between items-center p-3 rounded-lg hover:bg-emerald-50 transition-colors">
                    <span class="font-medium text-gray-600">Subuh</span>
                    <span class="font-bold text-gray-800"><?= $jadwal['Fajr'] ?></span>
                </li>
                <li class="flex justify-between items-center p-3 rounded-lg hover:bg-emerald-50 transition-colors">
                    <span class="font-medium text-gray-600">Terbit</span>
                    <span class="font-bold text-gray-800"><?= $jadwal['Sunrise'] ?></span>
                </li>
                <li class="flex justify-between items-center p-3 bg-emerald-100 rounded-lg border border-emerald-200">
                    <span class="font-bold text-emerald-800">Dzuhur</span>
                    <span class="font-black text-emerald-900"><?= $jadwal['Dhuhr'] ?></span>
                </li>
                <li class="flex justify-between items-center p-3 rounded-lg hover:bg-emerald-50 transition-colors">
                    <span class="font-medium text-gray-600">Ashar</span>
                    <span class="font-bold text-gray-800"><?= $jadwal['Asr'] ?></span>
                </li>
                <li class="flex justify-between items-center p-3 rounded-lg hover:bg-emerald-50 transition-colors">
                    <span class="font-medium text-gray-600">Maghrib</span>
                    <span class="font-bold text-gray-800"><?= $jadwal['Maghrib'] ?></span>
                </li>
                <li class="flex justify-between items-center p-3 rounded-lg hover:bg-emerald-50 transition-colors">
                    <span class="font-medium text-gray-600">Isya</span>
                    <span class="font-bold text-gray-800"><?= $jadwal['Isha'] ?></span>
                </li>
            </ul>
        </div>
    </div>

</body>
</html>