@extends('actividades_diarias.actividades_diarias.layout_general')
@section('title', 'Registrar Imprevisto')

@section('tabs')
    <a href="{{ route('actividades-imprevistas.index') }}" class="tab">Imprevistos de Hoy</a>
    <a href="#" class="tab active">Registrar Imprevisto</a>
@endsection

@section('module-content')
<div class="rh-card" style="border-top:4px solid #f59e0b; max-width:800px; margin:0 auto;">
    <h2 style="color:#d97706; margin-bottom:5px;"><i class="bi bi-lightning-fill me-2"></i>Registrar Actividad Imprevista</h2>
    <p style="color:#6b7280; font-size:13px; margin-bottom:20px;">Registra actividades urgentes que no fueron planificadas pero tomaron tiempo de tu día.</p>
    
    <form action="{{ route('actividades-imprevistas.store') }}" method="POST">
        @csrf
        <div class="empleado-grid">
            <div style="grid-column: span 2;">
                <b>Título *</b>
                <input type="text" name="titulo" required placeholder="Ej: Falla de internet masiva">
            </div>

            <div style="grid-column: span 2;">
                <b>Descripción Detallada *</b>
                <textarea name="descripcion_detallada" rows="2" required placeholder="¿Qué pasó exactamente?"></textarea>
            </div>
            
            <div style="grid-column: span 2;">
                <b>Motivo *</b>
                <input type="text" name="motivo" required placeholder="¿Por qué tuviste que atenderlo?">
            </div>

            <div><b>Hora Inicio</b><input type="time" name="hora_inicio"></div>
            <div><b>Hora Fin</b><input type="time" name="hora_fin"></div>
            
            <div><b>Horas Invertidas *</b><input type="number" step="0.1" name="horas_invertidas" required></div>
            <div>
                <b>Impacto Principal *</b>
                <select name="impacto" required>
                    <option value="Sistemas">Sistemas</option>
                    <option value="Pacientes">Pacientes</option>
                </select>
            </div>
            
            <div>
                <b>Estado *</b>
                <select name="estado" required>
                    <option value="finalizada" selected>Terminada / Completada al momento</option>
                    <option value="pendiente">Se quedó pendiente de terminar</option>
                    <option value="en_proceso">Sigue en proceso de atención</option>
                </select>
            </div>

            <div style="grid-column: span 2;">
                <b>Resultado Obtenido (O Estado Actual) *</b>
                <textarea name="resultado_obtenido" rows="2" required placeholder="Ej: El internet regresó a la normalidad en todo el piso"></textarea>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <a href="{{ route('actividades.index') }}" class="btn-ver" style="background:#6b7280; margin-right:10px;">Cancelar</a>
            <button type="submit" class="btn-form" style="background:#f59e0b; color:#000;">Guardar Imprevisto</button>
        </div>
    </form>
</div>
@endsection
