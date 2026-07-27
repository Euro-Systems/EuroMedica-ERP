@extends('layouts.app')

@section('title', 'Compras')

@section('content')

<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Roboto, Arial;
    overflow: hidden;
}

.container, .container-fluid {
    max-width: 100%!important;
    padding: 0!important;
    margin: 0!important;
}

.compras-container {
    display: flex;
    width: 100vw;
    height: 100vh;
    background: #f4f6f9;
}

.compras-menu {
    width: 230px;
    background: linear-gradient(180deg, #1e3a8a, #4f46e5);
    padding: 25px 20px;
    color: #fff;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 15px rgba(0,0,0,0.05);
}

.compras-menu h2 {
    margin-bottom: 25px;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.compras-nav {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.compras-nav:hover {
    background: rgba(255, 255, 255, 0.1);
}

.compras-nav.active {
    background: #fff;
    color: #1e3a8a;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.compras-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 24px;
    overflow-y: auto;
    background: #f8fafc;
}

.compras-card {
    background: #fff;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.compras-card h3 {
    margin-top: 0;
    font-size: 18px;
    color: #1e293b;
    margin-bottom: 20px;
}

.btn-compras {
    background: #4f46e5;
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-compras:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

.compras-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}

.compras-table th {
    background: #f1f5f9;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    padding: 12px 16px;
    border: none;
}

.compras-table td {
    background: #fff;
    padding: 16px;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 14px;
}

.compras-table tr td:first-child {
    border-left: 1px solid #f1f5f9;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.compras-table tr td:last-child {
    border-right: 1px solid #f1f5f9;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

.badge-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-pendiente { background: #fef3c7; color: #d97706; }
.badge-aprobado { background: #dbeafe; color: #2563eb; }
.badge-comprado { background: #dcfce7; color: #16a34a; }

.grid-req {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.grid-req input, .grid-req select, .grid-req textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.grid-req input:focus, .grid-req select:focus, .grid-req textarea:focus {
    border-color: #4f46e5;
}

.progress-bar-container {
    background: #e2e8f0;
    border-radius: 8px;
    height: 12px;
    width: 100%;
    overflow: hidden;
    margin-top: 6px;
}

.progress-bar-fill {
    background: #4f46e5;
    height: 100%;
    border-radius: 8px;
}
</style>

<div class="compras-container">
    <!-- Sidebar / Apartados a la izquierda -->
    <aside class="compras-menu">
        <h2>🛍️ Compras</h2>
        <div id="btnReqSide" class="compras-nav active" onclick="mostrarSeccion('requerimientos')">
            📋 Requerimientos
        </div>
        <div id="btnOrdSide" class="compras-nav" onclick="mostrarSeccion('ordenes')">
            💳 Órdenes de Compra
        </div>
        <div id="btnPresSide" class="compras-nav" onclick="mostrarSeccion('presupuestos')">
            📊 Presupuestos
        </div>
        <div id="btnHistSide" class="compras-nav" onclick="mostrarSeccion('historial')">
            ⏱️ Historial
        </div>
        <a href="{{ route('administracion.index') }}" class="compras-nav" style="margin-top: auto; background: rgba(0,0,0,0.15); color: #fff; text-decoration: none;">
            ← Volver a Admin
        </a>
    </aside>

    <!-- Contenido Principal -->
    <main class="compras-content">
        <div id="compras-contenido"></div>
    </main>
</div>

<script>
// Base de datos de simulación
let requerimientos = [
    { id: 101, item: "Jeringas Desechables 5ml", cantidad: 500, departamento: "Enfermería", estado: "pendiente" },
    { id: 102, item: "Termómetros Infrarrojos", cantidad: 25, departamento: "Pediatría", estado: "aprobado" },
    { id: 103, item: "Gel Antibacterial 1L", cantidad: 100, departamento: "Recursos Humanos", estado: "comprado" }
];

let ordenesCompra = [
    { id: 201, proveedor: "Medilab S.A.", total: 12500, fecha: "2026-06-15", estado: "Completada" },
    { id: 202, proveedor: "Equipos Médicos del Norte", total: 45000, fecha: "2026-06-18", estado: "En Proceso" }
];

let presupuestos = [
    { departamento: "Sistemas", asignado: 120000, gastado: 85000 },
    { departamento: "Recursos Humanos", asignado: 50000, gastado: 12000 },
    { departamento: "Compras", asignado: 35000, gastado: 15000 }
];

function activarNav(seccion) {
    const ids = ['btnReqSide', 'btnOrdSide', 'btnPresSide', 'btnHistSide'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('active');
    });
    
    if (seccion === 'requerimientos') document.getElementById('btnReqSide')?.classList.add('active');
    if (seccion === 'ordenes') document.getElementById('btnOrdSide')?.classList.add('active');
    if (seccion === 'presupuestos') document.getElementById('btnPresSide')?.classList.add('active');
    if (seccion === 'historial') document.getElementById('btnHistSide')?.classList.add('active');
}

function mostrarSeccion(seccion) {
    activarNav(seccion);
    let html = "";

    if (seccion === 'requerimientos') {
        html = `@include('administracion.compras.requerimientos')`;
    }
    else if (seccion === 'ordenes') {
        html = `@include('administracion.compras.ordenes')`;
    }
    else if (seccion === 'presupuestos') {
        html = `@include('administracion.compras.presupuestos')`;
    }
    else if (seccion === 'historial') {
        html = `@include('administracion.compras.historial')`;
    }

    document.getElementById('compras-contenido').innerHTML = html;
}

function abrirCrearRequerimiento() {
    document.getElementById('form-nuevo-req').style.display = 'block';
}

function cancelarReq() {
    document.getElementById('form-nuevo-req').style.display = 'none';
}

function guardarRequerimiento() {
    const item = document.getElementById('req_item').value;
    const cantidad = parseInt(document.getElementById('req_cant').value);
    const dep = document.getElementById('req_dep').value;
    
    if (!item || isNaN(cantidad)) {
        alert("Por favor complete todos los campos requeridos.");
        return;
    }

    requerimientos.push({
        id: requerimientos.length + 101,
        item: item,
        cantidad: cantidad,
        departamento: dep,
        estado: 'pendiente'
    });

    mostrarSeccion('requerimientos');
}

function aprobarReq(id) {
    const req = requerimientos.find(r => r.id === id);
    if (req) {
        req.estado = 'aprobado';
        mostrarSeccion('requerimientos');
    }
}

// Inicialización
mostrarSeccion('requerimientos');
</script>

@endsection
