<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjecucionRutina extends Model
{
    protected $table = 'ejecuciones_rutina';

    protected $fillable = [
        'rutina_id',
        'fecha',
        'cantidad_ejecuciones',
        'horas_registro'
    ];

    protected $casts = [
        'horas_registro' => 'array'
    ];

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }
}
