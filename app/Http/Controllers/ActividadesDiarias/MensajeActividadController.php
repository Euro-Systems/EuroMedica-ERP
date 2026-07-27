<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use App\Models\Actividad;
use App\Models\MensajeActividad;
use App\Http\Requests\StoreMensajeRequest;
use Illuminate\Support\Facades\Auth;

class MensajeActividadController extends Controller
{
    public function store(StoreMensajeRequest $request, $actividadId)
    {
        $actividad = Actividad::findOrFail($actividadId);

        $mensaje = MensajeActividad::create([
            'actividad_id' => $actividad->id,
            'user_id' => Auth::id() ?? 1,
            'mensaje' => $request->mensaje,
            'fecha' => now()->toDateString(),
            'hora' => now()->format('H:i:s'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mensaje' => $mensaje->load('user')
            ]);
        }

        return back()->with('success', 'Mensaje enviado.');
    }
}

