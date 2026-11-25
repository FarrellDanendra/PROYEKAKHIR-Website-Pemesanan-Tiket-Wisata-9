<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PemesananResource extends JsonResource
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
            'kode_tiket' => $this->kode_tiket,
            'wisata_id' => $this->wisata_id,
            'user_id' => $this->user_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan?->toDateString(),
            'jumlah_tiket' => (int) $this->jumlah_tiket,
            'total_harga' => (float) $this->total_harga,
            'status' => $this->status,
            'catatan' => $this->catatan,
            'wisata' => new WisataResource($this->whenLoaded('wisata')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}