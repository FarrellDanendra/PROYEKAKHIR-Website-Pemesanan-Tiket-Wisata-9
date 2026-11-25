<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWisataRequest;
use App\Http\Requests\UpdateWisataRequest;
use App\Models\Fasilitas;
use App\Models\KategoriWisata;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WisataController extends Controller
{
    public function index(Request $request): View|Response
    {
        $query = Wisata::with(['kategori', 'fasilitas', 'pengelola'])
            ->withCount('pemesanans');

        // 1. Pencarian Kata Kunci
        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q
                    ->where('nama', 'like', "%{$cari}%")
                    ->orWhere('lokasi', 'like', "%{$cari}%")
                    ->orWhere('deskripsi', 'like', "%{$cari}%")
                    ->orWhere('kode_wisata', 'like', "%{$cari}%");
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_wisata_id', $request->input('kategori_id'));
        }

        // 3. Filter Checkbox Status Buka (menggunakan scopeBuka)
        if ($request->boolean('hanya_buka')) {
            $query->buka();
        }

        // 4. Whitelist Sorting Aman
        $whitelistKolom = ['nama', 'harga_tiket', 'created_at'];
        $sort = in_array($request->query('sort'), $whitelistKolom) ? $request->query('sort') : 'created_at';
        $order = in_array(strtolower($request->query('order', '')), ['asc', 'desc']) ? strtolower($request->query('order')) : 'desc';

        $query->orderBy($sort, $order);

        $wisatas = $query->paginate(10)->withQueryString();
        $kategoris = KategoriWisata::orderBy('nama')->get();
        if ($request->ajax()) {
            return response()->view('wisata.list', compact('wisatas'));
        }
        return view('wisata.index', compact('wisatas', 'kategoris', 'sort', 'order'));
    }

    public function create(): View
    {
        $kategoris = KategoriWisata::orderBy('nama')->get();
        $fasilitas = Fasilitas::orderBy('nama')->get();

        return view('wisata.create', compact('kategoris', 'fasilitas'));
    }

    public function store(StoreWisataRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $wisata = new Wisata();
        $wisata->fill($validated);

        // Mengisi user_id secara eksplisit dari user yang sedang login
        $wisata->user_id = $request->user()->id;
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        // Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        // Sinkronisasi pivot fasilitas
        if ($request->filled('fasilitas')) {
            $wisata->fasilitas()->sync($request->input('fasilitas'));
        }

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata berhasil ditambahkan!');
    }

    public function show(Wisata $wisata): View
    {
        $wisata->load(['kategori', 'fasilitas', 'pengelola']);

        $namaHari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $namaBulan = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $start = \Carbon\Carbon::today();
        $end = \Carbon\Carbon::today()->endOfMonth();
        $tanggalTersedia = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $tanggalTersedia[] = [
                'value' => $date->toDateString(),
                'hari' => $namaHari[$date->dayOfWeek],
                'angka' => $date->format('d'),
                'bulan' => $namaBulan[$date->month],
            ];
        }

        return view('wisata.show', compact('wisata', 'tanggalTersedia'));
    }

    public function edit(Request $request, Wisata $wisata): View
    {
        $this->authorizeOwnership($request, $wisata);
        $wisata->load('fasilitas');
        $kategoris = KategoriWisata::orderBy('nama')->get();
        $fasilitas = Fasilitas::orderBy('nama')->get();
        return view('wisata.edit', compact('wisata', 'kategoris', 'fasilitas'));
    }

    public function update(UpdateWisataRequest $request, Wisata $wisata): RedirectResponse
    {
        $this->authorizeOwnership($request, $wisata);
        $validated = $request->validated();

        $wisata->fill($validated);
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        // Update Gambar jika ada upload baru
        if ($request->hasFile('gambar')) {
            if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
                Storage::disk('public')->delete($wisata->gambar);
            }
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        // Sinkronisasi pivot fasilitas
        $wisata->fasilitas()->sync($request->input('fasilitas', []));

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata berhasil diperbarui!');
    }

    public function destroy(Request $request, Wisata $wisata): RedirectResponse
    {
        $this->authorizeOwnership($request, $wisata);
        if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
            Storage::disk('public')->delete($wisata->gambar);
        }

        $wisata->fasilitas()->detach();
        $wisata->delete();

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata berhasil dihapus!');
    }

    private function authorizeOwnership(Request $request, Wisata $wisata): void
    {
        $user = $request->user();
        if (!$user->hasRole('admin') && $wisata->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah atau menghapus destinasi wisata milik pengelola lain.');
        }
    }
}
