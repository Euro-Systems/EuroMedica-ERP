<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\ServicioVehiculo;

class VehiculosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Unidad 1
        $u1 = Vehiculo::create([
            'nombre' => 'Unidad 1',
            'marca' => 'Toyota',
            'modelo' => 'Hilux 2022',
            'placas' => 'ABC-123-D',
            'color' => 'Blanco',
            'transmision' => 'Manual',
            'numero_serie' => 'JT123456789MX',
            'numero_economico' => 'U-045',
            'fecha_compra' => '2022-08-15',
            'seguro_auto' => 'Qualitas',
            'telefono_seguro' => '55 1234 5678',
            'inicio_seguro' => '2025-01-01',
            'caducidad_seguro' => '2026-01-01'
        ]);

        ServicioVehiculo::create([
            'vehiculo_id' => $u1->id,
            'fecha' => '2026-02-01',
            'solicitud_servicio' => 'Mantenimiento preventivo',
            'cotizacion_opciones' => [
                ['taller' => 'Taller López', 'costo' => 8500],
                ['taller' => 'AutoService MX', 'costo' => 9200]
            ],
            'cotizacion_aceptada' => 'Taller López - $8,500',
            'fecha_autorizacion' => '2026-02-03',
            'fecha_realizacion' => '2026-02-05',
            'observacion' => 'Servicio realizado correctamente',
            'proveedor' => 'Taller López',
            'costo' => 8500,
            'factura' => 'FAC-00125'
        ]);

        // Unidad 2
        $u2 = Vehiculo::create([
            'nombre' => 'Unidad 2',
            'marca' => 'Nissan',
            'modelo' => 'NP300 2021',
            'placas' => 'XYZ-987-A',
            'color' => 'Gris',
            'transmision' => 'Manual',
            'numero_serie' => 'NS987654321MX',
            'numero_economico' => 'U-046',
            'fecha_compra' => '2021-05-10',
            'seguro_auto' => 'GNP Seguros',
            'telefono_seguro' => '55 8765 4321',
            'inicio_seguro' => '2025-02-15',
            'caducidad_seguro' => '2026-02-15'
        ]);

        ServicioVehiculo::create([
            'vehiculo_id' => $u2->id,
            'fecha' => '2026-03-10',
            'solicitud_servicio' => 'Cambio de balatas y frenos',
            'cotizacion_opciones' => [
                ['taller' => 'Frenos Pro', 'costo' => 3200],
                ['taller' => 'Multiservicios', 'costo' => 3800]
            ],
            'cotizacion_aceptada' => 'Frenos Pro - $3,200',
            'fecha_autorizacion' => '2026-03-11',
            'fecha_realizacion' => '2026-03-12',
            'observacion' => 'Frenos delanteros y traseros rectificados',
            'proveedor' => 'Frenos Pro',
            'costo' => 3200,
            'factura' => 'FAC-0985'
        ]);
    }
}
