<?php

namespace App\Http\Controllers\ParqueVehicular;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\ServicioVehiculo;

class VehiculosController extends Controller
{
    public function index(Request $request)
    {
        $vehiculos = Vehiculo::orderBy('nombre', 'asc')->get();
        if ($request->ajax()) {
            return response()->json($vehiculos);
        }
        return view('parque_vehicular.index', compact('vehiculos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'placas' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'transmision' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'numero_economico' => 'nullable|string|max:255',
            'fecha_compra' => 'nullable|date',
            'seguro_auto' => 'nullable|string|max:255',
            'telefono_seguro' => 'nullable|string|max:255',
            'inicio_seguro' => 'nullable|date',
            'caducidad_seguro' => 'nullable|date',
        ]);

        $vehiculo = Vehiculo::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Vehículo guardado correctamente.',
            'vehiculo' => $vehiculo
        ]);
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with('servicios')->findOrFail($id);
        return response()->json($vehiculo);
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'placas' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'transmision' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'numero_economico' => 'nullable|string|max:255',
            'fecha_compra' => 'nullable|date',
            'seguro_auto' => 'nullable|string|max:255',
            'telefono_seguro' => 'nullable|string|max:255',
            'inicio_seguro' => 'nullable|date',
            'caducidad_seguro' => 'nullable|date',
        ]);

        $vehiculo->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Vehículo actualizado correctamente.',
            'vehiculo' => $vehiculo
        ]);
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehículo eliminado correctamente.'
        ]);
    }

    // Gestión de Servicios
    public function storeServicio(Request $request, $vehiculoId)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'solicitud_servicio' => 'nullable|string|max:255',
            'cotizaciones' => 'nullable|array',
            'cotizaciones.*.taller' => 'required|string',
            'cotizaciones.*.costo' => 'required|numeric',
            'cotizacion_aceptada' => 'nullable|string|max:255',
            'fecha_autorizacion' => 'nullable|date',
            'fecha_realizacion' => 'nullable|date',
            'observacion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:255',
            'costo' => 'nullable|numeric',
            'factura' => 'nullable|string|max:255',
        ]);

        $servicio = new ServicioVehiculo();
        $servicio->vehiculo_id = $vehiculoId;
        $servicio->fecha = $request->fecha;
        $servicio->solicitud_servicio = $request->solicitud_servicio;
        $servicio->cotizacion_opciones = $request->cotizaciones;
        $servicio->cotizacion_aceptada = $request->cotizacion_aceptada;
        $servicio->fecha_autorizacion = $request->fecha_autorizacion;
        $servicio->fecha_realizacion = $request->fecha_realizacion;
        $servicio->observacion = $request->observacion;
        $servicio->proveedor = $request->proveedor;
        $servicio->costo = $request->costo;
        $servicio->factura = $request->factura;
        $servicio->save();

        return response()->json([
            'success' => true,
            'message' => 'Servicio registrado correctamente.',
            'servicio' => $servicio
        ]);
    }

    public function updateServicio(Request $request, $id)
    {
        $servicio = ServicioVehiculo::findOrFail($id);

        $request->validate([
            'fecha' => 'nullable|date',
            'solicitud_servicio' => 'nullable|string|max:255',
            'cotizaciones' => 'nullable|array',
            'cotizaciones.*.taller' => 'required|string',
            'cotizaciones.*.costo' => 'required|numeric',
            'cotizacion_aceptada' => 'nullable|string|max:255',
            'fecha_autorizacion' => 'nullable|date',
            'fecha_realizacion' => 'nullable|date',
            'observacion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:255',
            'costo' => 'nullable|numeric',
            'factura' => 'nullable|string|max:255',
        ]);

        $servicio->fecha = $request->fecha;
        $servicio->solicitud_servicio = $request->solicitud_servicio;
        $servicio->cotizacion_opciones = $request->cotizaciones;
        $servicio->cotizacion_aceptada = $request->cotizacion_aceptada;
        $servicio->fecha_autorizacion = $request->fecha_autorizacion;
        $servicio->fecha_realizacion = $request->fecha_realizacion;
        $servicio->observacion = $request->observacion;
        $servicio->proveedor = $request->proveedor;
        $servicio->costo = $request->costo;
        $servicio->factura = $request->factura;
        $servicio->save();

        return response()->json([
            'success' => true,
            'message' => 'Servicio actualizado correctamente.',
            'servicio' => $servicio
        ]);
    }

    public function destroyServicio($id)
    {
        $servicio = ServicioVehiculo::findOrFail($id);
        $servicio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Servicio eliminado correctamente.'
        ]);
    }
}

