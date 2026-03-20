<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SummaryAbsenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pjlp_id' => 'nullable|exists:pjlp,id',
            'bulan'   => 'required|integer|min:1|max:12',
            'tahun'   => 'required|integer|min:2020|max:2100',
        ];
    }
}
