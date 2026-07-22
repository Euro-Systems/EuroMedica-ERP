@extends('layouts.app')

{{-- =========================================================
    TÍTULO DE LA PÁGINA
========================================================= --}}
@section('title', 'Administración')

@section('content')

<style>

/* =========================================================
   CONTENEDOR PRINCIPAL DEL DASHBOARD
   - Ajusta altura total de pantalla
   - Agrega separación superior
========================================================= */
.dashboard{
min-height:calc(100vh - 70px);
padding-top:25px;
}

/* =========================================================
   HEADER DEL PANEL
   - Alinea título y botón de regreso
========================================================= */
.dashboard-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

/* =========================================================
   CONTENEDOR DEL TÍTULO
========================================================= */
.dashboard-title h2{
font-weight:700;
margin:0;
}

/* Subtítulo descriptivo */
.dashboard-title p{
margin:0;
color:#6c757d;
font-size:14px;
}

/* =========================================================
   BOTÓN DE REGRESO
========================================================= */
.btn-back{
border-radius:10px;
padding:8px 18px;
font-weight:500;
}

/* =========================================================
   TARJETAS DE ESTADÍSTICAS
========================================================= */
.stat-card{
background:white;
border-radius:14px;
padding:18px;
box-shadow:0 8px 20px rgba(0,0,0,.08);
text-align:center;
transition:.25s;
}

/* Hover de estadísticas */
.stat-card:hover{
transform:translateY(-5px);
box-shadow:0 15px 30px rgba(0,0,0,.15);
}

/* Número grande */
.stat-number{
font-size:26px;
font-weight:700;
}

/* Texto descriptivo */
.stat-label{
font-size:13px;
color:#6c757d;
}

/* =========================================================
   TARJETAS DE MÓDULOS
========================================================= */
.module-card{
border:none;
border-radius:18px;
padding:30px;
text-align:center;
transition:.3s;
color:white;
box-shadow:0 15px 40px rgba(0,0,0,.15);
}

/* Hover de módulos */
.module-card:hover{
transform:translateY(-8px);
box-shadow:0 25px 55px rgba(0,0,0,.25);
}

/* =========================================================
   ICONO PRINCIPAL DEL MÓDULO
========================================================= */
.module-icon{
font-size:40px;
margin-bottom:15px;
}

/* =========================================================
   COLORES PERSONALIZADOS
========================================================= */

/* ---------------------------------------------------------
   RECURSOS HUMANOS
--------------------------------------------------------- */
.card-rh{
background:linear-gradient(135deg,#1d4ed8,#3b82f6);
}

/* ---------------------------------------------------------
   NÓMINA
--------------------------------------------------------- */
.card-nomina{
background:linear-gradient(135deg,#6d28d9,#8b5cf6);
}

/* ---------------------------------------------------------
   COMPRAS
--------------------------------------------------------- */
.card-compras{
background:linear-gradient(135deg,#4338ca,#7c3aed);
}

/* =========================================================
   TÍTULO DEL MÓDULO
========================================================= */
.module-card h5{
font-weight:600;
margin-bottom:10px;
}

/* =========================================================
   DESCRIPCIÓN DEL MÓDULO
========================================================= */
.module-card p{
font-size:14px;
opacity:.9;
}

/* =========================================================
   BOTONES DE LOS MÓDULOS
========================================================= */
.module-card .btn{
border-radius:8px;
font-weight:500;
margin-top:10px;
}

</style>

<!-- =========================================================
     CONTENEDOR PRINCIPAL
========================================================= -->
<div class="container dashboard">

<!-- =========================================================
     HEADER PRINCIPAL
========================================================= -->
<div class="dashboard-header">

    <!-- Título y descripción -->
    <div class="dashboard-title">

        <h2>
            Panel de Administración
        </h2>

        <p>
            Gestione los módulos principales del sistema
        </p>

    </div>

    <!-- Botón de regreso -->
    <a href="{{ url('/') }}" class="btn btn-light shadow-sm btn-back">
        ← Regresar
    </a>

</div>


<!-- =========================================================
     MÓDULOS PRINCIPALES
========================================================= -->
<div class="row g-4">

    <!-- =====================================================
         MÓDULO RECURSOS HUMANOS
    ====================================================== -->
    @if(Auth::user()->hasPermission('administracion') || Auth::user()->hasPermission('administracion_rh'))
    <div class="col-md-4">

        <div class="module-card card-rh h-100">

            <!-- Icono -->
            <div class="module-icon">
                👥
            </div>

            <!-- Título -->
            <h5>
                Recursos Humanos
            </h5>

            <!-- Descripción -->
            <p>
                Gestión del personal, asistencias y control administrativo
            </p>

            <!-- Botón que accede con ruta (la rutas estan en routes/ -->
            <a href="{{ route('rh.index') }}"
               class="btn btn-light w-100">

                Entrar al módulo

            </a>

        </div>

    </div>
    @endif

    <!-- =====================================================
         MÓDULO NÓMINA
    ====================================================== -->
    @if(Auth::user()->hasPermission('administracion') || Auth::user()->hasPermission('administracion_nomina'))
    <div class="col-md-4">

        <div class="module-card card-nomina h-100">

            <!-- Icono -->
            <div class="module-icon">
                🧾
            </div>

            <!-- Título -->
            <h5>
                Nómina
            </h5>

            <!-- Descripción -->
            <p>
                Administración de pagos, sueldos y recibos
            </p>

            <!-- Botón -->
            <a href="{{ route('nomina.index') }}"
               class="btn btn-light w-100">

                Entrar al módulo

            </a>

        </div>

    </div>
    @endif

    <!-- =====================================================
         MÓDULO COMPRAS
    ====================================================== -->
    @if(Auth::user()->hasPermission('administracion') || Auth::user()->hasPermission('administracion_compras'))
    <div class="col-md-4">

        <div class="module-card card-compras h-100">

            <!-- Icono -->
            <div class="module-icon">
                💼
            </div>

            <!-- Título -->
            <h5>
                Compras
            </h5>

            <!-- Descripción -->
            <p>
                Control de adquisición y registro de insumos médicos
            </p>

            <!-- Botón -->
            <a href="{{ route('compras.index') }}"
               class="btn btn-light w-100">

                Entrar al módulo

            </a>

        </div>

    </div>
    @endif

</div>

</div>

@endsection
