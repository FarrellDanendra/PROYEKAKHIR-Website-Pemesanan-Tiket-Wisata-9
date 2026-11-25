<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klien REST API — Tiket Wisata</title>
    @vite(['resources/css/app.css'])
    <script src="{{ asset('js/wisata-client.js') }}" defer></script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Klien Konsumsi REST API v1</h1>
                <p class="text-xs text-gray-500">Vanilla JS Fetch API + Sanctum Bearer Token (<code class="font-mono">/api/v1/*</code>)</p>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('wisata.index') }}" class="text-indigo-600 hover:underline font-medium">
                    &larr; Kembali ke Web Blade
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Kotak Notifikasi Global / Error -->
        <div id="alert-box" class="hidden rounded-lg p-4 text-sm border" role="alert"></div>

        <!-- Panel Autentikasi API (Sanctum Bearer Token) -->
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Autentikasi API Bearer Token</h2>
                    <p id="auth-status-text" class="text-xs text-gray-500 mt-1">
                        Belum login. Publik hanya dapat melihat daftar &amp; detail wisata.
                    </p>
                </div>

                <!-- Status Sudah Login -->
                <div id="auth-logged-in" class="hidden flex flex-wrap items-center gap-3">
                    <div class="px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-200 text-xs">
                        <span id="user-name-badge" class="font-semibold text-indigo-900">-</span>
                        (<span id="user-email-badge" class="text-indigo-700">-</span>)
                        <span id="user-role-badge" class="ml-2 px-2 py-0.5 rounded-full bg-indigo-600 text-white uppercase font-bold">-</span>
                    </div>
                    <button type="button" id="btn-cek-me" class="px-3 py-2 text-xs font-semibold rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700">
                        Cek /me
                    </button>
                    <button type="button" id="btn-logout" class="px-3 py-2 text-xs font-semibold rounded-md bg-red-600 hover:bg-red-700 text-white">
                        Logout API
                    </button>
                </div>
            </div>

            <!-- Form Login API -->
            <form id="login-form" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label for="login-email" class="block text-xs font-semibold uppercase text-gray-600">Email</label>
                    <input type="email" id="login-email" required placeholder="admin@gmail.com"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="login-password" class="block text-xs font-semibold uppercase text-gray-600">Password</label>
                    <input type="password" id="login-password" required value="password"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <button type="submit" id="btn-login" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
                        Login &amp; Simpan Token
                    </button>
                </div>
                <div class="flex flex-wrap gap-1.5" id="quick-login-group">
                    <button type="button" data-email="admin@gmail.com" class="quick-email px-2 py-1 text-xs rounded bg-slate-100 hover:bg-slate-200 text-slate-700">admin</button>
                    <button type="button" data-email="pengelola1@gmail.com" class="quick-email px-2 py-1 text-xs rounded bg-slate-100 hover:bg-slate-200 text-slate-700">pengelola1</button>
                    <button type="button" data-email="pengelola2@gmail.com" class="quick-email px-2 py-1 text-xs rounded bg-slate-100 hover:bg-slate-200 text-slate-700">pengelola2</button>
                    <button type="button" data-email="wisatawan1@gmail.com" class="quick-email px-2 py-1 text-xs rounded bg-slate-100 hover:bg-slate-200 text-slate-700">wisatawan1</button>
                    <button type="button" data-email="wisatawan2@gmail.com" class="quick-email px-2 py-1 text-xs rounded bg-slate-100 hover:bg-slate-200 text-slate-700">wisatawan2</button>
                </div>
            </form>
        </section>

        <!-- Panel Form Tambah / Edit Wisata (Muncul untuk admin & pengelola) -->
        <section id="crud-section" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 id="form-title" class="text-base font-semibold text-gray-900">Tambah Destinasi Wisata</h2>
                <button type="button" id="btn-reset-form" class="hidden text-xs font-semibold text-gray-600 hover:text-gray-900 underline">
                    Batal Edit
                </button>
            </div>

            <form id="wisata-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" id="wisata-id" value="">

                <div>
                    <label for="kode_wisata" class="block text-xs font-semibold uppercase text-gray-600">Kode Wisata *</label>
                    <input type="text" id="kode_wisata" required placeholder="WST-001"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="nama" class="block text-xs font-semibold uppercase text-gray-600">Nama Destinasi *</label>
                    <input type="text" id="nama" required placeholder="Bukit Bintang"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="kategori_wisata_id" class="block text-xs font-semibold uppercase text-gray-600">Kategori *</label>
                    <select id="kategori_wisata_id" required class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Pilih Kategori --</option>
                    </select>
                </div>

                <div>
                    <label for="harga_tiket" class="block text-xs font-semibold uppercase text-gray-600">Harga Tiket (Rp) *</label>
                    <input type="number" id="harga_tiket" min="0" step="500" required placeholder="25000"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="lokasi" class="block text-xs font-semibold uppercase text-gray-600">Lokasi *</label>
                    <input type="text" id="lokasi" required placeholder="Gunungkidul, DIY"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="tanggal_buka" class="block text-xs font-semibold uppercase text-gray-600">Tanggal Buka (Wajib jika Buka)</label>
                    <input type="date" id="tanggal_buka"
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-xs font-semibold uppercase text-gray-600">Deskripsi *</label>
                    <textarea id="deskripsi" rows="2" required placeholder="Deskripsi singkat destinasi wisata..."
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div class="flex flex-col justify-between gap-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" id="status_buka" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Status Operasional Buka</span>
                    </label>

                    <button type="submit" id="btn-submit-wisata"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow-sm">
                        Simpan Wisata
                    </button>
                </div>
            </form>
        </section>

        <!-- Panel Filter & Pencarian -->
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="filter-cari" class="block text-xs font-semibold uppercase text-gray-600">Cari</label>
                    <input type="text" id="filter-cari" placeholder="Nama / Lokasi / Kode..."
                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="filter-kategori" class="block text-xs font-semibold uppercase text-gray-600">Kategori</label>
                    <select id="filter-kategori" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>

                <div>
                    <label for="filter-sort" class="block text-xs font-semibold uppercase text-gray-600">Urutkan</label>
                    <select id="filter-sort" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm">
                        <option value="created_at">Terbaru</option>
                        <option value="nama">Nama</option>
                        <option value="harga_tiket">Harga Tiket</option>
                    </select>
                </div>

                <div class="flex items-center h-10">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="filter-hanya-buka" class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm font-medium text-gray-700">Hanya yang Buka</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
                        Terapkan
                    </button>
                    <button type="button" id="btn-reset-filter" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-md">
                        Reset
                    </button>
                </div>
            </form>
        </section>

        <!-- Tabel Data Wisata + Pagination -->
        <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Daftar Destinasi Wisata (JSON API)</h2>
                <span id="loading-indicator" class="hidden text-xs font-semibold text-indigo-600">Memuat data...</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Kode &amp; Nama</th>
                            <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                            <th class="px-4 py-3 text-left font-semibold">Lokasi</th>
                            <th class="px-4 py-3 text-right font-semibold">Harga Tiket</th>
                            <th class="px-4 py-3 text-center font-semibold">Status</th>
                            <th class="px-4 py-3 text-center font-semibold">Pemesanan</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wisata-tbody" class="divide-y divide-gray-200 bg-white">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-600">
                <div id="pagination-info">Menampilkan 0 data</div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-prev-page" disabled
                        class="px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-100 disabled:opacity-50 font-semibold">
                        &laquo; Sebelumnya
                    </button>
                    <span id="page-number-text" class="font-semibold">Halaman 1 / 1</span>
                    <button type="button" id="btn-next-page" disabled
                        class="px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-100 disabled:opacity-50 font-semibold">
                        Berikutnya &raquo;
                    </button>
                </div>
            </div>
        </section>
    </main>
</body>
</html>