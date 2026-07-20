<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evidencia Diaria - {{ $user->name }}</title>
    <style>
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header .title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .header .subtitle {
            font-size: 10px;
            color: #666666;
            margin-top: 4px;
        }
        .meta-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            vertical-align: top;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-avance { background-color: #e0e7ff; color: #3730a3; }
        .badge-imprevisto { background-color: #fef3c7; color: #92400e; }
        .badge-rutina { background-color: #dcfce7; color: #166534; }
        
        .table-activities {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-activities th, .table-activities td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .table-activities th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        .table-activities tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">Clínica Euromédica - Evidencia Diaria de Trabajo</div>
                    <div class="subtitle">Línea de tiempo de actividades realizadas</div>
                </td>
                <td style="text-align: right; color: #666666; font-size: 9px; vertical-align: bottom;">
                    Fecha de Reporte: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td>
                    <strong>Empleado:</strong> {{ $user->name }}<br>
                    <strong>Puesto / Rol:</strong> {{ ucfirst($user->rol) }}
                </td>
                <td>
                    <strong>Área:</strong> {{ $user->area->nombre ?? 'N/A' }}<br>
                    <strong>Correo:</strong> {{ $user->email }}
                </td>
                <td style="text-align: right; font-size: 13px;">
                    <strong>Horas Totales Reportadas:</strong><br>
                    <span style="color: #1e3a8a; font-weight: bold;">{{ $totalHoras }} hrs</span>
                </td>
            </tr>
        </table>
    </div>

    @php
        $timelineItems = collect();
        
        foreach($avances as $av) {
            $timelineItems->push((object)[
                'time' => $av->hora_inicio . ' a ' . $av->hora_fin,
                'sort_time' => $av->hora_inicio,
                'type' => 'avance',
                'badge' => 'Avance Asignado',
                'badge_class' => 'badge-avance',
                'title' => $av->actividad->titulo ?? 'Avance de Actividad',
                'description' => $av->que_se_hizo,
                'hours' => $av->horas_trabajadas
            ]);
        }
        
        foreach($imprevistos as $imp) {
            $timelineItems->push((object)[
                'time' => 'Registro de Imprevisto',
                'sort_time' => $imp->created_at->format('H:i:s'),
                'type' => 'imprevisto',
                'badge' => 'Imprevisto Urgente',
                'badge_class' => 'badge-imprevisto',
                'title' => $imp->titulo,
                'description' => $imp->descripcion_detallada,
                'hours' => $imp->horas_invertidas
            ]);
        }
        
        foreach($ejecucionesRutina as $ej) {
            $timelineItems->push((object)[
                'time' => \Carbon\Carbon::parse($ej->hora_ejecucion)->format('H:i') . ' hrs',
                'sort_time' => $ej->hora_ejecucion,
                'type' => 'rutina',
                'badge' => 'Rutina Ejecutada',
                'badge_class' => 'badge-rutina',
                'title' => $ej->rutina->titulo ?? 'Rutina Diaria',
                'description' => $ej->rutina->descripcion ?? 'Ejecución de tarea periódica.',
                'hours' => 0 // Rutinas no cargan horas directamente a menos que tengan avances
            ]);
        }
        
        $timelineItems = $timelineItems->sortBy('sort_time');
    @endphp

    <table class="table-activities">
        <thead>
            <tr>
                <th style="width: 15%;">Hora / Momento</th>
                <th style="width: 18%;">Tipo</th>
                <th style="width: 25%;">Actividad</th>
                <th style="width: 34%;">Resumen de lo Realizado</th>
                <th style="width: 8%;" class="text-center">Horas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timelineItems as $item)
                <tr>
                    <td><strong>{{ $item->time }}</strong></td>
                    <td>
                        <span class="badge {{ $item->badge_class }}">{{ $item->badge }}</span>
                    </td>
                    <td><strong>{{ $item->title }}</strong></td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center"><strong>{{ $item->hours > 0 ? $item->hours . ' hrs' : '-' }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #666; font-style: italic; padding: 20px;">
                        No se registraron actividades para esta fecha.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
