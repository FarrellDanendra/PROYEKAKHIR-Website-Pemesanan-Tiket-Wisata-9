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

            @if (session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/40 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-amber-50 dark:bg-amber-900/40 border-l-4 border-amber-500 rounded-md">
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                        {{ session('info') }}
                    </p>
                </div>
            @endif

            <!-- Form Pencarian, Filter & Sorting -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('wisata.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="cari" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Pencarian</label>
                        <input type="text" name="cari" id="cari" value="{{ request('cari') }}" placeholder="Ketik nama / lokasi..."
                               autocomplete="off"
                               class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

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

                    <div>
                        <label for="sort" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Urutkan</label>
                        <select name="sort" id="sort" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="harga_tiket" {{ request('sort') == 'harga_tiket' ? 'selected' : '' }}>Harga Tiket</option>
                        </select>
                    </div>

                    <div class="flex items-center h-10">
                        <label class="flex items-center cursor-pointer space-x-2">
                            <input type="checkbox" name="hanya_buka" value="1" {{ request('hanya_buka') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hanya yang Buka</span>
                        </label>
                    </div>

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

<div id="wisata-list-container">
    @include('wisata.list')
</div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="{{ route('wisata.index') }}"]');
        const cariInput = document.getElementById('cari');
        const container = document.getElementById('wisata-list-container');
        let timer = null;

        if (cariInput && form && container) {
            cariInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const params = new URLSearchParams(new FormData(form));
                    const url = `${form.action}?${params.toString()}`;
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            container.innerHTML = html;
                            window.history.replaceState({}, '', url);
                        });
                }, 300);
            });
        }
    });
    </script>
</x-app-layout>