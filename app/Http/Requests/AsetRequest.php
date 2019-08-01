<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lokasi_id' => 'required',
            'nama' => 'required',
            'kode' => 'required|max:20|unique:asets',
            'spesifikasi' => 'required',
            'unit_id' => 'required',
            'kondisi' => 'required',
            'status' => 'required',
        ];
    }
}
