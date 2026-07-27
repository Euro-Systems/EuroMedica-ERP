<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mensaje' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'mensaje.required' => 'El mensaje no puede estar vacío.',
        ];
    }
}
