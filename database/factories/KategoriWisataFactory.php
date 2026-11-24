<?php

namespace Database\Factories;

use App\Models\KategoriWisata;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriWisata>
 */
class KategoriWisataFactory extends Factory
{
    protected $model = KategoriWisata::class;

    public function definition(): array
    {
        $nama = fake()->unique()->randomElement([
            'Wisata Alam',
            'Wisata Budaya & Sejarah',
            'Wisata Bahari',
            'Wahana Rekreasi',
            'Wisata Edukasi',
            'Wisata Kuliner',
        ]);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'deskripsi' => fake()->paragraph(),
        ];
    }
}