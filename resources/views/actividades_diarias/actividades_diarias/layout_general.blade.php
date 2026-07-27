@extends('layouts.app')

@section('title')
    @yield('title', 'Actividades Diarias')
@endsection

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
/* Estilos base del layout de Actividades */
html,body{height:100%;margin:0;padding:0;font-family:'Segoe UI',Roboto,Arial;overflow:hidden;}
.container,.container-fluid{max-width:100%!important;padding:0!important;margin:0!important;height:100%;}
.rh-container{display:flex;width:100vw;height:100vh;background:#f4f6f9;}

/* Sidebar izquierdo - igual que RH */
.rh-menu{
    width:200px;
    background:linear-gradient(180deg,#1e3a8a,#3b82f6);
    padding:25px 15px;
    color:#fff;
    display:flex;
    flex-direction:column;
    flex-shrink:0;
}
.rh-menu h2{font-size:17px;font-weight:800;margin:0 0 25px 0;line-height:1.3;}
.rh-nav{
    padding:11px 14px;
    border-radius:8px;
    margin-bottom:8px;
    cursor:pointer;
    text-decoration:none;
    color:#fff;
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
    transition:background 0.15s;
}
.rh-nav:hover{background:rgba(255,255,255,0.15);color:#fff;}
.rh-nav.active{background:#fff;color:#1e3a8a;font-weight:bold;}
.rh-nav-bottom{margin-top:auto;}

/* Contenido principal */
.rh-content{flex:1;padding:14px;height:100%;display:flex;flex-direction:column;overflow:hidden;min-height:0;}
.tabs{display:flex;gap:10px;margin-bottom:10px;flex-shrink:0;}
.tab{padding:10px 16px;border-radius:10px 10px 0 0;background:#e5e7eb;cursor:pointer;color:#333;text-decoration:none;}
.tab.active{background:#fff;color:#1e3a8a;border-bottom:3px solid #3b82f6;}
#contenido{flex:1;overflow-y:auto;min-height:0;padding-bottom:120px;}
.rh-card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);margin-bottom:15px;}
.empleado-grid{display:grid;grid-template-columns:repeat(2, 1fr);gap:15px;margin-top:10px;}
.empleado-grid b{font-size:13px;color:#6b7280;display:block;margin-bottom:3px;}
.empleado-grid input, .empleado-grid select, .empleado-grid textarea{width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;}
.rh-table{width:100%;border-collapse:collapse;}
.rh-table th,.rh-table td{padding:12px;border-bottom:1px solid #e2e8f0;text-align:left;}
.rh-table th{background:#1e3a8a;color:white;}
.btn-ver{background:#1e3a8a;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-block;}
.btn-ver:hover{background:#172554;}
.btn-form{background:#22c55e;color:white;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-block;font-weight:bold;}
.btn-circle { width: 34px; height: 34px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.2s; border: none; }
.btn-circle:hover { transform: scale(1.1); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }

/* Modales Custom SPA */
.rh-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
.rh-modal.active { display: flex; }
.rh-modal-content { background-color: #fff; padding: 20px 30px; border-radius: 12px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; }
.rh-modal-close { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #6b7280; line-height: 1; }
.rh-modal-close:hover { color: #1f2937; }
</style>

@php
    $layoutArea = isset($area) ? $area : (session('active_area_id') ? \App\Models\Area::find(session('active_area_id')) : null);
    $isJefeAdmin = auth()->user() && in_array(auth()->user()->rol, ['jefe', 'admin']);
@endphp

<div class="rh-container">

    <!-- Sidebar izquierdo -->
    <aside class="rh-menu">
        <h2><i class="bi bi-calendar2-week me-1"></i> Actividades</h2>

        @if(Auth::user()->hasPermission('actividades_tablero') || Auth::user()->hasPermission('actividades'))
        <a href="{{ session('active_area_id') ? route('actividades.area.workspace', session('active_area_id')) : route('actividades.area.workspace', 1) }}"
           class="rh-nav {{ Request::routeIs('actividades.area.workspace') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Actividades Diarias
        </a>
        @endif

        @if(Auth::user()->hasPermission('actividades_resumen') || Auth::user()->hasPermission('actividades'))
        <a href="{{ route('actividades.resumen') }}"
           class="rh-nav {{ Request::routeIs('actividades.resumen') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> Resumen General
        </a>
        @endif

        @if(Auth::user()->hasPermission('actividades_mis_actividades') || Auth::user()->hasPermission('actividades'))
        <a href="{{ route('actividades.mias') }}"
           class="rh-nav {{ Request::routeIs('actividades.mias') ? 'active' : '' }}">
            <i class="bi bi-person-fill-check"></i> Mis Actividades
        </a>
        @endif

        @if(Auth::user()->hasPermission('actividades_reportes') || Auth::user()->hasPermission('actividades'))
            <a href="{{ route('bitacora.index') }}"
               class="rh-nav {{ Request::routeIs('bitacora.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
            </a>
        @endif

        <!-- Separador + botones inferiores -->
        <div class="rh-nav-bottom">
            @if($layoutArea)
                <div style="background:rgba(255,255,255,0.2); border-radius:8px; padding:8px 12px; margin-bottom:8px; font-size:12px; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-buildings-fill"></i>
                    <span>Área: <strong>{{ $layoutArea->nombre }}</strong></span>
                </div>
            @endif

            @if(Auth::user()->hasPermission('actividades_ver_areas') || Auth::user()->hasPermission('actividades'))
            <a href="{{ route('actividades.index') }}"
               class="rh-nav {{ Request::routeIs('actividades.index') ? 'active' : '' }}" style="margin-bottom:8px;">
                <i class="bi bi-buildings-fill"></i> Regresar a Áreas
            </a>
            @endif

            <a href="{{ url('/') }}" class="rh-nav">
                <i class="bi bi-box-arrow-left"></i> Regresar a Inicio
            </a>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="rh-content">
        <div id="contenido">
            @yield('module-content')
            @yield('actividades-content')
        </div>
        @include('actividades_diarias.actividades_diarias.modales_actividades')
    </main>
</div>

<script>
window.isBoss = @json($isJefeAdmin);
var isBoss = window.isBoss;
</script>
@endsection
