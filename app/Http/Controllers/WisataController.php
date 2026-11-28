<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWisataRequest;
use App\Http\Requests\UpdateWisataRequest;
use App\Models\Fasilitas;
use App\Models\KategoriWisata;
use App\Models\Pemesanan;
use App\Models\Wisata;
use Carbon\Carbon;
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

        if ($request->filled('kategori_id')) {
            $query->where('kategori_wisata_id', $request->input('kategori_id'));
        }

        if ($request->boolean('hanya_buka')) {
            $query->buka();
        }

        $user = $request->user();
        if ($user && $user->hasRole('pengelola')) {
            $query->orderByRaw('CASE WHEN user_id = ? THEN 0 ELSE 1 END', [$user->id]);
        }

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

        $fasilitasIds = $request->input('fasilitas', []);
        $fasilitasLainnya = $request->input('fasilitas_lainnya');

        // Susun array metode pembayaran custom
        $metodeList = [];
        $namas = $request->input('metode_nama', []);
        $nomors = $request->input('metode_nomor', []);
        foreach ($namas as $i => $namaMetode) {
            if (! empty($namaMetode) && ! empty($nomors[$i])) {
                $metodeList[] = [
                    'nama' => trim($namaMetode),
                    'nomor' => trim($nomors[$i]),
                ];
            }
        }

        unset($validated['fasilitas'], $validated['fasilitas_lainnya'], $validated['metode_nama'], $validated['metode_nomor']);

        $wisata = new Wisata();
        $wisata->fill($validated);
        $wisata->metode_pembayaran = $metodeList;
        $wisata->user_id = $request->user()->id;
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        if ($request->hasFile('gambar')) {
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        if ($fasilitasLainnya) {
            $tambahan = explode(',', $fasilitasLainnya);
            foreach ($tambahan as $item) {
                $namaBersih = trim($item);
                if ($namaBersih !== '') {
                    $baru = Fasilitas::firstOrCreate(['nama' => $namaBersih]);
                    $fasilitasIds[] = $baru->id;
                }
            }
        }

        $wisata->fasilitas()->sync(array_unique($fasilitasIds));

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata dan metode pembayaran berhasil ditambahkan!');
    }

    public function show(Wisata $wisata): View
    {
        $wisata->load(['kategori', 'fasilitas', 'pengelola']);

        $namaHari = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
        ];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $start = Carbon::today();
        $end = Carbon::today()->endOfMonth();
        $tanggalTersedia = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $tglStr = $date->toDateString();
            $isTutup = $wisata->isTutupPadaTanggal($tglStr);

            $tanggalTersedia[] = [
                'value' => $tglStr,
                'hari' => $namaHari[$date->dayOfWeek],
                'angka' => $date->format('d'),
                'bulan' => $namaBulan[$date->month],
                'is_tutup' => $isTutup,
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

        $fasilitasIds = $request->input('fasilitas', []);
        $fasilitasLainnya = $request->input('fasilitas_lainnya');

        $metodeList = [];
        $namas = $request->input('metode_nama', []);
        $nomors = $request->input('metode_nomor', []);
        foreach ($namas as $i => $namaMetode) {
            if (! empty($namaMetode) && ! empty($nomors[$i])) {
                $metodeList[] = [
                    'nama' => trim($namaMetode),
                    'nomor' => trim($nomors[$i]),
                ];
            }
        }

        unset($validated['fasilitas'], $validated['fasilitas_lainnya'], $validated['metode_nama'], $validated['metode_nomor']);

        $wisata->fill($validated);
        $wisata->metode_pembayaran = $metodeList;
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        if ($request->hasFile('gambar')) {
            if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
                Storage::disk('public')->delete($wisata->gambar);
            }
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        if ($fasilitasLainnya) {
            $tambahan = explode(',', $fasilitasLainnya);
            foreach ($tambahan as $item) {
                $namaBersih = trim($item);
                if ($namaBersih !== '') {
                    $baru = Fasilitas::firstOrCreate(['nama' => $namaBersih]);
                    $fasilitasIds[] = $baru->id;
                }
            }
        }

        $wisata->fasilitas()->sync(array_unique($fasilitasIds));

        if ($wisata->tanggal_tutup_mulai) {
            $mulai = $wisata->tanggal_tutup_mulai->toDateString();
            $selesai = $wisata->tanggal_tutup_selesai ? $wisata->tanggal_tutup_selesai->toDateString() : $mulai;

            Pemesanan::where('wisata_id', $wisata->id)
                ->whereIn('status', ['paid', 'pending'])
                ->whereBetween('tanggal_kunjungan', [$mulai, $selesai])
                ->update([
                    'status' => 'refund_berhasil',
                    'refund_processed_at' => Carbon::now(),
                    'alasan_refund' => "Tiket Anda otomatis direfund (Refund Berhasil) karena destinasi wisata ditutup pada tanggal kunjungan Anda ({$mulai} s/d {$selesai}) oleh pihak pengelola.",
                ]);
        }

        if (! $wisata->status_buka) {
            Pemesanan::where('wisata_id', $wisata->id)
                ->whereIn('status', ['paid', 'pending'])
                ->whereDate('tanggal_kunjungan', '>=', Carbon::today()->toDateString())
                ->update([
                    'status' => 'refund_berhasil',
                    'refund_processed_at' => Carbon::now(),
                    'alasan_refund' => 'Tiket Anda otomatis direfund (Refund Berhasil) karena destinasi wisata sedang ditutup operasionalnya oleh pihak pengelola.',
                ]);
        }

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata berhasil diperbarui!');
    }

    public function destroy(Request $request, Wisata $wisata): RedirectResponse
    {
        $this->authorizeOwnership($request, $wisata);

        Pemesanan::where('wisata_id', $wisata->id)
            ->whereIn('status', ['paid', 'pending'])
            ->update([
                'status' => 'refund_berhasil',
                'refund_processed_at' => Carbon::now(),
                'alasan_refund' => 'Tiket Anda otomatis direfund (Refund Berhasil) karena destinasi wisata (' . $wisata->nama . ') telah dihapus oleh pengelola/admin.',
            ]);

        if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
            Storage::disk('public')->delete($wisata->gambar);
        }

        $wisata->fasilitas()->detach();
        $wisata->delete();

        return redirect()->route('wisata.index')->with('success', 'Destinasi wisata berhasil dihapus dan tiket terkait otomatis diproses refund!');
    }

    private function authorizeOwnership(Request $request, Wisata $wisata): void
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            return;
        }

        if ($wisata->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah atau menghapus destinasi wisata milik pengelola lain.');
        }
    }
}