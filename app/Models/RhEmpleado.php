<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhEmpleado extends Model
{
    use HasFactory;
    
    protected $table = 'rh_empleados';
    
    protected $guarded = [];

    protected $casts = [
        'documentos' => 'array',
        'observaciones' => 'array',
    ];

    public function vacaciones()
    {
        return $this->hasMany(RhVacacion::class, 'empleado_id');
    }

    public function vacacionesAnuales()
    {
        return $this->hasMany(RhVacacionAnual::class, 'empleado_id');
    }
}
