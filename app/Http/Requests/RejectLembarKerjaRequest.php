<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectLembarKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catatan' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
            'catatan.min'      => 'Catatan minimal 10 karakter.',
        ];
    }
}
