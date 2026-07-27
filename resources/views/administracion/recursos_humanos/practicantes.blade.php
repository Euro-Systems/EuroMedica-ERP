<div class="tabs">
<div class="tab active" onclick="mostrar('practicantes')">Practicantes</div>
<div class="tab" onclick="mostrar('ficha_practicante')">Ficha Detalle</div>
</div>
<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    Practicantes
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevoPracticante()">+ Nuevo Practicante</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Nombre</th>
<th>Apellido paterno</th>
<th>Apellido materno</th>
<th>Empresa</th>
<th>Fecha Ingreso</th>
<th>Fecha Terminación</th>
<th>Horas requeridas</th>
<th>Horas acumuladas</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
${filtrados.map(p=>{
return `
<tr>
<td>${p.nombre}</td>
<td>${p.ap}</td>
<td>${p.am}</td>
<td>${p.empresa}</td>
<td>${p.fecha_inicio}</td>
<td>${p.fecha_termino}</td>
<td>${p.horas_requeridas}</td>
<td>${p.horas_llevadas}</td>
<td style="text-align:center;"><button class="btn-ver" onclick="seleccionarPract(${p.id})">Ver</button></td>
</tr>
`;
}).join('')}
</tbody>
</table>
</div>
