<div class="nomina-card">
    <h2>Historial de Pagos Registrados</h2>
    <table class="nomina-table">
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Sueldo Base</th>
                <th>Deducciones</th>
                <th>Neto Pagado</th>
            </tr>
        </thead>
        <tbody>
            ${pagosHistorial.length === 0 ? `
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 20px;">No hay registros de pago en el historial.</td>
                </tr>
            ` : pagosHistorial.map(h => {
                let emp = empleados.find(e => e.id === h.empleadoId);
                return `
                    <tr>
                        <td class="nombre">${emp ? emp.nombre : 'N/A'}</td>
                        <td>${h.fecha}</td>
                        <td>$${h.sueldo.toLocaleString()}</td>
                        <td>$${h.deducciones.toLocaleString()}</td>
                        <td style="font-weight: 600; color: #16a34a;">$${h.neto.toLocaleString()}</td>
                    </tr>
                `;
            }).join('')}
        </tbody>
    </table>
</div>
