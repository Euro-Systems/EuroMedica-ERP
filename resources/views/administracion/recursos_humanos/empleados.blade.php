<div class="tabs">
<div class="tab active" onclick="mostrar('empleados')">Empleados</div>
<div class="tab" onclick="mostrar('ficha')">Ficha Detalle</div>
</div>
<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    Empleados
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevoEmpleado()">+ Nuevo Empleado</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Nombre<br><input value="${filtroNombre}" oninput="filtroNombre=this.value;filtrarConDelay('empleados')" style="width:90%"></th>
<th>Apellido Paterno</th>
<th>Apellido Materno</th>
<th>Empresa<br><input value="${filtroEmpresa}" oninput="filtroEmpresa=this.value;filtrarConDelay('empleados')" style="width:90%"></th>
<th>Estado<br>
<select onchange="filtroEstado=this.value;mostrar('empleados')" style="width:95%">
<option value="">Todos</option>
<option value="Activo" ${filtroEstado==="Activo"?"selected":""}>Activo</option>
<option value="Inactivo" ${filtroEstado==="Inactivo"?"selected":""}>Inactivo</option>
</select>
</th>
<th>Fecha Ingreso</th>
<th>Fecha Egreso</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
${filtrados.map(e=>{
let estado = e.egreso ? "Inactivo" : "Activo";
return `
<tr>
<td>${e.nombre}</td>
<td>${e.ap}</td>
<td>${e.am}</td>
<td>${e.empresa}</td>
<td>${estado}</td>
<td>${e.fecha}</td>
<td>${e.egreso || '-'}</td>
<td><button class="btn-ver" onclick="seleccionar(${e.id})">Ver</button></td>
</tr>
`;
}).join('')}
</tbody>
</table>
</div>
