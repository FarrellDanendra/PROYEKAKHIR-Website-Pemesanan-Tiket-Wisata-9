<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Pemesanan Tiket') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/40 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-900/40 border-l-4 border-rose-500 rounded-md">
                    <p class="text-sm font-semibold text-rose-800 dark:text-rose-200">{{ session('error') }}</p>
                </div>
            @endif
            @if (session('info'))
                <div class="p-4 bg-amber-50 dark:bg-amber-900/40 border-l-4 border-amber-500 rounded-md">
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">{{ session('info') }}</p>
                </div>
            @endif

            <!-- Form Pencarian Tanggal, Bulan, Destinasi (Berlaku Semua Role) -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('pemesanan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
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
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
                            Cari
                        </button>
                        <a href="{{ route('pemesanan.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-md hover:bg-gray-300">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemesan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah &amp; Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($pemesanans as $p)
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-sm text-indigo-600 dark:text-indigo-400">{{ $p->kode_tiket }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $p->wisata->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $p->nama_pemesan ?? $p->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $p->tanggal_kunjungan->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $p->jumlah_tiket }} tiket <br>
                                    <span class="text-xs font-bold text-emerald-600">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $badgeMap = [
                                            'pending' => ['bg-amber-100 text-amber-800', 'Pending'],
                                            'paid' => ['bg-emerald-100 text-emerald-800', 'Paid'],
                                            'refund' => ['bg-purple-100 text-purple-800', 'Pengajuan Refund'],
                                            'refund_berhasil' => ['bg-blue-100 text-blue-800', 'Refund Berhasil'],
                                            'sudah_digunakan' => ['bg-teal-100 text-teal-800', 'Sudah Digunakan'],
                                            'kadaluarsa' => ['bg-rose-100 text-rose-800', 'Kadaluarsa'],
                                        ];
                                        [$badgeClass, $badgeLabel] = $badgeMap[$p->status] ?? ['bg-gray-100 text-gray-800', ucfirst($p->status)];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('pemesanan.show', $p) }}" class="text-indigo-600 hover:underline font-semibold">Detail</a>

                                    @if ($p->isPendingActive() && auth()->user()->hasRole('wisatawan'))
                                        <a href="{{ route('pemesanan.pay', $p) }}" class="inline-block px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded font-semibold">
                                            Bayar
                                        </a>
                                    @endif

                                    @if ($p->status === 'paid')
                                        <a href="{{ route('pemesanan.invoice', $p) }}" class="inline-block px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold">
                                            Cetak Invoice
                                        </a>

                                        @if ($p->isRefundable() && $p->user_id === auth()->id())
                                            <a href="{{ route('pemesanan.refund.form', $p) }}" class="inline-block px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-semibold">
                                                Refund
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat pemesanan tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $pemesanans->links() }}</div>
        </div>
    </div>
</x-app-layout>