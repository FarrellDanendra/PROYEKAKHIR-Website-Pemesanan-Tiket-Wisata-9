<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemesananRequest;
use App\Http\Requests\UpdatePemesananRequest;
use App\Models\Pemesanan;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PemesananController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Pemesanan::with(['wisata', 'user'])->latest();

        // Batasi tampilan jika wisatawan hanya melihat tiket miliknya sendiri
        if ($user->hasRole('wisatawan')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('pengelola')) {
            $query->whereHas('wisata', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $pemesanans = $query->paginate(10)->withQueryString();

        return view('pemesanan.index', compact('pemesanans'));
    }

    public function create(Request $request): View
    {
        $wisataId = $request->query('wisata_id');
        $wisataPilihan = $wisataId ? Wisata::find($wisataId) : null;
        $wisatas = Wisata::buka()->orderBy('nama')->get();

        return view('pemesanan.create', compact('wisatas', 'wisataPilihan'));
    }

    public function store(StorePemesananRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $wisata = Wisata::findOrFail($validated['wisata_id']);
        $totalHarga = $wisata->harga_tiket * $validated['jumlah_tiket'];

        $pemesanan = new Pemesanan();
        $pemesanan->fill($validated);

        // Isi data wajib secara eksplisit
        $pemesanan->user_id = $request->user()->id;
        $pemesanan->kode_tiket = 'TKT-' . strtoupper(Str::random(8));
        $pemesanan->total_harga = $totalHarga;
        $pemesanan->status = 'pending';
        $pemesanan->save();

        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan tiket berhasil dibuat dengan Kode Tiket: ' . $pemesanan->kode_tiket);
    }

    public function show(Pemesanan $pemesanan): View
    {
        $pemesanan->load(['wisata.kategori', 'user']);

        return view('pemesanan.show', compact('pemesanan'));
    }

    public function update(UpdatePemesananRequest $request, Pemesanan $pemesanan): RedirectResponse
    {
        $validated = $request->validated();
        $pemesanan->update($validated);

        return redirect()->route('pemesanan.show', $pemesanan)->with('success', 'Status pemesanan tiket berhasil diperbarui!');
    }

    public function destroy(Pemesanan $pemesanan): RedirectResponse
    {
        $pemesanan->delete();

        return redirect()->route('pemesanan.index')->with('success', 'Data pemesanan tiket berhasil dihapus!');
    }
}