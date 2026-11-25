<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail E-Tiket: ') }} {{ $pemesanan->kode_tiket }}
            </h2>
            <a href="{{ route('pemesanan.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Kembali ke Daftar Pemesanan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-md">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Batas Waktu Pembayaran (Hanya tampil jika status masih pending, hilang jika sudah paid) -->
            @if ($pemesanan->status === 'pending' && $pemesanan->batas_bayar)
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-amber-800">Menunggu Pembayaran</p>
                        <p class="text-xs text-amber-700">
                            Batas waktu: {{ $pemesanan->batas_bayar->format('d M Y H:i:s') }}
                            (<span id="detail-countdown" class="font-mono font-bold text-rose-600">...</span>)
                        </p>
                    </div>
                    @if (auth()->user()->hasRole('wisatawan') && $pemesanan->isPendingActive())
                        <a href="{{ route('pemesanan.pay', $pemesanan) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-md">
                            Bayar Sekarang
                        </a>
                    @endif
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Kode Pemesanan</p>
                        <h3 class="text-2xl font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $pemesanan->kode_tiket }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</p>
                        @php
                            $badgeMap = [
                                'pending' => ['bg-amber-100 text-amber-800', 'Pending'],
                                'paid' => ['bg-emerald-100 text-emerald-800', 'Paid'],
                                'refund' => ['bg-purple-100 text-purple-800', 'Pengajuan Refund'],
                                'refund_berhasil' => ['bg-blue-100 text-blue-800', 'Refund Berhasil'],
                                'sudah_digunakan' => ['bg-teal-100 text-teal-800', 'Sudah Digunakan'],
                                'kadaluarsa' => ['bg-rose-100 text-rose-800', 'Kadaluarsa'],
                            ];
                            [$badgeClass, $badgeLabel] = $badgeMap[$pemesanan->status] ?? ['bg-gray-100 text-gray-800', ucfirst($pemesanan->status)];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Destinasi Wisata</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->wisata->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Kunjungan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->tanggal_kunjungan->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nama Pemesan</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->nama_pemesan ?? $pemesanan->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->email_pemesan ?? $pemesanan->user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nomor Telepon</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->no_telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Metode Pembayaran</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100 uppercase">{{ str_replace('_', ' ', $pemesanan->metode_pembayaran ?? '-') }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jumlah &amp; Total Biaya</p>
                        <p class="font-bold text-emerald-600 text-base">{{ $pemesanan->jumlah_tiket }} Tiket (Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }})</p>
                    </div>
                    @if ($pemesanan->paid_at)
                        <div>
                            <p class="text-gray-500">Waktu Pembayaran</p>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->paid_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($pemesanan->used_at)
                        <div>
                            <p class="text-gray-500">Waktu Digunakan (Check-in)</p>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $pemesanan->used_at->format('d M Y H:i') }} ({{ $pemesanan->petugas->name ?? 'Petugas' }})</p>
                        </div>
                    @endif
                </div>

                @if ($pemesanan->alasan_refund)
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-200">
                        <p class="text-xs text-purple-700 font-semibold uppercase">Alasan Pengajuan Refund</p>
                        <p class="text-sm text-gray-800 dark:text-gray-200 mt-1">{{ $pemesanan->alasan_refund }}</p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3 pt-2">
                    @if (in_array($pemesanan->status, ['paid', 'sudah_digunakan'], true))
                        <a href="{{ route('pemesanan.invoice', $pemesanan) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md">
                            Cetak Invoice PDF
                        </a>
                    @endif

                    @if ($pemesanan->isRefundable() && $pemesanan->user_id === auth()->id())
                        <a href="{{ route('pemesanan.refund.form', $pemesanan) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-md">
                            Ajukan Refund
                        </a>
                    @endif
                </div>

                <!-- Panel Keputusan Refund (Hanya Admin & Pengelola pemilik wisata) -->
                @hasanyrole('admin|pengelola')
                    @if ($pemesanan->status === 'refund')
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">Tindakan Pengajuan Refund</h4>
                            <div class="flex items-center gap-3">
                                <form action="{{ route('pemesanan.refund.decision', $pemesanan) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="setujui">
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md">
                                        Setujui Refund
                                    </button>
                                </form>

                                <form action="{{ route('pemesanan.refund.decision', $pemesanan) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="tolak">
                                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-md">
                                        Tolak Refund
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endhasanyrole
            </div>
        </div>
    </div>

    @if ($pemesanan->status === 'pending' && $pemesanan->batas_bayar)
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const target = new Date("{{ $pemesanan->batas_bayar->toIso8601String() }}").getTime();
            const el = document.getElementById('detail-countdown');
            const timer = setInterval(() => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    clearInterval(timer);
                    el.textContent = 'Kadaluarsa';
                    window.location.reload();
                    return;
                }
                const m = Math.floor(diff / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                el.textContent = 'Sisa ' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }, 1000);
        });
        </script>
    @endif
</x-app-layout>