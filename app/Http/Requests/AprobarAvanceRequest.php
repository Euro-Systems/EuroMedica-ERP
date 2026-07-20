<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprobarAvanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comentario_jefe' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'comentario_jefe.string' => 'El comentario debe ser texto.',
        ];
    }
}
