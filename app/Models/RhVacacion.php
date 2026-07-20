<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhVacacion extends Model
{
    use HasFactory;
    
    protected $table = 'rh_vacaciones';
    
    protected $guarded = [];

    public function empleado()
    {
        return $this->belongsTo(RhEmpleado::class, 'empleado_id');
    }
}
