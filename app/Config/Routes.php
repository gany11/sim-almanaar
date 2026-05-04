<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Login (Admin)
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

// Halaman Profil & Update Profil (Admin)
$routes->get('admin/profile', 'Admin\ProfileController::index', ['filter' => 'auth:true']);
$routes->post('admin/profile/update', 'Admin\ProfileController::updateProfile', ['filter' => 'auth:true']);

// Registrasi Akun (Admin)
$routes->get('admin/account/register', 'Admin\AccountController::register', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/save', 'Admin\AccountController::save', ['filter' => ['auth:true', 'role:1,2']]);

// Manajemen Akun (Admin)
$routes->get('admin/account', 'Admin\AccountController::index', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/list', 'Admin\AccountController::list', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/account/status', 'Admin\AccountController::updateStatus', ['filter' => ['auth:true', 'role:1,2']]);

// Berita (Admin)
$routes->get('admin/news', 'Admin\NewsController::index', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->post('admin/news/list', 'Admin\NewsController::list', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->get('admin/news/create', 'Admin\NewsController::create', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/news/save', 'Admin\NewsController::save', ['filter' => 'auth:true,role:1,3']);
$routes->get('admin/news/edit/(:num)', 'Admin\NewsController::edit/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/news/update/(:num)', 'Admin\NewsController::update/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/news/delete', 'Admin\NewsController::delete', ['filter' => 'auth:true,role:1,3']);

// Artikel (Admin)
$routes->get('admin/article', 'Admin\ArticleController::index', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->post('admin/article/list', 'Admin\ArticleController::list', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->get('admin/article/create', 'Admin\ArticleController::create', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/article/save', 'Admin\ArticleController::save', ['filter' => 'auth:true,role:1,3']);
$routes->get('admin/article/edit/(:num)', 'Admin\ArticleController::edit/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/article/update/(:num)', 'Admin\ArticleController::update/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/article/delete', 'Admin\ArticleController::delete', ['filter' => 'auth:true,role:1,3']);

// Finance - Keuangan Rutin
$routes->get('admin/finance/routine', 'Admin\FinanceController::index', ['filter' => 'auth:true,role:1,2,3']);
$routes->post('admin/finance/routine/list', 'Admin\FinanceController::list', ['filter' => 'auth:true,role:1,2,3']);
$routes->get('admin/finance/routine/create', 'Admin\FinanceController::create', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/finance/routine/save', 'Admin\FinanceController::save', ['filter' => 'auth:true,role:1,3']);
$routes->get('admin/finance/routine/edit/(:num)', 'Admin\FinanceController::edit/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/finance/routine/update/(:num)', 'Admin\FinanceController::update/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/finance/routine/delete', 'Admin\FinanceController::delete', ['filter' => 'auth:true,role:1,3']);

// Route Detail Laporan Mingguan
$routes->get('admin/finance/report/detail/(:num)', 'Admin\ReportController::detail/$1', ['filter' => 'auth:true,role:1,3']);

// Route Edit Catatan Laporan
$routes->get('admin/finance/report/edit-note/(:num)', 'Admin\ReportController::editNote/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/finance/report/update-note', 'Admin\ReportController::updateNote', ['filter' => 'auth:true,role:1,3']);

// Route BARU: Laporan Periodik (Range Date)
$routes->get('admin/finance/report/periodic', 'Admin\ReportController::periodic', ['filter' => 'auth:true,role:1,3']);

// Agenda
$routes->get('admin/agenda', 'Admin\AgendaController::index', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/agenda/list', 'Admin\AgendaController::list', ['filter' => 'auth:true,role:1,3']);
$routes->get('admin/agenda/create', 'Admin\AgendaController::create', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/agenda/save', 'Admin\AgendaController::save', ['filter' => 'auth:true,role:1,3']);
$routes->get('admin/agenda/edit/(:num)', 'Admin\AgendaController::edit/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/agenda/update/(:num)', 'Admin\AgendaController::update/$1', ['filter' => 'auth:true,role:1,3']);
$routes->post('admin/agenda/delete', 'Admin\AgendaController::delete', ['filter' => 'auth:true,role:1,3']);

//
$routes->get('/sholat', 'SholatController::index');
