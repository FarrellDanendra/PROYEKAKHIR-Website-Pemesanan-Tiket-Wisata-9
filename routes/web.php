<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\KategoriWisataController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WisataController;
use Illuminate\Support\Facades\Route;

// 1. Halaman utama langsung menuju katalog wisata (Publik)
Route::get('/', function () {
    return redirect()->route('wisata.index');
});

Route::view('/klien-api', 'klien.wisata')->name('klien.api');

// Katalog wisata (Publik)
Route::get('/wisata', [WisataController::class, 'index'])->name('wisata.index');

// Route Auth + Verified + Tidak Diblokir
Route::middleware(['auth', 'verified', 'not.blocked'])->group(function () {
    // Redirect otomatis /dashboard sesuai role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('pengelola')) {
            return redirect()->route('pengelola.dashboard');
        }
        if ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }
        if ($user->hasRole('wisatawan')) {
            return redirect()->route('wisatawan.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    // Dashboard & Kelola Pengguna Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
        Route::patch('/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
    });

    // Dashboard Pengelola
    Route::middleware('role:pengelola')->prefix('pengelola')->name('pengelola.')->group(function () {
        Route::get('/dashboard', fn () => view('pengelola.dashboard'))->name('dashboard');
    });

    // Dashboard Wisatawan
    Route::middleware('role:wisatawan')->prefix('wisatawan')->name('wisatawan.')->group(function () {
        Route::get('/dashboard', fn () => view('wisatawan.dashboard'))->name('dashboard');
    });

    // Role Petugas (hanya input kode tiket, detail tiket terkonfirmasi, riwayat, profile)
    Route::middleware('role:petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
        Route::get('/checkin', [PetugasController::class, 'checkinForm'])->name('checkin');
        Route::post('/checkin', [PetugasController::class, 'checkinStore'])->name('checkin.store');
        Route::get('/tiket/{pemesanan}', [PetugasController::class, 'show'])->name('show');
    });

    // Halaman Bersama Admin & Pengelola
    Route::middleware('role:admin|pengelola')->prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/ringkasan', fn () => view('manajemen.ringkasan'))->name('ringkasan');
    });

    // CRUD Wisata (Hanya Admin & Pengelola - definisikan /create sebelum /{wisata})
    Route::middleware('role:admin|pengelola')->group(function () {
        Route::get('/wisata/create', [WisataController::class, 'create'])->name('wisata.create');
        Route::post('/wisata', [WisataController::class, 'store'])->name('wisata.store');
        Route::get('/wisata/{wisata}/edit', [WisataController::class, 'edit'])->name('wisata.edit');
        Route::put('/wisata/{wisata}', [WisataController::class, 'update'])->name('wisata.update');
        Route::patch('/wisata/{wisata}', [WisataController::class, 'update']);
        Route::delete('/wisata/{wisata}', [WisataController::class, 'destroy'])->name('wisata.destroy');
    });

    // Kategori & Laporan (Hanya Admin & Pengelola)
    Route::resource('kategori', KategoriWisataController::class)->middleware('role:admin|pengelola');
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('role:admin|pengelola')
        ->name('laporan.index');

    // Modul Pemesanan, Pembayaran, Invoice PDF, & Refund (Admin, Pengelola, Wisatawan)
    Route::middleware('role:admin|pengelola|wisatawan')->group(function () {
        Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
        Route::get('/pemesanan/pembayaran', [PemesananController::class, 'checkout'])->name('pemesanan.checkout');
        Route::post('/pemesanan/pembayaran', [PemesananController::class, 'processCheckout'])->name('pemesanan.checkout.process');
        Route::get('/pemesanan/{pemesanan}/bayar', [PemesananController::class, 'payPending'])->name('pemesanan.pay');
        Route::post('/pemesanan/{pemesanan}/bayar', [PemesananController::class, 'processPending'])->name('pemesanan.pay.process');
        Route::get('/pemesanan/{pemesanan}/invoice', [PemesananController::class, 'invoice'])->name('pemesanan.invoice');
        Route::get('/pemesanan/{pemesanan}/refund', [PemesananController::class, 'refundForm'])->name('pemesanan.refund.form');
        Route::post('/pemesanan/{pemesanan}/refund', [PemesananController::class, 'refundStore'])->name('pemesanan.refund.store');
        Route::patch('/pemesanan/{pemesanan}/refund-decision', [PemesananController::class, 'refundDecision'])
            ->middleware('role:admin|pengelola')
            ->name('pemesanan.refund.decision');
        Route::get('/pemesanan/{pemesanan}', [PemesananController::class, 'show'])->name('pemesanan.show');
        Route::delete('/pemesanan/{pemesanan}', [PemesananController::class, 'destroy'])
            ->middleware('role:admin|pengelola')
            ->name('pemesanan.destroy');
    });

    // Profile Settings (Semua role termasuk Petugas)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Detail Wisata (Publik, diletakkan SETELAH /wisata/create agar tidak bentrok)
Route::get('/wisata/{wisata}', [WisataController::class, 'show'])->name('wisata.show');

require __DIR__.'/auth.php';