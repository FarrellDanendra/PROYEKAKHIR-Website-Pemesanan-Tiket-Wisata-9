<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanans';

    protected $fillable = [
        'kode_tiket',
        'wisata_id',
        'tanggal_kunjungan',
        'jumlah_tiket',
        'total_harga',
        'status',
        'catatan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
            'jumlah_tiket' => 'integer',
            'total_harga' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope query untuk pesanan yang masih aktif (pending atau dikonfirmasi).
     */
    public function scopeAktif(Builder $query): void
    {
        $query->whereIn('status', ['pending', 'dikonfirmasi']);
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class, 'wisata_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}