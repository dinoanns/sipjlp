<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterAreaCsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master.manage');
    }

    public function rules(): array
    {
        $areaId = $this->route('area_c')->id;

        return [
            'kode'      => 'required|string|max:50|unique:master_area_cs,kode,' . $areaId,
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
