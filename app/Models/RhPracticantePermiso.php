<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhPracticantePermiso extends Model
{
    use HasFactory;
    
    protected $table = 'rh_practicante_permisos';
    
    protected $guarded = [];

    public function practicante()
    {
        return $this->belongsTo(RhPracticante::class, 'practicante_id');
    }
}
