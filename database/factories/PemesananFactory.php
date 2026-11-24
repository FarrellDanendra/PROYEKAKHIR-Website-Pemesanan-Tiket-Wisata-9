<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pemesanan>
 */
class PemesananFactory extends Factory
{
    protected $model = Pemesanan::class;

    public function definition(): array
    {
        $jumlahTiket = fake()->numberBetween(1, 6);
        $hargaPerTiket = fake()->numberBetween(20, 100) * 1000;

        return [
            'kode_tiket' => 'TKT-' . strtoupper(Str::random(8)),
            'wisata_id' => Wisata::factory(),
            'user_id' => User::factory(),
            'tanggal_kunjungan' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'jumlah_tiket' => $jumlahTiket,
            'total_harga' => $jumlahTiket * $hargaPerTiket,
            'status' => fake()->randomElement(['pending', 'dikonfirmasi', 'selesai', 'dibatalkan']),
            'catatan' => fake()->optional()->sentence(),
        ];
    }
}