<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $wisata->nama }}
            </h2>
            <a href="{{ route('wisata.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Kembali ke Katalog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-md">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($wisata->gambar)
                    <img src="{{ asset('storage/' . $wisata->gambar) }}" alt="{{ $wisata->nama }}" class="w-full h-80 object-cover">
                @endif

                <div class="p-6 space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $wisata->kategori->nama ?? 'Kategori Umum' }}
                            </span>
                            <span class="ml-2 font-mono text-xs text-gray-500">
                                Kode: {{ $wisata->kode_wisata ?? '-' }}
                            </span>
                        </div>

                        <div class="text-2xl font-bold text-emerald-600">
                            Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}
                            <span class="text-xs text-gray-500 font-normal">/ orang</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            📍 {{ $wisata->lokasi }} &bull; Dikelola oleh: <strong>{{ $wisata->pengelola->name ?? 'Admin Sistem' }}</strong>
                        </p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Deskripsi</h3>
                        <p class="mt-2 text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $wisata->deskripsi }}
                        </p>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">Fasilitas Tersedia</h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($wisata->fasilitas as $f)
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 rounded-md">
                                    ✓ {{ $f->nama }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-500">Belum ada data fasilitas.</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Form Pilih Tanggal Bulan Ini + Pesan Sekarang -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6"
                         x-data="{
                            selectedDate: '{{ $tanggalTersedia[0]['value'] ?? date('Y-m-d') }}',
                            jumlahTiket: 1,
                            harga: {{ (int) $wisata->harga_tiket }}
                         }">
                        <h4 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3">
                            Pilih Tanggal Kunjungan (Bulan Ini)
                        </h4>

                        <form action="{{ route('pemesanan.checkout') }}" method="GET" class="space-y-6">
                            <input type="hidden" name="wisata_id" value="{{ $wisata->id }}">
                            <input type="hidden" name="tanggal_kunjungan" :value="selectedDate">

                            <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-3">
                                @foreach ($tanggalTersedia as $tgl)
                                    <button type="button"
                                            @click="selectedDate = '{{ $tgl['value'] }}'"
                                            :class="selectedDate === '{{ $tgl['value'] }}'
                                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md'
                                                : 'bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-600 hover:border-emerald-400'"
                                            class="p-3 rounded-xl border text-center transition cursor-pointer flex flex-col items-center">
                                        <span class="text-[11px] font-medium opacity-80">{{ $tgl['hari'] }}</span>
                                        <span class="text-xl font-extrabold my-0.5">{{ $tgl['angka'] }}</span>
                                        <span class="text-xs font-semibold uppercase">{{ $tgl['bulan'] }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-4">
                                    <div>
                                        <label for="jumlah_tiket" class="block text-xs font-semibold text-gray-500">Jumlah Tiket</label>
                                        <input type="number" name="jumlah_tiket" id="jumlah_tiket" min="1" max="20"
                                               x-model.number="jumlahTiket"
                                               class="mt-1 w-24 rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500">Total Bayar</p>
                                        <p class="text-lg font-bold text-emerald-600"
                                           x-text="'Rp ' + (harga * (jumlahTiket || 1)).toLocaleString('id-ID')"></p>
                                    </div>
                                </div>

                                @if ($wisata->status_buka)
                                    <button type="submit"
                                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow-sm">
                                        Pesan Sekarang
                                    </button>
                                @else
                                    <span class="px-4 py-2 bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg">
                                        Destinasi Sedang Tutup
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>