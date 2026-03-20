<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateJadwalShiftCsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('jadwal-cs.manage');
    }

    public function rules(): array
    {
        return [
            'area_id'              => 'required|exists:master_area_cs,id',
            'jadwals'              => 'required|array',
            'jadwals.*.pjlp_id'   => 'required|exists:pjlp,id',
            'jadwals.*.tanggal'   => 'required|date',
            'jadwals.*.shift_id'  => 'nullable|exists:shifts,id',
            'jadwals.*.status'    => 'required|in:normal,libur,libur_hari_raya,cuti,izin,sakit,alpha',
        ];
    }
}
