<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Petugas — Riwayat Tiket Terkonfirmasi') }}
            </h2>
            <a href="{{ route('petugas.checkin') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md">
                + Input Kode Tiket
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Pencarian Tanggal, Bulan, Destinasi -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('petugas.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="tanggal" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Tanggal Kunjungan</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                               class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="bulan" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Bulan</label>
                        <input type="month" name="bulan" id="bulan" value="{{ request('bulan') }}"
                               class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="wisata_id" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Destinasi</label>
                        <select name="wisata_id" id="wisata_id"
                                class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Destinasi</option>
                            @foreach ($destinasis as $d)
                                <option value="{{ $d->id }}" {{ request('wisata_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
                            Cari
                        </button>
                        <a href="{{ route('petugas.dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-md">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destinasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Kunjungan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Konfirmasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($riwayats as $r)
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-sm text-indigo-600">{{ $r->kode_tiket }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $r->wisata->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $r->nama_pemesan ?? $r->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $r->tanggal_kunjungan->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $r->used_at ? $r->used_at->format('d M Y H:i') : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <a href="{{ route('petugas.show', $r) }}" class="text-indigo-600 hover:underline font-semibold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada tiket yang dikonfirmasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $riwayats->links() }}</div>
        </div>
    </div>
</x-app-layout>