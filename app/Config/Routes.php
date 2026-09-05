<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
*/

// $routes->get('/', 'Home::index');

/**
 * Home / Landing Page
 */
$routes->get('/', 'Landing\HomeController::index');

/**
 * Finance Reports (DataTables)
 */
$routes->get('keuangan', 'Landing\FinanceController::index');
// $routes->get('keuangan/(:num)/(:num)/(:num)', 'Landing\FinanceController::detail/$1/$2/$3');
// Iterasi 2
$routes->get('keuangan/(:num)/(:num)', 'Landing\FinanceController::detail/$1/$2');
$routes->get('keuangan/getMonthlyReportAjax', 'Landing\FinanceController::getMonthlyReportAjax', ['filter' => 'apiGuard']);

/**
 * Donasi
 */
$routes->get('donasi', 'Landing\DonationController::index');
$routes->get('donasi/detail/(:num)', 'Landing\DonationController::detail/$1');

/**
 * Donatur
 */
$routes->get('donatur', 'Landing\DonorController::index');
$routes->get('donatur/(:num)/(:segment)', 'Landing\DonorController::access/$1/$2');
$routes->post('donatur/login', 'Landing\DonorController::login');
$routes->post('donatur/pin', 'Landing\DonorController::setPin');
$routes->get('donatur/lupa-pin', 'Landing\DonorController::forgotPin');
$routes->post('donatur/lupa-pin', 'Landing\DonorController::requestResetPin');
$routes->get('donatur/reset-pin/(:segment)', 'Landing\DonorController::resetPin/$1');
$routes->post('donatur/reset-pin/(:segment)', 'Landing\DonorController::updatePin/$1');
$routes->get('donatur/logout', 'Landing\DonorController::logout');

/**
 * Agenda (FullCalendar)
 */
$routes->get('agenda', 'Landing\AgendaController::index');
$routes->get('api/agenda', 'Landing\AgendaController::getEvents', ['filter' => 'apiGuard']);

/**
 * News (Berita)
 */
$routes->get('berita', 'Landing\NewsController::index');
$routes->get('berita/(:num)/(:segment)', 'Landing\NewsController::detail/$1/$2');

/**
 * Articles (Artikel)
 */
$routes->get('artikel', 'Landing\ArticleController::index');
$routes->get('artikel/(:num)/(:segment)', 'Landing\ArticleController::detail/$1/$2');

// Login (Admin)
$routes->get('admin', static function () {
    return redirect()->to('/admin/login');
});
$routes->get('admin/login', 'AuthController::index', ['filter' => 'auth:false']);
$routes->post('admin/login/in', 'AuthController::login', ['filter' => 'auth:false']);

// Lupa Password (Admin)
$routes->get('admin/forgot-password', 'ForgotController::index', ['filter' => 'auth:false']);
$routes->post('admin/forgot-password/save', 'ForgotController::sendResetLink', ['filter' => 'auth:false']);
$routes->get('admin/reset-password/(:any)', 'ForgotController::resetPassword/$1', ['filter' => 'auth:false']);
$routes->post('admin/reset-password/update', 'ForgotController::updatePassword', ['filter' => 'auth:false']);

// Dashboard
$routes->get('admin/dashboard', 'Admin\DashboardController::index', ['filter' => 'auth:true']);

// Logout (Admin)
$routes->get('admin/logout', 'AuthController::logout', ['filter' => 'auth:true']);

// Update Password (Admin)
$routes->post('admin/profile/update-password', 'Admin\ProfileController::updatePassword', ['filter' => 'auth:true']);

// Profil
$routes->get('admin/profile', 'Admin\ProfileController::index', ['filter' => 'auth:true']);
$routes->post('admin/profile/update', 'Admin\ProfileController::updateProfile', ['filter' => 'auth:true']);

// Registrasi Akun
$routes->get('admin/account/register', 'Admin\AccountController::register', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/save', 'Admin\AccountController::save', ['filter' => ['auth:true', 'role:1,2']]);

// Manajemen Akun
$routes->get('admin/account', 'Admin\AccountController::index', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/list', 'Admin\AccountController::list', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/status', 'Admin\AccountController::updateStatus', ['filter' => ['auth:true', 'role:1,2']]);

// Whatsapp
$routes->get('admin/whatsapp', 'Admin\WhatsAppController::index', ['filter' => ['auth:true', 'role:1']]);
$routes->get('admin/whatsapp/status', 'Admin\WhatsAppController::status', ['filter' => ['auth:true', 'role:1']]);
$routes->get('admin/whatsapp/qr', 'Admin\WhatsAppController::qr', ['filter' => ['auth:true', 'role:1']]);
$routes->get('admin/whatsapp/groups', 'Admin\WhatsAppController::group', ['filter' => ['auth:true', 'role:1']]);
$routes->post('admin/whatsapp/logout', 'Admin\WhatsAppController::logout', ['filter' => ['auth:true', 'role:1']]);

// ===================== NEWS =====================
$routes->get('admin/news', 'Admin\NewsController::index', ['filter' => ['auth:true', 'role:2,3']]);
$routes->post('admin/news/list', 'Admin\NewsController::list', ['filter' => ['auth:true', 'role:2,3']]);

$routes->get('admin/news/create', 'Admin\NewsController::create', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/news/save', 'Admin\NewsController::save', ['filter' => ['auth:true', 'role:3']]);
$routes->get('admin/news/edit/(:num)', 'Admin\NewsController::edit/$1', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/news/update/(:num)', 'Admin\NewsController::update/$1', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/news/delete', 'Admin\NewsController::delete', ['filter' => ['auth:true', 'role:3']]);

// ===================== ARTICLE =====================
$routes->get('admin/article', 'Admin\ArticleController::index', ['filter' => ['auth:true', 'role:2,3']]);
$routes->post('admin/article/list', 'Admin\ArticleController::list', ['filter' => ['auth:true', 'role:2,3']]);

$routes->get('admin/article/create', 'Admin\ArticleController::create', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/article/save', 'Admin\ArticleController::save', ['filter' => ['auth:true', 'role:3']]);
$routes->get('admin/article/edit/(:num)', 'Admin\ArticleController::edit/$1', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/article/update/(:num)', 'Admin\ArticleController::update/$1', ['filter' => ['auth:true', 'role:3']]);
$routes->post('admin/article/delete', 'Admin\ArticleController::delete', ['filter' => ['auth:true', 'role:3']]);

// ===================== FINANCE =====================
$routes->get('admin/finance/data', 'Admin\FinanceController::index', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/finance/data/list', 'Admin\FinanceController::list', ['filter' => ['auth:true', 'role:4']]);

$routes->get('admin/finance/data/create', 'Admin\FinanceController::create', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/finance/data/save', 'Admin\FinanceController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/finance/data/edit/(:num)', 'Admin\FinanceController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/finance/data/update/(:num)', 'Admin\FinanceController::update/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/finance/data/delete', 'Admin\FinanceController::delete', ['filter' => ['auth:true', 'role:4']]);

// Iterasi 2
$routes->post('admin/finance/get-detail-alokasi', 'Admin\FinanceController::getDetailAlokasi', ['filter' => 'apiGuard']);
$routes->post('admin/finance/import-excel', 'Admin\FinanceController::importExcel', ['filter' => ['auth:true', 'role:4']]);

// Report
$routes->get('admin/finance/report/weekly/(:num)', 'Admin\ReportController::detail/$1', ['filter' => ['auth:true', 'role:2,3,4']]);

$routes->get('admin/finance/report/edit-note/(:num)', 'Admin\ReportController::editNote/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/finance/report/update-note', 'Admin\ReportController::updateNote', ['filter' => ['auth:true', 'role:4']]);

$routes->add('admin/finance/report/periodic', 'Admin\ReportController::periodic', ['filter' => ['auth:true', 'role:4']]);

//Iterasi 3
$routes->get('admin/finance/report/weekly', 'Admin\ReportController::weekly', ['filter' => ['auth:true', 'role:2,3,4']]);
$routes->get('admin/finance/report/monthly', 'Admin\ReportController::monthly', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/finance/report/monthly/(:num)/(:num)', 'Admin\ReportController::detailMonthly/$1/$2', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/finance/report/getWeeklyHistoryAjax', 'Admin\ReportController::getWeeklyHistoryAjax', ['filter' => 'apiGuard']);
$routes->get('admin/finance/report/chart', 'Admin\ReportController::chart', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/report/chart/detail-alokasi', 'Admin\ReportController::getDetailAlokasi', ['filter' => 'apiGuard']);
$routes->get('admin/report/chart/chart-data', 'Admin\ReportController::getChartData', ['filter' => 'apiGuard']);

// ===================== DONOR (DONATUR) =====================
$routes->get('admin/donors', 'Admin\DonorController::index', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donors/list', 'Admin\DonorController::list', ['filter' => ['auth:true', 'role:4']]);

$routes->get('admin/donors/detail/(:num)', 'Admin\DonorController::detail/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donors/create', 'Admin\DonorController::create', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donors/save', 'Admin\DonorController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donors/edit/(:num)', 'Admin\DonorController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donors/update/(:num)', 'Admin\DonorController::update/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donors/delete', 'Admin\DonorController::delete', ['filter' => ['auth:true', 'role:4']]);

// ===================== DONATION PROGRAMS (DONASI) =====================
$routes->get('admin/donations', 'Admin\DonationController::index', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donations/list', 'Admin\DonationController::list', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donations/partial-detail/(:num)', 'Admin\DonationController::partialDetail/$1', ['filter' => ['auth:true', 'role:4']]);

$routes->get('admin/donations/detail/(:num)', 'Admin\DonationController::detail/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donations/create', 'Admin\DonationController::create', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donations/save', 'Admin\DonationController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donations/edit/(:num)', 'Admin\DonationController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donations/update/(:num)', 'Admin\DonationController::update/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donations/delete', 'Admin\DonationController::delete', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donations/close', 'Admin\DonationController::close', ['filter' => ['auth:true', 'role:4']]);

// ===================== DONATION EXPENSES (PENGELUARAN DONASI) =====================
$routes->get('admin/donation-expenses/create', 'Admin\DonationExpenseController::create', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donation-expenses/create/(:num)', 'Admin\DonationExpenseController::create/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-expenses/save', 'Admin\DonationExpenseController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donation-expenses/edit/(:num)', 'Admin\DonationExpenseController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-expenses/update/(:num)', 'Admin\DonationExpenseController::update/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-expenses/delete', 'Admin\DonationExpenseController::delete', ['filter' => ['auth:true', 'role:4']]);

// ===================== DONATION DONOR (Calon Donatur Donasi) =====================
$routes->get('admin/donation-donors/create/(:num)', 'Admin\DonationDonorController::create/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donation-donors/edit/(:num)', 'Admin\DonationDonorController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-donors/save', 'Admin\DonationDonorController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-donors/update/(:num)', 'Admin\DonationDonorController::update/$1', ['filter' => ['auth:true', 'role:4']]);

$routes->get('admin/donation-donors/get-status-options/(:num)', 'Admin\DonationDonorController::getStatusOptions/$1', ['filter' => 'apiGuard']);
$routes->post('admin/donation-donors/update-status/(:num)', 'Admin\DonationDonorController::updateStatus/$1', ['filter' => ['auth:true', 'role:4']]);

// ==========================================
// RUTE TERPUSAT PEMASUKAN & DONASI (DonationIncomeController)
// ==========================================
$routes->get('admin/donation-incomes/record/(:num)', 'Admin\DonationIncomeController::record/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donors/record/(:num)', 'Admin\DonationIncomeController::donorRecord/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donation-incomes/create/(:num)', 'Admin\DonationIncomeController::create/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->get('admin/donation-incomes/edit/(:num)', 'Admin\DonationIncomeController::edit/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-incomes/save', 'Admin\DonationIncomeController::save', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-incomes/update/(:num)', 'Admin\DonationIncomeController::update/$1', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-incomes/delete', 'Admin\DonationIncomeController::delete', ['filter' => ['auth:true', 'role:4']]);
$routes->post('admin/donation-incomes/toggle-samarkan', 'Admin\DonationIncomeController::toggleSamarkan', ['filter' => ['auth:true', 'role:4']]);

// ===================== AGENDA =====================
$routes->get('admin/agenda', 'Admin\AgendaController::index', ['filter' => ['auth:true', 'role:2,3,5']]);
$routes->post('admin/agenda/list', 'Admin\AgendaController::list', ['filter' => ['auth:true', 'role:2,3,5']]);

$routes->get('admin/agenda/create', 'Admin\AgendaController::create', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/agenda/save', 'Admin\AgendaController::save', ['filter' => ['auth:true', 'role:3,5']]);
$routes->get('admin/agenda/edit/(:num)', 'Admin\AgendaController::edit/$1', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/agenda/update/(:num)', 'Admin\AgendaController::update/$1', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/agenda/delete', 'Admin\AgendaController::delete', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/agenda/import-excel', 'Admin\AgendaController::importExcel', ['filter' => ['auth:true', 'role:3,5']]);

// ===================== ROUTINE AGENDA =====================
$routes->get('admin/routine-agenda', 'Admin\RoutineAgendaController::index', ['filter' => ['auth:true', 'role:2,3,5']]);
$routes->post('admin/routine-agenda/list', 'Admin\RoutineAgendaController::list', ['filter' => ['auth:true', 'role:2,3,5']]);

$routes->get('admin/routine-agenda/create', 'Admin\RoutineAgendaController::create', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/routine-agenda/save', 'Admin\RoutineAgendaController::save', ['filter' => ['auth:true', 'role:3,5']]);
$routes->get('admin/routine-agenda/edit/(:num)', 'Admin\RoutineAgendaController::edit/$1', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/routine-agenda/update/(:num)', 'Admin\RoutineAgendaController::update/$1', ['filter' => ['auth:true', 'role:3,5']]);
$routes->post('admin/routine-agenda/delete', 'Admin\RoutineAgendaController::delete', ['filter' => ['auth:true', 'role:3,5']]);

$routes->get('/sholat', 'SholatController::index');
