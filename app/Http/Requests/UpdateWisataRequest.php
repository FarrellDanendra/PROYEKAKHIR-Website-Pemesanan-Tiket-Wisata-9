<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateWisataRequest extends StoreWisataRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['kode_wisata'] = [
            'required',
            'string',
            'max:50',
            Rule::unique('wisatas', 'kode_wisata')->ignore($this->route('wisata')),
        ];

        return $rules;
    }
}