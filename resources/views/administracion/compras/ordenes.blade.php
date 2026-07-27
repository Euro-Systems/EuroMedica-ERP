<div class="compras-card">
    <h3>💳 Órdenes de Compra</h3>
    <table class="compras-table">
        <thead>
            <tr>
                <th>ID Orden</th>
                <th>Proveedor</th>
                <th>Monto Total</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            ${ordenesCompra.map(o => `
                <tr>
                    <td>#ORD-${o.id}</td>
                    <td><strong>${o.proveedor}</strong></td>
                    <td>$${o.total.toLocaleString()} MXN</td>
                    <td>${o.fecha}</td>
                    <td>
                        <span class="badge-status badge-${o.estado === 'Completada' ? 'comprado' : 'aprobado'}">${o.estado}</span>
                    </td>
                </tr>
            `).join('')}
        </tbody>
    </table>
</div>
