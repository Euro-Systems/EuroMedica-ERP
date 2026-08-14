@extends(request()->ajax() ? 'actividades_diarias.actividades_diarias.layout_ajax' : 'actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Resumen General')

@section('actividades-content')
@php
    $currentUser = auth()->user();
    $isBossOrAdmin = $currentUser && in_array($currentUser->rol, ['jefe', 'admin', 'directivo']);
    $activeAreaId = session('active_area_id');
    $activeAreaObj = $activeAreaId ? $areas->firstWhere('id', $activeAreaId) : null;
@endphp

<!-- RESUMEN PARCIAL -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-bar-chart-line-fill me-2" style="color:#3b82f6;"></i>Resumen General del Equipo
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Visión global de todas las actividades asignadas · {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" onclick="abrirModalDescargaPDF('{{ route('bitacora.pdf', ['empleado' => auth()->id(), 'fecha' => now()->toDateString()]) }}')" class="btn-ver" style="background:#0284c7; color:white; border:none; padding:10px 18px; border-radius:8px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px; cursor:pointer; box-shadow:0 4px 6px rgba(2,132,199,0.2);">
            <i class="bi bi-file-earmark-pdf-fill"></i> Reporte PDF Hoy
        </button>
        <!-- BOTÓN NEGRO PARA NUEVA ACTIVIDAD -->
        <button type="button" onclick="abrirModalCrearActividad('asignada')" class="btn-ver" style="background:#0f172a; color:white; border:none; padding:10px 22px; border-radius:8px; font-weight:800; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 4px 6px rgba(0,0,0,0.15);">
            <i class="bi bi-plus-lg" style="font-size:16px;"></i> + Nueva Actividad
        </button>
    </div>
</div>

<!-- TARJETA DESTACADA DEL ÁREA CONSULTADA ACTUAL -->
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; border-radius: 12px; padding: 14px 20px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(37,99,235,0.25); flex-wrap:wrap; gap:12px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <div style="background: rgba(255,255,255,0.2); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="bi bi-buildings-fill"></i>
        </div>
        <div>
            <span style="font-size: 11px; text-transform: uppercase; font-weight: 800; opacity: 0.9; letter-spacing: 0.6px; display:block;">ÁREA SELECCIONADA ACTUALMENTE:</span>
            <h3 style="margin: 2px 0 0 0; font-size: 19px; font-weight: 900; color: #ffffff;">
                {{ $activeAreaObj ? $activeAreaObj->nombre : 'Todas las Áreas de la Clínica' }}
            </h3>
        </div>
    </div>
    <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; padding: 6px 16px; font-weight: 800; font-size: 13px;">
        <i class="bi bi-people-fill me-1"></i> {{ count($actividades) }} Actividades Mostradas
    </div>
</div>

@php
    $atrasadasCount = 0;
    foreach($actividades as $actCheck) {
        $isAtrasada = ($actCheck->tiene_plazo ?? 'si') !== 'no' && $actCheck->fecha_estimada_fin && \Carbon\Carbon::parse($actCheck->fecha_estimada_fin)->isPast() && !in_array($actCheck->estado, ['finalizada']);
        if ($isAtrasada || $actCheck->estado === 'atrasada') {
            $atrasadasCount++;
        }
    }
@endphp

@if($atrasadasCount > 0)
<div style="background:#fef2f2; border: 2px solid #ef4444; color:#991b1b; border-radius:12px; padding:12px 18px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(239,68,68,0.15); flex-wrap:wrap; gap:10px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="background:#ef4444; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:16px;">⚠️</span>
        <div>
            <strong style="font-size:14px; display:block;">ATENCIÓN: Se detectaron {{ $atrasadasCount }} actividades atrasadas o con plazo vencido</strong>
            <span style="font-size:12px; opacity:0.9;">Haga clic en el filtro "Atrasadas" para revisarlas e interactuar con el responsable.</span>
        </div>
    </div>
    <button type="button" onclick="document.getElementById('filter-status').value='atrasada'; filterActivities();" style="background:#991b1b; color:white; border:none; padding:8px 16px; border-radius:8px; font-weight:800; font-size:12px; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.15);">
        Ver {{ $atrasadasCount }} Atrasada(s)
    </button>
</div>
@endif

<!-- Filtros y Búsqueda Completos (Una sola fila horizontal compacta con overflow-x) -->
<div class="rh-card" style="margin-bottom:16px; padding:10px 14px; border-radius:12px; overflow-x:auto;">
    <div style="display:flex; gap:8px; align-items:center; flex-wrap:nowrap; min-width:max-content; width:100%;">
        <div style="width:200px; flex-shrink:0;">
            <input type="text" id="search-query" oninput="filterActivities()" placeholder="🔍 Buscar por colab, título..." style="width:100%; padding:8px 10px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; outline:none; box-sizing:border-box;">
        </div>
        <div style="width:115px; flex-shrink:0;">
            <select id="filter-type" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; font-weight:600; cursor:pointer;">
                <option value="">Tipo: Todos</option>
                <option value="asignada">Asignadas</option>
                <option value="imprevista">Personales</option>
                <option value="rutinaria">Rutinarias</option>
            </select>
        </div>
        <div style="width:115px; flex-shrink:0;">
            <select id="filter-priority" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; font-weight:600; cursor:pointer;">
                <option value="">Prioridad: Todas</option>
                <option value="sin_prioridad">Sin prioridad</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
        <div style="width:115px; flex-shrink:0;">
            <select id="filter-status" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; font-weight:600; cursor:pointer;">
                <option value="">Estado: Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="finalizada">Finalizada</option>
                <option value="atrasada">Atrasada</option>
            </select>
        </div>
        <div style="width:115px; flex-shrink:0;">
            <select id="filter-area" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; cursor:pointer;">
                <option value="">Área: Todas</option>
                @foreach($areas as $areaItem)
                    <option value="{{ $areaItem->id }}" {{ session('active_area_id') == $areaItem->id ? 'selected' : '' }}>{{ $areaItem->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div style="width:125px; flex-shrink:0;">
            <select id="filter-employee" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; cursor:pointer;">
                <option value="">Empleado: Todos</option>
                @foreach($empleadosRH as $emp)
                    <option value="{{ $emp['id'] ?? $emp->id }}">{{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}</option>
                @endforeach
            </select>
        </div>
        <div style="width:135px; flex-shrink:0;">
            @php
                $diasFiltro = [];
                for($i = 0; $i < 15; $i++) {
                    $d = \Carbon\Carbon::today()->subDays($i);
                    $diasFiltro[] = [
                        'valor' => $d->toDateString(),
                        'etiqueta' => $i === 0 ? 'Hoy (' . $d->format('d/m') . ')' : ($i === 1 ? 'Ayer (' . $d->format('d/m') . ')' : $d->format('d/m/Y'))
                    ];
                }
            @endphp
            <select id="filter-date-backend" onchange="window.location.href='?fecha_filtro='+this.value" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; font-weight:600; color:#1e3a8a; cursor:pointer;">
                @foreach($diasFiltro as $df)
                    <option value="{{ $df['valor'] }}" {{ (isset($filtroFecha) && $filtroFecha == $df['valor']) ? 'selected' : '' }}>
                        Fecha: {{ $df['etiqueta'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="width:130px; flex-shrink:0;">
            <select id="filter-seguimiento" onchange="filterActivities()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:12px; background:#fff; font-weight:600; cursor:pointer;">
                <option value="">Seguimiento: Todos</option>
                <option value="si">En seguimiento</option>
                <option value="no">Sin seguimiento</option>
            </select>
        </div>
    </div>
</div>

<!-- Listado Agrupado por Acordeones de Usuario -->
<div class="accordion" id="accordionResumenGeneral" style="display:flex; flex-direction:column; gap:12px;">
    @php
        $grouped = $actividades->groupBy('empleado_id');
    @endphp
    @forelse($grouped as $empId => $userActividades)
        @php
            $empObj = $userActividades->first()->empleado;
            $empNombre = $empObj ? $empObj->name : 'Sin Responsable';
            $empRol = $empObj ? ucfirst($empObj->rol) : 'Empleado';
            $totalCount = $userActividades->count();
            $pendientesCount = $userActividades->where('estado', 'pendiente')->count();
            $procesoCount = $userActividades->where('estado', 'en_proceso')->count();
            $seguimientoCount = $userActividades->where('en_seguimiento', true)->count();
            $finalizadasCount = $userActividades->where('estado', 'finalizada')->count();
            
            $collapseId = "collapseUser_" . $empId;
            $headerId = "headerUser_" . $empId;
        @endphp
        
        <div class="card card-user-group" id="card_user_group_{{ $empId }}" data-employee="{{ $empId }}" style="border: 1.5px solid #cbd5e1; border-radius: 10px; overflow: hidden; background: #ffffff;">
            <!-- Accordion Header -->
            <div class="card-header" id="{{ $headerId }}" style="padding: 0; background: #f8fafc; border-bottom: 1.5px solid #cbd5e1;">
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}" 
                        style="width: 100%; text-align: left; padding: 14px 20px; text-decoration: none; color: #1e3a8a; display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 14px; border: none; background: transparent; cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-person-fill" style="font-size: 16px; color: #2563eb;"></i>
                        <span style="font-size: 15px; font-weight: 900; color: #1e3a8a;">{{ strtoupper($empNombre) }}</span>
                        <span style="font-size: 11px; background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 12px; font-weight: bold; text-transform: uppercase;">{{ $empRol }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">{{ $totalCount }} Actividades</span>
                        @if($pendientesCount > 0)
                            <span class="badge" style="background: #fef3c7; color: #d97706; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">{{ $pendientesCount }} Pendientes</span>
                        @endif
                        @if($procesoCount > 0)
                            <span class="badge" style="background: #e2e8f0; color: #2563eb; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">{{ $procesoCount }} En Proceso</span>
                        @endif
                        @if($seguimientoCount > 0)
                            <span class="badge" style="background: #f3e8ff; color: #6b21a8; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">{{ $seguimientoCount }} Seguimiento</span>
                        @endif
                        @if($finalizadasCount > 0)
                            <span class="badge" style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px;">{{ $finalizadasCount }} Finalizadas</span>
                        @endif
                        <i class="bi bi-chevron-down ms-2 text-secondary" style="font-size: 14px;"></i>
                    </div>
                </button>
            </div>

            <!-- Accordion Content -->
            <div id="{{ $collapseId }}" class="collapse show" aria-labelledby="{{ $headerId }}">
                <div class="card-body" style="padding: 0;">
                    <div style="overflow-x: auto;">
                        <table class="rh-table" style="margin: 0; border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr style="background: #1e3a8a; color: white;">
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none; width: 25%;">Actividad</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none; text-align: center;">Prioridad</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none;">Progreso</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none;">Dependencia</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none; width: 25%;">Descripción</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none;">Plazo</th>
                                    <th style="padding: 8px 12px; font-weight: bold; font-size: 12px; border: none; text-align: center; width: 10%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userActividades as $act)
                                    @php
                                       $tipoKey = strtolower($act->tipo ?? 'asignada');
                                       $isComida = strtolower($act->titulo ?? '') === 'hora de comida';
                                       if ($isComida) continue;

                                       $esSeguimiento = (bool)($act->en_seguimiento ?? false);

                                       if ($tipoKey === 'imprevista') {
                                           $destroyRoute = route('actividades-imprevistas.destroy', $act->id);
                                           $descText = $act->descripcion_detallada ?? $act->descripcion;
                                           $rowBg = '#fff7ed';
                                           $rowBorder = '#ea580c';
                                       } elseif ($tipoKey === 'rutinaria') {
                                           $destroyRoute = route('rutinas.destroy', $act->id);
                                           $descText = $act->descripcion;
                                           $rowBg = '#eff6ff';
                                           $rowBorder = '#2563eb';
                                       } else {
                                           $destroyRoute = route('actividades.destroy', $act->id);
                                           $descText = $act->descripcion;
                                           $rowBg = '#f0fdf4';
                                           $rowBorder = '#16a34a';
                                       }

                                       if ($esSeguimiento) {
                                           $rowBg = '#f5f3ff';
                                           $rowBorder = '#8b5cf6';
                                       }

                                       $empNombre = $act->empleado ? $act->empleado->name : 'N/A';
                                       $empAreaId = $act->empleado ? ($act->empleado->area_id ?? '') : '';
                                       $empId     = $act->empleado_id;
                                       $creadorNombre = $act->creador ? $act->creador->name : ($isBossOrAdmin ? 'Jefe / Admin' : 'Empleado');
                                       $fechaDisplay = $tipoKey === 'rutinaria' ? 'Diaria' : (($act->fecha_inicio ? \Carbon\Carbon::parse($act->fecha_inicio)->format('d/m/Y') : '') . ' - ' . ($act->fecha_estimada_fin ? \Carbon\Carbon::parse($act->fecha_estimada_fin)->format('d/m/Y') : ''));
                                       $porcentaje = intval($act->porcentaje_avance ?? 0);
                                       $puedeAvance = ($act->permitir_registro_avance ?? 1) || $isBossOrAdmin;

                                        $ahora = \Carbon\Carbon::now();
                                        $esFinalizada = ($act->estado === 'finalizada');
                                        $tienePlazoVal = ($act->tiene_plazo ?? 'si') !== 'no';

                                        $plazoBadgeHtml = '';
                                        if ($esFinalizada) {
                                            $plazoBadgeHtml = '<span style="background:#dcfce7; color:#166534; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-check-circle-fill"></i> Finalizada</span>';
                                        } elseif ($esSeguimiento) {
                                            $plazoBadgeHtml = '<span style="background:#f3e8ff; color:#6b21a8; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1.5px solid #d8b4fe; display:inline-flex; align-items:center; gap:4px;" title="Actividad en Seguimiento"><i class="bi bi-calendar2-check-fill"></i> EN SEGUIMIENTO</span>';
                                            if ($tienePlazoVal && $act->fecha_display !== 'N/A') {
                                                $plazoBadgeHtml .= '<br><span style="font-size:10px; color:#6b7280; font-weight:bold; margin-top:3px; display:inline-block;">Plazo orig: ' . $act->fecha_display . '</span>';
                                            }
                                        } elseif (!$tienePlazoVal || (empty($act->hora_inicio) && empty($act->hora_fin) && empty($act->fecha_estimada_fin) && $tipoKey !== 'rutinaria')) {
                                            $plazoBadgeHtml = '<span style="color:#64748b; font-style:italic;">Sin plazo</span>';
                                        } elseif ($tipoKey === 'rutinaria') {
                                            $plazoBadgeHtml = '<span style="color:#1e40af; font-weight:700;">Diaria</span>';
                                        } else {
                                            $fechaFinStr = $act->fecha_estimada_fin ?? $act->fecha_inicio ?? $ahora->format('Y-m-d');
                                            $horaFinStr = $act->hora_fin ?? '23:59:59';
                                            try {
                                                $deadline = \Carbon\Carbon::parse($fechaFinStr . ' ' . $horaFinStr);
                                            } catch (\Exception $e) {
                                                $deadline = $ahora->copy()->addDays(1);
                                            }

                                            if ($ahora->greaterThan($deadline)) {
                                                $rowBg = '#fef2f2';
                                                $rowBorder = '#dc2626';
                                                $plazoBadgeHtml = '<span style="background:#fee2e2; color:#dc2626; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1.5px solid #fca5a5; display:inline-flex; align-items:center; gap:4px;" title="Actividad Atrasada / Plazo Expirado"><i class="bi bi-exclamation-triangle-fill"></i> VENCIDA</span>';
                                            } elseif ($ahora->diffInHours($deadline, false) <= 24) {
                                                $rowBg = '#fffbeb';
                                                $rowBorder = '#d97706';
                                                $plazoBadgeHtml = '<span style="background:#fef3c7; color:#d97706; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1.5px solid #fcd34d; display:inline-flex; align-items:center; gap:4px;" title="Plazo Próximo a Vencer"><i class="bi bi-clock-fill"></i> POR VENCER</span>';
                                            } else {
                                                $plazoBadgeHtml = '<span style="color:#1e293b; font-weight:600;">' . $fechaDisplay . '</span>';
                                            }
                                        }
                                    @endphp
                                    <tr class="tbl-row-gen" style="border-bottom:1px solid #e2e8f0; border-left:4px solid {{ $rowBorder }}; background:{{ $rowBg }}; cursor:pointer;"
                                        onclick="openShowModalFromRow(this)"
                                        data-tipo="{{ $tipoKey }}"
                                        data-id="{{ $act->id }}"
                                        data-titulo="{{ $act->titulo }}"
                                        data-descripcion="{{ $descText }}"
                                        data-estado="{{ strtolower($act->estado) }}"
                                        data-prioridad="{{ strtolower($act->prioridad ?? 'media') }}"
                                        data-area="{{ $empAreaId }}"
                                        data-employee="{{ $empId }}"
                                        data-seguimiento="{{ $esSeguimiento ? 'true' : 'false' }}"
                                        data-fechadisplay="{{ $fechaDisplay }}"
                                        data-empleadonombre="{{ $empNombre }}"
                                        data-creadornombre="{{ $creadorNombre }}"
                                        data-avance="{{ $porcentaje }}"
                                        data-motivo="{{ $act->motivo ?? '' }}"
                                        data-resultado="{{ $act->resultado_obtenido ?? '' }}"
                                        data-permitiravance="{{ $act->permitir_registro_avance ?? 1 }}"
                                        data-veces="{{ $act->veces_al_dia ?? 1 }}"
                                        data-ejecuciones="{{ $act->ejecuciones_hoy ?? 0 }}"
                                        data-depresp="{{ $act->dependencia_responsable ?? '' }}"
                                        data-depmotivo="{{ $act->dependencia_motivo ?? '' }}"
                                        data-atrasada="{{ (($act->tiene_plazo ?? 'si') !== 'no' && $act->fecha_estimada_fin && \Carbon\Carbon::parse($act->fecha_estimada_fin)->isPast() && !in_array($act->estado, ['finalizada'])) ? 'true' : 'false' }}"
                                        data-historial="{{ json_encode($act->historial_avances_list ?? []) }}">
                                        <td style="padding:6px 10px;">
                                            <span style="font-weight:700; font-size:13px; color:#1e293b; display:inline-block;">{{ $act->titulo }}</span>
                                        </td>
                                        <td style="padding:6px 10px; text-align:center;">
                                            @php
                                                $colors = ['bg' => '#f1f5f9', 'text' => '#475569'];
                                                $prioridadTexto = 'Sin prioridad';
                                                if (!empty($act->prioridad) && $act->prioridad !== 'sin_prioridad') {
                                                    $prioColors = [
                                                        'baja' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                                        'media' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                                        'alta' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                                        'urgente' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                                                    ];
                                                    $colors = $prioColors[strtolower($act->prioridad)] ?? $colors;
                                                    $prioridadTexto = ucfirst($act->prioridad);
                                                }
                                            @endphp
                                            <span style="background:{{ $colors['bg'] }}; color:{{ $colors['text'] }}; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:bold; text-transform:uppercase;">
                                                {{ $prioridadTexto }}
                                            </span>
                                        </td>
                                        <td style="padding:6px 10px; width:14%;">
                                            <div style="display:flex; align-items:center; gap:6px;">
                                                <div style="background:#e2e8f0; border-radius:4px; height:6px; flex:1; overflow:hidden;">
                                                    <div class="progreso-bar-val" style="background:#22c55e; width:{{ $porcentaje }}%; height:100%;"></div>
                                                </div>
                                                <span class="progreso-txt-val" style="font-size:11px; font-weight:bold; color:#475569; width:32px; text-align:right;">{{ $porcentaje }}%</span>
                                            </div>
                                            <span class="estado-badge-val" style="font-size:10px; color: {{ $act->estado === 'finalizada' ? '#10b981' : ($act->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }}; font-weight:bold; display:block; margin-top:2px;">
                                                {{ ucfirst(str_replace('_', ' ', $act->estado)) }}
                                            </span>
                                        </td>
                                        <td style="padding:6px 10px; font-size:12px;">
                                            @if(!empty($act->dependencia_responsable) || !empty($act->dependencia_motivo))
                                                <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 4px;">
                                                    <i class="bi bi-person-fill" style="color: #2563eb; font-size: 11px;"></i>
                                                    {{ $act->dependencia_responsable ?? 'N/A' }}
                                                </div>
                                                @if(!empty($act->dependencia_motivo))
                                                    <div style="font-size: 11px; color: #64748b; font-style: italic; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $act->dependencia_motivo }}">
                                                        {{ $act->dependencia_motivo }}
                                                    </div>
                                                @endif
                                            @else
                                                <span style="color:#94a3b8; font-style:italic;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding:6px 10px; font-size:12px; color:#475569; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $descText }}">
                                            {{ $descText }}
                                        </td>
                                        <td style="padding:6px 10px; font-size:12px; color:#475569;">
                                            {!! $plazoBadgeHtml !!}
                                        </td>
                                        <td style="padding:6px 10px; text-align:center;" onclick="event.stopPropagation();">
                                            <div style="display:flex; justify-content:flex-end; align-items:center; gap:5px; width:100%;">
                                                @if(!$esFinalizada)
                                                    <button type="button" class="btn-ver" style="background:#8b5cf6; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                            onclick="openSeguimientoModalFromRow(this, event)"
                                                            title="Dar Seguimiento">
                                                        <i class="bi bi-calendar2-check-fill" style="font-size:12px;"></i>
                                                    </button>
                                                @endif
                                                @if($puedeAvance && $porcentaje < 100)
                                                    <button type="button" class="btn-ver" style="background:#c2410c; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                            onclick="openAvanceGenericoModal(this, event)"
                                                            data-tipo="{{ $tipoKey }}"
                                                            data-id="{{ $act->id }}"
                                                            data-titulo="{{ $act->titulo }}"
                                                            data-avance="{{ $porcentaje }}"
                                                            data-veces="{{ $act->veces_al_dia ?? 1 }}"
                                                            data-ejecuciones="{{ $act->ejecuciones_hoy ?? 0 }}"
                                                            title="Registrar Avance / Progreso y Nota">
                                                        <i class="bi bi-graph-up-arrow" style="font-size:12px;"></i>
                                                    </button>
                                                @endif

                                                @if($isBossOrAdmin)
                                                    <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                            onclick="openEditModalFromRow(this, event)"
                                                            data-tipo="{{ $tipoKey }}"
                                                            data-id="{{ $act->id }}"
                                                            data-titulo="{{ $act->titulo }}"
                                                            data-descripcion="{{ $descText }}"
                                                            data-estado="{{ $act->estado }}"
                                                            data-prioridad="{{ $act->prioridad ?? 'media' }}"
                                                            data-veces="{{ $act->veces_al_dia ?? 1 }}"
                                                            data-motivo="{{ $act->motivo ?? '' }}"
                                                            data-resultado="{{ $act->resultado_obtenido ?? '' }}"
                                                            data-avance="{{ $porcentaje }}"
                                                            data-empleado="{{ $act->empleado_id }}"
                                                            data-dirigido="{{ $act->dirigido_a_id ?? '' }}"
                                                            data-horainicio="{{ $act->hora_inicio ?? '' }}"
                                                            data-horafin="{{ $act->hora_fin ?? '' }}"
                                                            data-deparea="{{ $act->dependencia_area ?? '' }}"
                                                            data-depresp="{{ $act->dependencia_responsable ?? '' }}"
                                                            data-depmotivo="{{ $act->dependencia_motivo ?? '' }}"
                                                            data-acciones="{{ $act->acciones_realizadas ?? '' }}"
                                                            data-observaciones="{{ $act->observaciones ?? '' }}"
                                                            data-modalidad="{{ $act->modalidad ?? 'un_dia' }}"
                                                            data-fechainicio="{{ $act->fecha_inicio ?? '' }}"
                                                            data-fechafin="{{ $act->fecha_estimada_fin ?? '' }}"
                                                            data-horas="{{ $act->horas_invertidas ?? '' }}"
                                                            data-permitiravance="{{ $act->permitir_registro_avance ?? 0 }}"
                                                            title="Editar Actividad">
                                                        <i class="bi bi-pencil" style="font-size:12px;"></i>
                                                    </button>
                                                    <button type="button" class="btn-ver" style="background:#ef4444; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                            onclick="confirmarEliminarActividad('{{ $destroyRoute }}', event)"
                                                            title="Eliminar Actividad">
                                                        <i class="bi bi-trash" style="font-size:12px;"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:40px; color:#64748b; font-style:italic; background:#ffffff; border-radius:12px; border:1px solid #e2e8f0;">
            No hay actividades registradas en esta fecha.
        </div>
    @endforelse
</div>

<!-- MODAL PARA DESCARGA PDF CON OBSERVACIONES -->
<div id="modalDescargaPDF" class="rh-modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="rh-modal-content" style="max-width: 500px; background: white; border: 3px solid #0284c7; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); padding: 22px;">
        <span class="rh-modal-close" onclick="cerrarModalPDF()" style="float: right; font-size: 24px; font-weight: bold; cursor: pointer; color: #64748b;">&times;</span>
        
        <div style="margin-bottom: 18px;">
            <h2 style="margin: 0 0 6px 0; color: #0284c7; font-size: 19px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-file-earmark-pdf-fill"></i> Descargar Reporte
            </h2>
            <p style="margin: 0; font-size: 13px; color: #475569; font-weight: 700;">Ingresa las observaciones para este PDF</p>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="font-weight: 800; font-size: 13px; color: #0f172a; display: block; margin-bottom: 6px;">Observaciones *</label>
            <textarea id="observaciones_pdf_input" rows="4" style="width: 100%; padding: 10px; border: 2px solid #0284c7; border-radius: 8px; font-family: inherit; font-weight: 500; color: #1e293b; resize: vertical;" placeholder="Observaciones: Reporte oficial..."></textarea>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button type="button" onclick="cerrarModalPDF()" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; margin-right: 10px;">
                Cancelar
            </button>
            <button type="button" onclick="generarPDF()" style="background: #0284c7; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer;">
                <i class="bi bi-download me-1"></i> Generar PDF
            </button>
        </div>
    </div>
</div>

<script>
    let urlDescargaPDF = '';

    function abrirModalDescargaPDF(url) {
        urlDescargaPDF = url;
        let modal = document.getElementById('modalDescargaPDF');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('observaciones_pdf_input').value = '';
            document.getElementById('observaciones_pdf_input').focus();
        }
    }

    function cerrarModalPDF() {
        let modal = document.getElementById('modalDescargaPDF');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    let generandoPDF = false;
    function generarPDF() {
        if (generandoPDF) return;
        let observaciones = document.getElementById('observaciones_pdf_input').value;
        if (!observaciones.trim()) {
            alert('Por favor ingresa las observaciones.');
            return;
        }

        generandoPDF = true;
        let btn = document.querySelector('#modalDescargaPDF button[onclick="generarPDF()"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generando...';
        }

        let finalUrl = urlDescargaPDF + (urlDescargaPDF.includes('?') ? '&' : '?') + 'observaciones_pdf=' + encodeURIComponent(observaciones);
        window.open(finalUrl, '_blank');
        cerrarModalPDF();

        setTimeout(() => {
            generandoPDF = false;
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-download me-1"></i> Generar PDF';
            }
        }, 3000);
    }

    function filterActivities() {
        let query = (document.getElementById('search-query')?.value || '').toLowerCase();
        let type  = (document.getElementById('filter-type')?.value || '').toLowerCase();
        let prio  = (document.getElementById('filter-priority')?.value || '').toLowerCase();
        let status= (document.getElementById('filter-status')?.value || '').toLowerCase();
        let area  = document.getElementById('filter-area')?.value || '';
        let emp   = document.getElementById('filter-employee')?.value || '';
        let seg   = (document.getElementById('filter-seguimiento')?.value || '').toLowerCase();

        document.querySelectorAll('.card-user-group').forEach(groupCard => {
            let visibleRowsInGroup = 0;

            groupCard.querySelectorAll('.tbl-row-gen').forEach(row => {
                let text = row.innerText.toLowerCase();
                let rowTipo = (row.dataset.tipo || '').toLowerCase();
                let rowPrio = (row.dataset.prioridad || '').toLowerCase();
                let rowEstado = (row.dataset.estado || '').toLowerCase();
                let rowArea = row.dataset.area || '';
                let rowEmp = row.dataset.employee || '';
                let isAtrasada = row.dataset.atrasada === 'true';
                let rowSeg = (row.dataset.seguimiento || 'false').toLowerCase() === 'true' ? 'si' : 'no';

                let match = true;
                if (query && !text.includes(query)) match = false;
                if (type && rowTipo !== type) match = false;
                if (prio) {
                    if (prio === 'sin_prioridad') {
                        if (rowPrio !== '' && rowPrio !== 'null' && rowPrio !== 'sin_prioridad') match = false;
                    } else if (rowPrio !== prio) {
                        match = false;
                    }
                }
                if (status) {
                    if (status === 'atrasada') {
                        if (!isAtrasada && rowEstado !== 'atrasada') match = false;
                    } else if (rowEstado !== status) {
                        match = false;
                    }
                }
                if (area && rowArea !== area) match = false;
                if (emp && rowEmp !== emp) match = false;
                if (seg && rowSeg !== seg) match = false;

                row.style.display = match ? '' : 'none';
                if (match) {
                    visibleRowsInGroup++;
                }
            });

            // Hide the entire group card if no rows inside match the filter
            groupCard.style.display = visibleRowsInGroup > 0 ? '' : 'none';
        });
    }

    // Defensive accordion toggle script
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.card-header button[data-bs-toggle="collapse"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let targetId = this.getAttribute('data-bs-target');
                let targetEl = document.querySelector(targetId);
                if (targetEl) {
                    if (targetEl.classList.contains('show')) {
                        targetEl.classList.remove('show');
                        this.querySelector('.bi-chevron-down')?.classList.replace('bi-chevron-down', 'bi-chevron-right');
                    } else {
                        targetEl.classList.add('show');
                        this.querySelector('.bi-chevron-right')?.classList.replace('bi-chevron-right', 'bi-chevron-down');
                    }
                }
            });
        });
    });
</script>
@endsection
