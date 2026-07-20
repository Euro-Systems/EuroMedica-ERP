<div class="tabs">
<div class="tab" onclick="mostrar('empleados')">Empleados</div>
<div class="tab active" onclick="mostrar('ficha')">Ficha Detalle</div>
</div>
<div class="rh-card">
<h2>${e.nombre} ${e.ap} ${e.am}</h2>
</div>

<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${e.nombre}" onchange="empSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${e.ap}" onchange="empSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${e.am}" onchange="empSel.am=this.value"></div>
<div><b>NSS</b><input value="${e.nss}" onchange="empSel.nss=this.value"></div>
<div><b>RFC</b><input value="${e.rfc}" onchange="empSel.rfc=this.value"></div>
<div><b>CURP</b><input value="${e.curp}" onchange="empSel.curp=this.value"></div>
<div><b>Género</b><div class="radio-group">
<label><input type="radio" onchange="empSel.sexo='Hombre'" name="rSexo" ${e.sexo==="Hombre"?"checked":""}> Hombre</label>
<label><input type="radio" onchange="empSel.sexo='Mujer'" name="rSexo" ${e.sexo==="Mujer"?"checked":""}> Mujer</label>
</div></div>
<div><b>Celular</b><input value="${e.celular}" onchange="empSel.celular=this.value"></div>
<div><b>Dirección</b><input value="${e.direccion}" onchange="empSel.direccion=this.value"></div>
<div><b>Estado civil</b><input value="${e.estado_civil}" onchange="empSel.estado_civil=this.value"></div>
<div><b>Fecha nacimiento</b><input type="date" value="${e.nacimiento}" onchange="empSel.nacimiento=this.value"></div>
<div><b>Talla Uniforme</b><select onchange="empSel.talla_uniforme=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="S" ${e.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
    <option value="M" ${e.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
    <option value="L" ${e.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
    <option value="XL" ${e.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
</select></div>
<div><b>Tipo de Sangre</b><input value="${e.tipo_sangre||''}" onchange="empSel.tipo_sangre=this.value" placeholder="O+"></div>
<div><b>Alergias/Med.</b><input value="${e.alergias||''}" onchange="empSel.alergias=this.value" placeholder="Ninguna"></div>
<div><b>Canal Captación</b><input value="${e.canal_captacion||''}" onchange="empSel.canal_captacion=this.value" placeholder="Facebook"></div>
<div><b>CLABE Bancaria</b><input value="${e.clabe_bancaria||''}" onchange="empSel.clabe_bancaria=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Contacto de emergencia</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${e.contacto_emergencia}" onchange="empSel.contacto_emergencia=this.value"></div>
<div><b>Parentesco</b><input value="${e.parentesco}" onchange="empSel.parentesco=this.value"></div>
<div><b>Teléfono 1</b><input value="${e.tel_emergencia1}" onchange="empSel.tel_emergencia1=this.value"></div>
<div><b>Teléfono 2</b><input value="${e.tel_emergencia2}" onchange="empSel.tel_emergencia2=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Historial de observaciones</h3>
<textarea id="txtObservacion" style="width:100%;height:90px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" placeholder="Escribe una observación..."></textarea>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarObservacion()">Guardar</button></div>
<hr>
<div class="obs-list">
${e.observaciones.length===0 ? `<div id="noObs">Sin observaciones registradas.</div>` : 
e.observaciones.map(o=>`<div class="obs-item"><div class="obs-fecha">${o.fecha}</div><div>${o.texto}</div></div>`).join('')}
</div>
</div>
</div>

<div class="col">
<div class="rh-card">
<h3>Datos laborales</h3>
<div class="empleado-grid">
<div><b>Puesto</b><input value="${e.puesto}" onchange="empSel.puesto=this.value"></div>
<div><b>Empresa</b><input value="${e.empresa}" onchange="empSel.empresa=this.value"></div>
<div><b>Fecha inicio</b><input type="date" value="${e.fecha}" onchange="empSel.fecha=this.value"></div>
<div><b>Alta IMSS</b><input type="date" value="${e.alta_imss}" onchange="empSel.alta_imss=this.value"></div>
<div><b>Fecha egreso</b><input value="${e.egreso}" onchange="empSel.egreso=this.value" placeholder="YYYY-MM-DD"></div>
<div><b>Motivo Egreso</b><input value="${e.motivo}" onchange="empSel.motivo=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Vacaciones</h3>
<div style="margin-bottom:10px;">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:10px;">
<div><b>Año:</b><div class="radio-group">
${aniosEmpleado.map(a=>`
<label style="padding:6px 12px;border-radius:20px;border:1px solid #d1d5db;cursor:pointer;background:${e.anioSeleccionado==a ? '#1e3a8a' : '#fff'};color:${e.anioSeleccionado==a ? '#fff' : '#000'};">
<input type="radio" name="anioVacaciones" value="${a}" onchange="cambiarAnio(this.value)" style="display:none" ${e.anioSeleccionado==a ? 'checked' : ''}>${a}
</label>`).join('')}
</div></div>
<div><b>Días disponibles:</b> ${disponibles} / ${diasTotales}</div>
</div>
${disponibles<=0 ? `<div style="background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px;">⚠ Ya no tiene días disponibles</div>` : ``}
</div>

<table class="rh-table">
<thead><tr><th>Inicio contrato</th><th>Inicio</th><th>Fin</th><th>Días</th><th>Tipo</th><th>Estado</th><th>Cobertura</th><th>Acción</th></tr></thead>
<tbody>
${vacEmp.map(v=>`
<tr>
<td>${formatearFecha(e.fecha)}</td>
<td>${formatearFecha(v.inicio)}</td>
<td>${formatearFecha(v.fin)}</td>
<td>${v.dias}</td>
<td>${v.tipo}</td>
<td><span style="padding:4px 8px;border-radius:6px;background:${v.estado==="Aprobadas"?"#22c55e":v.estado==="Pendiente"?"#facc15":"#ef4444"};color:white;font-size:12px;">${v.estado}</span></td>
<td>${v.cobertura}</td>
<td style="text-align:center;">
<button class="btn-ver" onclick="aprobarVacacionFicha(${v.index})" ${disponibles<=0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>Aprobar</button>
<button class="btn-ver" onclick="rechazarVacacionFicha(${v.index})" ${disponibles<=0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>Rechazar</button>
</td>
</tr>`).join('')}
</tbody>
</table>
</div>

<div class="rh-card">
<h3>Documentos</h3>
<button class="btn-ver" onclick="escanear()">Escanear documento</button>
<div id="dwtcontrolContainer"></div>
<hr>
${e.documentos.length===0 ? "Sin documentos" : e.documentos.map(d=>`
<div style="display:inline-block;margin:5px;">
<img src="${d.url}" onclick="ver('${d.url}')" style="width:120px;cursor:pointer;"><br>
<button class="btn-ver" onclick="descargarPDF('${d.url}')">PDF</button>
</div>`).join('')}
</div>
</div>
</div>
