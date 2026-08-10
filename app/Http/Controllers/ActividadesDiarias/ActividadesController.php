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

        if ($areaId === 'todas') {
            $area = (object) [
                'id' => 'todas',
                'nombre' => 'Todas las Áreas de la Empresa'
            ];
            $allUsersForWorkspace = User::where('activo', true)->orderBy('name', 'asc')->get();
        } else {
            $area = \App\Models\Area::find($areaId);
        }

        if ($area) {
            if ($areaId !== 'todas') {
                // Fetch normal employees of this area
                $employees = User::where('area_id', $areaId)->where('activo', true)->get();

                // Determine which Jefes to add
                $jefesToAdd = collect();
                $allJefes = User::where('rol', 'jefe')->where('activo', true)->get();
                foreach ($allJefes as $jefe) {
                    if ($jefe->isJefeForArea($area) || $jefe->area_id == $areaId) {
                        $jefesToAdd->push($jefe);
                    }
                }

                // Combine employees and jefes, Jefes first!
                $allUsersForWorkspace = $jefesToAdd->concat($employees)->unique('id');

                if ($currentUser && !$allUsersForWorkspace->contains('id', $currentUser->id)) {
                    $allUsersForWorkspace->push($currentUser);
                }

                // Order columns: Current user first (Admin/Boss), then Jefes of the area, then employees
                if ($currentUser) {
                    $allUsersForWorkspace = $allUsersForWorkspace->sortBy(function($u) use ($currentUser) {
                        if ($u->id === $currentUser->id) return 0;
                        if ($u->rol === 'jefe') return 1;
                        return 2;
                    })->values();
                }
            }
            $userIds = $allUsersForWorkspace->pluck('id')->toArray();

            $filtroFecha = $request->input('fecha_filtro');
            if (!$filtroFecha) {
                $filtroFecha = now()->toDateString();
            }

            // Bulk query activities for all users in the workspace
            $allUserActividades = Actividad::where(function($q) use ($userIds, $areaId, $currentUser) {
                $q->whereIn('empleado_id', $userIds)
                  ->orWhere('area_id', $areaId);
                if ($currentUser) {
                    $q->orWhere('jefe_id', $currentUser->id);
                }
            })
            ->whereDate('fecha_inicio', $filtroFecha)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('empleado_id');

            // Bulk query routines with preloaded executions
            $allUserRutinas = \App\Models\Rutina::whereIn('empleado_id', $userIds)
                ->with(['ejecuciones' => function($q) use ($filtroFecha) {
                    $q->whereDate('fecha', $filtroFecha);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('empleado_id');

            // Bulk query imprevistas
            $allUserImprevistas = \App\Models\ActividadImprevista::whereIn('empleado_id', $userIds)
                ->where('titulo', '!=', 'Hora de Comida')
                ->whereDate('fecha', $filtroFecha)
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

                $userImprevistas = collect($allUserImprevistas->get($u->id, []))->map(function($i) {
                    $i->tipo = 'Imprevista';
                    $i->fecha_display = $i->created_at ? $i->created_at->format('d/m/Y') : 'N/A';
                    $i->porcentaje_avance = ($i->estado === 'finalizada') ? 100 : (($i->estado === 'en_proceso') ? 50 : 0);
                    return $i;
                });

                $userRutinas = collect($allUserRutinas->get($u->id, []))->map(function($r) {
                    $r->tipo = 'Rutinaria';
                    $r->fecha_display = 'Diaria';
                    $r->veces_al_dia = ($r->veces_al_dia && $r->veces_al_dia > 0) ? intval($r->veces_al_dia) : 1;
                    
                    // Access the preloaded execution from memory (relation)
                    $ejecucion = $r->ejecuciones->first();
                    $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
                    $r->porcentaje_avance = round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100);
                    if ($r->porcentaje_avance >= 100) {
                        $r->estado = 'finalizada';
                    } elseif ($r->porcentaje_avance > 0) {
                        $r->estado = 'en_proceso';
                    } else {
                        $r->estado = 'pendiente';
                    }
                    return $r;
                });

                $merged = $userActividades->concat($userImprevistas)->concat($userRutinas);
                $u->setRelation('actividades', $merged);
            }

            // Assign the collection to the area relation so that the view reads it transparently!
            $area->setRelation('users', $allUsersForWorkspace);
        }

        $userId = Auth::id() ?? 1;
        $comidaRegistrada = \App\Models\ActividadImprevista::where('empleado_id', $userId)
            ->where('titulo', 'Hora de Comida')
            ->where(function($q) {
                $q->whereDate('fecha', today())->orWhereDate('created_at', today());
            })->first();

        return view('actividades_diarias.actividades_diarias.spa_panel_principal', compact('area', 'empleadosRH', 'rutinas', 'areas', 'comidaRegistrada', 'filtroFecha'));
    }

    public function resumen()
    {
        $request = request();
        $this->checkDefaults();
        $currentUser = auth()->user();

        $areas = \App\Models\Area::all();
        $activeAreaId = session('active_area_id');

        if ($activeAreaId === 'todas' || ($currentUser && in_array($currentUser->rol, ['admin', 'directivo']))) {
            $subordinateIds = User::where('activo', true)->pluck('id')->toArray();
        } elseif ($currentUser && $currentUser->rol === 'jefe') {
            $querySub = User::where('jefe_id', $currentUser->id)->orWhere('id', $currentUser->id);
            if ($activeAreaId) {
                $querySub->orWhere('area_id', $activeAreaId);
            }
            $subordinateIds = $querySub->pluck('id')->toArray();
        } else {
            $querySub = User::where('id', $currentUser ? $currentUser->id : 1);
            if ($activeAreaId) {
                $querySub->orWhere('area_id', $activeAreaId);
            }
            $subordinateIds = $querySub->pluck('id')->toArray();
        }
        
        $filtroFecha = $request->input('fecha_filtro'); 
        if (!$filtroFecha) {
            $filtroFecha = now()->toDateString();
        }

        $queryNormal = Actividad::whereIn('empleado_id', $subordinateIds);
        $queryNormal->whereDate('fecha_inicio', $filtroFecha);
        
        $normalActividades = $queryNormal->with(['empleado', 'avances' => function($q) {
                $q->with('empleado')->orderBy('created_at', 'desc');
            }])->orderBy('created_at', 'desc')->get()->map(function($a) {
            $a->tipo = 'Asignada';
            $a->fecha_grupo = $a->created_at ? $a->created_at->format('Y-m-d') : now()->format('Y-m-d');
            $a->fecha_display = $a->fecha_estimada_fin ? \Carbon\Carbon::parse($a->fecha_estimada_fin)->format('d/m/Y') : 'N/A';
            $a->historial_avances_list = $a->avances->map(function($av) {
                return [
                    'fecha' => $av->created_at ? $av->created_at->format('d/m/Y H:i') : ($av->fecha_avance ?? ''),
                    'porcentaje' => $av->porcentaje_avance ?? 0,
                    'empleado' => $av->empleado ? $av->empleado->name : 'Empleado',
                    'nota' => $av->comentario ?? $av->que_se_hizo ?? 'Sin notas'
                ];
            })->values()->toArray();
            return $a;
        });

        $queryImprevistas = \App\Models\ActividadImprevista::whereIn('empleado_id', $subordinateIds)
            ->where('titulo', '!=', 'Hora de Comida');
        if ($filtroFecha) {
            $queryImprevistas->whereDate('fecha', $filtroFecha);
        }
        $resumenImprevistas = $queryImprevistas->with('empleado')
            ->orderBy('created_at', 'desc')->get()->map(function($i) {
                $i->tipo = 'Imprevista';
                $i->fecha_grupo = $i->created_at ? $i->created_at->format('Y-m-d') : now()->format('Y-m-d');
                $i->fecha_display = $i->created_at ? $i->created_at->format('d/m/Y') : 'N/A';
                $i->porcentaje_avance = $i->porcentaje_avance ?? (($i->estado === 'finalizada') ? 100 : (($i->estado === 'en_proceso') ? 50 : 0));
                $i->historial_avances_list = [
                    [
                        'fecha' => $i->updated_at ? $i->updated_at->format('d/m/Y H:i') : ($i->created_at ? $i->created_at->format('d/m/Y H:i') : ''),
                        'porcentaje' => $i->porcentaje_avance,
                        'empleado' => $i->empleado ? $i->empleado->name : 'Empleado',
                        'nota' => $i->resultado_obtenido ?? $i->motivo ?? 'Atención de imprevisto'
                    ]
                ];
                return $i;
            });

        $queryRutinas = \App\Models\Rutina::whereIn('empleado_id', $subordinateIds);
        if ($filtroFecha) {
            $queryRutinas->with(['empleado', 'ejecuciones' => function($q) use ($filtroFecha) {
                $q->whereDate('fecha', $filtroFecha);
            }]);
        } else {
            $queryRutinas->with(['empleado', 'ejecuciones' => function($q) {
                $q->whereDate('fecha', today());
            }]);
        }
        
        $resumenRutinas = $queryRutinas->orderBy('created_at', 'desc')->get()->map(function($r) use ($filtroFecha) {
                $r->tipo = 'Rutinaria';
                $r->fecha_grupo = $filtroFecha ?: now()->format('Y-m-d');
                $r->fecha_display = 'Diaria';
                $r->veces_al_dia = ($r->veces_al_dia && $r->veces_al_dia > 0) ? intval($r->veces_al_dia) : 1;
                
                $ejecucion = $r->ejecuciones->first();
                $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
                $r->porcentaje_avance = round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100);
                if ($r->porcentaje_avance >= 100) {
                    $r->estado = 'finalizada';
                } elseif ($r->porcentaje_avance > 0) {
                    $r->estado = 'en_proceso';
                } else {
                    $r->estado = 'pendiente';
                }

                $list = [];
                if ($ejecucion && is_array($ejecucion->horas_registro)) {
                    foreach ($ejecucion->horas_registro as $index => $item) {
                        $p = round((($index + 1) / $r->veces_al_dia) * 100);
                        if (is_array($item)) {
                            $list[] = [
                                'fecha' => ($item['hora'] ?? 'HH:mm') . ' (Hoy)',
                                'porcentaje' => $p,
                                'empleado' => $item['usuario'] ?? ($r->empleado ? $r->empleado->name : 'Empleado'),
                                'nota' => $item['nota'] ?? "Ejecución " . ($index + 1) . " realizada"
                            ];
                        } else {
                            $list[] = [
                                'fecha' => $item . ' (Hoy)',
                                'porcentaje' => $p,
                                'empleado' => $r->empleado ? $r->empleado->name : 'Empleado',
                                'nota' => "Ejecución " . ($index + 1) . " de " . $r->veces_al_dia . " realizada"
                            ];
                        }
                    }
                }
                $r->historial_avances_list = $list;

                return $r;
            });

        $actividades = $normalActividades->concat($resumenImprevistas)->concat($resumenRutinas);
        
        $pendientes = $actividades->where('estado', 'pendiente')->count();
        $en_proceso = $actividades->where('estado', 'en_proceso')->count();
        $finalizadas = $actividades->where('estado', 'finalizada')->count();
        $atrasadas = $actividades->where('estado', 'atrasada')->count();

        $empleadosRH = $this->getEmpleados();
        $rutinas = \App\Models\Rutina::with('empleado')->orderBy('created_at', 'desc')->get();

        return view('actividades_diarias.resumen_general.tab_resumen_general', compact('actividades', 'pendientes', 'en_proceso', 'finalizadas', 'atrasadas', 'areas', 'empleadosRH', 'rutinas', 'filtroFecha'));
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
            6 => 'Recursos Humanos',
            7 => 'Nómina',
            8 => 'Enfermería',
            9 => 'ADD',
            10 => 'ADE',
            11 => 'Operaciones'
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
        if (empty($request->input('descripcion')) && $request->filled('descripcion_detallada')) {
            $request->merge(['descripcion' => $request->input('descripcion_detallada')]);
        }

        $request->validate([
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'required|string',
            'empleado_id'         => 'nullable',
            'prioridad'           => 'nullable',
            'fecha_inicio'        => 'nullable|date',
            'fecha_estimada_fin'  => 'nullable|date',
            'tiempo_estimado'     => 'nullable',
            'impacto'             => 'nullable',
            'modalidad'           => 'nullable|in:un_dia,varios_dias',
            'permitir_registro_avance' => 'nullable',
            'dirigido_a_id'      => 'nullable',
            'hora_inicio'        => 'nullable|string',
            'hora_fin'           => 'nullable|string',
            'acciones_realizadas'=> 'nullable|string',
            'dependencia_area'   => 'nullable|string',
            'dependencia_responsable' => 'nullable|string',
            'dependencia_motivo' => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'comentarios_dirigido' => 'nullable|string',
        ]);

        $data = $request->all();

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
        $validImpactos = ['Pacientes', 'Sistemas', 'Administración', 'Recursos Humanos', 'Medicina Laboral', 'Laboratorio', 'Operaciones', 'Dirección'];
        if (empty($data['impacto']) || !in_array($data['impacto'], $validImpactos)) {
            $emp = User::find($data['empleado_id'] ?? null);
            $areaId = $emp->area_id ?? session('active_area_id', 1);
            $area = \App\Models\Area::find($areaId);
            if ($area && in_array($area->nombre, $validImpactos)) {
                $data['impacto'] = $area->nombre;
            } else {
                $data['impacto'] = 'Administración';
            }
        }

        $this->checkDefaults();

        if ($request->input('tiene_plazo') === 'no') {
            $data['tiene_plazo'] = 'no';
            $data['hora_inicio'] = null;
            $data['hora_fin'] = null;
            $data['fecha_inicio'] = $request->input('fecha_inicio') ?: now()->toDateString();
            $data['fecha_estimada_fin'] = $request->input('fecha_estimada_fin') ?: $data['fecha_inicio'];
            $data['tiempo_estimado'] = 'Sin plazo';
            $data['prioridad'] = 'baja';
        }

        $data['jefe_id'] = Auth::id() ?? 1;
        $data['estado'] = 'pendiente';
        $data['porcentaje_avance'] = 0;
        $data['permitir_registro_avance'] = $request->has('permitir_registro_avance') ? 1 : 0;
        
        $empleado = User::find($data['empleado_id']);
        if ($empleado) {
            $data['area_id'] = $empleado->area_id ?? session('active_area_id', 1);
        } else {
            $data['area_id'] = session('active_area_id', 1);
        }

        // Create main activity
        $actividad = Actividad::create($data);

        // If it is shared, also create for other employees
        $nombresCompartidos = [];
        if (($request->input('_colaboro_asig_radio') === 'si' || $request->input('_compartida') === 'si') && is_array($request->input('empleados_compartidos'))) {
            foreach ($request->input('empleados_compartidos') as $compartidoId) {
                if ($compartidoId != $data['empleado_id']) {
                    $empShared = User::find($compartidoId);
                    if ($empShared) {
                        $nombresCompartidos[] = $empShared->name;
                    }
                    
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
            
            if (!empty($nombresCompartidos)) {
                $actividad->colaboradores_texto = implode(', ', $nombresCompartidos);
                $actividad->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Actividad asignada correctamente.']);
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

        if ($request->input('tiene_plazo') === 'no') {
            $data['tiene_plazo'] = 'no';
            $data['hora_inicio'] = null;
            $data['hora_fin'] = null;
            $data['fecha_inicio'] = $data['fecha_inicio'] ?? $actividad->fecha_inicio ?? now()->toDateString();
            $data['fecha_estimada_fin'] = $data['fecha_estimada_fin'] ?? $actividad->fecha_estimada_fin ?? $data['fecha_inicio'];
            $data['tiempo_estimado'] = 'Sin plazo';
            $data['prioridad'] = 'baja';
        }

        if (array_key_exists('tiempo_estimado', $data) && empty($data['tiempo_estimado'])) {
            $data['tiempo_estimado'] = 'Por definir';
        }

        if ($request->has('porcentaje_avance') || $request->filled('comentario_avance') || $request->hasFile('archivos_avance')) {
            $porcentaje = $request->has('porcentaje_avance')
                ? max(0, min(100, intval($request->input('porcentaje_avance'))))
                : ($actividad->porcentaje_avance ?? 0);

            $data['porcentaje_avance'] = $porcentaje;
            if ($porcentaje >= 100) {
                $data['estado'] = 'finalizada';
            } elseif ($porcentaje > 0 && ($actividad->estado === 'pendiente' || empty($data['estado']))) {
                $data['estado'] = 'en_proceso';
            }

            if ($request->filled('comentario_avance')) {
                unset($data['hora_inicio']);
                unset($data['hora_fin']);
            }

            $actividad->update($data);

            $fileHtml = $this->processUploadedFiles($request);
            $comentarioNota = $request->filled('comentario_avance') 
                ? $request->input('comentario_avance') 
                : ('Avance registrado al ' . $porcentaje . '%');
            if ($fileHtml !== '') {
                $comentarioNota .= $fileHtml;
            }

            \App\Models\AvanceActividad::create([
                'actividad_id' => $actividad->id,
                'empleado_id' => $actividad->empleado_id ?? (Auth::id() ?? 1),
                'que_se_hizo' => $comentarioNota,
                'comentario' => $comentarioNota,
                'resultado_final' => 'Avance registrado: ' . $porcentaje . '%',
                'porcentaje_avance' => $porcentaje,
                'fecha_avance' => now()->toDateString(),
                'estado_aprobacion' => 'aprobado',
                'motivo' => 'Actualización directa',
                'hora_inicio' => $request->has('sin_horario') ? null : $request->input('hora_inicio', now()->format('H:i')),
                'hora_fin' => $request->has('sin_horario') ? null : $request->input('hora_fin', now()->format('H:i')),
                'horas_trabajadas' => $request->has('sin_horario') ? 0 : floatval($request->input('horas_invertidas', 0))
            ]);
        } else {
            if ($request->has('marcar_completada') && $request->marcar_completada == '1') {
                $data['estado'] = 'finalizada';
                $data['porcentaje_avance'] = 100;
                $actividad->update($data);
                
                $comentarioNota = $request->input('notas_completada', 'Actividad marcada como completada directamente.');
                
                \App\Models\AvanceActividad::create([
                    'actividad_id' => $actividad->id,
                    'empleado_id' => $actividad->empleado_id ?? (Auth::id() ?? 1),
                    'que_se_hizo' => $comentarioNota,
                    'comentario' => $comentarioNota,
                    'resultado_final' => 'Actividad Completada 100%',
                    'porcentaje_avance' => 100,
                    'fecha_avance' => now()->toDateString(),
                    'estado_aprobacion' => 'aprobado',
                    'motivo' => 'Marcada como completada desde edición',
                    'hora_inicio' => $request->has('sin_horario') ? null : $request->input('hora_inicio', now()->format('H:i')),
                    'hora_fin' => $request->has('sin_horario') ? null : $request->input('hora_fin', now()->format('H:i')),
                    'horas_trabajadas' => $request->has('sin_horario') ? 0 : floatval($request->input('horas_invertidas', 0))
                ]);
            } else {
                $actividad->update($data);
            }
        }

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
        return $pdf->stream('reporte_actividad_' . $actividad->id . '.pdf');
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
        if (!$nombre && !$fecha) {
            $queryAsignadas->where(function($q) {
                $q->where('estado', '!=', 'finalizada')->orWhereDate('updated_at', '>=', now()->subDays(7));
            });
        }
        $misAsignadas = $queryAsignadas->orderBy('fecha_estimada_fin', 'asc')->get()->map(function($i) {
            $i->tipo = 'Asignada'; $i->fecha_display = $i->fecha_estimada_fin ? \Carbon\Carbon::parse($i->fecha_estimada_fin)->format('d/m/Y') : 'N/A';
            $i->horas = $i->tiempo_estimado ?? 'N/A';
            $i->historial_avances_list = $i->avances ? $i->avances->map(function($av) {
                return [
                    'fecha' => $av->created_at ? $av->created_at->format('d/m/Y H:i') : ($av->fecha_avance ?? ''),
                    'porcentaje' => $av->porcentaje_avance ?? 0,
                    'empleado' => $av->empleado ? $av->empleado->name : 'Empleado',
                    'nota' => $av->comentario ?? $av->que_se_hizo ?? 'Sin notas'
                ];
            })->values()->toArray() : [];
            return $i;
        });

        $queryImprevistas = \App\Models\ActividadImprevista::where('empleado_id', $userId)->with('empleado');
        if ($nombre) { $queryImprevistas->where('titulo', 'LIKE', "%{$nombre}%"); }
        if ($fecha) { $queryImprevistas->whereDate('fecha', $fecha); }
        if (!$nombre && !$fecha) {
            $queryImprevistas->whereDate('created_at', '>=', now()->subDays(7));
        }
        
        $misImprevistas = $queryImprevistas->orderBy('created_at', 'desc')->get()->map(function($i) {
            $i->tipo = 'Imprevista'; $i->fecha_display = $i->created_at ? $i->created_at->format('d/m/Y') : 'N/A';
            $i->horas = $i->horas_invertidas ? $i->horas_invertidas . ' hrs' : 'N/A';
            $i->porcentaje_avance = $i->porcentaje_avance ?? (($i->estado === 'finalizada') ? 100 : (($i->estado === 'en_proceso') ? 50 : 0));
            $i->historial_avances_list = [
                [
                    'fecha' => $i->updated_at ? $i->updated_at->format('d/m/Y H:i') : ($i->created_at ? $i->created_at->format('d/m/Y H:i') : ''),
                    'porcentaje' => $i->porcentaje_avance,
                    'empleado' => $i->empleado ? $i->empleado->name : 'Empleado',
                    'nota' => $i->resultado_obtenido ?? $i->motivo ?? 'Atención de imprevisto'
                ]
            ];
            return $i;
        });
        
        $queryRutinas = \App\Models\Rutina::where('empleado_id', $userId)
            ->with(['empleado', 'ejecuciones' => function($q) {
                $q->whereDate('fecha', today());
            }]);
        if ($nombre) { $queryRutinas->where('titulo', 'LIKE', "%{$nombre}%"); }
        $misRutinas = $queryRutinas->orderBy('created_at', 'desc')->get()->map(function($r) {
            $r->tipo = 'Rutinaria';
            $r->fecha_display = 'Diaria';
            $r->horas = 'N/A';
            $r->veces_al_dia = ($r->veces_al_dia && $r->veces_al_dia > 0) ? intval($r->veces_al_dia) : 1;
            
            $ejecucion = $r->ejecuciones->first();
            $r->ejecuciones_hoy = $ejecucion ? $ejecucion->cantidad_ejecuciones : 0;
            $r->porcentaje_avance = round(($r->ejecuciones_hoy / $r->veces_al_dia) * 100);
            if ($r->porcentaje_avance >= 100) {
                $r->estado = 'finalizada';
            } elseif ($r->porcentaje_avance > 0) {
                $r->estado = 'en_proceso';
            } else {
                $r->estado = 'pendiente';
            }

            $list = [];
            if ($ejecucion && is_array($ejecucion->horas_registro)) {
                foreach ($ejecucion->horas_registro as $index => $item) {
                    $p = round((($index + 1) / $r->veces_al_dia) * 100);
                    if (is_array($item)) {
                        $list[] = [
                            'fecha' => ($item['hora'] ?? 'HH:mm') . ' (Hoy)',
                            'porcentaje' => $p,
                            'empleado' => $item['usuario'] ?? ($r->empleado ? $r->empleado->name : 'Empleado'),
                            'nota' => $item['nota'] ?? "Ejecución " . ($index + 1) . " realizada"
                        ];
                    } else {
                        $list[] = [
                            'fecha' => $item . ' (Hoy)',
                            'porcentaje' => $p,
                            'empleado' => $r->empleado ? $r->empleado->name : 'Empleado',
                            'nota' => "Ejecución " . ($index + 1) . " de " . $r->veces_al_dia . " realizada"
                        ];
                    }
                }
            }
            $r->historial_avances_list = $list;

            return $r;
        });
        
        $listado = $misAsignadas->concat($misImprevistas)->concat($misRutinas);

        // Sort by Priority (Urgente > Alta > Media > Baja)
        $prioWeights = ['urgente' => 1, 'alta' => 2, 'media' => 3, 'baja' => 4];
        $listado = $listado->sortBy(function($item) use ($prioWeights) {
            $prioKey = strtolower($item->prioridad ?? 'media');
            return $prioWeights[$prioKey] ?? 3;
        })->values();
        
        $comidaRegistrada = \App\Models\ActividadImprevista::where('empleado_id', $userId)
            ->where('titulo', 'Hora de Comida')
            ->where(function($q) {
                $q->whereDate('fecha', today())->orWhereDate('created_at', today());
            })->first();

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
            ->where(function($q) {
                $q->whereDate('fecha', today())->orWhereDate('created_at', today());
            })
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
            'impacto' => 'Administración',
            'fecha' => today()->toDateString(),
            'estado' => 'finalizada',
            'porcentaje_avance' => 100,
            'resultado_obtenido' => 'Descanso de alimentos completado'
        ]);
        
        return back()->with('success', 'Hora de comida registrada con éxito.');
    }

    public function destroy(string $id)
    {
        Actividad::destroy($id);
        return back()->with('success', 'Actividad eliminada con éxito.');
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

    private function processUploadedFiles(Request $request) {
        $fileHtml = '';
        if ($request->hasFile('archivos_avance')) {
            $files = $request->file('archivos_avance');
            if (!is_array($files)) {
                $files = [$files];
            }

            $uploadPath = public_path('uploads/avances');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($uploadPath, $filename);

                    $fileUrl = asset('uploads/avances/' . $filename);
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

                    if (in_array($extension, $imageExtensions)) {
                        $fileHtml .= "\n" . '<div style="margin-top: 8px;"><a href="' . $fileUrl . '" target="_blank" style="text-decoration:none;"><img src="' . $fileUrl . '" style="max-width: 260px; max-height: 180px; border-radius: 8px; border: 2px solid #cbd5e1; object-fit: cover; display: block; margin-top: 4px;" alt="Evidencia"></a><span style="font-size:11px; color:#64748b; font-weight:600;">📷 ' . e($originalName) . '</span></div>';
                    } else {
                        $fileHtml .= "\n" . '<div style="margin-top: 8px;"><a href="' . $fileUrl . '" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #0f172a; border: 1.5px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"><i class="bi bi-file-earmark-arrow-down-fill" style="color:#2563eb;"></i> Documento: ' . e($originalName) . '</a></div>';
                    }
                }
            }
        }
        return $fileHtml;
    }

    public function devolver(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin', 'directivo'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para devolver esta actividad.'], 403);
        }

        $actividad = Actividad::findOrFail($id);
        $porcentajeAjustado = max(0, min(99, intval($request->input('porcentaje_avance', 50))));
        $comentarioJefe = trim($request->input('comentario_jefe') ?? '');

        if (empty($comentarioJefe)) {
            return response()->json(['success' => false, 'message' => 'Por favor indica qué faltó por completar o las instrucciones de corrección.'], 422);
        }

        $estadoNuevo = $porcentajeAjustado > 0 ? 'en_proceso' : 'pendiente';

        $actividad->update([
            'porcentaje_avance' => $porcentajeAjustado,
            'estado' => $estadoNuevo
        ]);

        $notaFinal = "↩️ [Devuelta por el Jefe - Avance ajustado al {$porcentajeAjustado}%]: " . $comentarioJefe;

        $horaDevolucion = $request->input('hora_devolucion', now()->format('H:i'));

        \App\Models\AvanceActividad::create([
            'actividad_id' => $actividad->id,
            'empleado_id' => $currentUser->id,
            'que_se_hizo' => $notaFinal,
            'comentario' => $notaFinal,
            'resultado_final' => 'Actividad devuelta por el jefe',
            'porcentaje_avance' => $porcentajeAjustado,
            'fecha_avance' => now()->toDateString(),
            'estado_aprobacion' => 'rechazado',
            'aprobado_por_id' => $currentUser->id,
            'comentario_jefe' => $comentarioJefe,
            'hora_inicio' => $horaDevolucion,
            'hora_fin' => $horaDevolucion,
            'horas_trabajadas' => 0
        ]);

        return response()->json(['success' => true, 'message' => 'Actividad devuelta al empleado con las observaciones registradas.']);
    }
}

