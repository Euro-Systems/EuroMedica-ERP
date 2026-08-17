<div class="tabs">
    <div class="tab" onclick="mostrar('candidatos')"><i class="bi bi-arrow-left me-1"></i> Candidatos</div>
    <div class="tab active" onclick="mostrar('ficha_candidato')">Ficha Detalle: ${c.nombre}</div>
</div>

<div class="rh-card" style="margin-bottom:15px; border-left:4px solid #1e3a8a; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0; font-size:18px; color:#1e3a8a; font-weight:bold;">
        <i class="bi bi-person-bounding-box me-2"></i>Candidato: ${c.nombre} ${c.ap || ''} ${c.am || ''}
    </h2>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button class="btn text-white" onclick="convertirCandidato()" style="background:#16a34a; border:none; padding:8px 16px; border-radius:8px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-check-circle-fill"></i> Aprobar y Convertir a ${c.tipo_candidatura}
        </button>
        <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="guardarCambiosFicha()"><i class="bi bi-floppy-fill"></i> Guardar Cambios</button>
        <button class="btn text-white" style="background:#dc2626; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="eliminarRegistro('candidato')"><i class="bi bi-trash-fill"></i> Eliminar</button>
    </div>
</div>

<!-- SUB-TABS NAVIGATION -->
<div class="tabs-sub" style="display:flex; gap:8px; margin-bottom:15px; background:#f8fafc; padding:6px; border-radius:12px; border:1px solid #e2e8f0;">
    <button type="button" class="tab-sub ${subTabCandidato === 'informacion' ? 'active' : ''}" onclick="cambiarSubTabCandidato('informacion')" style="flex:1; justify-content:center;">
        <i class="bi bi-person-lines-fill me-1"></i> Información
    </button>
    <button type="button" class="tab-sub ${subTabCandidato === 'expediente' ? 'active' : ''}" onclick="cambiarSubTabCandidato('expediente')" style="flex:1; justify-content:center;">
        <i class="bi bi-folder-fill me-1"></i> Expediente
    </button>
    <button type="button" class="tab-sub ${subTabCandidato === 'entrevista' ? 'active' : ''}" onclick="cambiarSubTabCandidato('entrevista')" style="flex:1; justify-content:center;">
        <i class="bi bi-chat-left-text-fill me-1"></i> Entrevista
    </button>
</div>

<div class="ficha-wrap">

    <!-- SUB-TAB 1: INFORMACIÓN -->
    <div id="subtab_cand_informacion" style="display: ${subTabCandidato === 'informacion' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Datos Personales -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-person-badge-fill me-2"></i>Datos Personales</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Nombre</label>
                        <input class="input-custom ${c.nombre ? 'input-filled' : ''}" value="${c.nombre}" onchange="candSel.nombre=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Paterno</label>
                        <input class="input-custom ${c.ap ? 'input-filled' : ''}" value="${c.ap || ''}" onchange="candSel.ap=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Materno</label>
                        <input class="input-custom ${c.am ? 'input-filled' : ''}" value="${c.am || ''}" onchange="candSel.am=this.value">
                    </div>
                    <div>
                        <label class="input-label">Celular</label>
                        <input class="input-custom ${c.celular ? 'input-filled' : ''}" value="${c.celular || ''}" onchange="candSel.celular=this.value">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Correo Electrónico</label>
                        <input class="input-custom ${c.correo ? 'input-filled' : ''}" value="${c.correo || ''}" onchange="candSel.correo=this.value">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Nivel Educativo</label>
                        <input class="input-custom ${c.nivel_educativo ? 'input-filled' : ''}" value="${c.nivel_educativo || ''}" onchange="candSel.nivel_educativo=this.value">
                    </div>
                </div>
            </div>

            <!-- Datos de la Vacante -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-briefcase-fill me-2"></i>Datos de la Vacante</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Tipo de Vacante</label>
                        <input class="input-custom input-filled" value="${c.tipo_candidatura}" readonly>
                    </div>
                    <div>
                        <label class="input-label">Puesto Deseado</label>
                        <input class="input-custom ${c.puesto_deseado ? 'input-filled' : ''}" value="${c.puesto_deseado || ''}" onchange="candSel.puesto_deseado=this.value">
                    </div>
                    <div>
                        <label class="input-label">Expectativa Salarial / Beca</label>
                        <input class="input-custom ${c.expectativa_salarial ? 'input-filled' : ''}" value="${c.expectativa_salarial || ''}" onchange="candSel.expectativa_salarial=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha Postulación</label>
                        <input class="input-custom input-filled" type="date" value="${c.fecha_postulacion || ''}" readonly>
                    </div>
                    <div>
                        <label class="input-label">Fecha Agendado (Contacto)</label>
                        <input class="input-custom ${c.fecha_agendado ? 'input-filled' : ''}" type="date" value="${c.fecha_agendado || ''}" onchange="candSel.fecha_agendado=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha de Cita Próxima</label>
                        <input class="input-custom ${c.fecha_entrevista ? 'input-filled' : ''}" type="date" value="${c.fecha_entrevista || ''}" onchange="candSel.fecha_entrevista=this.value">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Horarios Posibles (Disponibilidad)</label>
                        <textarea class="input-custom ${c.horarios_disponibles ? 'input-filled' : ''}" style="width:100%; height:50px; padding:8px; margin-top:4px;" placeholder="Ej. Lunes a Viernes por las tardes" onchange="candSel.horarios_disponibles=this.value">${c.horarios_disponibles || ''}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 2: EXPEDIENTE -->
    <div id="subtab_cand_expediente" style="display: ${subTabCandidato === 'expediente' ? 'block' : 'none'}; width:100%;">
        <div class="rh-card" style="border-left: 4px solid #16a34a;">
            <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#16a34a;"><i class="bi bi-folder2-open me-2"></i>Acuse Documental (CV, Portafolio)</h3>
            
            <div style="display:flex; gap:10px; margin-bottom:15px;">
                <button class="btn text-white" onclick="escanear()" style="background:#475569; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-printer-fill"></i> Escanear Físico</button>
                <button class="btn text-white" onclick="document.getElementById('fileUpload').click()" style="background:#2563eb; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-upload"></i> Subir Archivo (PDF/IMG)</button>
            </div>
            
            <input type="file" id="fileUpload" style="display:none" onchange="subirArchivoCandidato(this)">
            <div id="dwtcontrolContainer"></div>
            
            <hr style="margin:16px 0; border:0; border-top:1px solid #e2e8f0;">
            
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:12px;">
                ${(c.documentos || []).length === 0 ? 
                    '<p style="color:#6b7280; text-align:center; grid-column:1/-1; margin:15px 0; font-size:13px; font-style:italic;"><i class="bi bi-info-circle me-1"></i> Sin documentos cargados</p>' : 
                    (c.documentos || []).map(d => `
                    <div style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; padding:10px 16px; border:1px solid #e2e8f0; border-radius:10px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                            <i class="bi bi-file-earmark-text-fill" style="font-size:22px; color:#2563eb; flex-shrink:0;"></i>
                            <span style="font-weight:600; font-size:12.5px; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${d.nombre || 'Documento.pdf'}">${d.nombre || 'Documento.pdf'}</span>
                        </div>
                        <button class="btn text-white" style="background:#2563eb; border:none; border-radius:6px; padding:6px 10px; font-size:11px; display:inline-flex; align-items:center; gap:4px; margin:0;" onclick="descargarURL('${d.url}')"><i class="bi bi-download"></i> Descargar</button>
                    </div>`).join('')}
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: ENTREVISTA -->
    <div id="subtab_cand_entrevista" style="display: ${subTabCandidato === 'entrevista' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Estatus y Calificación -->
            <div class="rh-card" style="height:fit-content;">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-star-fill me-2"></i>Estatus de Reclutamiento</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Estatus Actual</label>
                        <select onchange="candSel.estatus_reclutamiento=this.value; mostrar('ficha_candidato');" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;">
                            <option value="Pendiente" ${c.estatus_reclutamiento==='Pendiente'?'selected':''}>Pendiente</option>
                            <option value="En Entrevista" ${c.estatus_reclutamiento==='En Entrevista'?'selected':''}>En Entrevista</option>
                            <option value="Prueba Técnica" ${c.estatus_reclutamiento==='Prueba Técnica'?'selected':''}>Prueba Técnica</option>
                            <option value="Rechazado" ${c.estatus_reclutamiento==='Rechazado'?'selected':''}>Rechazado</option>
                            <option value="Contratado" ${c.estatus_reclutamiento==='Contratado'?'selected':''}>Contratado</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Calificación del perfil</label>
                        <select onchange="candSel.calificacion=parseInt(this.value); mostrar('ficha_candidato');" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;">
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

            <!-- Notas de Entrevista (Observaciones) -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-journal-text me-2"></i>Notas y Observaciones</h3>
                <textarea id="txtObservacion" style="width:100%; height:80px; padding:8px; border:1px solid #cbd5e1; border-radius:8px;" placeholder="Escribe notas de la entrevista o del perfil..."></textarea>
                <div style="margin-top:10px; text-align:right;">
                    <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:6px; font-weight:600; font-size:12.5px;" onclick="guardarObservacionCand()">Guardar Nota</button>
                </div>
                <hr style="margin:15px 0; border:0; border-top:1px solid #e2e8f0;">
                <div class="obs-list" style="max-height: 250px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                    ${c.observaciones.length === 0 ? 
                        `<div id="noObs" style="text-align:center; color:#64748b; font-size:13px; font-style:italic;">Sin notas registradas.</div>` : 
                        c.observaciones.map(o => `
                        <div class="obs-item" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                            <div class="obs-fecha" style="font-weight:700; font-size:11px; color:#64748b; margin-bottom:4px;">${o.fecha}</div>
                            <div style="font-size:13px; color:#1e293b; word-break:break-word;">${o.texto}</div>
                        </div>`).join('')}
                </div>
            </div>

            <!-- Ficha Técnica Asociada si existe -->
            ${ (() => {
                // Buscamos cita asociada
                let citaAsoc = citas.find(ci => ci.nombre === c.nombre || ci.celular === c.celular || ci.correo === c.correo);
                if (citaAsoc && citaAsoc.evaluacion && citaAsoc.evaluacion.tipo) {
                    return `
                    <div class="rh-card" style="grid-column: span 2; border-left: 4px solid #4f46e5; background:#f5f3ff;">
                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #e0e7ff; padding-bottom:10px; margin-bottom:15px;">
                            <h3 style="margin:0; color:#4f46e5; border:none; padding:0;"><i class="bi bi-clipboard2-check-fill me-2"></i>Evaluación Técnica: ${citaAsoc.evaluacion.tipo}</h3>
                            <span style="font-size:12px; font-weight:700; color:#4f46e5; background:#e0e7ff; padding:4px 10px; border-radius:20px;">LECTURA BLOQUEADA</span>
                        </div>
                        <p style="font-size:13px; color:#475569; margin-bottom:15px;">A continuación se muestran los datos de la ficha técnica capturados en la entrevista inicial:</p>
                        
                        <div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:12px; padding:15px; max-height:400px; overflow-y:auto; pointer-events:none; opacity:0.85;">
                            <!-- Mapeo rápido de datos de la evaluación -->
                            <div class="empleado-grid grid-responsive-3" style="gap:12px; font-size:12px;">
                                <div><b>Puesto:</b> ${citaAsoc.evaluacion.candidato_para || citaAsoc.puesto || 'N/A'}</div>
                                <div><b>Entrevistador:</b> ${citaAsoc.evaluacion.entrevista_por || 'N/A'}</div>
                                <div><b>Fecha:</b> ${citaAsoc.evaluacion.fecha || 'N/A'}</div>
                                <div><b>Edad:</b> ${citaAsoc.evaluacion.edad || 'N/A'}</div>
                                <div><b>Estado Civil:</b> ${citaAsoc.evaluacion.estado_civil || 'N/A'}</div>
                                <div><b>Horario Propuesto:</b> ${citaAsoc.evaluacion.horario || 'N/A'}</div>
                                ${ citaAsoc.evaluacion.tipo === 'Practicante' ? `
                                    <div><b>Universidad:</b> ${citaAsoc.evaluacion.universidad || 'N/A'}</div>
                                    <div><b>Carrera:</b> ${citaAsoc.evaluacion.carrera || 'N/A'}</div>
                                    <div><b>Horas:</b> ${citaAsoc.evaluacion.horas_requeridas || 'N/A'}</div>
                                ` : '' }
                                ${ citaAsoc.evaluacion.tipo === 'Enfermero' ? `
                                    <div><b>Universidad:</b> ${citaAsoc.evaluacion.enf_universidad || 'N/A'}</div>
                                    <div><b>Título:</b> ${citaAsoc.evaluacion.enf_titulo || 'N/A'}</div>
                                    <div><b>Cédula:</b> ${citaAsoc.evaluacion.enf_cedula || 'N/A'}</div>
                                ` : '' }
                                ${ citaAsoc.evaluacion.tipo === 'Medico' ? `
                                    <div><b>Universidad:</b> ${citaAsoc.evaluacion.med_universidad || 'N/A'}</div>
                                    <div><b>Especialidad:</b> ${citaAsoc.evaluacion.med_especialidad || 'N/A'}</div>
                                    <div><b>Cédula:</b> ${citaAsoc.evaluacion.med_cedula || 'N/A'}</div>
                                ` : '' }
                            </div>
                        </div>
                    </div>
                    `;
                }
                return '';
            })() }
        </div>
    </div>

</div>


