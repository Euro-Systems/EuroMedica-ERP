<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechazarAvanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comentario_jefe' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'comentario_jefe.required' => 'El comentario es obligatorio cuando se rechaza un avance.',
            'comentario_jefe.string' => 'El comentario debe ser texto.',
        ];
    }
}
