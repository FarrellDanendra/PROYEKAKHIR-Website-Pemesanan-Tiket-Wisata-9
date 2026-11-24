<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Pengelola Wisata') }}
            </h2>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 uppercase tracking-wider">
                Role: Pengelola
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Kartu Selamat Datang -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Halo, {{ Auth::user()->name }}!
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Di dashboard ini Anda dapat mengelola destinasi wisata yang Anda miliki, memvalidasi pemesanan tiket wisatawan, dan meninjau laporan pendapatan.
                </p>
            </div>

            <!-- Menu Tindakan Cepat Pengelola -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-amber-500">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Area Bersama Manajemen</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Akses halaman ringkasan bersama yang diproteksi role admin & pengelola.</p>
                    <a href="{{ route('manajemen.ringkasan') }}" class="inline-block mt-3 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-md">
                        Buka Halaman Ringkasan
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Informasi Akun</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Email: {{ Auth::user()->email }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">Daftar permission: kelola kategori, kelola wisata, verifikasi pemesanan, lihat laporan.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>