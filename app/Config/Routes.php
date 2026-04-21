<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('admin/dashboard', 'Home::index', ['filter' => 'auth:true']);

// Login (Admin)
$routes->get('admin/login', 'AuthController::index', ['filter' => 'auth:false']);
$routes->post('admin/login/in', 'AuthController::login', ['filter' => 'auth:false']);

// Lupa Password (Admin)
$routes->get('admin/forgot-password', 'ForgotController::index', ['filter' => 'auth:false']);
$routes->post('admin/forgot-password/save', 'ForgotController::sendResetLink', ['filter' => 'auth:false']);
$routes->get('admin/reset-password/(:any)', 'ForgotController::resetPassword/$1', ['filter' => 'auth:false']);
$routes->post('admin/reset-password/update', 'ForgotController::updatePassword', ['filter' => 'auth:false']);

// Dashboard
// $routes->get('admin/dashboard', 'Admin/Dashboard::index', ['filter' => 'auth:true']);

// Logout (Admin)
$routes->get('admin/logout', 'AuthController::logout', ['filter' => 'auth:true']);

// Update Password (Admin)
$routes->post('admin/profil/update-password', 'Admin\ProfilController::updatePassword', ['filter' => 'auth:true']);

// Halaman Profil & Update Profil (Admin)
$routes->get('admin/profil', 'Admin\ProfilController::index', ['filter' => 'auth:true']);
$routes->post('admin/profil/update', 'Admin\ProfilController::updateProfil', ['filter' => 'auth:true']);

// Registrasi Akun (Admin)
$routes->get('admin/akun/registrasi', 'Admin\AkunController::registrasi', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/akun/save', 'Admin\AkunController::save', ['filter' => ['auth:true', 'role:1,2']]);

// Manajemen Akun (Admin)
$routes->get('admin/akun', 'Admin\AkunController::index', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/akun/list', 'Admin\AkunController::list', ['filter' => ['auth:true', 'role:1,2']]);
$routes->post('admin/akun/status', 'Admin\AkunController::updateStatus', ['filter' => ['auth:true', 'role:1,2']]);

// Berita (Admin)
$routes->get('admin/berita', 'Admin\BeritaController::index', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->post('admin/berita/list', 'Admin\BeritaController::list', ['filter' => ['auth:true', 'role:1,2,3']]);
$routes->get('admin/berita/tambah', 'Admin\BeritaController::create', ['filter' => ['auth:true', 'role:1,3']]);
$routes->post('admin/berita/save', 'Admin\BeritaController::save', ['filter' => ['auth:true', 'role:1,3']]);
$routes->get('admin/berita/edit/(:num)', 'Admin\BeritaController::edit/$1', ['filter' => ['auth:true', 'role:1,3']]);
$routes->post('admin/berita/update/(:num)', 'Admin\BeritaController::update/$1', ['filter' => ['auth:true', 'role:1,3']]);
$routes->get('admin/berita/delete/(:num)', 'Admin\BeritaController::delete/$1', ['filter' => ['auth:true', 'role:1,3']]);

$routes->get('/sholat', 'SholatController::index');
