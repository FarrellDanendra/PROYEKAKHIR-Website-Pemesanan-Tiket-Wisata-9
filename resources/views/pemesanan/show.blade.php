<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail E-Tiket: ') }} {{ $pemesanan->kode_tiket }}
            </h2>
            <a href="{{ route('pemesanan.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Kembali ke Daftar Pemesanan
            </a>
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
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Kode Pemesanan</p>
                        <h3 class="text-2xl font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $pemesanan->kode_tiket }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</p>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $pemesanan->status === 'dikonfirmasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $pemesanan->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinasi Wisata</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->wisata->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Kunjungan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->tanggal_kunjungan->format('l, d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nama Pemesan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->user->name ?? '-' }} ({{ $pemesanan->user->email ?? '-' }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jumlah & Total Biaya</p>
                        <p class="font-bold text-emerald-600 text-base">{{ $pemesanan->jumlah_tiket }} Tiket (Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }})</p>
                    </div>
                </div>

                @if ($pemesanan->catatan)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Catatan</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $pemesanan->catatan }}</p>
                    </div>
                @endif

                <!-- Panel Verifikasi (Hanya Admin / Pengelola) -->
                @hasanyrole('admin|pengelola')
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">Verifikasi Status Pemesanan</h4>
                        <form action="{{ route('pemesanan.update', $pemesanan) }}" method="POST" class="flex items-center gap-3">
                            @csrf
                            @method('PUT')
                            <select name="status" class="rounded-md border-gray-300 text-sm">
                                <option value="pending" {{ $pemesanan->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dikonfirmasi" {{ $pemesanan->status === 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="selesai" {{ $pemesanan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $pemesanan->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                                Update Status
                            </button>
                        </form>
                    </div>
                @endhasanyrole
            </div>
        </div>
    </div>
</x-app-layout>