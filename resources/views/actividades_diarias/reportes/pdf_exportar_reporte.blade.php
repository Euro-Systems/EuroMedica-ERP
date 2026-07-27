<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividad #{{ $actividad->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .header .subtitle {
            font-size: 12px;
            color: #666666;
            margin-top: 5px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a8a;
            background-color: #f1f5f9;
            padding: 5px 8px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 3px solid #3b82f6;
        }
        .info-table, .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            color: #4b5563;
            width: 150px;
        }
        .data-table th, .data-table td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #1f2937;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pendiente { background-color: #fef3c7; color: #92400e; }
        .badge-proceso { background-color: #dbeafe; color: #1e40af; }
        .badge-revision { background-color: #f3e8ff; color: #6b21a8; }
        .badge-finalizada { background-color: #dcfce7; color: #166534; }
        .badge-atrasada { background-color: #fee2e2; color: #991b1b; }
        .badge-aprobado { background-color: #dcfce7; color: #166534; }
        .badge-rechazado { background-color: #fee2e2; color: #991b1b; }
        
        .chat-container {
            margin-bottom: 15px;
        }
        .chat-message {
            border-bottom: 1px solid #f3f4f6;
            padding: 8px 0;
        }
        .chat-message .meta {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 3px;
        }
        .chat-message .meta span {
            font-weight: normal;
            font-size: 9px;
            color: #9ca3af;
            margin-left: 8px;
        }
        .chat-message .text {
            color: #1f2937;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }
        .signatures .line {
            width: 200px;
            border-top: 1px solid #9ca3af;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">Clínica Euromédica</div>
                    <div class="subtitle">Reporte Completo de Actividad Diaria</div>
                </td>
                <td style="text-align: right; color: #666666;">
                    Fecha de Impresión: {{ now()->format('d/m/Y H:i') }}<br>
                    ID Actividad: #{{ $actividad->id }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Información General de la Actividad</div>
    <table class="info-table">
        <tr>
            <td class="label">Actividad:</td>
            <td><strong>{{ $actividad->titulo }}</strong></td>
            <td class="label">Estado:</td>
            <td>
                <span class="badge badge-{{ $actividad->estado }}">
                    {{ ucfirst(str_replace('_', ' ', $actividad->estado)) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Empleado Responsable:</td>
            <td>{{ $actividad->empleado->name ?? 'N/A' }}</td>
            <td class="label">Jefe Asignador:</td>
            <td>{{ $actividad->jefe->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Descripción:</td>
            <td colspan="3">{{ $actividad->descripcion }}</td>
        </tr>
        <tr>
            <td class="label">Prioridad:</td>
            <td>{{ ucfirst($actividad->prioridad) }}</td>
            <td class="label">Impacto Principal:</td>
            <td>{{ $actividad->impacto }}</td>
        </tr>
        <tr>
            <td class="label">Duración / Modalidad:</td>
            <td>
                {{ $actividad->modalidad === 'un_dia' ? 'Un solo día' : 'Varios días' }}
            </td>
            <td class="label">Rango de Fechas:</td>
            <td>
                @if($actividad->modalidad === 'un_dia')
                    {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d/m/Y') }}
                @else
                    {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($actividad->fecha_estimada_fin)->format('d/m/Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Tiempo Estimado:</td>
            <td>{{ $actividad->tiempo_estimado }}</td>
            <td class="label">Avance General:</td>
            <td>{{ $actividad->porcentaje_avance }}%</td>
        </tr>
    </table>

    <div class="section-title">Historial de Avances Registrados</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha y Horas</th>
                <th>Qué se hizo</th>
                <th>Resultado / Observaciones</th>
                <th>Estado de Aprobación</th>
                <th>Detalles de Aprobación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actividad->avances as $avance)
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($avance->fecha_avance)->format('d/m/Y') }}<br>
                    <small style="color: #666;">{{ $avance->hora_inicio }} a {{ $avance->hora_fin }} ({{ $avance->horas_trabajadas }} hrs)</small>
                </td>
                <td>
                    <strong>Se hizo:</strong> {{ $avance->que_se_hizo }}<br>
                    <small><strong>Motivo:</strong> {{ $avance->motivo }}</small>
                </td>
                <td>
                    {{ $avance->resultado_final }}
                    @if($avance->observaciones)
                        <br><small style="color: #666;">Obs: {{ $avance->observaciones }}</small>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $avance->estado_aprobacion }}">
                        {{ ucfirst($avance->estado_aprobacion) }}
                    </span>
                </td>
                <td>
                    @if($avance->estado_aprobacion !== 'pendiente')
                        <small>
                            Por: {{ $avance->aprobadoPor->name ?? 'N/A' }}<br>
                            Fecha: {{ \Carbon\Carbon::parse($avance->fecha_aprobacion)->format('d/m/Y') }} {{ $avance->hora_aprobacion }}<br>
                            @if($avance->comentario_jefe)
                                <strong>Comentario:</strong> {{ $avance->comentario_jefe }}
                            @endif
                        </small>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #666;">No hay avances registrados para esta actividad.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Historial de Conversación y Aclaraciones</div>
    <div class="chat-container">
        @forelse($actividad->mensajes as $msj)
        <div class="chat-message">
            <div class="meta">
                {{ $msj->user->name ?? 'Usuario' }} 
                <span>{{ \Carbon\Carbon::parse($msj->fecha)->format('d/m/Y') }} {{ $msj->hora }}</span>
            </div>
            <div class="text">
                {{ $msj->mensaje }}
            </div>
        </div>
        @empty
        <p style="color: #666; font-style: italic;">No hay mensajes registrados en esta actividad.</p>
        @endforelse
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="line"></div>
                Firma de Empleado Responsable
            </td>
            <td>
                <div class="line"></div>
                Firma de Jefe / Supervisor
            </td>
        </tr>
    </table>

</body>
</html>
