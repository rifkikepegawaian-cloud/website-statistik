<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Middleware\AdminAuth;

// PUBLIC
Route::get('/', [UploadController::class, 'home'])->name('home');
Route::get('/api/uploads/{upload}/stats', [UploadController::class, 'stats'])->name('uploads.stats');

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN
Route::middleware([AdminAuth::class])->group(function () {
    Route::get('/admin', [UploadController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/uploads', [UploadController::class, 'index'])->name('uploads.index');
    Route::get('/admin/uploads/create', [UploadController::class, 'create'])->name('uploads.create');
    Route::post('/admin/uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::get('/admin/uploads/{upload}', [UploadController::class, 'show'])->name('uploads.show');
    Route::get('/admin/uploads/{upload}/download', [UploadController::class, 'download'])->name('uploads.download');
    Route::delete('/admin/uploads/{upload}', [UploadController::class, 'destroy'])->name('uploads.destroy');
    Route::put('/admin/uploads/{upload}', [UploadController::class, 'update'])->name('uploads.update');

    Route::get('/admin/password', [AuthController::class, 'showPassword'])->name('password.show');
    Route::post('/admin/password', [AuthController::class, 'updatePassword'])->name('password.update');
});
