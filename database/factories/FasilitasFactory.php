<?php

namespace Database\Factories;

use App\Models\Fasilitas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fasilitas>
 */
class FasilitasFactory extends Factory
{
    protected $model = Fasilitas::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->unique()->randomElement([
                'Area Parkir Luas',
                'Musholla Bersih',
                'Toilet & Kamar Mandi',
                'Food Court / Kantin',
                'WiFi Gratis',
                'Gazebo Santai',
                'Spot Foto Instagramable',
                'Pusat Informasi & P3K',
            ]),
            'ikon' => fake()->randomElement(['parkir', 'musholla', 'toilet', 'makan', 'wifi', 'gazebo']),
        ];
    }
}