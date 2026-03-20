<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePjlpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pjlp.edit');
    }

    public function rules(): array
    {
        $pjlpId = $this->route('pjlp')->id;

        return [
            'nip'               => 'required|string|max:50|unique:pjlp,nip,' . $pjlpId,
            'nama'              => 'required|string|max:255',
            'badge_number'      => 'nullable|string|max:50',
            'unit'              => 'required|in:security,cleaning',
            'jabatan'           => 'required|string|max:100',
            'no_telp'           => 'nullable|string|max:20',
            'alamat'            => 'nullable|string',
            'tanggal_bergabung' => 'required|date',
            'status'            => 'required|in:aktif,nonaktif,cuti,resign',
            'foto'              => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
    }
}
