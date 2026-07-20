@extends('actividades_diarias.actividades_diarias.layout_general')
@section('title', 'Detalle Imprevisto')

@section('tabs')
    <a href="{{ route('actividades-imprevistas.index') }}" class="tab">Imprevistos de Hoy</a>
    <a href="#" class="tab active">Detalle de Imprevisto</a>
@endsection

@section('module-content')
<div class="rh-card" style="border-top:4px solid #f59e0b; max-width: 800px; margin: 0 auto;">
    <h2 style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <span style="color:#d97706;"><i class="bi bi-lightning-fill"></i> Detalle de Imprevisto</span>
        <a href="{{ route('actividades-imprevistas.index') }}" class="btn-ver" style="background:#6b7280;">Volver</a>
    </h2>
    
    <h3 style="margin-bottom:15px; font-weight:bold;">{{ $imprevisto->titulo }}</h3>
    
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom: 20px;">
        <div>
            <b style="color:#6b7280; font-size:12px; text-transform:uppercase;">Empleado que atendió</b>
            <p style="margin:0;">{{ $imprevisto->empleado->name ?? $imprevisto->empleado->nombre ?? 'Yo (Jefe)' }}</p>
        </div>
        <div>
            <b style="color:#6b7280; font-size:12px; text-transform:uppercase;">Fecha y Duración</b>
            <p style="margin:0;">{{ \Carbon\Carbon::parse($imprevisto->fecha)->format('d/m/Y') }} <span style="background:#e5e7eb;padding:2px 6px;border-radius:4px;margin-left:5px;">{{ $imprevisto->horas_invertidas }} hrs invertidas</span></p>
        </div>
    </div>

    <div style="background:#f9fafb; padding:15px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom: 20px;">
        <h4 style="margin-top:0; font-size:16px;">Descripción Detallada</h4>
        <p style="margin:0; color:#4b5563;">{{ $imprevisto->descripcion_detallada }}</p>
    </div>

    <div style="background:#f9fafb; padding:15px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom: 20px;">
        <h4 style="margin-top:0; font-size:16px;">Motivo Original</h4>
        <p style="margin:0; color:#4b5563;">{{ $imprevisto->motivo }}</p>
    </div>

    <div style="background:#f0fdf4; padding:15px; border-radius:8px; border:1px solid #bbf7d0; border-left:4px solid #22c55e;">
        <h4 style="margin-top:0; font-size:16px; color:#166534;">Resultado Obtenido</h4>
        <p style="margin:0; color:#166534;">{{ $imprevisto->resultado_obtenido }}</p>
    </div>
</div>
@endsection
