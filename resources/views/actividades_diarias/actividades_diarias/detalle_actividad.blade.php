@extends('actividades_diarias.actividades_diarias.layout_general')

@section('actividades-content')
<div class="rh-card" style="max-width: 800px; margin: 20px auto; padding: 25px;">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:15px;">
        <h2 style="margin:0; color:#1e3a8a; font-weight: 800;">{{ $actividad->titulo }}</h2>
        <span style="background:#dbeafe; color:#1e40af; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:bold; text-transform:uppercase;">
            {{ $actividad->prioridad }}
        </span>
    </div>

    <div style="margin-bottom: 20px;">
        <strong style="color:#475569; font-size:14px; display:block; margin-bottom: 5px;">Descripción:</strong>
        <p style="font-size:14px; line-height:1.6; color:#334155; margin:0; background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
            {{ $actividad->descripcion }}
        </p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-bottom:25px; background:#f1f5f9; padding:15px; border-radius:8px;">
        <div>
            <strong style="color:#64748b; font-size:11px; display:block; text-transform: uppercase; font-weight: bold;">Asignado a</strong>
            <span style="color:#1e293b; font-weight:bold; font-size:14px;">{{ $actividad->empleado ? $actividad->empleado->name : 'N/A' }}</span>
        </div>
        <div>
            <strong style="color:#64748b; font-size:11px; display:block; text-transform: uppercase; font-weight: bold;">Estado</strong>
            <span style="color:#1e293b; font-weight:bold; font-size:14px; text-transform:capitalize;">{{ str_replace('_', ' ', $actividad->estado) }}</span>
        </div>
        <div>
            <strong style="color:#64748b; font-size:11px; display:block; text-transform: uppercase; font-weight: bold;">Plazo</strong>
            <span style="color:#1e293b; font-weight:bold; font-size:14px;">Del {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($actividad->fecha_estimada_fin)->format('d/m/Y') }}</span>
        </div>
        <div>
            <strong style="color:#64748b; font-size:11px; display:block; text-transform: uppercase; font-weight: bold;">Progreso</strong>
            <span style="color:#1e293b; font-weight:bold; font-size:14px;">{{ $actividad->porcentaje_avance }}%</span>
        </div>
    </div>

    <div style="text-align:right;">
        <a href="{{ route('actividades.resumen') }}" class="btn-ver" style="background:#1e3a8a; color:white; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:bold; font-size:13px; display: inline-block;">
            <i class="bi bi-arrow-left me-1"></i> Volver al Listado
        </a>
    </div>
</div>
@endsection
