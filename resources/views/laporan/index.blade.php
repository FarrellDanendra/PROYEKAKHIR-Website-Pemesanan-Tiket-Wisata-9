<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Rekapitulasi Penjualan & Destinasi Wisata') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Ringkasan Statistik Global -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-500">
                    <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Destinasi</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $totalWisata }} Destinasi</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Tiket Terjual</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $totalTiket }} Tiket</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-amber-500">
                    <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Tabel Rekap Per Kategori -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Rekapitulasi Penjualan Berdasarkan Kategori Wisata
                    </h3>
                    <p class="text-sm text-gray-500">Hasil agregasi relasi tabel kategori, wisata, dan pemesanan secara realtime.</p>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Jumlah Wisata</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tiket Terjual</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($rekapPerKategori as $rekap)
                            <tr>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ $rekap->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">
                                    {{ $rekap->total_wisata }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-bold text-emerald-600">
                                    {{ $rekap->total_tiket_terjual }} Tiket
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-extrabold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data untuk direkapitulasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>