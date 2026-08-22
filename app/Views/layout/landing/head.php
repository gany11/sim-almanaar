<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png');?>">
<title><?= esc(empty($title)? '' : $title. ' | ') ?>SIM Al Manaar Slipi</title>

<!-- SEO -->
<meta name="description" content="Sistem Informasi Manajemen Masjid Al Manaar Slipi untuk pengelolaan publikasi, agenda, dan informasi masjid.">
<meta name="keywords" content="Masjid Al Manaar, SIM Masjid, Al Manaar Slipi, DKM, Informasi Masjid">
<meta name="author" content="Masjid Al Manaar Slipi">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="SIM Al Manaar Slipi">
<meta property="og:title" content="<?= esc(empty($title) ? 'SIM Al Manaar Slipi' : $title . ' | SIM Al Manaar Slipi') ?>">
<meta property="og:description" content="Sistem Informasi Manajemen Masjid Al Manaar Slipi untuk pengelolaan publikasi, agenda, dan informasi masjid.">
<meta property="og:url" content="<?= current_url() ?>">
<meta property="og:image" content="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc(empty($title) ? 'SIM Al Manaar Slipi' : $title . ' | SIM Al Manaar Slipi') ?>">
<meta name="twitter:description" content="Sistem Informasi Manajemen Masjid Al Manaar Slipi untuk pengelolaan publikasi, agenda, dan informasi masjid.">
<meta name="twitter:image" content="<?= base_url('assets/images/Logo Masjid Al Manaar Slipi.png') ?>">

<link href="<?= base_url('css/style.css') ?>" rel="stylesheet">

<?= vite('public/js/landing.js') ?>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }

</style>