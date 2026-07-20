<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use App\Models\Rutina;
use App\Models\EjecucionRutina;
use App\Http\Requests\StoreRutinaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutinasController extends Controller
{
    public function store(StoreRutinaRequest $request)
    {
        $data = $request->validated();
        $data['prioridad'] = $data['prioridad'] ?? 'media';
        $data['impacto'] = $data['impacto'] ?? 'Ninguno';
        $data['frecuencia'] = $data['frecuencia'] ?? 'diaria';
        
        Rutina::create($data);

        // If it is shared, also create for other employees
        if (($data['_rutina_compartida'] ?? null) === 'si' && is_array($data['rutina_compartidos'] ?? null)) {
            foreach ($data['rutina_compartidos'] as $compartidoId) {
                if ($compartidoId != $data['empleado_id']) {
                    $sharedData = $data;
                    $sharedData['empleado_id'] = $compartidoId;
                    Rutina::create($sharedData);
                }
            }
        }

        return redirect()->back()->with('success', 'Rutina creada con éxito.');
    }

    public function ejecutar($id)
    {
        $rutina = Rutina::findOrFail($id);
        $hoy = now()->toDateString();
        $horaActual = now()->format('H:i');

        $ejecucion = EjecucionRutina::firstOrCreate(
            ['rutina_id' => $rutina->id, 'fecha' => $hoy],
            ['cantidad_ejecuciones' => 0, 'horas_registro' => []]
        );

        $horas = $ejecucion->horas_registro ?? [];
        $horas[] = $horaActual;

        $ejecucion->cantidad_ejecuciones += 1;
        $ejecucion->horas_registro = $horas;
        $ejecucion->save();

        return response()->json([
            'success' => true,
            'cantidad_ejecuciones' => $ejecucion->cantidad_ejecuciones,
            'ultima_hora' => $horaActual
        ]);
    }

    public function update(Request $request, $id)
    {
        $rutina = Rutina::findOrFail($id);
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'veces_al_dia' => 'required|integer|min:1',
        ]);

        $rutina->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'veces_al_dia' => $request->input('veces_al_dia'),
        ]);

        return redirect()->back()->with('success', 'Rutina actualizada con éxito.');
    }

    public function setEjecuciones(Request $request, $id)
    {
        $rutina = Rutina::findOrFail($id);
        $cantidad = (int) $request->input('cantidad', 0);
        $hoy = now()->toDateString();

        $ejecucion = EjecucionRutina::updateOrCreate(
            ['rutina_id' => $rutina->id, 'fecha' => $hoy],
            ['cantidad_ejecuciones' => $cantidad]
        );

        return response()->json([
            'success' => true,
            'cantidad_ejecuciones' => $ejecucion->cantidad_ejecuciones,
            'porcentaje' => $rutina->veces_al_dia > 0 ? round(($ejecucion->cantidad_ejecuciones / $rutina->veces_al_dia) * 100) : 100
        ]);
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return back()->with('error', 'No tienes permiso para eliminar rutinas.');
        }
        Rutina::destroy($id);
        return back()->with('success', 'Rutina eliminada.');
    }
}

