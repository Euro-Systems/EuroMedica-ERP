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
        'veces_al_dia'
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function ejecuciones()
    {
        return $this->hasMany(EjecucionRutina::class, 'rutina_id');
    }
}
