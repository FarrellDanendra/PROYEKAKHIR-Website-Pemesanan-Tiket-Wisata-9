<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KategoriWisataController;
use App\Http\Controllers\Api\V1\PemesananController;
use App\Http\Controllers\Api\V1\WisataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    // Autentikasi Bearer Token (Sanctum)
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Kategori Wisata (Publik)
    Route::get('/kategori-wisata', [KategoriWisataController::class, 'index'])
        ->name('kategori-wisata.index');

    // Destinasi Wisata (GET publik; POST/PUT/DELETE dilindungi #[Middleware('auth:sanctum')])
    Route::apiResource('wisata', WisataController::class)
        ->parameters(['wisata' => 'wisata']);

    // Pemesanan Tiket (seluruh operasi dilindungi #[Middleware('auth:sanctum')])
    Route::apiResource('pemesanan', PemesananController::class)
        ->parameters(['pemesanan' => 'pemesanan']);
});