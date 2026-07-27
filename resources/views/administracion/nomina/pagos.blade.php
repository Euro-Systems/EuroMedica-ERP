<div class="nomina-card">
    <h2>Registrar Pago de Nómina</h2>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 15px;">
        <div class="box">
            <span>Empleado</span>
            <select id="empleadoPago" onchange="calcularPago()">
                <option value="">Seleccione un empleado...</option>
                ${empleados.map(e => `<option value="${e.id}">${e.nombre} - ${e.puesto}</option>`).join('')}
            </select>
        </div>
        <div class="box">
            <span>Sueldo Mensual Base</span>
            <input type="number" id="sueldoBase" readonly>
        </div>
        <div class="box">
            <span>Mes de Pago</span>
            <input type="month" id="mesPago">
        </div>
        <div class="box">
            <span>Días Trabajados (Quincena/Mes)</span>
            <input type="number" id="diasPago" value="15" oninput="calcularPago()">
        </div>
        <div class="box">
            <span>Horas Extras ($)</span>
            <input type="number" id="horasExtras" value="0" oninput="calcularPago()">
        </div>
        <div class="box">
            <span>Guardias ($)</span>
            <input type="number" id="guardias" value="0" oninput="calcularPago()">
        </div>
        <div class="box">
            <span>Días Festivos ($)</span>
            <input type="number" id="diasFestivo" value="0" oninput="calcularPago()">
        </div>
        <div class="box">
            <span>Prima Vacacional ($)</span>
            <input type="number" id="primaVacacional" value="0" oninput="calcularPago()">
        </div>
        <div class="box">
            <span>Deducciones ($)</span>
            <input type="number" id="deducciones" value="0" oninput="calcularPago()">
        </div>
        <div class="box" style="background: #e2e8f0;">
            <span>Total Neto a Pagar</span>
            <input type="text" id="totalPago" readonly style="font-size: 16px; color: #1e3a8a;">
        </div>
    </div>
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button class="btn" style="background: #22c55e;" onclick="registrarPago()">Registrar Pago</button>
    </div>
    <div id="resultadoPago" style="margin-top: 15px; font-weight: 600;"></div>
</div>
