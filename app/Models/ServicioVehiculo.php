<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioVehiculo extends Model
{
    protected $table = 'servicios_vehiculo';

    protected $fillable = [
        'vehiculo_id',
        'fecha',
        'solicitud_servicio',
        'cotizacion_opciones', // Store JSON of quote options (e.g. [{"taller": "x", "costo": 100}])
        'cotizacion_aceptada',
        'fecha_autorizacion',
        'fecha_realizacion',
        'observacion',
        'proveedor',
        'costo',
        'factura'
    ];

    protected $casts = [
        'cotizacion_opciones' => 'array',
        'fecha' => 'date:Y-m-d',
        'fecha_autorizacion' => 'date:Y-m-d',
        'fecha_realizacion' => 'date:Y-m-d',
        'costo' => 'float'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
}
