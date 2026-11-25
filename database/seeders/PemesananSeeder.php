<?php

namespace Database\Seeders;

use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PemesananSeeder extends Seeder
{
    public function run(): void
    {
        $hasRole = Schema::hasColumn('users', 'role');

        $dataW1 = ['name' => 'Wisatawan Satu', 'password' => Hash::make('password')];
        if ($hasRole) $dataW1['role'] = 'wisatawan';
        $wisatawan1 = User::firstOrCreate(['email' => 'wisatawan1@gmail.com'], $dataW1);

        $dataW2 = ['name' => 'Wisatawan Dua', 'password' => Hash::make('password')];
        if ($hasRole) $dataW2['role'] = 'wisatawan';
        $wisatawan2 = User::firstOrCreate(['email' => 'wisatawan2@gmail.com'], $dataW2);

        $wisatas = Wisata::all();

        if ($wisatas->isEmpty()) {
            $this->call(WisataSeeder::class);
            $wisatas = Wisata::all();
        }

        $daftarWisatawan = [$wisatawan1, $wisatawan2];

        foreach ($wisatas as $index => $wisata) {
            $pembeli = $daftarWisatawan[$index % count($daftarWisatawan)];
            $jumlahTiket = ($index % 3) + 1;

            $pemesanan = new Pemesanan([
                'kode_tiket' => 'TKT-' . strtoupper(Str::random(8)),
                'wisata_id' => $wisata->id,
                'tanggal_kunjungan' => now()->addDays($index + 2)->toDateString(),
                'jumlah_tiket' => $jumlahTiket,
                'total_harga' => $wisata->harga_tiket * $jumlahTiket,
                'status' => $index % 2 === 0 ? 'paid' : 'pending',
                'catatan' => 'Pemesanan tiket wisata oleh ' . $pembeli->name,
                'batas_bayar' => $index % 2 === 0 ? null : now()->addMinutes(5),
                'paid_at' => $index % 2 === 0 ? now() : null
            ]);

            $pemesanan->user_id = $pembeli->id;
            $pemesanan->save();
        }
    }
}