<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Wisata;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetugasController extends Controller
{
    public function checkinForm(): View
    {
        return view('petugas.checkin');
    }

    public function checkinStore(Request $request): RedirectResponse
    {
        Pemesanan::syncExpiredStatuses();

        $validated = $request->validate([
            'kode_tiket' => ['required', 'string', 'max:50'],
        ], [
            'kode_tiket.required' => 'Kode pemesanan tiket wajib diisi.',
        ]);

        $kode = strtoupper(trim($validated['kode_tiket']));
        $pemesanan = Pemesanan::with(['wisata', 'user'])->where('kode_tiket', $kode)->first();

        if (! $pemesanan) {
            return back()->withErrors(['kode_tiket' => 'Kode pemesanan tidak ditemukan.'])->withInput();
        }

        if ($pemesanan->status === 'sudah_digunakan') {
            return back()->withErrors(['kode_tiket' => 'Kode tersebut sudah diinputkan dan sudah digunakan!'])->withInput();
        }

        if ($pemesanan->status !== 'paid') {
            return back()->withErrors([
                'kode_tiket' => 'Kode pemesanan tidak dapat dikonfirmasi karena status saat ini "' . $pemesanan->status . '". Hanya tiket berstatus paid yang dapat dikonfirmasi.',
            ])->withInput();
        }

        $pemesanan->update([
            'status' => 'sudah_digunakan',
            'used_at' => Carbon::now(),
            'verified_by' => $request->user()->id,
        ]);

        return redirect()->route('petugas.show', $pemesanan)
            ->with('success', 'Kode pemesanan berhasil dikonfirmasi dan status berubah menjadi Sudah Digunakan.');
    }

    public function show(Pemesanan $pemesanan): View
    {
        $pemesanan->load(['wisata.kategori', 'user', 'petugas']);

        return view('petugas.show', compact('pemesanan'));
    }

    public function dashboard(Request $request): View
    {
        Pemesanan::syncExpiredStatuses();

        $query = Pemesanan::with(['wisata', 'user', 'petugas'])
            ->where('status', 'sudah_digunakan')
            ->latest('used_at');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_kunjungan', $request->input('tanggal'));
        }

        if ($request->filled('bulan')) {
            $bulanInput = $request->input('bulan');
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

        $riwayats = $query->paginate(10)->withQueryString();
        $destinasis = Wisata::orderBy('nama')->get();

        return view('petugas.dashboard', compact('riwayats', 'destinasis'));
    }
}