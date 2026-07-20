<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
  Gestión de Vacaciones
  <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="mostrarModalVacaciones()">+ Solicitar</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Empleado<br><input value="${filtroNombreVacaciones}" oninput="filtroNombreVacaciones=this.value;filtrarConDelay('vacaciones')" style="width:90%;margin-top:5px;"></th>
<th>Inicio contrato</th>
<th>Inicio vacaciones</th>
<th>Fin vacaciones</th>
<th>Días</th>
<th>Tipo</th>
<th>Estado</th>
<th>Cobertura</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
${vacaciones.filter(v=>{
    let emp = empleados.find(e=>e.id===v.empleado_id);
    let nombreCompleto = emp ? (emp.nombre + " " + emp.ap + " " + emp.am).toLowerCase() : "";
    return nombreCompleto.includes(filtroNombreVacaciones.toLowerCase());
}).map(v=>{
let emp = empleados.find(e=>e.id===v.empleado_id);
return `
<tr>
<td>${emp ? emp.nombre : 'N/A'}</td>
<td>${emp ? formatearFecha(emp.fecha) : ''}</td>
<td>${formatearFecha(v.inicio)}</td>
<td>${formatearFecha(v.fin)}</td>
<td>${v.dias}</td>
<td>${v.tipo}</td>
<td><span style="padding:4px 8px;border-radius:6px;background:${v.estado==="Aprobadas"?"#22c55e":v.estado==="Pendiente"?"#facc15":"#ef4444"};color:white;font-size:12px;">${v.estado}</span></td>
<td>${v.cobertura}</td>
<td style="text-align:center;"><button style="background:#ef4444; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;" onclick="eliminarVacacionGlobal(${vacaciones.indexOf(v)})">Eliminar</button></td>
</tr>`;
}).join('')}
</tbody>
</table>
</div>
