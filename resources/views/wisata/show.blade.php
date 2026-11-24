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
                            <span class="text-xs text-gray-500 font-normal">/ tiket</span>
                        </div>
                    </div>

                    <div>
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

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-between items-center">
                        <p class="text-xs text-gray-500">
                            Dikelola oleh: <strong>{{ $wisata->pengelola->name ?? 'Admin Sistem' }}</strong>
                        </p>
                        <a href="{{ route('pemesanan.create', ['wisata_id' => $wisata->id]) }}" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow-sm">
                            Pesan Tiket Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>