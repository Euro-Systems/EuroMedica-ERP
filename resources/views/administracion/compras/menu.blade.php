@extends('layouts.app')

@section('title','Compras')

@section('content')
<style>
  /* Variables para mantener la consistencia */
:root {
    --bg-gradient: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
    --card-glass: rgba(255, 255, 255, 0.7);
}

body {
    background: var(--bg-gradient);
    min-height: 100vh;
}

/* Estilo para las tarjetas con efecto Glassmorphism */
.card {
    background: var(--card-glass);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card:hover {
    transform: translateY(-15px);
    box-shadow: 0 20px 40px rgba(31, 38, 135, 0.25);
}

/* Mejora del botón principal */
.btn-primary {
    background: linear-gradient(45deg, #1e3a8a, #7e22ce);
    border: none;
    border-radius: 50px; /* Botón píldora */
    padding: 10px 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}
    /* Ajuste adicional para el botón */
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px; /* Espacio entre el icono y el texto */
    border: 2px solid #1e3a8a;
    background: transparent;
    color: #1e3a8a;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #1e3a8a;
    color: #fff;
}
</style>
<a href="{{ url('administracion') }}" class="btn btn-back">
    ← Regresar
</a>
<div class="row g-4 mt-4">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">💊</div>
            <h4 class="card-title">Medicamentos</h4>
            <p class="card-text px-3">Gestión de insumos médicos con control de stock y caducidad.</p>
            <a href="{{ route('medicamentos.index') }}" class="btn btn-primary">Ingresar</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">🏥</div>
            <h4 class="card-title">Instalaciones</h4>
            <p class="card-text px-3">Mantenimiento de infraestructura y equipo médico especializado.</p>
            <a href="{{ route('compras.instalaciones') }}" class="btn btn-primary">Ingresar</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">📋</div>
            <h4 class="card-title">Administrativos</h4>
            <p class="card-text px-3">Control de papelería, office y suministros operativos.</p>
           <a href="{{ route('compras.administrativos') }}" class="btn btn-primary">Ingresar</a>
        </div>
    </div>
</div>


@endsection
