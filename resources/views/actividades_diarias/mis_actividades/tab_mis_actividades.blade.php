@extends('actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Mis Actividades')

@section('actividades-content')
<!-- MIAS PARCIAL -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-person-check-fill me-2" style="color:#3b82f6;"></i>Mis Actividades
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Listado de tus actividades asignadas y actividades imprevistas registradas · {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <div>
        <button type="button" onclick="abrirModalImprevista()" class="btn-ver" style="background:#ea580c; color:white; border:none; padding:10px 18px; border-radius:8px; font-weight:bold; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s;" onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">
            <i class="bi bi-lightning-fill"></i> Registrar Actividad Imprevista
        </button>
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

    <!-- TABLA DE ACTIVIDADES (ASIGNADAS E IMPREVISTAS) -->
    <div style="margin-bottom: 25px;">
        <div class="rh-card" style="box-shadow:0 4px 12px rgba(0,0,0,0.03); padding:0; overflow:hidden; border-radius:12px; border:1px solid #e2e8f0;">
            <table class="rh-table" style="margin:0; border-collapse:collapse; width:100%;">
                <thead>
                    <tr style="background:#1e3a8a; color:white;">
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none; border-radius:8px 0 0 0;">Actividad</th>
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Descripción</th>
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none; text-align:center;">Tipo</th>
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Fecha / Plazo</th>
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none;">Estado</th>
                        <th style="padding:15px; font-weight:bold; font-size:14px; border:none; text-align:center; border-radius:0 8px 0 0;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listado as $item)
                        @php
                           $borderColor = '#e2e8f0'; 
                           if ($item->estado === 'finalizada') $borderColor = '#10b981'; 
                           elseif ($item->estado === 'pendiente') $borderColor = '#facc15'; 
                           elseif ($item->estado === 'en_pausa') $borderColor = '#f97316'; 
                           elseif ($item->estado === 'atrasada') $borderColor = '#ef4444';
                           
                           $isImprevista = ($item->tipo === 'Imprevista');
                           $isRutinaria = ($item->tipo === 'Rutinaria');
                           
                           if ($isImprevista) {
                               $rowClick = "window.location.href='" . route('actividades-imprevistas.show', $item->id) . "'";
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
                            data-id="{{ $item->id }}" 
                            data-rutina="{!! $isRutinaria ? base64_encode(json_encode($item)) : '' !!}"
                            data-actividad="{!! !$isRutinaria ? base64_encode(json_encode($item)) : '' !!}">
                            <td style="padding:15px;">
                                <span style="font-weight:bold; font-size:15px; color:#1e293b; display:inline-block;">{{ $item->titulo }}</span>
                                @if($isRutinaria)
                                    <span style="background:#dbeafe; color:#1e40af; font-size:10px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:bold; vertical-align:middle;">Rutinaria</span>
                                @elseif($isImprevista)
                                    <span style="background:#fee2e2; color:#991b1b; font-size:10px; padding:2px 6px; border-radius:10px; margin-left:5px; font-weight:bold; vertical-align:middle;">Imprevista</span>
                                @endif
                            </td>
                            <td style="padding:15px; font-size:13px; color:#475569; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $item->descripcion }}">
                                {{ $item->descripcion }}
                            </td>
                            <td style="padding:15px; text-align:center;">
                                @if($isRutinaria)
                                    <span style="background:#dbeafe; color:#1e40af; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:bold;">
                                        Rutinaria
                                    </span>
                                @elseif($isImprevista)
                                    <span style="background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:bold;">
                                        Imprevista
                                    </span>
                                @else
                                    <span style="background:#dcfce7; color:#166534; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:bold;">
                                        Asignada
                                    </span>
                                @endif
                            </td>
                            <td style="padding:15px; font-size:13px; color:#475569;">
                                {{ $item->fecha_display }}
                            </td>
                            <td style="padding:15px; font-size:13px; color:#475569;">
                                <span class="estado-badge-val" style="font-weight:bold; color: {{ $item->estado === 'finalizada' ? '#166534' : ($item->estado === 'atrasada' ? '#991b1b' : '#ca8a04') }};">
                                    {{ ucfirst(str_replace('_', ' ', $item->estado)) }} ({{ $item->porcentaje_avance ?? 0 }}%)
                                </span>
                            </td>
                            <td style="padding:15px; text-align:center;" onclick="event.stopPropagation();">
                                <div style="display:flex; gap:5px; justify-content:center; align-items:center;">
                                    @if($isRutinaria)
                                        <div style="display:flex; gap:6px; align-items:center; background:#f1f5f9; padding:4px 8px; border-radius:8px; border:1px solid #cbd5e1;">
                                            @for($i = 1; $i <= $item->veces_al_dia; $i++)
                                                <input type="checkbox" 
                                                       class="rutina-check-box" 
                                                       data-id="{{ $item->id }}" 
                                                       value="{{ $i }}" 
                                                       {{ $i <= $item->ejecuciones_hoy ? 'checked' : '' }} 
                                                       onclick="handleRutinaCheck(this, event)"
                                                       style="width: 17px; height: 17px; cursor: pointer; accent-color: #2563eb;"
                                                       title="Ejecución {{ $i }} de {{ $item->veces_al_dia }}">
                                            @endfor
                                        </div>
                                        @if(in_array(auth()->user()->rol, ['jefe', 'admin']))
                                            <button type="button" class="btn-ver" style="background:#10b981; color:white; border:none; padding:4px 10px; font-size:11px; border-radius:6px; cursor:pointer;" onclick="openEditRutinaModal(this)" data-rutina="{!! base64_encode(json_encode($item)) !!}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('rutinas.destroy', $item->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Eliminar esta rutina?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:4px 10px; font-size:11px; border-radius:6px; cursor:pointer;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @elseif($isImprevista)
                                        <a href="{{ route('actividades-imprevistas.show', $item->id) }}" class="btn-ver" style="background:#ea580c; color:white; padding:4px 10px; font-size:11px; border-radius:6px; text-decoration:none;"><i class="bi bi-eye"></i> Ver</a>
                                    @else
                                        <button type="button" class="btn-ver" style="background:#16a34a; color:white; border:none; padding:4px 10px; font-size:11px; border-radius:6px; cursor:pointer;" onclick="openShowModal(this)" data-id="{{ $item->id }}">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px; text-align:center; color:#64748b;">
                                <i class="bi bi-journal-x" style="font-size:36px; display:block; margin-bottom:10px; color:#94a3b8;"></i>
                                No se encontraron actividades asignadas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function ejecutarRutina(id, btn) {
    btn.disabled = true;
    let icon = btn.querySelector('i');
    let originalClass = icon.className;
    icon.className = 'bi bi-arrow-repeat spinner-border spinner-border-sm';

    fetch(`/rutinas/${id}/ejecutar`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        icon.className = originalClass;

        if (data.success) {
            let card = document.getElementById(`rutina-card-${id}`);
            let container = document.getElementById(`rutina-status-container-${id}`);
            
            // Remove pending styles and update count
            card.classList.remove('rutina-pending');
            card.classList.add('rutina-completed');
            card.style.borderColor = '#e2e8f0';
            card.style.backgroundColor = '#ffffff';

            container.innerHTML = `
                <span class="badge-rutina-status text-success" style="font-weight:bold; font-size:12px;">
                    <i class="bi bi-check-circle-fill"></i> <span class="count-val">${data.ejecuciones_hoy}</span> ejecuciones
                </span>
            `;
        } else {
            alert('Error al registrar ejecución: ' + (data.message || 'Desconocido'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        icon.className = originalClass;
        console.error(err);
        alert('Error de red al registrar la ejecución.');
    });
}
</script>
<script>

function abrirModalImprevista() {
    let select = document.querySelector('#modalNuevaImprevista select[name="empleado_id"]');
    if (select) {
        select.value = 'self';
    }
    abrirModal('modalNuevaImprevista');
}


</script>
<!-- END CONTENIDO MIAS -->
@endsection
