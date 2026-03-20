<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MapBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pjlp_id'      => 'required|exists:pjlp,id',
            'badge_number' => 'required|string|max:50',
        ];
    }
}
