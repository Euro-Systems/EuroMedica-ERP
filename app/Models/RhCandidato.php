<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhCandidato extends Model
{
    use HasFactory;
    
    protected $table = 'rh_candidatos';
    
    protected $guarded = [];

    protected $casts = [
        'documentos' => 'array',
        'observaciones' => 'array',
        'evaluacion_details' => 'array',
    ];
}
