<!-- CDN Leaflet CSS & JS untuk Peta Lokasi Otomatis (Gratis, tanpa API Key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

    <!-- FITUR METODE PEMBAYARAN DINAMIS (MINIMAL 1, BISA TAMBAH SESUKA HATI) -->
    @php
        $existingMetode = old('metode_nama')
            ? collect(old('metode_nama'))->map(fn($nama, $idx) => ['nama' => $nama, 'nomor' => old('metode_nomor')[$idx] ?? ''])->toArray()
            : (isset($wisata) && is_array($wisata->metode_pembayaran) && count($wisata->metode_pembayaran) > 0
                ? $wisata->metode_pembayaran
                : [['nama' => 'Transfer Bank (BCA)', 'nomor' => '1234567890 a/n Pengelola']]);
    @endphp
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 space-y-4"
         x-data="{
            metodes: {{ json_encode($existingMetode) }},
            tambahMetode() {
                this.metodes.push({ nama: '', nomor: '' });
            },
            hapusMetode(index) {
                if (this.metodes.length > 1) {
                    this.metodes.splice(index, 1);
                } else {
                    alert('Minimal harus ada 1 metode pembayaran!');
                }
            }
         }">
        <div class="flex items-center justify-between">
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-gray-100">
                    💳 Metode Pembayaran Tiket Wisata Ini <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500">
                    Tentukan rekening / e-wallet / metode pembayaran untuk destinasi ini (minimal 1).
                </p>
            </div>
            <button type="button" @click="tambahMetode()"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md shadow-sm flex items-center gap-1">
                + Tambahkan Metode Pembayaran
            </button>
        </div>

        <div class="space-y-3">
            <template x-for="(m, index) in metodes" :key="index">
                <div class="flex flex-col sm:flex-row items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-300">Nama Metode / Bank</label>
                        <input type="text" name="metode_nama[]" x-model="m.nama" required
                               placeholder="Contoh: BCA / DANA / QRIS / Mandiri"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-300">Nomor Rekening / No. HP / Atas Nama</label>
                        <input type="text" name="metode_nomor[]" x-model="m.nomor" required
                               placeholder="Contoh: 1234567890 a/n Farrell"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                    </div>
                    <div class="pt-4 sm:pt-4">
                        <button type="button" @click="hapusMetode(index)"
                                class="p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded"
                                title="Hapus metode ini">
                            🗑️
                        </button>
                    </div>
                </div>
            </template>
        </div>
        @error('metode_nama') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('metode_nomor') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Lokasi Wisata & PETA INTERAKTIF GMAPS / OPENSTREETMAP -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <label for="lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Lokasi Wisata <span class="text-red-500">*</span>
            </label>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                📍 Klik pada peta di bawah untuk mengisi alamat otomatis
            </span>
        </div>

        <div class="flex gap-2">
            <input type="text" name="lokasi" id="lokasi"
                   value="{{ old('lokasi', $wisata->lokasi ?? '') }}"
                   placeholder="Ketik lokasi atau klik peta di bawah..."
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            <button type="button" id="btn-cari-lokasi"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md">
                Cari di Peta
            </button>
        </div>
        @error('lokasi')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <!-- Wadah Peta Leaflet Maps -->
        <div id="map-picker" class="w-full h-64 rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-inner z-0"></div>
    </div>

    <!-- Status Buka & Tanggal Tutup -->
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4 border border-gray-200 dark:border-gray-600">
        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">Jadwal Operasional</h4>

        <div class="flex items-center">
            <input type="checkbox" name="status_buka" id="status_buka" value="1"
                   {{ old('status_buka', isset($wisata) ? $wisata->status_buka : true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
            <label for="status_buka" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-200">
                Destinasi Wisata Buka Secara Umum
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

        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
            <label class="block text-sm font-semibold text-rose-600 dark:text-rose-400 mb-1">
                Tutup Sementara pada Rentang Tanggal Tertentu (Opsional)
            </label>
            <p class="text-xs text-gray-500 mb-2">
                Jika diisi, wisatawan tidak dapat membeli tiket pada tanggal tersebut. Tiket paid/pending pada tanggal tersebut otomatis direfund.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_tutup_mulai" class="block text-xs text-gray-600 dark:text-gray-300">Mulai Tutup</label>
                    <input type="date" name="tanggal_tutup_mulai" id="tanggal_tutup_mulai"
                           value="{{ old('tanggal_tutup_mulai', isset($wisata->tanggal_tutup_mulai) && $wisata->tanggal_tutup_mulai ? $wisata->tanggal_tutup_mulai->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white sm:text-sm">
                    @error('tanggal_tutup_mulai') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_tutup_selesai" class="block text-xs text-gray-600 dark:text-gray-300">Sampai Tanggal</label>
                    <input type="date" name="tanggal_tutup_selesai" id="tanggal_tutup_selesai"
                           value="{{ old('tanggal_tutup_selesai', isset($wisata->tanggal_tutup_selesai) && $wisata->tanggal_tutup_selesai ? $wisata->tanggal_tutup_selesai->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white sm:text-sm">
                    @error('tanggal_tutup_selesai') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Fasilitas & Fasilitas Lainnya -->
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

        <div class="mt-4">
            <label for="fasilitas_lainnya" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Fasilitas Lainnya (Opsional)
            </label>
            <input type="text" name="fasilitas_lainnya" id="fasilitas_lainnya"
                   value="{{ old('fasilitas_lainnya') }}"
                   placeholder="Tulis fasilitas baru, pisahkan dengan koma jika lebih dari satu (contoh: Kolam Renang, Gazebo VIP)"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            <p class="mt-1 text-xs text-gray-500">Fasilitas yang belum terdaftar di atas dapat Anda ketik langsung di sini.</p>
            @error('fasilitas_lainnya')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
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

<!-- SCRIPT PETA INTERAKTIF GMAPS / OPENSTREETMAP -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultLat = -7.797068; // Yogyakarta
    const defaultLng = 110.370529;

    const map = L.map('map-picker').setView([defaultLat, defaultLng], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    const lokasiInput = document.getElementById('lokasi');

    function updateAddressFromCoords(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    lokasiInput.value = data.display_name;
                }
            })
            .catch(() => {});
    }

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]);
        updateAddressFromCoords(lat, lng);
    });

    marker.on('dragend', function () {
        const { lat, lng } = marker.getLatLng();
        updateAddressFromCoords(lat, lng);
    });

    document.getElementById('btn-cari-lokasi').addEventListener('click', function () {
        const query = lokasiInput.value.trim();
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const first = data[0];
                    const lat = parseFloat(first.lat);
                    const lon = parseFloat(first.lon);
                    map.setView([lat, lon], 14);
                    marker.setLatLng([lat, lon]);
                }
            });
    });
});
</script>