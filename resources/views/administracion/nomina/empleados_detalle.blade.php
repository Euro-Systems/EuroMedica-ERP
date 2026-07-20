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

</div>
