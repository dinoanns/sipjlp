<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePjlpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pjlp.create');
    }

    public function rules(): array
    {
        return [
            'nip'               => 'required|string|max:50|unique:pjlp,nip',
            'nama'              => 'required|string|max:255',
            'unit'              => 'required|in:security,cleaning',
            'jabatan'           => 'required|string|max:100',
            'no_telp'           => 'nullable|string|max:20',
            'alamat'            => 'nullable|string',
            'tanggal_bergabung' => 'required|date',
            'foto'              => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'create_user'       => 'nullable|boolean',
            'email'             => 'required_if:create_user,1|nullable|email|unique:users,email',
            'password'          => 'required_if:create_user,1|nullable|min:8',
        ];
    }
}
