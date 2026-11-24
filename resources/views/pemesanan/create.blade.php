<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Form Pemesanan Tiket Wisata') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('pemesanan.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="wisata_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destinasi Wisata <span class="text-red-500">*</span></label>
                        <select name="wisata_id" id="wisata_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm">
                            <option value="">-- Pilih Destinasi Wisata --</option>
                            @foreach ($wisatas as $w)
                                <option value="{{ $w->id }}" {{ old('wisata_id', $wisataPilihan->id ?? '') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nama }} (Rp {{ number_format($w->harga_tiket, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('wisata_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm">
                        @error('tanggal_kunjungan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jumlah_tiket" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Tiket (1-20 Tiket) <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_tiket" id="jumlah_tiket" min="1" max="20" value="{{ old('jumlah_tiket', 1) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm">
                        @error('jumlah_tiket') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Pemesanan</label>
                        <textarea name="catatan" id="catatan" rows="3" placeholder="Informasi kedatangan atau rombongan..."
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm">{{ old('catatan') }}</textarea>
                        @error('catatan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('wisata.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-300">Batal</a>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Konfirmasi Pemesanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>