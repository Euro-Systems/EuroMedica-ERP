<div class="nomina-card">
    <h2>Reportes de Pagos</h2>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 15px; margin-bottom: 20px;">
        <div class="box">
            <span>Empleado</span>
            <select id="reporteEmpleado">
                <option value="">Seleccione un empleado...</option>
                ${empleados.map(e => `<option value="${e.id}">${e.nombre}</option>`).join('')}
            </select>
        </div>
        <div class="box">
            <span>Mes</span>
            <input type="month" id="mesReporte">
        </div>
        <div style="display: flex; align-items: flex-end;">
            <button class="btn" style="width: 100%; height: 42px;" onclick="generarReporte()">Generar Reporte</button>
        </div>
    </div>
    <div id="resultadoReporte" style="margin-top: 20px;"></div>
</div>
