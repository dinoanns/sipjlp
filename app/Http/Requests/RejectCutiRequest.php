<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cuti.approve');
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 10 karakter.',
        ];
    }
}
