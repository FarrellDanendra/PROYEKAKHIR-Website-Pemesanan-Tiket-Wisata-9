<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

// Dashboard & Area Admin
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// Dashboard & Area Pengelola
Route::middleware(['auth', 'verified', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pengelola.dashboard');
    })->name('dashboard');
});

// Dashboard & Area Wisatawan
Route::middleware(['auth', 'verified', 'role:wisatawan'])->prefix('wisatawan')->name('wisatawan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('wisatawan.dashboard');
    })->name('dashboard');
});

// Route Bersama Admin & Pengelola (role:admin|pengelola)
Route::middleware(['auth', 'verified', 'role:admin|pengelola'])->prefix('manajemen')->name('manajemen.')->group(function () {
    Route::get('/ringkasan', function () {
        return view('manajemen.ringkasan');
    })->name('ringkasan');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';