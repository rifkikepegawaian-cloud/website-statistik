<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------|
| Public
|--------------------------------------------------------------------------|
*/
Route::get('/', [UploadController::class, 'home'])->name('home');
Route::get('/api/uploads/{upload}/stats', [UploadController::class, 'stats'])->name('uploads.stats');

/*
|--------------------------------------------------------------------------|
| Auth (login/logout)
|--------------------------------------------------------------------------|
| /login hanya untuk guest. Redirect user yang sudah login dilakukan di
| AuthController@showLogin (Auth::check() -> redirect dashboard).
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    // kalau belum punya RateLimiter bernama 'login', ganti ke throttle:6,1
    Route::post('/login', [AuthController::class, 'doLogin'])->name('login.post')->middleware('throttle:6,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------|
| Admin Area (wajib login)
|--------------------------------------------------------------------------|
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [UploadController::class, 'dashboard'])->name('dashboard');

    // ---- ADMIN ONLY: create/store/edit/update/destroy ----
    Route::get('/uploads/create', [UploadController::class, 'create'])->middleware(AdminAuth::class)->name('uploads.create');
    Route::post('/uploads',        [UploadController::class, 'store'])->middleware(AdminAuth::class)->name('uploads.store');
    Route::get('/uploads/{upload}/edit', [UploadController::class, 'edit'])->middleware(AdminAuth::class)->name('uploads.edit');
    Route::put('/uploads/{upload}',      [UploadController::class, 'update'])->middleware(AdminAuth::class)->name('uploads.update');
    Route::delete('/uploads/{upload}',   [UploadController::class, 'destroy'])->middleware(AdminAuth::class)->name('uploads.destroy');

    // ---- Admin & User: lihat & unduh ----
    Route::get('/uploads',                    [UploadController::class, 'index'])->name('uploads.index');
    Route::get('/uploads/{upload}',           [UploadController::class, 'show'])->name('uploads.show');
    Route::get('/uploads/{upload}/download',  [UploadController::class, 'download'])->name('uploads.download');

    // Ubah password (admin & user boleh)
    Route::get('/password', [AuthController::class, 'showPassword'])->name('password.show');
    Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');

    // routes/web.php (di dalam group prefix('admin')->name('admin.')->middleware('auth'))
    Route::get('/uploads/{upload}/file', [\App\Http\Controllers\Admin\UploadController::class, 'file'])
     ->name('uploads.file');

    // User management (admin only)
    Route::get('/users',        [UserController::class, 'index'])->name('users.index')->middleware('can:users.manage');
    Route::post('/users',       [UserController::class, 'store'])->name('users.store')->middleware('can:users.manage');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:users.manage');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:users.manage'); // opsional

});
