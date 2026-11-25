<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pengajuan Refund Tiket: ') }} {{ $pemesanan->kode_tiket }}
            </h2>
            <a href="{{ route('pemesanan.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm space-y-1">
                    <p><strong>Destinasi:</strong> {{ $pemesanan->wisata->nama }}</p>
                    <p><strong>Tanggal Kunjungan:</strong> {{ $pemesanan->tanggal_kunjungan->format('d M Y') }}</p>
                    <p><strong>Total Pembayaran:</strong> Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
                </div>

                <form action="{{ route('pemesanan.refund.store', $pemesanan) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="alasan_refund" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Alasan Pengajuan Refund <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan_refund" id="alasan_refund" rows="4" required
                                  placeholder="Jelaskan alasan Anda mengajukan refund..."
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm sm:text-sm">{{ old('alasan_refund') }}</textarea>
                        @error('alasan_refund') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('pemesanan.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-300">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-md shadow-sm">
                            Kirim Pengajuan Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>