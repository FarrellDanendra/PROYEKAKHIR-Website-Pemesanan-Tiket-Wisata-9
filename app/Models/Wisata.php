<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wisata extends Model
{
    use HasFactory;

    protected $table = 'wisatas';

    protected $fillable = [
        'kategori_wisata_id',
        'nama',
        'slug',
        'deskripsi',
        'harga_tiket',
        'lokasi',
        'gambar',
        'status_buka',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_tiket' => 'decimal:2',
            'status_buka' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope query untuk hanya mengambil wisata yang berstatus buka.
     */
    public function scopeBuka(Builder $query): void
    {
        $query->where('status_buka', true);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriWisata::class, 'kategori_wisata_id');
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_wisata', 'wisata_id', 'fasilitas_id')
            ->withTimestamps();
    }

    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'wisata_id');
    }
}