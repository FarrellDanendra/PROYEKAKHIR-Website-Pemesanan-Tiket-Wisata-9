<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ringkasan Manajemen Bersama') }}
            </h2>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 uppercase tracking-wider">
                Akses: Admin & Pengelola
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Area Khusus Manajemen (Admin & Pengelola)
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Halaman ini dilindungi oleh middleware <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-pink-600 font-mono text-xs">role:admin|pengelola</code>. Pengguna dengan role <strong>wisatawan</strong> akan otomatis ditolak dengan kode status <strong>403 Forbidden</strong> jika mencoba mengakses URL ini.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>