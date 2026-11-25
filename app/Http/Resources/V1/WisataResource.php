<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WisataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_wisata' => $this->kode_wisata,
            'kategori_wisata_id' => $this->kategori_wisata_id,
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'harga_tiket' => (float) $this->harga_tiket,
            'lokasi' => $this->lokasi,
            'gambar' => $this->gambar,
            'gambar_url' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'status_buka' => (bool) $this->status_buka,
            'tanggal_buka' => $this->tanggal_buka?->toDateString(),
            'kategori' => new KategoriWisataResource($this->whenLoaded('kategori')),
            'pengelola' => $this->whenLoaded('pengelola', function () {
                return [
                    'id' => $this->pengelola->id,
                    'name' => $this->pengelola->name,
                    'email' => $this->pengelola->email,
                ];
            }),
            'fasilitas' => $this->whenLoaded('fasilitas', function () {
                return $this->fasilitas->map(fn ($item) => [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'ikon' => $item->ikon,
                ])->values();
            }),
            'pemesanans_count' => $this->whenCounted('pemesanans'),
            'pemesanans' => PemesananResource::collection($this->whenLoaded('pemesanans')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}