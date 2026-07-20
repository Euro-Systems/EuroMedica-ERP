<div class="tabs">
<div class="tab ${tipoCitaFiltro==='Agendadas'?'active':''}" onclick="tipoCitaFiltro='Agendadas'; mostrar('citas')">Citas Agendadas</div>
<div class="tab ${tipoCitaFiltro==='Realizadas'?'active':''}" onclick="tipoCitaFiltro='Realizadas'; mostrar('citas')">Citas Realizadas</div>
</div>

<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    ${tipoCitaFiltro}
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevaCita()">+ Nueva Cita</button>
</h2>

<table class="rh-table">
<thead>
<tr>
<th>Nombre</th>
<th>Puesto</th>
<th>Tipo</th>
<th>Fecha Cita</th>
<th>Hora</th>
<th>Entrevistador RH</th>
<th>Jefe Depto.</th>
<th>Estado</th>
</tr>
</thead>
<tbody>
${filtradas.map((ci,idx)=>`
<tr style="cursor:pointer;" onclick="seleccionarCita(${ci.id})">
<td>${ci.nombre}</td>
<td>${ci.puesto}</td>
<td>${ci.tipo}</td>
<td>${formatearFecha(ci.fecha)}</td>
<td>${ci.hora}</td>
<td>${ci.entrevistador_rh}</td>
<td>${ci.jefe_depto||'N/A'}</td>
<td><span style="font-weight:bold;color:${ci.estado==='Realizada'?'green':ci.estado==='Cancelada'?'red':'#ca8a04'}">${ci.estado}</span></td>
</tr>
`).join('')}
</tbody>
</table>
${filtradas.length===0 ? '<div style="text-align:center;padding:20px;color:#6b7280;">No hay registros en esta categoría.</div>' : ''}
</div>
