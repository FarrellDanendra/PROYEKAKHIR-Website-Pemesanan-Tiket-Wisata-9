<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePemesananRequest;
use App\Http\Requests\UpdatePemesananRequest;
use App\Http\Resources\V1\PemesananResource;
use App\Models\Pemesanan;
use App\Models\Wisata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Str;

#[Middleware('auth:sanctum')]
class PemesananController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $query = Pemesanan::with(['wisata.kategori', 'user'])->latest();

        if ($user->hasRole('wisatawan')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('pengelola')) {
            $query->whereHas('wisata', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        return PemesananResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function store(StorePemesananRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['wisatawan', 'admin'])) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya wisatawan atau admin yang dapat membuat pemesanan tiket.',
            ], 403);
        }

        $validated = $request->validated();
        $wisata = Wisata::findOrFail($validated['wisata_id']);

        if (! $wisata->status_buka) {
            return response()->json([
                'message' => 'Destinasi wisata sedang tutup dan tidak menerima pemesanan.',
                'errors' => [
                    'wisata_id' => ['Destinasi wisata ini sedang berstatus tutup.'],
                ],
            ], 422);
        }

        $pemesanan = new Pemesanan();
        $pemesanan->fill($validated);
        $pemesanan->user_id = $user->id;
        $pemesanan->kode_tiket = 'TKT-' . strtoupper(Str::random(8));
        $pemesanan->total_harga = $wisata->harga_tiket * $validated['jumlah_tiket'];
        $pemesanan->status = 'pending';
        $pemesanan->save();

        $pemesanan->load(['wisata.kategori', 'user']);

        return (new PemesananResource($pemesanan))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Pemesanan $pemesanan): PemesananResource|JsonResponse
    {
        $user = $request->user();
        $pemesanan->load(['wisata.kategori', 'user']);

        $isOwner = $pemesanan->user_id === $user->id;
        $isWisataPengelola = $user->hasRole('pengelola')
            && $pemesanan->wisata !== null
            && $pemesanan->wisata->user_id === $user->id;

        if (! $user->hasRole('admin') && ! $isOwner && ! $isWisataPengelola) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak berhak melihat pemesanan ini.',
            ], 403);
        }

        return new PemesananResource($pemesanan);
    }

    public function update(UpdatePemesananRequest $request, Pemesanan $pemesanan): PemesananResource|JsonResponse
    {
        $user = $request->user();
        $pemesanan->load('wisata');

        $isWisataPengelola = $user->hasRole('pengelola')
            && $pemesanan->wisata !== null
            && $pemesanan->wisata->user_id === $user->id;

        if (! $user->hasRole('admin') && ! $isWisataPengelola) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya admin atau pengelola destinasi terkait yang dapat memverifikasi pemesanan.',
            ], 403);
        }

        $pemesanan->update($request->validated());
        $pemesanan->load(['wisata.kategori', 'user']);

        return new PemesananResource($pemesanan);
    }

    public function destroy(Request $request, Pemesanan $pemesanan): Response|JsonResponse
    {
        $user = $request->user();
        $pemesanan->load('wisata');

        $isOwner = $pemesanan->user_id === $user->id;
        $isWisataPengelola = $user->hasRole('pengelola')
            && $pemesanan->wisata !== null
            && $pemesanan->wisata->user_id === $user->id;

        if (! $user->hasRole('admin') && ! $isOwner && ! $isWisataPengelola) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak berhak menghapus pemesanan ini.',
            ], 403);
        }

        $pemesanan->delete();

        return response()->noContent();
    }
}