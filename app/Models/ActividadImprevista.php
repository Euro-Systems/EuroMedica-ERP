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
        'estado'
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
