<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Actividades - {{ $user->name }}</title>
    <style>
        @page {
            margin: 15px 18px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* BANNER PÚRPURA CORPORATIVO */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .logo-box {
            width: 180px;
            background-color: #ffffff;
            border: 2px solid #6b21a8;
            padding: 6px 10px;
            text-align: center;
            vertical-align: middle;
        }
        .logo-title {
            color: #6b21a8;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.1;
        }
        .logo-sub {
            color: #0284c7;
            font-size: 9px;
            font-weight: 700;
        }
        .title-banner {
            background-color: #6b21a8;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle;
            letter-spacing: 0.5px;
        }

        /* METADATOS DE ENCABEZADO */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta-table th {
            background-color: #1e3a8a;
            border: 1px solid #cbd5e1;
            padding: 3px;
            font-size: 7.5px;
            font-weight: bold;
            color: white;
            text-align: center;
        }
        .sub-th {
            background-color: #3b82f6;
            font-size: 6.5px;
            padding: 2px;
        }
        .meta-table td {
            background-color: #ffffff;
            color: #1e293b;
            font-size: 8.5px;
            font-weight: 600;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            width: 22%;
        }

        /* TABLA PRINCIPAL DE ACTIVIDADES */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #1e3a8a;
            border: 1px solid #cbd5e1;
            padding: 3px;
            font-size: 7.5px;
            font-weight: bold;
            color: white;
            text-align: center;
        }
        .sub-th {
            background-color: #3b82f6;
            font-size: 6.5px;
            padding: 2px;
        }
        .data-table th.sub-th {
            background-color: #1e3a8a;
            border: 1px solid #cbd5e1;
            padding: 3px;
            font-size: 7.5px;
            font-weight: bold;
            color: white;
            text-align: center;
        }
        .sub-th {
            background-color: #3b82f6;
            font-size: 6.5px;
            padding: 2px;
        }
        .data-table td {
            padding: 4px 3px;
            border: 1px solid #cbd5e1;
            font-size: 7px;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .row-num {
            text-align: center;
            font-weight: bold;
            color: #1e3a8a;
        }
        .row-time {
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }
        .pct-val {
            text-align: center;
            font-weight: bold;
            color: #166534;
        }

        .badge-imp {
            background-color: #ffedd5;
            color: #9a3412;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7px;
        }
        .badge-com {
            background-color: #faf5ff;
            color: #7c3aed;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7px;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO SUPERIOR MORADO Y LOGO -->
    <table class="header-table">
        <tr>
            <td class="logo-box" style="border: none; background: transparent; padding: 0;">
                @php
                    $path = public_path('img/euromedica_logo.png');
                    $base64 = '';
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" style="max-height: 45px;" />
                @endif
            </td>
            <td class="title-banner">
                REPORTE DIARIO DE ACTIVIDADES
            </td>
        </tr>
    </table>

    @php
        $timelineItems = collect();
        $dirigidoANombre = 'Jefatura / Dirección';
        $dirigidoADepto = $user->departamento ?? ($user->area->nombre ?? 'Administración');
        $dirigidoAPuesto = 'Jefe de Área';

        // 1. Avances Asignadas (Agrupadas por actividad)
        $asignadasGrouped = collect();
        foreach($avances as $av) {
            $actividad = $av->actividad;
            if (!$actividad) continue;
            if (!$asignadasGrouped->has($actividad->id)) {
                $actividad->historial_avances_arr = [];
                $asignadasGrouped->put($actividad->id, $actividad);
            }
            $asignadasGrouped[$actividad->id]->historial_avances_arr[] = [
                'hora_inicio' => $av->hora_inicio,
                'hora_fin' => $av->hora_fin,
                'horas_invertidas' => $av->horas_trabajadas,
                'porcentaje_avance' => $av->porcentaje_avance,
                'comentario' => $av->comentario ?? $av->que_se_hizo,
            ];
        }

        foreach($asignadasGrouped as $actividad) {
            if ($actividad && $actividad->dirigidoA) {
                $dirigidoANombre = $actividad->dirigidoA->name;
            }
            $t_inicio = $actividad->hora_inicio ?? '09:00';
            $t_fin = $actividad->hora_fin ?? '17:00';
            
            $esSinPlazo = (strtolower($actividad->tiene_plazo ?? '') === 'no') || (($actividad->tiempo_estimado ?? '') === 'Sin plazo');
            if ($actividad->hora_inicio === null || $esSinPlazo) {
                $t_inicio = 'N/A';
                $t_fin = 'N/A';
            }

            $tituloBase = $actividad->titulo ?? 'Actividad Asignada';
            
            $isPendienteAsig = ($actividad->estado === 'pendiente');
            $badgePendAsig = $isPendienteAsig ? '<span style="color:#dc2626; font-weight:bold; font-size: 9px; margin-right: 4px;">[Pendiente]</span>' : '';
            $tituloConFormato = '<span style="color:#15803d; font-weight:bold; font-size: 9px; margin-right: 4px;">[Asignada]</span>' . $badgePendAsig . $tituloBase;

            $depArea = $actividad->dependencia_area ?? null;
            $depResp = $actividad->dependencia_responsable ?? null;
            if ($depArea || $depResp) {
                $depStr = '<div style="line-height: 1.3;">';
                $depStr .= '<strong>Área:</strong> ' . ($depArea ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                $depStr .= '<strong>Responsable:</strong> ' . ($depResp ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                $depStr .= '<strong>Motivo:</strong> ' . ($actividad->dependencia_motivo ?? '-');
                $depStr .= '</div>';
            } else {
                $depStr = 'No';
            }

            $totalTiempo = array_sum(array_column($actividad->historial_avances_arr, 'horas_invertidas'));
            $ultimoAvance = end($actividad->historial_avances_arr);
            $porcentajeFinal = $ultimoAvance['porcentaje_avance'] ?? ($actividad->porcentaje_avance ?? 50);

            $timelineItems->push((object)[
                'hora_inicio' => $t_inicio,
                'hora_fin'    => $t_fin,
                'tarea'       => $tituloConFormato,
                'actividad_descripcion' => $actividad->descripcion ?? $actividad->objetivo ?? 'Actividad Asignada',
                'porcentaje'  => $porcentajeFinal . '%',
                'acciones'    => $actividad->acciones_realizadas,
                'notas'       => $actividad->observaciones,
                'colaboradores' => $actividad->colaboradores_texto ?? null,
                'tiempo'      => $totalTiempo,
                'resultado_label' => '',
                'resultado_box_color' => '#ffffff',
                'resultado_border_color' => '#ffffff',
                'resultado'   => '',
                'avances_json'=> $actividad->historial_avances_arr,
                'dependencia' => $depStr,
                'created_at'  => $actividad->created_at ? $actividad->created_at->format('Y-m-d H:i:s') : '2000-01-01 00:00:00'
            ]);
        }

        // 2. Imprevistas & Comida
        foreach($imprevistos as $imp) {
            if ($imp->dirigidoA) {
                $dirigidoANombre = $imp->dirigidoA->name;
            }
            if (strtolower($imp->titulo) === 'hora de comida') {
                $timelineItems->push((object)[
                    'hora_inicio' => $imp->hora_inicio ?? '14:00',
                    'hora_fin'    => $imp->hora_fin ?? '15:00',
                    'tarea'       => '<span style="color:#7c3aed; font-weight:bold; font-size: 9px; margin-right: 4px;">[Descanso]</span>Hora de Comida',
                    'actividad_descripcion' => 'Hora de comida reglamentaria computada (1 hora)',
                    'porcentaje'  => '100%',
                    'acciones'    => 'Descanso de alimentos',
                    'notas'       => 'Hora de comida sin pendientes activos',
                    'colaboradores' => null,
                    'tiempo'      => '1',
                    'resultado_label' => '',
                    'resultado_box_color' => '#ffffff',
                    'resultado_border_color' => '#ffffff',
                    'resultado'   => '',
                    'dependencia' => 'No',
                    'created_at'  => $imp->hora_inicio ? \Carbon\Carbon::parse($fecha . ' ' . $imp->hora_inicio)->format('Y-m-d H:i:s') : ($imp->created_at ? $imp->created_at->format('Y-m-d H:i:s') : '2000-01-01 00:00:00')
                ]);
            } else {
                $t_inicio = $imp->hora_inicio ?? ($imp->created_at ? $imp->created_at->format('H:i') : '10:00');
                $t_fin = $imp->hora_fin ?? '11:00';
                
                $esSinPlazo = (strtolower($imp->tiene_plazo ?? '') === 'no') || (($imp->tiempo_estimado ?? '') === 'Sin plazo');
                if ($imp->hora_inicio === null || $esSinPlazo) {
                    $t_inicio = 'N/A';
                    $t_fin = 'N/A';
                }

                $depAreaImp = $imp->dependencia_area ?? null;
                $depRespImp = $imp->dependencia_responsable ?? null;
                if ($depAreaImp || $depRespImp) {
                    $depStrImp = '<div style="line-height: 1.3;">';
                    $depStrImp .= '<strong>Área:</strong> ' . ($depAreaImp ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                    $depStrImp .= '<strong>Responsable:</strong> ' . ($depRespImp ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                    $depStrImp .= '<strong>Motivo:</strong> ' . ($imp->dependencia_motivo ?? '-');
                    $depStrImp .= '</div>';
                } else {
                    $depStrImp = 'No';
                }

                $isPendienteImp = ($imp->estado === 'pendiente');
                $badgePendImp = $isPendienteImp ? '<span style="color:#dc2626; font-weight:bold; font-size: 9px; margin-right: 4px;">[Pendiente]</span>' : '';

                $timelineItems->push((object)[
                    'hora_inicio' => $t_inicio,
                    'hora_fin'    => $t_fin,
                    'tarea'       => '<span style="color:#c2410c; font-weight:bold; font-size: 9px; margin-right: 4px;">[Personal]</span>' . $badgePendImp . $imp->titulo,
                    'actividad_descripcion' => $imp->descripcion_detallada ?? $imp->motivo ?? 'Atención de imprevisto urgente',
                    'porcentaje'  => ($imp->porcentaje_avance ?? 100) . '%',
                    'acciones'    => $imp->acciones_realizadas,
                    'notas'       => $imp->observaciones ?? $imp->observaciones_imp,
                    'colaboradores' => $imp->colaboradores_texto ?? null,
                    'tiempo'      => $t_inicio === 'N/A' ? 'N/A' : ($imp->horas_invertidas ?? '-'),
                    'resultado_label' => 'Resultados obtenidos al momento (Personal):',
                    'resultado_box_color' => '#f3e8ff',
                    'resultado_border_color' => '#6b21a8',
                    'resultado'   => !empty(trim(strip_tags($imp->resultado_obtenido))) ? '[' . ($imp->created_at ? $imp->created_at->format('H:i') : 'N/A') . ' hrs] ' . $imp->resultado_obtenido : '',
                    'avances_json'=> $imp->historial_avances,
                    'dependencia' => $depStrImp,
                    'created_at'  => $imp->created_at ? $imp->created_at->format('Y-m-d H:i:s') : '2000-01-01 00:00:00'
                ]);
            }
        }

        // 3. Rutinas
        foreach($ejecucionesRutina as $ej) {
            $rut = $ej->rutina;
            if ($rut && $rut->dirigidoA) {
                $dirigidoANombre = $rut->dirigidoA->name;
            }
            $horasReg = is_string($ej->horas_registro) ? json_decode($ej->horas_registro, true) : $ej->horas_registro;
            
            $t_inicio = $rut->hora_inicio ?? '09:00';
            $t_fin = $rut->hora_fin ?? '17:00';
            
            $esSinPlazo = (strtolower($rut->tiene_plazo ?? '') === 'no') || (($rut->tiempo_estimado ?? '') === 'Sin plazo');
            if ($rut->hora_inicio === null || $esSinPlazo) {
                $t_inicio = 'N/A';
                $t_fin = 'N/A';
            }

            $depAreaRut = $rut->dependencia_area ?? null;
            $depRespRut = $rut->dependencia_responsable ?? null;
            if ($depAreaRut || $depRespRut) {
                $depStrRut = '<div style="line-height: 1.3;">';
                $depStrRut .= '<strong>Área:</strong> ' . ($depAreaRut ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                $depStrRut .= '<strong>Responsable:</strong> ' . ($depRespRut ?: 'No') . ' <span style="color:#94a3b8; margin:0 4px;">|</span> ';
                $depStrRut .= '<strong>Motivo:</strong> ' . ($rut->dependencia_motivo ?? '-');
                $depStrRut .= '</div>';
            } else {
                $depStrRut = 'No';
            }

            $historialRut = [];
            $totalHorasRut = 0;
            if (is_array($horasReg) && count($horasReg) > 0) {
                foreach($horasReg as $reg) {
                    $historialRut[] = [
                        'hora_inicio' => $reg['hora_inicio'] ?? 'N/A',
                        'hora_fin' => $reg['hora_fin'] ?? 'N/A',
                        'horas_invertidas' => 0, // Not explicitly tracked per execution for rutinas? Usually it's in horas_invertidas total
                        'porcentaje_avance' => 100,
                        'comentario' => $reg['nota'] ?? 'Ejecución completada',
                    ];
                }
                $totalHorasRut = $rut->horas_invertidas ?? $rut->tiempo_estimado ?? '-';
            }

            $timelineItems->push((object)[
                'hora_inicio' => $t_inicio,
                'hora_fin'    => $t_fin,
                'tarea'       => '<span style="color:#1e40af; font-weight:bold; font-size: 9px; margin-right: 4px;">[Rutinaria]</span>' . ($rut->titulo ?? 'Rutina Diaria'),
                'actividad_descripcion' => $rut->descripcion ?? 'Ejecución periódica diaria',
                'porcentaje'  => '100%',
                'acciones'    => $rut->acciones_realizadas ?? 'Verificación y ejecución de tareas repetitivas',
                'notas'       => $rut->observaciones ?? '-',
                'tiempo'      => $totalHorasRut,
                'resultado_label' => '',
                'resultado_box_color' => '#ffffff',
                'resultado_border_color' => '#ffffff',
                'resultado'   => '',
                'avances_json'=> $historialRut,
                'dependencia' => $depStrRut,
                'created_at'  => $rut->created_at ? $rut->created_at->format('Y-m-d H:i:s') : '2000-01-01 00:00:00'
            ]);
        }
        
        // Ordenar todas las actividades por su fecha/hora de creación
        $timelineItems = $timelineItems->sortBy('created_at')->values();
    @endphp

    <!-- METADATOS DE RESPONSABILIDAD (BLOQUE EXCEL OFICIAL) -->
    <table class="meta-table">
        <tr>
            <th>Responsable:</th>
            <td>{{ $user->name }}</td>
            <th>Dirigido a:</th>
            <td>{{ $dirigidoANombre }}</td>
            <th>Fecha:</th>
            <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Departamento:</th>
            <td>{{ $user->departamento ?? ($user->area->nombre ?? 'Sistemas / TI') }}</td>
            <th>Departamento:</th>
            <td>{{ $dirigidoADepto }}</td>
            <th rowspan="2">Observaciones:</th>
            <td rowspan="2" style="font-style: italic; font-size: 8px;">
                {{ $observacionesPdf ?? "Reporte oficial generado automáticamente por el ERP EuroMédica. Total de horas: " . $totalHoras . " hrs." }}
            </td>
        </tr>
        <tr>
            <th>Puesto:</th>
            <td>{{ ucfirst($user->rol ?? 'Empleado') }}</td>
            <th>Puesto:</th>
            <td>{{ $dirigidoAPuesto }}</td>
        </tr>
    </table>

        <!-- TARJETAS DE ACTIVIDADES (DISEÑO VERTICAL) -->
    <div style="margin-top: 15px;">
        <h3 style="color: #6b21a8; margin-bottom: 10px; font-size: 11px; border-bottom: 2px solid #6b21a8; padding-bottom: 3px;">
            DETALLE DE ACTIVIDADES Y AVANCES
        </h3>
        
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px; margin-top: -12px;">
            @forelse($timelineItems as $idx => $item)
                <tr>
                    <td style="border: 1.5px solid #1e3a8a; padding: 0; background-color: #ffffff;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <!-- Encabezado de la Tarjeta -->
                            <tr style="background-color: #1e3a8a;">
                                <td style="padding: 6px 8px; border-bottom: 1.5px solid #1e3a8a;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 15%; font-size: 8px; font-weight: bold; color: #ffffff;">
                                                Actividad #{{ $idx + 1 }}
                                            </td>
                                            <td style="width: 70%; text-align: center; font-size: 8px; color: #ffffff;">
                                                @if($item->hora_inicio !== 'N/A')
                                                    <strong>Horario:</strong> {{ $item->hora_inicio }} - {{ $item->hora_fin }}
                                                    <span style="margin: 0 4px; color: #94a3b8;">|</span>
                                                @endif
                                                <strong>Tiempo Invertido/Est.:</strong> {{ $item->tiempo }}
                                            </td>
                                            <td style="width: 15%; text-align: right; font-size: 8px; color: #ffffff;">
                                                <strong>Avance:</strong> <span style="color: #4ade80; font-weight: bold;">{{ $item->porcentaje }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- Cuerpo de la Tarjeta -->
                            <tr>
                                <td style="padding: 8px;">
                                    <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px;">
                                        {!! $item->tarea !!}
                                    </div>
                                    
                                    <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                                        <tr>
                                            <td style="padding: 3px 0; vertical-align: top; width: 17%;"><strong style="color: #1e3a8a;">Descripción / Objetivo:</strong></td>
                                            <td style="padding: 3px 0; text-align: justify; color: #334155;">{{ $item->actividad_descripcion ?? '' }}</td>
                                        </tr>
                                        @if(trim(strip_tags($item->acciones)) && !in_array(trim(strip_tags($item->acciones)), ['-', 'Sin descripción']))
                                        <tr>
                                            <td style="padding: 3px 0; vertical-align: top;"><strong style="color: #1e3a8a;">Acciones a Realizar:</strong></td>
                                            <td style="padding: 3px 0; text-align: justify; color: #334155;">{{ $item->acciones }}</td>
                                        </tr>
                                        @endif
                                        @if(trim(strip_tags($item->notas)) && !in_array(trim(strip_tags($item->notas)), ['-', 'Sin notas']))
                                        <tr>
                                            <td style="padding: 3px 0; vertical-align: top;"><strong style="color: #1e3a8a;">Notas y Observaciones:</strong></td>
                                            <td style="padding: 3px 0; text-align: justify; color: #334155;">{{ $item->notas }}</td>
                                        </tr>
                                        @endif
                                        @if(!empty($item->colaboradores))
                                        <tr>
                                            <td style="padding: 3px 0; vertical-align: top;"><strong style="color: #1e3a8a;">Colaboradores:</strong></td>
                                            <td style="padding: 3px 0; text-align: justify; color: #334155;">{{ $item->colaboradores }}</td>
                                        </tr>
                                        @endif
                                        @if(trim(strip_tags($item->resultado)) && !in_array(trim(strip_tags($item->resultado)), ['-', 'Sin notas de avance registradas', 'Sin resultado registrado', 'Ejecución periódica diaria completada']))
                                        <tr>
                                            <td colspan="2" style="padding-top: 6px; padding-bottom: 6px;">
                                                <div style="background-color: {{ $item->resultado_box_color ?? '#f3e8ff' }}; border-left: 3px solid {{ $item->resultado_border_color ?? '#6b21a8' }}; padding: 6px; font-size: 8px;">
                                                    <strong style="color: {{ $item->resultado_border_color ?? '#6b21a8' }};">{{ $item->resultado_label }}</strong><br>
                                                    <span style="color: #1e293b;">{!! nl2br(e($item->resultado)) !!}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @if(!empty($item->avances_json))
                                            @php $historial = is_string($item->avances_json) ? json_decode($item->avances_json, true) : $item->avances_json; @endphp
                                            @if($historial && is_array($historial) && count($historial) > 0)
                                            <tr>
                                                <td colspan="2" style="padding-top: 6px; padding-bottom: 6px;">
                                                    <div style="background-color: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 4px; padding: 6px; font-size: 8px;">
                                                        <strong style="color: #166534; display: block; margin-bottom: 4px;">Historial de Avances Registrados:</strong>
                                                        @foreach($historial as $idxAvance => $avData)
                                                            @php
                                                                $esDevolucion = str_contains($avData['comentario'] ?? $avData['nota'] ?? '', '[Devuelta');
                                                                $colorTexto = $esDevolucion ? '#991b1b' : '#15803d';
                                                                $colorFondo = $esDevolucion ? '#fef2f2' : 'transparent';
                                                                $colorBorde = $esDevolucion ? '#fca5a5' : '#bbf7d0';
                                                                $colorDetalle = $esDevolucion ? '#7f1d1d' : '#14532d';
                                                                $tituloAvance = $esDevolucion ? '[Devolución]' : '[Avance #' . ($idxAvance + 1) . ']';
                                                            @endphp
                                                            <div style="background-color: {{ $colorFondo }}; margin-bottom: 4px; padding: 4px; border-radius: 4px; {{ !$loop->last ? 'border-bottom: 1px dashed '.$colorBorde.';' : '' }}">
                                                                <span style="color: {{ $colorTexto }}; font-weight: bold;">{{ $tituloAvance }}</span> 
                                                                @if(!empty($avData['hora_inicio']) && $avData['hora_inicio'] !== 'N/A')
                                                                    <strong>Horario:</strong> {{ $avData['hora_inicio'] }} - {{ $avData['hora_fin'] }} <span style="color:{{ $colorBorde }};">|</span> 
                                                                @elseif(!empty($avData['hora']) && $avData['hora'] !== 'N/A')
                                                                    <strong>Horario:</strong> {{ $avData['hora'] }} <span style="color:{{ $colorBorde }};">|</span> 
                                                                @else
                                                                    <strong>Horario:</strong> N/A - N/A <span style="color:{{ $colorBorde }};">|</span> 
                                                                @endif
                                                                <strong>Tiempo:</strong> {{ $avData['horas_invertidas'] ?? 0 }} hrs <span style="color:{{ $colorBorde }};">|</span> <strong>Avance/Ajuste:</strong> {{ $avData['porcentaje_avance'] ?? 100 }}%<br>
                                                                <span style="color: {{ $colorDetalle }}; margin-top:2px; display:inline-block;">{!! nl2br(e($avData['comentario'] ?? $avData['nota'] ?? '')) !!}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                        @endif
                                        <tr>
                                            <td style="padding: 3px 0; vertical-align: top; width: 17%;"><strong style="color: #1e3a8a;">Dependencia:</strong></td>
                                            <td style="padding: 3px 0; text-align: justify; color: #334155;">{!! $item->dependencia !!}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td style="text-align: center; color: #64748b; font-style: italic; padding: 20px; font-size: 9px; border: 1px dashed #cbd5e1;">
                        No existen actividades o avances registrados para el día seleccionado.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>

</body>
</html>
