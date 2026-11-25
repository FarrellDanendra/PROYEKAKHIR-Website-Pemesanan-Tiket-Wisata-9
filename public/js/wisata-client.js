'use strict';

const API_BASE = '/api/v1';
const STORAGE_TOKEN_KEY = 'wisata_api_token';
const STORAGE_USER_KEY = 'wisata_api_user';

class ApiError extends Error {
    constructor(status, message, errors = null, payload = null) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.errors = errors;
        this.payload = payload;
    }
}

const state = {
    page: 1,
    lastPage: 1,
    cari: '',
    kategori: '',
    sort: 'created_at',
    hanyaBuka: false,
    wisataMap: new Map(),
};

const el = {
    alertBox: document.getElementById('alert-box'),
    authStatusText: document.getElementById('auth-status-text'),
    authLoggedIn: document.getElementById('auth-logged-in'),
    userNameBadge: document.getElementById('user-name-badge'),
    userEmailBadge: document.getElementById('user-email-badge'),
    userRoleBadge: document.getElementById('user-role-badge'),
    loginForm: document.getElementById('login-form'),
    loginEmail: document.getElementById('login-email'),
    loginPassword: document.getElementById('login-password'),
    quickLoginGroup: document.getElementById('quick-login-group'),
    btnCekMe: document.getElementById('btn-cek-me'),
    btnLogout: document.getElementById('btn-logout'),
    crudSection: document.getElementById('crud-section'),
    formTitle: document.getElementById('form-title'),
    btnResetForm: document.getElementById('btn-reset-form'),
    wisataForm: document.getElementById('wisata-form'),
    wisataId: document.getElementById('wisata-id'),
    kodeWisata: document.getElementById('kode_wisata'),
    nama: document.getElementById('nama'),
    kategoriWisataId: document.getElementById('kategori_wisata_id'),
    hargaTiket: document.getElementById('harga_tiket'),
    lokasi: document.getElementById('lokasi'),
    tanggalBuka: document.getElementById('tanggal_buka'),
    deskripsi: document.getElementById('deskripsi'),
    statusBuka: document.getElementById('status_buka'),
    btnSubmitWisata: document.getElementById('btn-submit-wisata'),
    filterForm: document.getElementById('filter-form'),
    filterCari: document.getElementById('filter-cari'),
    filterKategori: document.getElementById('filter-kategori'),
    filterSort: document.getElementById('filter-sort'),
    filterHanyaBuka: document.getElementById('filter-hanya-buka'),
    btnResetFilter: document.getElementById('btn-reset-filter'),
    loadingIndicator: document.getElementById('loading-indicator'),
    wisataTbody: document.getElementById('wisata-tbody'),
    paginationInfo: document.getElementById('pagination-info'),
    pageNumberText: document.getElementById('page-number-text'),
    btnPrevPage: document.getElementById('btn-prev-page'),
    btnNextPage: document.getElementById('btn-next-page'),
};

/**
 * Mencegah DOM XSS dengan meng-escape karakter khusus HTML.
 */
function esc(nilai) {
    if (nilai === null || nilai === undefined) {
        return '';
    }
    return String(nilai)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getToken() {
    return localStorage.getItem(STORAGE_TOKEN_KEY);
}

function getUser() {
    try {
        const raw = localStorage.getItem(STORAGE_USER_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

function saveAuth(token, user) {
    localStorage.setItem(STORAGE_TOKEN_KEY, token);
    localStorage.setItem(STORAGE_USER_KEY, JSON.stringify(user));
}

function clearAuth() {
    localStorage.removeItem(STORAGE_TOKEN_KEY);
    localStorage.removeItem(STORAGE_USER_KEY);
}

function tampilkanPesan(pesan, tipe = 'info', daftarError = null) {
    const gaya = {
        success: 'bg-emerald-50 border-emerald-300 text-emerald-800',
        error: 'bg-red-50 border-red-300 text-red-800',
        info: 'bg-indigo-50 border-indigo-300 text-indigo-800',
    };

    el.alertBox.className = `rounded-lg p-4 text-sm border ${gaya[tipe] || gaya.info}`;

    let html = `<p class="font-semibold">${esc(pesan)}</p>`;
    if (daftarError && typeof daftarError === 'object') {
        const items = Object.values(daftarError)
            .flat()
            .map((msg) => `<li>${esc(msg)}</li>`)
            .join('');
        if (items) {
            html += `<ul class="mt-2 list-disc list-inside text-xs space-y-0.5">${items}</ul>`;
        }
    }

    el.alertBox.innerHTML = html;
    el.alertBox.classList.remove('hidden');
}

function sembunyikanPesan() {
    el.alertBox.classList.add('hidden');
    el.alertBox.innerHTML = '';
}

/**
 * Fungsi terpusat untuk seluruh panggilan Fetch API ke /api/v1.
 */
async function apiRequest(endpoint, options = {}) {
    const token = getToken();
    const headers = {
        Accept: 'application/json',
        ...(options.headers || {}),
    };

    let body = options.body;
    if (body && !(body instanceof FormData) && typeof body === 'object') {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(body);
    }

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE}${endpoint}`, {
        method: options.method || 'GET',
        headers,
        body,
    });

    if (response.status === 204) {
        return null;
    }

    let payload = null;
    const contentType = response.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
        payload = await response.json();
    }

    if (!response.ok) {
        const pesan = payload?.message || `Permintaan gagal (HTTP ${response.status}).`;
        throw new ApiError(response.status, pesan, payload?.errors || null, payload);
    }

    return payload;
}

/**
 * Penanganan error terpusat berdasarkan kode status HTTP.
 */
function tanganiError(err) {
    if (err instanceof ApiError) {
        if (err.status === 401) {
            clearAuth();
            sinkronkanUIAuth();
            tampilkanPesan(`401 Unauthorized: ${err.message}`, 'error');
            return;
        }
        if (err.status === 403) {
            tampilkanPesan(`403 Forbidden: ${err.message}`, 'error');
            return;
        }
        if (err.status === 404) {
            tampilkanPesan(`404 Not Found: ${err.message}`, 'error');
            return;
        }
        if (err.status === 422) {
            tampilkanPesan(`422 Validasi Gagal: ${err.message}`, 'error', err.errors);
            return;
        }
        tampilkanPesan(`HTTP ${err.status}: ${err.message}`, 'error');
        return;
    }

    tampilkanPesan(`Kesalahan jaringan: ${err?.message || 'Tidak dapat terhubung ke server.'}`, 'error');
}

function sinkronkanUIAuth() {
    const token = getToken();
    const user = getUser();

    if (token && user) {
        el.authStatusText.textContent = 'Terautentikasi dengan Bearer Token di localStorage.';
        el.userNameBadge.textContent = user.name || '-';
        el.userEmailBadge.textContent = user.email || '-';
        el.userRoleBadge.textContent = user.role || '-';
        el.authLoggedIn.classList.remove('hidden');
        el.loginForm.classList.add('hidden');

        const bolehKelola = user.role === 'admin' || user.role === 'pengelola';
        el.crudSection.classList.toggle('hidden', !bolehKelola);
    } else {
        el.authStatusText.textContent = 'Belum login. Publik hanya dapat melihat daftar & detail wisata.';
        el.authLoggedIn.classList.add('hidden');
        el.loginForm.classList.remove('hidden');
        el.crudSection.classList.add('hidden');
        resetFormWisata();
    }
}

function formatRupiah(nominal) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(nominal || 0));
}

async function muatKategori() {
    try {
        const res = await apiRequest('/kategori-wisata');
        const list = res?.data || [];

        const filterOptions = ['<option value="">Semua Kategori</option>']
            .concat(list.map((k) => `<option value="${esc(k.id)}">${esc(k.nama)}</option>`))
            .join('');
        el.filterKategori.innerHTML = filterOptions;

        const formOptions = ['<option value="">-- Pilih Kategori --</option>']
            .concat(list.map((k) => `<option value="${esc(k.id)}">${esc(k.nama)}</option>`))
            .join('');
        el.kategoriWisataId.innerHTML = formOptions;
    } catch (err) {
        tanganiError(err);
    }
}

async function muatWisata() {
    el.loadingIndicator.classList.remove('hidden');

    const params = new URLSearchParams();
    params.set('page', String(state.page));
    params.set('per_page', '5');
    params.set('sort', state.sort);
    if (state.cari) {
        params.set('cari', state.cari);
    }
    if (state.kategori) {
        params.set('kategori', state.kategori);
    }
    if (state.hanyaBuka) {
        params.set('hanya_buka', '1');
    }

    try {
        const res = await apiRequest(`/wisata?${params.toString()}`);
        const items = res?.data || [];
        const meta = res?.meta || {};

        state.wisataMap.clear();
        items.forEach((item) => state.wisataMap.set(Number(item.id), item));

        renderTabelWisata(items);
        renderPagination(meta);
    } catch (err) {
        tanganiError(err);
    } finally {
        el.loadingIndicator.classList.add('hidden');
    }
}

function renderTabelWisata(items) {
    if (!items.length) {
        el.wisataTbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                    Tidak ada destinasi wisata yang sesuai filter.
                </td>
            </tr>
        `;
        return;
    }

    const user = getUser();

    el.wisataTbody.innerHTML = items.map((w) => {
        const badgeStatus = w.status_buka
            ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Buka</span>'
            : '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Tutup</span>';

        const bolehUbah = user && (
            user.role === 'admin' ||
            (user.role === 'pengelola' && Number(w.user_id) === Number(user.id))
        );

        const tombolAksi = bolehUbah
            ? `
                <button type="button" data-action="edit" data-id="${esc(w.id)}"
                    class="px-2.5 py-1 text-xs font-semibold rounded bg-amber-100 hover:bg-amber-200 text-amber-800">
                    Edit
                </button>
                <button type="button" data-action="hapus" data-id="${esc(w.id)}"
                    class="px-2.5 py-1 text-xs font-semibold rounded bg-rose-100 hover:bg-rose-200 text-rose-800">
                    Hapus
                </button>
            `
            : `<span class="text-xs text-gray-400">Hanya Lihat</span>`;

        return `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-900">${esc(w.nama)}</div>
                    <div class="text-xs font-mono text-gray-500">${esc(w.kode_wisata || '-')}</div>
                    <div class="text-xs text-gray-400">Pengelola: ${esc(w.pengelola?.name || '-')}</div>
                </td>
                <td class="px-4 py-3 text-gray-700">${esc(w.kategori?.nama || '-')}</td>
                <td class="px-4 py-3 text-gray-600">${esc(w.lokasi)}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">${esc(formatRupiah(w.harga_tiket))}</td>
                <td class="px-4 py-3 text-center">${badgeStatus}</td>
                <td class="px-4 py-3 text-center font-semibold text-gray-700">${esc(w.pemesanans_count ?? 0)}x</td>
                <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                    <button type="button" data-action="detail" data-id="${esc(w.id)}"
                        class="px-2.5 py-1 text-xs font-semibold rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700">
                        Detail
                    </button>
                    ${tombolAksi}
                </td>
            </tr>
        `;
    }).join('');
}

function renderPagination(meta) {
    const current = Number(meta.current_page || 1);
    const last = Number(meta.last_page || 1);
    const total = Number(meta.total || 0);
    const from = meta.from ?? 0;
    const to = meta.to ?? 0;

    state.page = current;
    state.lastPage = last;

    el.paginationInfo.textContent = `Menampilkan ${from}-${to} dari ${total} destinasi wisata`;
    el.pageNumberText.textContent = `Halaman ${current} / ${last}`;
    el.btnPrevPage.disabled = current <= 1;
    el.btnNextPage.disabled = current >= last;
}

function isiFormUntukEdit(wisata) {
    el.wisataId.value = wisata.id;
    el.kodeWisata.value = wisata.kode_wisata || '';
    el.nama.value = wisata.nama || '';
    el.kategoriWisataId.value = wisata.kategori_wisata_id || '';
    el.hargaTiket.value = wisata.harga_tiket ?? '';
    el.lokasi.value = wisata.lokasi || '';
    el.tanggalBuka.value = wisata.tanggal_buka || '';
    el.deskripsi.value = wisata.deskripsi || '';
    el.statusBuka.checked = Boolean(wisata.status_buka);

    el.formTitle.textContent = `Edit Destinasi: ${wisata.nama}`;
    el.btnSubmitWisata.textContent = 'Perbarui Wisata';
    el.btnResetForm.classList.remove('hidden');
    el.crudSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetFormWisata() {
    el.wisataForm.reset();
    el.wisataId.value = '';
    el.statusBuka.checked = true;
    el.formTitle.textContent = 'Tambah Destinasi Wisata';
    el.btnSubmitWisata.textContent = 'Simpan Wisata';
    el.btnResetForm.classList.add('hidden');
}

// Event Listeners
el.quickLoginGroup.addEventListener('click', (event) => {
    const btn = event.target.closest('.quick-email');
    if (!btn) {
        return;
    }
    el.loginEmail.value = btn.dataset.email || '';
    el.loginPassword.value = 'password';
});

el.loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    sembunyikanPesan();

    try {
        const res = await apiRequest('/login', {
            method: 'POST',
            body: {
                email: el.loginEmail.value.trim(),
                password: el.loginPassword.value,
                device_name: 'klien-web-fetch',
            },
        });

        saveAuth(res.access_token, res.user);
        sinkronkanUIAuth();
        await muatWisata();
        tampilkanPesan(`Selamat datang, ${res.user.name} (Role: ${res.user.role}).`, 'success');
    } catch (err) {
        tanganiError(err);
    }
});

el.btnCekMe.addEventListener('click', async () => {
    sembunyikanPesan();
    try {
        const res = await apiRequest('/me');
        const user = res.user;
        saveAuth(getToken(), user);
        sinkronkanUIAuth();
        tampilkanPesan(`Token valid untuk ${user.email} (${user.role}).`, 'info');
    } catch (err) {
        tanganiError(err);
    }
});

el.btnLogout.addEventListener('click', async () => {
    sembunyikanPesan();
    try {
        await apiRequest('/logout', { method: 'POST' });
    } catch {
        // Tetap hapus token lokal meskipun token sudah kedaluwarsa di server
    } finally {
        clearAuth();
        sinkronkanUIAuth();
        await muatWisata();
        tampilkanPesan('Logout berhasil. Token telah dihapus dari localStorage.', 'info');
    }
});

el.filterForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    state.cari = el.filterCari.value.trim();
    state.kategori = el.filterKategori.value;
    state.sort = el.filterSort.value;
    state.hanyaBuka = el.filterHanyaBuka.checked;
    state.page = 1;
    await muatWisata();
});

el.btnResetFilter.addEventListener('click', async () => {
    el.filterForm.reset();
    state.cari = '';
    state.kategori = '';
    state.sort = 'created_at';
    state.hanyaBuka = false;
    state.page = 1;
    await muatWisata();
});

el.btnPrevPage.addEventListener('click', async () => {
    if (state.page > 1) {
        state.page -= 1;
        await muatWisata();
    }
});

el.btnNextPage.addEventListener('click', async () => {
    if (state.page < state.lastPage) {
        state.page += 1;
        await muatWisata();
    }
});

el.btnResetForm.addEventListener('click', () => {
    resetFormWisata();
});

el.wisataForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    sembunyikanPesan();

    const id = el.wisataId.value;
    const payload = {
        kode_wisata: el.kodeWisata.value.trim(),
        nama: el.nama.value.trim(),
        kategori_wisata_id: Number(el.kategoriWisataId.value),
        harga_tiket: Number(el.hargaTiket.value),
        lokasi: el.lokasi.value.trim(),
        deskripsi: el.deskripsi.value.trim(),
        status_buka: el.statusBuka.checked,
        tanggal_buka: el.tanggalBuka.value || null,
    };

    try {
        if (id) {
            await apiRequest(`/wisata/${id}`, { method: 'PUT', body: payload });
            tampilkanPesan('Destinasi wisata berhasil diperbarui (200 OK).', 'success');
        } else {
            await apiRequest('/wisata', { method: 'POST', body: payload });
            tampilkanPesan('Destinasi wisata berhasil ditambahkan (201 Created).', 'success');
        }

        resetFormWisata();
        await muatWisata();
    } catch (err) {
        tanganiError(err);
    }
});

// Event Delegation pada <tbody> untuk Detail, Edit, dan Hapus
el.wisataTbody.addEventListener('click', async (event) => {
    const btn = event.target.closest('button[data-action]');
    if (!btn) {
        return;
    }

    const action = btn.dataset.action;
    const id = Number(btn.dataset.id);

    sembunyikanPesan();

    if (action === 'detail') {
        try {
            const res = await apiRequest(`/wisata/${id}`);
            const d = res.data;
            const fasilitasText = (d.fasilitas || []).map((f) => f.nama).join(', ') || 'Tidak ada';
            tampilkanPesan(
                `Detail "${d.nama}" (${d.kode_wisata}) — ${d.lokasi} | Harga: ${formatRupiah(d.harga_tiket)} | Fasilitas: ${fasilitasText}`,
                'info'
            );
        } catch (err) {
            tanganiError(err);
        }
        return;
    }

    if (action === 'edit') {
        const wisata = state.wisataMap.get(id);
        if (wisata) {
            isiFormUntukEdit(wisata);
        }
        return;
    }

    if (action === 'hapus') {
        if (!window.confirm('Apakah Anda yakin ingin menghapus destinasi wisata ini?')) {
            return;
        }

        try {
            await apiRequest(`/wisata/${id}`, { method: 'DELETE' });
            tampilkanPesan('Destinasi wisata berhasil dihapus (204 No Content).', 'success');
            await muatWisata();
        } catch (err) {
            tanganiError(err);
        }
    }
});

// Inisialisasi Halaman
document.addEventListener('DOMContentLoaded', async () => {
    sinkronkanUIAuth();
    await muatKategori();
    await muatWisata();
});