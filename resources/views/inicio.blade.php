@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

<style>
.site-header {
    background: linear-gradient(135deg, #ffffff 0%, #f3f6fb 100%);
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    padding: 20px 0 30px;
    position: relative;
    margin: -40px -12px 30px;
}
.site-header::after {
    content: "";
    position: absolute;
    bottom: 0; left: 0;
    height: 4px; width: 100%;
    background: linear-gradient(90deg, #0d6efd, #6f42c1);
}
.site-header .logo {
    position: absolute;
    top: 10px; left: 25px;
    width: 105px;
    opacity: 0.95;
}
.site-header .header-text {
    margin-left: 140px;
    padding-top: 10px;
}
.site-header .titulo {
    font-size: 2.4rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}
.site-header .subtitulo {
    font-size: 0.9rem;
    color: #6b7280;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
</style>

<div class="site-header">
    <img src="{{ asset('images/logo.png') }}" alt="Logo clinica" class="logo">
    <div class="header-text">
        <h1 class="titulo">Sistema de administración</h1>
        <div class="subtitulo">Clínica Euromédica</div>
    </div>
    @auth
    <div style="position: absolute; top: 25px; right: 25px; text-align: right;">
        <div class="fw-semibold text-dark" style="font-size: 0.95rem;">{{ Auth::user()->name }}</div>
        <div class="text-muted" style="font-size: 0.8rem; margin-top: -2px;">{{ Auth::user()->departamento ?? 'Usuario' }}</div>
        <div style="margin-top: 6px;">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 10px; font-size: 0.8rem; border-radius: 6px;">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    @endauth
</div>

<div class="row g-4 text-center">

    <!-- Administración -->
    @if(Auth::user()->hasPermission('administracion') || Auth::user()->hasPermission('administracion_rh') || Auth::user()->hasPermission('administracion_nomina') || Auth::user()->hasPermission('administracion_compras'))
    <div class="col-md-4">
        <a href="{{ route('administracion.index') }}" class="text-decoration-none">
            <div class="card card-menu card-morado h-100">
                <div class="card-body">
                    <h1>🧾</h1>
                    <h4 class="mt-3">Administración</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Operaciones -->
    @if(Auth::user()->hasPermission('operaciones'))
    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card card-menu card-azul h-100">
                <div class="card-body">
                    <h1>👤</h1>
                    <h4 class="mt-3">Operaciones</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Proveedores -->
    @if(Auth::user()->hasPermission('proveedores'))
    <div class="col-md-4">
        <a href="{{ route('proveedores.index') }}" class="text-decoration-none">
            <div class="card card-menu card-morado h-100">
                <div class="card-body">
                    <h1>📄</h1>
                    <h4 class="mt-3">Proveedores</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Actividades diarias -->
    @if(Auth::user()->hasPermission('actividades'))
    <div class="col-md-4">
        <a href="{{ route('actividades.index') }}" class="text-decoration-none">
            <div class="card card-menu card-azul h-100">
                <div class="card-body">
                    <h1>💰</h1>
                    <h4 class="mt-3">Actividades diarias</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Parque vehicular -->
    @if(Auth::user()->hasPermission('vehiculos'))
    <div class="col-md-4">
        <a href="{{ route('vehiculos.index') }}" class="text-decoration-none">
            <div class="card card-menu card-morado h-100">
                <div class="card-body">
                    <h1>🚑</h1>
                    <h4 class="mt-3">Parque vehicular</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Sistemas y registros -->
    @if(Auth::user()->hasPermission('sistemas'))
    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card card-menu card-azul h-100">
                <div class="card-body">
                    <h1>📦</h1>
                    <h4 class="mt-3">Sistemas y registros</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Registro medicos -->
    @if(Auth::user()->hasPermission('registros'))
    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card card-menu card-morado h-100">
                <div class="card-body">
                    <h1>🖥️</h1>
                    <h4 class="mt-3">Registro medicos</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Otros / ? -->
    @if(Auth::user()->hasPermission('otros'))
    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card card-menu card-azul h-100">
                <div class="card-body">
                    <h1>🩺</h1>
                    <h4 class="mt-3">?</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Gestión de Usuarios (Alineado debajo de Sistemas y registros en la cuadrícula de 3 columnas) -->
    @if(Auth::user()->hasPermission('users'))
    <div class="col-md-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none">
            <div class="card card-menu card-morado h-100">
                <div class="card-body">
                    <h1>👥</h1>
                    <h4 class="mt-3">Gestión de Usuarios</h4>
                </div>
            </div>
        </a>
    </div>
    @endif

</div>

@endsection
