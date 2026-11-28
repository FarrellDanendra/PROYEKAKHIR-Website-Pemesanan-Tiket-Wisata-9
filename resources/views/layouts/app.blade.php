<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- LOGIKA WIDGET FLOATING DI POJOK KANAN BAWAH -->
        @php
            $currentUser = auth()->user();
            // Cek apakah sedang berada di halaman pemesanan tiket
            $isHalamanPemesanan = request()->routeIs('pemesanan.index', 'pemesanan.checkout', 'pemesanan.pay');

            $pendingCount = 0;
            $refundCount = 0;

            if ($currentUser && ! $isHalamanPemesanan) {
                // 1. Hitung tiket pending milik user saat ini yang masih aktif batas bayarnya
                \App\Models\Pemesanan::syncExpiredStatuses();

                $pendingCount = \App\Models\Pemesanan::where('user_id', $currentUser->id)
                    ->where('status', 'pending')
                    ->where(function ($q) {
                        $q->whereNull('batas_bayar')->orWhere('batas_bayar', '>', now());
                    })
                    ->count();

                // 2. Hitung pengajuan refund untuk Admin atau Pengelola pemilik wisata
                if ($currentUser->hasRole('admin')) {
                    $refundCount = \App\Models\Pemesanan::where('status', 'refund')->count();
                } elseif ($currentUser->hasRole('pengelola')) {
                    $refundCount = \App\Models\Pemesanan::where('status', 'refund')
                        ->whereHas('wisata', fn ($q) => $q->where('user_id', $currentUser->id))
                        ->count();
                }
            }
        @endphp

        <!-- CONTAINER FLOATING WIDGET (TETAP MUNCUL SAAT DI-SCROLL KE MANA SAJA) -->
        <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none">

            <!-- WIDGET 1: PENGAJUAN REFUND MASUK (KHUSUS PENGELOLA & ADMIN) -->
            @if ($refundCount > 0)
                <div class="pointer-events-auto w-64 bg-white dark:bg-gray-800 border-2 border-purple-500 rounded-2xl shadow-2xl p-4 flex flex-col gap-2.5 animate-bounce-short transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-base">⚠️</span>
                            <span class="text-xs font-bold text-purple-900 dark:text-purple-300">
                                Ada Yang Refund
                            </span>
                        </div>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-extrabold text-white bg-purple-600 rounded-full shadow">
                            {{ $refundCount }}
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-tight">
                        Ada {{ $refundCount }} pengajuan refund tiket dari wisatawan yang perlu Anda periksa dan tentukan.
                    </p>

                    <a href="{{ route('pemesanan.index') }}"
                       class="w-full text-center py-2 px-3 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow transition">
                        Periksa Refund &rarr;
                    </a>
                </div>
            @endif

            <!-- WIDGET 2: PEMBAYARAN PENDING (SEMUA USER YANG PUNYA TIKET PENDING) -->
            @if ($pendingCount > 0)
                <div class="pointer-events-auto w-64 bg-white dark:bg-gray-800 border-2 border-amber-500 rounded-2xl shadow-2xl p-4 flex flex-col gap-2.5 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-base">⏳</span>
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-300">
                                Pembayaran Pending
                            </span>
                        </div>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-extrabold text-white bg-rose-600 rounded-full shadow animate-pulse">
                            {{ $pendingCount }}
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-tight">
                        Anda memiliki {{ $pendingCount }} pesanan tiket yang menunggu pembayaran sebelum waktu habis.
                    </p>

                    <a href="{{ route('pemesanan.index', ['tab' => 'saya']) }}"
                       class="w-full text-center py-2 px-3 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow transition">
                        Bayar Sekarang &rarr;
                    </a>
                </div>
            @endif

        </div>
    </body>
</html>