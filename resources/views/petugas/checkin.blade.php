<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Input Kode Pemesanan Tiket') }}
            </h2>
            <a href="{{ route('petugas.dashboard') }}" class="text-sm text-indigo-600 hover:underline font-semibold">
                Lihat Riwayat Tiket &rarr;
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Konfirmasi Tiket Pengunjung</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Masukkan kode pemesanan tiket yang berstatus <strong>Paid</strong> untuk mengonfirmasi kedatangan wisatawan.
                    </p>
                </div>

                <form action="{{ route('petugas.checkin.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="kode_tiket" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Kode Pemesanan Tiket <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode_tiket" id="kode_tiket" required autofocus
                               value="{{ old('kode_tiket') }}"
                               placeholder="Contoh: TKT-AB12CD34"
                               class="mt-1 block w-full font-mono uppercase rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm">
                        @error('kode_tiket')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow-sm">
                        Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>