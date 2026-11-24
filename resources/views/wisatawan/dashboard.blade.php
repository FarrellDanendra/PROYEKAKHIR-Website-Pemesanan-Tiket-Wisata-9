<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Wisatawan') }}
            </h2>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 uppercase tracking-wider">
                Role: Wisatawan
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Kartu Selamat Datang -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Selamat berlibur, {{ Auth::user()->name }}!
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Jelajahi berbagai destinasi wisata menarik, pesan tiket dengan mudah, dan nikmati liburan menyenangkan bersama keluarga.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Aktivitas Pemesanan</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tiket pesanan Anda akan muncul pada daftar pemesanan saat modul transaksi telah siap.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>