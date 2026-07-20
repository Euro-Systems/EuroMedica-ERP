@extends('actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Resumen General')

@section('actividades-content')
<!-- RESUMEN PARCIAL -->
{{-- ===== ENCABEZADO GERENCIAL ===== --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-bar-chart-line-fill me-2" style="color:#3b82f6;"></i>Resumen General del Equipo
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Visión global de todas las actividades asignadas · {{ now()->format('d/m/Y') }}
        </p>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="rh-card" style="margin-bottom:16px; padding:14px 18px; border-radius:12px;">
    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <input type="text" id="search-query" oninput="filterActivities()" placeholder="🔍 Buscar por título o descripción..." style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; outline:none; box-sizing:border-box;">
        </div>
        <div style="width:170px;">
            <select id="filter-status" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff;">
                <option value="">Todos los Estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="finalizada">Finalizada</option>
                <option value="atrasada">Atrasada</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>
        <div style="width:170px;">
            <select id="filter-area" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff;">
                <option value="">Todas las Áreas</option>
                @foreach($areas as $areaItem)
                    <option value="{{ $areaItem->id }}" {{ session('active_area_id') == $areaItem->id ? 'selected' : '' }}>{{ $areaItem->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div style="width:170px;">
            <select id="filter-employee" onchange="filterActivities()" style="width:100%; padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff;">
                <option value="">Todos los Empleados</option>
                @foreach($empleadosRH as $emp)
                    <option value="{{ $emp['id'] ?? $emp->id }}">{{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}</option>
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
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Actividad</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Asignado A</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none; text-align:center;">Prioridad</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Progreso</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Descripción</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Plazo</th>
                <th style="padding:15px; font-weight:bold; font-size:14px; border:none; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actividades as $act)
                @php
                   $borderColor = '#e2e8f0'; 
                   if ($act->estado === 'finalizada') $borderColor = '#10b981'; 
                   elseif ($act->estado === 'pendiente') $borderColor = '#facc15'; 
                   elseif ($act->estado === 'en_pausa') $borderColor = '#f97316'; 
                   elseif ($act->estado === 'atrasada') $borderColor = '#ef4444'; 
                   
                   $isImprevista = ($act->tipo === 'Imprevista');
                   $isRutinaria = ($act->tipo === 'Rutinaria');
                   
                   if ($isImprevista) {
                       $rowClick = "window.location.href='" . route('actividades-imprevistas.show', $act->id) . "'";
                   } elseif ($isRutinaria) {
                       $rowClick = (auth()->user() && in_array(auth()->user()->rol, ['jefe', 'admin'])) ? "openEditRutinaModal(this)" : "event.stopPropagation()";
                   } else {
                       $rowClick = "openShowModal(this)";
                   }
                @endphp
                <tr class="tbl-row-gen" 
                    style="cursor:pointer; border-bottom:1px solid #f1f5f9; border-left:4px solid {{ $borderColor }}; transition: background 0.2s;" 
                    onmouseover="this.style.background='#f8fafc'" 
                    onmouseout="this.style.background='white'"
                    onclick="{{ $rowClick }}"
                    data-id="{{ $act->id }}" 
                    data-rutina="{!! $isRutinaria ? base64_encode(json_encode($act)) : '' !!}"
                    data-actividad="{!! !$isRutinaria ? base64_encode(json_encode($act)) : '' !!}"
                    data-titulo="{{ $act->titulo }}"
                    data-descripcion="{{ $act->descripcion }}"
                    data-estado="{{ $act->estado }}"
                    data-area="{{ $act->empleado ? ($act->empleado->area_id ?? '') : '' }}"
                    data-empleado="{{ $act->empleado_id }}">
                    <td style="padding:15px;">
                        <span style="font-weight:bold; font-size:15px; color:#1e293b; display:inline-block;">{{ $act->titulo }}</span>
                        @if($isRutinaria)
                            <span style="background:#dbeafe; color:#1e40af; font-size:10px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:bold; vertical-align:middle;">Rutinaria</span>
                        @elseif($isImprevista)
                            <span style="background:#fee2e2; color:#991b1b; font-size:10px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:bold; vertical-align:middle;">Imprevista</span>
                        @endif
                    </td>
                    <td style="padding:15px; font-size:14px; color:#334155;">
                        <span style="font-weight:600; display:block;">{{ $act->empleado ? $act->empleado->name : 'N/A' }}</span>
                        <span style="font-size:12px; color:#64748b; display:block; margin-top:2px;">Área: {{ $act->empleado && $act->empleado->area ? $act->empleado->area->nombre : 'Sin Área' }}</span>
                    </td>
                    <td style="padding:15px; text-align:center;">
                        @php
                            $prioColors = [
                                'baja' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                'media' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                'alta' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'urgente' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                            ];
                            $colors = $prioColors[strtolower($act->prioridad)] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                        @endphp
                        <span style="background:{{ $colors['bg'] }}; color:{{ $colors['text'] }}; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:bold; text-transform:uppercase;">
                            {{ $act->prioridad }}
                        </span>
                    </td>
                    <td style="padding:15px; width:15%;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="background:#e2e8f0; border-radius:4px; height:8px; flex:1; overflow:hidden;">
                                <div class="progreso-bar-val" style="background:#22c55e; width:{{ $act->porcentaje_avance ?? 0 }}%; height:100%;"></div>
                            </div>
                            <span class="progreso-txt-val" style="font-size:12px; font-weight:bold; color:#475569; width:35px; text-align:right;">{{ $act->porcentaje_avance ?? 0 }}%</span>
                        </div>
                        <span class="estado-badge-val" style="font-size:11px; color: {{ $act->estado === 'finalizada' ? '#10b981' : ($act->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }}; font-weight:bold; display:block; margin-top:4px;">
                            {{ ucfirst(str_replace('_', ' ', $act->estado)) }}
                        </span>
                    </td>
                    <td style="padding:15px; font-size:13px; color:#475569; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $act->descripcion }}">
                        {{ $act->descripcion }}
                    </td>
                    <td style="padding:15px; font-size:13px; color:#475569;">
                        @if($isRutinaria)
                            <span>Diaria</span>
                        @else
                            <span>Del {{ $act->fecha_inicio ? \Carbon\Carbon::parse($act->fecha_inicio)->format('d/m/Y') : 'N/A' }}</span><br>
                            <span>Al {{ $act->fecha_estimada_fin ? \Carbon\Carbon::parse($act->fecha_estimada_fin)->format('d/m/Y') : 'N/A' }}</span>
                        @endif
                    </td>
                    <td style="padding:15px; text-align:center;" onclick="event.stopPropagation();">
                        <div style="display:flex; gap:5px; justify-content:center; align-items:center;">
                            @if($isRutinaria)
                                <div style="display:flex; gap:6px; align-items:center; background:#f1f5f9; padding:4px 8px; border-radius:8px; border:1px solid #cbd5e1;">
                                    @for($i = 1; $i <= $act->veces_al_dia; $i++)
                                        <input type="checkbox" 
                                               class="rutina-check-box" 
                                               data-id="{{ $act->id }}" 
                                               value="{{ $i }}" 
                                               {{ $i <= $act->ejecuciones_hoy ? 'checked' : '' }} 
                                               onclick="handleRutinaCheck(this, event)"
                                               style="width: 15px; height: 15px; cursor: pointer; accent-color: #2563eb;"
                                               title="Ejecución {{ $i }} de {{ $act->veces_al_dia }}">
                                    @endfor
                                </div>
                                @if(in_array(auth()->user()->rol, ['jefe', 'admin']))
                                    <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; padding:4px 10px; font-size:11px; border-radius:6px; cursor:pointer;" onclick="openEditRutinaModal(this)" data-rutina="{!! base64_encode(json_encode($act)) !!}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('rutinas.destroy', $act->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Eliminar esta rutina?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:4px 10px; font-size:11px; border-radius:6px; cursor:pointer;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span style="color:#64748b; font-size:12px;">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:40px; text-align:center; color:#64748b;">
                        <i class="bi bi-journal-x" style="font-size:36px; display:block; margin-bottom:10px; color:#94a3b8;"></i>
                        No se encontraron actividades registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function aprobarActividad(id, btn, isImprevista) {
    if (!confirm('¿Marcar esta actividad como finalizada/aprobada?')) return;
    
    btn.disabled = true;
    let icon = btn.querySelector('i');
    let originalClass = icon ? icon.className : '';
    if (icon) {
        icon.className = 'bi bi-arrow-repeat spinner-border spinner-border-sm';
    }

    let endpoint = isImprevista 
        ? `/actividades-imprevistas/${id}/aprobar` 
        : `/actividades/${id}/aprobar`;

    fetch(endpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            console.error(data);
            alert('Error: ' + (data.message || 'No tienes permisos.'));
            btn.disabled = false;
            if (icon) icon.className = originalClass;
        }
    })
    .catch(err => {
        btn.disabled = false;
        if (icon) icon.className = originalClass;
        console.error(err);
        alert('Error de red.');
    });
}

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

function filterActivities() {
    let query = document.getElementById('search-query');
    let status = document.getElementById('filter-status');
    let area = document.getElementById('filter-area');
    let employee = document.getElementById('filter-employee');

    if (!query) return;

    let queryVal = query.value.toLowerCase();
    let statusVal = status.value;
    let areaVal = area.value;
    let employeeVal = employee.value;

    document.querySelectorAll('.tbl-row-gen').forEach(row => {
        let title = row.dataset.titulo ? row.dataset.titulo.toLowerCase() : '';
        let desc = row.dataset.descripcion ? row.dataset.descripcion.toLowerCase() : '';
        let matchesQuery = !queryVal || title.includes(queryVal) || desc.includes(queryVal);
        let matchesStatus = !statusVal || row.dataset.estado === statusVal;
        let matchesArea = !areaVal || row.dataset.area == areaVal;
        let matchesEmployee = !employeeVal || row.dataset.empleado == employeeVal;

        if (matchesQuery && matchesStatus && matchesArea && matchesEmployee) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    filterActivities();
});


</script>
<!-- END RESUMEN -->
@endsection
