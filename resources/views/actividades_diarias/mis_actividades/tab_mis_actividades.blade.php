@extends(request()->ajax() ? 'actividades_diarias.actividades_diarias.layout_ajax' : 'actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Mis Actividades')

@section('actividades-content')
@php
    $currentUser = auth()->user();
    $isBossOrAdmin = $currentUser && in_array($currentUser->rol, ['jefe', 'admin', 'directivo']);
@endphp

<!-- MIAS PARCIAL -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-person-check-fill me-2" style="color:#3b82f6;"></i>Mis Actividades
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Listado de tus actividades asignadas, rutinas y personales · {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <div>
        <!-- BOTÓN NEGRO PARA NUEVA ACTIVIDAD -->
        <button type="button" onclick="abrirModalCrearActividad('imprevista', true)" class="btn-ver" style="background:#0f172a; color:white; border:none; padding:10px 22px; border-radius:8px; font-weight:800; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 4px 6px rgba(0,0,0,0.15);">
            <i class="bi bi-plus-lg" style="font-size:16px;"></i>Nueva Actividad
        </button>
    </div>
</div>

<!-- Filtros y Búsqueda para Mis Actividades -->
<div class="rh-card" style="margin-bottom:16px; padding:14px 18px; border-radius:12px;">
    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <input type="text" id="search-query-mias" oninput="filterActivitiesMias()" placeholder="🔍 Buscar por título o descripción..." style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; outline:none; box-sizing:border-box;">
        </div>
        <div style="width:160px;">
            <select id="filter-type-mias" onchange="filterActivitiesMias()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff; font-weight:600;">
                <option value="">Todos los Tipos</option>
                <option value="asignada">Asignadas</option>
                <option value="imprevista">Personales</option>
                <option value="rutinaria">Rutinarias</option>
            </select>
        </div>
        <div style="width:160px;">
            <select id="filter-priority-mias" onchange="filterActivitiesMias()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff; font-weight:600;">
                <option value="">Todas las Prioridades</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
        <div style="width:160px;">
            <select id="filter-status-mias" onchange="filterActivitiesMias()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff; font-weight:600;">
                <option value="">Todos los Estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="finalizada">Finalizada</option>
                <option value="atrasada">Atrasada</option>
            </select>
        </div>
    </div>
</div>

<div class="rh-card" style="padding-top:15px;">

    <!-- HORA DE COMIDA -->
    @if(!$comidaRegistrada)
        <div class="rh-card" style="border-left:4px solid #8b5cf6; padding: 15px; margin-bottom: 25px; background: #faf5ff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                <div>
                    <h3 style="margin:0; font-size:16px; color:#6d28d9;"><i class="bi bi-cup-hot me-2"></i>Registrar Hora de Comida de Hoy</h3>
                    <p style="color:#64748b; font-size:12px; margin:5px 0 0 0;">Registra tu almuerzo reglamentario. Se computará 1 hora en tu jornada de hoy.</p>
                </div>
                <form action="{{ route('actividades.registrarComida') }}" method="POST" style="margin:0; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    @csrf
                    <div>
                        <label style="font-size:11px; color:#64748b; display:block; font-weight:bold;">Hora de Inicio:</label>
                        <input type="time" name="hora_inicio" value="{{ now()->format('H:i') }}" onchange="calcComidaFin(this.value)" style="padding:5px; border-radius:6px; border:1px solid #d1d5db; font-size:13px;" required>
                    </div>
                    <div>
                        <label style="font-size:11px; color:#64748b; display:block; font-weight:bold;">Hora de Fin (1h):</label>
                        <input type="time" id="comida_hora_fin" name="hora_fin" value="{{ now()->addHour()->format('H:i') }}" style="padding:5px; border-radius:6px; border:1px solid #d1d5db; font-size:13px; background:#f1f5f9;" readonly>
                    </div>
                    <button type="submit" class="btn-ver" style="background:#8b5cf6; padding:8px 15px; font-size:13px; margin-top:15px; color:white; border:none; border-radius:6px; font-weight:bold;"><i class="bi bi-check-circle"></i> Registrar Comida</button>
                </form>
            </div>
        </div>
        <script>
        function calcComidaFin(val) {
            if(!val) return;
            let parts = val.split(':');
            let hr = parseInt(parts[0]);
            let min = parts[1];
            let newHr = (hr + 1) % 24;
            let newHrStr = newHr < 10 ? '0' + newHr : newHr;
            document.getElementById('comida_hora_fin').value = newHrStr + ':' + min;
        }
        </script>
    @else
        <div class="rh-card" style="border-left:4px solid #10b981; padding: 15px; margin-bottom: 25px; background:#f0fdf4; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                <div>
                    <span style="color:#15803d; font-weight:bold; font-size:15px;"><i class="bi bi-check-circle-fill me-2"></i>Hora de comida registrada hoy</span>
                    <p style="color:#475569; font-size:13px; margin:5px 0 0 0;">Horario registrado: <strong>{{ \Carbon\Carbon::parse($comidaRegistrada->hora_inicio)->format('H:i') }}</strong> a <strong>{{ \Carbon\Carbon::parse($comidaRegistrada->hora_fin)->format('H:i') }}</strong></p>
                </div>
                <span style="background:#dcfce7; color:#166534; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:bold; border: 1px solid #bbf7d0;"><i class="bi bi-cup-hot me-1"></i> 1.0 hr computada</span>
            </div>
        </div>
    @endif

    <!-- TABLA DE ACTIVIDADES -->
    <div style="margin-bottom: 25px;">
        <div class="rh-card" style="box-shadow:0 4px 12px rgba(0,0,0,0.03); padding:0; overflow:hidden; border-radius:12px; border:1px solid #e2e8f0;">
            <table class="rh-table" style="margin:0; border-collapse:collapse; width:100%;">
                <thead>
                    <tr style="background:#1e3a8a; color:white;">
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none; border-radius:8px 0 0 0;">Actividad</th>
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Progreso</th>
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Dependencia</th>
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Descripción</th>
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none;">Fecha / Plazo</th>
                        <th style="padding:8px 12px; font-weight:bold; font-size:12px; border:none; text-align:center; border-radius:0 8px 0 0;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listado as $item)
                        @php
                           $tipoKey = strtolower($item->tipo ?? 'asignada');
                           $isComida = strtolower($item->titulo ?? '') === 'hora de comida';

                           if ($isComida) {
                               $destroyRoute = route('actividades-imprevistas.destroy', $item->id);
                               $descText = $item->descripcion_detallada ?? $item->descripcion;
                               $rowBg = '#faf5ff';
                               $rowBorder = '#8b5cf6';
                           } elseif ($tipoKey === 'imprevista') {
                               $destroyRoute = route('actividades-imprevistas.destroy', $item->id);
                               $descText = $item->descripcion_detallada ?? $item->descripcion;
                               $rowBg = '#fff7ed';
                               $rowBorder = '#ea580c';
                           } elseif ($tipoKey === 'rutinaria') {
                               $destroyRoute = route('rutinas.destroy', $item->id);
                               $descText = $item->descripcion;
                               $rowBg = '#eff6ff';
                               $rowBorder = '#2563eb';
                           } else {
                               $destroyRoute = route('actividades.destroy', $item->id);
                               $descText = $item->descripcion;
                               $rowBg = '#f0fdf4';
                               $rowBorder = '#16a34a';
                           }

                           $empNombre = $item->empleado->name ?? $currentUser->name ?? 'Usuario';
                           $creadorNombre = $item->creador->name ?? ($isBossOrAdmin ? 'Jefe / Admin' : 'Empleado');
                           $porcentaje = intval($item->porcentaje_avance ?? 0);
                           $puedeAvance = ($item->permitir_registro_avance ?? 1) || $isBossOrAdmin;

                           $ahora = \Carbon\Carbon::now();
                           $esFinalizada = ($item->estado === 'finalizada');
                           $tienePlazoVal = ($item->tiene_plazo ?? 'si') !== 'no';

                           $plazoBadgeHtml = '';
                           if ($esFinalizada) {
                               $plazoBadgeHtml = '<span style="background:#dcfce7; color:#166534; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-check-circle-fill"></i> Finalizada</span>';
                           } elseif (!$tienePlazoVal || $item->fecha_display === 'N/A' || (empty($item->hora_inicio) && empty($item->hora_fin) && empty($item->fecha_estimada_fin) && $tipoKey !== 'rutinaria')) {
                               $plazoBadgeHtml = '<span style="color:#64748b; font-style:italic;">Sin plazo</span>';
                           } else {
                               $fechaFinStr = $item->fecha_estimada_fin ?? $item->fecha_inicio ?? $ahora->format('Y-m-d');
                               $horaFinStr = $item->hora_fin ?? '23:59:59';
                               try {
                                   $deadline = \Carbon\Carbon::parse($fechaFinStr . ' ' . $horaFinStr);
                               } catch (\Exception $e) {
                                   $deadline = $ahora->copy()->addDays(1);
                               }

                               if ($ahora->greaterThan($deadline)) {
                                   $rowBg = '#fef2f2';
                                   $rowBorder = '#dc2626';
                                   $plazoBadgeHtml = '<span style="background:#fee2e2; color:#dc2626; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1.5px solid #fca5a5; display:inline-flex; align-items:center; gap:4px;" title="Actividad Atrasada / Plazo Expirado"><i class="bi bi-exclamation-triangle-fill"></i> VENCIDA (' . $item->fecha_display . ')</span>';
                               } elseif ($ahora->diffInHours($deadline, false) <= 24) {
                                   $rowBg = '#fffbeb';
                                   $rowBorder = '#d97706';
                                   $plazoBadgeHtml = '<span style="background:#fef3c7; color:#d97706; font-size:11px; font-weight:800; padding:3px 8px; border-radius:10px; border:1.5px solid #fcd34d; display:inline-flex; align-items:center; gap:4px;" title="Plazo Próximo a Vencer"><i class="bi bi-clock-fill"></i> POR VENCER (' . $item->fecha_display . ')</span>';
                               } else {
                                   $plazoBadgeHtml = '<span style="color:#1e293b; font-weight:600;">' . $item->fecha_display . '</span>';
                               }
                           }
                        @endphp
                        <tr class="tbl-row-mias" style="border-left:4px solid {{ $rowBorder }}; background:{{ $rowBg }}; border-bottom:1px solid #e2e8f0; cursor:pointer;"
                            onclick="openShowModalFromRow(this)"
                            data-tipo="{{ $tipoKey }}"
                            data-id="{{ $item->id }}"
                            data-titulo="{{ $item->titulo }}"
                            data-descripcion="{{ $descText }}"
                            data-estado="{{ $item->estado }}"
                            data-prioridad="{{ strtolower($item->prioridad ?? 'media') }}"
                            data-fechadisplay="{{ $item->fecha_display }}"
                            data-empleadonombre="{{ $empNombre }}"
                            data-creadornombre="{{ $creadorNombre }}"
                            data-avance="{{ $porcentaje }}"
                            data-motivo="{{ $item->motivo ?? '' }}"
                            data-resultado="{{ $item->resultado_obtenido ?? '' }}"
                            data-permitiravance="{{ $item->permitir_registro_avance ?? 1 }}"
                            data-veces="{{ $item->veces_al_dia ?? 1 }}"
                            data-ejecuciones="{{ $item->ejecuciones_hoy ?? 0 }}"
                            data-depresp="{{ $item->dependencia_responsable ?? '' }}"
                            data-depmotivo="{{ $item->dependencia_motivo ?? '' }}"
                            data-historial="{{ json_encode($item->historial_avances_list ?? []) }}">
                            <td style="padding:6px 10px;">
                                <span style="font-weight:700; font-size:13px; color:#1e293b; display:inline-block;">{{ $item->titulo }}</span>
                            </td>
                            <td style="padding:6px 10px; width:16%;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <div style="background:#e2e8f0; border-radius:4px; height:6px; flex:1; overflow:hidden;">
                                        <div class="progreso-bar-val" style="background:#22c55e; width:{{ $porcentaje }}%; height:100%; transition: width 0.3s;"></div>
                                    </div>
                                    <span class="progreso-txt-val" style="font-size:11px; font-weight:bold; color:#475569; width:32px; text-align:right;">{{ $porcentaje }}%</span>
                                </div>
                                <span class="estado-badge-val" style="font-size:10px; color: {{ $item->estado === 'finalizada' ? '#166534' : ($item->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }}; font-weight:bold; display:block; margin-top:2px;">
                                    {{ ucfirst(str_replace('_', ' ', $item->estado)) }}
                                </span>
                            </td>
                            <td style="padding:6px 10px; font-size:12px;">
                                @if(!empty($item->dependencia_responsable) || !empty($item->dependencia_motivo))
                                    <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-person-fill" style="color: #2563eb; font-size: 11px;"></i>
                                        {{ $item->dependencia_responsable ?? 'N/A' }}
                                    </div>
                                    @if(!empty($item->dependencia_motivo))
                                        <div style="font-size: 11px; color: #64748b; font-style: italic; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->dependencia_motivo }}">
                                            {{ $item->dependencia_motivo }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">-</span>
                                @endif
                            </td>
                            <td style="padding:6px 10px; font-size:12px; color:#475569; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $descText }}">
                                {{ $descText }}
                            </td>
                            <td style="padding:6px 10px; font-size:12px; color:#475569;">
                                {!! $plazoBadgeHtml !!}
                            </td>
                            <td style="padding:6px 10px; text-align:center;" onclick="event.stopPropagation();">
                                <div style="display:flex; justify-content:flex-end; align-items:center; gap:5px; width:100%;">
                                    @if($puedeAvance && $porcentaje < 100)
                                            <!-- BOTÓN DE AVANCE DE PORCENTAJE Y NOTA PARA TODAS LAS ACTIVIDADES -->
                                            <button type="button" class="btn-ver" style="background:#c2410c; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                    onclick="openAvanceGenericoModal(this, event)"
                                                    data-tipo="{{ $tipoKey }}"
                                                    data-id="{{ $item->id }}"
                                                    data-titulo="{{ $item->titulo }}"
                                                    data-avance="{{ $porcentaje }}"
                                                    data-veces="{{ $item->veces_al_dia ?? 1 }}"
                                                    data-ejecuciones="{{ $item->ejecuciones_hoy ?? 0 }}"
                                                    title="Registrar Avance / Progreso y Nota">
                                                <i class="bi bi-graph-up-arrow" style="font-size:12px;"></i>
                                            </button>
                                        @endif

                                        @if($isBossOrAdmin)
                                            <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                    onclick="openEditModalFromRow(this, event)"
                                                    data-tipo="{{ $tipoKey }}"
                                                    data-id="{{ $item->id }}"
                                                    data-titulo="{{ $item->titulo }}"
                                                    data-descripcion="{{ $descText }}"
                                                    data-estado="{{ $item->estado }}"
                                                    data-prioridad="{{ $item->prioridad ?? 'media' }}"
                                                    data-veces="{{ $item->veces_al_dia ?? 1 }}"
                                                    data-motivo="{{ $item->motivo ?? '' }}"
                                                    data-resultado="{{ $item->resultado_obtenido ?? '' }}"
                                                    data-avance="{{ $porcentaje }}"
                                                    data-empleado="{{ $item->empleado_id }}"
                                                    data-dirigido="{{ $item->dirigido_a_id ?? '' }}"
                                                    data-horainicio="{{ $item->hora_inicio ?? '' }}"
                                                    data-horafin="{{ $item->hora_fin ?? '' }}"
                                                    data-deparea="{{ $item->dependencia_area ?? '' }}"
                                                    data-depresp="{{ $item->dependencia_responsable ?? '' }}"
                                                    data-depmotivo="{{ $item->dependencia_motivo ?? '' }}"
                                                    data-acciones="{{ $item->acciones_realizadas ?? '' }}"
                                                    data-observaciones="{{ $item->observaciones ?? '' }}"
                                                    data-modalidad="{{ $item->modalidad ?? 'un_dia' }}"
                                                    data-fechainicio="{{ $item->fecha_inicio ?? '' }}"
                                                    data-fechafin="{{ $item->fecha_estimada_fin ?? '' }}"
                                                    data-horas="{{ $item->horas_invertidas ?? '' }}"
                                                    data-permitiravance="{{ $item->permitir_registro_avance ?? 0 }}"
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
                            <td colspan="5" style="text-align:center; padding:30px; color:#64748b; font-style:italic;">
                                No tienes actividades asignadas actualmente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterActivitiesMias() {
    let query = (document.getElementById('search-query-mias')?.value || '').toLowerCase();
    let type  = (document.getElementById('filter-type-mias')?.value || '').toLowerCase();
    let prio  = (document.getElementById('filter-priority-mias')?.value || '').toLowerCase();
    let status= (document.getElementById('filter-status-mias')?.value || '').toLowerCase();

    document.querySelectorAll('.tbl-row-mias').forEach(row => {
        let text = row.innerText.toLowerCase();
        let rowTipo = (row.dataset.tipo || '').toLowerCase();
        let rowPrio = (row.dataset.prioridad || '').toLowerCase();
        let rowEstado = (row.dataset.estado || '').toLowerCase();

        let match = true;
        if (query && !text.includes(query)) match = false;
        if (type && rowTipo !== type) match = false;
        if (prio && rowPrio !== prio) match = false;
        if (status && rowEstado !== status) match = false;

        row.style.display = match ? '' : 'none';
    });
}
</script>
@endsection
