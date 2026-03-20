<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalKerjaCsBulananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('jadwal-cs.manage');
    }

    public function rules(): array
    {
        $rules = [
            'area_id'    => 'required|exists:master_area_cs,id',
            'tanggal'    => 'required|date',
            'shift_id'   => 'required|exists:shifts,id',
            'pjlp_id'    => 'nullable|exists:pjlp,id',
            'keterangan' => 'nullable|string',
        ];

        if ($this->input('pekerjaan_id') === 'lainnya') {
            $rules['pekerjaan'] = 'required|string|max:255';
        } else {
            $rules['pekerjaan_id'] = 'required|exists:master_pekerjaan_cs,id';
        }

        return $rules;
    }
}
