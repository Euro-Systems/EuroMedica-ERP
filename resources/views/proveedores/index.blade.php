@extends('layouts.app')

{{-- =========================
     TÍTULO DE LA PÁGINA
     ========================= --}}
@section('title','Proveedores')

{{-- =========================
     CONTENIDO PRINCIPAL
     ========================= --}}
@section('content')

<style>

/* =========================================================
   ESTILOS GENERALES BASE
   ========================================================= */

/* 
   Configuración global del documento:
   - elimina márgenes/padding
   - usa toda la pantalla
   - fuente principal
   - evita scroll general
*/
html,body{
    height:100%;
    margin:0;
    padding:0;
    font-family:'Segoe UI',sans-serif;
    overflow:hidden;
    background:#f1f5f9;
}

/*
   Ajuste de contenedores de Bootstrap
   para ocupar todo el ancho y alto
*/
.container,.container-fluid{
    max-width:100%!important;
    padding:0!important;
    margin:0!important;
    height:100%;
}

/* =========================================================
   LAYOUT PRINCIPAL
   ========================================================= */

/*
   Layout principal:
   sidebar + contenido
*/
.erp-layout{
    display:flex;
    width:100vw;
    height:100vh;
}

/*
   Sidebar lateral
*/
.erp-sidebar{
    width:250px;
    background:linear-gradient(180deg,#1e3a8a,#2563eb);
    color:white;
    padding:28px 22px;
    display:flex;
    flex-direction:column;
    flex-shrink:0;
}

/*
   Logo/Título del sidebar
*/
.erp-logo{
    font-size:24px;
    font-weight:700;
    margin-bottom:40px;
}

/*
   Item del menú lateral
*/
.erp-menu-item{
    padding:14px 16px;
    border-radius:14px;
    background:white;
    color:#2563eb;
    font-weight:700;
}

/*
   Área principal de contenido
*/
.erp-content{
    flex:1;
    padding:30px;
    display:flex;
    flex-direction:column;
    min-width:0;
    min-height:0;
}

/*
   Título de secciones
*/
.section-title{
    font-size:24px;
    font-weight:800;
    margin-bottom:20px;
}

/* =========================================================
   GRID DE MÓDULOS/CATEGORÍAS
   ========================================================= */

/*
   Grid responsive para tarjetas
*/
.modulos-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:25px;

    max-height: calc(100vh - 180px);
    overflow-y:auto;
    padding-right:6px;
}

/*
   Scroll personalizado
*/
.modulos-grid::-webkit-scrollbar{
    width:8px;
}

/*
   Color del scroll
*/
.modulos-grid::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:10px;
}

/* =========================================================
   TARJETAS DE CATEGORÍAS
   ========================================================= */

/*
   Tarjeta principal de categoría
*/
.modulo-card{
    background:linear-gradient(180deg,#ffffff,#f8fafc);
    border-radius:26px;
    padding:22px 22px;

    cursor:pointer;

    border:1px solid #e2e8f0;
    box-shadow:0 6px 14px rgba(0,0,0,0.05);

    display:flex;
    align-items:center;
    gap:16px;

    transition:all 0.25s ease;

    position:relative;
    min-height:120px;
}

/*
   Hover de tarjeta
*/
.modulo-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 40px rgba(30,58,138,0.15);
    border-color:#c7d2fe;
}

/*
   Efecto al hacer click
*/
.modulo-card:active{
    transform:translateY(-2px);
}

/*
   Línea decorativa lateral
*/
.modulo-card::before{
    content:'';
    position:absolute;
    left:10px;
    top:20%;
    width:4px;
    height:60%;
    border-radius:10px;
    background:#2563eb;
    opacity:0.6;
}

/* =========================================================
   ICONOS DE LAS TARJETAS
   ========================================================= */

/*
   Contenedor del icono
*/
.modulo-icon{
    width:72px;
    height:72px;
    border-radius:20px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:32px;
    color:white;

    flex-shrink:0;

    box-shadow:0 8px 18px rgba(0,0,0,0.10);
}

/*
   Colores por categoría
*/
.medico{background:linear-gradient(135deg,#2563eb,#1d4ed8);}
.radiologia{background:linear-gradient(135deg,#06b6d4,#0e7490);}
.laboratorio{background:linear-gradient(135deg,#8b5cf6,#6d28d9);}
.dentista{background:linear-gradient(135deg,#10b981,#047857);}
.admin{background:linear-gradient(135deg,#f59e0b,#d97706);}

/* =========================================================
   TEXTO DE TARJETAS
   ========================================================= */

/*
   Contenedor del texto
*/
.card-text{
    display:flex;
    flex-direction:column;
    flex:1;
}

/*
   Título de tarjeta
*/
.card-title{
    font-size:18px;
    font-weight:800;
    color:#0f172a;
}

/*
   Subtítulo/descripción
*/
.card-sub{
    font-size:13px;
    color:#64748b;
    margin-top:4px;
}

/*
   Badge de cantidad o estado
*/
.card-badge{
    margin-top:10px;
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    background:#eef2ff;
    color:#1e3a8a;
    width:max-content;
}

/*
   Flecha decorativa
*/
.card-arrow{
    margin-left:auto;
    font-size:18px;
    color:#94a3b8;
}

/* =========================================================
   TABLA DE PROVEEDORES
   ========================================================= */

/*
   Contenedor general de la tabla
*/
.tabla-container{
    display:none;
    flex:1;
    overflow:hidden;
}

/*
   Card blanca donde vive la tabla
*/
.tabla-card{
    background:white;
    padding:15px;
    border-radius:18px;
    overflow:auto;
    height:100%;
}

/*
   Tabla principal
*/
.rh-table{
    width:100%;
    border-collapse:collapse;
}

/*
   Celdas y encabezados
*/
.rh-table th,
.rh-table td{
    padding:12px;
    border-bottom:1px solid #e2e8f0;
    text-align:left;
    vertical-align:top;
}

/*
   Encabezados de tabla
*/
.rh-table th{
    background:#1e3a8a;
    color:white;
}

/*
   Botón volver
*/
.btn-volver{
    background:#1e3a8a;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:8px;
    cursor:pointer;
    font-weight:700;
}

/* =========================================================
   PRODUCTOS
   ========================================================= */

/*
   Card individual de producto
*/
.producto-item{
    background:#f8fafc;
    border:1px solid #2e8f0;
    border-radius:10px;
    padding:10px;
    margin-bottom:8px;
}

/*
   Nombre de producto
*/
.producto-nombre{
    font-weight:700;
}

/*
   Precio de producto
*/
.producto-precio{
    color:#2563eb;
    font-weight:800;
}

/*
   Select de productos
*/
.select-producto{
    width:100%;
    padding:8px;
    border-radius:8px;
    border:1px solid #cbd5e1;
    margin-top:8px;
}

/*
   Badge de recomendado
*/
.recomendado{
    background:#dcfce7;
    color:#166534;
    padding:4px 8px;
    border-radius:8px;
    font-size:11px;
    font-weight:700;
}

/*
   Input filtro de tabla
*/
.filtro-tabla{
    width:100%;
    margin-top:8px;
    padding:8px;
    border:1px solid #cbd5e1;
    border-radius:8px;
}

</style>

{{-- =========================================================
     ESTRUCTURA PRINCIPAL
     ========================================================= --}}
<div class="erp-layout">

{{-- =========================
     SIDEBAR
     ========================= --}}
<aside class="erp-sidebar">

    {{-- Logo/Título --}}
    <div class="erp-logo">Proveedores</div>

    {{-- Menú lateral --}}
    <div class="erp-menu-item">Panel general</div>

</aside>

{{-- =========================
     CONTENIDO
     ========================= --}}
<main class="erp-content">

{{-- =========================================================
     PANEL PRINCIPAL DE CATEGORÍAS
     ========================================================= --}}
<div id="panelPrincipal">

    {{-- Título --}}
    <div class="section-title">Categorías de proveedores</div>

    {{-- Grid de categorías --}}
    <div class="modulos-grid">

        {{-- =========================
             SERVICIO MÉDICO
             ========================= --}}
        <div class="modulo-card" onclick="abrirTabla('Servicio medico')">

            <div class="modulo-icon medico">🏥</div>

            <div class="card-text">
                <div class="card-title">Servicio Médico</div>
                <div class="card-sub">Clínicas y hospitales</div>
                <div class="card-badge">3 proveedores</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

        {{-- =========================
             RADIOLOGÍA
             ========================= --}}
        <div class="modulo-card" onclick="abrirTabla('Radiologia')">

            <div class="modulo-icon radiologia">🩻</div>

            <div class="card-text">
                <div class="card-title">Radiología</div>
                <div class="card-sub">Estudios de imagen</div>
                <div class="card-badge">1 proveedor</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

        {{-- =========================
             LABORATORIO
             ========================= --}}
        <div class="modulo-card" onclick="abrirTabla('Laboratorio')">

            <div class="modulo-icon laboratorio">🧪</div>

            <div class="card-text">
                <div class="card-title">Laboratorio</div>
                <div class="card-sub">Análisis clínicos</div>
                <div class="card-badge">1 proveedor</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

        {{-- =========================
             DENTISTA
             ========================= --}}
        <div class="modulo-card" onclick="abrirTabla('Dentista')">

            <div class="modulo-icon dentista">🦷</div>

            <div class="card-text">
                <div class="card-title">Dentista</div>
                <div class="card-sub">Servicios odontológicos</div>
                <div class="card-badge">1 proveedor</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

        {{-- =========================
             ADMINISTRACIÓN
             ========================= --}}
        <div class="modulo-card" onclick="abrirTabla('Administracion')">

            <div class="modulo-icon admin">📋</div>

            <div class="card-text">
                <div class="card-title">Administración</div>
                <div class="card-sub">Gestión y soporte</div>
                <div class="card-badge">1 proveedor</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

        {{-- =========================
             AGREGAR CATEGORÍA
             ========================= --}}
        <div class="modulo-card" onclick="alert('Agregar nueva categoría')">

            <div class="modulo-icon admin">➕</div>

            <div class="card-text">
                <div class="card-title">Agregar categoría</div>
                <div class="card-sub">Crear nuevo tipo de proveedor</div>
                <div class="card-badge">Nuevo</div>
            </div>

            <div class="card-arrow">→</div>

        </div>

    </div>

</div>

{{-- =========================================================
     TABLA DE PROVEEDORES
     ========================================================= --}}
<div id="tablaProveedores" class="tabla-container">

{{-- Encabezado de tabla --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">

    {{-- Título dinámico --}}
    <div id="tituloCategoria" class="section-title"></div>

    {{-- Botón volver --}}
    <button class="btn-volver" onclick="volverPanel()">← Volver</button>

</div>

{{-- Card contenedor --}}
<div class="tabla-card">

<table class="rh-table">

<thead>

<tr>

    {{-- Columna proveedor con filtro --}}
    <th>
        Proveedor

        <input 
            id="filtroProveedor"
            class="filtro-tabla"
            placeholder="Buscar proveedor..."
            oninput="filtrarTabla()"
        >

    </th>

    <th>Estado</th>
    <th>Teléfono</th>
    <th>Productos</th>
    <th>Mejor opción</th>

</tr>

</thead>

{{-- Body dinámico --}}
<tbody id="bodyTabla"></tbody>

</table>

</div>

</div>

</main>

</div>

<script>

/* =========================================================
   VARIABLES GLOBALES
   ========================================================= */

/*
   Guarda la categoría seleccionada actualmente
*/
let categoriaActual = "";

/*
   Base de datos temporal de proveedores
   organizada por categoría
*/
const proveedores = {

'Servicio medico': [

{
nombre:'Clínica San José',
estado:'Activo',
telefono:'844-123-4567',

productos:[

    {nombre:'Consulta general',precio:650},
    {nombre:'Consulta especialista',precio:1200},
    {nombre:'Medicamento antibiótico',precio:380}
]
},

{
nombre:'Hospital Central',
estado:'Pendiente',
telefono:'844-888-2222',

productos:[
    {nombre:'Consulta general',precio:720},
    {nombre:'Cirugía menor',precio:5500},
    {nombre:'Medicamento antibiótico',precio:410}
]
}

],

'Radiologia': [

{
nombre:'Rayos X del Norte',
estado:'Activo',
telefono:'844-555-1111',

productos:[
    {nombre:'Rayos X',precio:850},
    {nombre:'Tomografía',precio:3200},
    {nombre:'Ultrasonido',precio:1400}
]
}

],

'Laboratorio': [

{
nombre:'Lab Express',
estado:'Activo',
telefono:'844-777-9999',

productos:[
    {nombre:'Biometría hemática',precio:250},
    {nombre:'Química sanguría',precio:550},
    {nombre:'Prueba COVID',precio:300}
]
}

],

'Dentista': [

{
nombre:'Dental Care',
estado:'Activo',
telefono:'844-444-5555',

productos:[
    {nombre:'Limpieza dental',precio:600},
    {nombre:'Extracción',precio:1200},
    {nombre:'Ortodoncia',precio:18000}
]
}

],

'Administracion': [

{
nombre:'Gestión Médica',
estado:'Pendiente',
telefono:'844-999-1111',

productos:[
    {nombre:'Papelería',precio:1200},
    {nombre:'Sistema administrativo',precio:8500},
    {nombre:'Facturación',precio:3200}
]
}

]

};

/* =========================================================
   ABRIR TABLA
   ========================================================= */

/*
   Muestra la tabla de una categoría
   y oculta el panel principal
*/
function abrirTabla(categoria){

categoriaActual = categoria;

/*
   Oculta panel principal
*/
document.getElementById('panelPrincipal').style.display='none';

/*
   Muestra tabla
*/
document.getElementById('tablaProveedores').style.display='block';

/*
   Cambia título dinámicamente
*/
document.getElementById('tituloCategoria').innerText =
'Proveedores - ' + categoria;

/*
   Renderiza la tabla
*/
renderTabla(categoria);

}

/* =========================================================
   RENDER TABLA
   ========================================================= */

/*
   Genera dinámicamente la tabla
   según la categoría seleccionada
*/
function renderTabla(categoria){

let body = document.getElementById('bodyTabla');

/*
   Limpia contenido previo
*/
body.innerHTML='';

/*
   Obtiene texto del filtro
*/
let filtro = document.getElementById('filtroProveedor')
?.value
?.toLowerCase() || "";

/*
   Filtra proveedores y recorre resultados
*/
proveedores[categoria]

.filter(p => p.nombre.toLowerCase().includes(filtro))

.forEach((p,i)=>{

/*
   Busca el producto más barato
*/
let masBarato = p.productos.reduce((a,b)=>
a.precio < b.precio ? a : b
);

/*
   Genera HTML de productos
*/
let productosHTML = p.productos.map(x=>`
<div class="producto-item">

<div class="producto-nombre">${x.nombre}</div>

<div class="producto-precio">
$${x.precio.toLocaleString()}
</div>

</div>
`).join('');

/*
   Inserta fila en la tabla
*/
body.innerHTML += `
<tr>

<td><b>${p.nombre}</b></td>

<td>${p.estado}</td>

<td>${p.telefono}</td>

<td>

${productosHTML}

<select class="select-producto"
onchange="cambiarOpcion(this, ${i}, '${categoria}')">

<option value="">Elegir mejor opción</option>

${p.productos.map((x,idx)=>`
<option value="${idx}">
${x.nombre} - $${x.precio.toLocaleString()}
</option>
`).join('')}

</select>

</td>

<td>

<div class="recomendado">Mejor precio</div>

<div style="margin-top:6px;font-weight:700;">
${masBarato.nombre}
</div>

<div style="color:#2563eb;font-size:18px;font-weight:800;">
$${masBarato.precio.toLocaleString()}
</div>

</td>

</tr>
`;

});

}

/* =========================================================
   FILTRAR TABLA
   ========================================================= */

/*
   Ejecuta render nuevamente
   para aplicar filtro de búsqueda
*/
function filtrarTabla(){

if(categoriaActual){

renderTabla(categoriaActual);

}

}

/* =========================================================
   CAMBIAR OPCIÓN
   ========================================================= */

/*
   Cambia la recomendación
   según el producto seleccionado
*/
function cambiarOpcion(select,i,categoria){

let idx = select.value;

/*
   Si no selecciona nada, salir
*/
if(idx === '') return;

/*
   Obtiene producto seleccionado
*/
let producto = proveedores[categoria][i].productos[idx];

/*
   Busca la última celda de la fila
*/
let celda = select.closest('tr').querySelector('td:last-child');

/*
   Actualiza contenido
*/
celda.innerHTML = `
<div class="recomendado">Opción elegida</div>

<div style="margin-top:6px;font-weight:700;">
${producto.nombre}
</div>

<div style="color:#2563eb;font-size:18px;font-weight:800;">
$${producto.precio.toLocaleString()}
</div>
`;

}

/* =========================================================
   VOLVER AL PANEL PRINCIPAL
   ========================================================= */

/*
   Oculta tabla y muestra categorías
*/
function volverPanel(){

document.getElementById('panelPrincipal').style.display='block';

document.getElementById('tablaProveedores').style.display='none';

}

</script>

@endsection