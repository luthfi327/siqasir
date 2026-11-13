<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\QrisController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminPromoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ============================================================
// ROUTES LAMA — Dilindungi middleware 'android.key'
//
// Semua endpoint berikut memerlukan header:
//   X-Android-Key: {nilai ANDROID_API_KEY dari .env}
//
// Kenapa android.key bukan auth.admin?
//   Endpoint ini digunakan oleh aplikasi Android itu sendiri — bukan
//   oleh admin. Tidak ada sesi per-user saat proses registrasi atau
//   sinkronisasi awal, sehingga Bearer token per-user tidak cocok.
//   API key statis per-aplikasi adalah kompromi yang tepat:
//   - Mencegah akses publik anonim (scraping, spam, data leak)
//   - Tidak mengubah alur registrasi Android secara signifikan
//   - Mudah dirotasi jika key bocor (update .env + rilis APK baru)
//
// CATATAN UNTUK PENGEMBANG ANDROID:
//   Tambahkan header berikut ke semua request OkHttp/Volley:
//     .addHeader("X-Android-Key", BuildConfig.ANDROID_API_KEY)
//   atau tambahkan di OkHttp interceptor global via ApiKeyHttpClient.java
// ============================================================

Route::middleware('android.key')->group(function () {

    // ----------------------------------------------------------
    // Users (CRUD pegawai & toko)
    // ----------------------------------------------------------
    Route::prefix('users')->group(function () {
        Route::get('/',       [UserController::class, 'index']);
        Route::post('/',      [UserController::class, 'store']);
        Route::get('{id}',    [UserController::class, 'show']);
        Route::put('{id}',    [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy']);

        /**
         * POST /api/users/update-credential
         *
         * Update password atau PIN dari Android setelah user ganti credential
         * melalui fitur "Ganti Password" / "Ganti PIN" (ResetPswdPinAdmin.java).
         *
         * PENTING: Route ini harus didaftarkan di DALAM prefix 'users' dan
         * SEBELUM '{id}' agar tidak salah tangkap sebagai route parameter
         * (jika ada). Karena tidak ada overlap dengan {id} integer di sini,
         * urutannya sudah benar.
         *
         * Body: kode_pegawai, type (password|pin), current_value, new_value
         *
         * FASE 6 — Sinkronisasi ganti password/PIN Android ke server.
         */
        Route::post('update-credential', [UserController::class, 'updateCredential']);
    });

    Route::post('/check-exp-date',       [UserController::class, 'check_exp_date']);

    /**
     * GET /api/toko/status?kode_owner=X&kode_users=Y
     *
     * PERUBAHAN GAP 5:
     * Parameter kode_users ditambahkan (opsional).
     * Server sekarang mengembalikan hak_akses dari DB bukan null,
     * digunakan Android untuk verifikasi role anti-bypass.
     */
    Route::get('/toko/status',           [UserController::class, 'get_status_toko']);

    Route::post('/delete-by-kode-owner', [UserController::class, 'delete_by_kode_owner']);

    // ----------------------------------------------------------
    // Upload & Sinkronisasi database SQLite
    // ----------------------------------------------------------
    Route::post('/check-username',       [UploadController::class, 'checkUsername']);
    Route::post('/upload-db',            [UploadController::class, 'uploadDatabase']);
    Route::post('/sync/data-compare',    [UploadController::class, 'syncDataCompare']);

    // ----------------------------------------------------------
    // Merge, Download, Clean
    // ----------------------------------------------------------
    Route::get('/merge/{kodeOwner}',     [MergeController::class, 'mergeOwner']);
    Route::post('/download-owner-db',    [DownloadController::class, 'downloadOwnerDatabase']);
    Route::post('/download-db-login',    [DownloadController::class, 'downloadOwnerDatabaseWithLogin']);
    Route::get('/clean/{kodeOwner}',     [CleanController::class, 'cleanOwner']);

    // ----------------------------------------------------------
    // OTP & Verifikasi Email
    // ----------------------------------------------------------
    Route::post('/otp/check-email',      [OtpController::class, 'checkEmail']);
    Route::post('/otp/send',             [OtpController::class, 'sendOtp']);
    Route::post('/otp/activate',         [OtpController::class, 'activateEmail']);
    Route::post('/otp/getusers',         [OtpController::class, 'getUsersByEmail']);

    // ----------------------------------------------------------
    // QRIS / Pembayaran (endpoint yang dipanggil Android)
    // ----------------------------------------------------------
    Route::post('/qris/create',                     [QrisController::class, 'create']);
    Route::get('/qris/status',                      [QrisController::class, 'status']);
    Route::post('/qris/cancel/{external_id}',       [QrisController::class, 'cancel']);
    Route::get('/payment/first-time/{kode_owner}',  [QrisController::class, 'isFirstTimePayment']);
    Route::get('/payment/get-pricing/{kode_owner}', [QrisController::class, 'getPricing']);

    /**
     * [GAP 3] GET /api/payment/history/{kode_owner}
     * Mengembalikan ARRAY semua riwayat pembayaran owner, urut DESC.
     * Menggantikan penggunaan /api/qris/status yang hanya return 1 record.
     */
    Route::get('/payment/history/{kode_owner}',     [QrisController::class, 'paymentHistory']);

});

// ============================================================
// ENDPOINT KHUSUS — Tidak memerlukan autentikasi apapun
// ============================================================
Route::get('/otp/verify/{key}', [OtpController::class, 'verifyEmail']);
Route::post('/qris/callback',   [QrisController::class, 'callback']);


// ============================================================
// ROUTES ADMIN — dilindungi middleware 'auth.admin'
// ============================================================

Route::prefix('admin')->group(function () {

    /**
     * POST /api/admin/login
     * Login admin menggunakan username + password.
     * Tidak memerlukan token.
     */
    Route::post('/login', [AdminAuthController::class, 'login']);

    /**
     * Semua route di dalam group ini dilindungi middleware 'auth.admin'.
     * Setiap request WAJIB menyertakan header:
     *   Authorization: Bearer {token_dari_login}
     */
    Route::middleware('auth.admin')->group(function () {

        // ── Auth ──────────────────────────────────────────────────────────
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me',      [AdminAuthController::class, 'me']);

        // ── User & Toko Monitoring ────────────────────────────────────────

        /**
         * PENTING: 'summary' harus didaftarkan SEBELUM '{kode_owner}'
         * agar tidak salah tangkap sebagai route parameter.
         */
        Route::get('/users/summary',      [AdminUserController::class, 'summary']);
        Route::get('/users',              [AdminUserController::class, 'index']);
        Route::get('/users/{kode_owner}', [AdminUserController::class, 'show']);

        // ── Pembayaran ────────────────────────────────────────────────────

        Route::get('/payments/pending', [AdminPaymentController::class, 'pendingRequests']);
        Route::get('/payments/history', [AdminPaymentController::class, 'history']);
        Route::get('/payments/rekap',   [AdminPaymentController::class, 'rekapPendapatan']);

        /**
         * [GAP 6] GET /api/admin/payments/export
         *
         * Export data pembayaran ke CSV.
         * Harus didaftarkan SEBELUM route dengan parameter dinamis
         * untuk menghindari konflik routing.
         *
         * Query params:
         *   ?from=YYYY-MM-DD   Filter tanggal mulai
         *   ?to=YYYY-MM-DD     Filter tanggal akhir
         *   ?status=PAID       Filter status (PAID|PENDING|CANCELLED|EXPIRED)
         *   ?kode_owner=XXX    (opsional) filter satu toko
         *   ?format=csv        Format output (default: csv)
         *
         * Akses ganda:
         *   1. Via API (Bearer token)  → route ini
         *   2. Via web session         → AdminWebController@paymentsExport
         *      (sudah dibuat di GAP 2, route di web.php)
         *
         * Response: file CSV dengan BOM UTF-8 untuk kompatibilitas Excel
         */
        Route::get('/payments/export', [AdminPaymentController::class, 'export']);

        // ── Promo ─────────────────────────────────────────────────────────

        /**
         * PENTING: 'check/{kode_owner}' harus didaftarkan SEBELUM '{id}'
         * agar tidak salah tangkap sebagai route parameter integer.
         */
        Route::get('/promos/check/{kode_owner}', [AdminPromoController::class, 'checkPromo']);
        Route::get('/promos',                    [AdminPromoController::class, 'index']);
        Route::post('/promos',                   [AdminPromoController::class, 'store']);
        Route::put('/promos/{id}',               [AdminPromoController::class, 'update']);

        /**
         * DELETE /api/admin/promos/{id}
         * Soft-delete: set aktif = false, data tidak dihapus dari DB.
         */
        Route::delete('/promos/{id}', [AdminPromoController::class, 'destroy']);

    });

});