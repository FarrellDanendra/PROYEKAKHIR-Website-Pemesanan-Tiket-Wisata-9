<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWisataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status_buka' => $this->boolean('status_buka'),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_wisata' => ['required', 'string', 'max:50', Rule::unique('wisatas', 'kode_wisata')],
            'nama' => ['required', 'string', 'max:150'],
            'kategori_wisata_id' => ['required', 'exists:kategori_wisatas,id'],
            'harga_tiket' => ['required', 'numeric', 'min:0'],
            'lokasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'status_buka' => ['boolean'],
            'tanggal_buka' => ['nullable', 'date', 'required_if:status_buka,true'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['exists:fasilitas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah digunakan, pilih kode lain.',
            'required_if' => ':attribute wajib diisi ketika status operasional wisata buka.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute tidak boleh bernilai kurang dari :min.',
            'max' => ':attribute tidak boleh lebih dari :max.',
            'date' => ':attribute harus berformat tanggal yang valid.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat jpeg, png, jpg, atau webp.',
            'exists' => ':attribute yang dipilih tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_wisata' => 'Kode Wisata',
            'nama' => 'Nama Destinasi Wisata',
            'kategori_wisata_id' => 'Kategori Wisata',
            'harga_tiket' => 'Harga Tiket Masuk',
            'lokasi' => 'Lokasi Wisata',
            'deskripsi' => 'Deskripsi Wisata',
            'status_buka' => 'Status Operasional',
            'tanggal_buka' => 'Tanggal Buka',
            'gambar' => 'Foto Wisata',
            'fasilitas' => 'Fasilitas Wisata',
        ];
    }
}