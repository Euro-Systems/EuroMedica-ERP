<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActividadesController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        if ($currentUser && !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return redirect()->route('actividades.mias');
        }

        $this->checkDefaults();
        $areas = \App\Models\Area::all();
        return view('actividades_diarias.actividades_diarias.seleccion_de_area', compact('areas'));
    }

    public function spa_master()
    {
        $areaId = session('active_area_id', 1);
        return $this->areaWorkspace($areaId);
    }

    public function areaWorkspace($id)
    {
        $request = request();
        $this->checkDefaults();
        $currentUser = auth()->user();
        
        $empleadosRH = $this->getEmpleados();
        $areas = \App\Models\Area::all();
        $rutinas = \App\Models\Rutina::with('empleado')->orderBy('created_at', 'desc')->get();

        $areaId = $id;
        session(['active_area_id' => $areaId]);
        $area = \App\Models\Area::find($areaId);

        if ($area) {
            // Fetch normal employees of this area
            $employeesQuery = User::where('area_id', $areaId)->where('activo', true);
            if ($currentUser && $currentUser->rol === 'jefe') {
                // A jefe only sees employees under them (where jefe_id = jefe->id)
                $employeesQuery->where('jefe_id', $currentUser->id);
            }
            $employees = $employeesQuery->get();

            // Determine which Jefes to add
            $jefesToAdd = collect();
            if ($currentUser) {
                if ($currentUser->rol === 'jefe') {
                    // If the current user is a jefe, check if they cover this area
                    if ($currentUser->isJefeForArea($area)) {
                        $jefesToAdd->push($currentUser);
                    }
                } elseif ($currentUser->rol === 'admin') {
                    // If the current user is Admin, find all Jefes responsible for this area
                    $allJefes = User::where('rol', 'jefe')->where('activo', true)->get();
                    foreach ($allJefes as $jefe) {
                        if ($jefe->isJefeForArea($area)) {
                            $jefesToAdd->push($jefe);
                        }
                    }
                }
            }

            // Combine employees and jefes, Jefes first!
            $allUsersForWorkspace = $jefesToAdd->concat($employees);

            if ($currentUser && !$allUsersForWorkspace->contains('id', $currentUser->id)) {
                $allUsersForWorkspace->push($currentUser);
            }

            // Move current user to the front (first column)
            if ($currentUser) {
                $allUsersForWorkspace = $allUsersForWorkspace->sortBy(function($u) use ($currentUser) {
                    return $u->id === $currentUser->id ? 0 : 1;
                })->values();
            }
            $userIds = $allUsersForWorkspace->pluck('id')->toArray();

            // Bulk query activities for all users in the workspace
            $jefeIds = $jefesToAdd->pluck('id')->toArray();
            $employeeIds = $employees->pluck('id')->toArray();

            $allUserActividades = Actividad::where(function($query) use ($employeeIds, $areaId) {
                    $query->whereIn('empleado_id', $employeeIds)
                          ->where('area_id', $areaId);
                })
                ->orWhere(function($query) use ($jefeIds) {
                    $query->whereIn('empleado_id', $jefeIds);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('empleado_id');

            // Bulk query routines with preloaded today's executions
            $allUserRutinas = \App\Models\Rutina::whereIn('empleado_id', $userIds)
                ->with(['ejecuciones' => function($q) {
                    $q->whereDate('fecha', today());
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('empleado_id');

            // Map and relate them in memory
            foreach ($allUsersForWorkspace as $u) {
                $userActividades = collect($allUserActividades->get($u->id, []))->map(function($a) {
                    $a->tipo = 'Asignada';
                    $a->fecha_display = $a->fecha_estimada_fin ? \Carbon\Carbon::parse($a->fecha_estimada_fin)->format('d/m/Y') : 'N/A';
                    return $a;
                });

                $userRutinas = collect($allUserRutinas->get($u->id, []))->map(function($r) {
                    $r->tipo = 'Rutinaria';
                    $r->fecha_display = 'Diaria';
                    
                    // Access the preloaded execution from memory (relation)
                    $ejecucion = $r->ejecuciones->first();
                    $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
                    $r->porcentaje_avance = $r->veces_al_dia > 0 ? round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100) : 0;
                    if ($r->porcentaje_avance >= 100) {
                        $r->estado = 'finalizada';
                    } elseif ($r->porcentaje_avance > 0) {
                        $r->estado = 'en_proceso';
                    } else {
                        $r->estado = 'pendiente';
                    }
                    return $r;
                });

                $merged = $userActividades->concat($userRutinas);
                $u->setRelation('actividades', $merged);
            }

            // Assign the collection to the area relation so that the view reads it transparently!
            $area->setRelation('users', $allUsersForWorkspace);
        }

        $userId = Auth::id() ?? 1;
        $comidaRegistrada = \App\Models\ActividadImprevista::where('empleado_id', $userId)
            ->where('titulo', 'Hora de Comida')->whereDate('fecha', today())->first();

        return view('actividades_diarias.actividades_diarias.spa_panel_principal', compact('area', 'empleadosRH', 'rutinas', 'areas', 'comidaRegistrada'));
    }

    public function resumen()
    {
        $request = request();
        $this->checkDefaults();
        $currentUser = auth()->user();

        $areas = \App\Models\Area::all();
        
        if ($currentUser && $currentUser->rol === 'jefe') {
            $subordinateIds = User::where('jefe_id', $currentUser->id)->orWhere('id', $currentUser->id)->pluck('id')->toArray();
        } else {
            $subordinateIds = User::pluck('id')->toArray();
        }
        
        $normalActividades = Actividad::whereIn('empleado_id', $subordinateIds)->with(['empleado', 'avances'])->orderBy('created_at', 'desc')->get()->map(function($a) {
            $a->tipo = 'Asignada';
            $a->fecha_display = $a->fecha_estimada_fin ? \Carbon\Carbon::parse($a->fecha_estimada_fin)->format('d/m/Y') : 'N/A';
            return $a;
        });

        $resumenRutinas = \App\Models\Rutina::whereIn('empleado_id', $subordinateIds)
            ->with(['empleado', 'ejecuciones' => function($q) {
                $q->whereDate('fecha', today());
            }])
            ->orderBy('created_at', 'desc')->get()->map(function($r) {
                $r->tipo = 'Rutinaria';
                $r->fecha_display = 'Diaria';
                
                $ejecucion = $r->ejecuciones->first();
                $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
                $r->porcentaje_avance = $r->veces_al_dia > 0 ? round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100) : 0;
                if ($r->porcentaje_avance >= 100) {
                    $r->estado = 'finalizada';
                } elseif ($r->porcentaje_avance > 0) {
                    $r->estado = 'en_proceso';
                } else {
                    $r->estado = 'pendiente';
                }
                return $r;
            });

        $actividades = $normalActividades->concat($resumenRutinas);
        
        $pendientes = $actividades->where('estado', 'pendiente')->count();
        $en_proceso = $actividades->where('estado', 'en_proceso')->count();
        $finalizadas = $actividades->where('estado', 'finalizada')->count();
        $atrasadas = $actividades->where('estado', 'atrasada')->count();

        $empleadosRH = $this->getEmpleados();
        $rutinas = \App\Models\Rutina::with('empleado')->orderBy('created_at', 'desc')->get();

        return view('actividades_diarias.resumen_general.tab_resumen_general', compact('actividades', 'pendientes', 'en_proceso', 'finalizadas', 'atrasadas', 'areas', 'empleadosRH', 'rutinas'));
    }

    public function selectArea($id)
    {
        session(['active_area_id' => $id]);
        return redirect()->route('actividades.resumen');
    }



    private function getEmpleados() {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->rol === 'jefe') {
            return User::where('jefe_id', $currentUser->id)->orWhere('id', $currentUser->id)->get();
        }
        return User::all();
    }

    // Helper para no interferir con la base de datos central de Recursos Humanos
    private function getEmpleadosRH_Seguro() {
        return [
            ['id' => 1, 'nombre' => 'Juan Pérez', 'puesto' => 'Soporte Técnico', 'tipo' => 'Trabajador'],
            ['id' => 2, 'nombre' => 'María Gómez', 'puesto' => 'Recepcionista', 'tipo' => 'Trabajador'],
            ['id' => 3, 'nombre' => 'Luis Rodríguez', 'puesto' => 'Auxiliar de Redes', 'tipo' => 'Practicante'],
            ['id' => 4, 'nombre' => 'Ana Martínez', 'puesto' => 'Enfermera General', 'tipo' => 'Trabajador'],
            ['id' => 5, 'nombre' => 'Carlos López', 'puesto' => 'Programador Web', 'tipo' => 'Practicante'],
        ];
    }

    private function checkDefaults() {
        if (session('defaults_checked_v2')) {
            return;
        }
        
        // Para evitar Foreign Key constraints en base de datos vacía.
        $defaultAreas = [
            1 => 'Administrativos',
            2 => 'Sistemas',
            3 => 'Marketing',
            4 => 'Administración de empresas',
            5 => 'Análisis de datos',
            6 => 'Recursos Humanos'
        ];
        
        // Batch lookup existing areas to avoid 6 separate find queries
        $existingAreas = \App\Models\Area::whereIn('id', array_keys($defaultAreas))->pluck('id')->toArray();
        
        foreach ($defaultAreas as $id => $nombre) {
            if (!in_array($id, $existingAreas)) {
                \App\Models\Area::forceCreate([
                    'id' => $id,
                    'nombre' => $nombre,
                    'descripcion' => "Área de $nombre",
                    'activo' => true
                ]);
            }
        }
        
        if (!User::where('id', 1)->exists()) {
            // Asigna los campos necesarios según tu fillable
            User::forceCreate([
                'id' => 1,
                'name' => 'Usuario de Pruebas',
                'email' => 'admin@test.com',
                'password' => bcrypt('1234'),
                'area_id' => 1,
                'rol' => 'jefe',
                'activo' => true
            ]);
        }
        
        session(['defaults_checked_v2' => true]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empleadosRH = $this->getEmpleados();
        return view('actividades_diarias.actividades.create', compact('empleadosRH'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'required|string',
            'empleado_id'         => 'nullable',
            'prioridad'           => 'nullable',
            'fecha_inicio'        => 'nullable|date',
            'fecha_estimada_fin'  => 'nullable|date',
            'tiempo_estimado'     => 'nullable',
            'impacto'             => 'nullable',
            'modalidad'           => 'nullable|in:un_dia,varios_dias'
        ]);

        $data = $request->all();

        // Defaults para actividad sencilla (sin empleado, prioridad ni fechas)
        if (empty($data['empleado_id']) || $data['empleado_id'] === 'self') {
            $data['empleado_id'] = Auth::id() ?? 1;
        }
        if (empty($data['prioridad'])) {
            $data['prioridad'] = 'media';
        }
        if (empty($data['modalidad'])) {
            $data['modalidad'] = 'un_dia';
        }
        if (empty($data['fecha_inicio'])) {
            $data['fecha_inicio'] = now()->toDateString();
        }
        if ($data['modalidad'] === 'un_dia' || empty($data['fecha_estimada_fin'])) {
            $data['fecha_estimada_fin'] = $data['fecha_inicio'];
        }
        if (empty($data['tiempo_estimado'])) {
            $data['tiempo_estimado'] = 'Por definir';
        }
        if (empty($data['impacto'])) {
            $data['impacto'] = 'Ninguno';
        }

        $this->checkDefaults();

        $data['jefe_id'] = Auth::id() ?? 1;
        $data['estado'] = 'pendiente';
        $data['porcentaje_avance'] = 0;
        
        $empleado = User::find($data['empleado_id']);
        if ($empleado) {
            $data['area_id'] = $empleado->area_id ?? session('active_area_id', 1);
        } else {
            $data['area_id'] = session('active_area_id', 1);
        }

        // Create main activity
        Actividad::create($data);

        // If it is shared, also create for other employees
        if ($request->input('_compartida') === 'si' && is_array($request->input('empleados_compartidos'))) {
            foreach ($request->input('empleados_compartidos') as $compartidoId) {
                if ($compartidoId != $data['empleado_id']) {
                    $sharedData = $data;
                    $sharedData['empleado_id'] = $compartidoId;
                    
                    $empShared = User::find($compartidoId);
                    if ($empShared) {
                        $sharedData['area_id'] = $empShared->area_id ?? session('active_area_id', 1);
                    } else {
                        $sharedData['area_id'] = session('active_area_id', 1);
                    }
                    
                    Actividad::create($sharedData);
                }
            }
        }

        return redirect()->back()->with('success', 'Actividad asignada correctamente.');
    }

    public function show(string $id)
    {
        $actividad = Actividad::with(['empleado', 'avances'])->findOrFail($id);
        return view('actividades_diarias.actividades.detalle_actividad', compact('actividad'));
    }

    public function edit(string $id)
    {
        $actividad = Actividad::findOrFail($id);
        $empleadosRH = $this->getEmpleados();
        return view('actividades_diarias.actividades.edit', compact('actividad', 'empleadosRH'));
    }

    public function update(Request $request, string $id)
    {
        $actividad = Actividad::findOrFail($id);
        
        $data = $request->all();
        if (isset($data['empleado_id']) && $data['empleado_id'] === 'self') {
            $data['empleado_id'] = Auth::id() ?? 1;
        }
        
        if (isset($data['empleado_id'])) {
            $empleado = User::find($data['empleado_id']);
            if ($empleado) {
                $data['area_id'] = $empleado->area_id ?? session('active_area_id', 1);
            } else {
                $data['area_id'] = session('active_area_id', 1);
            }
        }

        if (isset($data['modalidad']) && $data['modalidad'] === 'un_dia') {
            $data['fecha_estimada_fin'] = $data['fecha_inicio'] ?? $actividad->fecha_inicio;
        }

        if (array_key_exists('tiempo_estimado', $data) && empty($data['tiempo_estimado'])) {
            $data['tiempo_estimado'] = 'Por definir';
        }

        $actividad->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Actividad modificada con éxito.');
    }

    public function details($id)
    {
        $actividad = Actividad::with([
            'empleado',
            'jefe',
            'avances' => function($q) {
                $q->with(['empleado', 'aprobadoPor']);
            },
            'mensajes' => function($q) {
                $q->with('user');
            }
        ])->findOrFail($id);

        return response()->json($actividad);
    }

    public function exportPdf($id)
    {
        $actividad = Actividad::with([
            'empleado',
            'jefe',
            'avances' => function($q) {
                $q->with(['empleado', 'aprobadoPor']);
            },
            'mensajes' => function($q) {
                $q->with('user');
            }
        ])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('actividades_diarias.reportes.pdf_exportar_reporte', compact('actividad'));
        return $pdf->download('reporte_actividad_' . $actividad->id . '.pdf');
    }

    public function misActividades(Request $request)
    {
        $this->checkDefaults();
        $currentUser = auth()->user();
        $userId = Auth::id() ?? 1;

        $nombre = $request->input('nombre');
        $fecha = $request->input('fecha');

        $queryAsignadas = Actividad::where('empleado_id', $userId)->with(['empleado', 'avances']);
        if ($nombre) { $queryAsignadas->where('titulo', 'LIKE', "%{$nombre}%"); }
        if ($fecha) {
            $queryAsignadas->where(function($q) use ($fecha) {
                $q->whereDate('fecha_inicio', '<=', $fecha)->whereDate('fecha_estimada_fin', '>=', $fecha);
            });
        }
        $misAsignadas = $queryAsignadas->orderBy('fecha_estimada_fin', 'asc')->get()->map(function($i) {
            $i->tipo = 'Asignada'; $i->fecha_display = $i->fecha_estimada_fin ? \Carbon\Carbon::parse($i->fecha_estimada_fin)->format('d/m/Y') : 'N/A';
            $i->horas = $i->tiempo_estimado ?? 'N/A';
            return $i;
        });

        $queryImprevistas = \App\Models\ActividadImprevista::where('empleado_id', $userId)->with('empleado');
        if ($nombre) { $queryImprevistas->where('titulo', 'LIKE', "%{$nombre}%"); }
        if ($fecha) { $queryImprevistas->whereDate('fecha', $fecha); }
        
        $misImprevistas = $queryImprevistas->orderBy('created_at', 'desc')->get()->map(function($i) {
            $i->tipo = 'Imprevista'; $i->fecha_display = $i->created_at->format('d/m/Y');
            $i->horas = $i->horas_invertidas ? $i->horas_invertidas . ' hrs' : 'N/A';
            return $i;
        });
        
        $queryRutinas = \App\Models\Rutina::where('empleado_id', $userId)
            ->with(['ejecuciones' => function($q) {
                $q->whereDate('fecha', today());
            }]);
        if ($nombre) { $queryRutinas->where('titulo', 'LIKE', "%{$nombre}%"); }
        $misRutinas = $queryRutinas->orderBy('created_at', 'desc')->get()->map(function($r) {
            $r->tipo = 'Rutinaria';
            $r->fecha_display = 'Diaria';
            $r->horas = 'N/A';
            
            $ejecucion = $r->ejecuciones->first();
            $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
            $r->porcentaje_avance = $r->veces_al_dia > 0 ? round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100) : 0;
            if ($r->porcentaje_avance >= 100) {
                $r->estado = 'finalizada';
            } elseif ($r->porcentaje_avance > 0) {
                $r->estado = 'en_proceso';
            } else {
                $r->estado = 'pendiente';
            }
            return $r;
        });
        
        $listado = $misAsignadas->concat($misImprevistas)->concat($misRutinas);
        
        $comidaRegistrada = \App\Models\ActividadImprevista::where('empleado_id', $userId)
            ->where('titulo', 'Hora de Comida')->whereDate('fecha', today())->first();

        // We also need $areas, $empleadosRH, $rutinas for the modals/sidebar!
        $areas = \App\Models\Area::all();
        $empleadosRH = $this->getEmpleados();
        $rutinas = \App\Models\Rutina::with('empleado')->orderBy('created_at', 'desc')->get();

        return view('actividades_diarias.mis_actividades.tab_mis_actividades', compact('listado', 'nombre', 'fecha', 'comidaRegistrada', 'areas', 'empleadosRH', 'rutinas'));
    }

    public function registrarComida(Request $request)
    {
        $userId = Auth::id() ?? 1;
        
        // Check if already registered today
        $comidaExistente = \App\Models\ActividadImprevista::where('empleado_id', $userId)
            ->where('titulo', 'Hora de Comida')
            ->whereDate('fecha', today())
            ->first();
            
        if ($comidaExistente) {
            return back()->with('error', 'Ya registraste tu hora de comida el día de hoy.');
        }
        
        $request->validate([
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);
        
        $empleado = User::find($userId);
        
        \App\Models\ActividadImprevista::create([
            'empleado_id' => $userId,
            'area_id' => $empleado ? ($empleado->area_id ?? session('active_area_id', 1)) : session('active_area_id', 1),
            'titulo' => 'Hora de Comida',
            'descripcion_detallada' => 'Tiempo utilizado para tomar los alimentos.',
            'motivo' => 'Hora de Comida Reglamentaria',
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'horas_invertidas' => 1,
            'resultado_obtenido' => 'Comida tomada.',
            'impacto' => 'Ninguno',
            'fecha' => today()->toDateString()
        ]);
        
        return back()->with('success', 'Hora de comida registrada con éxito.');
    }

    public function destroy(string $id)
    {
        Actividad::destroy($id);
        return back()->with('success', 'Rango registrado o actulizado.');
    }

    public function actualizarEstado(Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,en_pausa,finalizada,atrasada,cancelada'
        ]);
        
        $actividad = \App\Models\Actividad::findOrFail($id);
        $actividad->estado = $request->estado;
        
        if ($request->estado == 'finalizada') {
            $actividad->porcentaje_avance = 100;
        }
        
        $actividad->save();

        return response()->json(['success' => true]);
    }

    public function aprobarRapido($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para aprobar esta actividad.'], 403);
        }
        $actividad = \App\Models\Actividad::findOrFail($id);
        $actividad->update(['estado' => 'finalizada', 'porcentaje_avance' => 100]);
        return response()->json(['success' => true]);
    }

    public function reabrirRapido($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para reabrir esta actividad.'], 403);
        }
        $actividad = \App\Models\Actividad::findOrFail($id);
        $actividad->update(['estado' => 'pendiente', 'porcentaje_avance' => 0]);
        return response()->json(['success' => true]);
    }
}

