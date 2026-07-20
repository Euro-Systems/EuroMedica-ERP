<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutinaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'prioridad'          => 'nullable|string',
            'impacto'            => 'nullable|string|max:255',
            'empleado_id'        => 'required',
            'frecuencia'         => 'nullable|string',
            'veces_al_dia'       => 'nullable|integer|min:1',
            '_rutina_compartida' => 'nullable|string',
            'rutina_compartidos' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'      => 'El título es obligatorio.',
            'empleado_id.required' => 'El empleado asignado es obligatorio.',
        ];
    }

    protected function passedValidation(): void
    {
        // Defaults para campos eliminados del formulario
        $this->merge([
            'prioridad' => $this->prioridad ?? 'media',
            'impacto'   => $this->impacto   ?? 'Ninguno',
            'frecuencia' => $this->frecuencia ?? 'diaria',
        ]);
    }
}
