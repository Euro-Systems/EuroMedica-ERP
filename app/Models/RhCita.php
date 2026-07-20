<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhCita extends Model
{
    use HasFactory;
    
    protected $table = 'rh_citas';
    
    protected $guarded = [];

    protected $casts = [
        'documentos' => 'array',
    ];
}
