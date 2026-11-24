<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar Permission
        $permissions = [
            'kelola pengguna',
            'kelola kategori',
            'kelola wisata',
            'verifikasi pemesanan',
            'lihat laporan',
            'pesan tiket',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // 3. Buat Roles dan Sinkronisasi Permission
        // Role Admin: Memiliki seluruh permission
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions([
            'kelola pengguna',
            'kelola kategori',
            'kelola wisata',
            'verifikasi pemesanan',
            'lihat laporan',
            'pesan tiket',
        ]);

        // Role Pengelola: Mengelola kategori, wisata, verifikasi tiket, dan melihat laporan
        $rolePengelola = Role::firstOrCreate(['name' => 'pengelola']);
        $rolePengelola->syncPermissions([
            'kelola kategori',
            'kelola wisata',
            'verifikasi pemesanan',
            'lihat laporan',
        ]);

        // Role Wisatawan: Melakukan pemesanan tiket wisata
        $roleWisatawan = Role::firstOrCreate(['name' => 'wisatawan']);
        $roleWisatawan->syncPermissions([
            'pesan tiket',
        ]);
    }
}