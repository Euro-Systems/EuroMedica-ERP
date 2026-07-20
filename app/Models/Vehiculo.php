<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'nombre',
        'marca',
        'modelo',
        'placas',
        'color',
        'transmision',
        'numero_serie',
        'numero_economico',
        'fecha_compra',
        'seguro_auto',
        'telefono_seguro',
        'inicio_seguro',
        'caducidad_seguro'
    ];

    public function servicios()
    {
        return $this->hasMany(ServicioVehiculo::class, 'vehiculo_id')->orderBy('fecha', 'desc');
    }
}
