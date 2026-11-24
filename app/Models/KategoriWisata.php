<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriWisata extends Model
{
    use HasFactory;

    protected $table = 'kategori_wisatas';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function wisatas(): HasMany
    {
        return $this->hasMany(Wisata::class, 'kategori_wisata_id');
    }
}