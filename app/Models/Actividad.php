<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'titulo',
        'descripcion',
        'objetivo',
        'empleado_id',
        'jefe_id',
        'area_id',
        'fecha_inicio',
        'fecha_estimada_fin',
        'tiempo_estimado',
        'prioridad',
        'estado',
        'impacto',
        'modalidad',
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
        'porcentaje_avance',
        'en_seguimiento',
        'fecha_seguimiento'
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function jefe()
    {
        return $this->belongsTo(User::class, 'jefe_id');
    }

    public function dirigidoA()
    {
        return $this->belongsTo(User::class, 'dirigido_a_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function avances()
    {
        return $this->hasMany(AvanceActividad::class);
    }

    public function avancesAprobados()
    {
        return $this->hasMany(AvanceActividad::class)->where('estado_aprobacion', 'aprobado');
    }

    public function mensajes()
    {
        return $this->hasMany(MensajeActividad::class)->orderBy('created_at', 'asc');
    }
}
