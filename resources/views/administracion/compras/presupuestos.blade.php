<div class="compras-card">
    <h3>📊 Presupuesto por Departamento</h3>
    <div style="display:grid; grid-template-columns: 1fr; gap:20px;">
        ${presupuestos.map(p => {
            const porc = Math.min((p.gastado / p.asignado) * 100, 100);
            return `
                <div style="background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="display:flex; justify-content:space-between; font-weight:600; margin-bottom:8px;">
                        <span>${p.departamento}</span>
                        <span>$${p.gastado.toLocaleString()} / $${p.asignado.toLocaleString()} MXN (${porc.toFixed(1)}%)</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: ${porc}%; background: ${porc > 85 ? '#ef4444' : '#4f46e5'};"></div>
                    </div>
                </div>
            `;
        }).join('')}
    </div>
</div>
