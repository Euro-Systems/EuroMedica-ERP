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
.rh-card{
    background:#fff;
    padding:10px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    margin-bottom:10px;
}

/* Layout de la ficha técnica usando Grid (2 columnas) */
.ficha-wrap{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
    align-items:start;
}

.col{
    display:flex;
    flex-direction:column;
    gap:10px;
    min-height:0;
}

/* Cuadrícula para formularios de datos de empleados */
.empleado-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:8px;
}

.empleado-grid b{
    font-size:12px;
    color:#6b7280;
}

.empleado-grid input{
    width:100%;
    padding:5px;
    border:1px solid #d1d5db;
    border-radius:6px;
}

.radio-group{
    display:flex;
    gap:10px;
}

/* Estilos para tablas de datos */
.rh-table{
    width:100%;
    border-collapse:collapse;
}

.rh-table th,.rh-table td{
    padding:10px;
    border-bottom:1px solid #e2e8f0;
}

.rh-table th{
    background:#1e3a8a;
    color:white;
}

.btn-ver{
    background:#1e3a8a;
    color:white;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
    display:block;
    margin:auto;
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
  <div style="background:#fff;padding:25px;border-radius:12px;width:400px;max-width:90%;">
    <h3>Solicitar Vacaciones / Permiso</h3>
    <div class="empleado-grid" style="display:flex;flex-direction:column;gap:10px;">
      <div><b>ID de Empleado</b><input type="number" id="v_emp_id" placeholder="Ej. 1"></div>
      <div><b>Fecha Inicio</b><input type="date" id="v_inicio"></div>
      <div><b>Fecha Fin</b><input type="date" id="v_fin"></div>
      <div><b>Días a descontar</b><input type="number" id="v_dias"></div>
      <div><b>Tipo</b><select id="v_tipo" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;"><option>Vacaciones</option><option>Permiso</option></select></div>
      <div><b>Persona que cubre</b><input type="text" id="v_cobertura" placeholder="Nombre completo"></div>
    </div>
    <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px;">
      <button class="btn-ver" style="background:#6b7280;" onclick="document.getElementById('modalVacaciones').style.display='none'">Cancelar</button>
      <button class="btn-ver" style="background:#22c55e;" onclick="guardarNuevaVacacion()">Guardar Solicitud</button>
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
citas.forEach(ci => sanitizeJSON(ci, ['documentos']));

const csrfToken = '{{ csrf_token() }}';

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
new Date(b.fecha+" "+(b.hora||"00:00"))
-
new Date(a.fecha+" "+(a.hora||"00:00"))
)
.map((ci,idx)=>`
<tr style="cursor:pointer;" onclick="seleccionarCita('${ci.id}')">
<td>${ci.nombre}</td>
<td>${ci.puesto}</td>
<td>${ci.tipo}</td>
<td>${formatearFecha(ci.fecha)}</td>
<td>${ci.hora}</td>
<td>${ci.entrevistador_rh}</td>
<td>${ci.jefe_depto||'N/A'}</td>
<td><span style="font-weight:bold;color:${ci.estado==='Realizada'?'green':ci.estado==='Cancelada'?'red':'#ca8a04'}">${ci.estado}</span></td>
</tr>
`).join('')}
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
html=`
<div class="tabs">
<div class="tab" onclick="mostrar('citas')">Agendar Citas</div>
<div class="tab active" onclick="mostrar('ficha_cita')">Detalle de Cita</div>
</div>
<div class="rh-card"><h2>Cita: ${ci.nombre}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos de la Cita</h3>
<div class="empleado-grid">
<div><b>Nombre del aspirante</b><input value="${ci.nombre}" onchange="citaSel.nombre=this.value"></div>
<div><b>Puesto deseado</b><input value="${ci.puesto}" onchange="citaSel.puesto=this.value"></div>
<div><b>Sector</b><select onchange="citaSel.tipo=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="Trabajador" ${ci.tipo==='Trabajador'?'selected':''}>Trabajador</option>
    <option value="Practicante" ${ci.tipo==='Practicante'?'selected':''}>Practicante</option>
</select></div>
<div><b>Fecha de cita</b><input type="date" value="${ci.fecha || ''}" onchange="citaSel.fecha=this.value"></div>
<div><b>Hora</b><input type="time" value="${ci.hora}" onchange="citaSel.hora=this.value"></div>
<div><b>Entrevistador RH</b><input value="${ci.entrevistador_rh}" onchange="citaSel.entrevistador_rh=this.value"></div>
<div><b>Jefe Depto.</b><input value="${ci.jefe_depto}" onchange="citaSel.jefe_depto=this.value"></div>
<div><b>Celular</b><input value="${ci.celular}" onchange="citaSel.celular=this.value"></div>
<div><b>Correo</b><input value="${ci.correo}" onchange="citaSel.correo=this.value"></div>
</div>
</div>
</div>
<div class="col">
<div class="rh-card">
<h3>Notas previas</h3>
<textarea style="width:100%;height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" onchange="citaSel.notas=this.value" placeholder="Observaciones preliminares...">${ci.notas||''}</textarea>
</div>
<div class="rh-card">
<h3>CV Preliminar</h3>
<button class="btn-ver" onclick="document.getElementById('fileUploadCita').click()" style="background:#3b82f6;">Subir Archivo</button>
<input type="file" id="fileUploadCita" style="display:none" onchange="subirArchivoCita(this)">
<hr>
${(ci.documentos || []) && ((ci.documentos || []) || []).length > 0 ? `
<div style="text-align:center;">
${(ci.documentos || [])[0].tipo==='imagen' ? `<img src="${(ci.documentos || [])[0].url}" style="width:120px;cursor:pointer;" onclick="ver('${(ci.documentos || [])[0].url}')"><br>` : `<div style="padding:10px;background:#e5e7eb;font-weight:bold;">PDF</div>`}
<small>${(ci.documentos || [])[0].nombre||'CV'}</small><br>
<button class="btn-ver" onclick="descargarURL('${(ci.documentos || [])[0].url}')">Descargar</button>
<button class="btn-ver" style="background:#ef4444;" onclick="eliminarCVCita()">✕</button>
</div>
` : '<p style="color:#6b7280;text-align:center;">Sin CV cargado</p>'}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
<div style="display:flex; gap:8px;">
<button class="btn-ver" style="background:#3b82f6; padding:5px 10px; font-size:13px;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#facc15; padding:5px 10px; font-size:13px; color:black;" onclick="citaSel.estado='Realizada';guardarCambiosFicha();mostrar('citas')">Marcar como Realizada</button>
<button class="btn-ver" style="background:#ef4444; padding:5px 10px; font-size:13px;" onclick="eliminarRegistro('cita')">Eliminar Registro</button>
<button 
  class="btn-ver" 
  style="background:#6b7280; margin:0; flex:1;" 
  onclick="noSePresentoCita()">
   No se presentó
</button>
</div>
<button class="btn-ver" onclick="pasarFichaCitaACandidato()" style="background:#22c55e; padding:5px 12px; font-size:14px;">✅ Aprobar y Convertir a Candidato</button>
</div>
</div>`;
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

html=`
<div class="tabs">
<div class="tab" onclick="mostrar('empleados')">Empleados</div>
<div class="tab active" onclick="mostrar('ficha')">Ficha Detalle</div>
</div>
<div class="rh-card">
<h2>${e.nombre} ${e.ap} ${e.am}</h2>
</div>

<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${e.nombre}" onchange="empSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${e.ap}" onchange="empSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${e.am}" onchange="empSel.am=this.value"></div>
<div><b>NSS</b><input value="${e.nss}" onchange="empSel.nss=this.value"></div>
<div><b>RFC</b><input value="${e.rfc}" onchange="empSel.rfc=this.value"></div>
<div><b>CURP</b><input value="${e.curp}" onchange="empSel.curp=this.value"></div>
<div><b>Género</b><div class="radio-group">
<label><input type="radio" onchange="empSel.sexo='Hombre'" name="rSexo" ${e.sexo==="Hombre"?"checked":""}> Hombre</label>
<label><input type="radio" onchange="empSel.sexo='Mujer'" name="rSexo" ${e.sexo==="Mujer"?"checked":""}> Mujer</label>
</div></div>
<div><b>Celular</b><input value="${e.celular}" onchange="empSel.celular=this.value"></div>
<div><b>Dirección</b><input value="${e.direccion}" onchange="empSel.direccion=this.value"></div>
<div><b>Estado civil</b><input value="${e.estado_civil}" onchange="empSel.estado_civil=this.value"></div>
<div><b>Fecha nacimiento</b><input type="date" value="${e.nacimiento || ''}" onchange="empSel.nacimiento=this.value"></div>
<div><b>Talla Uniforme</b><select onchange="empSel.talla_uniforme=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="S" ${e.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
    <option value="M" ${e.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
    <option value="L" ${e.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
    <option value="XL" ${e.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
</select></div>
<div><b>Tipo de Sangre</b><input value="${e.tipo_sangre||''}" onchange="empSel.tipo_sangre=this.value" placeholder="O+"></div>
<div><b>Alergias/Med.</b><input value="${e.alergias||''}" onchange="empSel.alergias=this.value" placeholder="Ninguna"></div>
<div><b>Canal Captación</b><input value="${e.canal_captacion||''}" onchange="empSel.canal_captacion=this.value" placeholder="Facebook"></div>
<div><b>CLABE Bancaria</b><input value="${e.clabe_bancaria||''}" onchange="empSel.clabe_bancaria=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Contacto de emergencia</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${e.contacto_emergencia}" onchange="empSel.contacto_emergencia=this.value"></div>
<div><b>Parentesco</b><input value="${e.parentesco}" onchange="empSel.parentesco=this.value"></div>
<div><b>Teléfono 1</b><input value="${e.tel_emergencia1}" onchange="empSel.tel_emergencia1=this.value"></div>
<div><b>Teléfono 2</b><input value="${e.tel_emergencia2}" onchange="empSel.tel_emergencia2=this.value"></div>
</div>
<h3>Contacto de emergencia 2</h3>
<div class="empleado-grid">
<div><b>Contacto 2</b><input value="${e.contacto_emergencia}" onchange="empSel.contacto_emergencia=this.value"></div>
<div><b>Parentesco</b><input value="${e.parentesco}" onchange="empSel.parentesco=this.value"></div>
<div><b>Teléfono 1</b><input value="${e.tel_emergencia1}" onchange="empSel.tel_emergencia1=this.value"></div>
<div><b>Teléfono 2</b><input value="${e.tel_emergencia2}" onchange="empSel.tel_emergencia2=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Historial de observaciones</h3>
<textarea id="txtObservacion" style="width:100%;height:90px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" placeholder="Escribe una observación..."></textarea>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarObservacion()">Guardar</button></div>
<hr>
<div class="obs-list">
${(e.observaciones || []).length===0 ? `<div id="noObs">Sin observaciones registradas.</div>` : 
(e.observaciones || []).map(o=>`<div class="obs-item"><div class="obs-fecha">${o.fecha}</div><div>${o.texto}</div></div>`).join('')}
</div>
</div>
</div>

<div class="col">
<div class="rh-card">
<h3>Datos laborales</h3>
<div class="empleado-grid">
<div><b>Puesto</b><input value="${e.puesto}" onchange="empSel.puesto=this.value"></div>
<div><b>Empresa</b><input value="${e.empresa}" onchange="empSel.empresa=this.value" placeholder="Nombre de la empresa"></div>
<div><b>Fecha inicio</b><input type="date" value="${e.fecha || ''}" onchange="empSel.fecha=this.value"></div>
<div><b>Alta IMSS</b><input type="date" value="${e.alta_imss || ''}" onchange="empSel.alta_imss=this.value"></div>
<div><b>Fecha egreso</b><input value="${e.egreso}" onchange="empSel.egreso=this.value" placeholder="YYYY-MM-DD"></div>
<div><b>Motivo Egreso</b><input value="${e.motivo}" onchange="empSel.motivo=this.value"></div>
<button class="btn-ver" style="background:#ef4444; margin-top:20px;" onclick="document.getElementById('modalBaja').style.display='flex'">
    Dar de baja a este empleado
</button>
</div>
</div>

<div class="rh-card">
<h3>Vacaciones</h3>
<div style="margin-bottom:10px;">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:10px;">
<div><b>Año:</b><div class="radio-group">
${aniosEmpleado.map(a=>`
<label style="padding:6px 12px;border-radius:20px;border:1px solid #d1d5db;cursor:pointer;background:${e.anioSeleccionado==a ? '#1e3a8a' : '#fff'};color:${e.anioSeleccionado==a ? '#fff' : '#000'};">
<input type="radio" name="anioVacaciones" value="${a}" onchange="cambiarAnio(this.value)" style="display:none" ${e.anioSeleccionado==a ? 'checked' : ''}>${a}
</label>`).join('')}
</div></div>
<div><b>Días disponibles:</b> ${disponibles} / ${diasTotales}</div>
</div>
${disponibles<=0 ? `<div style="background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px;">⚠ Ya no tiene días disponibles</div>` : ``}
</div>

<table class="rh-table">
<thead><tr><th>Inicio contrato</th><th>Inicio</th><th>Fin</th><th>Días</th><th>Tipo</th><th>Estado</th><th>Cobertura</th><th>Acción</th></tr></thead>
<tbody>
${vacEmp.map(v=>`
<tr>
<td>${formatearFecha(e.fecha)}</td>
<td>${formatearFecha(v.inicio)}</td>
<td>${formatearFecha(v.fin)}</td>
<td>${v.dias}</td>
<td>${v.tipo}</td>
<td><span style="padding:4px 8px;border-radius:6px;background:${v.estado==="Aprobadas"?"#22c55e":v.estado==="Pendiente"?"#facc15":"#ef4444"};color:white;font-size:12px;">${v.estado}</span></td>
<td>${v.cobertura}</td>
<td style="text-align:center;">
<button class="btn-ver" onclick="aprobarVacacionFicha(${v.index})" ${disponibles<=0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>Aprobar</button>
<button class="btn-ver" onclick="rechazarVacacionFicha(${v.index})" ${disponibles<=0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>Rechazar</button>
<button
    class="btn-ver"
    style="background:#ef4444;"
    onclick="marcarNoPresentado()">
     No se presentó
</button>
</td>
</tr>`).join('')}
</tbody>
</table>
</div>

<div class="rh-card">
<h3>Documentos</h3>
<button class="btn-ver" onclick="escanear()">Escanear documento</button>
<div id="dwtcontrolContainer"></div>
<hr>
${(e.documentos || []).length===0 ? "Sin documentos" : (e.documentos || []).map(d=>`
<div style="display:inline-block;margin:5px;">
<img src="${d.url}" onclick="ver('${d.url}')" style="width:120px;cursor:pointer;"><br>
<button class="btn-ver" onclick="descargarPDF('${d.url}')">PDF</button>
</div>`).join('')}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<h3>Acciones de Ficha</h3>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#3b82f6; margin:0;" onclick="guardarBD()">Guardar Cambios</button>
<button class="btn-ver" style="background:#ef4444; margin:0;" onclick="document.getElementById('modalBaja').style.display='flex'">Dar de Baja</button>
</div>
</div>
`;
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
html = `
<div class="tabs">
<div class="tab" onclick="mostrar('practicantes')">Practicantes</div>
<div class="tab active" onclick="mostrar('ficha_practicante')">Ficha Detalle</div>
</div>
<div class="rh-card"><h2>${p.nombre} ${p.ap} ${p.am}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${p.nombre}" onchange="practSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${p.ap}" onchange="practSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${p.am}" onchange="practSel.am=this.value"></div>
<div><b>NSS</b><input value="${p.nss}" onchange="practSel.nss=this.value"></div>
<div><b>RFC</b><input value="${p.rfc}" onchange="practSel.rfc=this.value"></div>
<div><b>CURP</b><input value="${p.curp}" onchange="practSel.curp=this.value"></div>
<div><b>Celular</b><input value="${p.celular}" onchange="practSel.celular=this.value"></div>
<div><b>Dirección</b><input value="${p.direccion}" onchange="practSel.direccion=this.value"></div>
<div><b>Estado civil</b><input value="${p.estado_civil}" onchange="practSel.estado_civil=this.value"></div>
<div><b>Fecha nacimiento</b><input type="date" value="${p.nacimiento}" onchange="practSel.nacimiento=this.value"></div>
<div><b>Talla Uniforme</b><select onchange="practSel.talla_uniforme=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="S" ${p.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
    <option value="M" ${p.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
    <option value="L" ${p.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
    <option value="XL" ${p.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
</select></div>
<div><b>Tipo de Sangre</b><input value="${p.tipo_sangre||''}" onchange="practSel.tipo_sangre=this.value"></div>
<div><b>Alergias</b><input value="${p.alergias||''}" onchange="practSel.alergias=this.value"></div>
<div><b>Nivel Inglés</b><input value="${p.nivel_ingles||''}" onchange="practSel.nivel_ingles=this.value"></div>
</div>
</div>
</div>
<div class="col">
<div class="rh-card">
<div class="rh-card">
<h3>Control de horas</h3>
<div class="empleado-grid">
<div><b>Horas requeridas</b><input type="number" id="horasReqInput" value="${p.horas_requeridas}"></div>
<div><b>Horas acumuladas</b><input type="number" id="horasInput" value="${p.horas_llevadas}"></div>
</div>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarHoras()">Guardar horas</button></div>
</div>
<div class="rh-card">
<h3>Periodo</h3>
<div class="empleado-grid">
<div><b>Escuela de procedencia</b><input value="${p.escuela_procedencia || ''}" onchange="practSel.escuela_procedencia=this.value"></div>
<div><b>Fecha inicio</b><input type="date" value="${p.fecha_inicio || ''}" onchange="practSel.fecha_inicio=this.value"></div>
<div><b>Fecha término</b><input type="date" value="${p.fecha_termino || ''}" onchange="practSel.fecha_termino=this.value"></div>
</div>
</div>
</div>
</div>
</div>
<div class="rh-card" style="margin-top:20px; border-left: 5px solid #1e3a8a;">
    <h3 style="margin-top:0;">📂 Documentos Digitales</h3>
    <div style="display:flex; gap:10px; align-items:center; background:#f9fafb; padding:15px; border-radius:8px;">
        <input type="text" id="docNombre" placeholder="Nombre (ej. CV, Acta)" style="padding:8px; border:1px solid #d1d5db; border-radius:4px; flex:1;">
        <input type="file" id="docFile">
        <button onclick="subirDocumento()" class="btn-ver" style="margin:0; background:#22c55e;">Subir Archivo</button>
    </div>

    <table class="rh-table" style="margin-top:15px;">
        <thead>
            <tr style="background:#3b82f6;">
                <th style="color:white; padding:10px;">Nombre del Documento</th>
                <th style="color:white; padding:10px; width:150px;">Acción</th>
            </tr>
        </thead>
        <tbody id="listaDocumentos">
            <!-- Se carga dinámicamente -->
        </tbody>
    </table>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<h3>Acciones de Ficha</h3>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#3b82f6; margin:0;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#eab308; margin:0;" onclick="exportarFichaPDF()">Exportar PDF</button>
<button class="btn-ver" style="background:#ef4444; margin:0;" onclick="eliminarRegistro('practicante')">Eliminar Registro</button>

<button class="btn-ver" style="background:#f97316; margin:0;" onclick="darDeBajaPracticante()">Dar de Baja</button>
</div>
</div>`;
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
html = `
<div class="tabs">
<div class="tab" onclick="mostrar('candidatos')">Candidatos</div>
<div class="tab active" onclick="mostrar('ficha_candidato')">Ficha Detalle</div>
</div>
<div class="rh-card"><h2>Candidato: ${c.nombre} ${c.ap} ${c.am}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${c.nombre}" onchange="candSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${c.ap}" onchange="candSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${c.am}" onchange="candSel.am=this.value"></div>
<div><b>Celular</b><input value="${c.celular}" onchange="candSel.celular=this.value"></div>
<div><b>Correo</b><input value="${c.correo}" onchange="candSel.correo=this.value"></div>
<div><b>Nivel educativo</b><input value="${c.nivel_educativo}" onchange="candSel.nivel_educativo=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Historial y Entrevistas</h3>
<div class="empleado-grid">
<div><b>Estatus Actual</b>
<select onchange="candSel.estatus_reclutamiento=this.value; mostrar('ficha_candidato');" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="Pendiente" ${c.estatus_reclutamiento==='Pendiente'?'selected':''}>Pendiente</option>
    <option value="En Entrevista" ${c.estatus_reclutamiento==='En Entrevista'?'selected':''}>En Entrevista</option>
    <option value="Prueba Técnica" ${c.estatus_reclutamiento==='Prueba Técnica'?'selected':''}>Prueba Técnica</option>
    <option value="Rechazado" ${c.estatus_reclutamiento==='Rechazado'?'selected':''}>Rechazado</option>
    <option value="Contratado" ${c.estatus_reclutamiento==='Contratado'?'selected':''}>Contratado</option>
</select>
</div>
<div><b>Calificación del perfil</b>
<select onchange="candSel.calificacion=parseInt(this.value); mostrar('ficha_candidato');" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="0" ${c.calificacion===0?'selected':''}>0 Estrellas</option>
    <option value="1" ${c.calificacion===1?'selected':''}>1 Estrella</option>
    <option value="2" ${c.calificacion===2?'selected':''}>2 Estrellas</option>
    <option value="3" ${c.calificacion===3?'selected':''}>3 Estrellas</option>
    <option value="4" ${c.calificacion===4?'selected':''}>4 Estrellas</option>
    <option value="5" ${c.calificacion===5?'selected':''}>5 Estrellas</option>
</select>
</div>
</div>
</div>

<div class="rh-card">
<h3>Notas de Entrevista (Observaciones)</h3>
<textarea id="txtObservacion" style="width:100%;height:90px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" placeholder="Escribe notas de la entrevista o del perfil..."></textarea>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarObservacionCand()">Guardar Nota</button></div>
<hr>
<div class="obs-list">
${(c.observaciones || []).length===0 ? `<div id="noObs">Sin notas registradas.</div>` : 
(c.observaciones || []).map(o=>`<div class="obs-item"><div class="obs-fecha">${o.fecha}</div><div>${o.texto}</div></div>`).join('')}
</div>
</div>
</div>

<div class="col">
<div class="rh-card">
<h3>Datos de la Vacante</h3>
<div class="empleado-grid">
<div><b>Tipo de vacante</b><input value="${c.tipo_candidatura}" readonly></div>
<div><b>Puesto deseado</b><input value="${c.puesto_deseado}" onchange="candSel.puesto_deseado=this.value"></div>
<div><b>Expectativa Salarial / Beca</b><input value="${c.expectativa_salarial}" onchange="candSel.expectativa_salarial=this.value"></div>
<div><b>Fecha Primera Postulación</b><input type="date" value="${c.fecha_postulacion || ''}" readonly></div>
<div><b>Fecha Agendado (Contacto)</b><input type="date" value="${c.fecha_agendado || ''}" onchange="candSel.fecha_agendado=this.value"></div>
<div><b>Fecha de Cita Próxima</b><input type="date" value="${c.fecha_entrevista || ''}" onchange="candSel.fecha_entrevista=this.value"></div>
</div>
<div style="margin-top:10px;">
<b>Horarios Posibles (Disponibilidad)</b>
<textarea style="width:100%;height:60px;padding:8px;border:1px solid #d1d5db;border-radius:6px;margin-top:5px;" placeholder="Ej. Lunes a Viernes por las tardes" onchange="candSel.horarios_disponibles=this.value">${c.horarios_disponibles||''}</textarea>
</div>
</div>

<div class="rh-card">
<h3>Acuse Documental (CV, Portafolio)</h3>
<button class="btn-ver" onclick="escanear()" style="margin-bottom:10px;">Escanear Físico</button>
<button class="btn-ver" onclick="document.getElementById('fileUpload').click()" style="background:#3b82f6;">Subir Archivo (PDF/IMG)</button>
<input type="file" id="fileUpload" style="display:none" onchange="subirArchivoCandidato(this)">
<div id="dwtcontrolContainer"></div>
<hr>
${(c.documentos || []).length===0 ? "Sin documentos" : (c.documentos || []).map(d=>`
<div style="display:inline-block;margin:5px;text-align:center;">
${d.tipo==='imagen' ? `<img src="${d.url}" onclick="ver('${d.url}')" style="width:120px;cursor:pointer;"><br>` : `<div style="padding:20px;background:#e5e7eb;font-weight:bold;">PDF/DOC</div><br>`}
<small>${d.nombre||''}</small><br>
<button class="btn-ver" onclick="descargarURL('${d.url}')">Descargar</button>
</div>`).join('')}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<h3>Acciones de Ficha</h3>
<button class="btn-ver" onclick="convertirCandidato()" style="background:#22c55e; width:100%; margin-bottom:10px; font-size:15px;">✅ Aprobar y Convertir a ${c.tipo_candidatura}</button>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#3b82f6; margin:0; flex:1;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#eab308; margin:0; flex:1;" onclick="exportarFichaPDF()">Exportar PDF</button>
<button class="btn-ver" style="background:#ef4444; margin:0; flex:1;" onclick="eliminarRegistro('candidato')">Eliminar Registro</button>
</div>
</div>`;
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
    document.getElementById('modalVacaciones').style.display='flex';
}

function guardarNuevaVacacion(){
    let empId = document.getElementById('v_emp_id').value;
    if(!empId){ alert('ID requerido'); return; }
    let emp = empleados.find(e=>e.id==empId);
    if(!emp){ alert('No existe ese ID de empleado. Si es practicante, las vacaciones no aplican directamente.'); return; }
    vacaciones.unshift({
        empleado_id: parseInt(empId),
        inicio: document.getElementById('v_inicio').value,
        fin: document.getElementById('v_fin').value,
        dias: parseInt(document.getElementById('v_dias').value) || 0,
        tipo: document.getElementById('v_tipo').value,
        estado: 'Pendiente',
        cobertura: document.getElementById('v_cobertura').value
    });
    document.getElementById('modalVacaciones').style.display='none';
    mostrar('vacaciones');
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
    mostrar("ficha_cita");
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



// Inicialización por defecto
mostrar("citas");

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
