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

<!-- Filtros y Búsqueda Completos -->
<div class="rh-card" style="margin-bottom:16px; padding:14px 18px; border-radius:12px;">
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <input type="text" id="search-query" oninput="filterActivities()" placeholder="🔍 Buscar por colaborador, título o descripción..." style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; outline:none; box-sizing:border-box;">
        </div>
        <div style="width:145px;">
            <select id="filter-type" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff; font-weight:600;">
                <option value="">Todos los Tipos</option>
                <option value="asignada">🟢 Asignada</option>
                <option value="imprevista">🟠 Personales</option>
                <option value="rutinaria">🔵 Rutinaria</option>
            </select>
        </div>
        <div style="width:145px;">
            <select id="filter-priority" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff; font-weight:600;">
                <option value="">Todas las Prioridades</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
        <div style="width:145px;">
            <select id="filter-status" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff; font-weight:600;">
                <option value="">Todos los Estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="finalizada">Finalizada</option>
                <option value="atrasada">🚨 Atrasada</option>
            </select>
        </div>
        <div style="width:145px;">
            <select id="filter-area" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff;">
                <option value="">Todas las Áreas</option>
                @foreach($areas as $areaItem)
                    <option value="{{ $areaItem->id }}" {{ session('active_area_id') == $areaItem->id ? 'selected' : '' }}>{{ $areaItem->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div style="width:150px;">
            <select id="filter-employee" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff;">
                <option value="">Todos los Empleados</option>
                @foreach($empleadosRH as $emp)
                    <option value="{{ $emp['id'] ?? $emp->id }}">{{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}</option>
                @endforeach
            </select>
        </div>
        <div style="width:150px;">
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
            <select id="filter-date-backend" onchange="window.location.href='?fecha_filtro='+this.value" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; background:#fff; font-weight:600; color:#1e3a8a;">
                @foreach($diasFiltro as $df)
                    <option value="{{ $df['valor'] }}" {{ (isset($filtroFecha) && $filtroFecha == $df['valor']) ? 'selected' : '' }}>
                        {{ $df['etiqueta'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Tabla de Actividades -->
<div class="rh-card" style="box-shadow:0 4px 12px rgba(0,0,0,0.03); padding:0; overflow:hidden; border-radius:12px; border:1px solid #e2e8f0;">
    <table class="rh-table" style="margin:0; border-collapse:collapse; width:100%;">
        <thead>
            <tr style="background:#1e3a8a; color:white;">
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Actividad</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Asignado a</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none; text-align:center;">Prioridad</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Progreso</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Dependencia</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Descripción</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Plazo</th>
                <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actividades as $act)
                @php
                   $tipoKey = strtolower($act->tipo ?? 'asignada');
                   $isComida = strtolower($act->titulo ?? '') === 'hora de comida';
                   if ($isComida) continue;

                   if ($isComida) {
                       $destroyRoute = route('actividades-imprevistas.destroy', $act->id);
                       $descText = $act->descripcion_detallada ?? $act->descripcion;
                       $rowBg = '#faf5ff';
                       $rowBorder = '#8b5cf6';
                   } elseif ($tipoKey === 'imprevista') {
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
                    <td style="padding:6px 10px;">
                        <div style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 6px; padding: 3px 8px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <i class="bi bi-person-fill" style="color: #2563eb; font-size: 12px;"></i>
                            <span style="font-weight: 800; font-size: 12px; color: #1e3a8a;">
                                {{ $empNombre }}
                            </span>
                        </div>
                    </td>
                    <td style="padding:6px 10px; text-align:center;">
                        @php
                            $prioColors = [
                                'baja' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                'media' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                'alta' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'urgente' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                            ];
                            $colors = $prioColors[strtolower($act->prioridad ?? 'media')] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                        @endphp
                        <span style="background:{{ $colors['bg'] }}; color:{{ $colors['text'] }}; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:bold; text-transform:uppercase;">
                            {{ $act->prioridad ?? 'media' }}
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
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:30px; color:#64748b; font-style:italic;">
                        No hay actividades registradas en esta fecha.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function filterActivities() {
    let query = (document.getElementById('search-query')?.value || '').toLowerCase();
    let type  = (document.getElementById('filter-type')?.value || '').toLowerCase();
    let prio  = (document.getElementById('filter-priority')?.value || '').toLowerCase();
    let status= (document.getElementById('filter-status')?.value || '').toLowerCase();
    let area  = document.getElementById('filter-area')?.value || '';
    let emp   = document.getElementById('filter-employee')?.value || '';

    document.querySelectorAll('.tbl-row-gen').forEach(row => {
        let text = row.innerText.toLowerCase();
        let rowTipo = (row.dataset.tipo || '').toLowerCase();
        let rowPrio = (row.dataset.prioridad || '').toLowerCase();
        let rowEstado = (row.dataset.estado || '').toLowerCase();
        let rowArea = row.dataset.area || '';
        let rowEmp = row.dataset.employee || '';
        let isAtrasada = row.dataset.atrasada === 'true';

        let match = true;
        if (query && !text.includes(query)) match = false;
        if (type && rowTipo !== type) match = false;
        if (prio && rowPrio !== prio) match = false;
        if (status) {
            if (status === 'atrasada') {
                if (!isAtrasada && rowEstado !== 'atrasada') match = false;
            } else if (rowEstado !== status) {
                match = false;
            }
        }
        if (area && rowArea !== area) match = false;
        if (emp && rowEmp !== emp) match = false;

        row.style.display = match ? '' : 'none';
    });
}
</script>
@endsection

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

    function generarPDF() {
        let observaciones = document.getElementById('observaciones_pdf_input').value;
        if (!observaciones.trim()) {
            alert('Por favor ingresa las observaciones.');
            return;
        }

        let finalUrl = urlDescargaPDF + (urlDescargaPDF.includes('?') ? '&' : '?') + 'observaciones_pdf=' + encodeURIComponent(observaciones);
        window.open(finalUrl, '_blank');
        cerrarModalPDF();
    }
</script>
