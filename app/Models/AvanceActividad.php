<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvanceActividad extends Model
{
    protected $table = 'avances_actividad';

    protected $fillable = [
        'actividad_id',
        'empleado_id',
        'porcentaje_avance',
        'horas_trabajadas',
        'comentario',
        'que_se_hizo',
        'motivo',
        'problema_detectado',
        'acciones_realizadas',
        'resultado_final',
        'observaciones',
        'fecha_avance',
        'hora_inicio',
        'hora_fin',
        'estado_aprobacion',
        'aprobado_por_id',
        'fecha_aprobacion',
        'hora_aprobacion',
        'comentario_jefe'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }
}
