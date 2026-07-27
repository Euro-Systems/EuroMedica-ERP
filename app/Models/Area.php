<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function actividadesImprevistas()
    {
        return $this->hasMany(ActividadImprevista::class);
    }
}
