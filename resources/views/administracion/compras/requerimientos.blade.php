<div class="compras-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3>📋 Requerimientos Internos</h3>
        <button class="btn-compras" onclick="abrirCrearRequerimiento()">＋ Nuevo Requerimiento</button>
    </div>
    
    <div id="form-nuevo-req" style="display:none; margin-bottom:25px; padding:20px; background:#f8fafc; border-radius:12px; border:1px solid #cbd5e1;">
        <h4>Nuevo Requerimiento</h4>
        <div class="grid-req">
            <div>
                <label>Artículo / Insumo</label>
                <input type="text" id="req_item" placeholder="Ej: Cubrebocas tricapa">
            </div>
            <div>
                <label>Cantidad</label>
                <input type="number" id="req_cant" placeholder="Ej: 100">
            </div>
            <div>
                <label>Departamento Solicitante</label>
                <select id="req_dep">
                    <option>Recursos Humanos</option>
                    <option>Sistemas</option>
                    <option>Operaciones</option>
                </select>
            </div>
        </div>
        <div style="text-align:right;">
            <button class="btn-compras" style="background:#64748b; margin-right:8px;" onclick="cancelarReq()">Cancelar</button>
            <button class="btn-compras" onclick="guardarRequerimiento()">Guardar</button>
        </div>
    </div>

    <table class="compras-table">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Artículo / Insumo</th>
                <th>Cantidad</th>
                <th>Solicitante</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            ${requerimientos.map(r => `
                <tr>
                    <td>#REQ-${r.id}</td>
                    <td><strong>${r.item}</strong></td>
                    <td>${r.cantidad} uds</td>
                    <td>${r.departamento}</td>
                    <td>
                        <span class="badge-status badge-${r.estado === 'pendiente' ? 'pendiente' : (r.estado === 'aprobado' ? 'aprobado' : 'comprado')}">${r.estado}</span>
                    </td>
                    <td>
                        ${r.estado === 'pendiente' ? `<button class="btn-compras" style="background:#10b981; padding:4px 8px; font-size:12px;" onclick="aprobarReq(${r.id})">Aprobar</button>` : '---'}
                    </td>
                </tr>
            `).join('')}
        </tbody>
    </table>
</div>
