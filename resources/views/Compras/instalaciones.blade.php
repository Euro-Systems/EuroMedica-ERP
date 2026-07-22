@extends('layouts.app')

@section('title', 'Instalaciones')

@section('content')
<style> 
/* Estilos base para asegurar que la aplicación ocupe toda la pantalla y no tenga scroll innecesario */
html,body{
    height:100%;
    margin:0;
    padding:0;
    font-family:'Segoe UI',Roboto,Arial;
    overflow:hidden;
}
/* Reset de contenedores de Bootstrap/Framework para diseño de pantalla completa */
.container,.container-fluid{
    max-width:100%!important;
    padding:0!important;
    margin:0!important;
    height:100%;
}
/* Botón para regresar al ERP */
.btn-btn-back {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    transition: background 0.3s ease;
}

.btn-btn-back {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
}
/* Estilos del menú lateral (Sidebar) */
.com-menu {
    width: 230px;
    background: linear-gradient(180deg, #1e3a8a, #3b82f6);
    padding: 25px;
    padding-bottom: 80px; /* Hacemos un espacio abajo para que no se encime el texto con el botón */
    color: #fff;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    position: relative; /* ¡ESTO ES VITAL! Convierte al menú en la "caja" de referencia del botón */
    height: 100%; /* Asegura que tome el alto total disponible */
}

/* Elementos de navegación del menú lateral */
.com-nav{
    padding:12px;
    border-radius:8px;
    margin-bottom:10px;
    cursor:pointer;
}

/* Estado activo para el menú lateral */
.com-nav.active{
    background:#fff;
    color:#1e3a8a;
    font-weight:bold;
}

/* Contenedor principal con Flexbox para separar menú lateral y contenido */
.com-container{
    display:flex;
    width:100vw;
    height:100vh;
    background:#f4f6f9;
}

/* Contenedor principal que une menú y contenido */
.com-container {
    display: flex;
    width: 100%;
    height: 100vh; /* Ocupa todo el alto visible */
    background: #f4f6f9;
}

/* El menú lateral ya lo tienes bien, solo asegúrate de esto */
.com-menu {
    width: 230px;
    background: linear-gradient(180deg, #1e3a8a, #3b82f6);
    padding: 25px;
    color: #fff;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Área de contenido a la derecha */
.com-content {
    flex: 1; /* Esto hace que ocupe todo el resto del ancho */
    overflow-y: auto; /* Permite scroll solo si el contenido es muy largo */
}

.btn-back:hover {
    background: white;
    color: #1e3a8a;
    border-color: white;
}

/* El botón ahora tiene su propio espacio de separación */
.btn-back {
    display: block;
    text-align: center;
    padding: 12px;
    margin-top: 20px; /* Esto le da separación manual con respecto a "Vacaciones" */
    border: 2px solid rgba(255, 255, 255, 0.5); /* Borde suave */
    background: rgba(255, 255, 255, 0.1); /* Fondo muy sutil */
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: all 0.3s ease;
}
/* El fondo oscuro que tapa la pantalla */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}
/* La ventana blanca */
.modal-content-custom {
    background: white;
    width: 80%;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
}
.show { display: block !important; }

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

</style>
<div class="com-container">
    <aside class="com-menu">
        <h2 class="menu-title">Gestión</h2>
        <div class="com-nav-items">
            <!-- Menú lateral -->
            <div class="com-nav">Catálogo de Activos</div>
            <div class="com-nav">Insumos</div>
            <div class="com-nav">Bitácora de Mantenimiento</div>
            <div class="com-nav">Reporte de Incidencias</div>
            <div class="com-nav">Agenda de Mantenimiento</div> 
            
            <!-- Ruta corregida: apunta al menu principal que definimos -->
            <a href="{{ route('compras.') }}" class="btn-back">
                ← Regresar
            </a>
        </div>
    </aside>

<!-- Contenedor de Tarjetas (Visible por defecto) -->
<div class="row g-4 mt-4" id="cards-container">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">💊</div>
            <h4 class="card-title">Piso 1:</h4>
            <p class="card-text px-3">Recepcion, Sala de espera,Consultorios...</p>
            <button onclick="showDetails('piso1')" class="btn btn-primary">Ingresar</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">🏥</div>
            <h4 class="card-title">Piso 2</h4>
            <p class="card-text px-3">Oficina Doc,Spa,Laboratorio.</p>
            <button onclick="showDetails('piso2')" class="btn btn-primary">Ingresar</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">📋</div>
            <h4 class="card-title">Piso 3</h4>
            <p class="card-text px-3">Oficinas,Cosina,Salas de reuniones.</p>
            <button onclick="showDetails('piso3')" class="btn btn-primary">Ingresar</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="display-4 mb-3">🚑</div>
            <h4 class="card-title">Parque Vehicular</h4>
            <p class="card-text px-3">Ambulancias,Carros eléctricos</p>
            <button onclick="showDetails('vehicular')" class="btn btn-primary">Ingresar</button>
        </div>
    </div>
</div>

<!-- Contenedor de Detalles (Oculto inicialmente) -->
<div id="details-container" style="display:none; background: white; padding: 20px; border-radius: 20px; margin-top: 20px;">
    <button onclick="goBack()" class="btn btn-secondary mb-3">← Volver al Menú</button>
    
    <div id="detail-piso1" class="area-detail" style="display:none;"><h3>Gestión de Activos - Piso 1</h3><div class="card p-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
   <div class="card p-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Inventario de Activos</h3>
        <button class="btn btn-primary">+ Agregar Activo</button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Ejemplo de Activo</td>
                    <td><span class="badge bg-success">Disponible</span></td>
                    <td>Área Asignada</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">Editar</button>
                    </td>
                </tr>
                <!-- Aquí repetirán tus registros -->
            </tbody>
        </table>
    </div>
</div>


    <div id="detail-piso2" class="area-detail" style="display:none;"><h3>Gestión de Activos - Piso 2</h3></div>
    <div id="detail-piso3" class="area-detail" style="display:none;"><h3>Gestión de Activos - Piso 3</h3></div>
    <div id="detail-vehicular" class="area-detail" style="display:none;"><h3>Gestión de Activos - Parque Vehicular</h3></div>
</div>
<!-- Modal Genérico para Agregar/Editar -->
<div id="action-modal" class="modal-overlay">
    <div class="modal-content-custom">
        <h4 id="modal-title">Gestión de Activo</h4>
        <form id="active-form">
            <div class="mb-3">
                <label>Nombre del Activo</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
<!-- Lógica JS -->
<script>
function showDetails(area) {
    document.getElementById('cards-container').style.display = 'none';
    document.getElementById('details-container').style.display = 'block';
    
    // Ocultar todos los detalles y mostrar solo el seleccionado
    document.querySelectorAll('.area-detail').forEach(el => el.style.display = 'none');
    document.getElementById('detail-' + area).style.display = 'block';
}

function goBack() {
    document.getElementById('details-container').style.display = 'none';
    document.getElementById('cards-container').style.display = 'flex';
}
</script>
@endsection
