<div class="tabs">
<div class="tab" onclick="mostrar('candidatos')">Candidatos</div>
<div class="tab active" onclick="mostrar('ficha_candidato')">Ficha Detalle</div>
</div>
<div class="rh-card"><h2>Candidato: ${c.nombre} ${c.ap} ${c.am}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${c.nombre}" onchange="candSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${c.ap}" onchange="candSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${c.am}" onchange="candSel.am=this.value"></div>
<div><b>Celular</b><input value="${c.celular}" onchange="candSel.celular=this.value"></div>
<div><b>Correo</b><input value="${c.correo}" onchange="candSel.correo=this.value"></div>
<div><b>Nivel educativo</b><input value="${c.nivel_educativo}" onchange="candSel.nivel_educativo=this.value"></div>
</div>
</div>

<div class="rh-card">
<h3>Historial y Entrevistas</h3>
<div class="empleado-grid">
<div><b>Estatus Actual</b>
<select onchange="candSel.estatus_reclutamiento=this.value; mostrar('ficha_candidato');" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="Pendiente" ${c.estatus_reclutamiento==='Pendiente'?'selected':''}>Pendiente</option>
    <option value="En Entrevista" ${c.estatus_reclutamiento==='En Entrevista'?'selected':''}>En Entrevista</option>
    <option value="Prueba Técnica" ${c.estatus_reclutamiento==='Prueba Técnica'?'selected':''}>Prueba Técnica</option>
    <option value="Rechazado" ${c.estatus_reclutamiento==='Rechazado'?'selected':''}>Rechazado</option>
    <option value="Contratado" ${c.estatus_reclutamiento==='Contratado'?'selected':''}>Contratado</option>
</select>
</div>
<div><b>Calificación del perfil</b>
<select onchange="candSel.calificacion=parseInt(this.value); mostrar('ficha_candidato');" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="0" ${c.calificacion===0?'selected':''}>0 Estrellas</option>
    <option value="1" ${c.calificacion===1?'selected':''}>1 Estrella</option>
    <option value="2" ${c.calificacion===2?'selected':''}>2 Estrellas</option>
    <option value="3" ${c.calificacion===3?'selected':''}>3 Estrellas</option>
    <option value="4" ${c.calificacion===4?'selected':''}>4 Estrellas</option>
    <option value="5" ${c.calificacion===5?'selected':''}>5 Estrellas</option>
</select>
</div>
</div>
</div>

<div class="rh-card">
<h3>Notas de Entrevista (Observaciones)</h3>
<textarea id="txtObservacion" style="width:100%;height:90px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" placeholder="Escribe notas de la entrevista o del perfil..."></textarea>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarObservacionCand()">Guardar Nota</button></div>
<hr>
<div class="obs-list">
${c.observaciones.length===0 ? `<div id="noObs">Sin notas registradas.</div>` : 
c.observaciones.map(o=>`<div class="obs-item"><div class="obs-fecha">${o.fecha}</div><div>${o.texto}</div></div>`).join('')}
</div>
</div>
</div>

<div class="col">
<div class="rh-card">
<h3>Datos de la Vacante</h3>
<div class="empleado-grid">
<div><b>Tipo de vacante</b><input value="${c.tipo_candidatura}" readonly></div>
<div><b>Puesto deseado</b><input value="${c.puesto_deseado}" onchange="candSel.puesto_deseado=this.value"></div>
<div><b>Expectativa Salarial / Beca</b><input value="${c.expectativa_salarial}" onchange="candSel.expectativa_salarial=this.value"></div>
<div><b>Fecha Primera Postulación</b><input type="date" value="${c.fecha_postulacion}" readonly></div>
<div><b>Fecha Agendado (Contacto)</b><input type="date" value="${c.fecha_agendado || ''}" onchange="candSel.fecha_agendado=this.value"></div>
<div><b>Fecha de Cita Próxima</b><input type="date" value="${c.fecha_entrevista || ''}" onchange="candSel.fecha_entrevista=this.value"></div>
</div>
<div style="margin-top:10px;">
<b>Horarios Posibles (Disponibilidad)</b>
<textarea style="width:100%;height:60px;padding:8px;border:1px solid #d1d5db;border-radius:6px;margin-top:5px;" placeholder="Ej. Lunes a Viernes por las tardes" onchange="candSel.horarios_disponibles=this.value">${c.horarios_disponibles||''}</textarea>
</div>
</div>

<div class="rh-card">
<h3>Acuse Documental (CV, Portafolio)</h3>
<button class="btn-ver" onclick="escanear()" style="margin-bottom:10px;">Escanear Físico</button>
<button class="btn-ver" onclick="document.getElementById('fileUpload').click()" style="background:#3b82f6;">Subir Archivo (PDF/IMG)</button>
<input type="file" id="fileUpload" style="display:none" onchange="subirArchivoCandidato(this)">
<div id="dwtcontrolContainer"></div>
<hr>
${c.documentos.length===0 ? "Sin documentos" : c.documentos.map(d=>`
<div style="display:inline-block;margin:5px;text-align:center;">
${d.tipo==='imagen' ? `<img src="${d.url}" onclick="ver('${d.url}')" style="width:120px;cursor:pointer;"><br>` : `<div style="padding:20px;background:#e5e7eb;font-weight:bold;">PDF/DOC</div><br>`}
<small>${d.nombre||''}</small><br>
<button class="btn-ver" onclick="descargarURL('${d.url}')">Descargar</button>
</div>`).join('')}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<h3>Acciones de Ficha</h3>
<button class="btn-ver" onclick="convertirCandidato()" style="background:#22c55e; width:100%; margin-bottom:10px; font-size:15px;">✅ Aprobar y Convertir a ${c.tipo_candidatura}</button>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#3b82f6; margin:0; flex:1;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#eab308; margin:0; flex:1;" onclick="exportarFichaPDF()">Exportar PDF</button>
<button class="btn-ver" style="background:#ef4444; margin:0; flex:1;" onclick="eliminarRegistro('candidato')">Eliminar Registro</button>
</div>
</div>
