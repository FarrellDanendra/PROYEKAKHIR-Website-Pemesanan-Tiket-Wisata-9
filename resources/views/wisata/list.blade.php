@if ($wisatas->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($wisatas as $w)
            <div class="flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:ring-2 hover:ring-indigo-400 transition">
                <a href="{{ route('wisata.show', $w) }}" class="block flex-1 cursor-pointer">
                    <x-kartu-wisata
                        :nama="$w->nama"
                        :kategori="$w->kategori->nama ?? 'Umum'"
                        :harga="$w->harga_tiket"
                        :statusBuka="$w->status_buka"
                        :gambar="$w->gambar"
                        :highlight="request('cari')"
                    >
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            📍 {{ $w->lokasi }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Kode: <span class="font-mono font-semibold">{{ $w->kode_wisata ?? '-' }}</span> | Pemesanan: {{ $w->pemesanans_count }}x
                        </p>
                    </x-kartu-wisata>
                </a>

                @if (auth()->check() && (auth()->user()->hasRole('admin') || (auth()->user()->hasRole('pengelola') && $w->user_id === auth()->id())))
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end space-x-4 text-xs font-semibold">
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
                    </div>
                @endif
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