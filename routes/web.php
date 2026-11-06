<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/masuk', [AuthController::class, 'form'])->middleware('guest')->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::middleware('auth')->group(function () {
    Route::get('/', [PackageController::class, 'index']);
    Route::post('/keluar', [AuthController::class, 'logout']);
    Route::get('/paket/baru', [PackageController::class, 'create']);
    Route::post('/paket', [PackageController::class, 'store']);
    Route::get('/paket/{package}', [PackageController::class, 'show'])->name('package.show');
    Route::get('/paket/{package}/foto', [PackageController::class, 'photo']);
    Route::post('/paket/{package}/ambil', [PackageController::class, 'collect'])->middleware('throttle:5,1');
    Route::get('/laporan', [ReportController::class, 'index']);
});
