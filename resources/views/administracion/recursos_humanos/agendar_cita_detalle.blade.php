<div class="tabs">
<div class="tab" onclick="mostrar('citas')">Agendar Citas</div>
<div class="tab active" onclick="mostrar('ficha_cita')">Detalle de Cita</div>
</div>
<div class="rh-card"><h2>Cita: ${ci.nombre}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos de la Cita</h3>
<div class="empleado-grid">
<div><b>Nombre del aspirante</b><input value="${ci.nombre}" onchange="citaSel.nombre=this.value"></div>
<div><b>Puesto deseado</b><input value="${ci.puesto}" onchange="citaSel.puesto=this.value"></div>
<div><b>Sector</b><select onchange="citaSel.tipo=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="Trabajador" ${ci.tipo==='Trabajador'?'selected':''}>Trabajador</option>
    <option value="Practicante" ${ci.tipo==='Practicante'?'selected':''}>Practicante</option>
</select></div>
<div><b>Fecha de cita</b><input type="date" value="${ci.fecha}" onchange="citaSel.fecha=this.value"></div>
<div><b>Hora</b><input type="time" value="${ci.hora}" onchange="citaSel.hora=this.value"></div>
<div><b>Entrevistador RH</b><input value="${ci.entrevistador_rh}" onchange="citaSel.entrevistador_rh=this.value"></div>
<div><b>Jefe Depto.</b><input value="${ci.jefe_depto}" onchange="citaSel.jefe_depto=this.value"></div>
<div><b>Celular</b><input value="${ci.celular}" onchange="citaSel.celular=this.value"></div>
<div><b>Correo</b><input value="${ci.correo}" onchange="citaSel.correo=this.value"></div>
</div>
</div>
</div>
<div class="col">
<div class="rh-card">
<h3>Notas previas</h3>
<textarea style="width:100%;height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px;" onchange="citaSel.notas=this.value" placeholder="Observaciones preliminares...">${ci.notas||''}</textarea>
</div>
<div class="rh-card">
<h3>CV Preliminar</h3>
<button class="btn-ver" onclick="document.getElementById('fileUploadCita').click()" style="background:#3b82f6;">Subir Archivo</button>
<input type="file" id="fileUploadCita" style="display:none" onchange="subirArchivoCita(this)">
<hr>
${ci.documentos && ci.documentos.length > 0 ? `
<div style="text-align:center;">
${ci.documentos[0].tipo==='imagen' ? `<img src="${ci.documentos[0].url}" style="width:120px;cursor:pointer;" onclick="ver('${ci.documentos[0].url}')"><br>` : `<div style="padding:10px;background:#e5e7eb;font-weight:bold;">PDF</div>`}
<small>${ci.documentos[0].nombre||'CV'}</small><br>
<button class="btn-ver" onclick="descargarURL('${ci.documentos[0].url}')">Descargar</button>
<button class="btn-ver" style="background:#ef4444;" onclick="eliminarCVCita()">✕</button>
</div>
` : '<p style="color:#6b7280;text-align:center;">Sin CV cargado</p>'}
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
<div style="display:flex; gap:8px;">
<button class="btn-ver" style="background:#3b82f6; padding:5px 10px; font-size:13px;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#facc15; padding:5px 10px; font-size:13px; color:black;" onclick="citaSel.estado='Realizada';guardarCambiosFicha();mostrar('citas')">Marcar como Realizada</button>
<button class="btn-ver" style="background:#ef4444; padding:5px 10px; font-size:13px;" onclick="eliminarRegistro('cita')">Eliminar Registro</button>
</div>
<button class="btn-ver" onclick="pasarFichaCitaACandidato()" style="background:#22c55e; padding:5px 12px; font-size:14px;">✅ Aprobar y Convertir a Candidato</button>
</div>
</div>
