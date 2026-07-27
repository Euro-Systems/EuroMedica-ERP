<!-- DIARIAS PARCIAL -->
<div class="area-dashboard-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
                <i class="bi bi-calendar-check me-2" style="color:#3b82f6;"></i>Actividades Diarias: {{ $area->nombre }}
            </h2>
            <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
                Monitoreo y asignación de tareas a los empleados de esta área · {{ now()->format('d/m/Y') }}
            </p>
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
                    <!-- Asignar Actividad (Tabla de notas) -->
                    <button type="button" class="btn-ver" style="background:#22c55e; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalConEmpleado('modalNueva', {{ $emp->id }})" title="Asignar Actividad (Tabla de notas)">
                        <i class="bi bi-journal-text" style="font-size:14px; color:white;"></i>
                    </button>
                    
                    <!-- Actividad Imprevista (Uno de alerta) -->
                    <button type="button" class="btn-ver" style="background:#f59e0b; border:none; width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalConEmpleado('modalNuevaImprevista', {{ $emp->id }})" title="Actividad Imprevista (Alerta)">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:13px; color:white;"></i>
                    </button>
                    
                    <!-- Actividad Rutinaria (Dia y noche) -->
                    <button type="button" class="btn-ver" style="background:#3b82f6; border:none; width:34px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; gap:2px; border-radius:6px; cursor:pointer;" onclick="abrirModalConEmpleado('modalNuevaRutina', {{ $emp->id }})" title="Actividad Rutinaria (Día y Noche)">
                        <i class="bi bi-sun-fill" style="font-size:11px; color:white;"></i>
                        <i class="bi bi-moon-stars-fill" style="font-size:10px; color:white;"></i>
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
                           
                           $isImprevista = ($actividad->tipo === 'Imprevista');
                           $isRutinaria = ($actividad->tipo === 'Rutinaria');
                           
                           if ($isImprevista) {
                               $rowClick = "window.location.href='" . route('actividades-imprevistas.show', $actividad->id) . "'";
                           } elseif ($isRutinaria) {
                               $rowClick = "openEditRutinaModal(this)";
                           } else {
                               $rowClick = "openShowModal(this)";
                           }
                        @endphp
                        <tr onclick="{{ $rowClick }}" 
                            data-id="{{ $actividad->id }}" 
                            data-rutina="{!! $isRutinaria ? base64_encode(json_encode($actividad)) : '' !!}"
                            data-actividad="{!! !$isRutinaria ? base64_encode(json_encode($actividad)) : '' !!}" 
                            data-area="{{ $actividad->area_id ?? 1 }}" 
                            style="cursor:pointer; border-left:3px solid {{ $borderColor }};" 
                            class="tr-hover tbl-row-gen">
                            <td style="padding:5px 10px; font-weight:600;">
                                {{ $actividad->titulo }}
                                @if($isRutinaria)
                                    <span style="background:#dbeafe; color:#1e40af; font-size:9px; padding:1px 4px; border-radius:6px; margin-left:4px; font-weight:bold;">Rutina</span>
                                @elseif($isImprevista)
                                    <span style="background:#fee2e2; color:#991b1b; font-size:9px; padding:1px 4px; border-radius:6px; margin-left:4px; font-weight:bold;">Imprevista</span>
                                @endif
                            </td>
                            <td style="padding:5px 10px; color:#475569; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $actividad->descripcion }}">{{ $actividad->descripcion }}</td>
                            <td style="padding:5px 10px; color:#475569;">
                                {{ $isRutinaria ? 'Diaria' : ($actividad->fecha_estimada_fin ? \Carbon\Carbon::parse($actividad->fecha_estimada_fin)->format('d/m/Y') : 'N/A') }}
                            </td>
                            <td style="padding:5px 10px;">
                                <span class="estado-badge-val" style="font-weight:bold; font-size:11px; color: {{ $actividad->estado === 'finalizada' ? '#166534' : ($actividad->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }};">
                                    {{ ucfirst(str_replace('_', ' ', $actividad->estado)) }} ({{ $actividad->porcentaje_avance ?? 0 }}%)
                                </span>
                            </td>
                            <td style="padding:5px 10px; text-align:center;" onclick="event.stopPropagation();">
                                <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                                    @if($isRutinaria)
                                        <div style="display:flex; gap:4px; align-items:center; background:#f1f5f9; padding:2px 5px; border-radius:6px; border:1px solid #cbd5e1;">
                                            @for($i = 1; $i <= $actividad->veces_al_dia; $i++)
                                                <input type="checkbox" 
                                                       class="rutina-check-box" 
                                                       data-id="{{ $actividad->id }}" 
                                                       value="{{ $i }}" 
                                                       {{ $i <= $actividad->ejecuciones_hoy ? 'checked' : '' }} 
                                                       onclick="handleRutinaCheck(this, event)"
                                                       style="width: 13px; height: 13px; cursor: pointer; accent-color: #2563eb;"
                                                       title="Ejecución {{ $i }} de {{ $actividad->veces_al_dia }}">
                                            @endfor
                                        </div>
                                        @if(in_array(auth()->user()->rol, ['jefe', 'directivo', 'admin']) || $actividad->empleado_id === auth()->id() || auth()->user()->hasPermission('actividades'))
                                            <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" onclick="event.stopPropagation(); openEditRutinaModal(this)" data-rutina="{!! base64_encode(json_encode($actividad)) !!}" title="Editar Rutina">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('rutinas.destroy', $actividad->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Seguro que deseas eliminar esta rutina definitivamente?');" onclick="event.stopPropagation();">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" title="Eliminar Rutina" onclick="event.stopPropagation();">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                     @elseif($isImprevista)
                                         @if(auth()->check() && (in_array(auth()->user()->rol, ['jefe', 'directivo', 'admin']) || $actividad->empleado_id === auth()->id() || auth()->user()->hasPermission('actividades')))
                                             <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" onclick="event.stopPropagation(); openEditImprevistaModal(this)" data-imprevisto="{!! base64_encode(json_encode($actividad)) !!}" title="Editar Imprevisto">
                                                 <i class="bi bi-pencil"></i>
                                             </button>
                                             <form action="{{ route('actividades-imprevistas.destroy', $actividad->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Seguro que deseas eliminar este imprevisto definitivamente?');" onclick="event.stopPropagation();">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" title="Eliminar Imprevisto" onclick="event.stopPropagation();">
                                                     <i class="bi bi-trash"></i>
                                                 </button>
                                             </form>
                                         @endif
                                    @else
                                        @if(auth()->check() && (in_array(auth()->user()->rol, ['jefe', 'directivo', 'admin']) || $actividad->empleado_id === auth()->id() || auth()->user()->hasPermission('actividades')))
                                            <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" onclick="event.stopPropagation(); openEditModalFromRow(this)" data-actividad="{!! base64_encode(json_encode($actividad)) !!}" title="Editar Actividad">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('actividades.destroy', $actividad->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Seguro que deseas eliminar esta actividad definitivamente?');" onclick="event.stopPropagation();">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:3px 6px; font-size:10px; border-radius:4px; cursor:pointer;" title="Eliminar Actividad" onclick="event.stopPropagation();">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#64748b; font-size:11px;">-</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:6px; padding:10px; text-align:center; color:#64748b; font-size:12px;">
                    <i class="bi bi-info-circle me-1"></i> Este empleado no tiene actividades asignadas.
                </div>
            @endif
        </div>
    @empty
        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:20px; text-align:center; color:#64748b; font-size:13px;">
            <i class="bi bi-people-fill" style="font-size:24px; display:block; margin-bottom:8px;"></i>
            No hay empleados asignados a esta área actualmente.
        </div>
    @endforelse
</div>

<script>
function toggleModalidadNueva(val) {
    let container = document.getElementById('nueva_fecha_fin_container');
    let input = document.getElementById('nueva_fecha_estimada_fin');
    if (val === 'un_dia') {
        container.style.display = 'none';
        input.removeAttribute('required');
    } else {
        container.style.display = 'block';
        input.setAttribute('required', 'required');
    }
}

function abrirModalConEmpleado(modalId, empleadoId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        let select = modal.querySelector('select[name="empleado_id"]');
        if (select) {
            select.value = empleadoId;
        } else {
            let input = modal.querySelector('input[name="empleado_id"]');
            if (input) {
                input.value = empleadoId;
            }
        }
    }
    abrirModal(modalId);
}
</script>
<!-- END AREA PARCIAL -->
