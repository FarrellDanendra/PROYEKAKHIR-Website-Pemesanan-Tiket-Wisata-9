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
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
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

        @if (($pendingPaymentCount ?? 0) > 0)
            <div class="fixed bottom-5 right-5 z-50 w-52 bg-white dark:bg-gray-800 border-2 border-amber-400 rounded-xl shadow-xl p-4 flex flex-col justify-between gap-3">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-bold text-gray-800 dark:text-gray-100 leading-tight">
                        Pembayaran Pending
                    </span>
                    <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-rose-600 rounded-full">
                        {{ $pendingPaymentCount }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                    Selesaikan pembayaran sebelum batas waktu 5 menit habis.
                </p>
                <a href="{{ route('pemesanan.index') }}"
                   class="w-full text-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition">
                    Bayar
                </a>
            </div>
        @endif
    </body>
</html>