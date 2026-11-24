<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminData = [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
        ];

        if (Schema::hasColumn('users', 'role')) {
            $adminData['role'] = 'admin';
        }

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            $adminData
        );

        $this->call([
            WisataSeeder::class,
            PemesananSeeder::class,
        ]);
    }
}