<?php

use App\Http\Controllers\KategoriWisataController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WisataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('wisata.index');
});

// Halaman Klien Konsumsi REST API (Vanilla JS Fetch API)
Route::view('/klien-api', 'klien.wisata')->name('klien.api');

// Redirect otomatis dari /dashboard berdasarkan role
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('pengelola')) {
        return redirect()->route('pengelola.dashboard');
    }

    if ($user->hasRole('wisatawan')) {
        return redirect()->route('wisatawan.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard Masing-Masing Role
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pengelola.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:wisatawan'])->prefix('wisatawan')->name('wisatawan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('wisatawan.dashboard');
    })->name('dashboard');
});

// Halaman Bersama Admin & Pengelola
Route::middleware(['auth', 'verified', 'role:admin|pengelola'])->prefix('manajemen')->name('manajemen.')->group(function () {
    Route::get('/ringkasan', function () {
        return view('manajemen.ringkasan');
    })->name('ringkasan');
});

// CRUD Modules (Auth Required)
Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Modul Wisata (Semua role dapat melihat index & detail)
    Route::resource('wisata', WisataController::class);

    // 2. Modul Kategori (Hanya Admin & Pengelola)
    Route::resource('kategori', KategoriWisataController::class)->middleware('role:admin|pengelola');

    // 3. Modul Pemesanan Tiket
    Route::resource('pemesanan', PemesananController::class);

    // 4. Laporan Rekapitulasi (Admin & Pengelola)
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('role:admin|pengelola')
        ->name('laporan.index');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';