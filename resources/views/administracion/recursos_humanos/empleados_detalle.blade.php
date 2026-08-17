<div class="tabs">
    <div class="tab" onclick="filtroEstado = e.egreso ? 'Inactivo' : 'Activo'; mostrar('empleados');">
        <i class="bi bi-arrow-left me-1"></i> 
        ${ e.egreso ? 'Volver a Historial' : 'Volver a Empleados' }
    </div>
    <div class="tab active" onclick="mostrar('ficha')">Ficha Detalle: ${e.nombre}</div>
</div>

<div class="rh-card" style="margin-bottom:15px; border-left:4px solid #1e3a8a; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0; font-size:18px; color:#1e3a8a; font-weight:bold;">
        <i class="bi bi-person-badge-fill me-2"></i>Empleado: ${e.nombre} ${e.ap || ''} ${e.am || ''}
    </h2>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="guardarBD()"><i class="bi bi-floppy-fill"></i> Guardar Cambios</button>
        <button class="btn text-white" style="background:#d97706; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="document.getElementById('modalBaja').style.display='flex'"><i class="bi bi-person-x-fill"></i> Dar de Baja</button>
        ${esAdminRH ? `<button class="btn text-white" style="background:#dc2626; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="eliminarRegistro('empleado')" title="Solo administradores"><i class="bi bi-trash3-fill"></i> Eliminar Registro</button>` : ''}
    </div>
</div>

<!-- SUB-TABS NAVIGATION -->
<div class="tabs-sub" style="display:flex; gap:8px; margin-bottom:15px; background:#f8fafc; padding:6px; border-radius:12px; border:1px solid #e2e8f0;">
    <button type="button" class="tab-sub ${subTabEmpleado === 'informacion' ? 'active' : ''}" onclick="cambiarSubTabEmpleado('informacion')" style="flex:1; justify-content:center;">
        <i class="bi bi-person-lines-fill me-1"></i> Información
    </button>
    <button type="button" class="tab-sub ${subTabEmpleado === 'expediente' ? 'active' : ''}" onclick="cambiarSubTabEmpleado('expediente')" style="flex:1; justify-content:center;">
        <i class="bi bi-folder-fill me-1"></i> Expediente
    </button>
    <button type="button" class="tab-sub ${subTabEmpleado === 'entrevista' ? 'active' : ''}" onclick="cambiarSubTabEmpleado('entrevista')" style="flex:1; justify-content:center;">
        <i class="bi bi-chat-left-text-fill me-1"></i> Entrevista
    </button>
    <button type="button" class="tab-sub ${subTabEmpleado === 'vacaciones' ? 'active' : ''}" onclick="cambiarSubTabEmpleado('vacaciones')" style="flex:1; justify-content:center;">
        <i class="bi bi-calendar3 me-1"></i> Vacaciones
    </button>
</div>

<div class="ficha-wrap">

    <!-- SUB-TAB 1: INFORMACIÓN -->
    <div id="subtab_emp_informacion" style="display: ${subTabEmpleado === 'informacion' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Datos Personales -->
            <div class="rh-card" style="grid-column: span 2;">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-person-badge-fill me-2"></i>Datos Personales</h3>
                <div class="empleado-grid grid-responsive-4" style="gap:12px;">
                    <div>
                        <label class="input-label">Nombre</label>
                        <input class="input-custom ${e.nombre ? 'input-filled' : ''}" value="${e.nombre}" onchange="empSel.nombre=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Paterno</label>
                        <input class="input-custom ${e.ap ? 'input-filled' : ''}" value="${e.ap || ''}" onchange="empSel.ap=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Materno</label>
                        <input class="input-custom ${e.am ? 'input-filled' : ''}" value="${e.am || ''}" onchange="empSel.am=this.value">
                    </div>
                    <div>
                        <label class="input-label">Celular</label>
                        <input class="input-custom ${e.celular ? 'input-filled' : ''}" value="${e.celular || ''}" onchange="empSel.celular=this.value">
                    </div>
                    <div>
                        <label class="input-label">NSS</label>
                        <input class="input-custom ${e.nss ? 'input-filled' : ''}" value="${e.nss || ''}" onchange="empSel.nss=this.value">
                    </div>
                    <div>
                        <label class="input-label">RFC</label>
                        <input class="input-custom ${e.rfc ? 'input-filled' : ''}" value="${e.rfc || ''}" onchange="empSel.rfc=this.value">
                    </div>
                    <div>
                        <label class="input-label">CURP</label>
                        <input class="input-custom ${e.curp ? 'input-filled' : ''}" value="${e.curp || ''}" onchange="empSel.curp=this.value">
                    </div>
                    <div>
                        <label class="input-label">Género</label>
                        <div class="radio-group" style="display:flex; gap:10px; margin-top:8px;">
                            <label style="cursor:pointer; font-size:13px;"><input type="radio" onchange="empSel.sexo='Hombre'" name="rSexo" ${e.sexo==="Hombre"?"checked":""}> Hombre</label>
                            <label style="cursor:pointer; font-size:13px;"><input type="radio" onchange="empSel.sexo='Mujer'" name="rSexo" ${e.sexo==="Mujer"?"checked":""}> Mujer</label>
                        </div>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Dirección</label>
                        <input class="input-custom ${e.direccion ? 'input-filled' : ''}" value="${e.direccion || ''}" onchange="empSel.direccion=this.value">
                    </div>
                    <div>
                        <label class="input-label">Estado civil</label>
                        <input class="input-custom ${e.estado_civil ? 'input-filled' : ''}" value="${e.estado_civil || ''}" onchange="empSel.estado_civil=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha nacimiento</label>
                        <input class="input-custom ${e.nacimiento ? 'input-filled' : ''}" type="date" value="${e.nacimiento || ''}" onchange="empSel.nacimiento=this.value">
                    </div>
                    <div>
                        <label class="input-label">Talla Uniforme</label>
                        <select onchange="empSel.talla_uniforme=this.value" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;">
                            <option value="S" ${e.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
                            <option value="M" ${e.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
                            <option value="L" ${e.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
                            <option value="XL" ${e.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Tipo de Sangre</label>
                        <input class="input-custom ${e.tipo_sangre ? 'input-filled' : ''}" value="${e.tipo_sangre || ''}" onchange="empSel.tipo_sangre=this.value" placeholder="O+">
                    </div>
                    <div>
                        <label class="input-label">Alergias / Med.</label>
                        <input class="input-custom ${e.alergias ? 'input-filled' : ''}" value="${e.alergias || ''}" onchange="empSel.alergias=this.value" placeholder="Ninguna">
                    </div>
                    <div>
                        <label class="input-label">Canal Captación</label>
                        <input class="input-custom ${e.canal_captacion ? 'input-filled' : ''}" value="${e.canal_captacion || ''}" onchange="empSel.canal_captacion=this.value" placeholder="Facebook">
                    </div>
                    <div style="grid-column: span 4;">
                        <label class="input-label">CLABE Bancaria</label>
                        <input class="input-custom ${e.clabe_bancaria ? 'input-filled' : ''}" value="${e.clabe_bancaria || ''}" onchange="empSel.clabe_bancaria=this.value">
                    </div>
                </div>
            </div>

            <!-- Contacto de Emergencia -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-telephone-outbound-fill me-2"></i>Contacto de Emergencia</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div style="grid-column: span 2;">
                        <label class="input-label">Nombre completo</label>
                        <input class="input-custom ${e.contacto_emergencia ? 'input-filled' : ''}" value="${e.contacto_emergencia || ''}" onchange="empSel.contacto_emergencia=this.value">
                    </div>
                    <div>
                        <label class="input-label">Parentesco</label>
                        <input class="input-custom ${e.parentesco ? 'input-filled' : ''}" value="${e.parentesco || ''}" onchange="empSel.parentesco=this.value">
                    </div>
                    <div>
                        <label class="input-label">Teléfono Principal</label>
                        <input class="input-custom ${e.tel_emergencia1 ? 'input-filled' : ''}" value="${e.tel_emergencia1 || ''}" onchange="empSel.tel_emergencia1=this.value">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Teléfono Secundario</label>
                        <input class="input-custom ${e.tel_emergencia2 ? 'input-filled' : ''}" value="${e.tel_emergencia2 || ''}" onchange="empSel.tel_emergencia2=this.value">
                    </div>
                </div>
            </div>

            <!-- Datos Laborales -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-briefcase-fill me-2"></i>Datos Laborales</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Puesto</label>
                        <input class="input-custom ${e.puesto ? 'input-filled' : ''}" value="${e.puesto || ''}" onchange="empSel.puesto=this.value">
                    </div>
                    <div>
                        <label class="input-label">Empresa</label>
                        <input class="input-custom ${e.empresa ? 'input-filled' : ''}" value="${e.empresa || ''}" onchange="empSel.empresa=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha de Ingreso</label>
                        <input class="input-custom ${e.fecha ? 'input-filled' : ''}" type="date" value="${e.fecha || ''}" onchange="empSel.fecha=this.value; mostrar('ficha')">
                    </div>
                    <div>
                        <label class="input-label">Alta IMSS</label>
                        <input class="input-custom ${e.alta_imss ? 'input-filled' : ''}" type="date" value="${e.alta_imss || ''}" onchange="empSel.alta_imss=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha Egreso</label>
                        <input class="input-custom ${e.egreso ? 'input-filled' : ''}" value="${e.egreso || ''}" placeholder="YYYY-MM-DD" onchange="empSel.egreso=this.value">
                    </div>
                    <div>
                        <label class="input-label">Motivo Egreso</label>
                        <input class="input-custom ${e.motivo ? 'input-filled' : ''}" value="${e.motivo || ''}" onchange="empSel.motivo=this.value">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 2: EXPEDIENTE -->
    <div id="subtab_emp_expediente" style="display: ${subTabEmpleado === 'expediente' ? 'block' : 'none'}; width:100%;">
        <div class="rh-card" style="border-left: 4px solid #16a34a;">
            <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#16a34a;"><i class="bi bi-folder-fill me-2"></i>Documentos Digitales</h3>
            <div style="margin-bottom:15px; display:flex; gap:10px;">
                <button class="btn text-white" onclick="escanear()" style="background:#475569; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-printer-fill"></i> Escanear Documento</button>
                <button class="btn text-white" onclick="document.getElementById('fileUploadEmp').click()" style="background:#2563eb; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-upload"></i> Subir Archivo (PDF/IMG)</button>
            </div>
            <input type="file" id="fileUploadEmp" style="display:none" onchange="subirArchivoEmpleado(this)">
            
            <div id="dwtcontrolContainer"></div>
            
            <hr style="margin:16px 0; border:0; border-top:1px solid #e2e8f0;">
            
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:12px;">
                ${(e.documentos || []).length === 0 ? 
                    '<p style="color:#6b7280; text-align:center; grid-column:1/-1; margin:15px 0; font-size:13px; font-style:italic;"><i class="bi bi-info-circle me-1"></i> Sin documentos cargados</p>' : 
                    (e.documentos || []).map(d => `
                    <div style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; padding:10px 16px; border:1px solid #e2e8f0; border-radius:10px;">
                        <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                            <i class="bi bi-file-earmark-text-fill" style="font-size:22px; color:#2563eb; flex-shrink:0;"></i>
                            <span style="font-weight:600; font-size:12.5px; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${d.nombre || 'Documento.pdf'}">${d.nombre || 'Documento.pdf'}</span>
                        </div>
                        <button class="btn text-white" style="background:#2563eb; border:none; border-radius:6px; padding:6px 10px; font-size:11px; margin:0;" onclick="descargarPDF('${d.url}')"><i class="bi bi-download"></i> Descargar</button>
                    </div>`).join('')}
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: ENTREVISTA -->
    <div id="subtab_emp_entrevista" style="display: ${subTabEmpleado === 'entrevista' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Notas de Empleado (Observaciones) -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-journal-text me-2"></i>Historial de Observaciones</h3>
                <textarea id="txtObservacion" style="width:100%; height:80px; padding:8px; border:1px solid #cbd5e1; border-radius:8px;" placeholder="Escribe notas u observaciones del empleado..."></textarea>
                <div style="margin-top:10px; text-align:right;">
                    <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:6px; font-weight:600; font-size:12.5px;" onclick="guardarObservacion()">Guardar</button>
                </div>
                <hr style="margin:15px 0; border:0; border-top:1px solid #e2e8f0;">
                <div class="obs-list" style="max-height: 250px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                    ${e.observaciones.length === 0 ? 
                        `<div id="noObs" style="text-align:center; color:#64748b; font-size:13px; font-style:italic;">Sin observaciones registradas.</div>` : 
                        e.observaciones.map(o => `
                        <div class="obs-item" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                            <div class="obs-fecha" style="font-weight:700; font-size:11px; color:#64748b; margin-bottom:4px;">${o.fecha}</div>
                            <div style="font-size:13px; color:#1e293b; word-break:break-word;">${o.texto}</div>
                        </div>`).join('')}
                </div>
            </div>

            <!-- Ficha Técnica de la entrevista inicial si existe -->
            ${ (() => {
                let citaAsoc = citas.find(ci => ci.nombre === e.nombre || ci.celular === e.celular || ci.correo === e.correo);
                if (citaAsoc && citaAsoc.evaluacion && citaAsoc.evaluacion.tipo) {
                    return `
                    <div class="rh-card" style="border-left: 4px solid #4f46e5; background:#f5f3ff;">
                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #e0e7ff; padding-bottom:10px; margin-bottom:15px;">
                            <h3 style="margin:0; color:#4f46e5; border:none; padding:0;"><i class="bi bi-clipboard2-check-fill me-2"></i>Entrevista Inicial: ${citaAsoc.evaluacion.tipo}</h3>
                            <span style="font-size:12px; font-weight:700; color:#4f46e5; background:#e0e7ff; padding:4px 10px; border-radius:20px;">LECTURA BLOQUEADA</span>
                        </div>
                        <p style="font-size:13px; color:#475569; margin-bottom:15px;">Datos registrados durante su entrevista de reclutamiento:</p>
                        
                        <div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:12px; padding:15px; max-height:280px; overflow-y:auto; pointer-events:none; opacity:0.85;">
                            <div class="empleado-grid grid-responsive-2" style="gap:12px; font-size:12px;">
                                <div><b>Puesto evaluado:</b> ${citaAsoc.evaluacion.candidato_para || citaAsoc.puesto || 'N/A'}</div>
                                <div><b>Entrevistador:</b> ${citaAsoc.evaluacion.entrevista_por || 'N/A'}</div>
                                <div><b>Fecha entrevista:</b> ${citaAsoc.evaluacion.fecha || 'N/A'}</div>
                                <div><b>Edad en entrevista:</b> ${citaAsoc.evaluacion.edad || 'N/A'}</div>
                                <div><b>Estado civil:</b> ${citaAsoc.evaluacion.estado_civil || 'N/A'}</div>
                                <div><b>Horario propuesto:</b> ${citaAsoc.evaluacion.horario || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                    `;
                }
                return `
                <div class="rh-card" style="border-left: 4px solid #6b7280; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:30px; color:#6b7280;">
                    <i class="bi bi-clipboard-x" style="font-size:40px; margin-bottom:10px;"></i>
                    <p style="font-size:13px; font-style:italic;">No se encontró una evaluación técnica asociada a este nombre o datos de contacto en las citas de reclutamiento.</p>
                </div>
                `;
            })() }
        </div>
    </div>

    <!-- SUB-TAB 4: VACACIONES -->
    <div id="subtab_emp_vacaciones" style="display: ${subTabEmpleado === 'vacaciones' ? 'block' : 'none'}; width:100%;">
        <!-- Métricas desglosadas de vacaciones -->
        <div class="grid-responsive-4" style="gap:16px; margin-bottom:15px;">
            <div class="rh-card" style="padding:15px; border-left:4px solid #1e3a8a; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Totales del Año</h4>
                <div style="font-size:24px; font-weight:800; color:#1e3a8a; margin-top:5px;">${diasTotales}</div>
            </div>
            <div class="rh-card" style="padding:15px; border-left:4px solid #2563eb; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Solicitados</h4>
                <div style="font-size:24px; font-weight:800; color:#2563eb; margin-top:5px;">${vacEmp.reduce((a,v)=>a+v.dias, 0)}</div>
            </div>
            <div class="rh-card" style="padding:15px; border-left:4px solid #16a34a; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Aprobados</h4>
                <div style="font-size:24px; font-weight:800; color:#16a34a; margin-top:5px;">${vacEmp.filter(v=>v.estado==='Aprobadas').reduce((a,v)=>a+v.dias, 0)}</div>
            </div>
            <div class="rh-card" style="padding:15px; border-left:4px solid #eab308; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Pendientes</h4>
                <div style="font-size:24px; font-weight:800; color:#ca8a04; margin-top:5px;">${vacEmp.filter(v=>v.estado==='Pendiente').reduce((a,v)=>a+v.dias, 0)}</div>
            </div>
        </div>

        <div class="rh-card" style="border-left: 4px solid #1e3a8a;">
            <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-calendar3 me-2"></i>Gestión de Vacaciones</h3>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                <div>
                    <b>Año de servicio:</b>
                    <div class="radio-group" style="display:inline-flex; gap:6px; margin-left:10px;">
                        ${aniosEmpleado.map(a=>`
                        <label style="padding:6px 12px; border-radius:20px; border:1px solid #d1d5db; cursor:pointer; font-size:12px; font-weight:700; background:${e.anioSeleccionado==a ? '#1e3a8a' : '#fff'}; color:${e.anioSeleccionado==a ? '#fff' : '#000'}; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center;">
                            <input type="radio" name="anioVacaciones" value="${a}" onchange="cambiarAnio(this.value)" style="display:none" ${e.anioSeleccionado==a ? 'checked' : ''}>${a}
                        </label>`).join('')}
                    </div>
                </div>
                <div style="font-size:14px; font-weight:bold; color:#1e3a8a; background:#eff6ff; padding:8px 16px; border-radius:8px;">
                    Días disponibles: <span style="font-size:16px; color:#16a34a; font-weight:800;">${disponibles}</span> / ${diasTotales}
                </div>
            </div>

            ${disponibles > 0 ? `
            <div style="background:#dcfce7; border:1px solid #16a34a; color:#15803d; padding:12px; border-radius:8px; margin-bottom:15px; display:flex; align-items:center; gap:8px; font-size:12.5px; line-height:1.4;">
                <i class="bi bi-check-circle-fill" style="font-size:18px; color:#16a34a; flex-shrink:0;"></i>
                <div>El empleado cuenta con <b>${disponibles} días disponibles</b> para solicitar vacaciones.</div>
            </div>
            ` : `
            <div style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:15px; display:flex; align-items:center; gap:8px; font-size:12.5px; line-height:1.4;">
                <i class="bi bi-x-circle-fill" style="font-size:18px; color:#ef4444; flex-shrink:0;"></i>
                <div>El empleado <b>no tiene días disponibles</b> de vacaciones en este momento.</div>
            </div>
            `}

            ${(() => {
                let alerta = comprobarVacacionesNoTomadas(e);
                if(alerta.alertar) {
                    return `<div style="background:#fffbeb; border:1px solid #f59e0b; color:#b45309; padding:12px; border-radius:8px; margin-bottom:15px; display:flex; align-items:center; gap:8px; font-size:12.5px; line-height:1.4;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:18px; color:#d97706; flex-shrink:0;"></i>
                        <div><b>Alerta de Vacaciones:</b> El empleado tiene vacaciones pendientes por disfrutar del año de servicio <b>${alerta.anio}</b>.</div>
                    </div>`;
                }
                return '';
            })()}

            <div style="overflow-x: auto; width: 100%;">
            <table class="rh-table" style="width:100%; min-width:800px; border-collapse:collapse; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                <thead>
                    <tr style="background:#1e3a8a; color:white;">
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Inicio contrato</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Inicio</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Fin</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center;">Días</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Tipo</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Estado</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Cobertura</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    ${vacEmp.length === 0 ? `
                        <tr><td colspan="8" style="text-align:center; color:#6b7280; padding:15px; font-style:italic;">Sin solicitudes de vacaciones registradas.</td></tr>
                    ` : vacEmp.map(v=>`
                    <tr style="border-bottom:1px solid #e2e8f0; transition:background-color 0.2s;">
                        <td style="padding:12px; font-size:13px; color:#475569;">${formatearFecha(e.fecha)}</td>
                        <td style="padding:12px; font-size:13px; color:#475569;">${formatearFecha(v.inicio)}</td>
                        <td style="padding:12px; font-size:13px; color:#475569;">${formatearFecha(v.fin)}</td>
                        <td style="padding:12px; font-size:13px; font-weight:600; color:#1e293b; text-align:center;">${v.dias}</td>
                        <td style="padding:12px; font-size:13px; color:#475569;">${v.tipo}</td>
                        <td style="padding:12px; font-size:13px; vertical-align:middle;">
                            <span style="padding:6px 12px; border-radius:20px; background:${v.estado==="Aprobadas"?"#dcfce7":v.estado==="Pendiente"?"#fef9c3":"#fee2e2"}; color:${v.estado==="Aprobadas"?"#15803d":v.estado==="Pendiente"?"#854d0e":"#991b1b"}; font-size:11.5px; font-weight:700; display:inline-block;">
                                ${v.estado}
                            </span>
                        </td>
                        <td style="padding:12px; font-size:13px; color:#475569;">${v.cobertura || '-'}</td>
                        <td style="padding:12px; text-align:center; white-space:nowrap; vertical-align:middle;">
                            ${v.estado === 'Pendiente' ? `
                                <div style="display:inline-flex; gap:8px; justify-content:center; align-items:center;">
                                    <button type="button" class="btn text-white" style="background-color:#16a34a; border:none; border-radius:8px; padding:6px 10px; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" onclick="aprobarVacacionFicha(${v.index})" title="Aprobar Solicitud">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn text-white" style="background-color:#ef4444; border:none; border-radius:8px; padding:6px 10px; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" onclick="rechazarVacacionFicha(${v.index})" title="Rechazar Solicitud">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            ` : v.estado === 'Aprobadas' ? `
                                <span style="color:#16a34a; font-weight:bold; font-size:13px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-check-circle-fill"></i> Aprobada</span>
                            ` : `
                                <span style="color:#ef4444; font-weight:bold; font-size:13px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-x-circle-fill"></i> Rechazada</span>
                            `}
                        </td>
                    </tr>`).join('')}
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>


