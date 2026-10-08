<?php

use CodeIgniter\Router\RouteCollection;
use Config\Feature;

/**
 * @var RouteCollection $routes
*/

// $routes->get('/', 'Home::index');

/**
 * Home / Landing Page
 */
$routes->get('/', 'Landing\HomeController::index', ['filter' => ['auth:hybrid', 'feature:publik.home']]);

/**
 * Finance Reports (DataTables)
 */
$routes->get('keuangan', 'Landing\FinanceController::index', ['filter' => ['auth:hybrid', 'feature:publik.keuangan']]);
// $routes->get('keuangan/(:num)/(:num)/(:num)', 'Landing\FinanceController::detail/$1/$2/$3');
// Iterasi 2
$routes->get('keuangan/(:num)/(:num)', 'Landing\FinanceController::detail/$1/$2', ['filter' => ['auth:hybrid', 'feature:publik.detail.keuangan']]);
$routes->get('keuangan/getMonthlyReportAjax', 'Landing\FinanceController::getMonthlyReportAjax', ['filter' => 'apiGuard']);

/**
 * Donasi
 */
$routes->get('donasi', 'Landing\DonationController::index', ['filter' => ['auth:hybrid', 'feature:publik.program.donasi']]);
$routes->get('donasi/detail/(:num)', 'Landing\DonationController::detail/$1', ['filter' => ['auth:hybrid', 'feature:publik.detail.program.donasi']]);

/**
 * Donatur
 */
$routes->get('donatur', 'Landing\DonorController::index', ['filter' => ['auth:hybrid', 'feature:publik.donatur']]);
$routes->get('donatur/(:num)/(:segment)', 'Landing\DonorController::access/$1/$2', ['filter' => ['auth:hybrid', 'feature:publik.donatur.link']]);
$routes->post('donatur/login', 'Landing\DonorController::login', ['filter' => ['auth:hybrid', 'feature:publik.login.donatur']]);
$routes->post('donatur/pin', 'Landing\DonorController::setPin', ['filter' => ['auth:hybrid', 'feature:publik.set.pin.donatur']]);
$routes->get('donatur/lupa-pin', 'Landing\DonorController::forgotPin', ['filter' => ['auth:hybrid', 'feature:publik.lupa.pin.donatur']]);
$routes->post('donatur/lupa-pin', 'Landing\DonorController::requestResetPin', ['filter' => ['auth:hybrid', 'feature:publik.lupa.pin.donatur']]);
$routes->get('donatur/reset-pin/(:segment)', 'Landing\DonorController::resetPin/$1', ['filter' => ['auth:hybrid', 'feature:publik.reset.pin.donatur']]);
$routes->post('donatur/reset-pin/(:segment)', 'Landing\DonorController::updatePin/$1', ['filter' => ['auth:hybrid', 'feature:publik.reset.pin.donatur']]);
$routes->get('donatur/logout', 'Landing\DonorController::logout', ['filter' => ['auth:hybrid', 'feature:publik.logout.donatur']]);

/**
 * Agenda (FullCalendar)
 */
$routes->get('agenda', 'Landing\AgendaController::index', ['filter' => ['auth:hybrid', 'feature:publik.agenda']]);
$routes->get('api/agenda', 'Landing\AgendaController::getEvents', ['filter' => 'apiGuard']);

/**
 * News (Berita)
 */
$routes->get('berita', 'Landing\NewsController::index', ['filter' => ['auth:hybrid', 'feature:publik.berita']]);
$routes->get('berita/(:num)/(:segment)', 'Landing\NewsController::detail/$1/$2', ['filter' => ['auth:hybrid', 'feature:publik.detail.berita']]);

/**
 * Articles (Artikel)
 */
$routes->get('artikel', 'Landing\ArticleController::index', ['filter' => ['auth:hybrid', 'feature:publik.artikel']]);
$routes->get('artikel/(:num)/(:segment)', 'Landing\ArticleController::detail/$1/$2', ['filter' => ['auth:hybrid', 'feature:publik.detail.artikel']]);

// Login (Admin)
$routes->get('admin', static function () {
    return redirect()->to('/admin/login');
});
$routes->get('admin/login', 'AuthController::index', ['filter' => ['auth:false', 'feature:autentikasi.login.admin']]);
$routes->post('admin/login/in', 'AuthController::login', ['filter' => ['auth:false', 'feature:autentikasi.login.admin']]);

// Lupa Password (Admin)
$routes->get('admin/forgot-password', 'ForgotController::index', ['filter' => ['auth:false', 'feature:autentikasi.lupa.password.admin']]);
$routes->post('admin/forgot-password/save', 'ForgotController::sendResetLink', ['filter' => ['auth:false', 'feature:autentikasi.lupa.password.admin']]);
$routes->get('admin/reset-password/(:any)', 'ForgotController::resetPassword/$1', ['filter' => ['auth:false', 'feature:autentikasi.reset.password.admin']]);
$routes->post('admin/reset-password/update', 'ForgotController::updatePassword', ['filter' => ['auth:false', 'feature:autentikasi.reset.password.admin']]);

// Dashboard
$routes->get('admin/dashboard', 'Admin\DashboardController::index', ['filter' => ['auth:true', 'feature:admin.dashboard']]);

// Logout (Admin)
$routes->get('admin/logout', 'AuthController::logout', ['filter' => ['auth:true', 'feature:autentikasi.logout.admin']]);

// Read Profil
$routes->get('admin/profile', 'Admin\ProfileController::index', ['filter' => ['auth:true', 'feature:admin.profil']]);
// Update Profil (Admin)
$routes->post('admin/profile/update', 'Admin\ProfileController::updateProfile', ['filter' => ['auth:true', 'feature:admin.update.profil']]);
// Update Password (Admin)
$routes->post('admin/profile/update-password', 'Admin\ProfileController::updatePassword', ['filter' => ['auth:true', 'feature:admin.update.password']]);

// Registrasi Akun
$routes->get('admin/account/register', 'Admin\AccountController::register', ['filter' => ['auth:true', 'feature:akun.create']]);
$routes->post('admin/account/save', 'Admin\AccountController::save', ['filter' => ['auth:true', 'feature:akun.create']]);

// Manajemen Akun
// Read Akun
$routes->get('admin/account', 'Admin\AccountController::index', ['filter' => ['auth:true', 'feature:akun.read']]);
$routes->post('admin/account/list', 'Admin\AccountController::list', ['filter' => ['auth:true', 'feature:akun.read']]);
// Update Status Akun
$routes->post('admin/account/status', 'Admin\AccountController::updateStatus', ['filter' => ['auth:true', 'feature:akun.update.status']]);
// Detail Akun
$routes->get('admin/account/detail/(:num)', 'Admin\AccountController::detail/$1', ['filter' => ['auth:true', 'feature:akun.detail']]);
// Sinkronisasi Fitur Akun
$routes->post('admin/account/feature/sync', 'Admin\AccountController::featureSync', ['filter' => ['auth:true', 'feature:akun.sinkronisasi.add.fitur']]);

// Manajemen Whatsapp
// Read Whatsapp
$routes->get('admin/whatsapp', 'Admin\WhatsAppController::index', ['filter' => ['auth:true', 'feature:whatsapp.read']]);
$routes->get('admin/whatsapp/status', 'Admin\WhatsAppController::status', ['filter' => ['auth:true', 'feature:whatsapp.read']]);
$routes->get('admin/whatsapp/qr', 'Admin\WhatsAppController::qr', ['filter' => ['auth:true', 'feature:whatsapp.read']]);
$routes->get('admin/whatsapp/groups', 'Admin\WhatsAppController::group', ['filter' => ['auth:true', 'feature:whatsapp.read']]);
// Logout Whatsapp
$routes->post('admin/whatsapp/logout', 'Admin\WhatsAppController::logout', ['filter' => ['auth:true', 'feature:whatsapp.logout']]);

// Manajemen Berita (News)
// Read Berita
$routes->get('admin/news', 'Admin\NewsController::index', ['filter' => ['auth:true', 'feature:berita.read']]);
$routes->post('admin/news/list', 'Admin\NewsController::list', ['filter' => ['auth:true', 'feature:berita.read']]);
// Create Berita
$routes->get('admin/news/create', 'Admin\NewsController::create', ['filter' => ['auth:true', 'feature:berita.create']]);
$routes->post('admin/news/save', 'Admin\NewsController::save', ['filter' => ['auth:true', 'feature:berita.create']]);
// Update Berita
$routes->get('admin/news/edit/(:num)', 'Admin\NewsController::edit/$1', ['filter' => ['auth:true', 'feature:berita.update']]);
$routes->post('admin/news/update/(:num)', 'Admin\NewsController::update/$1', ['filter' => ['auth:true', 'feature:berita.update']]);
// Delete Berita
$routes->post('admin/news/delete', 'Admin\NewsController::delete', ['filter' => ['auth:true', 'feature:berita.delete']]);

// Manajemen Artikel (Articles)
// Read Artikel
$routes->get('admin/article', 'Admin\ArticleController::index', ['filter' => ['auth:true', 'feature:artikel.read']]);
$routes->post('admin/article/list', 'Admin\ArticleController::list', ['filter' => ['auth:true', 'feature:artikel.read']]);
// Create Artikel
$routes->get('admin/article/create', 'Admin\ArticleController::create', ['filter' => ['auth:true', 'feature:artikel.create']]);
$routes->post('admin/article/save', 'Admin\ArticleController::save', ['filter' => ['auth:true', 'feature:artikel.create']]);
// Update Artikel
$routes->get('admin/article/edit/(:num)', 'Admin\ArticleController::edit/$1', ['filter' => ['auth:true', 'feature:artikel.update']]);
$routes->post('admin/article/update/(:num)', 'Admin\ArticleController::update/$1', ['filter' => ['auth:true', 'feature:artikel.update']]);
// Delete Artikel
$routes->post('admin/article/delete', 'Admin\ArticleController::delete', ['filter' => ['auth:true', 'feature:artikel.delete']]);

// Manajemen Keuangan (Finance)
// Read Data Keuangan
$routes->get('admin/finance/data', 'Admin\FinanceController::index', ['filter' => ['auth:true', 'feature:keuangan.read']]);
$routes->post('admin/finance/data/list', 'Admin\FinanceController::list', ['filter' => ['auth:true', 'feature:keuangan.read']]);
// Create Data Keuangan
$routes->get('admin/finance/data/create', 'Admin\FinanceController::create', ['filter' => ['auth:true', 'feature:keuangan.create']]);
$routes->post('admin/finance/data/save', 'Admin\FinanceController::save', ['filter' => ['auth:true', 'feature:keuangan.create']]);
// Update Data Keuangan
$routes->get('admin/finance/data/edit/(:num)', 'Admin\FinanceController::edit/$1', ['filter' => ['auth:true', 'feature:keuangan.update']]);
$routes->post('admin/finance/data/update/(:num)', 'Admin\FinanceController::update/$1', ['filter' => ['auth:true', 'feature:keuangan.update']]);
// Delete Data Keuangan
$routes->post('admin/finance/data/delete', 'Admin\FinanceController::delete', ['filter' => ['auth:true', 'feature:keuangan.delete']]);
// Import Data Keuangan (Excel)
$routes->post('admin/finance/import-excel', 'Admin\FinanceController::importExcel', ['filter' => ['auth:true', 'feature:keuangan.impor']]);
// Ajax untuk mendapatkan detail alokasi berdasarkan id alokasi
$routes->post('admin/finance/get-detail-alokasi', 'Admin\FinanceController::getDetailAlokasi', ['filter' => 'apiGuard']);

// Report
// Read Laporan Keuangan Mingguan
$routes->get('admin/finance/report/weekly', 'Admin\ReportController::weekly', ['filter' => ['auth:true', 'feature:laporan.keuangan.mingguan']]);
$routes->get('admin/finance/report/getWeeklyHistoryAjax', 'Admin\ReportController::getWeeklyHistoryAjax', ['filter' => ['apiGuard', 'feature:laporan.keuangan.mingguan']]);
// Read Detail Laporan Keuangan Mingguan
$routes->get('admin/finance/report/weekly/(:num)', 'Admin\ReportController::detail/$1', ['filter' => ['auth:true', 'feature:laporan.keuangan.detail.mingguan']]);
// Edit Catatan Laporan Keuangan Mingguan
$routes->get('admin/finance/report/edit-note/(:num)', 'Admin\ReportController::editNote/$1', ['filter' => ['auth:true', 'feature:laporan.keuangan.catatan.mingguan']]);
$routes->post('admin/finance/report/update-note', 'Admin\ReportController::updateNote', ['filter' => ['auth:true', 'feature:laporan.keuangan.catatan.mingguan']]);
// Read Laporan Keuangan Bulanan
$routes->get('admin/finance/report/monthly', 'Admin\ReportController::monthly', ['filter' => ['auth:true', 'feature:laporan.keuangan.bulanan']]);
// Read Detail Laporan Keuangan Bulanan
$routes->get('admin/finance/report/monthly/(:num)/(:num)', 'Admin\ReportController::detailMonthly/$1/$2', ['filter' => ['auth:true', 'feature:laporan.keuangan.detail.bulanan']]);
// Export Laporan Keuangan Bulanan
$routes->get('admin/finance/report/monthly/export/(:num)/(:num)', 'Admin\ReportController::exportMonthly/$1/$2', ['filter' => ['auth:true', 'feature:laporan.keuangan.ekspor.bulanan']]);
// Read Laporan Keuangan Periodik
$routes->add('admin/finance/report/periodic', 'Admin\ReportController::periodic', ['filter' => ['auth:true', 'feature:laporan.keuangan.periodik']]);
// Read Laporan Keuangan Grafik
$routes->get('admin/finance/report/chart', 'Admin\ReportController::chart', ['filter' => ['auth:true', 'feature:laporan.keuangan.grafik']]);
$routes->get('admin/report/chart/detail-alokasi', 'Admin\ReportController::getDetailAlokasi', ['filter' => ['apiGuard', 'feature:laporan.keuangan.grafik']]);
$routes->get('admin/report/chart/chart-data', 'Admin\ReportController::getChartData', ['filter' => ['apiGuard', 'feature:laporan.keuangan.grafik']]);

// Manajemen Donatur (Donors)
// Read Donatur
$routes->get('admin/donors', 'Admin\DonorController::index', ['filter' => ['auth:true', 'feature:donatur.read']]);
$routes->post('admin/donors/list', 'Admin\DonorController::list', ['filter' => ['auth:true', 'feature:donatur.read']]);
// Detail Donatur
$routes->get('admin/donors/detail/(:num)', 'Admin\DonorController::detail/$1', ['filter' => ['auth:true', 'feature:donatur.detail']]);
// Create Donatur
$routes->get('admin/donors/create', 'Admin\DonorController::create', ['filter' => ['auth:true', 'feature:donatur.create']]);
$routes->post('admin/donors/save', 'Admin\DonorController::save', ['filter' => ['auth:true', 'feature:donatur.create']]);
// Update Donatur
$routes->get('admin/donors/edit/(:num)', 'Admin\DonorController::edit/$1', ['filter' => ['auth:true', 'feature:donatur.update']]);
$routes->post('admin/donors/update/(:num)', 'Admin\DonorController::update/$1', ['filter' => ['auth:true', 'feature:donatur.update']]);
// Delete Donatur
$routes->post('admin/donors/delete', 'Admin\DonorController::delete', ['filter' => ['auth:true', 'feature:donatur.delete']]);

// Manajeman Donasi (Donations)
// Read Donasi
$routes->get('admin/donations', 'Admin\DonationController::index', ['filter' => ['auth:true', 'feature:donasi.read']]);
$routes->post('admin/donations/list', 'Admin\DonationController::list', ['filter' => ['auth:true', 'feature:donasi.read']]);
// Detail Donasi
$routes->get('admin/donations/detail/(:num)', 'Admin\DonationController::detail/$1', ['filter' => ['auth:true', 'feature:donasi.detail']]);
$routes->get('admin/donations/partial-detail/(:num)', 'Admin\DonationController::partialDetail/$1', ['filter' => ['auth:true', 'feature:donasi.detail']]);
// Create Donasi
$routes->get('admin/donations/create', 'Admin\DonationController::create', ['filter' => ['auth:true', 'feature:donasi.create']]);
$routes->post('admin/donations/save', 'Admin\DonationController::save', ['filter' => ['auth:true', 'feature:donasi.create']]);
// Update Donasi
$routes->get('admin/donations/edit/(:num)', 'Admin\DonationController::edit/$1', ['filter' => ['auth:true', 'feature:donasi.update']]);
$routes->post('admin/donations/update/(:num)', 'Admin\DonationController::update/$1', ['filter' => ['auth:true', 'feature:donasi.update']]);
// Delete Donasi
$routes->post('admin/donations/delete', 'Admin\DonationController::delete', ['filter' => ['auth:true', 'feature:donasi.delete']]);
// Close Donasi
$routes->post('admin/donations/close', 'Admin\DonationController::close', ['filter' => ['auth:true', 'feature:donasi.close']]);

// Pengeluaran Donasi (Donation Expenses)
// Create Pengeluaran Donasi
$routes->get('admin/donation-expenses/create', 'Admin\DonationExpenseController::create', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.create']]);
$routes->get('admin/donation-expenses/create/(:num)', 'Admin\DonationExpenseController::create/$1', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.create']]);
$routes->post('admin/donation-expenses/save', 'Admin\DonationExpenseController::save', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.create']]);
// Update Pengeluaran Donasi
$routes->get('admin/donation-expenses/edit/(:num)', 'Admin\DonationExpenseController::edit/$1', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.update']]);
$routes->post('admin/donation-expenses/update/(:num)', 'Admin\DonationExpenseController::update/$1', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.update']]);
// Delete Pengeluaran Donasi
$routes->post('admin/donation-expenses/delete', 'Admin\DonationExpenseController::delete', ['filter' => ['auth:true', 'feature:pengeluaran.donasi.delete']]);

// Calon Donatur (Donation Donors)
// Create Calon Donatur
$routes->get('admin/donation-donors/create/(:num)', 'Admin\DonationDonorController::create/$1', ['filter' => ['auth:true', 'feature:calon.donatur.donasi.create']]);
$routes->post('admin/donation-donors/save', 'Admin\DonationDonorController::save', ['filter' => ['auth:true', 'feature:calon.donatur.donasi.create']]);
// Update Calon Donatur
$routes->get('admin/donation-donors/edit/(:num)', 'Admin\DonationDonorController::edit/$1', ['filter' => ['auth:true', 'feature:calon.donatur.donasi.update']]);
$routes->post('admin/donation-donors/update/(:num)', 'Admin\DonationDonorController::update/$1', ['filter' => ['auth:true', 'feature:calon.donatur.donasi.update']]);
// Update Status Calon Donatur
$routes->get('admin/donation-donors/get-status-options/(:num)', 'Admin\DonationDonorController::getStatusOptions/$1', ['filter' => ['apiGuard', 'feature:calon.donatur.donasi.update.status']]);
$routes->post('admin/donation-donors/update-status/(:num)', 'Admin\DonationDonorController::updateStatus/$1', ['filter' => ['auth:true', 'feature:calon.donatur.donasi.update.status']]);

// Pemasuk Donasi (Donation Incomes)
// Create Pemasukan Donasi
$routes->get('admin/donors/record/(:num)', 'Admin\DonationIncomeController::donorRecord/$1', ['filter' => ['auth:true', 'feature:pemasukan.donasi.donatur']]);
$routes->get('admin/donation-incomes/record/(:num)', 'Admin\DonationIncomeController::record/$1', ['filter' => ['auth:true', 'feature:pemasukan.donasi.calon.donatur']]);
$routes->get('admin/donation-incomes/create/(:num)', 'Admin\DonationIncomeController::create/$1', ['filter' => ['auth:true', 'feature:pemasukan.donasi.detail']]);
$routes->post('admin/donation-incomes/save', 'Admin\DonationIncomeController::save', ['filter' => ['auth:true', 'feature:pemasukan.donasi.donatur, pemasukan.donasi.calon.donatur, pemasukan.donasi.detail']]);
// Update Pemasukan Donasi
$routes->get('admin/donation-incomes/edit/(:num)', 'Admin\DonationIncomeController::edit/$1', ['filter' => ['auth:true', 'feature:pemasukan.donasi.update']]);
$routes->post('admin/donation-incomes/update/(:num)', 'Admin\DonationIncomeController::update/$1', ['filter' => ['auth:true', 'feature:pemasukan.donasi.update']]);
// Delete Pemasukan Donasi
$routes->post('admin/donation-incomes/delete', 'Admin\DonationIncomeController::delete', ['filter' => ['auth:true', 'feature:pemasukan.donasi.delete']]);
// Toggle Samarkan Pemasukan Donasi
$routes->post('admin/donation-incomes/toggle-samarkan', 'Admin\DonationIncomeController::toggleSamarkan', ['filter' => ['auth:true', 'feature:pemasukan.donasi.update.samaran']]);

// Manajemen Agenda (Agenda)
// Read Agenda
$routes->get('admin/agenda', 'Admin\AgendaController::index', ['filter' => ['auth:true', 'feature:agenda.read']]);
$routes->post('admin/agenda/list', 'Admin\AgendaController::list', ['filter' => ['auth:true', 'feature:agenda.read']]);
// Create Agenda
$routes->get('admin/agenda/create', 'Admin\AgendaController::create', ['filter' => ['auth:true', 'feature:agenda.create']]);
$routes->post('admin/agenda/save', 'Admin\AgendaController::save', ['filter' => ['auth:true', 'feature:agenda.create']]);
// Update Agenda
$routes->get('admin/agenda/edit/(:num)', 'Admin\AgendaController::edit/$1', ['filter' => ['auth:true', 'feature:agenda.update']]);
$routes->post('admin/agenda/update/(:num)', 'Admin\AgendaController::update/$1', ['filter' => ['auth:true', 'feature:agenda.update']]);
// Delete Agenda
$routes->post('admin/agenda/delete', 'Admin\AgendaController::delete', ['filter' => ['auth:true', 'feature:agenda.delete']]);
// Import Excel Agenda
$routes->post('admin/agenda/import-excel', 'Admin\AgendaController::importExcel', ['filter' => ['auth:true', 'feature:agenda.impor']]);

// Manajemen Agenda Rutin (Routine Agenda)
// Read Routine Agenda
$routes->get('admin/routine-agenda', 'Admin\RoutineAgendaController::index', ['filter' => ['auth:true', 'feature:agenda.rutin.read']]);
$routes->post('admin/routine-agenda/list', 'Admin\RoutineAgendaController::list', ['filter' => ['auth:true', 'feature:agenda.rutin.read']]);
// Create Routine Agenda
$routes->get('admin/routine-agenda/create', 'Admin\RoutineAgendaController::create', ['filter' => ['auth:true', 'feature:agenda.rutin.create']]);
$routes->post('admin/routine-agenda/save', 'Admin\RoutineAgendaController::save', ['filter' => ['auth:true', 'feature:agenda.rutin.create']]);
// Update Routine Agenda
$routes->get('admin/routine-agenda/edit/(:num)', 'Admin\RoutineAgendaController::edit/$1', ['filter' => ['auth:true', 'feature:agenda.rutin.update']]);
$routes->post('admin/routine-agenda/update/(:num)', 'Admin\RoutineAgendaController::update/$1', ['filter' => ['auth:true', 'feature:agenda.rutin.update']]);
// Delete Routine Agenda
$routes->post('admin/routine-agenda/delete', 'Admin\RoutineAgendaController::delete', ['filter' => ['auth:true', 'feature:agenda.rutin.delete']]);

// Manajemen Carousel
// Read Carousel
$routes->get('admin/carousel', 'Admin\CarouselController::index', ['filter' => ['auth:true', 'feature:carousel.read']]);
$routes->post('admin/carousel/list', 'Admin\CarouselController::list', ['filter' => ['auth:true', 'feature:carousel.read']]);
// Create Carousel
$routes->get('admin/carousel/create', 'Admin\CarouselController::create', ['filter' => ['auth:true', 'feature:carousel.create']]);
$routes->post('admin/carousel/save', 'Admin\CarouselController::save', ['filter' => ['auth:true', 'feature:carousel.create']]);
// Update Carousel
$routes->get('admin/carousel/edit/(:num)', 'Admin\CarouselController::edit/$1', ['filter' => ['auth:true', 'feature:carousel.update']]);
$routes->post('admin/carousel/update/(:num)', 'Admin\CarouselController::update/$1', ['filter' => ['auth:true', 'feature:carousel.update']]);
// Delete Carousel
$routes->post('admin/carousel/delete', 'Admin\CarouselController::delete', ['filter' => ['auth:true', 'feature:carousel.delete']]);

// Manajemen SDM
// Read SDM
$routes->get('admin/sdm', 'Admin\SdmController::index', ['filter' => ['auth:true', 'feature:sdm.read']]);
$routes->post('admin/sdm/list', 'Admin\SdmController::list', ['filter' => ['auth:true', 'feature:sdm.read']]);
// Create SDM
$routes->get('admin/sdm/create', 'Admin\SdmController::create', ['filter' => ['auth:true', 'feature:sdm.create']]);
$routes->post('admin/sdm/save', 'Admin\SdmController::save', ['filter' => ['auth:true', 'feature:sdm.create']]);
// Update SDM
$routes->get('admin/sdm/edit/(:num)', 'Admin\SdmController::edit/$1', ['filter' => ['auth:true', 'feature:sdm.update']]);
$routes->post('admin/sdm/update/(:num)', 'Admin\SdmController::update/$1', ['filter' => ['auth:true', 'feature:sdm.update']]);
// Delete SDM
$routes->post('admin/sdm/delete', 'Admin\SdmController::delete', ['filter' => ['auth:true', 'feature:sdm.delete']]);
// Detail SDM
$routes->get('admin/sdm/detail/(:num)', 'Admin\SdmController::detail/$1', ['filter' => ['auth:true', 'feature:sdm.detail']]);
// Get Alternative SDM
$routes->post('admin/sdm/alternative', 'Admin\SdmController::getAlternativeSdm', ['filter' => ['apiGuard', 'feature:sdm.delete']]);
// Replace and Delete SDM
$routes->post('admin/sdm/replace-delete', 'Admin\SdmController::replaceAndDelete', ['filter' => ['apiGuard', 'feature:sdm.delete']]);

// Manajemen KHGT via Sinkronisasi API
// Read KHGT
$routes->get('admin/khgt', 'Admin\KhgtController::index', ['filter' => ['auth:true', 'feature:khgt.read']]);
$routes->get('admin/khgt/list', 'Admin\KhgtController::list', ['filter' => ['apiGuard', 'feature:khgt.read']]);
// Sync KHGT
$routes->get('admin/khgt/sync', 'Admin\KhgtController::sync', ['filter' => ['apiGuard', 'feature:khgt.sinkronisasi.data']]);

// Catatan Keuangan (Keep Cash / Draf)
// Read Catatan Keuangan
$routes->get('admin/cash-notes', 'Admin\CashNoteController::index', ['filter' => ['auth:true', 'feature:catatan.keuangan.read']]);
$routes->post('admin/cash-notes/list', 'Admin\CashNoteController::list', ['filter' => ['auth:true', 'feature:catatan.keuangan.read']]);
// Create Catatan Keuangan
$routes->get('admin/cash-notes/create', 'Admin\CashNoteController::create', ['filter' => ['auth:true', 'feature:catatan.keuangan.create']]);
$routes->post('admin/cash-notes/save', 'Admin\CashNoteController::save', ['filter' => ['auth:true', 'feature:catatan.keuangan.create']]);
// Update Catatan Keuangan
$routes->get('admin/cash-notes/edit/(:num)', 'Admin\CashNoteController::edit/$1', ['filter' => ['auth:true', 'feature:catatan.keuangan.update']]);
$routes->post('admin/cash-notes/update/(:num)', 'Admin\CashNoteController::update/$1', ['filter' => ['auth:true', 'feature:catatan.keuangan.update']]);
// Update Status Catatan Keuangan
$routes->post('admin/cash-notes/status', 'Admin\CashNoteController::updateStatus', ['filter' => ['auth:true', 'feature:catatan.keuangan.update.status']]);
// Delete Catatan Keuangan
$routes->post('admin/cash-notes/delete', 'Admin\CashNoteController::delete', ['filter' => ['auth:true', 'feature:catatan.keuangan.delete']]);

// Manajemen Fitur (Features)
// Read Fitur
$routes->get('admin/features', 'Admin\FeatureController::index', ['filter' => ['auth:true', 'feature:fitur.read']]);
$routes->post('admin/features/list', 'Admin\FeatureController::list', ['filter' => ['auth:true', 'feature:fitur.read']]);
// Create Fitur
$routes->get('admin/features/create', 'Admin\FeatureController::create', ['filter' => ['auth:true', 'feature:fitur.create']]);
$routes->post('admin/features/save', 'Admin\FeatureController::save', ['filter' => ['auth:true', 'feature:fitur.create']]);
// Update Fitur
$routes->get('admin/features/edit/(:num)', 'Admin\FeatureController::edit/$1', ['filter' => ['auth:true', 'feature:fitur.update']]);
$routes->post('admin/features/update/(:num)', 'Admin\FeatureController::update/$1', ['filter' => ['auth:true', 'feature:fitur.update']]);
// Delete Fitur
$routes->post('admin/features/delete', 'Admin\FeatureController::delete', ['filter' => ['auth:true', 'feature:fitur.delete']]);
// Detail Fitur
$routes->get('admin/features/detail/(:num)', 'Admin\FeatureController::detail/$1', ['filter' => ['auth:true', 'feature:fitur.detail']]);
// List Prerequisite Fitur
$routes->post('admin/features/prerequisite/list/(:num)', 'Admin\FeatureController::prerequisiteList/$1', ['filter' => ['auth:true', 'feature:fitur.read']]);
// Save Prerequisite Fitur
$routes->post('admin/features/prerequisite/save', 'Admin\FeatureController::prerequisiteSave', ['filter' => ['auth:true', 'feature:fitur.add.syarat']]);
// Delete Prerequisite Fitur
$routes->post('admin/features/prerequisite/delete', 'Admin\FeatureController::prerequisiteDelete', ['filter' => ['auth:true', 'feature:fitur.remove.syarat']]);
// Toggle Maintenance Mode
$routes->post('admin/features/toggle-maintenance', 'Admin\FeatureController::toggleMaintenance', ['filter' => ['auth:true', 'feature:fitur.update.maintenance']]);

// Manajemen Peran (Roles)
// Read Peran
$routes->get('admin/roles', 'Admin\RoleController::index', ['filter' => ['auth:true', 'feature:peran.read']]);
$routes->post('admin/roles/list', 'Admin\RoleController::list', ['filter' => ['auth:true', 'feature:peran.read']]);
// Create Peran
$routes->get('admin/roles/create', 'Admin\RoleController::create', ['filter' => ['auth:true', 'feature:peran.create']]);
$routes->post('admin/roles/save', 'Admin\RoleController::save', ['filter' => ['auth:true', 'feature:peran.create']]);
// Update Peran
$routes->get('admin/roles/edit/(:num)', 'Admin\RoleController::edit/$1', ['filter' => ['auth:true', 'feature:peran.update']]);
$routes->post('admin/roles/update/(:num)', 'Admin\RoleController::update/$1', ['filter' => ['auth:true', 'feature:peran.update']]);
// Delete Peran
$routes->post('admin/roles/delete', 'Admin\RoleController::delete', ['filter' => ['auth:true', 'feature:peran.delete']]);
// Detail Peran & Manajemen Fitur
$routes->get('admin/roles/detail/(:num)', 'Admin\RoleController::detail/$1', ['filter' => ['auth:true', 'feature:peran.detail']]);
// Sync Fitur Peran
$routes->post('admin/roles/feature/sync', 'Admin\RoleController::featureSync', ['filter' => ['auth:true', 'feature:peran.sinkronisasi.fitur']]);

$routes->get('/sholat', 'SholatController::index');
$routes->get('tv', 'TvController::index');
$routes->group('api/tv', static function ($routes) {
    $routes->get('prayer', 'Api\TvApiController::getPrayer');
    $routes->get('jadwal', 'Api\TvApiController::getJadwal');
    $routes->get('saldo', 'Api\TvApiController::getSaldo');
    $routes->get('carousel', 'Api\TvApiController::getCarousel');

    $routes->get('info', 'Api\TvApiController::getInfo');
});
