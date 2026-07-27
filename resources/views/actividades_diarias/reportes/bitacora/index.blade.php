@extends('actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Directorio de Bitácoras')

@section('module-content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-file-earmark-bar-graph me-2" style="color:#3b82f6;"></i>Reportes de Actividades
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Directorio de bitácoras diarias de los empleados · {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <form action="{{ route('bitacora.index') }}" method="GET" style="margin:0; display:flex; gap:10px;">
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar empleado..." style="padding:8px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px; outline:none;">
        <button type="submit" class="btn-ver" style="font-size:13px; font-weight:bold; padding:8px 16px;"><i class="bi bi-search"></i> Buscar</button>
        @if($buscar)
            <a href="{{ route('bitacora.index') }}" class="btn-ver" style="background:#6b7280; text-decoration:none; display:inline-flex; align-items:center; font-size:13px; font-weight:bold; padding:8px 16px;">Limpiar</a>
        @endif
    </form>
</div>

<div class="rh-card">

    <p style="color:#6b7280; margin-bottom:15px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        Seleccione un empleado para visualizar su reporte diario.
        @if(isset($areaActiva) && $areaActiva)
            <span style="background:#dbeafe; color:#1e40af; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600; border:1px solid #bfdbfe;">
                <i class="bi bi-buildings-fill me-1"></i>Área: {{ $areaActiva->nombre }}
            </span>
        @else
            <span style="background:#fef3c7; color:#92400e; padding:4px 12px; border-radius:20px; font-size:13px; border:1px solid #fde68a;">
                <i class="bi bi-exclamation-circle me-1"></i>Sin área seleccionada — mostrando todos
            </span>
        @endif
    </p>

    <table class="rh-table">
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Área</th>
                <th>Horas Hoy</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $u)
            <tr onclick="window.location='{{ route('bitacora.usuario', ['empleado' => $u->id]) }}'" style="cursor:pointer;" class="tr-hover">
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:35px;height:35px;background:#1e3a8a;color:#fff;border-radius:50%;display:flex;justify-content:center;align-items:center;font-weight:bold;font-size:12px;">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                        <div>
                            <b style="font-size:14px;">{{ $u->name }}</b><br>
                            <span style="font-size:12px;color:#6b7280;">{{ $u->email }}</span>
                        </div>
                    </div>
                </td>
                <td>{{ $u->area->nombre ?? 'N/A' }}</td>
                <td><span style="background:#dbeafe;color:#1e40af;padding:3px 8px;border-radius:12px;font-weight:bold;">{{ $u->horas_hoy }} hrs</span></td>
                <td><span style="background:#dcfce7;color:#166534;padding:3px 8px;border-radius:12px;">Activo</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; color:#6b7280; padding:20px;">No se encontraron empleados en el directorio.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
