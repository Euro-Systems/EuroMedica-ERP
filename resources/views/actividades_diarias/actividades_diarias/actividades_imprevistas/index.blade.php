@extends('actividades_diarias.actividades_diarias.layout_general')
@section('title', 'Historial de Imprevistos')

@section('tabs')
    <a href="#" class="tab active">Imprevistos de Hoy</a>
    <a href="{{ route('actividades-imprevistas.create') }}" class="tab">Registrar Imprevisto</a>
@endsection

@section('module-content')
<div class="rh-card">
    <h2 style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <span style="color:#d97706;"><i class="bi bi-lightning-fill"></i> Historial de Imprevistos</span>
        <a href="{{ route('actividades-imprevistas.create') }}" class="btn-form" style="background:#f59e0b;color:#000;">+ Registrar Imprevisto</a>
    </h2>

    <table class="rh-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Empleado</th>
                <th>Imprevisto</th>
                <th>Horas</th>
                <th>Impacto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($imprevistos as $imprevisto)
            <tr>
                <td>{{ \Carbon\Carbon::parse($imprevisto->fecha)->format('d/m/Y') }}</td>
                <td>{{ $imprevisto->empleado->name ?? $imprevisto->empleado->nombre ?? 'Yo (Jefe)' }}</td>
                <td>
                    <b>{{ $imprevisto->titulo }}</b><br>
                    <small style="color:#6b7280;">{{ str($imprevisto->descripcion_detallada)->limit(50) }}</small>
                </td>
                <td><span style="background:#e5e7eb;padding:3px 8px;border-radius:12px;">{{ $imprevisto->horas_invertidas }} hrs</span></td>
                <td><span style="background:#fee2e2;color:#991b1b;padding:3px 8px;border-radius:12px;">{{ $imprevisto->impacto }}</span></td>
                <td>
                    <div style="display:flex; gap: 5px;">
                        <a href="{{ route('actividades-imprevistas.show', $imprevisto->id) }}" class="btn-ver" style="background:#4b5563;">Ver</a>
                        <form action="{{ route('actividades-imprevistas.destroy', $imprevisto->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Seguro que deseas eliminar este imprevisto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ver" style="background:#ef4444;"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#6b7280;padding:20px;">No hay imprevistos registrados actualmente.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
