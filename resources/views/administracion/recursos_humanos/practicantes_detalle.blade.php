<div class="tabs">
    <div class="tab" onclick="vistaPract = p.egreso ? 'historial' : 'activos'; mostrar('practicantes');">
        <i class="bi bi-arrow-left me-1"></i> 
        ${ p.egreso ? 'Volver a Historial' : 'Volver a Practicantes' }
    </div>
    <div class="tab active" onclick="mostrar('ficha_practicante')">Ficha Detalle: ${p.nombre}</div>
</div>

<div class="rh-card" style="margin-bottom:15px; border-left:4px solid #1e3a8a; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0; font-size:18px; color:#1e3a8a; font-weight:bold;">
        <i class="bi bi-mortarboard-fill me-2"></i>Practicante: ${p.nombre} ${p.ap || ''} ${p.am || ''}
    </h2>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="guardarCambiosFicha()"><i class="bi bi-floppy-fill"></i> Guardar Cambios</button>
        <button class="btn text-white" style="background:#d97706; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="darDeBajaPracticante()"><i class="bi bi-person-x-fill"></i> Dar de Baja</button>
        ${esAdminRH ? `<button class="btn text-white" style="background:#dc2626; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:13px; display:inline-flex; align-items:center; gap:4px;" onclick="eliminarRegistro('practicante')" title="Solo administradores"><i class="bi bi-trash3-fill"></i> Eliminar Registro</button>` : ''}
    </div>
</div>

<!-- SUB-TABS NAVIGATION -->
<div class="tabs-sub" style="display:flex; gap:8px; margin-bottom:15px; background:#f8fafc; padding:6px; border-radius:12px; border:1px solid #e2e8f0;">
    <button type="button" class="tab-sub ${subTabPracticante === 'informacion' ? 'active' : ''}" onclick="cambiarSubTabPracticante('informacion')" style="flex:1; justify-content:center;">
        <i class="bi bi-person-lines-fill me-1"></i> Información
    </button>
    <button type="button" class="tab-sub ${subTabPracticante === 'expediente' ? 'active' : ''}" onclick="cambiarSubTabPracticante('expediente')" style="flex:1; justify-content:center;">
        <i class="bi bi-folder-fill me-1"></i> Expediente
    </button>
    <button type="button" class="tab-sub ${subTabPracticante === 'entrevista' ? 'active' : ''}" onclick="cambiarSubTabPracticante('entrevista')" style="flex:1; justify-content:center;">
        <i class="bi bi-chat-left-text-fill me-1"></i> Entrevista
    </button>
    <button type="button" class="tab-sub ${subTabPracticante === 'permisos' ? 'active' : ''}" onclick="cambiarSubTabPracticante('permisos')" style="flex:1; justify-content:center;">
        <i class="bi bi-calendar-check me-1"></i> Permisos
    </button>
</div>

<div class="ficha-wrap">

    <!-- SUB-TAB 1: INFORMACIÓN -->
    <div id="subtab_prac_informacion" style="display: ${subTabPracticante === 'informacion' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Datos Personales -->
            <div class="rh-card" style="grid-column: span 2;">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-person-badge-fill me-2"></i>Datos Personales</h3>
                <div class="empleado-grid grid-responsive-4" style="gap:12px;">
                    <div>
                        <label class="input-label">Nombre</label>
                        <input class="input-custom ${p.nombre ? 'input-filled' : ''}" value="${p.nombre}" onchange="practSel.nombre=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Paterno</label>
                        <input class="input-custom ${p.ap ? 'input-filled' : ''}" value="${p.ap || ''}" onchange="practSel.ap=this.value">
                    </div>
                    <div>
                        <label class="input-label">Apellido Materno</label>
                        <input class="input-custom ${p.am ? 'input-filled' : ''}" value="${p.am || ''}" onchange="practSel.am=this.value">
                    </div>
                    <div>
                        <label class="input-label">Celular</label>
                        <input class="input-custom ${p.celular ? 'input-filled' : ''}" value="${p.celular || ''}" onchange="practSel.celular=this.value">
                    </div>
                    <div>
                        <label class="input-label">NSS</label>
                        <input class="input-custom ${p.nss ? 'input-filled' : ''}" value="${p.nss || ''}" onchange="practSel.nss=this.value">
                    </div>
                    <div>
                        <label class="input-label">RFC</label>
                        <input class="input-custom ${p.rfc ? 'input-filled' : ''}" value="${p.rfc || ''}" onchange="practSel.rfc=this.value">
                    </div>
                    <div>
                        <label class="input-label">CURP</label>
                        <input class="input-custom ${p.curp ? 'input-filled' : ''}" value="${p.curp || ''}" onchange="practSel.curp=this.value">
                    </div>
                    <div>
                        <label class="input-label">Estado civil</label>
                        <input class="input-custom ${p.estado_civil ? 'input-filled' : ''}" value="${p.estado_civil || ''}" onchange="practSel.estado_civil=this.value">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Dirección</label>
                        <input class="input-custom ${p.direccion ? 'input-filled' : ''}" value="${p.direccion || ''}" onchange="practSel.direccion=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha nacimiento</label>
                        <input class="input-custom ${p.nacimiento ? 'input-filled' : ''}" type="date" value="${p.nacimiento || ''}" onchange="practSel.nacimiento=this.value">
                    </div>
                    <div>
                        <label class="input-label">Talla Uniforme</label>
                        <select onchange="practSel.talla_uniforme=this.value" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;">
                            <option value="S" ${p.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
                            <option value="M" ${p.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
                            <option value="L" ${p.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
                            <option value="XL" ${p.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Tipo de Sangre</label>
                        <input class="input-custom ${p.tipo_sangre ? 'input-filled' : ''}" value="${p.tipo_sangre || ''}" onchange="practSel.tipo_sangre=this.value" placeholder="O+">
                    </div>
                    <div>
                        <label class="input-label">Alergias</label>
                        <input class="input-custom ${p.alergias ? 'input-filled' : ''}" value="${p.alergias || ''}" onchange="practSel.alergias=this.value" placeholder="Ninguna">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="input-label">Nivel de Inglés</label>
                        <input class="input-custom ${p.nivel_ingles ? 'input-filled' : ''}" value="${p.nivel_ingles || ''}" onchange="practSel.nivel_ingles=this.value" placeholder="B2 / 80%">
                    </div>
                </div>
            </div>

            <!-- Control de Horas -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-clock-history me-2"></i>Control de Horas</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Horas requeridas</label>
                        <input id="horasReqInput" class="input-custom ${p.horas_requeridas ? 'input-filled' : ''}" value="${p.horas_requeridas || 480}">
                    </div>
                    <div>
                        <label class="input-label">Horas acumuladas</label>
                        <input id="horasInput" class="input-custom ${p.horas_llevadas ? 'input-filled' : ''}" value="${p.horas_llevadas || 0}">
                    </div>
                </div>
                <div style="margin-top:15px; text-align:right;">
                    <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:6px; font-weight:600; font-size:12.5px;" onclick="guardarHoras()">Guardar Horas</button>
                </div>
            </div>

            <!-- Periodo -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-calendar-range-fill me-2"></i>Periodo de Prácticas</h3>
                <div class="empleado-grid grid-responsive-2" style="gap:12px;">
                    <div>
                        <label class="input-label">Fecha de Inicio</label>
                        <input class="input-custom ${p.fecha_inicio ? 'input-filled' : ''}" type="date" value="${p.fecha_inicio || ''}" onchange="practSel.fecha_inicio=this.value">
                    </div>
                    <div>
                        <label class="input-label">Fecha de Término</label>
                        <input class="input-custom ${p.fecha_termino ? 'input-filled' : ''}" type="date" value="${p.fecha_termino || ''}" onchange="practSel.fecha_termino=this.value">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 2: EXPEDIENTE -->
    <div id="subtab_prac_expediente" style="display: ${subTabPracticante === 'expediente' ? 'block' : 'none'}; width:100%;">
        <div class="rh-card" style="border-left: 4px solid #16a34a;">
            <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#16a34a;"><i class="bi bi-folder-fill me-2"></i>Documentos Digitales</h3>
            
            <div style="display:flex; gap:10px; margin-bottom:15px;">
                <button class="btn text-white" onclick="escanear()" style="background:#475569; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-printer-fill"></i> Escanear Documento</button>
                <button class="btn text-white" onclick="document.getElementById('fileUploadPrac').click()" style="background:#2563eb; border:none; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;"><i class="bi bi-upload"></i> Subir Archivo (PDF/IMG)</button>
            </div>
            <input type="file" id="fileUploadPrac" style="display:none" onchange="subirArchivoPracticante(this)">
            
            <div id="dwtcontrolContainer"></div>
            
            <hr style="margin:16px 0; border:0; border-top:1px solid #e2e8f0;">
            
            <div style="overflow-x: auto; width: 100%;">
            <table class="rh-table" style="width:100%; min-width:500px; border-collapse:collapse; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                <thead>
                    <tr style="background:#1e3a8a; color:white;">
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Documento</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center; width:150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="listaDocumentos">
                    <!-- Populated dynamically via renderizarDocumentos() -->
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: ENTREVISTA -->
    <div id="subtab_prac_entrevista" style="display: ${subTabPracticante === 'entrevista' ? 'block' : 'none'}; width:100%;">
        <div class="grid-responsive-1-1" style="gap:16px;">
            <!-- Notas de Observaciones -->
            <div class="rh-card">
                <h3 style="margin-top:0; border-bottom:2px solid #f1f5f9; padding-bottom:10px; color:#1e3a8a;"><i class="bi bi-journal-text me-2"></i>Historial de Observaciones</h3>
                <textarea id="txtObservacion" style="width:100%; height:80px; padding:8px; border:1px solid #cbd5e1; border-radius:8px;" placeholder="Escribe observaciones del practicante..."></textarea>
                <div style="margin-top:10px; text-align:right;">
                    <button class="btn text-white" style="background:#2563eb; border:none; padding:8px 16px; border-radius:6px; font-weight:600; font-size:12.5px;" onclick="guardarObservacion()">Guardar</button>
                </div>
                <hr style="margin:15px 0; border:0; border-top:1px solid #e2e8f0;">
                <div class="obs-list" style="max-height: 250px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                    ${p.observaciones.length === 0 ? 
                        `<div id="noObs" style="text-align:center; color:#64748b; font-size:13px; font-style:italic;">Sin observaciones registradas.</div>` : 
                        p.observaciones.map(o => `
                        <div class="obs-item" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                            <div class="obs-fecha" style="font-weight:700; font-size:11px; color:#64748b; margin-bottom:4px;">${o.fecha}</div>
                            <div style="font-size:13px; color:#1e293b; word-break:break-word;">${o.texto}</div>
                        </div>`).join('')}
                </div>
            </div>

            <!-- Ficha Técnica de Entrevista Asociada -->
            ${ (() => {
                let citaAsoc = citas.find(ci => ci.nombre === p.nombre || ci.celular === p.celular || ci.correo === p.correo);
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

    <!-- SUB-TAB 4: PERMISOS -->
    <div id="subtab_prac_permisos" style="display: ${subTabPracticante === 'permisos' ? 'block' : 'none'}; width:100%;">
        <!-- Métricas desglosadas de permisos -->
        <div class="grid-responsive-3" style="gap:16px; margin-bottom:15px;">
            <div class="rh-card" style="padding:15px; border-left:4px solid #2563eb; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Permisos Solicitados (Días)</h4>
                <div style="font-size:24px; font-weight:800; color:#2563eb; margin-top:5px;">${permPrac.reduce((sum, v) => sum + v.dias, 0)}</div>
            </div>
            <div class="rh-card" style="padding:15px; border-left:4px solid #16a34a; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Aprobados (Ausencia)</h4>
                <div style="font-size:24px; font-weight:800; color:#16a34a; margin-top:5px;">${totalDiasTomados}</div>
            </div>
            <div class="rh-card" style="padding:15px; border-left:4px solid #eab308; text-align:center; margin:0;">
                <h4 style="margin:0; font-size:12px; color:#475569; text-transform:uppercase;">Días Pendientes de Aprobación</h4>
                <div style="font-size:24px; font-weight:800; color:#ca8a04; margin-top:5px;">${permPrac.filter(v => v.estado === 'Pendiente').reduce((sum, v) => sum + v.dias, 0)}</div>
            </div>
        </div>

        <div class="rh-card" style="border-left: 4px solid #1e3a8a;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom: 2px solid #f1f5f9; padding-bottom:10px;">
                <h3 style="margin:0; color:#1e3a8a; border:none; padding:0;"><i class="bi bi-calendar-check me-2"></i>Permisos y Ausencias</h3>
                <button class="btn text-white" style="background:#22c55e; margin:0; padding:8px 16px; font-size:12.5px; border-radius:8px; font-weight:600; display:inline-flex; align-items:center; gap:4px;" onclick="mostrarModalPermisosPracticante()">
                    <i class="bi bi-plus-lg"></i> Registrar Permiso
                </button>
            </div>
            
            <div style="font-size:14px; font-weight:bold; color:#1e3a8a; background:#eff6ff; padding:10px 16px; border-radius:8px; margin-bottom:15px; display:inline-block;">
                Días de inasistencia tomados (Aprobados): <span style="font-size:16px; color:#16a34a; font-weight:800;">${totalDiasTomados}</span>
            </div>

            <div style="overflow-x: auto; width: 100%;">
            <table class="rh-table" style="width:100%; min-width:800px; border-collapse:collapse; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                <thead>
                    <tr style="background:#1e3a8a; color:white;">
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Inicio</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Fin</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center;">Días</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Tipo</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Estado</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:left;">Motivo / Obs.</th>
                        <th style="padding:12px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    ${permPrac.length === 0 ? `
                    <tr>
                        <td colspan="7" style="padding:15px; text-align:center; color:#6b7280; font-size:13px; font-style:italic;"><i class="bi bi-info-circle me-1"></i> No se han registrado permisos para este practicante.</td>
                    </tr>
                    ` : permPrac.map(v=>`
                    <tr style="border-bottom:1px solid #e2e8f0; transition:background-color 0.2s;">
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
                            <div style="display:inline-flex; gap:8px; justify-content:center; align-items:center;">
                            ${v.estado === 'Pendiente' ? `
                                <button type="button" class="btn text-white" style="background-color:#16a34a; border:none; border-radius:8px; padding:6px 10px; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" onclick="aprobarPermisoPracticante(${v.index})" title="Aprobar Permiso">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" class="btn text-white" style="background-color:#ef4444; border:none; border-radius:8px; padding:6px 10px; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" onclick="rechazarPermisoPracticante(${v.index})" title="Rechazar Permiso">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            ` : v.estado === 'Aprobadas' ? `
                                <span style="color:#16a34a; font-weight:bold; font-size:13px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-check-circle-fill"></i> Aprobado</span>
                            ` : `
                                <span style="color:#ef4444; font-weight:bold; font-size:13px; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-x-circle-fill"></i> Rechazado</span>
                            `}
                            <button type="button" class="btn text-white" style="background-color:#6b7280; border:none; border-radius:8px; padding:6px 10px; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" onclick="eliminarPermisoPracticante(${v.index})" title="Eliminar Registro">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                            </div>
                        </td>
                    </tr>`).join('')}
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>


