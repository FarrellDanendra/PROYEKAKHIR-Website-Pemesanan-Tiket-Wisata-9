<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hasRoleColumn = Schema::hasColumn('users', 'role');

        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Pengelola Satu',
                'email' => 'pengelola1@gmail.com',
                'role' => 'pengelola',
            ],
            [
                'name' => 'Pengelola Dua',
                'email' => 'pengelola2@gmail.com',
                'role' => 'pengelola',
            ],
            [
                'name' => 'Petugas Loket',
                'email' => 'petugas@gmail.com',
                'role' => 'petugas',
            ],
            [
                'name' => 'Wisatawan Satu',
                'email' => 'wisatawan1@gmail.com',
                'role' => 'wisatawan',
            ],
            [
                'name' => 'Wisatawan Dua',
                'email' => 'wisatawan2@gmail.com',
                'role' => 'wisatawan',
            ],
        ];

        foreach ($users as $userData) {
            $attributes = [
                'name' => $userData['name'],
                'password' => Hash::make('password'),
            ];

            if ($hasRoleColumn) {
                $attributes['role'] = $userData['role'];
            }

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $attributes
            );

            // Menghubungkan role Spatie ke user
            $user->syncRoles([$userData['role']]);
        }
    }
}
