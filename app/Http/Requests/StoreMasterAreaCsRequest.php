<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterAreaCsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master.manage');
    }

    public function rules(): array
    {
        return [
            'kode'      => 'required|string|max:50|unique:master_area_cs,kode',
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
        ];
    }
}
