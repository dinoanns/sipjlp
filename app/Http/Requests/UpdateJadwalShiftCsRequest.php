<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalShiftCsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('jadwal-cs.manage');
    }

    public function rules(): array
    {
        return [
            'pjlp_id' => 'required|exists:pjlp,id',
            'tanggal' => 'required|date',
            'shift_id'=> 'nullable|exists:shifts,id',
            'status'  => 'required|in:normal,libur,libur_hari_raya,cuti,izin,sakit,alpha',
        ];
    }
}
