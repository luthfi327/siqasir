<?php

// routes/web.php
// STATUS: MODIFIKASI
// Perubahan GAP 2:
//   - Tambah use OwnerDataController
//   - Tambah 9 route baru di dalam group middleware 'owner.web':
//     GET /owner/data/dashboard-stats
//     GET /owner/data/penjualan
//     GET /owner/data/penjualan/{invoice}/detail
//     GET /owner/data/produk
//     GET /owner/data/stok
//     GET /owner/data/laporan-kas
//     GET /owner/data/pelanggan
//     GET /owner/data/laporan-penjualan
//     GET /owner/data/toko-list

use Illuminate\Support\Facades\Route;

// ── Controller Admin ──────────────────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminWebAuthController;
use App\Http\Controllers\Admin\AdminWebDashboardController;
use App\Http\Controllers\Admin\AdminWebUserController;
use App\Http\Controllers\Admin\AdminWebPromoController;
use App\Http\Controllers\Admin\AdminWebPaymentController;

// ── Controller Owner ──────────────────────────────────────────────────────────
use App\Http\Controllers\Web\OwnerWebController;
use App\Http\Controllers\Web\OwnerDataController; // [GAP 2] BARU

Route::get('/', function () {
    return view('welcome');
});

// ============================================================
// PANEL WEB ADMIN PYRAMIDSOFT — /admin/...
//
// Session key : admin_web_id
// Middleware  : admin.web (AdminWebAuthenticate)
// ============================================================

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return session('admin_web_id')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });

    // Autentikasi (publik)
    Route::get('/login',  [AdminWebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminWebAuthController::class, 'login'])->name('login.submit');

    // Route yang dilindungi session
    Route::middleware('admin.web')->group(function () {

        Route::post('/logout', [AdminWebAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [AdminWebDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users',              [AdminWebUserController::class, 'index'])->name('users.index');
        Route::get('/users/{kode_owner}', [AdminWebUserController::class, 'show'])->name('users.show');

        Route::get('/promos',                   [AdminWebPromoController::class, 'index'])->name('promos.index');
        Route::post('/promos',                  [AdminWebPromoController::class, 'store'])->name('promos.store');
        Route::post('/promos/{id}/nonaktifkan', [AdminWebPromoController::class, 'nonaktifkan'])->name('promos.nonaktifkan');

        // PENTING: /payments/export HARUS sebelum /payments
        Route::get('/payments/export', [AdminWebPaymentController::class, 'export'])->name('payments.export');
        Route::get('/payments',        [AdminWebPaymentController::class, 'index'])->name('payments.index');
    });
});


// ============================================================
// PORTAL OWNER TOKO — /owner/...
//
// Session key : owner_toko (array asosiatif)
// Middleware  : owner.web (OwnerWebAuthenticate)
// ============================================================

// Login/logout publik (di luar middleware)
Route::get('/owner/login',   [OwnerWebController::class, 'loginForm'])->name('owner.login');
Route::post('/owner/login',  [OwnerWebController::class, 'loginPost'])->name('owner.login.post');
Route::post('/owner/logout', [OwnerWebController::class, 'logout'])->name('owner.logout');

Route::middleware('owner.web')->prefix('owner')->group(function () {

    // ── Halaman utama ──────────────────────────────────────────────────────
    Route::get('/dashboard',            [OwnerWebController::class, 'dashboard'])->name('owner.dashboard');
    Route::get('/payments',             [OwnerWebController::class, 'payments'])->name('owner.payments');
    Route::get('/pegawai',              [OwnerWebController::class, 'pegawai'])->name('owner.pegawai');
    Route::post('/pegawai/{id}/toggle', [OwnerWebController::class, 'pegawaiToggle'])->name('owner.pegawai.toggle');
    Route::get('/promo',                [OwnerWebController::class, 'promo'])->name('owner.promo');

    // ── Halaman dari GAP 3-7 (method di OwnerWebController) ──────────────────
    Route::get('/penjualan', [OwnerWebController::class, 'penjualan'])->name('owner.penjualan');
    Route::get('/produk',    [OwnerWebController::class, 'produk'])->name('owner.produk');
    Route::get('/laporan',   [OwnerWebController::class, 'laporan'])->name('owner.laporan');
    Route::get('/kas-keluar', [OwnerWebController::class, 'kasKeluar'])->name('owner.kas-keluar');

    // ── [GAP 2] Data dari SQLite — endpoint AJAX untuk semua halaman owner ─
    // PENTING: route /data/* harus didaftarkan SEBELUM route halaman lain
    // yang mungkin punya pola konflik. Semua endpoint ini return JSON.
    Route::prefix('data')->name('owner.data.')->group(function () {

        // 2a — Statistik dashboard (omzet, transaksi, stok, pelanggan)
        Route::get('/dashboard-stats',           [OwnerDataController::class, 'dashboardStats'])->name('dashboard-stats');

        // 2b — Daftar penjualan dengan filter tanggal & id_toko
        Route::get('/penjualan',                 [OwnerDataController::class, 'penjualan'])->name('penjualan');

        // 2c — Detail satu transaksi berdasarkan invoice
        // PENTING: route /penjualan/{invoice}/detail HARUS setelah /penjualan
        Route::get('/penjualan/{invoice}/detail', [OwnerDataController::class, 'detailPenjualan'])->name('penjualan.detail');

        // 2d — Daftar produk dengan filter kategori, search, stok_filter
        Route::get('/produk',                    [OwnerDataController::class, 'produk'])->name('produk');

        // 2e — Stok produk dengan highlight hampir habis
        Route::get('/stok',                      [OwnerDataController::class, 'stok'])->name('stok');

        // 2f — Laporan kas (riwayat buka-tutup kasir + rekapitulasi)
        Route::get('/laporan-kas',               [OwnerDataController::class, 'laporanKas'])->name('laporan-kas');

        // 2g — Rincian kas keluar dengan filter tanggal & outlet
        Route::get('/kas-keluar',                [OwnerDataController::class, 'kasKeluar'])->name('kas-keluar');

        // 2h — Laporan penjualan per bulan (per hari, per kasir, produk terlaris)
        Route::get('/laporan-penjualan',         [OwnerDataController::class, 'laporanPenjualan'])->name('laporan-penjualan');

        // Bonus — Daftar outlet/cabang dari SQLite (untuk dropdown filter)
        Route::get('/toko-list',                 [OwnerDataController::class, 'tokoList'])->name('toko-list');
    });
});