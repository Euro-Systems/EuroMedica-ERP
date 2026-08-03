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
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            padding: 4px 6px;
            border: 1px solid #1d4ed8;
            text-align: left;
            width: 11%;
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
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            padding: 4px 3px;
            border: 1px solid #1d4ed8;
            text-align: center;
            vertical-align: middle;
        }
        .data-table th.sub-th {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 7.5px;
        }
        .data-table td {
            padding: 4px 3px;
            border: 1px solid #cbd5e1;
            font-size: 8px;
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
            <td class="logo-box">
                <div class="logo-title">Clínica<br>EURO Médica</div>
                <div class="logo-sub">+ Salud Integral</div>
            </td>
            <td class="title-banner">
                REPORTE DIARIO DE ACTIVIDADES
            </td>
        </tr>
    </table>

    @php
        $timelineItems = collect();
        $dirigidoANombre = 'Jefatura / Dirección';
        $dirigidoADepto = $user->area->nombre ?? 'Administración';
        $dirigidoAPuesto = 'Jefe de Área';

        // 1. Avances Asignadas
        foreach($avances as $av) {
            if ($av->actividad && $av->actividad->dirigidoA) {
                $dirigidoANombre = $av->actividad->dirigidoA->name;
            }
            $timelineItems->push((object)[
                'hora_inicio' => $av->actividad->hora_inicio ?? ($av->created_at ? $av->created_at->format('H:i') : '09:00'),
                'hora_fin'    => $av->actividad->hora_fin ?? '17:00',
                'tarea'       => $av->actividad->titulo ?? 'Actividad Asignada',
                'descripcion' => $av->comentario ?? $av->que_se_hizo ?? ($av->actividad->descripcion ?? 'En progreso'),
                'porcentaje'  => ($av->porcentaje_avance ?? 50) . '%',
                'acciones'    => $av->actividad->acciones_realizadas ?? 'Ejecución de actividades correspondientes al puesto',
                'dep_area'    => $av->actividad->dependencia_area ?? ($user->area->nombre ?? '-'),
                'dep_resp'    => $av->actividad->dependencia_responsable ?? '-',
                'notas'       => $av->actividad->observaciones ?? '-',
                'comentarios' => $av->actividad->comentarios_dirigido ?? '-'
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
                    'tarea'       => 'Hora de Comida (Almuerzo)',
                    'descripcion' => 'Hora de comida reglamentaria computada (1 hora)',
                    'porcentaje'  => '100%',
                    'acciones'    => 'Descanso de alimentos',
                    'dep_area'    => '-',
                    'dep_resp'    => '-',
                    'notas'       => 'Hora de comida sin pendientes activos',
                    'comentarios' => '-'
                ]);
            } else {
                $timelineItems->push((object)[
                    'hora_inicio' => $imp->hora_inicio ?? ($imp->created_at ? $imp->created_at->format('H:i') : '10:00'),
                    'hora_fin'    => $imp->hora_fin ?? '11:00',
                    'tarea'       => '[IMPREVISTO] ' . $imp->titulo,
                    'descripcion' => $imp->resultado_obtenido ?? $imp->motivo ?? 'Atención de imprevisto urgente',
                    'porcentaje'  => ($imp->porcentaje_avance ?? 100) . '%',
                    'acciones'    => $imp->acciones_realizadas ?? 'Resolución inmediata de imprevisto',
                    'dep_area'    => $imp->dependencia_area ?? ($user->area->nombre ?? '-'),
                    'dep_resp'    => $imp->dependencia_responsable ?? '-',
                    'notas'       => $imp->observaciones ?? ('Motivo: ' . ($imp->motivo ?? 'Urgencia')),
                    'comentarios' => $imp->comentarios_dirigido ?? '-'
                ]);
            }
        }

        // 3. Rutinas
        foreach($ejecucionesRutina as $ej) {
            $rut = $ej->rutina;
            if ($rut && $rut->dirigidoA) {
                $dirigidoANombre = $rut->dirigidoA->name;
            }
            $timelineItems->push((object)[
                'hora_inicio' => $rut->hora_inicio ?? '09:00',
                'hora_fin'    => $rut->hora_fin ?? '17:00',
                'tarea'       => '[RUTINA] ' . ($rut->titulo ?? 'Rutina Diaria'),
                'descripcion' => $rut->descripcion ?? 'Ejecución periódica diaria completada',
                'porcentaje'  => '100%',
                'acciones'    => $rut->acciones_realizadas ?? 'Verificación y ejecución de tareas repetitivas',
                'dep_area'    => $rut->dependencia_area ?? ($user->area->nombre ?? '-'),
                'dep_resp'    => $rut->dependencia_responsable ?? '-',
                'notas'       => $rut->observaciones ?? '-',
                'comentarios' => $rut->comentarios_dirigido ?? '-'
            ]);
        }
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
            <td>{{ $user->area->nombre ?? 'Sistemas / TI' }}</td>
            <th>Departamento:</th>
            <td>{{ $dirigidoADepto }}</td>
            <th rowspan="2">Observaciones:</th>
            <td rowspan="2" style="font-style: italic; font-size: 8px;">
                Reporte oficial generado automáticamente por el ERP EuroMédica. Total de horas: {{ $totalHoras }} hrs.
            </td>
        </tr>
        <tr>
            <th>Puesto:</th>
            <td>{{ ucfirst($user->rol ?? 'Empleado') }}</td>
            <th>Puesto:</th>
            <td>{{ $dirigidoAPuesto }}</td>
        </tr>
    </table>

    <!-- TABLA DE ACTIVIDADES (ESTRUCTURA EXACTA AL FORMATO DE LA CLÍNICA) -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">NUM</th>
                <th colspan="2" style="width: 14%;">Horario</th>
                <th rowspan="2" style="width: 18%;">Tarea</th>
                <th colspan="2" style="width: 22%;">Estatus</th>
                <th rowspan="2" style="width: 14%;">Acciones</th>
                <th colspan="2" style="width: 14%;">Dependencia o Vinculación</th>
                <th rowspan="2" style="width: 7%;">Notas y Observaciones</th>
                <th rowspan="2" style="width: 7%;">Comentarios Destinatario</th>
            </tr>
            <tr>
                <th class="sub-th">INICIO</th>
                <th class="sub-th">TÉRMINO</th>
                <th class="sub-th">Descripción</th>
                <th class="sub-th">% Avance</th>
                <th class="sub-th">Área</th>
                <th class="sub-th">Responsable</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timelineItems as $idx => $item)
                <tr>
                    <td class="row-num">{{ $idx + 1 }}</td>
                    <td class="row-time">{{ $item->hora_inicio }}</td>
                    <td class="row-time">{{ $item->hora_fin }}</td>
                    <td><strong>{{ $item->tarea }}</strong></td>
                    <td>{{ $item->descripcion }}</td>
                    <td class="pct-val">{{ $item->porcentaje }}</td>
                    <td>{{ $item->acciones }}</td>
                    <td>{{ $item->dep_area }}</td>
                    <td>{{ $item->dep_resp }}</td>
                    <td>{{ $item->notas }}</td>
                    <td>{{ $item->comentarios }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; color: #64748b; font-style: italic; padding: 16px;">
                        No existen actividades o avances registrados para el día seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
