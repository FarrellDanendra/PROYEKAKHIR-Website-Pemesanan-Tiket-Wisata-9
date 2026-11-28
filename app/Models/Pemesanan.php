<?php

namespace App\Models;

use Carbon\Carbon;
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
        'user_id',
        'nama_pemesan',
        'email_pemesan',
        'no_telepon',
        'alamat',
        'metode_pembayaran',
        'tanggal_kunjungan',
        'jumlah_tiket',
        'total_harga',
        'status',
        'batas_bayar',
        'paid_at',
        'used_at',
        'verified_by',
        'alasan_refund',
        'refund_requested_at',
        'refund_processed_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
            'jumlah_tiket' => 'integer',
            'total_harga' => 'decimal:2',
            'batas_bayar' => 'datetime',
            'paid_at' => 'datetime',
            'used_at' => 'datetime',
            'refund_requested_at' => 'datetime',
            'refund_processed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Otomatis ubah status menjadi kadaluarsa jika:
     * 1. Status 'pending' melewati batas_bayar (5 menit)
     * 2. Status 'paid' melewati tanggal_kunjungan (hari ini > tanggal_kunjungan)
     */
    public static function syncExpiredStatuses(): void
    {
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();

        static::where('status', 'pending')
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<=', $now)
            ->update(['status' => 'kadaluarsa']);

        static::where('status', 'paid')
            ->whereDate('tanggal_kunjungan', '<', $today)
            ->update(['status' => 'kadaluarsa']);
    }

    public function isRefundable(): bool
    {
        return $this->status === 'paid'
            && $this->tanggal_kunjungan->greaterThanOrEqualTo(Carbon::today());
    }

    public function isPendingActive(): bool
    {
        return $this->status === 'pending'
            && ($this->batas_bayar === null || $this->batas_bayar->isFuture());
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class, 'wisata_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}