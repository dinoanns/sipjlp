<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkCopyJadwalKerjaCsBulananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('jadwal-cs.manage');
    }

    public function rules(): array
    {
        return [
            'area_id'           => 'required|exists:master_area_cs,id',
            'tanggal_sumber'    => 'required|date',
            'tanggal_tujuan'    => 'required|array',
            'tanggal_tujuan.*'  => 'date',
        ];
    }
}
