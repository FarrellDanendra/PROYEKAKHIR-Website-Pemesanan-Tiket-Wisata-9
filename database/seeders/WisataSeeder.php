<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\KategoriWisata;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Siapkan akun pengelola
        $hasRole = Schema::hasColumn('users', 'role');

        $dataP1 = ['name' => 'Pengelola Satu', 'password' => Hash::make('password')];
        if ($hasRole) $dataP1['role'] = 'pengelola';
        $pengelola1 = User::firstOrCreate(['email' => 'pengelola1@gmail.com'], $dataP1);

        $dataP2 = ['name' => 'Pengelola Dua', 'password' => Hash::make('password')];
        if ($hasRole) $dataP2['role'] = 'pengelola';
        $pengelola2 = User::firstOrCreate(['email' => 'pengelola2@gmail.com'], $dataP2);

        $daftarPengelola = [$pengelola1, $pengelola2];

        // 2. Siapkan Kategori Wisata
        $daftarKategori = [
            'Wisata Alam' => 'Destinasi keindahan alam terbuka, pegunungan, dan air terjun.',
            'Wisata Bahari' => 'Pantai pasir putih, pulau karang, dan wisata bawah laut.',
            'Wahana Rekreasi' => 'Taman hiburan keluarga, waterpark, dan wahana bermain seru.',
            'Wisata Budaya' => 'Candi, museum, peninggalan sejarah, dan desa adat.',
        ];

        $kategoriModels = [];
        foreach ($daftarKategori as $nama => $deskripsi) {
            $kategoriModels[] = KategoriWisata::firstOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama, 'deskripsi' => $deskripsi]
            );
        }

        // 3. Siapkan Fasilitas
        $daftarFasilitas = [
            ['nama' => 'Area Parkir Luas', 'ikon' => 'parking'],
            ['nama' => 'Musholla Nyaman', 'ikon' => 'mosque'],
            ['nama' => 'Toilet Bersih', 'ikon' => 'toilet'],
            ['nama' => 'Food Court & Kafe', 'ikon' => 'utensils'],
            ['nama' => 'Akses WiFi Gratis', 'ikon' => 'wifi'],
            ['nama' => 'Pusat Informasi & P3K', 'ikon' => 'first-aid'],
        ];

        $fasilitasModels = [];
        foreach ($daftarFasilitas as $f) {
            $fasilitasModels[] = Fasilitas::firstOrCreate(
                ['nama' => $f['nama']],
                ['ikon' => $f['ikon']]
            );
        }

        // 4. Buat Contoh Wisata & Bagi Kepemilikan Selang-seling
        $dataWisata = [
            [
                'nama' => 'Puncak Pinus Asri',
                'kategori_index' => 0,
                'harga_tiket' => 25000,
                'lokasi' => 'Dlingo, Bantul, D.I. Yogyakarta',
                'status_buka' => true,
                'deskripsi' => 'Panorama hutan pinus yang asri dengan gardu pandang matahari terbit yang mempesona.',
            ],
            [
                'nama' => 'Pantai Karang Putih',
                'kategori_index' => 1,
                'harga_tiket' => 35000,
                'lokasi' => 'Gunungkidul, D.I. Yogyakarta',
                'status_buka' => true,
                'deskripsi' => 'Pantai eksotis berpasir putih dikelilingi tebing karang alami yang megah.',
            ],
            [
                'nama' => 'Galaxy Water Adventure',
                'kategori_index' => 2,
                'harga_tiket' => 60000,
                'lokasi' => 'Sleman, D.I. Yogyakarta',
                'status_buka' => true,
                'deskripsi' => 'Wahana permainan air modern terlengkap untuk keluarga dan anak-anak.',
            ],
            [
                'nama' => 'Museum Pusaka Keraton',
                'kategori_index' => 3,
                'harga_tiket' => 20000,
                'lokasi' => 'Kota Yogyakarta',
                'status_buka' => false,
                'deskripsi' => 'Koleksi artefak sejarah budaya adiluhung warisan nusantara.',
            ],
        ];

        foreach ($dataWisata as $index => $item) {
            $pengelolaTerpilih = $daftarPengelola[$index % count($daftarPengelola)];

            $wisata = Wisata::updateOrCreate(
                ['slug' => Str::slug($item['nama'])],
                [
                    'kategori_wisata_id' => $kategoriModels[$item['kategori_index']]->id,
                    'nama' => $item['nama'],
                    'deskripsi' => $item['deskripsi'],
                    'harga_tiket' => $item['harga_tiket'],
                    'lokasi' => $item['lokasi'],
                    'gambar' => null,
                    'status_buka' => $item['status_buka'],
                ]
            );

            $wisata->user_id = $pengelolaTerpilih->id;
            $wisata->save();

            $randomFasilitasIds = collect($fasilitasModels)->random(3)->pluck('id')->toArray();
            $wisata->fasilitas()->sync($randomFasilitasIds);
        }
    }
}