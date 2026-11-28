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
            'tanggal_tutup_mulai' => ['nullable', 'date'],
            'tanggal_tutup_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_tutup_mulai'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['exists:fasilitas,id'],
            'fasilitas_lainnya' => ['nullable', 'string', 'max:255'],
            'metode_nama' => ['required', 'array', 'min:1'],
            'metode_nama.*' => ['required', 'string', 'max:100'],
            'metode_nomor' => ['required', 'array', 'min:1'],
            'metode_nomor.*' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah digunakan, pilih kode lain.',
            'required_if' => ':attribute wajib diisi ketika status operasional wisata buka.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal diisi :min item.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'date' => ':attribute harus berformat tanggal yang valid.',
            'after_or_equal' => ':attribute harus tanggal yang sama atau setelah tanggal mulai tutup.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat jpeg, png, jpg, atau webp.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'metode_nama.min' => 'Wajib menambahkan minimal 1 metode pembayaran.',
            'metode_nama.*.required' => 'Nama metode pembayaran tidak boleh kosong.',
            'metode_nomor.*.required' => 'Nomor rekening/akun pembayaran tidak boleh kosong.',
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
            'tanggal_tutup_mulai' => 'Tanggal Mulai Tutup',
            'tanggal_tutup_selesai' => 'Tanggal Selesai Tutup',
            'gambar' => 'Foto Wisata',
            'fasilitas' => 'Fasilitas Wisata',
            'fasilitas_lainnya' => 'Fasilitas Lainnya',
            'metode_nama' => 'Daftar Nama Metode Pembayaran',
            'metode_nomor' => 'Daftar Nomor Akun Pembayaran',
        ];
    }
}