<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Parque vehicular</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Estilos generales y reset de márgenes */
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #edf1f5;
            color: #1e293b;
        }

        /* Contenedor principal que usa Flexbox para separar el menú del contenido */
        .container {
            display: flex;
            height: 100vh;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Menú lateral (Sidebar) con degradado nuevo */
        .rh-menu {
            width: 210px;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            padding: 25px 15px;
            color: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
            overflow-y: auto;
        }

        .rh-menu h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 25px;
            text-transform: none;
            letter-spacing: normal;
            padding-left: 5px;
        }

        .rh-menu h3 {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 12px;
            padding-left: 5px;
        }

        /* Elementos de navegación del menú lateral */
        .rh-nav {
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }

        .rh-nav:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .rh-nav.active {
            background: #fff;
            color: #1e3a8a;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Botón de regreso posicionado al final del menú */
        .btn-back {
            margin-top: auto;
            padding: 10px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Área de contenido principal */
        .content {
            flex: 1;
            padding: 45px;
            overflow-y: auto;
        }

        /* Tarjetas para organizar la información con animación de entrada */
        .card {
            background-color: #ffffff;
            border: 1px solid #dce3ec;
            padding: 28px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            animation: fadeSlide 0.4s ease-out;
            margin-bottom: 25px;
            position: relative;
        }

        /* Grid para mostrar los detalles técnicos del vehículo */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            font-size: 14px;
        }

        .info-item span {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }

        /* Estilos de tabla para el historial de servicios */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 14px;
        }

        th, td {
            padding: 13px 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        select {
            width: 100%;
            padding: 6px;
            font-size: 13px;
        }

        /* Estilos para modales */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-card {
            background-color: #ffffff;
            border-radius: 8px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
            line-height: 1;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background-color: #f8fafc;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        /* Botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #0d62b6;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #0b5197;
            color: #ffffff;
        }
        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1;
        }
        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
        }
        .btn-danger:hover {
            background-color: #dc2626;
            color: #ffffff;
        }
        .btn-success {
            background-color: #10b981;
            color: #ffffff;
        }
        .btn-success:hover {
            background-color: #059669;
            color: #ffffff;
        }
        
        /* Grid de formulario */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #0d62b6;
        }
        .full-width {
            grid-column: span 2;
        }

        /* Cotizaciones dinámicas */
        .quote-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
        .quote-row input {
            flex: 1;
        }

        /* Animación: desvanecimiento y desplazamiento hacia arriba */
        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Sección lateral: Menú de selección -->
    <aside class="rh-menu">
        <h2>Parque vehicular</h2>

        <div class="rh-nav" id="nav-item-editable" onclick="mostrarEditable()">
            Editable
        </div>

        <hr style="border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0 12px; width: 100%;">

        <h3>Unidades</h3>
        <div id="sidebar-vehicles" style="display: flex; flex-direction: column; gap: 4px;">
            @foreach($vehiculos as $v)
                <div class="rh-nav vehicle-nav-item" id="nav-item-{{ $v->id }}" onclick="cargarVehiculo({{ $v->id }})">
                    {{ $v->nombre }}
                </div>
            @endforeach
        </div>

        <a href="{{ url('/') }}" class="btn-back">← Regresar al inicio</a>
    </aside>

    <!-- Sección derecha: Renderizado dinámico de contenido -->
    <main class="content" id="contenido">
        <!-- Cargado de forma dinámica -->
    </main>

</div>

<!-- MODAL VEHÍCULO (NUEVO / EDITAR) -->
<div class="modal-overlay" id="modal-vehiculo">
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="modal-vehiculo-title">Registrar Vehículo</h3>
            <button class="modal-close" onclick="cerrarModales()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-vehiculo">
                <input type="hidden" id="vehiculo-id">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nombre de la Unidad *</label>
                        <input type="text" id="vehiculo-nombre" required placeholder="Ej: Unidad 1, U-045">
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" id="vehiculo-marca" placeholder="Ej: Toyota">
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" id="vehiculo-modelo" placeholder="Ej: Hilux 2022">
                    </div>
                    <div class="form-group">
                        <label>Placas</label>
                        <input type="text" id="vehiculo-placas" placeholder="Ej: ABC-123-D">
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" id="vehiculo-color" placeholder="Ej: Blanco">
                    </div>
                    <div class="form-group">
                        <label>Transmisión</label>
                        <select id="vehiculo-transmision">
                            <option value="">Seleccionar</option>
                            <option value="Manual">Manual</option>
                            <option value="Automática">Automática</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Número de Serie</label>
                        <input type="text" id="vehiculo-numero_serie" placeholder="Ej: JT123456789MX">
                    </div>
                    <div class="form-group">
                        <label>Número Económico</label>
                        <input type="text" id="vehiculo-numero_economico" placeholder="Ej: U-045">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Compra</label>
                        <input type="date" id="vehiculo-fecha_compra">
                    </div>
                    <div class="form-group">
                        <label>Seguro del Auto</label>
                        <input type="text" id="vehiculo-seguro_auto" placeholder="Ej: Qualitas">
                    </div>
                    <div class="form-group">
                        <label>Teléfono Seguro</label>
                        <input type="text" id="vehiculo-telefono_seguro" placeholder="Ej: 55 1234 5678">
                    </div>
                    <div class="form-group">
                        <label>Inicio de Seguro</label>
                        <input type="date" id="vehiculo-inicio_seguro">
                    </div>
                    <div class="form-group">
                        <label>Caducidad de Seguro</label>
                        <input type="date" id="vehiculo-caducidad_seguro">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModales()">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarVehiculo()">Guardar</button>
        </div>
    </div>
</div>

<!-- MODAL SERVICIO (NUEVO / EDITAR) -->
<div class="modal-overlay" id="modal-servicio">
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="modal-servicio-title">Registrar Mantenimiento / Servicio</h3>
            <button class="modal-close" onclick="cerrarModales()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-servicio">
                <input type="hidden" id="servicio-id">
                <input type="hidden" id="servicio-vehiculo-id">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Fecha de Solicitud</label>
                        <input type="date" id="servicio-fecha" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Solicitud de Servicio *</label>
                        <input type="text" id="servicio-solicitud" required placeholder="Ej: Mantenimiento preventivo">
                    </div>
                    
                    <!-- Cotizaciones Opciones -->
                    <div class="form-group full-width">
                        <label>Opciones de Cotización (Taller y Costo)</label>
                        <div id="cotizaciones-container">
                            <!-- Filas dinámicas -->
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="agregarFilaCotizacion()" style="padding: 4px 10px; margin-top: 6px; font-size:12px;">+ Agregar Opción</button>
                    </div>

                    <div class="form-group">
                        <label>Cotización Aceptada</label>
                        <input type="text" id="servicio-cotizacion_aceptada" placeholder="Ej: Taller López - $8,500">
                    </div>
                    <div class="form-group">
                        <label>Fecha Autorización</label>
                        <input type="date" id="servicio-fecha_autorizacion">
                    </div>
                    <div class="form-group">
                        <label>Fecha Realización</label>
                        <input type="date" id="servicio-fecha_realizacion">
                    </div>
                    <div class="form-group">
                        <label>Proveedor</label>
                        <input type="text" id="servicio-proveedor" placeholder="Ej: Taller López">
                    </div>
                    <div class="form-group">
                        <label>Costo Final ($)</label>
                        <input type="number" step="0.01" id="servicio-costo" placeholder="Ej: 8500.00">
                    </div>
                    <div class="form-group">
                        <label>Factura</label>
                        <input type="text" id="servicio-factura" placeholder="Ej: FAC-00125">
                    </div>
                    <div class="form-group full-width">
                        <label>Observación</label>
                        <textarea id="servicio-observacion" rows="3" placeholder="Detalle de las reparaciones u observaciones..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModales()">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarServicio()">Guardar</button>
        </div>
    </div>
</div>

<script>
    let activeVehiculoId = null;

    // Encabezado para peticiones fetch con CSRF token
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
    };

    /**
     * Carga la información de un vehículo y sus servicios asociados vía AJAX
     */
    function cargarVehiculo(id) {
        activeVehiculoId = id;
        
        // Marcar activo en la barra lateral
        document.querySelectorAll('.vehicle-nav-item').forEach(item => item.classList.remove('active'));
        document.getElementById('nav-item-editable').classList.remove('active');
        const activeNav = document.getElementById(`nav-item-${id}`);
        if(activeNav) activeNav.classList.add('active');

        fetch(`/vehiculos/${id}`, { headers })
            .then(res => res.json())
            .then(vehiculo => {
                let infoCompra = vehiculo.fecha_compra || 'N/A';
                let inicioSeg = vehiculo.inicio_seguro || 'N/A';
                let finSeg = vehiculo.caducidad_seguro || 'N/A';

                // Generar HTML de la Ficha Técnica
                let fichaHtml = `
                    <div class="card">
                        <h2>Información del vehículo</h2>
                        <div class="info-grid">
                            <div class="info-item"><span>Nombre</span>${vehiculo.nombre}</div>
                            <div class="info-item"><span>Marca</span>${vehiculo.marca || 'N/A'}</div>
                            <div class="info-item"><span>Modelo</span>${vehiculo.modelo || 'N/A'}</div>
                            <div class="info-item"><span>Placas</span>${vehiculo.placas || 'N/A'}</div>
                            <div class="info-item"><span>Color</span>${vehiculo.color || 'N/A'}</div>
                            <div class="info-item"><span>Transmisión</span>${vehiculo.transmision || 'N/A'}</div>
                            <div class="info-item"><span>Número de serie</span>${vehiculo.numero_serie || 'N/A'}</div>
                            <div class="info-item"><span>Número económico</span>${vehiculo.numero_economico || 'N/A'}</div>
                            <div class="info-item"><span>Fecha de compra</span>${infoCompra}</div>
                            <div class="info-item"><span>Seguro del auto</span>${vehiculo.seguro_auto || 'N/A'}</div>
                            <div class="info-item"><span>Teléfono</span>${vehiculo.telefono_seguro || 'N/A'}</div>
                            <div class="info-item"><span>Inicio de seguro</span>${inicioSeg}</div>
                            <div class="info-item"><span>Caducidad del seguro</span>${finSeg}</div>
                        </div>
                        <div style="margin-top: 24px; display:flex; gap: 8px;">
                            <button class="btn btn-secondary" onclick="abrirModalEditarVehiculo(${JSON.stringify(vehiculo).replace(/"/g, '&quot;')})">Editar Ficha</button>
                            <button class="btn btn-danger" onclick="eliminarVehiculo(${vehiculo.id})">Eliminar Unidad</button>
                        </div>
                    </div>
                `;

                // Generar HTML de la Tabla de Servicios
                let tablaServiciosHtml = `
                    <div class="card" style="overflow-x:auto;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <h2>Historial de Servicios - ${vehiculo.nombre}</h2>
                            <button class="btn btn-primary" onclick="abrirModalNuevoServicio(${vehiculo.id})">+ Registrar Servicio</button>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Solicitud de servicio</th>
                                    <th>Cotizaciones</th>
                                    <th>Cotización Aceptada</th>
                                    <th>Fecha Autorización</th>
                                    <th>Fecha Realización</th>
                                    <th>Observación</th>
                                    <th>Proveedor</th>
                                    <th>Costo</th>
                                    <th>Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (vehiculo.servicios && vehiculo.servicios.length > 0) {
                    vehiculo.servicios.forEach(s => {
                        // Crear dropdown para cotizaciones
                        let optionsHtml = `<option value="">Seleccionar</option>`;
                        let cotizaciones = [];
                        try {
                            cotizaciones = typeof s.cotizacion_opciones === 'string' ? JSON.parse(s.cotizacion_opciones) : s.cotizacion_opciones;
                        } catch(e){}

                        if (Array.isArray(cotizaciones)) {
                            cotizaciones.forEach(c => {
                                let optVal = `${c.taller} - $${parseFloat(c.costo).toLocaleString()}`;
                                let selected = (s.cotizacion_aceptada === optVal) ? 'selected' : '';
                                optionsHtml += `<option value="${optVal}" ${selected}>${optVal}</option>`;
                            });
                        }

                        let selectCotizacion = `
                            <select onchange="actualizarCotizacionAceptada(${s.id}, this.value)">
                                ${optionsHtml}
                            </select>
                        `;

                        let fechaS = s.fecha || 'N/A';
                        let fechaA = s.fecha_autorizacion || 'N/A';
                        let fechaR = s.fecha_realizacion || 'N/A';
                        let costoS = s.costo ? `$${parseFloat(s.costo).toLocaleString()}` : 'N/A';

                        tablaServiciosHtml += `
                            <tr>
                                <td>${fechaS}</td>
                                <td>${s.solicitud_servicio || 'N/A'}</td>
                                <td>${selectCotizacion}</td>
                                <td>${s.cotizacion_aceptada || 'N/A'}</td>
                                <td>${fechaA}</td>
                                <td>${fechaR}</td>
                                <td>${s.observacion || 'N/A'}</td>
                                <td>${s.proveedor || 'N/A'}</td>
                                <td>${costoS}</td>
                                <td>${s.factura || 'N/A'}</td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <button class="btn btn-primary" style="padding:0; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalEditarServicio(${JSON.stringify(s).replace(/"/g, '&quot;')})" title="Editar Servicio"><i class="bi bi-pencil-fill" style="font-size:12px; color:#fff;"></i></button>
                                        <button class="btn btn-danger" style="padding:0; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="eliminarServicio(${s.id})" title="Eliminar Servicio"><i class="bi bi-trash" style="font-size:13px; color:#fff;"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tablaServiciosHtml += `
                        <tr>
                            <td colspan="11" style="text-align:center;color:#64748b;">No hay servicios registrados para esta unidad.</td>
                        </tr>
                    `;
                }

                tablaServiciosHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                document.getElementById('contenido').innerHTML = fichaHtml + tablaServiciosHtml;
            });
    }

    /**
     * Muestra la vista "Editable" que lista todos los vehículos con accesos rápidos
     */
    function mostrarEditable() {
        activeVehiculoId = null;
        document.querySelectorAll('.vehicle-nav-item').forEach(item => item.classList.remove('active'));
        document.getElementById('nav-item-editable').classList.add('active');

        fetch('/vehiculos', { headers })
            .then(res => res.json())
            .then(vehiculos => {
                let html = `
                    <div class="card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                            <h2>Administración de Parque Vehicular (Editable)</h2>
                            <button class="btn btn-success" onclick="abrirModalNuevoVehiculo()">+ Nuevo Vehículo</button>
                        </div>
                        <p>Aquí puedes gestionar todas las unidades registradas en el sistema de pruebas.</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Unidad</th>
                                    <th>Marca / Modelo</th>
                                    <th>Placas</th>
                                    <th>Número Económico</th>
                                    <th>Seguro</th>
                                    <th>Vencimiento Seguro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if(vehiculos.length > 0) {
                    vehiculos.forEach(v => {
                        html += `
                            <tr>
                                <td><strong>${v.nombre}</strong></td>
                                <td>${v.marca || ''} ${v.modelo || ''}</td>
                                <td>${v.placas || 'N/A'}</td>
                                <td>${v.numero_economic || v.numero_economico || 'N/A'}</td>
                                <td>${v.seguro_auto || 'N/A'}</td>
                                <td>${v.caducidad_seguro || 'N/A'}</td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <button class="btn btn-secondary" style="padding:0; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="cargarVehiculo(${v.id})" title="Ver Ficha"><i class="bi bi-eye-fill" style="font-size:13px; color:#fff;"></i></button>
                                        <button class="btn btn-primary" style="padding:0; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="abrirModalEditarVehiculo(${JSON.stringify(v).replace(/"/g, '&quot;')})" title="Editar Unidad"><i class="bi bi-pencil-fill" style="font-size:12px; color:#fff;"></i></button>
                                        <button class="btn btn-danger" style="padding:0; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; cursor:pointer;" onclick="eliminarVehiculo(${v.id})" title="Eliminar Unidad"><i class="bi bi-trash" style="font-size:13px; color:#fff;"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="7" style="text-align:center;color:#64748b;">No hay vehículos registrados.</td>
                        </tr>
                    `;
                }

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                document.getElementById('contenido').innerHTML = html;
            });
    }

    // --- MODALES Y OPERACIONES VEHÍCULO ---

    function abrirModalNuevoVehiculo() {
        document.getElementById('form-vehiculo').reset();
        document.getElementById('vehiculo-id').value = '';
        document.getElementById('modal-vehiculo-title').textContent = 'Registrar Vehículo';
        document.getElementById('modal-vehiculo').classList.add('active');
    }

    function abrirModalEditarVehiculo(vehiculo) {
        document.getElementById('vehiculo-id').value = vehiculo.id;
        document.getElementById('vehiculo-nombre').value = vehiculo.nombre;
        document.getElementById('vehiculo-marca').value = vehiculo.marca || '';
        document.getElementById('vehiculo-modelo').value = vehiculo.modelo || '';
        document.getElementById('vehiculo-placas').value = vehiculo.placas || '';
        document.getElementById('vehiculo-color').value = vehiculo.color || '';
        document.getElementById('vehiculo-transmision').value = vehiculo.transmision || '';
        document.getElementById('vehiculo-numero_serie').value = vehiculo.numero_serie || '';
        document.getElementById('vehiculo-numero_economico').value = vehiculo.numero_economico || '';
        document.getElementById('vehiculo-fecha_compra').value = vehiculo.fecha_compra || '';
        document.getElementById('vehiculo-seguro_auto').value = vehiculo.seguro_auto || '';
        document.getElementById('vehiculo-telefono_seguro').value = vehiculo.telefono_seguro || '';
        document.getElementById('vehiculo-inicio_seguro').value = vehiculo.inicio_seguro || '';
        document.getElementById('vehiculo-caducidad_seguro').value = vehiculo.caducidad_seguro || '';

        document.getElementById('modal-vehiculo-title').textContent = 'Editar Vehículo';
        document.getElementById('modal-vehiculo').classList.add('active');
    }

    function guardarVehiculo() {
        const id = document.getElementById('vehiculo-id').value;
        const url = id ? `/vehiculos/${id}` : '/vehiculos';
        const method = id ? 'PUT' : 'POST';

        const data = {
            nombre: document.getElementById('vehiculo-nombre').value,
            marca: document.getElementById('vehiculo-marca').value,
            modelo: document.getElementById('vehiculo-modelo').value,
            placas: document.getElementById('vehiculo-placas').value,
            color: document.getElementById('vehiculo-color').value,
            transmision: document.getElementById('vehiculo-transmision').value,
            numero_serie: document.getElementById('vehiculo-numero_serie').value,
            numero_economico: document.getElementById('vehiculo-numero_economico').value,
            fecha_compra: document.getElementById('vehiculo-fecha_compra').value || null,
            seguro_auto: document.getElementById('vehiculo-seguro_auto').value,
            telefono_seguro: document.getElementById('vehiculo-telefono_seguro').value,
            inicio_seguro: document.getElementById('vehiculo-inicio_seguro').value || null,
            caducidad_seguro: document.getElementById('vehiculo-caducidad_seguro').value || null,
        };

        if(!data.nombre) {
            alert("El nombre de la unidad es requerido.");
            return;
        }

        fetch(url, {
            method,
            headers,
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                cerrarModales();
                recargarSidebar(res.vehiculo.id);
            }
        });
    }

    function eliminarVehiculo(id) {
        if(!confirm("¿Estás seguro de que deseas eliminar este vehículo y todo su historial de mantenimiento?")) return;

        fetch(`/vehiculos/${id}`, {
            method: 'DELETE',
            headers
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert(res.message);
                recargarSidebar(null);
            }
        });
    }

    // --- MODALES Y OPERACIONES SERVICIO ---

    function abrirModalNuevoServicio(vehiculoId) {
        document.getElementById('form-servicio').reset();
        document.getElementById('servicio-id').value = '';
        document.getElementById('servicio-vehiculo-id').value = vehiculoId;
        document.getElementById('cotizaciones-container').innerHTML = '';
        agregarFilaCotizacion(); // agregar primera fila vacía
        document.getElementById('modal-servicio-title').textContent = 'Registrar Servicio / Mantenimiento';
        document.getElementById('modal-servicio').classList.add('active');
    }

    function abrirModalEditarServicio(servicio) {
        document.getElementById('servicio-id').value = servicio.id;
        document.getElementById('servicio-vehiculo-id').value = servicio.vehiculo_id;
        document.getElementById('servicio-fecha').value = servicio.fecha || '';
        document.getElementById('servicio-solicitud').value = servicio.solicitud_servicio || '';
        document.getElementById('servicio-cotizacion_aceptada').value = servicio.cotizacion_aceptada || '';
        document.getElementById('servicio-fecha_autorizacion').value = servicio.fecha_autorizacion || '';
        document.getElementById('servicio-fecha_realizacion').value = servicio.fecha_realizacion || '';
        document.getElementById('servicio-proveedor').value = servicio.proveedor || '';
        document.getElementById('servicio-costo').value = servicio.costo || '';
        document.getElementById('servicio-factura').value = servicio.factura || '';
        document.getElementById('servicio-observacion').value = servicio.observacion || '';

        // Limpiar y cargar cotizaciones
        const container = document.getElementById('cotizaciones-container');
        container.innerHTML = '';
        let cotizaciones = [];
        try {
            cotizaciones = typeof servicio.cotizacion_opciones === 'string' ? JSON.parse(servicio.cotizacion_opciones) : servicio.cotizacion_opciones;
        } catch(e){}

        if (Array.isArray(cotizaciones) && cotizaciones.length > 0) {
            cotizaciones.forEach(c => agregarFilaCotizacion(c.taller, c.costo));
        } else {
            agregarFilaCotizacion();
        }

        document.getElementById('modal-servicio-title').textContent = 'Editar Servicio / Mantenimiento';
        document.getElementById('modal-servicio').classList.add('active');
    }

    function agregarFilaCotizacion(taller = '', costo = '') {
        const container = document.getElementById('cotizaciones-container');
        const div = document.createElement('div');
        div.className = 'quote-row';
        div.innerHTML = `
            <input type="text" class="quote-taller" placeholder="Nombre Taller/Proveedor" value="${taller}" required>
            <input type="number" class="quote-costo" placeholder="Costo ($)" value="${costo}" required>
            <button type="button" class="btn btn-danger" style="padding:6px; line-height:1;" onclick="this.parentElement.remove()">&times;</button>
        `;
        container.appendChild(div);
    }

    function guardarServicio() {
        const id = document.getElementById('servicio-id').value;
        const vehiculoId = document.getElementById('servicio-vehiculo-id').value;
        const url = id ? `/vehiculos/servicios/${id}` : `/vehiculos/${vehiculoId}/servicios`;
        const method = id ? 'PUT' : 'POST';

        // Recolectar cotizaciones
        const cotizaciones = [];
        document.querySelectorAll('.quote-row').forEach(row => {
            const taller = row.querySelector('.quote-taller').value;
            const costo = row.querySelector('.quote-costo').value;
            if(taller && costo) {
                cotizaciones.push({ taller, costo: parseFloat(costo) });
            }
        });

        const data = {
            fecha: document.getElementById('servicio-fecha').value || null,
            solicitud_servicio: document.getElementById('servicio-solicitud').value,
            cotizaciones: cotizaciones,
            cotizacion_aceptada: document.getElementById('servicio-cotizacion_aceptada').value,
            fecha_autorizacion: document.getElementById('servicio-fecha_autorizacion').value || null,
            fecha_realizacion: document.getElementById('servicio-fecha_realizacion').value || null,
            proveedor: document.getElementById('servicio-proveedor').value,
            costo: document.getElementById('servicio-costo').value || null,
            factura: document.getElementById('servicio-factura').value,
            observacion: document.getElementById('servicio-observacion').value
        };

        if(!data.solicitud_servicio) {
            alert("La solicitud de servicio es requerida.");
            return;
        }

        fetch(url, {
            method,
            headers,
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                cerrarModales();
                cargarVehiculo(vehiculoId);
            }
        });
    }

    function eliminarServicio(id) {
        if(!confirm("¿Estás seguro de que deseas eliminar este servicio del historial?")) return;

        fetch(`/vehiculos/servicios/${id}`, {
            method: 'DELETE',
            headers
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert(res.message);
                if(activeVehiculoId) {
                    cargarVehiculo(activeVehiculoId);
                } else {
                    mostrarEditable();
                }
            }
        });
    }

    /**
     * Actualiza rápidamente la cotización aceptada al cambiar el dropdown en la tabla de servicios
     */
    function actualizarCotizacionAceptada(servicioId, valor) {
        fetch(`/vehiculos/${activeVehiculoId}`, { headers })
            .then(res => res.json())
            .then(vehiculo => {
                const servicio = vehiculo.servicios.find(s => s.id === servicioId);
                if(!servicio) return;

                // Parse quotes
                let cotizaciones = [];
                try {
                    cotizaciones = typeof servicio.cotizacion_opciones === 'string' ? JSON.parse(servicio.cotizacion_opciones) : servicio.cotizacion_opciones;
                } catch(e){}

                // Buscar proveedor y costo basados en la selección
                let autoProveedor = '';
                let autoCosto = null;
                if(valor) {
                    const match = cotizaciones.find(c => `${c.taller} - $${parseFloat(c.costo).toLocaleString()}` === valor);
                    if(match) {
                        autoProveedor = match.taller;
                        autoCosto = match.costo;
                    }
                }

                // Enviar actualización
                const updatedData = {
                    fecha: servicio.fecha,
                    solicitud_servicio: servicio.solicitud_servicio,
                    cotizaciones: cotizaciones,
                    cotizacion_aceptada: valor,
                    fecha_autorizacion: servicio.fecha_autorizacion || new Date().toISOString().split('T')[0],
                    fecha_realizacion: servicio.fecha_realizacion,
                    proveedor: autoProveedor || servicio.proveedor,
                    costo: autoCosto || servicio.costo,
                    factura: servicio.factura,
                    observacion: servicio.observacion
                };

                fetch(`/vehiculos/servicios/${servicioId}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(updatedData)
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        cargarVehiculo(activeVehiculoId);
                    }
                });
            });
    }

    // --- FUNCIONES COMUNES ---

    function cerrarModales() {
        document.querySelectorAll('.modal-overlay').forEach(modal => modal.classList.remove('active'));
    }

    function recargarSidebar(selectId) {
        fetch('/vehiculos', { headers })
            .then(res => res.json())
            .then(vehiculos => {
                let html = '';
                vehiculos.forEach(v => {
                    html += `
                        <div class="rh-nav vehicle-nav-item" id="nav-item-${v.id}" onclick="cargarVehiculo(${v.id})">
                            ${v.nombre}
                        </div>
                    `;
                });
                document.getElementById('sidebar-vehicles').innerHTML = html;

                if (selectId) {
                    cargarVehiculo(selectId);
                } else if (document.getElementById('nav-item-editable').classList.contains('active')) {
                    mostrarEditable();
                } else if(vehiculos.length > 0) {
                    cargarVehiculo(vehiculos[0].id);
                } else {
                    document.getElementById('contenido').innerHTML = `
                        <div class="card">
                            <h2>Parque vehicular</h2>
                            <p>Seleccione una unidad en el menú lateral o agregue una nueva para comenzar.</p>
                        </div>
                    `;
                }
            });
    }

    // Auto-cargar la vista "Editable" por defecto al entrar a la página
    window.addEventListener('DOMContentLoaded', () => {
        mostrarEditable();
    });
</script>

</body>
</html>
