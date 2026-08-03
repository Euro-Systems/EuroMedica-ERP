@extends(request()->ajax() ? 'actividades_diarias.actividades_diarias.layout_ajax' : 'actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Reporte Gráfico de Evidencia Diaria')

@section('actividades-content')
<style>
/* Estilos para el Cronograma Visual / Diagrama de Flujo */
.diagram-timeline { position: relative; padding-left: 35px; margin-top: 20px; }
.diagram-timeline::before { content: ''; position: absolute; top: 0; left: 14px; height: 100%; width: 4px; background: #cbd5e1; border-radius: 2px; }

.diagram-item { position: relative; margin-bottom: 24px; }
.diagram-node { position: absolute; left: -35px; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; color: white; z-index: 2; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

.diagram-card { background: #ffffff; border-radius: 12px; padding: 16px; border: 2px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.03); transition: transform 0.2s; }
.diagram-card:hover { transform: translateY(-2px); }

/* Temas de Nodos */
.node-asignada { background: #16a34a; }
.card-asignada { border-color: #16a34a; background: #f0fdf4; }

.node-imprevista { background: #ea580c; }
.card-imprevista { border-color: #ea580c; background: #fff7ed; }

.node-rutinaria { background: #2563eb; }
.card-rutinaria { border-color: #2563eb; background: #eff6ff; }

.node-comida { background: #8b5cf6; }
.card-comida { border-color: #8b5cf6; background: #faf5ff; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-diagram-3-fill me-2" style="color:#2563eb;"></i>Diagrama de Evidencia Diaria - {{ $user->name }}
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Cronograma gráfico de actividades y avances registrados · {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </div>
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('bitacora.usuario', ['empleado' => $user->id]) }}" class="btn-ver" style="background:#6b7280; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-arrow-left"></i> Volver a Fechas
        </a>
        <a href="{{ route('bitacora.pdf', ['empleado' => $user->id, 'fecha' => $fecha]) }}" class="btn-ver" style="background:#0f172a; color:white; text-decoration:none; font-size:13px; font-weight:800; padding:9px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 6px rgba(0,0,0,0.15);" target="_blank">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444; font-size:16px;"></i> Descargar Reporte PDF (Diagrama)
        </a>
        @if(auth()->user() && in_array(auth()->user()->rol, ['jefe', 'admin']))
            <a href="{{ route('bitacora.index') }}" class="btn-ver" style="background:#475569; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;">
                <i class="bi bi-people-fill"></i> Directorio
            </a>
        @endif
    </div>
</div>

<div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
    
    <!-- Panel Izquierdo: Selección y Tarjetas de Métricas -->
    <div style="flex:1; min-width:280px;">
        <div class="rh-card" style="border-radius:12px; padding:16px; margin-bottom:16px;">
            <h4 style="margin:0 0 10px 0; color:#1e293b; font-size:14px; font-weight:800;">Consultar Otra Fecha</h4>
            <form onsubmit="changeDate(event)" style="margin:0;">
                <div style="display:flex; gap:8px;">
                    <input type="date" id="select_fecha" value="{{ $fecha }}" style="padding:8px 12px; border:1.5px solid #cbd5e1; border-radius:8px; width:100%; font-weight:600; font-size:13px;">
                    <button type="submit" class="btn-ver" style="background:#2563eb; color:white; font-weight:bold; padding:8px 14px; border-radius:8px;"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>

        <div class="rh-card" style="border-radius:12px; padding:16px;">
            <h4 style="color:#64748b; font-size:12px; font-weight:800; text-transform:uppercase; margin-bottom:14px; letter-spacing:0.5px;">Resumen del Jornada</h4>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px; font-weight:700; color:#166534;"><i class="bi bi-check2-circle me-1"></i> Asignadas</span>
                <span style="background:#dcfce7; color:#166534; border-radius:12px; padding:2px 10px; font-size:12px; font-weight:800;">{{ $avances->count() }}</span>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px; font-weight:700; color:#9a3412;"><i class="bi bi-person-fill me-1"></i> Personales</span>
                <span style="background:#ffedd5; color:#9a3412; border-radius:12px; padding:2px 10px; font-size:12px; font-weight:800;">{{ $imprevistos->where('titulo', '!=', 'Hora de Comida')->count() }}</span>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px; font-weight:700; color:#1e3a8a;"><i class="bi bi-arrow-repeat me-1"></i> Rutinarias</span>
                <span style="background:#dbeafe; color:#1e3a8a; border-radius:12px; padding:2px 10px; font-size:12px; font-weight:800;">{{ $ejecucionesRutina->count() }}</span>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px; font-weight:700; color:#7c3aed;"><i class="bi bi-cup-hot me-1"></i> Hora de Comida</span>
                <span style="background:#faf5ff; color:#7c3aed; border-radius:12px; padding:2px 10px; font-size:12px; font-weight:800; border:1px solid #e9d5ff;">{{ $imprevistos->where('titulo', 'Hora de Comida')->count() > 0 ? 'Registrada' : 'Pendiente' }}</span>
            </div>

            <div style="margin-top:14px; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                <span style="font-size:11px; font-weight:700; color:#64748b; display:block; text-transform:uppercase;">TOTAL HORAS COMPUTADAS</span>
                <strong style="font-size:22px; color:#1e3a8a;">{{ $totalHoras }} hrs</strong>
            </div>
        </div>
    </div>

    <!-- Panel Derecho: Cronograma Gráfico de Flujo -->
    <div class="rh-card" style="flex:2.5; min-width:320px; border-radius:12px; padding:20px;">
        <h3 style="margin:0 0 16px 0; color:#1e293b; font-size:17px; font-weight:800;">
            Diagrama de Registro por Hora — {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </h3>
        
        @if(count($rutinasFaltantes) > 0)
            <div style="background:#fee2e2; border:1.5px solid #fecaca; border-radius:10px; padding:14px; margin-bottom:20px; color:#991b1b;">
                <h4 style="margin:0 0 4px 0; font-size:13px; font-weight:800;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Rutinas Pendientes de Ejecución</h4>
                <ul style="margin:4px 0 0 0; padding-left:18px; font-size:12px; font-weight:600;">
                    @foreach($rutinasFaltantes as $rf)
                        <li><strong>{{ $rf->titulo }}</strong> (Prioridad: {{ strtoupper($rf->prioridad) }})</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $timelineItems = collect();
            
            // 1. Avances de Actividades Asignadas
            foreach($avances as $av) {
                $timelineItems->push((object)[
                    'time' => $av->created_at ? $av->created_at->format('H:i') . ' hrs' : ($av->hora_inicio ?? '09:00'),
                    'sort_time' => $av->created_at ? $av->created_at->format('H:i:s') : '09:00:00',
                    'type' => 'asignada',
                    'icon' => 'bi-journal-check',
                    'node_class' => 'node-asignada',
                    'card_class' => 'card-asignada',
                    'title' => $av->actividad->titulo ?? 'Avance de Actividad Asignada',
                    'badge' => 'ASIGNADA',
                    'badge_bg' => '#dcfce7',
                    'badge_color' => '#166534',
                    'porcentaje' => $av->porcentaje_avance ?? 50,
                    'nota' => $av->comentario ?? $av->que_se_hizo ?? 'Sin notas',
                    'extra' => 'Horas computadas: ' . ($av->horas_trabajadas ?? 0) . ' hrs'
                ]);
            }
            
            // 2. Actividades Imprevistas & Comida
            foreach($imprevistos as $imp) {
                if (strtolower($imp->titulo) === 'hora de comida') {
                    $timelineItems->push((object)[
                        'time' => ($imp->hora_inicio ?? '14:00') . ' a ' . ($imp->hora_fin ?? '15:00'),
                        'sort_time' => $imp->hora_inicio ?? '14:00:00',
                        'type' => 'comida',
                        'icon' => 'bi-cup-hot',
                        'node_class' => 'node-comida',
                        'card_class' => 'card-comida',
                        'title' => 'Hora de Comida (Almuerzo)',
                        'badge' => 'HORA DE COMIDA',
                        'badge_bg' => '#faf5ff',
                        'badge_color' => '#7c3aed',
                        'porcentaje' => 100,
                        'nota' => 'Hora de comida reglamentaria computada en la jornada diaria (1.0 hr).',
                        'extra' => '1.0 hr computada'
                    ]);
                } else {
                    $timelineItems->push((object)[
                        'time' => $imp->created_at ? $imp->created_at->format('H:i') . ' hrs' : '10:00 hrs',
                        'sort_time' => $imp->created_at ? $imp->created_at->format('H:i:s') : '10:00:00',
                        'type' => 'imprevista',
                        'icon' => 'bi-person-fill',
                        'node_class' => 'node-imprevista',
                        'card_class' => 'card-imprevista',
                        'title' => $imp->titulo,
                        'badge' => 'PERSONAL',
                        'badge_bg' => '#ffedd5',
                        'badge_color' => '#9a3412',
                        'porcentaje' => $imp->porcentaje_avance ?? 100,
                        'nota' => $imp->resultado_obtenido ?? $imp->motivo ?? 'Actividad personal',
                        'extra' => 'Motivo: ' . ($imp->motivo ?? 'Personal') . ' · ' . ($imp->horas_invertidas ?? 1) . ' hrs'
                    ]);
                }
            }
            
            // 3. Ejecuciones de Rutinas
            foreach($ejecucionesRutina as $ej) {
                $rutinaObj = $ej->rutina;
                $vecesTotal = $rutinaObj && $rutinaObj->veces_al_dia ? intval($rutinaObj->veces_al_dia) : 1;
                $horasReg = is_array($ej->horas_registro) ? $ej->horas_registro : [];

                if (count($horasReg) > 0) {
                    foreach($horasReg as $idx => $itemReg) {
                        $horaVal = is_array($itemReg) ? ($itemReg['hora'] ?? '10:00') : $itemReg;
                        $notaVal = is_array($itemReg) ? ($itemReg['nota'] ?? "Ejecución " . ($idx+1) . " realizada") : "Ejecución " . ($idx+1) . " de {$vecesTotal} realizada";
                        $pctCalc = round((($idx + 1) / $vecesTotal) * 100);

                        $timelineItems->push((object)[
                            'time' => $horaVal . ' hrs',
                            'sort_time' => $horaVal,
                            'type' => 'rutinaria',
                            'icon' => 'bi-arrow-repeat',
                            'node_class' => 'node-rutinaria',
                            'card_class' => 'card-rutinaria',
                            'title' => ($rutinaObj->titulo ?? 'Rutina Diaria') . " (Ejecución " . ($idx+1) . "/{$vecesTotal})",
                            'badge' => 'RUTINARIA',
                            'badge_bg' => '#dbeafe',
                            'badge_color' => '#1e3a8a',
                            'porcentaje' => $pctCalc,
                            'nota' => $notaVal,
                            'extra' => 'Ejecución registrada en rutina diaria'
                        ]);
                    }
                } else {
                    $timelineItems->push((object)[
                        'time' => \Carbon\Carbon::parse($ej->created_at)->format('H:i') . ' hrs',
                        'sort_time' => \Carbon\Carbon::parse($ej->created_at)->format('H:i:s'),
                        'type' => 'rutinaria',
                        'icon' => 'bi-arrow-repeat',
                        'node_class' => 'node-rutinaria',
                        'card_class' => 'card-rutinaria',
                        'title' => $rutinaObj->titulo ?? 'Rutina Diaria',
                        'badge' => 'RUTINARIA',
                        'badge_bg' => '#dbeafe',
                        'badge_color' => '#1e3a8a',
                        'porcentaje' => 100,
                        'nota' => $rutinaObj->descripcion ?? 'Ejecución completada.',
                        'extra' => 'Tarea periódica realizada'
                    ]);
                }
            }
            
            $timelineItems = $timelineItems->sortBy('sort_time');
        @endphp

        <div class="diagram-timeline">
            @forelse($timelineItems as $item)
                <div class="diagram-item">
                    <div class="diagram-node {{ $item->node_class }}">
                        <i class="bi {{ $item->icon }}"></i>
                    </div>
                    <div class="diagram-card {{ $item->card_class }}">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
                            <span style="font-size:12px; font-weight:800; color:#475569;">
                                <i class="bi bi-clock-fill me-1"></i> {{ $item->time }}
                            </span>
                            <div style="display:flex; gap:6px; align-items:center;">
                                <span style="background:{{ $item->badge_bg }}; color:{{ $item->badge_color }}; font-size:11px; padding:3px 8px; border-radius:12px; font-weight:800;">
                                    {{ $item->badge }}
                                </span>
                                <span style="background:#ffffff; color:#1e293b; font-size:11px; padding:3px 8px; border-radius:12px; font-weight:800; border:1px solid #cbd5e1;">
                                    {{ $item->porcentaje }}% completado
                                </span>
                            </div>
                        </div>

                        <h4 style="margin:0 0 6px 0; color:#1e293b; font-size:15px; font-weight:800;">
                            {{ $item->title }}
                        </h4>

                        <!-- Barra de Avance Visual -->
                        <div style="background:#e2e8f0; border-radius:4px; height:6px; width:100%; overflow:hidden; margin-bottom:10px;">
                            <div style="background: {{ $item->porcentaje >= 100 ? '#10b981' : '#2563eb' }}; width:{{ $item->porcentaje }}%; height:100%;"></div>
                        </div>

                        <div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; margin-bottom:6px;">
                            <span style="font-size:11px; font-weight:800; color:#64748b; display:block; margin-bottom:2px;">NOTAS / TRABAJO REALIZADO:</span>
                            <p style="margin:0; font-size:13px; color:#334155; font-weight:500; white-space:pre-wrap;">"{{ $item->nota }}"</p>
                        </div>

                        @if($item->extra)
                            <span style="font-size:11px; font-weight:700; color:#64748b; font-style:italic;">
                                {{ $item->extra }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#64748b; padding:40px 20px;">
                    <i class="bi bi-calendar-x" style="font-size:36px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
                    No hay registro de actividades, avances ni ejecuciones para esta fecha.
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
