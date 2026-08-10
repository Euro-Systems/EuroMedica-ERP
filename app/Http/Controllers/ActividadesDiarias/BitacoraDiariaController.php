<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AvanceActividad;
use App\Models\ActividadImprevista;
use App\Models\Rutina;
use App\Models\EjecucionRutina;

class BitacoraDiariaController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(403);
        }

        // If employee or intern, render their own dates view directly
        if (in_array($currentUser->rol, ['empleado', 'practicante'])) {
            return $this->usuarioFechas($currentUser->id);
        }

        $buscar     = $request->input('buscar');
        $areaId     = session('active_area_id');
        $areaActiva = $areaId ? \App\Models\Area::find($areaId) : null;

        // Buscar los Jefes correspondientes al área activa
        $jefesForAreaIds = [];
        if ($areaActiva) {
            $allJefes = User::where('rol', 'jefe')->where('activo', true)->get();
            foreach ($allJefes as $jefe) {
                if ($jefe->isJefeForArea($areaActiva) || $jefe->area_id == $areaId) {
                    $jefesForAreaIds[] = $jefe->id;
                }
            }
        }

        $query = User::query()->where('activo', true);

        if ($areaId === 'todas') {
            // Ver todas las áreas: mostrar todos los usuarios activos
        } elseif ($areaId) {
            $query->where(function($q) use ($areaId, $jefesForAreaIds, $currentUser) {
                $q->where('area_id', $areaId)
                  ->orWhere('id', $currentUser->id);
                if (!empty($jefesForAreaIds)) {
                    $q->orWhereIn('id', $jefesForAreaIds);
                }
            });
        } elseif ($currentUser->rol === 'jefe') {
            $query->where(function($q) use ($currentUser) {
                $q->where('jefe_id', $currentUser->id)->orWhere('id', $currentUser->id);
            });
        }

        if ($buscar) {
            $query->where(function($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->with('area')->get();
        if (!$usuarios->contains('id', $currentUser->id)) {
            $usuarios->prepend($currentUser);
        }

        // Ordenar: Usuario actual (Admin/Jefe) primero, luego Jefes de área, luego empleados y practicantes
        $usuarios = $usuarios->sortBy(function($u) use ($currentUser) {
            if ($u->id === $currentUser->id) return 0;
            if ($u->rol === 'jefe') return 1;
            return 2;
        })->values();
        $hoy = now()->toDateString();
        $userIds = $usuarios->pluck('id')->toArray();

        // Bulk query sums for advances
        $avancesSums = AvanceActividad::whereIn('empleado_id', $userIds)
            ->whereDate('fecha_avance', $hoy)
            ->groupBy('empleado_id')
            ->selectRaw('empleado_id, SUM(horas_trabajadas) as total')
            ->pluck('total', 'empleado_id')
            ->toArray();

        // Bulk query sums for imprevistos
        $imprevistosSums = ActividadImprevista::whereIn('empleado_id', $userIds)
            ->whereDate('fecha', $hoy)
            ->groupBy('empleado_id')
            ->selectRaw('empleado_id, SUM(horas_invertidas) as total')
            ->pluck('total', 'empleado_id')
            ->toArray();

        foreach ($usuarios as $u) {
            $horasAvances = $avancesSums[$u->id] ?? 0;
            $horasImprevistas = $imprevistosSums[$u->id] ?? 0;
            $u->horas_hoy = round($horasAvances + $horasImprevistas, 2);
        }

        return view('actividades_diarias.reportes.bitacora.index', compact('usuarios', 'buscar', 'areaActiva'));
    }

    public function usuarioFechas($empleado)
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(403);
        }

        // Security check: employees can only see their own report
        if (in_array($currentUser->rol, ['empleado', 'practicante']) && $currentUser->id != $empleado) {
            $empleado = $currentUser->id;
        }

        // Boss security check: allow if target user is subordinate OR belongs to the same area
        if ($currentUser->rol === 'jefe' && $currentUser->id != $empleado) {
            $targetUser = User::findOrFail($empleado);
            $sameArea = $currentUser->area_id && ($targetUser->area_id == $currentUser->area_id);
            $isSubordinate = ($targetUser->jefe_id == $currentUser->id);
            if (!$sameArea && !$isSubordinate) {
                abort(403, 'No tienes permiso para ver este empleado.');
            }
        }

        $user = User::findOrFail($empleado);

        // Fetch grouped advances
        $avancesGrouped = AvanceActividad::where('empleado_id', $user->id)
            ->selectRaw('DATE(fecha_avance) as fecha_f, COUNT(*) as cnt, SUM(horas_trabajadas) as hrs')
            ->groupBy('fecha_f')
            ->get()
            ->keyBy('fecha_f');

        // Fetch grouped imprevistos
        $imprevistosGrouped = ActividadImprevista::where('empleado_id', $user->id)
            ->selectRaw('DATE(fecha) as fecha_f, COUNT(*) as cnt, SUM(horas_invertidas) as hrs')
            ->groupBy('fecha_f')
            ->get()
            ->keyBy('fecha_f');

        // Fetch grouped rutinas
        $rutinasGrouped = EjecucionRutina::whereHas('rutina', function($q) use ($user) {
                $q->where('empleado_id', $user->id);
            })
            ->selectRaw('DATE(fecha) as fecha_f, COUNT(*) as cnt')
            ->groupBy('fecha_f')
            ->get()
            ->keyBy('fecha_f');

        // Merge all dates
        $allDates = collect()
            ->concat($avancesGrouped->keys())
            ->concat($imprevistosGrouped->keys())
            ->concat($rutinasGrouped->keys())
            ->unique()
            ->sortDesc()
            ->toArray();

        $fechasList = [];
        foreach ($allDates as $f) {
            $avance = $avancesGrouped->get($f);
            $imprevisto = $imprevistosGrouped->get($f);
            $rutina = $rutinasGrouped->get($f);

            $countAvances = $avance ? $avance->cnt : 0;
            $countImprevistos = $imprevisto ? $imprevisto->cnt : 0;
            $countRutinas = $rutina ? $rutina->cnt : 0;

            $horasAvances = $avance ? (float) $avance->hrs : 0.0;
            $horasImprevistas = $imprevisto ? (float) $imprevisto->hrs : 0.0;

            $fechasList[] = (object)[
                'fecha' => $f,
                'count_avances' => $countAvances,
                'count_imprevistos' => $countImprevistos,
                'count_rutinas' => $countRutinas,
                'total_horas' => round($horasAvances + $horasImprevistas, 2)
            ];
        }

        return view('actividades_diarias.reportes.bitacora.usuario_fechas', compact('user', 'fechasList'));
    }

    public function show($empleado, $fecha)
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(403);
        }

        // Security checks
        if (in_array($currentUser->rol, ['empleado', 'practicante']) && $currentUser->id != $empleado) {
            $empleado = $currentUser->id;
        }

        if ($currentUser->rol === 'jefe' && $currentUser->id != $empleado) {
            $targetUser = User::findOrFail($empleado);
            $sameArea = $currentUser->area_id && ($targetUser->area_id == $currentUser->area_id);
            $isSubordinate = ($targetUser->jefe_id == $currentUser->id);
            if (!$sameArea && !$isSubordinate) {
                abort(403, 'No tienes permiso para ver este reporte.');
            }
        }

        $user = User::findOrFail($empleado);

        // Load advances
        $avances = AvanceActividad::where('empleado_id', $user->id)
            ->whereDate('fecha_avance', $fecha)
            ->with('actividad')
            ->get();

        // Load imprevistos
        $imprevistos = ActividadImprevista::where('empleado_id', $user->id)
            ->whereDate('fecha', $fecha)
            ->get();

        // Load routine executions
        $ejecucionesRutina = EjecucionRutina::whereHas('rutina', function($q) use ($user) {
                $q->where('empleado_id', $user->id);
            })
            ->whereDate('fecha', $fecha)
            ->with('rutina')
            ->get();

        // Calculate total hours
        $horasAvances = $avances->sum('horas_trabajadas');
        $horasImprevistas = $imprevistos->sum('horas_invertidas');
        $totalHoras = round($horasAvances + $horasImprevistas, 2);

        // Check if any routines assigned to this user were not executed on this day
        $rutinasAsignadas = Rutina::where('empleado_id', $user->id)->get();
        $rutinasConEjecucion = $ejecucionesRutina->pluck('rutina_id')->toArray();
        
        $rutinasFaltantes = [];
        foreach ($rutinasAsignadas as $rutina) {
            if (!in_array($rutina->id, $rutinasConEjecucion)) {
                $rutinasFaltantes[] = $rutina;
            }
        }

        return view('actividades_diarias.reportes.bitacora.show', compact(
            'user',
            'fecha',
            'avances',
            'imprevistos',
            'ejecucionesRutina',
            'totalHoras',
            'rutinasFaltantes',
            'rutinasAsignadas'
        ));
    }

    public function exportPdf(Request $request, $empleado, $fecha)
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(403);
        }

        if (in_array($currentUser->rol, ['empleado', 'practicante']) && $currentUser->id != $empleado) {
            $empleado = $currentUser->id;
        }

        if ($currentUser->rol === 'jefe' && $currentUser->id != $empleado) {
            $targetUser = User::findOrFail($empleado);
            $sameArea = $currentUser->area_id && ($targetUser->area_id == $currentUser->area_id);
            $isSubordinate = ($targetUser->jefe_id == $currentUser->id);
            if (!$sameArea && !$isSubordinate) {
                abort(403, 'No tienes permiso para ver este reporte.');
            }
        }

        $user = User::findOrFail($empleado);

        $avances = AvanceActividad::where('empleado_id', $user->id)
            ->whereDate('fecha_avance', $fecha)
            ->with('actividad')
            ->get();

        $imprevistos = ActividadImprevista::where('empleado_id', $user->id)
            ->whereDate('fecha', $fecha)
            ->get();

        $ejecucionesRutina = EjecucionRutina::whereHas('rutina', function($q) use ($user) {
                $q->where('empleado_id', $user->id);
            })
            ->whereDate('fecha', $fecha)
            ->with('rutina')
            ->get();

        $horasAvances = $avances->sum('horas_trabajadas');
        $horasImprevistas = $imprevistos->sum('horas_invertidas');
        $totalHoras = round($horasAvances + $horasImprevistas, 2);

        $observacionesPdf = $request->query('observaciones_pdf');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('actividades_diarias.reportes.bitacora.pdf_timeline', compact(
            'user',
            'fecha',
            'avances',
            'imprevistos',
            'ejecucionesRutina',
            'totalHoras',
            'observacionesPdf'
        ));

        return $pdf->stream('evidencia_diaria_' . str_replace(' ', '_', $user->name) . '_' . $fecha . '.pdf');
    }
}
