@extends('actividades_diarias.actividades_diarias.layout_general')

@section('title', 'Historial de Evidencias - ' . $user->name)

@section('module-content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="margin:0; color:#1e3a8a; font-size:20px; font-weight:800;">
            <i class="bi bi-file-earmark-text-fill me-2" style="color:#3b82f6;"></i>Evidencias de Trabajo - {{ $user->name }}
        </h2>
        <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">
            Historial de evidencias diarias de trabajo del empleado · {{ now()->format('d/m/Y') }}
        </p>
    </div>
    @if(auth()->user() && in_array(auth()->user()->rol, ['jefe', 'admin']))
        <a href="{{ route('bitacora.index') }}" class="btn-ver" style="background:#6b7280; text-decoration:none; font-size:13px; font-weight:bold; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-arrow-left"></i> Volver al Directorio
        </a>
    @endif
</div>

<div class="rh-card">

    <p style="color:#6b7280; margin-bottom:15px; font-size:14px;">A continuación se muestran todas las fechas en las cuales se han registrado actividades, avances o rutinas de trabajo.</p>

    <table class="rh-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Avances Planificados</th>
                <th>Actividades Imprevistas</th>
                <th>Rutinas Ejecutadas</th>
                <th>Horas Reportadas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fechasList as $item)
            <tr onclick="window.location='{{ route('bitacora.show', ['empleado' => $user->id, 'fecha' => $item->fecha]) }}'" style="cursor:pointer;" class="tr-hover">
                <td>
                    <strong>{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}</strong>
                </td>
                <td>
                    @if($item->count_avances > 0)
                        <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:6px; font-size:12px; font-weight:bold;">{{ $item->count_avances }} avances</span>
                    @else
                        <span style="color:#94a3b8;">-</span>
                    @endif
                </td>
                <td>
                    @if($item->count_imprevistos > 0)
                        <span style="background:#fef3c7; color:#b45309; padding:2px 8px; border-radius:6px; font-size:12px; font-weight:bold;">{{ $item->count_imprevistos }} imprevistos</span>
                    @else
                        <span style="color:#94a3b8;">-</span>
                    @endif
                </td>
                <td>
                    @if($item->count_rutinas > 0)
                        <span style="background:#dcfce7; color:#166534; padding:2px 8px; border-radius:6px; font-size:12px; font-weight:bold;">{{ $item->count_rutinas }} rutinas</span>
                    @else
                        <span style="color:#94a3b8;">-</span>
                    @endif
                </td>
                <td>
                    <span style="background:#f3e8ff; color:#6b21a8; padding:3px 8px; border-radius:12px; font-weight:bold;">{{ $item->total_horas }} hrs</span>
                </td>
                <td>
                    <a href="{{ route('bitacora.show', ['empleado' => $user->id, 'fecha' => $item->fecha]) }}" class="btn-ver" style="font-size:12px; padding:4px 8px;"><i class="bi bi-eye"></i> Ver Evidencia</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; color:#6b7280; padding:30px; font-style:italic;">No hay reportes de evidencia registrados para este usuario.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
