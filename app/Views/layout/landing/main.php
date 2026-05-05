<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('layout/landing/head') ?>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?= view('layout/landing/navbar') ?>

    <main class="flex-grow">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('layout/landing/footer') ?>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>