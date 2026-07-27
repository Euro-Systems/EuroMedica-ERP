@extends('layouts.app')

@section('title','Recursos Humanos')

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

/* Contenedor principal con Flexbox para separar menú lateral y contenido */
.rh-container{
    display:flex;
    width:100vw;
    height:100vh;
    background:#f4f6f9;
}

/* Estilos del menú lateral (Sidebar) */
.rh-menu {
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
.rh-nav{
    padding:12px;
    border-radius:8px;
    margin-bottom:10px;
    cursor:pointer;
}

/* Estado activo para el menú lateral */
.rh-nav.active{
    background:#fff;
    color:#1e3a8a;
    font-weight:bold;
}

/* Área de contenido principal */
.rh-content{
    flex:1;
    padding:14px;
    height:100%;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    min-height:0;
}

/* Sistema de pestañas (Tabs) superiores */
.tabs{
    display:flex;
    gap:10px;
    margin-bottom:10px;
    flex-shrink:0;
}

.tab{
    padding:10px 16px;
    border-radius:10px 10px 0 0;
    background:#e5e7eb;
    cursor:pointer;
}

.tab.active{
    background:#fff;
    color:#1e3a8a;
    border-bottom:3px solid #3b82f6;
}

/* Contenedor dinámico donde se inyecta el HTML mediante JavaScript */
#contenido{
    flex:1;
    overflow-y:auto;
    min-height:0;
    padding-bottom:120px;
}

/* Estilos de tarjetas para separar secciones visualmente */
.rh-card {
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 14px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease-in-out;
}

.rh-card h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1e3a8a;
    margin-top: 0;
    margin-bottom: 10px;
}

.rh-card h3 {
    font-size: 15px;
    font-weight: 700;
    color: #1e3a8a;
    margin-top: 0;
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid #e2e8f0;
}

/* Layout de la ficha técnica usando Grid (2 columnas) */
.ficha-wrap{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
    align-items:start;
}

.col{
    display:flex;
    flex-direction:column;
    gap:12px;
    min-height:0;
}

/* Cuadrícula para formularios de datos de empleados */
.empleado-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:10px;
}

.empleado-grid b {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.empleado-grid input,
.empleado-grid select,
.empleado-grid textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background-color: #f8fafc;
    color: #0f172a;
    font-size: 13px;
    font-family: inherit;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
}

.empleado-grid input:focus,
.empleado-grid select:focus,
.empleado-grid textarea:focus {
    outline: none;
    border-color: #2563eb;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.empleado-grid input[readonly] {
    background-color: #e2e8f0;
    color: #64748b;
    cursor: not-allowed;
}

.radio-group{
    display:flex;
    gap:12px;
    align-items:center;
}

/* Estilos para tablas de datos */
.rh-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.rh-table th {
    background: linear-gradient(130deg, #1e3a8a, #2563eb);
    color: #ffffff;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border: none;
}

.rh-table td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 13px;
}

.rh-table tr:last-child td {
    border-bottom: none;
}

.rh-table tr:hover td {
    background-color: #f8fafc;
}

.btn-ver {
    background-color: #2563eb;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-ver:hover {
    filter: brightness(1.08);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}

.btn-ver:active {
    transform: translateY(0);
}

.ficha-wrap{
    min-height:0;
}

.col{
    min-height:0;
}

/* Modal o Visor de imágenes a pantalla completa */
#visor{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,.9);
display:none;
align-items:center;
justify-content:center;
z-index:9999;
}

#visor img{
max-width:90%;
max-height:90%;
}

/* Estilos para la lista de observaciones/bitácora */
.obs-item{
border:1px solid #e5e7eb;
border-radius:8px;
padding:10px;
margin-bottom:8px;
background:#f9fafb;
}

.obs-fecha{
font-size:12px;
color:#6b7280;
margin-bottom:5px;
}

.sticky-acciones{
    position: fixed;
    bottom: 0;
    right: 0;
    width: calc(100% - 250px); /* Restando el sidebar */
    z-index: 1000;
    background: white;
    padding: 15px;
    margin: 0;
    border-top: 2px solid #e5e7eb;
    box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .sticky-acciones {
        width: 100%;
        left: 0;
    }
}

/* Botón para regresar al ERP */
.btn-regresar {
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

.btn-regresar:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
}

</style>

<div class="rh-container">

<!-- Menú de navegación lateral -->
<aside class="rh-menu">
    <h2>Recursos Humanos</h2>
    @if(Auth::user()->hasPermission('rh_agendar_citas') || Auth::user()->hasPermission('rh_ver_citas_realizadas') || Auth::user()->hasPermission('administracion_rh'))
        <div class="rh-nav" onclick="mostrar('citas')">Agendar Cita</div>
    @endif
    @if(Auth::user()->hasPermission('rh_ver_editar_candidatos') || Auth::user()->hasPermission('rh_aprobar_candidato') || Auth::user()->hasPermission('administracion_rh'))
        <div class="rh-nav" onclick="mostrar('candidatos')">Candidatos</div>
    @endif
    @if(Auth::user()->hasPermission('rh_ver_editar_empleados') || Auth::user()->hasPermission('administracion_rh'))
        <div class="rh-nav" onclick="mostrar('practicantes')">Practicantes</div>
        <div class="rh-nav" onclick="mostrar('empleados')">Empleados</div>
    @endif
    @if(Auth::user()->hasPermission('rh_gestion_vacaciones') || Auth::user()->hasPermission('administracion_rh'))
        <div class="rh-nav" onclick="mostrar('vacaciones')">Vacaciones</div>
    @endif
    @if(Auth::user()->hasPermission('rh_gestion_contratos') || Auth::user()->hasPermission('administracion_rh'))
        <div class="rh-nav" onclick="mostrar('contratos')">Contratos</div>
    @endif

    <a href="{{ url('administracion') }}" class="btn-regresar">
        ⬅ Volver al Menú Principal
    </a>
</aside>

<main class="rh-content">
<!-- Espacio donde se renderiza la vista seleccionada -->
<div id="contenido"></div>

</main>
</div>

<!-- Contenedor del visor de documentos escaneados -->
<div id="visor" onclick="this.style.display='none'">
<img id="imgGrande">
</div> 

<!-- Modal Vacaciones -->
<div id="modalVacaciones" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:25px;border-radius:12px;width:420px;max-width:90%;">
    <h3 style="margin:0 0 15px; color:#1e3a8a;"><i class="bi bi-calendar-plus me-2"></i>Solicitar Vacaciones / Permiso</h3>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Empleado</b>
        <select id="v_emp_id" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;font-size:13px;">
          <option value="">-- Seleccionar empleado --</option>
        </select>
      </div>
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Fecha Inicio</b><input type="date" id="v_inicio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;"></div>
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Fecha Fin</b><input type="date" id="v_fin" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;"></div>
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Días a descontar</b><input type="number" id="v_dias" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;"></div>
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Tipo</b><select id="v_tipo" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;"><option>Vacaciones</option><option>Permiso</option></select></div>
      <div><b style="font-size:12px;color:#475569;text-transform:uppercase;">Persona que cubre</b><input type="text" id="v_cobertura" placeholder="Nombre completo" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:4px;"></div>
    </div>
    <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px;">
      <button class="btn-ver" style="background:#6b7280;" onclick="document.getElementById('modalVacaciones').style.display='none'">Cancelar</button>
      <button class="btn-ver" style="background:#22c55e;" onclick="guardarNuevaVacacion()">Guardar Solicitud</button>
    </div>
  </div>
</div>

<!-- Modal Nuevo Empleado Directo - EXPEDIENTE COMPLETO -->
<div id="modalNuevoEmpleado" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:1100;align-items:center;justify-content:center;backdrop-filter:blur(5px);">
  <div style="background:#fff;padding:0;border-radius:16px;width:680px;max-width:96%;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 50px rgba(0,0,0,0.3);overflow:hidden;">

    <!-- Header del modal -->
    <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:20px 25px;flex-shrink:0;">
      <h3 style="margin:0;color:#fff;font-size:18px;font-weight:bold;"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Empleado</h3>
      <small style="color:#bfdbfe;">Llena el expediente completo. Los campos opcionales se pueden completar después.</small>
    </div>

    <!-- Cuerpo scrolleable -->
    <div style="padding:22px 25px;overflow-y:auto;flex:1;">

      <!-- SECCIÓN: Datos Personales -->
      <div style="margin-bottom:18px;">
        <h4 style="color:#1e3a8a;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px;margin-bottom:12px;"><i class="bi bi-person-fill me-2"></i>Datos Personales</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div style="grid-column:span 3;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Nombre(s) *</b><input type="text" id="ne_nombre" placeholder="Nombre(s)" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Apellido Paterno *</b><input type="text" id="ne_ap" placeholder="Apellido Paterno" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Apellido Materno</b><input type="text" id="ne_am" placeholder="Apellido Materno" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Fecha Nacimiento</b><input type="date" id="ne_nacimiento" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">NSS</b><input type="text" id="ne_nss" placeholder="Número Seguro Social" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">RFC</b><input type="text" id="ne_rfc" placeholder="RFC" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">CURP</b><input type="text" id="ne_curp" placeholder="CURP" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Género</b>
            <select id="ne_sexo" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;">
              <option value="Indefinido">No especificado</option>
              <option value="Hombre">Hombre</option>
              <option value="Mujer">Mujer</option>
            </select>
          </div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Estado Civil</b><input type="text" id="ne_estado_civil" placeholder="Soltero/a, Casado/a..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Celular</b><input type="text" id="ne_celular" placeholder="10 dígitos" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Correo Electrónico</b><input type="email" id="ne_correo" placeholder="correo@ejemplo.com" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div style="grid-column:span 3;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Dirección</b><input type="text" id="ne_direccion" placeholder="Calle, número, colonia, municipio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Tipo de Sangre</b><input type="text" id="ne_tipo_sangre" placeholder="O+, A-, etc." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Alergias / Med.</b><input type="text" id="ne_alergias" placeholder="Ninguna" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Talla Uniforme</b>
            <select id="ne_talla" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;">
              <option value="S">Chica (S)</option>
              <option value="M" selected>Mediana (M)</option>
              <option value="L">Grande (L)</option>
              <option value="XL">Extra Grande (XL)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- SECCIÓN: Datos Laborales -->
      <div style="margin-bottom:18px;">
        <h4 style="color:#1e3a8a;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px;margin-bottom:12px;"><i class="bi bi-briefcase-fill me-2"></i>Datos Laborales</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Puesto *</b><input type="text" id="ne_puesto" placeholder="Ej. Enfermera Jefe" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Empresa / Área</b><input type="text" id="ne_empresa" placeholder="Ej. EuroMedica" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Fecha de Ingreso *</b><input type="date" id="ne_fecha" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Alta IMSS</b><input type="date" id="ne_alta_imss" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">CLABE Bancaria</b><input type="text" id="ne_clabe" placeholder="18 dígitos" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Canal de Captación</b><input type="text" id="ne_canal" placeholder="Facebook, referido, etc." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
        </div>
      </div>

      <!-- SECCIÓN: Contacto de Emergencia -->
      <div style="margin-bottom:10px;">
        <h4 style="color:#1e3a8a;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px;margin-bottom:12px;"><i class="bi bi-telephone-fill me-2"></i>Contacto de Emergencia</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Nombre del Contacto</b><input type="text" id="ne_contacto" placeholder="Nombre completo" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Parentesco</b><input type="text" id="ne_parentesco" placeholder="Madre, Esposo/a..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Teléfono 1</b><input type="text" id="ne_tel1" placeholder="10 dígitos" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Teléfono 2 (Opcional)</b><input type="text" id="ne_tel2" placeholder="10 dígitos" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
        </div>
      </div>
    </div>

    <!-- Footer con botones -->
    <div style="padding:16px 25px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;background:#f8fafc;">
      <button class="btn-ver" style="background:#6b7280;" onclick="document.getElementById('modalNuevoEmpleado').style.display='none'"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
      <button class="btn-ver" style="background:#16a34a;" onclick="guardarNuevoEmpleadoDirecto()"><i class="bi bi-floppy-fill me-1"></i> Guardar y Abrir Expediente</button>
    </div>
  </div>
</div>

<!-- Modal Nuevo Practicante Directo - EXPEDIENTE COMPLETO -->
<div id="modalNuevoPracticante" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:1100;align-items:center;justify-content:center;backdrop-filter:blur(5px);">
  <div style="background:#fff;padding:0;border-radius:16px;width:680px;max-width:96%;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 50px rgba(0,0,0,0.3);overflow:hidden;">

    <!-- Header del modal -->
    <div style="background:linear-gradient(135deg,#0f766e,#14b8a6);padding:20px 25px;flex-shrink:0;">
      <h3 style="margin:0;color:#fff;font-size:18px;font-weight:bold;"><i class="bi bi-mortarboard-fill me-2"></i>Registrar Nuevo Practicante</h3>
      <small style="color:#ccfbf1;">Llena el expediente completo. Los campos opcionales se pueden completar después.</small>
    </div>

    <!-- Cuerpo scrolleable -->
    <div style="padding:22px 25px;overflow-y:auto;flex:1;">

      <!-- SECCIÓN: Datos Personales -->
      <div style="margin-bottom:18px;">
        <h4 style="color:#0f766e;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px;margin-bottom:12px;"><i class="bi bi-person-fill me-2"></i>Datos Personales</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div style="grid-column:span 3;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Nombre(s) *</b><input type="text" id="np_nombre" placeholder="Nombre(s)" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Apellido Paterno *</b><input type="text" id="np_ap" placeholder="Apellido Paterno" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Apellido Materno</b><input type="text" id="np_am" placeholder="Apellido Materno" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Fecha Nacimiento</b><input type="date" id="np_nacimiento" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">NSS</b><input type="text" id="np_nss" placeholder="Número Seguro Social" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">RFC</b><input type="text" id="np_rfc" placeholder="RFC" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">CURP</b><input type="text" id="np_curp" placeholder="CURP" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Celular</b><input type="text" id="np_celular" placeholder="10 dígitos" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Correo Electrónico</b><input type="email" id="np_correo" placeholder="correo@ejemplo.com" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div style="grid-column:span 3;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Dirección</b><input type="text" id="np_direccion" placeholder="Calle, número, colonia, municipio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Tipo de Sangre</b><input type="text" id="np_tipo_sangre" placeholder="O+, A-, etc." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Alergias</b><input type="text" id="np_alergias" placeholder="Ninguna" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Nivel de Inglés</b><input type="text" id="np_nivel_ingles" placeholder="Básico, Intermedio..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Estado Civil</b><input type="text" id="np_estado_civil" placeholder="Soltero/a, Casado/a..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Talla Uniforme</b>
            <select id="np_talla" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;">
              <option value="S">Chica (S)</option>
              <option value="M" selected>Mediana (M)</option>
              <option value="L">Grande (L)</option>
              <option value="XL">Extra Grande (XL)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- SECCIÓN: Datos Académicos / Prácticas -->
      <div style="margin-bottom:10px;">
        <h4 style="color:#0f766e;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px;margin-bottom:12px;"><i class="bi bi-mortarboard me-2"></i>Datos Académicos y de Prácticas</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Escuela de Procedencia *</b><input type="text" id="np_escuela" placeholder="Ej. UNAM, ITESM" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Horas Requeridas</b><input type="number" id="np_horas" placeholder="480" value="480" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div style="grid-column:span 2;"><b style="font-size:11px;color:#475569;text-transform:uppercase;">Área / Puesto Solicitado *</b><input type="text" id="np_puesto" placeholder="Ej. Enfermería, Laboratorio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Empresa</b><input type="text" id="np_empresa" placeholder="EuroMedica" value="EuroMedica" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Fecha Inicio *</b><input type="date" id="np_fecha_inicio" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
          <div><b style="font-size:11px;color:#475569;text-transform:uppercase;">Fecha Término</b><input type="date" id="np_fecha_termino" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;margin-top:3px;box-sizing:border-box;"></div>
        </div>
      </div>

    </div>

    <!-- Footer con botones -->
    <div style="padding:16px 25px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;background:#f8fafc;">
      <button class="btn-ver" style="background:#6b7280;" onclick="document.getElementById('modalNuevoPracticante').style.display='none'"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
      <button class="btn-ver" style="background:#0f766e;" onclick="guardarNuevoPracticanteDirecto()"><i class="bi bi-floppy-fill me-1"></i> Guardar y Abrir Expediente</button>
    </div>
  </div>
</div> 

<!--modal baja practicante -->
<div id="modalBajaPracticante" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:25px;border-radius:12px;width:400px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <h3>Dar de baja</h3>
        <p id="txtNombreBaja"></p>
        <textarea id="baja_motivo" placeholder="Escribe el motivo de la baja..." style="width:100%;height:80px;margin-bottom:10px;"></textarea>
        <div>
            <input type="checkbox" id="baja_destacado"> <label for="baja_destacado">⭐ Marcar como Destacado</label>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px;">
            <button onclick="document.getElementById('modalBajaPracticante').style.display='none'">Cancelar</button>
            <button onclick="confirmarBajaPracticante()" style="background:#ef4444;color:white;border:none;padding:8px 16px;border-radius:4px;">Confirmar Baja</button>
        </div>
    </div>
</div>


<!-- Modal para registrar un nuevo contrato -->

<div id="modalNuevoContrato" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000; backdrop-filter: blur(4px);">
    <div class="rh-card" style="width: 450px; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="margin-top:0; color:#1e3a8a; border-bottom:2px solid #e5e7eb; padding-bottom:10px;">Registrar Nuevo Contrato</h3>
        
        <div class="empleado-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div style="grid-column: span 2;">
                <b>Empleado</b>
                <input type="text" id="contratoNombre" placeholder="Escribe el nombre completo" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;">
            </div>
            
            <div style="grid-column: span 2;">
                <b>Tipo de Contrato</b>
                <input type="text" id="contratoTipo" placeholder="Ej. Prácticas Profesionales" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;">
            </div>

            <div><b>1er Mes</b><input type="date" id="Mes1" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;"></div>
            <div><b>2do Mes</b><input type="date" id="Mes2" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;"></div>
            <div><b>3er Mes</b><input type="date" id="Mes3" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;"></div>
            <div><b>Indefinido</b><input type="date" id="Indefinido" style="width:100%; padding:8px; border-radius:5px; border:1px solid #d1d5db; margin-top:5px;"></div>
        </div>

        <div style="margin-top:25px; display:flex; gap:10px; justify-content:flex-end;">
            <button onclick="document.getElementById('modalNuevoContrato').style.display='none'" class="btn-ver" style="background:#6b7280; border:none; padding:10px 20px;">Cancelar</button>
            <button onclick="guardarContrato()" class="btn-ver" style="background:#22c55e; border:none; padding:10px 20px;">Guardar</button>
        </div>
    </div>
</div>
<div id="modalBaja" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:25px;border-radius:12px;width:400px;max-width:90%;">
    <h3>Dar de baja al empleado</h3>
    <p>¿Cuál es el motivo de la baja?</p>
    <textarea id="motivoBaja" style="width:100%;height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px;"></textarea>
    <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px;">
      <button class="btn-ver" style="background:#6b7280;" onclick="document.getElementById('modalBaja').style.display='none'">Cancelar</button>
      <button class="btn-ver" style="background:#ef4444;" onclick="confirmarBaja()">Confirmar Baja</button>
    </div>
  </div>
</div>

<!-- Librerías externas: Dynamic Web TWAIN para escaneo y jsPDF para generación de documentos -->
<script src="https://unpkg.com/dwt/dist/dynamsoft.webtwain.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>

// Globales para el objeto de escaneo y control de vacaciones
// let vacacionesAnuales = []; // Inyectado desde PHP

/**
 * Genera registros de días de vacaciones para un rango de años (-2 a +2 del actual)
 * @param {number} id - ID del empleado
 */
function crearAniosEmpleado(id){
    let anioActual = new Date().getFullYear();

    for(let i=-2;i<=2;i++){
        let existe = vacacionesAnuales.find(v =>
            v.empleado_id === id &&
            v.anio === anioActual + i
        );

        if(!existe){
            vacacionesAnuales.push({
                empleado_id:id,
                anio:anioActual + i,
                dias_totales:12 // Días base asignados
            });
        }
    }
}

// Datos de prueba para Empleados
let empleados = @json($empleados);
let vacacionesAnuales = @json($vacacionesAnuales);

// Inicialización de años de vacaciones para cada empleado
empleados.forEach(e=>{
    e.anioSeleccionado = new Date().getFullYear();
    crearAniosEmpleado(e.id);
});

// Variables para manejar la selección actual
let empSel=null;
let practSel=null;
let candSel=null;
let citaSel=null;
let filtroCandidatoTipo="Trabajador";
let tipoCitaFiltro="Agendadas";
let filtroMesCita = "";
let filtroFechaCita = "";

/**
 * Obtiene el objeto actualmente seleccionado (sea empleado o practicante)
 */
function getSeleccionado(){
    if(empSel){
        return { data: empSel, tipo: "empleado" };
    }
    if(practSel){
        return { data: practSel, tipo: "practicante" };
    }
    if(candSel){
        return { data: candSel, tipo: "candidato" };
    }
    if(citaSel){
        return { data: citaSel, tipo: "cita" };
    }
    return null;
}

// Variables globales para filtros de búsqueda
let filtroNombre="";
let filtroEmpresa="";
let filtroEstado="";
let filtroNombreVacaciones="";
let filtroNombreCandidato="";
let timeoutFiltro;

/**
 * Ejecuta el filtrado con un retraso para evitar recargas excesivas al escribir
 */
function filtrarConDelay(vista){
    clearTimeout(timeoutFiltro);
    timeoutFiltro = setTimeout(()=>{
        mostrar(vista);
    }, 300);
}

// Datos inyectados de Vacaciones
let vacaciones = @json($vacaciones);

// Datos inyectados de Practicantes
let practicantes = @json($practicantes);

// Datos inyectados de Candidatos
let candidatos = @json($candidatos);

// Datos inyectados de Citas Agendadas
let citas = @json($citas);

let contratos = @json($contratos ?? []);

// SANITIZACIÓN DEFENSIVA PARA BASES DE DATOS DOBLEMENTE CODIFICADAS
function sanitizeJSON(obj, props) {
    if(!obj) return;
    props.forEach(p => {
        if(typeof obj[p] === 'string') {
            try { obj[p] = JSON.parse(obj[p] || '[]'); } catch(ex) { obj[p] = []; }
        }
    });
}
empleados.forEach(e => sanitizeJSON(e, ['documentos', 'observaciones']));
practicantes.forEach(p => sanitizeJSON(p, ['documentos', 'observaciones']));
candidatos.forEach(c => sanitizeJSON(c, ['documentos', 'observaciones', 'evaluacion_details']));
citas.forEach(ci => sanitizeJSON(ci, ['documentos', 'evaluacion']));

const csrfToken = '{{ csrf_token() }}';
// Solo el administrador RH puede eliminar registros de forma permanente
const esAdminRH = {{ Auth::user()->hasPermission('administracion_rh') ? 'true' : 'false' }};

function syncToServer() {
    fetch('{{ route("rh.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            citas: citas,
            candidatos: candidatos,
            practicantes: practicantes,
            empleados: empleados,
            vacaciones: vacaciones,
            vacacionesAnuales: vacacionesAnuales,
            contratos: contratos
        })
    })
    .catch(console.error);
}

function guardarBD() {
    syncToServer();
}



/**
 * FUNCIONES PARA EVALUACIÓN DE CITAS (FORMULARIO PRACTICANTE)
 */
function seleccionarFormularioEvaluacion(tipo) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.tipo = tipo;

    // Ocultar todos los bloques
    let bloques = ['bloque_form_practicante', 'bloque_form_enfermero', 'bloque_form_medico'];
    bloques.forEach(id => {
        let el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    // Mostrar el seleccionado
    if (tipo === 'Practicante') {
        let el = document.getElementById('bloque_form_practicante');
        if (el) el.style.display = 'block';
    } else if (tipo === 'Enfermero') {
        let el = document.getElementById('bloque_form_enfermero');
        if (el) el.style.display = 'block';
    } else if (tipo === 'Medico') {
        let el = document.getElementById('bloque_form_medico');
        if (el) el.style.display = 'block';
    }
}

function renderizarCamposHijos(num) {
    if (!citaSel) return;
    num = Math.max(1, Math.min(10, num));
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.hijos_num = num;
    if (!citaSel.evaluacion.hijos_lista) citaSel.evaluacion.hijos_lista = [];

    let cont = document.getElementById('contenedor_hijos_lista');
    if (!cont) return;

    let html = '';
    for (let i = 0; i < num; i++) {
        let item = citaSel.evaluacion.hijos_lista[i] || { nombre: '', edad: '' };
        html += `
            <div style="display:flex; gap:10px; align-items:center; background:#fff; padding:8px 12px; border-radius:6px; border:1px solid #cbd5e1;">
                <b style="min-width:65px; color:#1e3a8a; font-size:12px;">Hijo ${i+1}:</b>
                <input type="text" placeholder="Nombre completo" value="${item.nombre || ''}" onchange="actualizarHijoItem(${i}, 'nombre', this.value)" style="flex:2; padding:5px 8px; font-size:12px;">
                <input type="text" placeholder="Edad (ej. 5 años)" value="${item.edad || ''}" onchange="actualizarHijoItem(${i}, 'edad', this.value)" style="flex:1; padding:5px 8px; font-size:12px;">
            </div>
        `;
    }
    cont.innerHTML = html;
}



function actualizarHijoItem(index, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    if (!citaSel.evaluacion.hijos_lista) citaSel.evaluacion.hijos_lista = [];
    if (!citaSel.evaluacion.hijos_lista[index]) citaSel.evaluacion.hijos_lista[index] = { nombre: '', edad: '' };
    citaSel.evaluacion.hijos_lista[index][campo] = valor;
}

function toggleSeguroOp(val) {
    if (!citaSel) return;
    actualizarEvaluacionCampo('seguro_facultativo', val);
    let wrap = document.getElementById('wrapper_seguro_cual');
    if (wrap) wrap.style.display = (val === 'si') ? 'block' : 'none';
}

function actualizarEvaluacionAreaUnica(area) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.area_unica = area;
    citaSel.evaluacion.areas = [area];
}

function actualizarEvaluacionPsicometrica(prueba, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = { tipo: 'Practicante' };
    if (!citaSel.evaluacion.psicometricas) citaSel.evaluacion.psicometricas = {};
    if (!citaSel.evaluacion.psicometricas[prueba]) citaSel.evaluacion.psicometricas[prueba] = {};
    citaSel.evaluacion.psicometricas[prueba][campo] = valor;
}

// ================================================================
// FUNCIONES EXCLUSIVAS DEL FORMULARIO ENFERMERÍA
// ================================================================


// ================================================================
// FUNCIONES EXCLUSIVAS DEL FORMULARIO MÉDICO
// ================================================================

function toggleMedTranspMueven(check) {
    if (!citaSel) return;
    actualizarEvaluacionCheck('med_transp_mueven_chk', check);
    let wrap = document.getElementById('med_wrapper_mueven');
    if (wrap) wrap.style.display = check ? 'block' : 'none';
}

function toggleMedTranspOtro(check) {
    if (!citaSel) return;
    actualizarEvaluacionCheck('med_transp_otro_chk', check);
    let wrap = document.getElementById('med_wrapper_otro');
    if (wrap) wrap.style.display = check ? 'block' : 'none';
}

function toggleMedHijos(val) {
    if (!citaSel) return;
    actualizarEvaluacionCampo('med_tiene_hijos', val);
    let wrap = document.getElementById('med_wrapper_hijos');
    if (wrap) wrap.style.display = (val === 'si') ? 'block' : 'none';
    if (val === 'si') {
        let num = parseInt(citaSel.evaluacion?.med_hijos_num || 1);
        renderizarCamposHijosMed(num);
    }
}

function renderizarCamposHijosMed(num) {
    if (!citaSel) return;
    num = Math.max(1, Math.min(10, num));
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.med_hijos_num = num;
    if (!citaSel.evaluacion.med_hijos_lista) citaSel.evaluacion.med_hijos_lista = [];

    let cont = document.getElementById('med_hijos_lista');
    if (!cont) return;

    let html = '';
    for (let i = 0; i < num; i++) {
        let item = citaSel.evaluacion.med_hijos_lista[i] || { nombre: '', edad: '' };
        html += `
            <div style="display:flex; gap:10px; align-items:center; background:#fff; padding:8px 12px; border-radius:6px; border:1px solid #cbd5e1;">
                <b style="min-width:65px; color:#4338ca; font-size:12px;">Hijo ${i+1}:</b>
                <input type="text" placeholder="Nombre completo" value="${item.nombre || ''}" onchange="actualizarHijoItemMed(${i}, 'nombre', this.value)" style="flex:2; padding:5px 8px; font-size:12px;">
                <input type="text" placeholder="Edad (ej. 5 años)" value="${item.edad || ''}" onchange="actualizarHijoItemMed(${i}, 'edad', this.value)" style="flex:1; padding:5px 8px; font-size:12px;">
            </div>
        `;
    }
    cont.innerHTML = html;
}

function actualizarHijoItemMed(index, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    if (!citaSel.evaluacion.med_hijos_lista) citaSel.evaluacion.med_hijos_lista = [];
    if (!citaSel.evaluacion.med_hijos_lista[index]) citaSel.evaluacion.med_hijos_lista[index] = { nombre: '', edad: '' };
    citaSel.evaluacion.med_hijos_lista[index][campo] = valor;
}

function actualizarEvaluacionPsicometricaMed(prueba, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = { tipo: 'Medico' };
    if (!citaSel.evaluacion.med_psicometricas) citaSel.evaluacion.med_psicometricas = {};
    if (!citaSel.evaluacion.med_psicometricas[prueba]) citaSel.evaluacion.med_psicometricas[prueba] = {};
    citaSel.evaluacion.med_psicometricas[prueba][campo] = valor;
}

function toggleEnfTranspMueven(check) {
    if (!citaSel) return;
    actualizarEvaluacionCheck('enf_transp_mueven_chk', check);
    let wrap = document.getElementById('enf_wrapper_mueven');
    if (wrap) wrap.style.display = check ? 'block' : 'none';
}

function toggleEnfTranspOtro(check) {
    if (!citaSel) return;
    actualizarEvaluacionCheck('enf_transp_otro_chk', check);
    let wrap = document.getElementById('enf_wrapper_otro');
    if (wrap) wrap.style.display = check ? 'block' : 'none';
}

function toggleEnfHijos(val) {
    if (!citaSel) return;
    actualizarEvaluacionCampo('enf_tiene_hijos', val);
    let wrap = document.getElementById('enf_wrapper_hijos');
    if (wrap) wrap.style.display = (val === 'si') ? 'block' : 'none';
    if (val === 'si') {
        let num = parseInt(citaSel.evaluacion?.enf_hijos_num || 1);
        renderizarCamposHijosEnf(num);
    }
}

function renderizarCamposHijosEnf(num) {
    if (!citaSel) return;
    num = Math.max(1, Math.min(10, num));
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.enf_hijos_num = num;
    if (!citaSel.evaluacion.enf_hijos_lista) citaSel.evaluacion.enf_hijos_lista = [];

    let cont = document.getElementById('enf_hijos_lista');
    if (!cont) return;

    let html = '';
    for (let i = 0; i < num; i++) {
        let item = citaSel.evaluacion.enf_hijos_lista[i] || { nombre: '', edad: '' };
        html += `
            <div style="display:flex; gap:10px; align-items:center; background:#fff; padding:8px 12px; border-radius:6px; border:1px solid #cbd5e1;">
                <b style="min-width:65px; color:#0f766e; font-size:12px;">Hijo ${i+1}:</b>
                <input type="text" placeholder="Nombre completo" value="${item.nombre || ''}" onchange="actualizarHijoItemEnf(${i}, 'nombre', this.value)" style="flex:2; padding:5px 8px; font-size:12px;">
                <input type="text" placeholder="Edad (ej. 5 años)" value="${item.edad || ''}" onchange="actualizarHijoItemEnf(${i}, 'edad', this.value)" style="flex:1; padding:5px 8px; font-size:12px;">
            </div>
        `;
    }
    cont.innerHTML = html;
}

function actualizarHijoItemEnf(index, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    if (!citaSel.evaluacion.enf_hijos_lista) citaSel.evaluacion.enf_hijos_lista = [];
    if (!citaSel.evaluacion.enf_hijos_lista[index]) citaSel.evaluacion.enf_hijos_lista[index] = { nombre: '', edad: '' };
    citaSel.evaluacion.enf_hijos_lista[index][campo] = valor;
}

function actualizarEvaluacionPsicometricaEnf(prueba, campo, valor) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = { tipo: 'Enfermero' };
    if (!citaSel.evaluacion.enf_psicometricas) citaSel.evaluacion.enf_psicometricas = {};
    if (!citaSel.evaluacion.enf_psicometricas[prueba]) citaSel.evaluacion.enf_psicometricas[prueba] = {};
    citaSel.evaluacion.enf_psicometricas[prueba][campo] = valor;
}

function imprimirFichaEnfermeriaPDF() {
    alert('Función de impresión de Ficha Técnica de Enfermería en desarrollo.');
}

function seleccionarFormularioEvaluacion(tipo) {
    if (!citaSel) return;
    if (!citaSel.evaluacion) citaSel.evaluacion = {};
    citaSel.evaluacion.tipo = tipo;

    // Ocultar todos los bloques
    let bloques = ['bloque_form_practicante', 'bloque_form_enfermero', 'bloque_form_medico'];
    bloques.forEach(id => {
        let el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    // Mostrar el seleccionado
    if (tipo === 'Practicante') {
        let el = document.getElementById('bloque_form_practicante');
        if (el) el.style.display = 'block';
    } else if (tipo === 'Enfermero') {
        let el = document.getElementById('bloque_form_enfermero');
        if (el) el.style.display = 'block';
    } else if (tipo === 'Medico') {
        let el = document.getElementById('bloque_form_medico');
        if (el) el.style.display = 'block';
    }
}


// NAVEGACIÓN ENTRE CASILLAS CON ENTER (SIN GUARDAR AUTOMÁTICAMENTE)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const target = e.target;
        if (target && (target.tagName === 'INPUT' || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA')) {
            e.preventDefault();
            
            const focusables = Array.from(document.querySelectorAll('#contenido input:not([disabled]):not([type=hidden]), #contenido select:not([disabled]), #contenido textarea:not([disabled])'));
            const currentIndex = focusables.indexOf(target);
            
            if (currentIndex !== -1 && currentIndex + 1 < focusables.length) {
                const nextEl = focusables[currentIndex + 1];
                nextEl.focus();
                if (nextEl.select && nextEl.tagName === 'INPUT') {
                    nextEl.select();
                }
            }
            // Si es la última casilla, NO guarda. Solo se guarda al dar clic en el botón.
        }
    }
});

function imprimirFichaPracticantePDF() {
    if (!citaSel) {
        alert("Selecciona una cita primero.");
        return;
    }
    let ev = citaSel.evaluacion || {};
    
    // Función auxiliar para que lo no llenado se muestre como N/A
    function val(v) {
        if (v === null || v === undefined || v === "") return "N/A";
        let str = v.toString().trim();
        return str === "" ? "N/A" : str;
    }

    let printWin = window.open('', '_blank');
    
    // Construcción de string de hijos si los tiene
    let hijosTxt = "N/A";
    if (ev.tiene_hijos === 'si') {
        if (ev.hijos_lista && ev.hijos_lista.length > 0) {
            hijosTxt = ev.hijos_lista.map((h, i) => `Hijo ${i+1}: ${val(h.nombre)} (${val(h.edad)})`).join('; ');
        } else {
            hijosTxt = `Sí (${val(ev.hijos_num)} hijo/s)`;
        }
    } else if (ev.tiene_hijos === 'no') {
        hijosTxt = "No tiene hijos";
    }

    let html = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Ficha Técnica de Evaluación - ${citaSel.nombre || 'Practicante'}</title>
        <style>
            @page { size: letter; margin: 12mm; }
            body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #0f172a; margin: 0; padding: 10px; line-height: 1.4; }
            h1 { text-align: center; font-size: 17px; margin: 0; color: #1e3a8a; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; }
            .subtitle { text-align: center; font-size: 12px; font-style: italic; color: #475569; margin-bottom: 12px; font-weight: 600; }
            .section-title { font-size: 11px; font-weight: 800; color: #1e3a8a; margin-top: 12px; margin-bottom: 4px; text-transform: uppercase; border-bottom: 1.5px solid #1e3a8a; padding-bottom: 2px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11px; }
            th, td { border: 1px solid #94a3b8; padding: 5px 8px; text-align: left; vertical-align: top; }
            th { background-color: #f1f5f9; font-weight: 700; color: #1e3a8a; }
            .box { border: 1px solid #94a3b8; padding: 8px; min-height: 45px; margin-bottom: 8px; font-size: 11px; }
            .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; margin-top: 4px; }
            .page-break { page-break-before: always; }
        </style>
    </head>
    <body>
        <h1>FICHA TÉCNICA DE EVALUACIÓN</h1>
        <div class="subtitle">Área de Practicantes</div>

        <div class="section-title">I. DATOS DE CONTROL Y ENTREVISTA</div>
        <table>
            <tr>
                <td><b>Candidato para:</b> ${val(ev.candidato_para || citaSel.puesto)}</td>
                <td><b>Entrevista #:</b> ${val(ev.entrevista_num || '1')}</td>
                <td><b>Por:</b> ${val(ev.entrevista_por || citaSel.entrevistador_rh)}</td>
            </tr>
            <tr>
                <td><b>Fecha:</b> ${val(ev.fecha || citaSel.fecha)}</td>
                <td><b>Disponibilidad:</b> ${val(ev.disponibilidad)}</td>
                <td><b>Horario:</b> ${val(ev.horario)}</td>
            </tr>
        </table>

        <div class="section-title">II. DATOS PERSONALES Y FAMILIARES</div>
        <table>
            <tr>
                <td style="width:50%;"><b>Edad:</b> ${val(ev.edad)}</td>
                <td style="width:50%;"><b>A qué se dedica papá:</b> ${val(ev.papa_dedica)}</td>
            </tr>
            <tr>
                <td><b>Vive en:</b> ${val(ev.vive_en)}</td>
                <td><b>A qué se dedica mamá:</b> ${val(ev.mama_dedica)}</td>
            </tr>
            <tr>
                <td><b>Vive con:</b> ${val(ev.vive_con)}</td>
                <td><b>Hermanos (A qué se dedican):</b> ${val(ev.hermanos_dedican)}</td>
            </tr>
            <tr>
                <td><b>Estado Civil:</b> ${val(ev.estado_civil)}</td>
                <td>
                    <b>Medio de transporte:</b><br>
                    [${ev.transp_auto ? 'X' : ' '}] Auto propio &nbsp;
                    [${ev.transp_uber ? 'X' : ' '}] Uber/Didi/Taxi &nbsp;
                    [${ev.transp_publico ? 'X' : ' '}] Transp. Público<br>
                    [${ev.transp_check_mueven ? 'X' : ' '}] Lo mueven: ${val(ev.transp_lo_mueven)} &nbsp;
                    [${ev.transp_check_otro ? 'X' : ' '}] Otro: ${val(ev.transp_otro)}
                </td>
            </tr>
            <tr>
                <td><b>Hijos:</b> ${hijosTxt}</td>
                <td><b>Tiempo para llegar:</b> ${val(ev.tiempo_llegar)}</td>
            </tr>
        </table>

        <div class="section-title">III. PERFIL PROFESIONAL Y ESPECÍFICO</div>
        <table>
            <tr>
                <td><b>Universidad:</b> ${val(ev.universidad)}</td>
                <td><b>Carrera:</b> ${val(ev.carrera)}</td>
            </tr>
            <tr>
                <td><b>Horas requeridas:</b> ${val(ev.horas_requeridas)}</td>
                <td><b>Días disponibles:</b> ${val(ev.dias_disponibles)}</td>
            </tr>
            <tr>
                <td><b>Horario tentativo:</b> ${val(ev.horario_tentativo)}</td>
                <td><b>Horas por semana:</b> ${val(ev.horas_por_semana)}</td>
            </tr>
            <tr>
                <td colspan="2"><b>Seguro Facultativo:</b> ${ev.seguro_facultativo==='si' ? `Sí (Cuál: ${val(ev.seguro_cual)})` : 'No'}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Área de Interés Única:</b> ${val(ev.area_unica || (ev.areas || [])[0])}
                </td>
            </tr>
        </table>

        <div class="page-break"></div>

        <table>
            <tr>
                <td colspan="2">
                    <b>Servicio social:</b><br>
                    <b>Lugar:</b> ${val(ev.ss_lugar)} &nbsp; <b>Fecha: De</b> ${val(ev.ss_fecha_de)} <b>A</b> ${val(ev.ss_fecha_a)}<br>
                    <b>Actividades:</b> ${val(ev.ss_actividades)}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Prácticas Profesionales:</b><br>
                    <b>Lugar:</b> ${val(ev.pp_lugar)} &nbsp; <b>Fecha: De</b> ${val(ev.pp_fecha_de)} <b>A</b> ${val(ev.pp_fecha_a)}<br>
                    <b>Actividades:</b> ${val(ev.pp_actividades)}
                </td>
            </tr>
        </table>

        <div class="section-title">IV. EXPERIENCIAS LABORALES:</div>
        <div class="box">${val(ev.exp_laboral)}</div>

        <div class="section-title">V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS</div>
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">Prueba</th>
                    <th style="width:20%;">Tiempo</th>
                    <th style="width:65%;">Observaciones amplias</th>
                </tr>
            </thead>
            <tbody>
                ${ ['DFH', 'PBL', 'Familia', 'Árbol', 'Casa'].map(p => {
                    let key = p.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    let res = ev.psicometricas?.[key] || {};
                    return `
                        <tr>
                            <td><b>${p}</b></td>
                            <td>${val(res.tiempo)}</td>
                            <td>${val(res.obs)}</td>
                        </tr>
                    `;
                }).join('') }
            </tbody>
        </table>
    </body>
    </html>
    `;

    printWin.document.write(html);
    printWin.document.close();
    setTimeout(() => {
        printWin.focus();
        printWin.print();
    }, 500);
}

/**
 * Funciones para cambiar el estado de solicitudes de vacaciones
 */
function aprobarVacacion(index){
if(confirm("¿Aprobar solicitud?")){
vacaciones[index].estado="Aprobadas";
mostrar("ficha");
}
}

function rechazarVacacion(index){
if(confirm("¿Rechazar solicitud?")){
vacaciones[index].estado="Rechazadas";
mostrar("ficha");
}
}

/**
 * Guarda una nueva observación en el perfil del empleado seleccionado
 */
function guardarObservacion(){
if(!empSel)return;

let txt=document.getElementById("txtObservacion").value.trim();
if(txt==""){
alert("Escribe una observación");
return;
}

// Agregar al inicio del array de observaciones
empSel.observaciones.unshift({
fecha:new Date().toLocaleString(),
texto:txt
});

document.getElementById("txtObservacion").value="";

// Actualización reactiva del DOM para la lista de observaciones
let lista=document.querySelector(".obs-list");
if(lista){
let html="";
html += `<div id="noObs" style="display:none;">Sin observaciones registradas.</div>`;
html += empSel.observaciones.map(o=>`
<div class="obs-item">
<div class="obs-fecha">${o.fecha}</div>
<div>${o.texto}</div>
</div>
`).join('');
lista.innerHTML = html;
}
}

/**
 * Inicia el proceso de escaneo de documentos usando Dynamsoft Web TWAIN
 */
function escanear(){
let sel = getSeleccionado();
if(!sel){
    alert("Selecciona un registro primero");
    return;
}

// Configuración de la licencia y recursos de Dynamsoft
//Poner la API ProductKey de Dynasoft propia
//Dynamsoft.DWT.ProductKey=";
Dynamsoft.DWT.ResourcesPath="https://unpkg.com/dwt/dist/";
Dynamsoft.DWT.Containers=[{
WebTwainId:'dwtcontrolContainer',
Width:'100%',
Height:'300px'
}];

Dynamsoft.DWT.RegisterEvent('OnWebTwainReady',function(){
DWObject=Dynamsoft.DWT.GetWebTwain('dwtcontrolContainer');

// Lógica para seleccionar fuente y adquirir imagen
DWObject.SelectSource(function(){
DWObject.AcquireImage({
IfShowUI:true,
PixelType:2,
Resolution:200
},function(){
let index=DWObject.CurrentImageIndexInBuffer;

// Conversión de la imagen escaneada a Base64 para guardarla en el objeto del empleado
DWObject.ConvertToBase64([index],
Dynamsoft.DWT.EnumDWT_ImageType.IT_PNG,
function(res){
let img="data:image/png;base64,"+
res.getData(0,res.getLength());

sel.data.documentos.push({
    url: img,
    tipo: "imagen",
    owner_tipo: sel.tipo,
    owner_id: sel.data.id
});

// Recargar la ficha correspondiente
if(sel.tipo === "empleado"){
    mostrar("ficha");
}else{
    mostrar("ficha_practicante");
}
});
});
});
});

Dynamsoft.DWT.Load();
}

/**
 * PERSISTENCIA DE NAVEGACIÓN Y ESTADO AL RECARGAR (F5 / Ctrl+F5)
 */
function guardarEstadoNavegacionRH(v) {
    try {
        let estadoRH = {
            vista: v,
            empSelId: empSel ? empSel.id : null,
            practSelId: practSel ? practSel.id : null,
            candSelId: candSel ? candSel.id : null,
            citaSelId: citaSel ? citaSel.id : null,
            citaModoActual: citaSel ? (citaSel.modo_actual || 'editar') : 'editar',
            tipoCitaFiltro: typeof tipoCitaFiltro !== 'undefined' ? tipoCitaFiltro : 'Agendadas',
            vistaPract: typeof vistaPract !== 'undefined' ? vistaPract : 'activos',
            filtroCandidatoTipo: typeof filtroCandidatoTipo !== 'undefined' ? filtroCandidatoTipo : 'Trabajador'
        };
        localStorage.setItem('rh_estado_navegacion', JSON.stringify(estadoRH));
    } catch(err) {
        console.error("Error guardando estado RH:", err);
    }
}

function restaurarEstadoNavegacionRH() {
    try {
        let raw = localStorage.getItem('rh_estado_navegacion');
        if (!raw) return false;
        let estadoRH = JSON.parse(raw);
        if (!estadoRH || !estadoRH.vista) return false;

        if (estadoRH.empSelId && typeof empleados !== 'undefined') {
            empSel = empleados.find(e => e.id == estadoRH.empSelId) || null;
        }
        if (estadoRH.practSelId && typeof practicantes !== 'undefined') {
            practSel = practicantes.find(p => p.id == estadoRH.practSelId) || null;
        }
        if (estadoRH.candSelId && typeof candidatos !== 'undefined') {
            candSel = candidatos.find(c => c.id == estadoRH.candSelId) || null;
        }
        if (estadoRH.citaSelId && typeof citas !== 'undefined') {
            citaSel = citas.find(ci => ci.id == estadoRH.citaSelId) || null;
            if (citaSel && estadoRH.citaModoActual) {
                citaSel.modo_actual = estadoRH.citaModoActual;
            }
        }
        if (estadoRH.tipoCitaFiltro) tipoCitaFiltro = estadoRH.tipoCitaFiltro;
        if (estadoRH.vistaPract) vistaPract = estadoRH.vistaPract;
        if (estadoRH.filtroCandidatoTipo) filtroCandidatoTipo = estadoRH.filtroCandidatoTipo;

        mostrar(estadoRH.vista);
        return true;
    } catch(err) {
        console.error("Error restaurando estado RH:", err);
        return false;
    }
}

/**
 * Formatea fechas ISO a formato legible en español mexicano
 */
function formatearFecha(fecha){
    if(!fecha) return "";
    let f = new Date(fecha);
    return f.toLocaleDateString("es-MX", {
        day: "numeric",
        month: "long",
        year: "numeric"
    });
}

/**
 * Función principal de ruteo y renderizado de la interfaz
 * @param {string} v - Nombre de la vista a mostrar
 */
function mostrar(v){
try {
guardarEstadoNavegacionRH(v);

// Manejo de clases activas en navegación
document.querySelectorAll(".rh-nav").forEach(n=>{
n.classList.remove("active");
});

if(v==="citas" || v==="ficha_cita"){
document.querySelectorAll(".rh-nav")[0]?.classList.add("active");
}
if(v==="candidatos" || v==="ficha_candidato"){
document.querySelectorAll(".rh-nav")[1]?.classList.add("active");
}
if(v==="practicantes" || v==="ficha_practicante"){
document.querySelectorAll(".rh-nav")[2]?.classList.add("active");
setTimeout(() => {
        if (typeof renderizarDocumentos === 'function') {
            renderizarDocumentos();
        }
    }, 50);
}
if(v==="empleados" || v==="ficha"){
document.querySelectorAll(".rh-nav")[3]?.classList.add("active");
}
if(v==="vacaciones"){
document.querySelectorAll(".rh-nav")[4]?.classList.add("active");
}
if(v==="contratos"){
document.querySelectorAll(".rh-nav")[5]?.classList.add("active");
}

let html="";
let contenido=document.getElementById("contenido");

// VISTA: AGENDAR CITAS
if(v==="citas"){
let hoy = new Date().toISOString().split('T')[0];

let filtradas = citas.filter(c=>{

    if(tipoCitaFiltro==="Historial"){

        // Filtro por mes (compara si la fecha empieza con YYYY-MM)
        let coincideMes = (filtroMesCita === "" || c.fecha.startsWith(filtroMesCita));
        
        // Filtro por fecha exacta (comparación directa YYYY-MM-DD)
       // ASÍ DEBE QUEDAR PARA EVITAR EL DESFASE DE UN DÍA
        let coincideFecha = filtroFechaCita === "" || c.fecha === filtroFechaCita;

        // Retornamos si cumple los estados de historial Y los filtros seleccionados
        return (c.estado === "Realizada" || c.estado === "No se presentó" || c.estado === "Cancelada") 
               && coincideMes 
               && coincideFecha;

        // AQUÍ ESTÁ LA MAGIA: Agregamos "No se presentó" y "Cancelada" al historial
        return (
            (c.estado==="Realizada" || c.estado==="No se presentó" || c.estado==="Cancelada" || c.fecha < hoy)
            &&
            coincideMes
            &&
            coincideFecha
        );
    }

    if(tipoCitaFiltro==="Realizadas"){
        return c.estado==="Realizada";
    }

    // PARA AGENDADAS: Excluimos las que ya se procesaron
    return c.estado!=="Realizada" && c.estado!=="No se presentó" && c.estado!=="Cancelada";
});
html=`
<div class="tabs">

<div class="tab ${tipoCitaFiltro==='Agendadas'?'active':''}"
onclick="tipoCitaFiltro='Agendadas'; mostrar('citas')">
Citas Agendadas
</div>

<div class="tab ${tipoCitaFiltro==='Realizadas'?'active':''}"
onclick="tipoCitaFiltro='Realizadas'; mostrar('citas')">
Citas Realizadas
</div>

<div class="tab ${tipoCitaFiltro==='Historial'?'active':''}"
onclick="tipoCitaFiltro='Historial'; mostrar('citas')">
Historial
</div>
</div>

<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    ${tipoCitaFiltro}
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevaCita()">+ Nueva Cita</button>
</h2>

${tipoCitaFiltro === 'Historial' ? `
<div style="display:flex;gap:15px;margin:15px 0;flex-wrap:wrap; background:#f4f6f9; padding:15px; border-radius:8px;">
    <div>
        <b>Mes</b><br>
        <input type="month" value="${filtroMesCita}" onchange="filtroMesCita=this.value;mostrar('citas')">
    </div>
    <div>
        <b>Fecha exacta</b><br>
        <input type="date" value="${filtroFechaCita}" onchange="filtroFechaCita=this.value;mostrar('citas')">
    </div>
    <div>
        <br>
        <button class="btn-ver" style="background:#6b7280; padding:6px 12px;" 
        onclick="filtroMesCita=''; filtroFechaCita=''; mostrar('citas');">Limpiar</button>
    </div>
</div>
` : ''}
<table class="rh-table">
<thead>
<tr>
<th>Nombre</th>
<th>Puesto</th>
<th>Tipo</th>
<th>Fecha Cita</th>
<th>Hora</th>
<th>Entrevistador RH</th>
<th>Jefe Depto.</th>
<th>Estado</th>
</tr>
</thead>
<tbody>
${filtradas
.sort((a,b)=>
new Date(a.fecha+" "+(a.hora||"00:00"))
-
new Date(b.fecha+" "+(b.hora||"00:00"))
)
.map((ci,idx)=>{
    // Calcular color de urgencia SOLO para citas agendadas
    let rowStyle = 'cursor:pointer;';
    if(tipoCitaFiltro === 'Agendadas') {
        let hoy = new Date();
        hoy.setHours(0,0,0,0);
        let fechaCita = new Date(ci.fecha + 'T' + (ci.hora || '00:00'));
        let diffMs = fechaCita - hoy;
        let diffDias = diffMs / (1000 * 60 * 60 * 24);
        if(diffDias <= 1) {
            rowStyle = 'cursor:pointer; background:#fef2f2; border-left:4px solid #dc2626;';
        } else if(diffDias <= 2) {
            rowStyle = 'cursor:pointer; background:#fefce8; border-left:4px solid #ca8a04;';
        } else {
            rowStyle = 'cursor:pointer; background:#f0fdf4; border-left:4px solid #16a34a;';
        }
    }
    return `
<tr style="${rowStyle}" onclick="seleccionarCita('${ci.id}')">
<td>${ci.nombre}</td>
<td>${ci.puesto}</td>
<td>${ci.tipo}</td>
<td>${formatearFecha(ci.fecha)}</td>
<td>${ci.hora}</td>
<td>${ci.entrevistador_rh}</td>
<td>${ci.jefe_depto||'N/A'}</td>
<td><span style="font-weight:bold;color:${ci.estado==='Realizada'?'green':ci.estado==='Cancelada'?'red':'#ca8a04'}">${ci.estado}</span></td>
</tr>
`;
}).join('')}
</tbody>
</table>
${filtradas.length===0 ? '<div style="text-align:center;padding:20px;color:#6b7280;">No hay registros en esta categoría.</div>' : ''}
</div>`;
}

// VISTA: FICHA DE CITA
if(v==="ficha_cita" && !citaSel){
    html = `
    <div class="tabs">
    <div class="tab" onclick="mostrar('citas')">Agendar Citas</div>
    <div class="tab active" onclick="mostrar('ficha_cita')">Detalle de Cita</div>
    </div>
    <div class="rh-card" style="text-align:center;padding:40px;">
        <h2>Ninguna cita seleccionada</h2>
    </div>
    `;
}

if(v==="ficha_cita" && citaSel){
let ci = citaSel;
html=`@include('administracion.recursos_humanos.agendar_cita_detalle')`;
}



// VISTA: LISTADO DE EMPLEADOS
if(v==="empleados"){
let filtrados = empleados.filter(e => {
    // 1. Si tienen fecha de egreso, los excluimos inmediatamente
    if (e.egreso) return false;

    // 2. Si no tienen fecha de egreso, aplicamos los filtros normales
    return (
        ((e.nombre || '').toLowerCase().includes(filtroNombre.toLowerCase())) &&
        ((e.empresa || '').toLowerCase().includes(filtroEmpresa.toLowerCase()))
    );
});

html=`
<div class="tabs">
<div class="tab active" onclick="mostrar('empleados')">Empleados</div>
<div class="tab" onclick="mostrar('ficha')">Ficha Detalle</div>
</div>
<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    Empleados
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevoEmpleado()">+ Nuevo Empleado</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Nombre<br><input value="${filtroNombre}" oninput="filtroNombre=this.value;filtrarConDelay('empleados')" style="width:90%"></th>
<th>Apellido Paterno</th>
<th>Apellido Materno</th>
<th>Empresa<br><input value="${filtroEmpresa}" oninput="filtroEmpresa=this.value;filtrarConDelay('empleados')" style="width:90%"></th>
<th>Estado<br>
<select onchange="filtroEstado=this.value;mostrar('empleados')" style="width:95%">
<option value="">Todos</option>
<option value="Activo" ${filtroEstado==="Activo"?"selected":""}>Activo</option>
<option value="Inactivo" ${filtroEstado==="Inactivo"?"selected":""}>Inactivo</option>
</select>
</th>
<th>Fecha Ingreso</th>
<th>Fecha Egreso</th>
</tr>
</thead>
<tbody>
${filtrados.map(e=>{
let estado = e.egreso ? "Inactivo" : "Activo";
return `
<tr style="cursor:pointer;" onclick="seleccionar('${e.id}')">
<td>${e.nombre}</td>
<td>${e.ap}</td>
<td>${e.am}</td>
<td>${e.empresa}</td>
<td>${estado}</td>
<td>${e.fecha || ''}</td>
<td>${e.egreso || '-'}</td>
</tr>
`;
}).join('')}
</tbody>
</table>
</div>`;
}

// VISTA: LISTADO DE PRACTICANTES
if(v==="practicantes"){
    // Filtramos para separar Activos e Historial
    let activos = (practicantes || []).filter(p => !p.egreso);
    let historial = (practicantes || []).filter(p => p.egreso);
    
    // Si no definimos pestaña, por defecto 'activos'
    if(typeof vistaPract === 'undefined') vistaPract = 'activos';

    html = `
    <div class="tabs">
        <div class="tab ${vistaPract==='activos'?'active':''}" onclick="vistaPract='activos';mostrar('practicantes')">Activos</div>
        <div class="tab ${vistaPract==='historial'?'active':''}" onclick="vistaPract='historial';mostrar('practicantes')">Historial</div>
        <div class="tab" onclick="mostrar('ficha_practicante')">Ficha Detalle</div>
    </div>
    <div class="rh-card">
        <h2>${vistaPract === 'activos' ? 'Practicantes Activos' : 'Historial de Bajas'}</h2>
        <table class="rh-table">
            <thead>
                <tr>
                    <th>Nombre</th> 
                     <th>Puesto</th>
                    <th>Escuela de Procedencia</th>
                    <th>Horas (Acum. / Req.)</th>
                    <th>${vistaPract === 'activos' ? 'Ingreso' : 'Egreso'}</th>
                    <th>Acciones</th>
                  
                </tr>
            </thead>
            <tbody>
                ${(vistaPract === 'activos' ? activos : historial).map(p => `
                <tr style="cursor:pointer;" onclick="seleccionarPract('${p.id}'); mostrar('ficha_practicante');">
                    <td>${p.destacado ? '⭐ ' : ''}${p.nombre || ''} ${p.ap || ''}</td>
                    <td>${p.puesto_solicitado || p.puesto || 'N/A'}</td>
                    <td>${p.escuela_procedencia || 'N/A'}</td>
                    <td>
                        <span style="font-weight:bold; color:${(p.horas_llevadas || 0) >= (p.horas_requeridas || 480) ? '#22c55e' : '#3b82f6'}">
                            ${p.horas_llevadas || 0}
                        </span> / ${p.horas_requeridas || 480}
                    </td>
                    <td>${vistaPract === 'activos' ? (p.fecha_inicio || '-') : (p.egreso || '-')}</td>
                    <td>
                        <button onclick="event.stopPropagation(); seleccionarPract(${p.id}); mostrar('ficha_practicante');">Ver Ficha</button>
                    </td>
                </tr>
                `).join('')}
            </tbody>
        </table>
    </div>`;
    
    document.getElementById("contenido").innerHTML = html;
}

// VISTA: LISTADO DE CANDIDATOS
if(v==="candidatos"){
let filtrados = candidatos.filter(c => {
    return c.tipo_candidatura === filtroCandidatoTipo && 
           (c.nombre || '').toLowerCase().includes(filtroNombreCandidato.toLowerCase());
});

html = `
<div class="tabs">
<div class="tab ${filtroCandidatoTipo==='Trabajador' ? 'active' : ''}" onclick="filtroCandidatoTipo='Trabajador';mostrar('candidatos')">Para Trabajadores</div>
<div class="tab ${filtroCandidatoTipo==='Practicante' ? 'active' : ''}" onclick="filtroCandidatoTipo='Practicante';mostrar('candidatos')">Para Practicantes</div>
<div class="tab" onclick="mostrar('ficha_candidato')">Ficha Detalle</div>
</div>

<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    Candidatos a ${filtroCandidatoTipo}
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Nombre <br><input value="${filtroNombreCandidato}" oninput="filtroNombreCandidato=this.value;filtrarConDelay('candidatos')" style="width:90%"></th>
<th>Puesto Deseado</th>
<th>Nivel Educativo</th>
<th>Fecha Postulación</th>
<th>Estatus</th>
<th>Calificación</th>
</tr>
</thead>
<tbody>
${filtrados.map(c => `
<tr style="cursor:pointer;" onclick="seleccionarCandidato('${c.id}')">
<td>${c.nombre} ${c.ap} ${c.am}</td>
<td>${c.puesto_deseado}</td>
<td>${c.nivel_educativo}</td>
<td>${formatearFecha(c.fecha_postulacion)}</td>
<td><span style="font-weight:bold;color:${c.estatus_reclutamiento==='Contratado'?'green':c.estatus_reclutamiento==='Rechazado'?'red':'#ca8a04'}">${c.estatus_reclutamiento}</span></td>
<td>${"⭐".repeat(c.calificacion)}${"☆".repeat(5-c.calificacion)}</td>
</tr>
`).join('')}
</tbody>
</table>
</div>`;
}

// VALIDACIÓN DE SELECCIÓN PARA FICHA
if(v==="ficha" && !empSel){
html = `
<div class="tabs">
<div class="tab" onclick="mostrar('empleados')">Empleados</div>
<div class="tab active" onclick="mostrar('ficha')">Ficha Detalle</div>
</div>
<div class="rh-card" style="text-align:center;padding:40px;">
    <h2>Ningún empleado seleccionado</h2>
    <p>Por favor selecciona un empleado desde la pestaña de <b>Empleados</b>.</p>
</div>
`;
}

// VISTA: FICHA DETALLADA DEL EMPLEADO
if(v==="ficha" && empSel){
let e=empSel;
let aniosEmpleado = vacacionesAnuales.filter(v=>v.empleado_id===e.id).map(v=>v.anio);
let vacEmp = vacaciones.map((v,index)=>({...v,index})).filter(v=> 
    v.empleado_id===e.id && new Date(v.inicio).getFullYear() === e.anioSeleccionado
);

let registro = vacacionesAnuales.find(v=> v.empleado_id===e.id && v.anio===e.anioSeleccionado);
let diasTotales = registro ? registro.dias_totales : 0;
let usados = vacEmp.reduce((a,v)=>a+v.dias,0);
let disponibles = diasTotales - usados;

html=`@include('administracion.recursos_humanos.empleados_detalle')`;
}

// VALIDACIÓN DE SELECCIÓN PARA FICHA PRACTICANTE
if(v==="ficha_practicante" && !practSel){
    html = `
    <div class="tabs">
    <div class="tab" onclick="mostrar('practicantes')">Practicantes</div>
    <div class="tab active" onclick="mostrar('ficha_practicante')">Ficha Detalle</div>
    </div>
    <div class="rh-card" style="text-align:center;padding:40px;">
        <h2>Ningún practicante seleccionado</h2>
        <p>Por favor selecciona un practicante desde la tabla correspondiente.</p>
    </div>
    `;
}

// VISTA: FICHA DE PRACTICANTE
if(v==="ficha_practicante" && practSel){
let p = practSel;
html = `@include('administracion.recursos_humanos.practicantes_detalle')`;
document.getElementById("contenido").innerHTML = html;
renderizarDocumentos();
}

// VALIDACIÓN DE SELECCIÓN PARA FICHA CANDIDATO
if(v==="ficha_candidato" && !candSel){
    html = `
    <div class="tabs">
    <div class="tab" onclick="mostrar('candidatos')">Candidatos</div>
    <div class="tab active" onclick="mostrar('ficha_candidato')">Ficha Detalle</div>
    </div>
    <div class="rh-card" style="text-align:center;padding:40px;">
        <h2>Ningún candidato seleccionado</h2>
        <p>Por favor selecciona un candidato desde la tabla correspondiente.</p>
    </div>
    `;
}

// VISTA: FICHA DE CANDIDATO
if(v==="ficha_candidato" && candSel){
let c = candSel;
html = `@include('administracion.recursos_humanos.candidatos_detalle')`;
}

// VISTA: GESTIÓN GENERAL DE VACACIONES
if(v==="vacaciones"){
html=`
<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
  Gestión de Vacaciones
  <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="mostrarModalVacaciones()">+ Solicitar</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Empleado<br><input value="${filtroNombreVacaciones}" oninput="filtroNombreVacaciones=this.value;filtrarConDelay('vacaciones')" style="width:90%;margin-top:5px;"></th>
<th>Inicio contrato</th>
<th>Inicio vacaciones</th>
<th>Fin vacaciones</th>
<th>Días</th>
<th>Tipo</th>
<th>Estado</th>
<th>Cobertura</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
${vacaciones.filter(v=>{
    let emp = empleados.find(e=>e.id==v.empleado_id);
    let nombreCompleto = emp ? (emp.nombre + " " + emp.ap + " " + emp.am).toLowerCase() : "";
    return nombreCompleto.includes(filtroNombreVacaciones.toLowerCase());
}).map(v=>{
let emp = empleados.find(e=>e.id==v.empleado_id);
return `
<tr>
<td>${emp ? emp.nombre : 'N/A'}</td>
<td>${emp ? formatearFecha(emp.fecha) : ''}</td>
<td>${formatearFecha(v.inicio)}</td>
<td>${formatearFecha(v.fin)}</td>
<td>${v.dias}</td>
<td>${v.tipo}</td>
<td><span style="padding:4px 8px;border-radius:6px;background:${v.estado==="Aprobadas"?"#22c55e":v.estado==="Pendiente"?"#facc15":"#ef4444"};color:white;font-size:12px;">${v.estado}</span></td>
<td>${v.cobertura}</td>
<td style="text-align:center;"><button style="background:#ef4444; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;" onclick="eliminarVacacionGlobal(${vacaciones.indexOf(v)})">Eliminar</button></td>
</tr>`;
}).join('')}
</tbody>
</table>
</div>`;
}

// ... dentro de tu función mostrar(v) ...

if(v === "contratos") {
    html = `
    <div class="rh-card">
        <h2>Gestión de Contratos</h2>
        <div style="margin-bottom: 20px;">
         <button onclick="document.getElementById('modalNuevoContrato').style.display = 'flex'" class="btn-ver">
    + Nuevo Contrato
</button>
        </div>
        <table class="rh-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>1er mes</th>
                    <th>2do mes</th>
                    <th>3er mes</th>
                    <th>Indefinido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="listaContratos">
                </tbody>
        </table>
    </div>`;
    document.getElementById("contenido").innerHTML = html;
}

contenido.innerHTML=html;
} catch (err) {
    alert("¡Ups! Error en la plataforma: " + err.message + "\nLínea aproximada en consola.");
    console.error("Crash report:", err);
}
}

// ... dentro de tu función mostrar(v) ...

/**
 * Selecciona un empleado y cambia a su ficha
 */
function seleccionar(id){
console.log("Seleccionando emp con ID:", id, typeof id);
empSel=empleados.find(e=>e.id==id);
if(!empSel) { alert("ERROR CRÍTICO: No se encontró empleado con ID: " + id); }
practSel=null;
candSel=null;
mostrar("ficha");
}

/**
 * Cambia el año de consulta de vacaciones para el empleado actual
 */
function cambiarAnio(anio){
    if(empSel){
        empSel.anioSeleccionado = parseInt(anio);
        mostrar("ficha");
    }
}

/**
 * Selecciona un practicante y cambia a su ficha
 */
function seleccionarPract(id){
practSel = practicantes.find(p => p.id == id);
empSel = null;
candSel = null;
mostrar("ficha_practicante");
}

function seleccionarCandidato(id){
console.log("Seleccionando candidato con ID:", id, typeof id);
candSel=candidatos.find(c=>c.id==id);
if(!candSel) { alert("ERROR CRÍTICO: No se encontró candidato con ID: " + id); }
empSel=null;
practSel=null;
mostrar("ficha_candidato");
if (typeof renderizarDocumentos === 'function') {
        setTimeout(renderizarDocumentos, 100); 
    }
}

function guardarObservacionCand(){
if(!candSel)return;
let txt=document.getElementById("txtObservacion").value.trim();
if(txt==""){
alert("Escribe una observación");
return;
}
candSel.observaciones.unshift({
fecha:new Date().toLocaleString(),
texto:txt
});
document.getElementById("txtObservacion").value="";
mostrar("ficha_candidato");
}

function subirArchivoCandidato(input){
if(input.files && input.files[0]){
    let reader = new FileReader();
    let file = input.files[0];
    let isImage = file.type.startsWith("image/");
    reader.onload = function(e){
        if(e.target.result.length > 2_000_000){
            alert("Este archivo es demasiado grande para guardarse localmente. Intenta con un archivo más pequeño.");
            return;
        }
        candSel.documentos.push({
            url: e.target.result,
            tipo: isImage ? 'imagen' : 'pdf',
            nombre: file.name,
            owner_tipo: "candidato",
            owner_id: candSel.id
        });
        guardarBD();
        mostrar("ficha_candidato");
    };
    reader.readAsDataURL(file);
}
}

function descargarURL(url){
    let a = document.createElement("a");
    a.href = url;
    a.download = "Documento";
    a.click();
}

function convertirCandidato(){
    if(!candSel) return;
    if(!confirm("¿Deseas convertir este candidato a " + candSel.tipo_candidatura +"?")) return;

    candSel.estatus_reclutamiento = "Contratado";
    let nId = Date.now(); 
    let fNac = candSel.fecha_postulacion;

    if(candSel.tipo_candidatura === "Trabajador"){
        let fechaConv = new Date().toISOString().split('T')[0];
        empleados.push({
            id: nId,
            nombre: candSel.nombre, ap: candSel.ap, am: candSel.am,
            empresa: "",
            nss: "", rfc: "", curp: "", sexo: "Indefinido",
            celular: candSel.celular, correo: candSel.correo, direccion: "",
            estado_civil: "Soltero", nacimiento: "",
            fecha_conversion: fechaConv, fecha: fechaConv,
            alta_imss: "", egreso: "", motivo: "", puesto: candSel.puesto_deseado,
            contacto_emergencia: "", parentesco: "", tel_emergencia1: "", tel_emergencia2: "",
            talla_uniforme:"M", tipo_sangre:"", alergias:"", canal_captacion: candSel.canal_captacion||'', clabe_bancaria:"",
            documentos: candSel.documentos, observaciones: candSel.observaciones
        });
        guardarBD();
        alert("Candidato convertido a Empleado exitosamente.");
        candSel = null; 
        mostrar("empleados");
    } else {
        let fechaConv = new Date().toISOString().split('T')[0];
        practicantes.push({
            id: nId,
            nombre: candSel.nombre, ap: candSel.ap, am: candSel.am,
            empresa: " ",
            fecha_inicio: fechaConv, fecha_termino: "",
            
            // AQUÍ SALVAMOS LOS DATOS DE LA ESCUELA Y HORAS REQUERIDAS
            horas_requeridas: candSel.horas_requeridas || 480,
            escuela_procedencia: candSel.escuela_procedencia || "",
            
            horas_llevadas: 0,
            nss: "", rfc: "", curp: "", sexo: "Indefinido",
            celular: candSel.celular, correo: candSel.correo, direccion: "",
            estado_civil: "Soltero", nacimiento: "",
            fecha_conversion: fechaConv, fecha: fechaConv,
            alta_imss: "", egreso: "", motivo: "", puesto: "Practicante",
            contacto_emergencia: "", parentesco: "", tel_emergencia1: "", tel_emergencia2: "",
            talla_uniforme:"M", tipo_sangre:"", alergias:"", nivel_ingles:"",
            documentos: candSel.documentos || [], observaciones: candSel.observaciones
        });
        guardarBD();
        alert("Candidato convertido a Practicante exitosamente.");
        candSel = null;
        mostrar("practicantes");
    }
}

function mostrarModalVacaciones(){
    // Poblar el select de empleados con los empleados activos
    let sel = document.getElementById('v_emp_id');
    sel.innerHTML = '<option value="">-- Seleccionar empleado --</option>';
    empleados
        .filter(e => !e.egreso)
        .sort((a,b) => (a.nombre+a.ap).localeCompare(b.nombre+b.ap))
        .forEach(e => {
            let opt = document.createElement('option');
            opt.value = e.id;
            opt.textContent = `${e.nombre} ${e.ap} ${e.am || ''}`.trim();
            sel.appendChild(opt);
        });
    document.getElementById('modalVacaciones').style.display='flex';
}

function guardarNuevaVacacion(){
    let empId = document.getElementById('v_emp_id').value;
    if(!empId){ alert('Debes seleccionar un empleado.'); return; }
    let emp = empleados.find(e=>e.id==empId);
    if(!emp){ alert('Empleado no encontrado.'); return; }
    let inicio = document.getElementById('v_inicio').value;
    let fin = document.getElementById('v_fin').value;
    if(!inicio || !fin){ alert('Debes indicar las fechas de inicio y fin.'); return; }
    vacaciones.unshift({
        empleado_id: parseInt(empId),
        inicio: inicio,
        fin: fin,
        dias: parseInt(document.getElementById('v_dias').value) || 0,
        tipo: document.getElementById('v_tipo').value,
        estado: 'Pendiente',
        cobertura: document.getElementById('v_cobertura').value
    });
    guardarBD();
    document.getElementById('modalVacaciones').style.display='none';
    mostrar('vacaciones');
}

function nuevoEmpleado(){
    let hoy = new Date().toISOString().split('T')[0];
    // Reset todos los campos del modal completo
    ['ne_nombre','ne_ap','ne_am','ne_nss','ne_rfc','ne_curp','ne_celular','ne_correo',
     'ne_direccion','ne_tipo_sangre','ne_alergias','ne_puesto','ne_empresa','ne_clabe',
     'ne_canal','ne_contacto','ne_parentesco','ne_tel1','ne_tel2','ne_estado_civil'].forEach(id => {
        let el = document.getElementById(id);
        if(el) el.value = '';
    });
    document.getElementById('ne_fecha').value = hoy;
    document.getElementById('ne_alta_imss').value = '';
    document.getElementById('ne_nacimiento').value = '';
    document.getElementById('ne_sexo').value = 'Indefinido';
    document.getElementById('ne_talla').value = 'M';
    document.getElementById('ne_canal').value = '';
    document.getElementById('modalNuevoEmpleado').style.display = 'flex';
}

function guardarNuevoEmpleadoDirecto(){
    let nombre = document.getElementById('ne_nombre').value.trim();
    if(!nombre){ alert('El nombre es requerido.'); return; }
    let nId = Date.now();
    let fechaHoy = new Date().toISOString().split('T')[0];
    let g = (id) => { let el = document.getElementById(id); return el ? el.value.trim() : ''; };
    let nuevoEmp = {
        id: nId,
        nombre: nombre,
        ap: g('ne_ap'),
        am: g('ne_am'),
        nacimiento: g('ne_nacimiento'),
        nss: g('ne_nss'),
        rfc: g('ne_rfc'),
        curp: g('ne_curp'),
        sexo: g('ne_sexo') || 'Indefinido',
        estado_civil: g('ne_estado_civil') || 'Soltero',
        celular: g('ne_celular'),
        correo: g('ne_correo'),
        direccion: g('ne_direccion'),
        tipo_sangre: g('ne_tipo_sangre'),
        alergias: g('ne_alergias'),
        talla_uniforme: g('ne_talla') || 'M',
        puesto: g('ne_puesto'),
        empresa: g('ne_empresa'),
        fecha: g('ne_fecha') || fechaHoy,
        alta_imss: g('ne_alta_imss'),
        clabe_bancaria: g('ne_clabe'),
        canal_captacion: g('ne_canal') || 'Directo',
        contacto_emergencia: g('ne_contacto'),
        parentesco: g('ne_parentesco'),
        tel_emergencia1: g('ne_tel1'),
        tel_emergencia2: g('ne_tel2'),
        egreso: '', motivo: '',
        documentos: [], observaciones: []
    };
    empleados.push(nuevoEmp);
    crearAniosEmpleado(nId);
    guardarBD();
    document.getElementById('modalNuevoEmpleado').style.display = 'none';
    empSel = nuevoEmp;
    mostrar('ficha');
}

function nuevoPracticante(){
    let hoy = new Date().toISOString().split('T')[0];
    // Reset todos los campos del modal completo
    ['np_nombre','np_ap','np_am','np_nss','np_rfc','np_curp','np_celular','np_correo',
     'np_direccion','np_tipo_sangre','np_alergias','np_nivel_ingles','np_puesto',
     'np_escuela','np_empresa','np_estado_civil'].forEach(id => {
        let el = document.getElementById(id);
        if(el) el.value = '';
    });
    document.getElementById('np_horas').value = '480';
    document.getElementById('np_fecha_inicio').value = hoy;
    document.getElementById('np_fecha_termino').value = '';
    document.getElementById('np_nacimiento').value = '';
    document.getElementById('np_talla').value = 'M';
    let empEl = document.getElementById('np_empresa');
    if(empEl) empEl.value = 'EuroMedica';
    document.getElementById('modalNuevoPracticante').style.display = 'flex';
}

function guardarNuevoPracticanteDirecto(){
    let nombre = document.getElementById('np_nombre').value.trim();
    if(!nombre){ alert('El nombre es requerido.'); return; }
    let nId = Date.now();
    let fechaHoy = new Date().toISOString().split('T')[0];
    let g = (id) => { let el = document.getElementById(id); return el ? el.value.trim() : ''; };
    let nuevoPract = {
        id: nId,
        nombre: nombre,
        ap: g('np_ap'),
        am: g('np_am'),
        nacimiento: g('np_nacimiento'),
        nss: g('np_nss'),
        rfc: g('np_rfc'),
        curp: g('np_curp'),
        sexo: 'Indefinido',
        estado_civil: g('np_estado_civil') || 'Soltero',
        celular: g('np_celular'),
        correo: g('np_correo'),
        direccion: g('np_direccion'),
        tipo_sangre: g('np_tipo_sangre'),
        alergias: g('np_alergias'),
        nivel_ingles: g('np_nivel_ingles'),
        talla_uniforme: g('np_talla') || 'M',
        escuela_procedencia: g('np_escuela'),
        horas_requeridas: parseInt(g('np_horas')) || 480,
        puesto_solicitado: g('np_puesto'),
        puesto: g('np_puesto'),
        empresa: g('np_empresa') || 'EuroMedica',
        fecha_inicio: g('np_fecha_inicio') || fechaHoy,
        fecha_termino: g('np_fecha_termino') || '',
        horas_llevadas: 0,
        egreso: '', motivo: '', destacado: false,
        canal_captacion: 'Directo',
        documentos: [], observaciones: []
    };
    practicantes.push(nuevoPract);
    guardarBD();
    document.getElementById('modalNuevoPracticante').style.display = 'none';
    practSel = nuevoPract;
    mostrar('ficha_practicante');
}

function guardarCambiosFicha(){
    if(citaSel){ 
        let existe = citas.find(c=>c.id==citaSel.id);
        if(!existe) citas.unshift(citaSel); // Sólo empuja si es nueva
    }
    
    guardarBD();
    // Banner verde temporal
    let banner = document.createElement('div');
    banner.innerHTML = '✅ Cambios guardados';
    banner.style.cssText = 'position:fixed;top:16px;right:16px;background:#22c55e;color:white;padding:12px 20px;border-radius:8px;font-weight:bold;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
    document.body.appendChild(banner);
    setTimeout(()=>banner.remove(), 2000);
}

function eliminarRegistro(tipo){
    if(!confirm("¿Estás seguro de que deseas eliminar este registro PERMANENTEMENTE?")) return;
    
    if(tipo==='empleado' && empSel){
        empleados = empleados.filter(e=>e.id !== empSel.id);
        empSel = null;
        guardarBD();
        mostrar('empleados');
    }
    else if(tipo==='practicante' && practSel){
        practicantes = practicantes.filter(p=>p.id !== practSel.id);
        practSel = null;
        guardarBD();
        mostrar('practicantes');
    }
    else if(tipo==='candidato' && candSel){
        candidatos = candidatos.filter(c=>c.id !== candSel.id);
        candSel = null;
        guardarBD();
        mostrar('candidatos');
    }
    else if(tipo==='cita' && citaSel){
        citas = citas.filter(c=>c.id !== citaSel.id);
        citaSel = null;
        guardarBD();
        mostrar('citas');
    }
}

function eliminarVacacionGlobal(index){
    if(confirm("¿Eliminar solicitud de vacación permanentemente?")){
        vacaciones.splice(index, 1);
        guardarBD();
        mostrar('vacaciones');
    }
}

// === FUNCIONES DE CITAS ===
function nuevaCita(){
    let nId = Date.now();
    let nuevaCita = {
        id: nId,
        nombre: "",
        puesto: "",
        tipo: 'Trabajador',
        fecha: new Date().toISOString().split('T')[0],
        hora: "",
        entrevistador_rh: "",
        jefe_depto: "",
        celular: "",
        correo: "",
        notas: "",
        estado: 'Agendada',
        fecha_creacion: new Date().toISOString().split('T')[0],
        documentos: []
    };
    citaSel = nuevaCita;
    empSel=null; practSel=null; candSel=null;
    mostrar("ficha_cita");
}

function seleccionarCita(id){
    citaSel = citas.find(c=>c.id==id);
    empSel=null; practSel=null; candSel=null;
    if (citaSel) {
        citaSel.modo_actual = 'editar';
    }
    mostrar("ficha_cita");
}

function cambiarModoVistaCita(modo) {
    if (!citaSel) return;
    citaSel.modo_actual = modo;
    guardarEstadoNavegacionRH('ficha_cita');
    
    let vistaEdit = document.getElementById('vista_modo_editar_cita');
    let vistaEnt = document.getElementById('vista_modo_entrevista_cita') || document.getElementById('vista_modo_empezar_cita');
    let btnEdit = document.getElementById('btn_modo_editar');
    let btnEnt = document.getElementById('btn_modo_entrevista') || document.getElementById('btn_modo_empezar');
    
    if (vistaEdit && vistaEnt) {
        if (modo === 'entrevista' || modo === 'empezar') {
            vistaEdit.style.display = 'none';
            vistaEnt.style.display = 'block';
            if (btnEdit) {
                btnEdit.style.background = 'transparent';
                btnEdit.style.color = '#ffffff';
            }
            if (btnEnt) {
                btnEnt.style.background = '#16a34a';
                btnEnt.style.color = '#ffffff';
            }
        } else {
            vistaEdit.style.display = 'block';
            vistaEnt.style.display = 'none';
            if (btnEdit) {
                btnEdit.style.background = '#ffffff';
                btnEdit.style.color = '#1e3a8a';
            }
            if (btnEnt) {
                btnEnt.style.background = 'transparent';
                btnEnt.style.color = '#ffffff';
            }
        }
    }
}

function subirArchivoCita(input){
    if(input.files && input.files[0]){
        let reader = new FileReader();
        let file = input.files[0];
        let isImage = file.type.startsWith("image/");
        reader.onload = function(e){
            if(e.target.result.length > 2_000_000){
                alert("Este archivo es demasiado grande para guardarse localmente.");
                return;
            }
            citaSel.documentos = [{
                url: e.target.result,
                tipo: isImage ? 'imagen' : 'pdf',
                nombre: file.name
            }];
            guardarBD();
            mostrar("ficha_cita");
        };
        reader.readAsDataURL(file);
    }
}

function eliminarCVCita(){
    if(!confirm("¿Eliminar archivo adjunto?")) return;
    citaSel.documentos = [];
    guardarBD();
    mostrar("ficha_cita");
}

function noSePresentoCita(){
    if(!citaSel) return;

    if(!confirm("¿Marcar esta cita como 'No se presentó'?")) return;

    // 1. actualizar ficha
    citaSel.estado = "No se presentó";

    // 2. actualizar base REAL (array citas)
    let idx = citas.findIndex(c => c.id == citaSel.id);
    if(idx !== -1){
        citas[idx].estado = "No se presentó";
    }

    // 3. nota automática
    if(!citas[idx].notas){
        citas[idx].notas = "";
    }

    citas[idx].notas += "\n[" + new Date().toLocaleString() + "] No se presentó.";

    guardarBD();

    alert("Cita enviada al historial como 'No se presentó'");

    citaSel = null;

    mostrar("citas");
}
function marcarCitaRealizada(idx){
    citas[idx].estado = 'Realizada';
    guardarBD();
    mostrar('citas');
}



function eliminarCita(idx){
    if(confirm('¿Eliminar esta cita?')){
        citas.splice(idx, 1);
        guardarBD();
        mostrar('citas');
    }
}


function exportarFichaPDF(){
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    let y = 20;
    
    doc.setFontSize(22);
    doc.setTextColor(30, 58, 138);
    
    if(empSel) {
        doc.text("FICHA DE EMPLEADO", 10, y);
        y += 15; doc.setFontSize(14); doc.setTextColor(0,0,0);
        doc.text(`Nombre: ${empSel.nombre} ${empSel.ap} ${empSel.am}`, 10, y); y+=10;
        doc.text(`Puesto: ${empSel.puesto}`, 10, y); y+=10;
        doc.text(`NSS: ${empSel.nss}  |  CURP: ${empSel.curp}  |  RFC: ${empSel.rfc}`, 10, y); y+=10;
        doc.text(`Contacto: ${empSel.celular}  |  Emergencia: ${empSel.contacto_emergencia}`, 10, y);
    } else if (practSel) {
        doc.text("FICHA DE PRACTICANTE", 10, y);
        y += 15; doc.setFontSize(14); doc.setTextColor(0,0,0);
        doc.text(`Nombre: ${practSel.nombre} ${practSel.ap} ${practSel.am}`, 10, y); y+=10;
        doc.text(`Horas llevadas: ${practSel.horas_llevadas}/${practSel.horas_requeridas}`, 10, y); y+=10;
        doc.text(`NSS: ${practSel.nss}  |  CURP: ${practSel.curp}  |  RFC: ${practSel.rfc}`, 10, y); y+=10;
        doc.text(`Contacto: ${practSel.celular}`, 10, y);
    } else if (candSel) {
        doc.text("FICHA DE CANDIDATO", 10, y);
        y += 15; doc.setFontSize(14); doc.setTextColor(0,0,0);
        doc.text(`Nombre: ${candSel.nombre} ${candSel.ap} ${candSel.am}`, 10, y); y+=10;
        doc.text(`Tipo de vacante: Para ${candSel.tipo_candidatura}`, 10, y); y+=10;
        doc.text(`Puesto deseado: ${candSel.puesto_deseado}`, 10, y); y+=10;
        doc.text(`Nivel Educativo: ${candSel.nivel_educativo}`, 10, y); y+=10;
        doc.text(`Estatus de Reclutamiento: ${candSel.estatus_reclutamiento}`, 10, y); y+=10;
        doc.text(`Calificación (0-5): ${candSel.calificacion}`, 10, y); y+=10;
        doc.text(`Expectativa Salarial / Beca: ${candSel.expectativa_salarial}`, 10, y);
    }
    
    doc.save("Ficha_Documental.pdf");
}



// Inicialización por defecto con restauración de estado (F5)
if (!restaurarEstadoNavegacionRH()) {
    mostrar("citas");
}

/**
 * Aprueba una solicitud de vacaciones validando disponibilidad de días
 */
function aprobarVacacionFicha(index){
let e = empSel;
let registro = vacacionesAnuales.find(v=> v.empleado_id===e.id && v.anio===e.anioSeleccionado);
let diasTotales = registro ? registro.dias_totales : 0;
let usados = vacaciones.filter(v=> v.empleado_id===e.id && new Date(v.inicio).getFullYear() === e.anioSeleccionado).reduce((a,v)=>a+v.dias,0);
let disponibles = diasTotales - usados;

if(disponibles <= 0){
    alert("Este año ya no tiene días disponibles");
    return;
}

vacaciones[index].estado="Aprobadas";
mostrar("ficha");
}

// Manejador global para saltar al siguiente input con tecla Enter
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        let nodeName = e.target.nodeName;
        // Evitar saltos en textarea o search
        if (nodeName === 'TEXTAREA' || e.target.type === 'search') return;
        if (nodeName === 'INPUT' || nodeName === 'SELECT') {
            e.preventDefault();
            let formElements = Array.from(document.querySelectorAll('input:not([type="hidden"]), select, textarea'));
            let index = formElements.indexOf(e.target);
            if (index > -1 && index < formElements.length - 1) {
                let nextEl = formElements[index + 1];
                // Intentar encontrar el siguiente no oculto ni readonly
                while(nextEl && (nextEl.style.display === 'none' || nextEl.readOnly)){
                     index++;
                     nextEl = formElements[index + 1];
                }
                if(nextEl) nextEl.focus();
            }
        }
    }
});
function pasarFichaCitaACandidato() {
    if(!citaSel) return;
    if(!confirm("¿Deseas aprobar esta cita y convertirla en Candidato?")) return;

    let nId = Date.now();
    let nC = {
        id: nId,
        nombre: citaSel.nombre,
        ap: "", 
        am: "",
        tipo_candidatura: citaSel.tipo, 
        puesto_deseado: citaSel.puesto,
        expectativa_salarial: "",
        nivel_educativo: "",
        fecha_postulacion: citaSel.fecha,
        fecha_agendado: new Date().toISOString().split('T')[0],
        fecha_entrevista: "",
        horarios_disponibles: "",
        estatus_reclutamiento: "Pendiente",
        calificacion: 0,
        celular: citaSel.celular,
        correo: citaSel.correo,
        canal_captacion: "",
        documentos: citaSel.documentos,
        observaciones: citaSel.notas ? [{fecha: new Date().toLocaleString(), texto: citaSel.notas}] : [],
        // Pasamos los datos del practicante
        horas_requeridas: citaSel.horas_requeridas || 480,
        escuela_procedencia: citaSel.escuela_procedencia || ""
    };
    
    candidatos.push(nC);
    citaSel.estado = "Realizada"; 
    guardarBD();
    
    alert("Cita convertida a Candidato exitosamente.");
    
    citaSel = null; 
    filtroCandidatoTipo = nC.tipo_candidatura; 
    mostrar("candidatos");
}

function guardarHoras() {
    if(!practSel) return;
    
    // Obtenemos los valores de los dos inputs
    let inputHorasLlevadas = document.getElementById("horasInput").value;
    let inputHorasRequeridas = document.getElementById("horasReqInput").value;
    
    // Actualizamos el objeto seleccionado
    practSel.horas_llevadas = parseInt(inputHorasLlevadas) || 0;
    practSel.horas_requeridas = parseInt(inputHorasRequeridas) || 480;
    
    guardarCambiosFicha();
}
//baja de empleados
function confirmarBaja() {
    if(!empSel) return;
    
    let motivo = document.getElementById('motivoBaja').value;
    if(motivo.trim() === "") {
        alert("Por favor, ingresa el motivo de la baja.");
        return;
    }

    // 1. Guardar motivo y fecha
    empSel.motivo = motivo;
    empSel.egreso = new Date().toISOString().split('T')[0]; // Fecha actual
    
    // 2. Guardar en BD
    guardarBD();
    
    // 3. Cerrar modal y redirigir
    document.getElementById('modalBaja').style.display = 'none';
    alert("Empleado dado de baja correctamente.");
    
    // 4. Limpiar selección y regresar al listado
    empSel = null;
    mostrar("empleados");
}

// Usamos un objeto para evitar conflictos con otros archivos
const AppRH = {
    confirmarBajaPracticante: function() {
        if (!window.practSel) return;
        
        window.practSel.egreso = new Date().toISOString().split('T')[0];
        window.practSel.motivo = document.getElementById('baja_motivo').value || 'Sin especificar';
        window.practSel.destacado = document.getElementById('baja_destacado').checked;
        
        // Guardamos y refrescamos
        if(typeof guardarBD === 'function') guardarBD();
        
        document.getElementById('modalBajaPracticante').style.display = 'none';
        alert("Practicante dado de baja.");
        
        window.practSel = null; 
        if(typeof mostrar === 'function') mostrar("practicantes");
    }
};
function mostrarVistaPracticantes() {
    try {
        console.log("Intentando renderizar practicantes...");
        // Tu lógica de renderizado aquí...
    } catch (e) {
        console.error("Error crítico en vista practicantes:", e);
    }
}
function darDeBajaPracticante() {
    if (!practSel) return;
    document.getElementById('txtNombreBaja').innerText = `¿Dar de baja a ${practSel.nombre}?`;
    document.getElementById('modalBajaPracticante').style.display = 'flex';
}

function confirmarBajaPracticante() {
    if (!practSel) return;
    
    // 1. Asignar fecha de egreso y datos
    practSel.egreso = new Date().toISOString().split('T')[0];
    practSel.motivo = document.getElementById('baja_motivo').value || 'Sin especificar';
    practSel.destacado = document.getElementById('baja_destacado').checked;
    
    // 2. Guardar en tu base de datos
    guardarBD(); 
    
    // 3. Cerrar, avisar y recargar
    document.getElementById('modalBajaPracticante').style.display = 'none';
    alert("Practicante movido al historial.");
    
    practSel = null; 
    // Forzamos a que se muestre el historial para que veas el cambio
    vistaPract = 'historial'; 
    mostrar("practicantes");
}
function subirDocumento() {
    let nombre = document.getElementById('docNombre').value;
    let fileInput = document.getElementById('docFile');
    
    if(!nombre || fileInput.files.length === 0) {
        alert("Escribe un nombre y selecciona un archivo.");
        return;
    }

    let reader = new FileReader();
    reader.onload = function(e) {
        if (!practSel.documentos) practSel.documentos = [];
        
        practSel.documentos.push({
            nombre: nombre,
            data: e.target.result
        });
        
        guardarBD(); // <--- ESTO ES LA CLAVE: guarda inmediatamente
        renderizarDocumentos(); // <--- Actualiza la vista
        
        // Limpiar inputs
        document.getElementById('docNombre').value = "";
        document.getElementById('docFile').value = "";
    };
    reader.readAsDataURL(fileInput.files[0]);
}

function renderizarDocumentos() {
    let tbody = document.getElementById('listaDocumentos');
    
    // Si no encuentra el elemento, no significa que haya error, 
    // significa que la vista no es la ficha. Salimos tranquilamente.
    if(!tbody) return;

    // Usamos el array de documentos del practicante actual
    // Si es undefined, usamos un array vacío
    let documentos = practSel.documentos || [];
    
    if(documentos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align:center; color:#6b7280; padding:15px;">No hay documentos subidos.</td></tr>';
        return;
    }
    
    tbody.innerHTML = documentos.map((doc, index) => `
        <tr>
            <td style="padding:10px;">📄 ${doc.nombre}</td>
            <td style="padding:10px; text-align:center;">
                <a href="${doc.data}" target="_blank" class="btn-ver" style="display:inline-block; margin-right:5px; padding:4px 8px; font-size:12px;">Ver</a>
                <button onclick="eliminarDocumento(${index})" class="btn-ver" style="display:inline-block; padding:4px 8px; font-size:12px; background:#ef4444;">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

function eliminarDocumento(index) {
    if (!confirm("¿Eliminar este documento?")) return;
    
    if (practSel && practSel.documentos) {
        practSel.documentos.splice(index, 1);
        guardarBD(); // Guardamos el cambio
        renderizarDocumentos(); // Recargamos la tabla
    }
}

// Función para abrir el modal y cargar practicantes
function abrirModalContrato() {
    let select = document.getElementById('contratoPracticante');
    select.innerHTML = practicantes.map(p => `<option value="${p.id}">${p.nombre} ${p.ap}</option>`).join('');
    document.getElementById('modalNuevoContrato').style.display = 'flex';
}

// Función para guardar
function guardarContrato() {
    let datos = {
        nombre: document.getElementById('contratoNombre').value,
        tipo: document.getElementById('contratoTipo').value,
        mes1: document.getElementById('Mes1').value,
        mes2: document.getElementById('Mes2').value,
        mes3: document.getElementById('Mes3').value,
        indefinido: document.getElementById('Indefinido').value
    };

    if (typeof contratos !== 'undefined') {
        contratos.push(datos);
    }

    if (typeof guardarBD === 'function') {
        guardarBD();
    }

    if (document.getElementById('modalNuevoContrato')) {
        document.getElementById('modalNuevoContrato').style.display = 'none';
    }
}


// NO debe haber nada más después de esto.
// --- CIERRE DE SCRIPT ---
</script>

@endsection
