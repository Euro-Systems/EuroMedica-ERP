@extends('layouts.app')

{{-- =========================================================
    TÍTULO DE LA PÁGINA
========================================================= --}}
@section('title','Nómina')

@section('content')

<style>
/* =========================================================
   RESET GENERAL
   - Elimina márgenes y padding por defecto
   - Define altura completa
   - Configura tipografía principal
   - Oculta scroll general del body
========================================================= */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Roboto, Arial;
    overflow: hidden;
}

/* =========================================================
   CONTENEDORES GENERALES DE BOOTSTRAP
   - Se fuerza ancho completo
   - Se eliminan márgenes/paddings
========================================================= */
.container, .container-fluid {
    max-width: 100%!important;
    padding: 0!important;
    margin: 0!important;
}

/* =========================================================
   CONTENEDOR PRINCIPAL DEL SISTEMA DE NÓMINA
========================================================= */
.nomina-container{
    display: flex;
    width: 100vw;
    height: 100vh;
    background: #f4f6f9;
}

/* =========================================================
   SIDEBAR / MENÚ LATERAL
========================================================= */
.nomina-menu{
    width: 220px;
    background: linear-gradient(180deg,#1e3a8a,#3b82f6);
    padding: 25px;
    color: #fff;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}

/* Título del menú lateral */
.nomina-menu h2{
    margin-bottom: 20px;
    font-size: 20px;
}

/* Opciones del menú lateral */
.nomina-nav{
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

/* Estado activo del menú */
.nomina-nav.active{
    background: #fff;
    color: #1e3a8a;
    font-weight: bold;
}

/* =========================================================
   CONTENIDO PRINCIPAL
========================================================= */
.nomina-content{
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
    overflow-y: auto;
}

/* =========================================================
   TABS SUPERIORES
========================================================= */
.tabs{
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

/* Tab individual */
.tab{
    padding: 10px 18px;
    border-radius: 10px 10px 0 0;
    background: #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
}

/* Tab activo */
.tab.active{
    background: #fff;
    color: #1e3a8a;
    border-bottom: 3px solid #3b82f6;
}

/* =========================================================
   TARJETAS / CARDS
========================================================= */
.nomina-card{
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* =========================================================
   GRID RESPONSIVE
========================================================= */
.grid{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
}

/* Caja individual */
.box{
    background: #f8fafc;
    padding: 10px;
    border-radius: 8px;
}

/* Texto secundario dentro de cajas */
.box span{
    font-size: 11px;
    color: #6b7280;
}

/* Inputs y selects */
.box input, .box select{
    width: 100%;
    border: none;
    background: transparent;
    font-weight: 600;
    font-size: 13px;
    outline: none;
}

/* =========================================================
   TABLA MODERNA
========================================================= */
.nomina-table{
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

/* Encabezado de tabla */
.nomina-table thead tr{
    background: #1e3a8a;
    color: #fff;
    text-align: left;
    border-radius: 12px;
}

/* Celdas */
.nomina-table th, .nomina-table td{
    padding: 12px 15px;
}

/* Filas del body */
.nomina-table tbody tr{
    background: #fff;
    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    border-radius: 10px;
    transition: transform 0.2s, box-shadow 0.2s;
}

/* Hover de filas */
.nomina-table tbody tr:hover{
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Nombre destacado */
.nombre{
    font-weight: 600;
    color: #111827;
}

/* Badge de puesto */
.badge{
    background: #dbebff;
    color: #1e3a8a;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-block;
}

/* =========================================================
   CAJA TOTAL A PAGAR
========================================================= */
.total-box{
    background: #111827;
    color: #fff;
    padding: 18px;
    border-radius: 10px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
}

/* =========================================================
   BOTONES
========================================================= */
.btn{
    padding: 6px 14px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    background: #1e3a8a;
    color: white;
    font-size: 13px;
    transition: all 0.2s;
}

/* Hover del botón */
.btn:hover{
    background: #1e40af;
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

<div class="nomina-container">

    <!-- =====================================================
         SIDEBAR / MENÚ PRINCIPAL
    ====================================================== -->
    <aside class="nomina-menu">

        <!-- Título -->
        <h2>Nómina</h2>

        <!-- Opciones principales -->
        <div id="btnEmpSide" class="nomina-nav active" onclick="mostrar('empleados')">
            Empleados
        </div>

        <div id="btnPagoSide" class="nomina-nav" onclick="mostrar('pagos')">
            Pagos
        </div>

        <div id="btnReporteSide" class="nomina-nav" onclick="mostrar('reportes')">
            Reportes
        </div>

        <div id="btnConfigSide" class="nomina-nav" onclick="mostrar('config')">
            Configuración
        </div>

        <a href="{{ route('administracion.index') }}" class="btn-regresar">
        ⬅ Volver al Menú Principal
    </a>

    </aside>

    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->
    <main class="nomina-content">

        <!-- Tabs superiores -->
        <div class="tabs">

            <div id="btnEmp" class="tab active" onclick="mostrar('empleados')">
                Empleados
            </div>

            <div id="btnFicha" class="tab" onclick="mostrar('detalle')">
                Ficha
            </div>

            <div id="btnHistorial" class="tab" onclick="mostrar('historial')">
                Historial de pago
            </div>

            <div id="btnPago" class="tab" onclick="mostrar('pagos')">
                Pagos
            </div>

        </div>

        <!-- Aquí se renderiza dinámicamente el contenido -->
        <div id="contenido"></div>

    </main>
</div>

<script>

/* =========================================================
   LISTA PRINCIPAL DE EMPLEADOS
   - Simula base de datos temporal
========================================================= */
let empleados = [];

/* =========================================================
   HISTORIAL DE PAGOS
   - Aquí se guardan los pagos registrados
========================================================= */
let pagosHistorial = [];

/* =========================================================
   EMPLEADO ACTUALMENTE SELECCIONADO
========================================================= */
let empSel = null;

/* =========================================================
   FUNCIÓN: activar(v)
   - Activa visualmente tabs y menú lateral
========================================================= */
function activar(v){

    // Quitar clase active a tabs superiores
    ["btnEmp","btnFicha","btnPago","btnHistorial"]
    .forEach(i=>document.getElementById(i).classList.remove("active"));

    // Quitar clase active al menú lateral
    ["btnEmpSide","btnPagoSide","btnReporteSide","btnConfigSide"]
    .forEach(i=>document.getElementById(i).classList.remove("active"));

    // Activar sección correspondiente
    if(v==="empleados"){
        btnEmp.classList.add("active");
        btnEmpSide.classList.add("active");
    }

    if(v==="detalle"){
        btnFicha.classList.add("active");
    }

    if(v==="historial"){
        btnHistorial.classList.add("active");
    }

    if(v==="pagos"){
        btnPago.classList.add("active");
        btnPagoSide.classList.add("active");
    }

    if(v==="reportes"){
        btnReporteSide.classList.add("active");
    }

    if(v==="config"){
        btnConfigSide.classList.add("active");
    }
}

/* =========================================================
   FUNCIÓN PRINCIPAL DE RENDERIZADO
   - Cambia la vista según la sección seleccionada
========================================================= */
function mostrar(v){

    // Activar visualmente la sección
    activar(v);

    // Variable HTML dinámica
    let html="";

    /* =====================================================
       VISTA: EMPLEADOS
    ====================================================== */
    if(v==="empleados"){

        html=`<div class="nomina-card">

            <h2>Lista de empleados</h2>

            <table class="nomina-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Salario</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    ${empleados.map(e=>`
                    <tr>

                        <td>${e.id}</td>

                        <td class="nombre">${e.nombre}</td>

                        <td>
                            <span class="badge">${e.puesto}</span>
                        </td>

                        <td>
                            $${e.salario.toLocaleString()}
                        </td>

                        <td>
                            <button class="btn" onclick="seleccionar(${e.id})">
                                Ver
                            </button>
                        </td>

                    </tr>`).join('')}

                </tbody>

            </table>

        </div>`;
    }

    /* =====================================================
       VISTA: DETALLE DEL EMPLEADO
    ====================================================== */
    if(v==="detalle"){

        // Si no hay empleado seleccionado
        if(!empSel){
            html=`<div class="nomina-card">
                Seleccione un empleado
            </div>`;
        }

        else {

            // Referencia rápida al empleado seleccionado
            let e = empSel;

            /* =============================================
               CÁLCULOS DE NÓMINA
            ============================================== */
            let quincenal = e.salario/2;
            let diario = e.salario/30;
            let dias = 15;
            let pagoDias = diario*dias;
            let extras = 500;
            let guardias = 300;
            let festivo = 200;
            let prima = 400;

            // Total de ingresos
            let totalIngreso =
                pagoDias +
                extras +
                guardias +
                festivo +
                prima;

            // Deducciones fijas
            let deducciones = 2000;

            // Neto final
            let neto = totalIngreso - deducciones;

            /* =============================================
               HTML DETALLE EMPLEADO
            ============================================== */
            html=`

            <!-- ENCABEZADO -->
            <div class="nomina-card"
                 style="display:flex; justify-content: space-between; align-items:center;">

                <h2>${e.nombre}</h2>

                <!-- Estatus -->
                <span style="
                    background: ${e.estatus==='Baja'?'#f87171':'#dbebff'};
                    color: ${e.estatus==='Baja'?'#991b1b':'#1e3a8a'};
                    font-weight:600;
                    padding:6px 12px;
                    border-radius:20px;
                    font-size:13px;">

                    ${e.estatus}

                </span>

            </div>

            <!-- DATOS GENERALES -->
            <div class="nomina-card">

                <h3>Datos generales y bancarios</h3>

                <div class="grid">

                    <div class="box">
                        <span>Puesto</span>
                        <input value="${e.puesto}">
                    </div>

                    <div class="box">
                        <span>Empresa</span>
                        <input value="${e.empresa}">
                    </div>

                    <div class="box">
                        <span>Nombre</span>
                        <input value="${e.nombre}">
                    </div>

                    <div class="box">
                        <span>Fecha ingreso</span>
                        <input value="${e.fecha}">
                    </div>

                    <div class="box">
                        <span>Baja / Estatus</span>
                        <input value="${e.estatus}">
                    </div>

                    <div class="box">
                        <span>Cuenta</span>
                        <input value="${e.cuenta}">
                    </div>

                    <div class="box">
                        <span>Banco</span>
                        <input value="${e.banco}">
                    </div>

                </div>

            </div>`;
        }
    }

    /* =====================================================
       AL FINAL INSERTA HTML EN EL CONTENEDOR
    ====================================================== */
    document.getElementById("contenido").innerHTML = html;
}

/* =========================================================
   FUNCIÓN: seleccionar(id)
   - Busca empleado por ID
   - Lo guarda como seleccionado
   - Abre la vista detalle
========================================================= */
function seleccionar(id){

    empSel = empleados.find(e=>e.id===id);

    mostrar("detalle");
}

/* =========================================================
   FUNCIÓN: calcularPago()
   - Calcula total del pago dinámicamente
========================================================= */
function calcularPago(){

    // Obtener empleado seleccionado
    let empId = document.getElementById("empleadoPago")?.value;

    // Validar selección
    if(!empId) return;

    // Buscar empleado
    let empleado = empleados.find(e => e.id == empId);

    if(!empleado) return;

    // Sueldo base
    let sueldoBase = empleado.salario;

    document.getElementById("sueldoBase").value = sueldoBase;

    // Obtener datos capturados
    let dias = parseFloat(document.getElementById("diasPago").value) || 0;

    let extras = parseFloat(document.getElementById("horasExtras").value) || 0;

    let guardias = parseFloat(document.getElementById("guardias").value) || 0;

    let festivo = parseFloat(document.getElementById("diasFestivo").value) || 0;

    let prima = parseFloat(document.getElementById("primaVacacional").value) || 0;

    let deducciones = parseFloat(document.getElementById("deducciones").value) || 0;

    // Cálculo sueldo diario
    let diario = sueldoBase / 30;

    // Pago por días trabajados
    let pagoDias = diario * dias;

    // Total final
    let total =
        pagoDias +
        extras +
        guardias +
        festivo +
        prima -
        deducciones;

    // Mostrar resultado
    document.getElementById("totalPago").value = total.toFixed(2);
}

/* =========================================================
   FUNCIÓN: registrarPago()
   - Guarda el pago en historial
========================================================= */
function registrarPago(){

    let empId = document.getElementById("empleadoPago").value;

    let mes = document.getElementById("mesPago").value;

    // Validar datos
    if(!empId || !mes){

        alert("Seleccione un empleado y mes");

        return;
    }

    // Obtener datos del formulario
    let total =
        parseFloat(document.getElementById("totalPago").value) || 0;

    let deducciones =
        parseFloat(document.getElementById("deducciones").value) || 0;

    let sueldoBase =
        parseFloat(document.getElementById("sueldoBase").value) || 0;

    // Guardar registro
    pagosHistorial.push({

        empleadoId: parseInt(empId),

        fecha: mes+"-15",

        sueldo: sueldoBase,

        deducciones: deducciones,

        neto: total
    });

    // Mensaje de éxito
    document.getElementById("resultadoPago").innerHTML =
    `<p style="color:green;">
        Pago registrado: $${total.toLocaleString()}
    </p>`;
}

/* =========================================================
   FUNCIÓN: generarReporte()
   - Genera tabla de pagos por empleado y mes
========================================================= */
function generarReporte(){

    let empId =
        document.getElementById("reporteEmpleado").value;

    let mes =
        document.getElementById("mesReporte").value;

    // Validar selección
    if(!empId || !mes){

        alert("Seleccione empleado y mes");

        return;
    }

    // Filtrar historial
    let historial = pagosHistorial.filter(
        p => p.empleadoId==empId && p.fecha.startsWith(mes)
    );

    // Si no hay registros
    if(historial.length===0){

        document.getElementById("resultadoReporte").innerHTML =
        "<p>No hay pagos registrados</p>";

        return;
    }

    // Crear tabla HTML
    let html =
    "<table class='nomina-table'>" +
    "<thead>" +
    "<tr>" +
    "<th>Fecha</th>" +
    "<th>Sueldo</th>" +
    "<th>Deducciones</th>" +
    "<th>Neto</th>" +
    "</tr>" +
    "</thead>" +
    "<tbody>";

    // Agregar filas
    historial.forEach(h=>{

        html+=`
        <tr>

            <td>${h.fecha}</td>

            <td>
                $${h.sueldo.toLocaleString()}
            </td>

            <td>
                $${h.deducciones.toLocaleString()}
            </td>

            <td>
                $${h.neto.toLocaleString()}
            </td>

        </tr>`;
    });

    html+="</tbody></table>";

    // Mostrar resultado
    document.getElementById("resultadoReporte").innerHTML = html;
}

/* =========================================================
   INICIALIZACIÓN
   - Al cargar la página abre empleados
========================================================= */
mostrar('empleados');

</script>

@endsection
