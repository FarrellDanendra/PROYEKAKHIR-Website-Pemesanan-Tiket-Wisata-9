<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wisata_id' => ['required', 'exists:wisatas,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'jumlah_tiket' => ['required', 'integer', 'between:1,20'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'after_or_equal' => ':attribute minimal adalah hari ini atau hari mendatang.',
            'integer' => ':attribute harus berupa angka bulat.',
            'between' => ':attribute harus berjumlah antara :min sampai :max tiket.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'wisata_id' => 'Destinasi Wisata',
            'tanggal_kunjungan' => 'Tanggal Kunjungan',
            'jumlah_tiket' => 'Jumlah Tiket',
            'catatan' => 'Catatan Tambahan',
        ];
    }
}