<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kode Pemesanan: ') }} {{ $pemesanan->kode_tiket }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Kode Tiket</p>
                        <h3 class="text-2xl font-mono font-bold text-indigo-600">{{ $pemesanan->kode_tiket }}</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-teal-100 text-teal-800">
                        {{ str_replace('_', ' ', $pemesanan->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinasi Wisata</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->wisata->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Kunjungan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->tanggal_kunjungan->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nama Pelanggan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->nama_pemesan ?? $pemesanan->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email &amp; Telepon</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->email_pemesan ?? $pemesanan->user->email ?? '-' }} / {{ $pemesanan->no_telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jumlah &amp; Total Bayar</p>
                        <p class="font-bold text-emerald-600">{{ $pemesanan->jumlah_tiket }} Tiket (Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Dikonfirmasi Pada</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->used_at ? $pemesanan->used_at->format('d M Y H:i:s') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Petugas Konfirmasi</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->petugas->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('petugas.checkin') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
                        Kembali ke Input Kode
                    </a>
                    <a href="{{ route('petugas.dashboard') }}" class="text-sm font-semibold text-gray-600 hover:underline">
                        Buka Riwayat Tiket &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>