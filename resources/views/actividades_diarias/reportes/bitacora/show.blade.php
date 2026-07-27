@extends('actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Línea de Tiempo Diaria')

@section('module-content')
<style>
/* CSS nativo específico para la timeline */
.timeline { position: relative; padding-left: 40px; margin-top: 20px; }
.timeline::before { content: ''; position: absolute; top: 0; left: 16px; height: 100%; width: 4px; background: #e5e7eb; border-radius: 2px; }
.timeline-item { position: relative; margin-bottom: 25px; }
.timeline-icon { position: absolute; left: -40px; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; color: white; z-index: 1; box-shadow: 0 0 0 4px #f4f6f9; }
.timeline-content { background: #fff; border-radius: 10px; padding: 15px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
.timeline-time { font-size: 13px; color: #6b7280; font-weight: bold; margin-bottom: 8px; display: block; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-person-fill me-2" style="color:#3b82f6;"></i>Evidencia Diaria - {{ $user->name }}
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Línea de tiempo de actividades correspondientes al {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
        <a href="{{ route('bitacora.usuario', ['empleado' => $user->id]) }}" class="btn-ver" style="background:#6b7280; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-arrow-left"></i> Volver a Fechas</a>
        <a href="{{ route('bitacora.pdf', ['empleado' => $user->id, 'fecha' => $fecha]) }}" class="btn-ver" style="background:#10b981; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;" target="_blank">
            <i class="bi bi-file-earmark-pdf-fill"></i> Descargar PDF
        </a>
        @if(auth()->user() && in_array(auth()->user()->rol, ['jefe', 'admin']))
            <a href="{{ route('bitacora.index') }}" class="btn-ver" style="background:#475569; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-people-fill"></i> Directorio</a>
        @endif
        <div style="background:#fff; padding:6px 16px; border-radius:8px; border:1px solid #cbd5e1; display:flex; align-items:center; gap:8px;">
            <span style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:bold;">Total:</span>
            <b style="font-size:16px; color:#1e3a8a;">{{ $totalHoras }} hrs</b>
        </div>
    </div>
</div>

<div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
    
    <!-- Filtro y Resumen -->
    <div style="flex:1; min-width:280px;">
        <div class="rh-card">
            <h4>Filtros</h4>
            <hr style="border:0; border-top:1px solid #e2e8f0; margin:10px 0;">
            <form onsubmit="changeDate(event)" style="margin:0;">
                <div style="margin-bottom:10px;">
                    <b style="font-size:13px; color:#6b7280;">Fecha a consultar</b>
                    <div style="display:flex; gap:10px; margin-top:5px;">
                        <input type="date" id="select_fecha" value="{{ $fecha }}" style="padding:6px; border:1px solid #d1d5db; border-radius:6px; width:100%;">
                        <button type="submit" class="btn-ver"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="rh-card">
            <h4 style="color:#6b7280; font-size:14px; text-transform:uppercase; margin-bottom:15px;">Resumen del Día</h4>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">
                <span>Avances Registrados</span>
                <span style="background:#1e3a8a; color:#fff; border-radius:12px; padding:2px 8px; font-size:12px; font-weight:bold;">{{ $avances->count() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">
                <span>Imprevistos</span>
                <span style="background:#f59e0b; color:#000; border-radius:12px; padding:2px 8px; font-size:12px; font-weight:bold;">{{ $imprevistos->count() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">
                <span>Rutinas Ejecutadas</span>
                <span style="background:#10b981; color:#fff; border-radius:12px; padding:2px 8px; font-size:12px; font-weight:bold;">{{ $ejecucionesRutina->count() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>Rutinas Faltantes</span>
                <span style="background:#ef4444; color:#fff; border-radius:12px; padding:2px 8px; font-size:12px; font-weight:bold;">{{ count($rutinasFaltantes) }}</span>
            </div>
        </div>
    </div>

    <!-- Línea de Tiempo Visual -->
    <div class="rh-card" style="flex:2.5; min-width:320px;">
        <h3 style="margin-bottom: 20px;">Bitácora Visual - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h3>
        
        @if(count($rutinasFaltantes) > 0)
            <div style="background:#fee2e2; border:1px solid #fecaca; border-radius:10px; padding:15px; margin-bottom:20px; color:#991b1b;">
                <h4 style="margin:0 0 5px 0; font-size:14px;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Rutinas Pendientes de Ejecución</h4>
                <p style="font-size:12px; margin:5px 0 10px 0;">Las siguientes rutinas diarias obligatorias no registraron ninguna ejecución en esta fecha:</p>
                <ul style="margin:0; padding-left:20px; font-size:13px;">
                    @foreach($rutinasFaltantes as $rf)
                        <li><strong>{{ $rf->titulo }}</strong> (Prioridad: <span style="text-transform:capitalize;">{{ $rf->prioridad }}</span>)</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $timelineItems = collect();
            
            foreach($avances as $av) {
                $timelineItems->push((object)[
                    'time' => $av->hora_inicio . ' a ' . $av->hora_fin,
                    'sort_time' => $av->hora_inicio,
                    'type' => 'avance',
                    'icon' => 'bi-journal-check',
                    'icon_bg' => '#1e3a8a',
                    'title' => $av->actividad->titulo ?? 'Avance de Actividad',
                    'badge' => 'Avance Asignado',
                    'badge_bg' => '#e0e7ff',
                    'badge_color' => '#3730a3',
                    'description' => $av->que_se_hizo,
                    'problems' => 'Motivo / Contexto: ' . $av->motivo,
                    'actions' => 'Resultado / Producto: ' . $av->resultado_final,
                    'extra' => $av->observaciones ? 'Observaciones: ' . $av->observaciones : null
                ]);
            }
            
            foreach($imprevistos as $imp) {
                $timelineItems->push((object)[
                    'time' => 'Registro de Imprevisto',
                    'sort_time' => $imp->created_at->format('H:i:s'),
                    'type' => 'imprevisto',
                    'icon' => 'bi-lightning-fill',
                    'icon_bg' => '#f59e0b',
                    'title' => $imp->titulo,
                    'badge' => 'Imprevisto Urgente',
                    'badge_bg' => '#fef3c7',
                    'badge_color' => '#92400e',
                    'description' => $imp->descripcion_detallada,
                    'problems' => 'Motivo: ' . $imp->motivo,
                    'actions' => 'Resultado obtenido: ' . $imp->resultado_obtenido,
                    'extra' => 'Horas invertidas: ' . $imp->horas_invertidas . ' hrs'
                ]);
            }
            
            foreach($ejecucionesRutina as $ej) {
                $timelineItems->push((object)[
                    'time' => \Carbon\Carbon::parse($ej->hora_ejecucion)->format('H:i') . ' hrs',
                    'sort_time' => $ej->hora_ejecucion,
                    'type' => 'rutina',
                    'icon' => 'bi-arrow-repeat',
                    'icon_bg' => '#10b981',
                    'title' => $ej->rutina->titulo ?? 'Rutina Diaria',
                    'badge' => 'Rutina Ejecutada',
                    'badge_bg' => '#dcfce7',
                    'badge_color' => '#166534',
                    'description' => $ej->rutina->descripcion ?? 'Ejecución de tarea periódica asignada.',
                    'problems' => null,
                    'actions' => null,
                    'extra' => null
                ]);
            }
            
            $timelineItems = $timelineItems->sortBy('sort_time');
        @endphp

        <div class="timeline">
            @forelse($timelineItems as $item)
                <div class="timeline-item">
                    <div class="timeline-icon" style="background: {{ $item->icon_bg }};">
                        <i class="bi {{ $item->icon }}"></i>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-time"><i class="bi bi-clock me-1"></i> {{ $item->time }}</span>
                        
                        <h4 style="margin:0 0 10px 0; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            {{ $item->title }}
                            <span style="background:{{ $item->badge_bg }}; color:{{ $item->badge_color }}; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:normal;">{{ $item->badge }}</span>
                        </h4>
                        
                        <p style="color:#4b5563; margin-bottom:10px; font-size:13px;">{{ $item->description }}</p>
                        
                        @if($item->problems || $item->actions || $item->extra)
                            <div style="background:#f8fafc; padding:10px; border-radius:6px; font-size:13px; border:1px solid #e2e8f0; display:flex; flex-direction:column; gap:4px;">
                                @if($item->problems)
                                    <div><b style="color:#334155;">{{ $item->type === 'imprevisto' ? 'Motivo' : 'Motivo / Contexto' }}:</b> {{ str_replace(['Motivo / Contexto: ', 'Motivo: '], '', $item->problems) }}</div>
                                @endif
                                @if($item->actions)
                                    <div><b style="color:#334155;">{{ $item->type === 'imprevisto' ? 'Resultado' : 'Resultado / Producto' }}:</b> {{ str_replace(['Resultado obtenido: ', 'Resultado / Producto: '], '', $item->actions) }}</div>
                                @endif
                                @if($item->extra)
                                    <div style="color:#64748b; font-style:italic;">{{ $item->extra }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#64748b; padding:40px 20px;">
                    <i class="bi bi-calendar-x" style="font-size:32px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
                    No hay registro de actividades, avances ni ejecuciones de rutina para esta fecha.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function changeDate(event) {
    event.preventDefault();
    let dateVal = document.getElementById('select_fecha').value;
    if (dateVal) {
        window.location.href = `/bitacora/{{ $user->id }}/${dateVal}`;
    }
}
</script>
@endsection
