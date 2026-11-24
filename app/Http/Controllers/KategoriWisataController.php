<?php

namespace App\Http\Controllers;

use App\Models\KategoriWisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategoriWisataController extends Controller
{
    public function index(): View
    {
        $kategoris = KategoriWisata::withCount('wisatas')
            ->latest()
            ->paginate(10);

        return view('kategori.index', compact('kategoris'));
    }

    public function create(): View
    {
        return view('kategori.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori_wisatas,nama'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
        ], [
            'nama' => 'Nama Kategori',
            'deskripsi' => 'Deskripsi Kategori',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        KategoriWisata::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori wisata berhasil ditambahkan!');
    }

    public function edit(KategoriWisata $kategori): View
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriWisata $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori_wisatas,nama,' . $kategori->id],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
        ], [
            'nama' => 'Nama Kategori',
            'deskripsi' => 'Deskripsi Kategori',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori wisata berhasil diperbarui!');
    }

    public function destroy(KategoriWisata $kategori): RedirectResponse
    {
        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori wisata berhasil dihapus!');
    }
}