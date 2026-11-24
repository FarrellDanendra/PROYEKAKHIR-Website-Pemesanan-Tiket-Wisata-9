<?php

namespace Database\Factories;

use App\Models\KategoriWisata;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wisata>
 */
class WisataFactory extends Factory
{
    protected $model = Wisata::class;

    public function definition(): array
    {
        $nama = fake()->unique()->sentence(3);

        return [
            'kategori_wisata_id' => KategoriWisata::factory(),
            'user_id' => null,
            'nama' => $nama,
            'slug' => Str::slug($nama) . '-' . fake()->unique()->numberBetween(100, 999),
            'deskripsi' => fake()->paragraphs(3, true),
            'harga_tiket' => fake()->numberBetween(15, 250) * 1000,
            'lokasi' => fake()->city() . ', ' . fake()->state(),
            'gambar' => null,
            'status_buka' => fake()->boolean(80),
        ];
    }

    /**
     * State untuk wisata yang dipastikan berstatus buka.
     */
    public function buka(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_buka' => true,
        ]);
    }
}