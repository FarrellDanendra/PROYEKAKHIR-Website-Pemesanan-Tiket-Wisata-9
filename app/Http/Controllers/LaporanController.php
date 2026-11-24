<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        // Rekapitulasi per Kategori menggunakan DB::table()->leftJoin()->groupBy()
        $rekapPerKategori = DB::table('kategori_wisatas')
            ->leftJoin('wisatas', 'kategori_wisatas.id', '=', 'wisatas.kategori_wisata_id')
            ->leftJoin('pemesanans', 'wisatas.id', '=', 'pemesanans.wisata_id')
            ->select(
                'kategori_wisatas.id',
                'kategori_wisatas.nama as nama_kategori',
                DB::raw('COUNT(DISTINCT wisatas.id) as total_wisata'),
                DB::raw('COALESCE(SUM(pemesanans.jumlah_tiket), 0) as total_tiket_terjual'),
                DB::raw('COALESCE(SUM(pemesanans.total_harga), 0) as total_pendapatan')
            )
            ->groupBy('kategori_wisatas.id', 'kategori_wisatas.nama')
            ->orderByDesc('total_pendapatan')
            ->get();

        // Ringkasan Global
        $totalWisata = DB::table('wisatas')->count();
        $totalTiket = DB::table('pemesanans')->sum('jumlah_tiket') ?? 0;
        $totalPendapatan = DB::table('pemesanans')->sum('total_harga') ?? 0;

        return view('laporan.index', compact('rekapPerKategori', 'totalWisata', 'totalTiket', 'totalPendapatan'));
    }
}