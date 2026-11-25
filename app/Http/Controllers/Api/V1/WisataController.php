<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWisataRequest;
use App\Http\Requests\UpdateWisataRequest;
use App\Http\Resources\V1\WisataResource;
use App\Models\Wisata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WisataController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Wisata::with(['kategori', 'fasilitas', 'pengelola'])
            ->withCount('pemesanans');

        // Filter pencarian kata kunci
        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('lokasi', 'like', "%{$cari}%")
                    ->orWhere('deskripsi', 'like', "%{$cari}%")
                    ->orWhere('kode_wisata', 'like', "%{$cari}%");
            });
        }

        // Filter kategori (mendukung ?kategori=ID/slug maupun ?kategori_id=ID)
        $kategoriFilter = $request->input('kategori', $request->input('kategori_id'));
        if (filled($kategoriFilter)) {
            if (is_numeric($kategoriFilter)) {
                $query->where('kategori_wisata_id', (int) $kategoriFilter);
            } else {
                $query->whereHas('kategori', function ($q) use ($kategoriFilter) {
                    $q->where('slug', $kategoriFilter);
                });
            }
        }

        // Filter status buka
        if ($request->boolean('hanya_buka')) {
            $query->buka();
        }

        // Whitelist sorting aman
        $whitelistKolom = ['nama', 'harga_tiket', 'created_at'];
        $sort = in_array($request->query('sort'), $whitelistKolom, true)
            ? $request->query('sort')
            : 'created_at';
        $order = in_array(strtolower((string) $request->query('order')), ['asc', 'desc'], true)
            ? strtolower((string) $request->query('order'))
            : 'desc';

        $query->orderBy($sort, $order);

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        $wisatas = $query->paginate($perPage)->withQueryString();

        return WisataResource::collection($wisatas);
    }

    public function show(Wisata $wisata): WisataResource
    {
        $wisata->load(['kategori', 'fasilitas', 'pengelola'])
            ->loadCount('pemesanans');

        return new WisataResource($wisata);
    }

    #[Middleware('auth:sanctum')]
    public function store(StoreWisataRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['admin', 'pengelola'])) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya admin atau pengelola yang dapat menambah destinasi wisata.',
            ], 403);
        }

        $validated = $request->validated();

        $wisata = new Wisata();
        $wisata->fill($validated);
        $wisata->user_id = $user->id;
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        if ($request->hasFile('gambar')) {
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        if ($request->filled('fasilitas')) {
            $wisata->fasilitas()->sync($request->input('fasilitas', []));
        }

        $wisata->load(['kategori', 'fasilitas', 'pengelola'])
            ->loadCount('pemesanans');

        return (new WisataResource($wisata))
            ->response()
            ->setStatusCode(201);
    }

    #[Middleware('auth:sanctum')]
    public function update(UpdateWisataRequest $request, Wisata $wisata): WisataResource|JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole('admin') && ! ($user->hasRole('pengelola') && $wisata->user_id === $user->id)) {
            return response()->json([
                'message' => 'Akses ditolak. Anda hanya dapat mengubah destinasi wisata milik Anda sendiri.',
            ], 403);
        }

        $validated = $request->validated();

        $wisata->fill($validated);
        $wisata->slug = Str::slug($validated['nama']) . '-' . Str::lower(Str::random(5));

        if ($request->hasFile('gambar')) {
            if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
                Storage::disk('public')->delete($wisata->gambar);
            }
            $wisata->gambar = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata->save();

        if ($request->has('fasilitas')) {
            $wisata->fasilitas()->sync($request->input('fasilitas', []));
        }

        $wisata->load(['kategori', 'fasilitas', 'pengelola'])
            ->loadCount('pemesanans');

        return new WisataResource($wisata);
    }

    #[Middleware('auth:sanctum')]
    public function destroy(Request $request, Wisata $wisata): Response|JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole('admin') && ! ($user->hasRole('pengelola') && $wisata->user_id === $user->id)) {
            return response()->json([
                'message' => 'Akses ditolak. Anda hanya dapat menghapus destinasi wisata milik Anda sendiri.',
            ], 403);
        }

        if ($wisata->gambar && Storage::disk('public')->exists($wisata->gambar)) {
            Storage::disk('public')->delete($wisata->gambar);
        }

        $wisata->fasilitas()->detach();
        $wisata->delete();

        return response()->noContent();
    }
}