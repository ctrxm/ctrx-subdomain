<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Rute Publik & Landing Page
// --------------------------------------------------------------------
$routes->get('/', 'Home::index');


// --------------------------------------------------------------------
// Rute Sistem Lisensi (Harus bisa diakses publik)
// --------------------------------------------------------------------
// Rute ini diperlukan agar pengguna bisa diarahkan ke halaman aktivasi
// atau halaman invalid saat lisensi mereka bermasalah.
$routes->get('/activate', 'LicenseController::activate');
$routes->post('/activate/process', 'LicenseController::processActivation');
$routes->get('/invalid', 'LicenseController::invalid');

// ROUTES INTALLER
$routes->get('/install', 'InstallController::index');
$routes->post('/install/process', 'InstallController::process');

//ROUTES TEST DB
$routes->post('/install/testdb', 'InstallController::testDB');

// --------------------------------------------------------------------
// Rute Autentikasi Bawaan Shield
// --------------------------------------------------------------------
// Ini akan secara otomatis membuat rute seperti /login, /register, 
// /logout, /forgot, dll.
$routes->get('/logout', 'AuthController::logout');

service('auth')->routes($routes);


// --------------------------------------------------------------------
// Rute Dashboard Pengguna (Memerlukan Login)
// --------------------------------------------------------------------
// Semua rute di dalam grup ini akan otomatis dilindungi oleh filter 'auth'
// dari Shield, yang memastikan hanya pengguna yang sudah login bisa mengaksesnya.
$routes->group('dashboard', ['filter' => 'auth'], static function ($routes) {
    // Halaman utama dashboard
    $routes->get('/', 'DashboardController::index');
    
    // Rute untuk memproses form pembuatan subdomain
    $routes->post('subdomain/create', 'DashboardController::createSubdomain');
    
    // Rute untuk menghapus subdomain (contoh)
    $routes->post('subdomain/delete/(:segment)', 'DashboardController::deleteSubdomain/$1');
});


// --------------------------------------------------------------------
// Rute Panel Admin (Memerlukan Login & Status Admin)
// --------------------------------------------------------------------
// Grup ini dilindungi oleh dua filter: 'auth' untuk login, dan 'admin'
// untuk memastikan hanya user dengan status admin yang bisa masuk.
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    // Halaman utama admin (bisa berupa statistik)
    $routes->get('/', 'Admin\DashboardController::index');

    // Mengelola domain dasar
    $routes->get('domains', 'Admin\DomainController::index');
    $routes->get('domains/create', 'Admin\DomainController::create');
    $routes->post('domains/store', 'Admin\DomainController::store');
    $routes->post('domains/delete/(:num)', 'Admin\DomainController::delete/$1');

    // Mengelola pengguna (contoh)
    $routes->get('users', 'Admin\UserController::index');
});

