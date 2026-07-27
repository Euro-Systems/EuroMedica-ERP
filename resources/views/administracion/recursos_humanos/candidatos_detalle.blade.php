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
<div style="display:flex; gap:10px; margin-bottom:10px;">
    <button class="btn-ver" onclick="escanear()" style="background:#475569;"><i class="bi bi-printer-fill me-1"></i> Escanear Físico</button>
    <button class="btn-ver" onclick="document.getElementById('fileUpload').click()" style="background:#2563eb;"><i class="bi bi-upload me-1"></i> Subir Archivo (PDF/IMG)</button>
</div>
<input type="file" id="fileUpload" style="display:none" onchange="subirArchivoCandidato(this)">
<div id="dwtcontrolContainer"></div>
<hr style="margin:12px 0; border:0; border-top:1px solid #e2e8f0;">
${(c.documentos || []).length===0 ? '<p style="color:#6b7280;text-align:center;margin:8px 0;font-size:13px;"><i class="bi bi-info-circle me-1"></i> Sin documentos cargados</p>' : (c.documentos || []).map(d=>`
<div style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <i class="bi bi-file-earmark-text-fill" style="font-size:20px; color:#2563eb;"></i>
        <span style="font-weight:600; font-size:13px; color:#1e293b;">${d.nombre || 'Documento.pdf'}</span>
    </div>
    <button class="btn-ver" style="background:#2563eb; padding:5px 10px; font-size:12px;" onclick="descargarURL('${d.url}')"><i class="bi bi-download"></i> Descargar</button>
</div>`).join('')}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:14px;">
<h3 style="border:none; padding:0; margin-bottom:10px;"><i class="bi bi-gear-fill me-2"></i>Acciones de Ficha</h3>
<button class="btn-ver" onclick="convertirCandidato()" style="background:#16a34a; width:100%; margin-bottom:10px; font-size:14px; padding:10px 18px;">
    <i class="bi bi-check-circle-fill me-1"></i> Aprobar y Convertir a ${c.tipo_candidatura}
</button>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#2563eb; flex:1;" onclick="guardarCambiosFicha()"><i class="bi bi-floppy-fill me-1"></i> Guardar Cambios</button>
<button class="btn-ver" style="background:#0284c7; flex:1;" onclick="exportarFichaPDF()"><i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar PDF</button>
<button class="btn-ver" style="background:#dc2626; flex:1;" onclick="eliminarRegistro('candidato')"><i class="bi bi-trash-fill me-1"></i> Eliminar Registro</button>
</div>
</div>
