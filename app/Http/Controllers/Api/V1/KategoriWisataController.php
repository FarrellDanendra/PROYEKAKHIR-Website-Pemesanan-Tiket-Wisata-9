<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\KategoriWisataResource;
use App\Models\KategoriWisata;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KategoriWisataController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $kategoris = KategoriWisata::withCount('wisatas')
            ->orderBy('nama')
            ->get();

        return KategoriWisataResource::collection($kategoris);
    }
}