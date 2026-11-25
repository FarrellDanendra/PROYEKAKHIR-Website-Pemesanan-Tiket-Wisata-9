<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Halaman Pembayaran Tiket') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($pemesanan && $pemesanan->batas_bayar)
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-md flex justify-between items-center">
                    <span class="text-sm font-semibold text-amber-800">
                        Selesaikan pembayaran untuk kode <strong>{{ $pemesanan->kode_tiket }}</strong> sebelum waktu habis:
                    </span>
                    <span id="countdown-bayar" class="font-mono font-bold text-rose-600 text-sm">Memuat...</span>
                </div>
            @endif

            <!-- Ringkasan Pesanan -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3">Ringkasan Pemesanan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinasi Wisata</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $wisata->nama }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Kunjungan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($tanggalKunjungan)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jumlah &amp; Total</p>
                        <p class="font-bold text-emerald-600">{{ $jumlahTiket }} Tiket (Rp {{ number_format($totalHarga, 0, ',', '.') }})</p>
                    </div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ $pemesanan ? route('pemesanan.pay.process', $pemesanan) : route('pemesanan.checkout.process') }}" method="POST" class="space-y-5">
                    @csrf

                    <input type="hidden" name="wisata_id" value="{{ $wisata->id }}">
                    <input type="hidden" name="tanggal_kunjungan" value="{{ $tanggalKunjungan }}">
                    <input type="hidden" name="jumlah_tiket" value="{{ $jumlahTiket }}">

                    <div>
                        <label for="nama_pemesan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pemesan" id="nama_pemesan" required
                               value="{{ old('nama_pemesan', $pemesanan->nama_pemesan ?? auth()->user()->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm sm:text-sm">
                        @error('nama_pemesan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email_pemesan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email_pemesan" id="email_pemesan" required
                               value="{{ old('email_pemesan', $pemesanan->email_pemesan ?? auth()->user()->email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm sm:text-sm">
                        @error('email_pemesan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="no_telepon" id="no_telepon" required
                               placeholder="08xxxxxxxxxx"
                               value="{{ old('no_telepon', $pemesanan->no_telepon ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm sm:text-sm">
                        @error('no_telepon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" id="alamat" rows="3" required
                                  placeholder="Masukkan alamat lengkap Anda..."
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm sm:text-sm">{{ old('alamat', $pemesanan->alamat ?? '') }}</textarea>
                        @error('alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        @php
                            $metodeList = [
                                'transfer_bank' => 'Transfer Bank (BCA / BRI / Mandiri)',
                                'qris' => 'QRIS',
                                'e_wallet' => 'E-Wallet (GoPay / OVO / DANA)',
                                'virtual_account' => 'Virtual Account',
                            ];
                            $selectedMetode = old('metode_pembayaran', $pemesanan->metode_pembayaran ?? '');
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($metodeList as $val => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 dark:border-gray-600">
                                    <input type="radio" name="metode_pembayaran" value="{{ $val }}"
                                           {{ $selectedMetode === $val ? 'checked' : '' }} required
                                           class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('metode_pembayaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" name="action" value="kembali" formnovalidate
                                class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg">
                            Kembali
                        </button>

                        <button type="submit" name="action" value="bayar"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm">
                            Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($pemesanan && $pemesanan->batas_bayar)
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const target = new Date("{{ $pemesanan->batas_bayar->toIso8601String() }}").getTime();
            const el = document.getElementById('countdown-bayar');
            const interval = setInterval(() => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    clearInterval(interval);
                    el.textContent = 'Waktu Habis';
                    window.location.href = "{{ route('pemesanan.index') }}";
                    return;
                }
                const m = Math.floor(diff / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                el.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }, 1000);
        });
        </script>
    @endif
</x-app-layout>