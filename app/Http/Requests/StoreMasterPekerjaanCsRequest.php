<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterPekerjaanCsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cs.pekerjaan.manage');
    }

    public function rules(): array
    {
        return [
            'nama'      => 'required|string|max:255',
            'kode'      => 'nullable|string|max:50|unique:master_pekerjaan_cs,kode',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
