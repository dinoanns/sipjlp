<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddLembarKerjaDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jam'       => 'required|date_format:H:i',
            'pekerjaan' => 'required|string|min:10',
            'lokasi_id' => 'required|exists:lokasi,id',
            'keterangan'=> 'nullable|string',
            'foto'      => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Foto bukti pekerjaan wajib diupload.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.max'      => 'Ukuran foto maksimal 2MB.',
            'pekerjaan.min' => 'Deskripsi pekerjaan minimal 10 karakter.',
        ];
    }
}
