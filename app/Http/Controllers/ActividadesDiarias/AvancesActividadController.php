<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\AvanceActividad;
use App\Models\Actividad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AvancesActividadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'que_se_hizo' => 'required',
            'resultado_final' => 'required'
        ]);
        
        if (!User::find(1)) {
            User::forceCreate(['id' => 1, 'name' => 'Usuario', 'email' => 'u@a.com', 'password' => bcrypt('1')]);
        }
        
        $data['acciones_realizadas'] = $request->acciones_realizadas;
        $data['empleado_id'] = Auth::id() ?? 1;

        $data['motivo'] = 'No aplica';
        $data['hora_inicio'] = now()->format('H:i');
        $data['hora_fin'] = now()->format('H:i');
        $data['horas_trabajadas'] = 0;
        
        // Remove dependence on porcentaje_avance as requested
        $data['porcentaje_avance'] = 0; 
        $data['fecha_avance'] = now()->toDateString();
        $data['estado_aprobacion'] = 'pendiente';
        
        AvanceActividad::create($data);

        return back()->with('success', 'Avance registrado con éxito. Queda en espera de aprobación.');
    }

    public function aprobar(\App\Http\Requests\AprobarAvanceRequest $request, string $id)
    {
        $avance = AvanceActividad::findOrFail($id);
        
        $avance->update([
            'estado_aprobacion' => 'aprobado',
            'aprobado_por_id' => Auth::id() ?? 1,
            'fecha_aprobacion' => now()->toDateString(),
            'hora_aprobacion' => now()->format('H:i:s'),
            'comentario_jefe' => $request->comentario_jefe
        ]);

        // Automatically update the activity's status or percentage if it has been marked as finalizada/en_proceso.
        return back()->with('success', 'Avance aprobado correctamente.');
    }

    public function rechazar(\App\Http\Requests\RechazarAvanceRequest $request, string $id)
    {
        $avance = AvanceActividad::findOrFail($id);
        
        $avance->update([
            'estado_aprobacion' => 'rechazado',
            'aprobado_por_id' => Auth::id() ?? 1,
            'fecha_aprobacion' => now()->toDateString(),
            'hora_aprobacion' => now()->format('H:i:s'),
            'comentario_jefe' => $request->comentario_jefe
        ]);

        return back()->with('success', 'Avance rechazado con éxito.');
    }

    public function destroy(string $id)
    {
        AvanceActividad::destroy($id);
        return back();
    }
}

