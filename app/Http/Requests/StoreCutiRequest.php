<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'alasan'        => 'required|string|min:10',
            'no_telp'       => 'required|string|max:20',
            'tgl_mulai'     => 'required|date|after_or_equal:today',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_cuti_id.required' => 'Pilih jenis cuti.',
            'alasan.required'        => 'Alasan cuti harus diisi.',
            'alasan.min'             => 'Alasan cuti minimal 10 karakter.',
            'no_telp.required'       => 'Nomor telepon harus diisi.',
            'tgl_mulai.required'     => 'Tanggal mulai harus diisi.',
            'tgl_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tgl_selesai.required'   => 'Tanggal selesai harus diisi.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
