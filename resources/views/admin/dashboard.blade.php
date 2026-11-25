<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Administrator') }}
            </h2>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 uppercase tracking-wider">
                Role: Admin
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Selamat datang kembali, {{ Auth::user()->name }}!
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Anda memiliki hak akses penuh untuk mengelola pengguna, data destinasi wisata, kategori, dan memonitor seluruh transaksi sistem.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Manajemen Akun</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">Kelola Pengguna</p>
                    <a href="{{ route('admin.users.index') }}" class="inline-block mt-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:underline">
                        Atur Role &amp; Blokir Pengguna &rarr;
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pusat Manajemen</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">Kelola Sistem</p>
                    <a href="{{ route('manajemen.ringkasan') }}" class="inline-block mt-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Buka Ringkasan Manajemen &rarr;
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hak Akses Role</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">Full Permissions</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Spatie Laravel Permission aktif</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>