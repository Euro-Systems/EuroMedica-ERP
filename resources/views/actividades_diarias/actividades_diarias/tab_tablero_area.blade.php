<!-- DIARIAS PARCIAL -->
<div class="area-dashboard-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
                <i class="bi bi-calendar-check me-2" style="color:#3b82f6;"></i>Actividades Diarias: {{ $area->nombre }}
            </h2>
            <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
                Monitoreo y asignación de tareas a los empleados de esta área · {{ now()->format('d/m/Y') }}
            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" onclick="abrirModalCrearActividad('asignada')" class="btn-ver" style="background:#22c55e; color:white; border:none; padding:8px 14px; border-radius:8px; font-weight:bold; font-size:12px; display:flex; align-items:center; gap:6px; cursor:pointer;">
                <i class="bi bi-plus-lg"></i> Asignar Actividad
            </button>
            <button type="button" onclick="abrirModalCrearActividad('imprevista')" class="btn-ver" style="background:#ea580c; color:white; border:none; padding:8px 14px; border-radius:8px; font-weight:bold; font-size:12px; display:flex; align-items:center; gap:6px; cursor:pointer;">
                <i class="bi bi-person-fill"></i> Actividad Personal
            </button>
            <button type="button" onclick="abrirModalCrearActividad('rutinaria')" class="btn-ver" style="background:#3b82f6; color:white; border:none; padding:8px 14px; border-radius:8px; font-weight:bold; font-size:12px; display:flex; align-items:center; gap:6px; cursor:pointer;">
                <i class="bi bi-arrow-repeat"></i> Crear Rutina
            </button>
        </div>
    </div>
    
    @forelse($area->users as $emp)
        <div class="rh-card" style="margin-bottom: 12px; border-left: 3px solid #3b82f6; padding: 10px 14px; background: #fafcff; box-shadow: 0 2px 4px rgba(0,0,0,0.03); border-radius: 8px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid #e2e8f0; padding-bottom:8px; flex-wrap:wrap; gap:8px;">
                <div>
                    <h4 style="margin:0; color:#1e293b; font-size:14px; font-weight:bold;"><i class="bi bi-person-circle me-2 text-primary" style="font-size:13px;"></i>{{ (auth()->user() && $emp->id === auth()->id()) ? 'YO' : $emp->name }}</h4>
                    <span style="font-size:11px; color:#64748b; font-weight:500;">Rol: <span style="text-transform:capitalize;">{{ $emp->rol }}</span> | Email: {{ $emp->email }}</span>
                </div>
                <div style="display:flex; gap:6px; align-items:center;">
                    <button type="button" class="btn-ver" style="background:#22c55e; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalCrearActividad('asignada'); document.getElementById('crear_empleado_id').value={{ $emp->id }};" title="Asignar Actividad">
                        <i class="bi bi-journal-text" style="font-size:14px; color:white;"></i>
                    </button>
                    <button type="button" class="btn-ver" style="background:#ea580c; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalCrearActividad('imprevista'); document.getElementById('crear_empleado_id').value={{ $emp->id }};" title="Actividad Personal">
                        <i class="bi bi-person-fill" style="font-size:13px; color:white;"></i>
                    </button>
                    <button type="button" class="btn-ver" style="background:#3b82f6; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalCrearActividad('rutinaria'); document.getElementById('crear_empleado_id').value={{ $emp->id }};" title="Crear Rutina">
                        <i class="bi bi-arrow-repeat" style="font-size:13px; color:white;"></i>
                    </button>
                </div>
            </div>

            @php
                $empActividades = $emp->actividades;
            @endphp

            @if($empActividades->count() > 0)
                <table class="rh-table" style="font-size:12px; margin:0; width:100%;">
                    <thead>
                        <tr style="background:#1e3a8a; color:white;">
                            <th style="padding:6px 10px; border-radius:6px 0 0 6px; font-size:11px; font-weight:700;">Actividad</th>
                            <th style="padding:6px 10px; font-size:11px; font-weight:700;">Dependencia</th>
                            <th style="padding:6px 10px; font-size:11px; font-weight:700;">Descripción</th>
                            <th style="padding:6px 10px; font-size:11px; font-weight:700;">Fecha Estimada</th>
                            <th style="padding:6px 10px; font-size:11px; font-weight:700;">Estado</th>
                            <th style="padding:6px 10px; border-radius:0 6px 6px 0; text-align:center; font-size:11px; font-weight:700;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($empActividades as $actividad)
                        @php
                           $borderColor = '#e2e8f0'; 
                           if ($actividad->estado === 'finalizada') $borderColor = '#10b981'; 
                           elseif ($actividad->estado === 'pendiente') $borderColor = '#facc15'; 
                           elseif ($actividad->estado === 'en_pausa') $borderColor = '#f97316'; 
                           elseif ($actividad->estado === 'atrasada') $borderColor = '#ef4444';
                           
                           $tipoKey = strtolower($actividad->tipo ?? 'asignada');
                           if ($tipoKey === 'imprevista') {
                               $destroyRoute = route('actividades-imprevistas.destroy', $actividad->id);
                               $descText = $actividad->descripcion_detallada ?? $actividad->descripcion;
                           } elseif ($tipoKey === 'rutinaria') {
                               $destroyRoute = route('rutinas.destroy', $actividad->id);
                               $descText = $actividad->descripcion;
                           } else {
                               $destroyRoute = route('actividades.destroy', $actividad->id);
                               $descText = $actividad->descripcion;
                           }
                        @endphp
                        <tr style="border-left:3px solid {{ $borderColor }};" class="tr-hover tbl-row-gen">
                            <td style="padding:5px 10px; font-weight:600;">
                                {{ $actividad->titulo }}
                                @if($tipoKey === 'rutinaria')
                                    <span style="background:#2563eb; color:#ffffff; font-size:9px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:800; display:inline-flex; align-items:center; gap:3px; box-shadow:0 1px 3px rgba(37,99,235,0.3);"><i class="bi bi-arrow-repeat"></i> Rutina</span>
                                @elseif($tipoKey === 'imprevista')
                                    <span style="background:#ea580c; color:#ffffff; font-size:9px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:800; display:inline-flex; align-items:center; gap:3px; box-shadow:0 1px 3px rgba(234,88,12,0.3);"><i class="bi bi-person-fill"></i> Personal</span>
                                @else
                                    <span style="background:#16a34a; color:#ffffff; font-size:9px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:800; display:inline-flex; align-items:center; gap:3px; box-shadow:0 1px 3px rgba(22,163,74,0.3);"><i class="bi bi-check2-circle"></i> Asignada</span>
                                @endif
                            </td>
                            <td style="padding:5px 10px; font-size:11px;">
                                @if(!empty($actividad->dependencia_responsable) || !empty($actividad->dependencia_motivo))
                                    <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-person-fill" style="color: #2563eb; font-size: 11px;"></i>
                                        {{ $actividad->dependencia_responsable ?? 'N/A' }}
                                    </div>
                                    @if(!empty($actividad->dependencia_motivo))
                                        <div style="font-size: 10px; color: #64748b; font-style: italic; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $actividad->dependencia_motivo }}">
                                            {{ $actividad->dependencia_motivo }}
                                        </div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">-</span>
                                @endif
                            </td>
                            <td style="padding:5px 10px; color:#475569; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $descText }}">{{ $descText }}</td>
                            <td style="padding:5px 10px; color:#475569;">
                                @php
                                    $ahora = \Carbon\Carbon::now();
                                    $esFinalizada = ($actividad->estado === 'finalizada');
                                    $tienePlazoVal = ($actividad->tiene_plazo ?? 'si') !== 'no';

                                    $plazoBadgeHtmlTabular = '';
                                    if ($esFinalizada) {
                                        $plazoBadgeHtmlTabular = '<span style="background:#dcfce7; color:#166534; font-size:10px; font-weight:800; padding:2px 6px; border-radius:8px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:3px;"><i class="bi bi-check-circle-fill"></i> Finalizada</span>';
                                    } elseif (!$tienePlazoVal || (empty($actividad->hora_inicio) && empty($actividad->hora_fin) && empty($actividad->fecha_estimada_fin) && $tipoKey !== 'rutinaria')) {
                                        $plazoBadgeHtmlTabular = '<span style="color:#64748b; font-style:italic;">Sin plazo</span>';
                                    } elseif ($tipoKey === 'rutinaria') {
                                        $plazoBadgeHtmlTabular = '<span style="color:#1e40af; font-weight:700;">Diaria</span>';
                                    } else {
                                        $fechaFinStr = $actividad->fecha_estimada_fin ?? $actividad->fecha_inicio ?? $ahora->format('Y-m-d');
                                        $horaFinStr = $actividad->hora_fin ?? '23:59:59';
                                        try {
                                            $deadline = \Carbon\Carbon::parse($fechaFinStr . ' ' . $horaFinStr);
                                        } catch (\Exception $e) {
                                            $deadline = $ahora->copy()->addDays(1);
                                        }

                                        $fDisplay = $actividad->fecha_estimada_fin ? \Carbon\Carbon::parse($actividad->fecha_estimada_fin)->format('d/m/Y') : 'Hoy';

                                        if ($ahora->greaterThan($deadline)) {
                                            $borderColor = '#dc2626';
                                            $plazoBadgeHtmlTabular = '<span style="background:#fee2e2; color:#dc2626; font-size:10px; font-weight:800; padding:2px 6px; border-radius:8px; border:1px solid #fca5a5; display:inline-flex; align-items:center; gap:3px;" title="Actividad Atrasada / Vencida"><i class="bi bi-exclamation-triangle-fill"></i> VENCIDA</span>';
                                        } elseif ($ahora->diffInHours($deadline, false) <= 24) {
                                            $borderColor = '#d97706';
                                            $plazoBadgeHtmlTabular = '<span style="background:#fef3c7; color:#d97706; font-size:10px; font-weight:800; padding:2px 6px; border-radius:8px; border:1px solid #fcd34d; display:inline-flex; align-items:center; gap:3px;" title="Plazo Próximo a Vencer"><i class="bi bi-clock-fill"></i> POR VENCER</span>';
                                        } else {
                                            $plazoBadgeHtmlTabular = '<span style="color:#1e293b; font-weight:600;">' . $fDisplay . '</span>';
                                        }
                                    }
                                @endphp
                                {!! $plazoBadgeHtmlTabular !!}
                            </td>
                            <td style="padding:5px 10px;">
                                <span class="estado-badge-val" style="font-weight:bold; font-size:11px; color: {{ $actividad->estado === 'finalizada' ? '#166534' : ($actividad->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }};">
                                    {{ ucfirst(str_replace('_', ' ', $actividad->estado)) }} ({{ $actividad->porcentaje_avance ?? 0 }}%)
                                </span>
                            </td>
                            <td style="padding:5px 10px; text-align:center;" onclick="event.stopPropagation();">
                                <div style="display:flex; justify-content:flex-end; align-items:center; gap:4px; width:100%;">
                                    <button type="button" class="btn-ver" style="background:#c2410c; color:white; border:none; width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                            onclick="openAvanceGenericoModal(this, event)"
                                            data-tipo="{{ $tipoKey }}"
                                            data-id="{{ $actividad->id }}"
                                            data-titulo="{{ $actividad->titulo }}"
                                            data-avance="{{ $actividad->porcentaje_avance ?? 0 }}"
                                            data-veces="{{ $actividad->veces_al_dia ?? 1 }}"
                                            data-ejecuciones="{{ $actividad->ejecuciones_hoy ?? 0 }}"
                                            title="Registrar Avance / Progreso y Nota">
                                        <i class="bi bi-graph-up-arrow" style="font-size:12px;"></i>
                                    </button>
                                    <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                onclick="openEditModalFromRow(this)"
                                                data-tipo="{{ $tipoKey }}"
                                                data-id="{{ $actividad->id }}"
                                                data-titulo="{{ $actividad->titulo }}"
                                                data-descripcion="{{ $descText }}"
                                                data-estado="{{ $actividad->estado }}"
                                                data-prioridad="{{ $actividad->prioridad ?? 'media' }}"
                                                data-veces="{{ $actividad->veces_al_dia ?? 1 }}"
                                                data-motivo="{{ $actividad->motivo ?? '' }}"
                                                data-resultado="{{ $actividad->resultado_obtenido ?? '' }}"
                                                data-empleado="{{ $actividad->empleado_id }}"
                                                data-dirigido="{{ $actividad->dirigido_a_id ?? '' }}"
                                                data-horainicio="{{ $actividad->hora_inicio ?? '' }}"
                                                data-horafin="{{ $actividad->hora_fin ?? '' }}"
                                                data-deparea="{{ $actividad->dependencia_area ?? '' }}"
                                                data-depresp="{{ $actividad->dependencia_responsable ?? '' }}"
                                                data-depmotivo="{{ $actividad->dependencia_motivo ?? '' }}"
                                                data-acciones="{{ $actividad->acciones_realizadas ?? '' }}"
                                                data-observaciones="{{ $actividad->observaciones ?? '' }}"
                                                data-modalidad="{{ $actividad->modalidad ?? 'un_dia' }}"
                                                data-fechainicio="{{ $actividad->fecha_inicio ?? '' }}"
                                                data-fechafin="{{ $actividad->fecha_estimada_fin ?? '' }}"
                                                data-horas="{{ $actividad->horas_invertidas ?? '' }}"
                                                data-permitiravance="{{ $actividad->permitir_registro_avance ?? 0 }}"
                                                title="Editar Actividad">
                                            <i class="bi bi-pencil" style="font-size:12px;"></i>
                                        </button>
                                        <button type="button" class="btn-ver" style="background:#ef4444; color:white; border:none; width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;"
                                                onclick="confirmarEliminarActividad('{{ $destroyRoute }}', event)" title="Eliminar Actividad">
                                            <i class="bi bi-trash" style="font-size:12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:15px; color:#94a3b8; font-size:13px; font-style:italic; background:#f8fafc; border-radius:6px;">
                    Sin actividades asignadas el día de hoy
                </div>
            @endif
        </div>
    @empty
        <div style="text-align:center; padding:40px; color:#64748b; font-size:14px; background:white; border-radius:10px;">
            No hay empleados en esta área.
        </div>
    @endforelse
</div>
