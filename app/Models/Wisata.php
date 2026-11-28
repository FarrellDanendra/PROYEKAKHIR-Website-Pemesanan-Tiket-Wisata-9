<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wisata extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wisatas';

    protected $fillable = [
        'kode_wisata',
        'kategori_wisata_id',
        'user_id',
        'nama',
        'slug',
        'deskripsi',
        'harga_tiket',
        'metode_pembayaran',
        'lokasi',
        'gambar',
        'status_buka',
        'tanggal_buka',
        'tanggal_tutup_mulai',
        'tanggal_tutup_selesai',
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
            'metode_pembayaran' => 'array',
            'status_buka' => 'boolean',
            'tanggal_buka' => 'date',
            'tanggal_tutup_mulai' => 'date',
            'tanggal_tutup_selesai' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope query untuk hanya mengambil wisata yang berstatus buka.
     */
    public function scopeBuka(Builder $query): void
    {
        $query->where('status_buka', true);
    }

    /**
     * Cek apakah destinasi tutup pada tanggal tertentu.
     */
    public function isTutupPadaTanggal(string|Carbon $tanggal): bool
    {
        if (! $this->status_buka) {
            return true;
        }

        if (! $this->tanggal_tutup_mulai) {
            return false;
        }

        $target = Carbon::parse($tanggal)->toDateString();
        $mulai = $this->tanggal_tutup_mulai->toDateString();
        $selesai = $this->tanggal_tutup_selesai ? $this->tanggal_tutup_selesai->toDateString() : $mulai;

        return $target >= $mulai && $target <= $selesai;
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