<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Wisata;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PemesananController extends Controller
{
    private function authorizeAccess(Request $request, Pemesanan $pemesanan): void
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return;
        }

        if ($user->hasRole('pengelola')) {
            if ($pemesanan->wisata && $pemesanan->wisata->user_id === $user->id) {
                return;
            }
            abort(403, 'Anda hanya dapat mengelola pemesanan untuk destinasi wisata milik Anda sendiri.');
        }

        if ($pemesanan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini.');
        }
    }

    public function index(Request $request): View
    {
        Pemesanan::syncExpiredStatuses();

        $user = $request->user();
        $query = Pemesanan::with(['wisata', 'user'])->latest();

        if ($user->hasRole('wisatawan')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('pengelola')) {
            $query->whereHas('wisata', fn ($q) => $q->where('user_id', $user->id));
        }

        // Pencarian berdasarkan tanggal, bulan, dan destinasi (berlaku semua role)
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_kunjungan', $request->input('tanggal'));
        }

        if ($request->filled('bulan')) {
            $bulanInput = $request->input('bulan'); // format YYYY-MM atau 1-12
            if (str_contains($bulanInput, '-')) {
                [$tahun, $bulan] = explode('-', $bulanInput);
                $query->whereYear('tanggal_kunjungan', (int) $tahun)
                      ->whereMonth('tanggal_kunjungan', (int) $bulan);
            } else {
                $query->whereMonth('tanggal_kunjungan', (int) $bulanInput);
            }
        }

        if ($request->filled('wisata_id')) {
            $query->where('wisata_id', $request->input('wisata_id'));
        }

        $pemesanans = $query->paginate(10)->withQueryString();

        $destinasis = $user->hasRole('pengelola')
            ? Wisata::where('user_id', $user->id)->orderBy('nama')->get()
            : Wisata::orderBy('nama')->get();

        return view('pemesanan.index', compact('pemesanans', 'destinasis'));
    }

    // Halaman pembayaran dari detail wisata
    public function checkout(Request $request): View|RedirectResponse
    {
        $request->validate([
            'wisata_id' => ['required', 'exists:wisatas,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'jumlah_tiket' => ['nullable', 'integer', 'between:1,20'],
        ]);

        $wisata = Wisata::findOrFail($request->query('wisata_id'));
        if (! $wisata->status_buka) {
            return redirect()->route('wisata.show', $wisata)->with('error', 'Destinasi wisata sedang tutup.');
        }

        $tanggalKunjungan = $request->query('tanggal_kunjungan');
        $jumlahTiket = (int) $request->query('jumlah_tiket', 1);
        $totalHarga = $wisata->harga_tiket * $jumlahTiket;
        $pemesanan = null;

        return view('pemesanan.pembayaran', compact('wisata', 'tanggalKunjungan', 'jumlahTiket', 'totalHarga', 'pemesanan'));
    }

    // Proses tombol "Bayar" atau "Kembali" dari checkout baru
    public function processCheckout(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'bayar');

        if ($action === 'kembali') {
            $validated = $request->validate([
                'wisata_id' => ['required', 'exists:wisatas,id'],
                'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
                'jumlah_tiket' => ['required', 'integer', 'between:1,20'],
                'nama_pemesan' => ['nullable', 'string', 'max:255'],
                'email_pemesan' => ['nullable', 'email', 'max:255'],
                'no_telepon' => ['nullable', 'string', 'max:30'],
                'alamat' => ['nullable', 'string', 'max:500'],
                'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            ]);

            $wisata = Wisata::findOrFail($validated['wisata_id']);
            $user = $request->user();

            $pemesanan = new Pemesanan([
                'kode_tiket' => 'TKT-' . strtoupper(Str::random(8)),
                'wisata_id' => $wisata->id,
                'nama_pemesan' => $validated['nama_pemesan'] ?: $user->name,
                'email_pemesan' => $validated['email_pemesan'] ?: $user->email,
                'no_telepon' => $validated['no_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'jumlah_tiket' => $validated['jumlah_tiket'],
                'total_harga' => $wisata->harga_tiket * $validated['jumlah_tiket'],
                'status' => 'pending',
                'batas_bayar' => Carbon::now()->addMinutes(5),
            ]);
            $pemesanan->user_id = $user->id;
            $pemesanan->save();

            return redirect()->route('wisata.index')->with('info', 'Pembayaran berstatus pending. Selesaikan pembayaran dalam 5 menit.');
        }

        // Jika klik tombol "Bayar"
        $validated = $request->validate([
            'wisata_id' => ['required', 'exists:wisatas,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'jumlah_tiket' => ['required', 'integer', 'between:1,20'],
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'email_pemesan' => ['required', 'email', 'max:255'],
            'no_telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:500'],
            'metode_pembayaran' => ['required', 'in:transfer_bank,qris,e_wallet,virtual_account'],
        ]);

        $wisata = Wisata::findOrFail($validated['wisata_id']);

        $pemesanan = new Pemesanan([
            'kode_tiket' => 'TKT-' . strtoupper(Str::random(8)),
            'wisata_id' => $wisata->id,
            'nama_pemesan' => $validated['nama_pemesan'],
            'email_pemesan' => $validated['email_pemesan'],
            'no_telepon' => $validated['no_telepon'],
            'alamat' => $validated['alamat'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
            'jumlah_tiket' => $validated['jumlah_tiket'],
            'total_harga' => $wisata->harga_tiket * $validated['jumlah_tiket'],
            'status' => 'paid',
            'batas_bayar' => null,
            'paid_at' => Carbon::now(),
        ]);
        $pemesanan->user_id = $request->user()->id;
        $pemesanan->save();

        return redirect()->route('pemesanan.index')->with('success', 'Pembayaran berhasil! Kode Tiket Anda: ' . $pemesanan->kode_tiket);
    }

    // Halaman pembayaran untuk tiket yang sudah berstatus pending
    public function payPending(Request $request, Pemesanan $pemesanan): View|RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();
        $this->authorizeAccess($request, $pemesanan);

        if (! $pemesanan->isPendingActive()) {
            return redirect()->route('pemesanan.index')->with('error', 'Pemesanan tidak dapat dibayar atau batas waktu 5 menit telah habis.');
        }

        $wisata = $pemesanan->wisata;
        $tanggalKunjungan = $pemesanan->tanggal_kunjungan->toDateString();
        $jumlahTiket = $pemesanan->jumlah_tiket;
        $totalHarga = $pemesanan->total_harga;

        return view('pemesanan.pembayaran', compact('wisata', 'tanggalKunjungan', 'jumlahTiket', 'totalHarga', 'pemesanan'));
    }

    // Proses pembayaran tiket pending
    public function processPending(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();
        $this->authorizeAccess($request, $pemesanan);

        if (! $pemesanan->isPendingActive()) {
            return redirect()->route('pemesanan.index')->with('error', 'Batas waktu pembayaran 5 menit telah habis.');
        }

        if ($request->input('action') === 'kembali') {
            return redirect()->route('wisata.index');
        }

        $validated = $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'email_pemesan' => ['required', 'email', 'max:255'],
            'no_telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:500'],
            'metode_pembayaran' => ['required', 'in:transfer_bank,qris,e_wallet,virtual_account'],
        ]);

        $pemesanan->update([
            'nama_pemesan' => $validated['nama_pemesan'],
            'email_pemesan' => $validated['email_pemesan'],
            'no_telepon' => $validated['no_telepon'],
            'alamat' => $validated['alamat'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status' => 'paid',
            'batas_bayar' => null,
            'paid_at' => Carbon::now(),
        ]);

        return redirect()->route('pemesanan.index')->with('success', 'Pembayaran tiket ' . $pemesanan->kode_tiket . ' berhasil!');
    }

    public function show(Request $request, Pemesanan $pemesanan): View
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();
        $this->authorizeAccess($request, $pemesanan);
        $pemesanan->load(['wisata.kategori', 'user', 'petugas']);

        return view('pemesanan.show', compact('pemesanan'));
    }

    // Cetak Invoice PDF (hanya jika sudah paid / sudah_digunakan)
    public function invoice(Request $request, Pemesanan $pemesanan): Response|RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();
        $this->authorizeAccess($request, $pemesanan);

        if (! in_array($pemesanan->status, ['paid', 'sudah_digunakan'], true)) {
            return redirect()->route('pemesanan.index')->with('error', 'Invoice hanya tersedia untuk tiket yang sudah dibayar.');
        }

        $pemesanan->load(['wisata.kategori', 'user']);
        $pdf = Pdf::loadView('pemesanan.invoice', compact('pemesanan'));

        return $pdf->download('invoice-' . $pemesanan->kode_tiket . '.pdf');
    }

    // Form Pengajuan Refund (Wisatawan)
    public function refundForm(Request $request, Pemesanan $pemesanan): View|RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();

        if ($pemesanan->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $pemesanan->isRefundable()) {
            return redirect()->route('pemesanan.index')->with('error', 'Tiket tidak memenuhi syarat refund.');
        }

        $pemesanan->load('wisata');

        return view('pemesanan.refund', compact('pemesanan'));
    }

    public function refundStore(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();
        $pemesanan->refresh();

        if ($pemesanan->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $pemesanan->isRefundable()) {
            return redirect()->route('pemesanan.index')->with('error', 'Tiket tidak dapat di-refund karena sudah kadaluarsa, digunakan, atau belum paid.');
        }

        $validated = $request->validate([
            'alasan_refund' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'alasan_refund.required' => 'Alasan pengajuan refund wajib diisi.',
        ]);

        $pemesanan->update([
            'status' => 'refund',
            'alasan_refund' => $validated['alasan_refund'],
            'refund_requested_at' => Carbon::now(),
        ]);

        return redirect()->route('pemesanan.index')->with('success', 'Pengajuan refund untuk tiket ' . $pemesanan->kode_tiket . ' telah dikirim.');
    }

    // Persetujuan / Penolakan Refund oleh Admin atau Pengelola pemilik wisata
    public function refundDecision(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorizeAccess($request, $pemesanan);

        if ($pemesanan->status !== 'refund') {
            return redirect()->route('pemesanan.show', $pemesanan)->with('error', 'Pemesanan ini tidak sedang mengajukan refund.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:setujui,tolak'],
        ]);

        if ($validated['decision'] === 'setujui') {
            $pemesanan->update([
                'status' => 'refund_berhasil',
                'refund_processed_at' => Carbon::now(),
            ]);
            $msg = 'Refund disetujui. Status pemesanan berubah menjadi Refund Berhasil.';
        } else {
            $pemesanan->update([
                'status' => 'paid',
                'refund_processed_at' => Carbon::now(),
            ]);
            $msg = 'Refund ditolak. Status pemesanan dikembalikan menjadi Paid.';
        }

        return redirect()->route('pemesanan.show', $pemesanan)->with('success', $msg);
    }

    public function destroy(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorizeAccess($request, $pemesanan);
        $pemesanan->delete();

        return redirect()->route('pemesanan.index')->with('success', 'Data pemesanan tiket berhasil dihapus!');
    }
}