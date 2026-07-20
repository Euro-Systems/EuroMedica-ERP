<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhPracticante extends Model
{
    use HasFactory;
    
    protected $table = 'rh_practicantes';
    
    protected $guarded = [];

    protected $casts = [
        'documentos' => 'array',
        'observaciones' => 'array',
    ];
}
