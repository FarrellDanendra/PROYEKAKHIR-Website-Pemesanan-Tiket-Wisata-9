<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Katalog Destinasi Wisata') }}
            </h2>
            @hasanyrole('admin|pengelola')
                <a href="{{ route('wisata.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
                    + Tambah Wisata
                </a>
            @endhasanyrole
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message (Post-Redirect-Get) -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/40 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <!-- Form Pencarian, Filter & Sorting -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('wisata.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <!-- Pencarian Kata Kunci -->
                    <div>
                        <label for="cari" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Pencarian</label>
                        <input type="text" name="cari" id="cari" value="{{ request('cari') }}" placeholder="Nama / Lokasi..."
                               class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Filter Kategori -->
                    <div>
                        <label for="kategori_id" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sorting Whitelist -->
                    <div>
                        <label for="sort" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Urutkan</label>
                        <select name="sort" id="sort" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="harga_tiket" {{ request('sort') == 'harga_tiket' ? 'selected' : '' }}>Harga Tiket</option>
                        </select>
                    </div>

                    <!-- Filter Checkbox Hanya Buka -->
                    <div class="flex items-center h-10">
                        <label class="flex items-center cursor-pointer space-x-2">
                            <input type="checkbox" name="hanya_buka" value="1" {{ request('hanya_buka') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hanya yang Buka</span>
                        </label>
                    </div>

                    <!-- Tombol Filter & Reset -->
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
                            Terapkan
                        </button>
                        <a href="{{ route('wisata.index') }}" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-md hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Daftar Destinasi Wisata Menggunakan Component kartu-wisata --}}
            @if ($wisatas->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($wisatas as $w)
                        <div class="flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            {{-- Panggil component kartu-wisata buatan tim --}}
                            <x-kartu-wisata
                                :nama="$w->nama"
                                :kategori="$w->kategori->nama ?? 'Umum'"
                                :harga="$w->harga_tiket"
                                :statusBuka="$w->status_buka"
                                :gambar="$w->gambar"
                            >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    📍 {{ $w->lokasi }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Kode: <span class="font-mono font-semibold">{{ $w->kode_wisata ?? '-' }}</span> | Pemesanan: {{ $w->pemesanans_count }}x
                                </p>
                            </x-kartu-wisata>

                            <!-- Aksi Kartu -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs font-semibold">
                                <a href="{{ route('wisata.show', $w) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Lihat Detail &rarr;
                                </a>

                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('pemesanan.create', ['wisata_id' => $w->id]) }}" class="px-2.5 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                                        Pesan Tiket
                                    </a>

                                    @hasanyrole('admin|pengelola')
                                        <a href="{{ route('wisata.edit', $w) }}" class="text-amber-600 hover:underline">
                                            Edit
                                        </a>

                                        <form action="{{ route('wisata.destroy', $w) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi wisata ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    @endhasanyrole
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $wisatas->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 p-12 text-center rounded-lg shadow-sm">
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada destinasi wisata yang cocok dengan kriteria pencarian Anda.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>