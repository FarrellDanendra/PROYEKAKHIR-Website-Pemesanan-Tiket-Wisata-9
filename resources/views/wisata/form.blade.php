<div class="space-y-6">
    <!-- Kode Wisata -->
    <div>
        <label for="kode_wisata" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Kode Wisata <span class="text-red-500">*</span>
        </label>
        <input type="text" name="kode_wisata" id="kode_wisata"
               value="{{ old('kode_wisata', $wisata->kode_wisata ?? '') }}"
               placeholder="Contoh: WST-001"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
        @error('kode_wisata')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama Destinasi Wisata -->
    <div>
        <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Nama Destinasi Wisata <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nama" id="nama"
               value="{{ old('nama', $wisata->nama ?? '') }}"
               placeholder="Contoh: Pantai Indah Sepanjang"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
        @error('nama')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kategori Wisata -->
    <div>
        <label for="kategori_wisata_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Kategori Wisata <span class="text-red-500">*</span>
        </label>
        <select name="kategori_wisata_id" id="kategori_wisata_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" {{ old('kategori_wisata_id', $wisata->kategori_wisata_id ?? '') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama }}
                </option>
            @endforeach
        </select>
        @error('kategori_wisata_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Harga Tiket Masuk -->
    <div>
        <label for="harga_tiket" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Harga Tiket Masuk (Rp) <span class="text-red-500">*</span>
        </label>
        <input type="number" step="1000" min="0" name="harga_tiket" id="harga_tiket"
               value="{{ old('harga_tiket', $wisata->harga_tiket ?? '') }}"
               placeholder="25000"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
        @error('harga_tiket')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Lokasi Wisata -->
    <div>
        <label for="lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Lokasi Wisata <span class="text-red-500">*</span>
        </label>
        <input type="text" name="lokasi" id="lokasi"
               value="{{ old('lokasi', $wisata->lokasi ?? '') }}"
               placeholder="Contoh: Bantul, D.I. Yogyakarta"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
        @error('lokasi')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Buka (Checkbox) & Tanggal Buka -->
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
        <div class="flex items-center">
            <input type="checkbox" name="status_buka" id="status_buka" value="1"
                   {{ old('status_buka', isset($wisata) ? $wisata->status_buka : true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
            <label for="status_buka" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-200">
                Destinasi Wisata Sedang Buka
            </label>
        </div>
        @error('status_buka')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <div>
            <label for="tanggal_buka" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tanggal Buka Operasional <span class="text-xs text-gray-500">(Wajib jika status buka aktif)</span>
            </label>
            <input type="date" name="tanggal_buka" id="tanggal_buka"
                   value="{{ old('tanggal_buka', isset($wisata->tanggal_buka) && $wisata->tanggal_buka ? $wisata->tanggal_buka->format('Y-m-d') : date('Y-m-d')) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            @error('tanggal_buka')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Fasilitas (Pivot BelongsToMany) -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Fasilitas yang Tersedia
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
                $fasilitasTerpilih = old('fasilitas', isset($wisata) ? $wisata->fasilitas->pluck('id')->toArray() : []);
            @endphp
            @foreach ($fasilitas as $f)
                <label class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700/40 rounded border border-gray-200 dark:border-gray-600 cursor-pointer">
                    <input type="checkbox" name="fasilitas[]" value="{{ $f->id }}"
                           {{ in_array($f->id, $fasilitasTerpilih) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $f->nama }}</span>
                </label>
            @endforeach
        </div>
        @error('fasilitas')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Foto Wisata -->
    <div>
        <label for="gambar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Foto / Banner Wisata
        </label>
        @if (isset($wisata) && $wisata->gambar)
            <div class="my-2">
                <img src="{{ asset('storage/' . $wisata->gambar) }}" alt="{{ $wisata->nama }}" class="h-32 w-auto object-cover rounded-md border">
            </div>
        @endif
        <input type="file" name="gambar" id="gambar" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
        @error('gambar')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Deskripsi Wisata -->
    <div>
        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Deskripsi Wisata <span class="text-red-500">*</span>
        </label>
        <textarea name="deskripsi" id="deskripsi" rows="4"
                  placeholder="Ceritakan keindahan dan keunikan destinasi ini..."
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">{{ old('deskripsi', $wisata->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tombol Aksi -->
    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('wisata.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            Batal
        </a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
            Simpan Destinasi
        </button>
    </div>
</div>