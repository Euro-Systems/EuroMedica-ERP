<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    protected $table = 'rutinas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'prioridad',
        'impacto',
        'empleado_id',
        'frecuencia',
        'veces_al_dia',
        'permitir_registro_avance',
        'dirigido_a_id',
        'hora_inicio',
        'hora_fin',
        'acciones_realizadas',
        'dependencia_area',
        'dependencia_responsable',
        'dependencia_motivo',
        'observaciones',
        'comentarios_dirigido',
        'porcentaje_avance'
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function dirigidoA()
    {
        return $this->belongsTo(User::class, 'dirigido_a_id');
    }

    public function ejecuciones()
    {
        return $this->hasMany(EjecucionRutina::class, 'rutina_id');
    }
}
