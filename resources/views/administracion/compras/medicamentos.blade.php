@extends('layouts.app')

@section('title', 'Medicamentos')

@section('content')
<style> 


    .badge {
        padding: 0.5em 0.8em;
        border-radius: 12px;
        font-weight: 600;
    }
    
    /* Ajuste fino para el amarillo para que sea más legible */
    .bg-warning {
        background-color: #ffc107 !important;
    }

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

.com-nav {
    display: block;
    color: white;
    text-decoration: none;
    padding: 15px; /* Más espacio para que el texto largo respire */
    margin-bottom: 5px;
    border-radius: 8px;
    transition: 0.3s;
    line-height: 1.2; /* Asegura que el texto largo se vea bien */

    /* Estilo de Tarjetas Modernas */
    .card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    /* Mejorar inputs */
    .form-control {
        border-radius: 8px !important;
        border: 1px solid #ced4da !important;
        padding: 10px !important;
    }

    /* Estilo de botones */
    .btn {
        border-radius: 8px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
    }

    /* Ajustar acordeón */
    .accordion-item {
        border: none !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05) !important;
        margin-bottom: 10px !important;
        border-radius: 10px !important;
    }
    
    .accordion-button {
        background-color: #f8f9fa !important;
        border-radius: 10px !important;
    }
}
</style>
<div class="com-container">
    
    <aside class="com-menu">
    <h2 class="menu-title">💊 Gestión</h2>
    
    <div class="com-nav-items">
     <div class="com-nav" onclick="mostrarSeccion('seccion-lotes')">Gestion de Lotes y Caducidad</div>
    <div class="com-nav" onclick="mostrarSeccion('seccion-lab')">Control de laboratorio</div> 
     <div class="com-nav" onclick="mostrarSeccion('seccion-stock')">Stock</div>
    
     <div class="mt-auto">
    <a href="{{ route('compras.index') }}" 
   onclick="localStorage.removeItem('seccionActiva');" 
   class="btn btn-outline-light w-100 mt-4">
   ← Regresar
</a>
</div>
      
</aside>

   <main class="com-content">
    <div id="seccion-inicio" class="seccion">
        <div class="card p-4">
            <h2>Gestión de Medicamentos</h2>
            <p>Selecciona una opción del menú para comenzar.</p>
        </div>
    </div>
<div id="seccion-lotes" class="seccion" style="display:none;">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4">📦 Gestión de Lotes</h2>

        <!-- Navegación de pestañas -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" id="tab-lote-registro" href="#" onclick="mostrarLote('registro')">Registrar Nuevo Lote</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-lote-historial" href="#" onclick="mostrarLote('historial')">Ver Historial</a>
            </li>
        </ul>

        <!-- Contenido: Formulario -->
        <div id="lote-registro">
            <form action="{{ route('medicamentos.storeLote') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nombre del Fármaco</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Lote</label>
                        <input type="text" name="lote" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Fecha de Caducidad</label>
                    <input type="date" name="caducidad" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Lote</button>
            </form>
        </div>

        <!-- Contenido: Historial (Acordeón) -->
        <div id="lote-historial" style="display:none;">
            <div class="accordion" id="accordionLotes">
                @forelse($lotes as $lote)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $lote['id'] }}">
                            {{ $lote['farmaco'] }} - Lote: {{ $lote['lote'] }}
                        </button>
                    </h2>
                    <div id="collapse{{ $lote['id'] }}" class="accordion-collapse collapse" data-bs-parent="#accordionLotes">
                        <div class="accordion-body">
                            <p><strong>Cantidad:</strong> {{ $lote['cantidad'] }} unidades</p>
                            <p><strong>Caducidad:</strong> {{ $lote['caducidad'] }}</p>
                        </div>
                    </div>
                </div>
                @empty
                    <p class="text-muted">No hay lotes registrados aún.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

 <div id="seccion-lab" class="seccion" style="display:none;">
    <div class="card p-4 shadow-sm">
        <h2>🧪 Control de Laboratorio</h2>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" id="tab-registro" href="#" onclick="mostrarLab('registro')">Nuevo Registro</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-historial" href="#" onclick="mostrarLab('historial')">Ver Historial</a>
            </li>
        </ul>

        <div id="lab-registro">
            <form action="{{ route('laboratorio.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Laboratorio</label>
                        <input type="text" name="nombre_lab" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tipo de Análisis</label>
                        <input type="text" name="analisis" class="form-control" required>
                    </div>
                    <div class="mb-3">
                     <label class="form-label">Nombre del Paciente</label>
                        <input type="text" name="paciente" class="form-control" required>
                        </div>
                     <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Completado">Completado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Análisis</button>
            </form>
        </div>

        <div id="lab-historial" style="display:none;">
            <div class="mb-3">
    <button class="btn btn-outline-secondary btn-sm" onclick="filtrarTabla('todos')">Todos</button>
    <button class="btn btn-warning btn-sm" onclick="filtrarTabla('Pendiente')">Pendientes</button>
    <button class="btn btn-success btn-sm" onclick="filtrarTabla('Completado')">Completados</button>
</div>
           <table class="table table-hover">
    <thead class="table-light">
        <tr>
            <th>Laboratorio</th>
            <th>Análisis</th>
            <th>Estado</th>           <th>Observaciones</th>    <th>Paciente</th>         </tr>
    </thead>
    <tbody>
        @foreach($laboratorios as $lab)
        <tr class="fila-lab estado-{{ $lab['estado'] ?? 'Sin asignar' }}">
            <td>{{ $lab['nombre_lab'] }}</td>
            <td>{{ $lab['analisis'] }}</td>
            
            <td>
                @if(($lab['estado'] ?? '') == 'Pendiente')
                    <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                @else
                    <span class="badge bg-success text-white">✅ Completado</span>
                @endif
            </td>
            
            <td>{{ $lab['observaciones'] ?? '-' }}</td>
            
            <td>{{ $lab['paciente'] ?? 'Sin asignar' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>
</div>
        


    <div id="seccion-stock" class="seccion" style="display:none;">
        <div class="card p-4">
            <h2>📉 Control de Stock</h2>
            <table class="table">
                <thead>
                    <tr><th>Nombre</th><th>Cantidad</th><th>Caducidad</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @if(isset($inventario) && count($inventario) > 0)
                        @foreach($inventario as $index => $item)
                        <tr class="{{ isset($item->caducidad) && $item->caducidad < date('Y-m-d') ? 'table-danger' : '' }}">
                            <td>{{ $item->nombre ?? 'N/A' }}</td>
                            <td>{{ $item->cantidad ?? '1' }}</td>
                            <td>
                                {{ $item->caducidad ?? 'N/A' }}
                                @if(isset($item->caducidad) && $item->caducidad < date('Y-m-d'))
                                    <span class="badge bg-danger">Caducado!</span>
                                @endif
                            </td>
                           <td>
    <form action="{{ route('medicamentos.eliminar', $index) }}" method="POST">
    @csrf
    @method('DELETE') <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
</form>
</td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="4" class="text-center">No hay medicamentos en stock.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    function mostrarSeccion(id) {
        // Ocultar todas las secciones
        document.querySelectorAll('.seccion').forEach(s => s.style.display = 'none');
        // Mostrar la elegida
        const target = document.getElementById(id);
        if (target) {
            target.style.display = 'block';
            localStorage.setItem('seccionActiva', id);
        }
    }

    // Al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const activa = localStorage.getItem('seccionActiva');
        if (activa && document.getElementById(activa)) {
            mostrarSeccion(activa);
        } else {
            // Si no hay nada guardado, mostramos una por defecto
            mostrarSeccion('seccion-lotes');
        }
        // Al cargar la página, recuperar la última sección
    document.addEventListener('DOMContentLoaded', () => {
        const activa = localStorage.getItem('seccionActiva') || 'seccion-inicio';
        mostrarSeccion(activa);
        
    });
    
    });

    function mostrarLab(vista) {
    // 1. Ocultar ambos contenedores
    document.getElementById('lab-registro').style.display = (vista === 'registro') ? 'block' : 'none';
    document.getElementById('lab-historial').style.display = (vista === 'historial') ? 'block' : 'none';

    // 2. Cambiar estilos de las pestañas
    document.getElementById('tab-registro').className = (vista === 'registro') ? 'nav-link active' : 'nav-link';
    document.getElementById('tab-historial').className = (vista === 'historial') ? 'nav-link active' : 'nav-link';
}
    function filtrarTabla(estado) {
    const filas = document.querySelectorAll('.fila-lab');
    
    filas.forEach(fila => {
        if (estado === 'todos') {
            fila.style.display = ''; // Mostrar todo
        } else {
            // Verifica si la fila tiene la clase del estado seleccionado
            if (fila.classList.contains('estado-' + estado)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        }
    });
}

function mostrarLote(vista) {
    // Mostrar/Ocultar contenedores
    document.getElementById('lote-registro').style.display = (vista === 'registro') ? 'block' : 'none';
    document.getElementById('lote-historial').style.display = (vista === 'historial') ? 'block' : 'none';

    // Cambiar estado de pestañas
    document.getElementById('tab-lote-registro').className = (vista === 'registro') ? 'nav-link active' : 'nav-link';
    document.getElementById('tab-lote-historial').className = (vista === 'historial') ? 'nav-link active' : 'nav-link';
}
</script>


@endsection
