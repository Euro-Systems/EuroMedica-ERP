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
            'empleados_asig_checkboxes' => 'nullable|array',
            'permitir_registro_avance' => 'nullable',
            'dirigido_a_id'      => 'nullable',
            'hora_inicio'        => 'nullable|string',
            'hora_fin'           => 'nullable|string',
            'acciones_realizadas'=> 'nullable|string',
            'dependencia_area'   => 'nullable|string',
            'dependencia_responsable' => 'nullable|string',
            'dependencia_motivo' => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'comentarios_dirigido' => 'nullable|string',
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
            'veces_al_dia' => $this->veces_al_dia ? max(1, intval($this->veces_al_dia)) : 1,
        ]);
    }
}
