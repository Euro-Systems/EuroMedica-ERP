<div class="tabs">
<div class="tab ${filtroCandidatoTipo==='Trabajador' ? 'active' : ''}" onclick="filtroCandidatoTipo='Trabajador';mostrar('candidatos')">Para Trabajadores</div>
<div class="tab ${filtroCandidatoTipo==='Practicante' ? 'active' : ''}" onclick="filtroCandidatoTipo='Practicante';mostrar('candidatos')">Para Practicantes</div>
<div class="tab" onclick="mostrar('ficha_candidato')">Ficha Detalle</div>
</div>

<div class="rh-card">
<h2 style="display:flex; justify-content:space-between; align-items:center;">
    Candidatos a ${filtroCandidatoTipo}
    <button class="btn-ver" style="background:#22c55e; margin:0; padding:4px 8px; font-size:12px; font-weight:normal; border-radius:4px;" onclick="nuevoCandidato()">+ Nuevo Candidato</button>
</h2>
<table class="rh-table">
<thead>
<tr>
<th>Nombre <br><input value="${filtroNombreCandidato}" oninput="filtroNombreCandidato=this.value;filtrarConDelay('candidatos')" style="width:90%"></th>
<th>Puesto Deseado</th>
<th>Nivel Educativo</th>
<th>Fecha Postulación</th>
<th>Estatus</th>
<th>Calificación</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
${filtrados.map(c => `
<tr>
<td>${c.nombre} ${c.ap} ${c.am}</td>
<td>${c.puesto_deseado}</td>
<td>${c.nivel_educativo}</td>
<td>${formatearFecha(c.fecha_postulacion)}</td>
<td><span style="font-weight:bold;color:${c.estatus_reclutamiento==='Contratado'?'green':c.estatus_reclutamiento==='Rechazado'?'red':'#ca8a04'}">${c.estatus_reclutamiento}</span></td>
<td>${"⭐".repeat(c.calificacion)}${"☆".repeat(5-c.calificacion)}</td>
<td style="text-align:center;"><button class="btn-ver" onclick="seleccionarCandidato(${c.id})">Ver</button></td>
</tr>
`).join('')}
</tbody>
</table>
</div>
