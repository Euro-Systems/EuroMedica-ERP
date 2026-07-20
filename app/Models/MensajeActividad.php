<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeActividad extends Model
{
    protected $table = 'mensajes_actividad';

    protected $fillable = [
        'actividad_id',
        'user_id',
        'mensaje',
        'fecha',
        'hora'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
