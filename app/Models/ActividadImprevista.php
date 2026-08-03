<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadImprevista extends Model
{
    protected $table = 'actividades_imprevistas';

    protected $fillable = [
        'empleado_id',
        'area_id',
        'titulo',
        'descripcion_detallada',
        'motivo',
        'hora_inicio',
        'hora_fin',
        'horas_invertidas',
        'resultado_obtenido',
        'observaciones',
        'impacto',
        'fecha',
        'estado',
        'porcentaje_avance',
        'permitir_registro_avance',
        'dirigido_a_id',
        'acciones_realizadas',
        'dependencia_area',
        'dependencia_responsable',
        'dependencia_motivo',
        'comentarios_dirigido'
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function dirigidoA()
    {
        return $this->belongsTo(User::class, 'dirigido_a_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
