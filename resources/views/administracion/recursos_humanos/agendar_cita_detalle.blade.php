<div class="tabs">
<div class="tab" onclick="mostrar('citas')"><i class="bi bi-arrow-left me-1"></i> Volver a Citas</div>
<div class="tab active" onclick="mostrar('ficha_cita')">Detalle de Cita</div>
</div>

<!-- MODO DE INTERACCIÓN: ENTREVISTA VS EXPEDIENTE -->
<div class="rh-card" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; padding:16px 20px; border-radius:12px; margin-bottom:15px; border:none;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="color:white; margin:0; font-size:18px; font-weight:bold;">
                <i class="bi bi-calendar-event me-2"></i>Cita: ${ci.nombre}
            </h2>
            <small style="color:#dbeafe; font-size:13px;">${ci.puesto || ''} | ${formatearFecha(ci.fecha)} - ${ci.hora || ''}</small>
        </div>
        
        <!-- BOTONES DE SELECCIÓN DE MODO -->
        <div style="display:${ !citas.find(c=>c.id==ci.id) ? 'none' : 'flex' }; gap:8px; background:rgba(255,255,255,0.15); padding:5px; border-radius:10px; border:1px solid rgba(255,255,255,0.2);">
            <button type="button" id="btn_modo_entrevista" class="btn-ver" style="background:${ (!ci.modo_actual || ci.modo_actual==='entrevista') ? '#ffffff' : 'transparent' }; color:${ (!ci.modo_actual || ci.modo_actual==='entrevista') ? '#1e3a8a' : '#ffffff' }; font-weight:bold; box-shadow:none; padding:7px 14px;" onclick="cambiarModoVistaCita('entrevista')">
                <i class="bi bi-journal-check me-1"></i> Entrevista
            </button>
            <button type="button" id="btn_modo_expediente" class="btn-ver" style="background:${ (ci.modo_actual==='expediente') ? '#16a34a' : 'transparent' }; color:#ffffff; font-weight:bold; box-shadow:none; padding:7px 14px;" onclick="cambiarModoVistaCita('expediente')">
                <i class="bi bi-folder2-open me-1"></i> Expediente
            </button>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- VISTA 1: ENTREVISTA (Datos y Formulario de Evaluación) -->
<!-- ========================================================= -->
<div id="vista_modo_entrevista_cita" style="display: ${ (!ci.modo_actual || ci.modo_actual==='entrevista') ? 'block' : 'none' };">
    <!-- DATOS BÁSICOS PRECARGADOS -->
    <div class="rh-card" style="margin-bottom: 15px; border-left:4px solid #1e3a8a;">
        <h3 style="margin:0 0 10px; font-size:15px; color:#1e3a8a;"><i class="bi bi-person-badge me-2"></i>Datos de la Cita</h3>
        <div class="empleado-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div><b style="font-size:12px;color:#64748b;">Nombre del aspirante</b><br><input value="${ci.nombre}" onchange="citaSel.nombre=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Puesto deseado</b><br><input value="${ci.puesto}" onchange="citaSel.puesto=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Sector</b><br><select onchange="citaSel.tipo=this.value">
                <option value="Trabajador" ${ci.tipo==='Trabajador'?'selected':''}>Trabajador</option>
                <option value="Practicante" ${ci.tipo==='Practicante'?'selected':''}>Practicante</option>
            </select></div>
            <div><b style="font-size:12px;color:#64748b;">Entrevistador RH</b><br><input value="${ci.entrevistador_rh}" onchange="citaSel.entrevistador_rh=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Fecha de cita</b><br><input type="date" value="${ci.fecha}" onchange="citaSel.fecha=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Hora</b><br><input type="time" value="${ci.hora}" onchange="citaSel.hora=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Validador</b><br><input value="${ci.jefe_depto}" onchange="citaSel.jefe_depto=this.value"></div>
            <div><b style="font-size:12px;color:#64748b;">Celular</b><br><input value="${ci.celular}" oninput="this.value=this.value.replace(/[^0-9+ ]/g,'')" onchange="citaSel.celular=this.value"></div>
        </div>
    </div>
    
    <div class="rh-card" style="border-left:4px solid #2563eb; display:${ !citas.find(c=>c.id==ci.id) ? 'none' : 'block' }; text-align:center; padding:30px;">
        <h3 style="margin:0 0 10px; font-size:20px; color:#1e3a8a; font-weight:bold; border:none; padding:0;">
            <i class="bi bi-journal-text me-2"></i>Selecciona el Formulario de Entrevista
        </h3>
        <p style="color:#64748b; font-size:14px; margin-bottom:20px;">Por favor elige el formulario que corresponde al perfil del candidato para comenzar la evaluación.</p>
        <select id="select_form_eval_cita" style="padding:12px 20px; border-radius:12px; border:2px solid #3b82f6; font-weight:bold; font-size:16px; background:#f0f9ff; color:#1e3a8a; cursor:pointer; width:100%; max-width:400px; text-align:center;" onchange="seleccionarFormularioEvaluacion(this.value)">
            <option value="">-- Seleccionar Formulario --</option>
            <option value="Practicante" ${ci.evaluacion && ci.evaluacion.tipo==='Practicante' ? 'selected' : ''}>👨‍🎓 Practicante (Ficha Técnica)</option>
            <option value="Enfermero" ${ci.evaluacion && ci.evaluacion.tipo==='Enfermero' ? 'selected' : ''}>🩺 Enfermería (Ficha Técnica)</option>
            <option value="Medico" ${ci.evaluacion && ci.evaluacion.tipo==='Medico' ? 'selected' : ''}>⚕️ Médico (Ficha Técnica)</option>
        </select>
    </div>

        <!-- BLOQUE FORMULARIO PRACTICANTE -->
        <div id="bloque_form_practicante" style="display: ${ (ci.evaluacion && ci.evaluacion.tipo==='Practicante') ? 'block' : 'none' }; background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #cbd5e1;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #1e3a8a; padding-bottom:10px;">
                <div>
                    <h2 style="margin:0; font-size:18px; color:#1e3a8a; font-weight:bold; text-transform:uppercase;">FICHA TÉCNICA DE EVALUACIÓN</h2>
                    <span style="font-size:14px; font-style:italic; color:#475569;">Área de Practicantes</span>
                </div>
                <div>
                    <button class="btn-ver" onclick="imprimirFichaPracticantePDF()" style="background:#0284c7; font-weight:bold;">
                        <i class="bi bi-printer me-1"></i> Imprimir / Descargar PDF
                    </button>
                </div>
            </div>

            <!-- I. DATOS DE CONTROL Y ENTREVISTA -->
            <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
                <h4 style="margin:0 0 10px; color:#1e3a8a; font-size:14px; font-weight:bold;">I. DATOS DE CONTROL Y ENTREVISTA</h4>
                <div class="empleado-grid" style="grid-template-columns: repeat(3, 1fr); gap:10px;">
                    <div><b>Candidato para:</b><input type="text" id="ev_candidato_para" value="${ci.evaluacion?.candidato_para || ci.puesto || ''}" onchange="actualizarEvaluacionCampo('candidato_para', this.value)"></div>
                    <div><b>Por:</b><input type="text" id="ev_entrevista_por" value="${ci.evaluacion?.entrevista_por || ci.entrevistador_rh || ''}" onchange="actualizarEvaluacionCampo('entrevista_por', this.value)"></div>
                    <div><b>Fecha:</b><input type="date" id="ev_fecha" value="${ci.evaluacion?.fecha || ci.fecha || ''}" onchange="actualizarEvaluacionCampo('fecha', this.value)"></div>
                    <div><b>Disponibilidad:</b><input type="text" id="ev_disponibilidad" value="${ci.evaluacion?.disponibilidad || ''}" placeholder="Ej: Inmediata / Medios tiempos" onchange="actualizarEvaluacionCampo('disponibilidad', this.value)"></div>
                    <div><b>Horario:</b><input type="text" id="ev_horario" value="${ci.evaluacion?.horario || ''}" placeholder="Ej: 8:00 am - 2:00 pm" onchange="actualizarEvaluacionCampo('horario', this.value)"></div>
                </div>
            </div>

            <!-- II. DATOS PERSONALES Y FAMILIARES -->
            <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
                <h4 style="margin:0 0 10px; color:#1e3a8a; font-size:14px; font-weight:bold;">II. DATOS PERSONALES Y FAMILIARES</h4>
                <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:12px;">
                    <div><b>Edad:</b><input type="text" id="ev_edad" value="${ci.evaluacion?.edad || ''}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" onchange="actualizarEvaluacionCampo('edad', this.value)"></div>
                    <div><b>A qué se dedica papá:</b><input type="text" id="ev_papa_dedica" value="${ci.evaluacion?.papa_dedica || ''}" onchange="actualizarEvaluacionCampo('papa_dedica', this.value)"></div>
                    <div><b>Vive en:</b><input type="text" id="ev_vive_en" value="${ci.evaluacion?.vive_en || ''}" placeholder="Colonia / Zona" onchange="actualizarEvaluacionCampo('vive_en', this.value)"></div>
                    <div><b>A qué se dedica mamá:</b><input type="text" id="ev_mama_dedica" value="${ci.evaluacion?.mama_dedica || ''}" onchange="actualizarEvaluacionCampo('mama_dedica', this.value)"></div>
                    <div><b>Vive con:</b><input type="text" id="ev_vive_con" value="${ci.evaluacion?.vive_con || ''}" placeholder="Padres, pareja, solo, etc." onchange="actualizarEvaluacionCampo('vive_con', this.value)"></div>
                    <div><b>Hermanos (A qué se dedican):</b><input type="text" id="ev_hermanos_dedican" value="${ci.evaluacion?.hermanos_dedican || ''}" onchange="actualizarEvaluacionCampo('hermanos_dedican', this.value)"></div>
                    <div>
                        <b>Estado Civil:</b>
                        <select id="ev_estado_civil" onchange="actualizarEvaluacionCampo('estado_civil', this.value)">
                            <option value="Soltero(a)" ${ci.evaluacion?.estado_civil==='Soltero(a)'?'selected':''}>Soltero(a)</option>
                            <option value="Casado(a)" ${ci.evaluacion?.estado_civil==='Casado(a)'?'selected':''}>Casado(a)</option>
                            <option value="Unión Libre" ${ci.evaluacion?.estado_civil==='Unión Libre'?'selected':''}>Unión Libre</option>
                            <option value="Otro" ${ci.evaluacion?.estado_civil==='Otro'?'selected':''}>Otro</option>
                        </select>
                    </div>

                    <!-- MEDIO DE TRANSPORTE Y TIEMPO DE LLEGAR EN UNA SOLA LÍNEA DISTRIBUIDA -->
                    <div style="grid-column: span 2; background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; margin-top:4px;">
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; font-size:13px;">
                            <b style="color:#1e3a8a;">Medio de transporte:</b>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="ev_transp_auto" ${ci.evaluacion?.transp_auto ? 'checked' : ''} onchange="actualizarEvaluacionCheck('transp_auto', this.checked)"> Auto propio</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="ev_transp_uber" ${ci.evaluacion?.transp_uber ? 'checked' : ''} onchange="actualizarEvaluacionCheck('transp_uber', this.checked)"> Uber / Didi / Taxi</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="ev_transp_publico" ${ci.evaluacion?.transp_publico ? 'checked' : ''} onchange="actualizarEvaluacionCheck('transp_publico', this.checked)"> Transp. Público</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="ev_transp_check_mueven" ${ci.evaluacion?.transp_check_mueven ? 'checked' : ''} onchange="toggleTranspMueven(this.checked)"> Lo mueven</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="ev_transp_check_otro" ${ci.evaluacion?.transp_check_otro ? 'checked' : ''} onchange="toggleTranspOtro(this.checked)"> Otro</label>
                            
                            <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                                <b style="color:#1e3a8a; white-space:nowrap;">Tiempo para llegar:</b>
                                <input type="text" id="ev_tiempo_llegar" value="${ci.evaluacion?.tiempo_llegar || ''}" placeholder="Ej: 30 minutos" style="width:160px; padding:5px 8px;" onchange="actualizarEvaluacionCampo('tiempo_llegar', this.value)">
                            </div>
                        </div>

                        <!-- INPUTS CONDICIONALES DE TRANSPORTE -->
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                            <div id="wrapper_transp_mueven" style="display: ${ci.evaluacion?.transp_check_mueven ? 'block' : 'none'};">
                                <b style="color:#1e3a8a;">¿Quién lo mueve?</b>
                                <input type="text" id="ev_transp_lo_mueven" value="${ci.evaluacion?.transp_lo_mueven || ''}" placeholder="Escribe quién..." onchange="actualizarEvaluacionCampo('transp_lo_mueven', this.value)">
                            </div>
                            <div id="wrapper_transp_otro" style="display: ${ci.evaluacion?.transp_check_otro ? 'block' : 'none'};">
                                <b style="color:#1e3a8a;">Especificar otro medio:</b>
                                <input type="text" id="ev_transp_otro" value="${ci.evaluacion?.transp_otro || ''}" placeholder="Especificar..." onchange="actualizarEvaluacionCampo('transp_otro', this.value)">
                            </div>
                        </div>
                    </div>

                    <!-- HIJOS (SÍ / NO -> CUÁNTOS -> CAMPOS INDIVIDUALES) -->
                    <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #cbd5e1; margin-top:4px;">
                        <b>Hijos:</b>
                        <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                            <label style="cursor:pointer;"><input type="radio" name="ev_hijos_radio" value="no" ${!ci.evaluacion?.tiene_hijos || ci.evaluacion?.tiene_hijos==='no' ? 'checked' : ''} onchange="toggleHijosOp('no')"> No</label>
                            <label style="cursor:pointer;"><input type="radio" name="ev_hijos_radio" value="si" ${ci.evaluacion?.tiene_hijos==='si' ? 'checked' : ''} onchange="toggleHijosOp('si')"> Sí</label>
                        </div>

                        <div id="wrapper_hijos_detalle" style="display: ${ci.evaluacion?.tiene_hijos==='si' ? 'block' : 'none'}; margin-top:12px;">
                            <div style="margin-bottom:10px; max-width:220px;">
                                <b style="color:#1e3a8a;">¿Cuántos hijos tiene?</b>
                                <input type="number" min="1" max="10" id="ev_hijos_num" value="${ci.evaluacion?.hijos_num || 1}" onchange="renderizarCamposHijos(parseInt(this.value)||1)" oninput="renderizarCamposHijos(parseInt(this.value)||1)">
                            </div>
                            <div id="contenedor_hijos_lista" style="display:flex; flex-direction:column; gap:8px;">
                                <!-- Se genera dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- III. PERFIL PROFESIONAL Y ESPECÍFICO -->
            <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
                <h4 style="margin:0 0 10px; color:#1e3a8a; font-size:14px; font-weight:bold;">III. PERFIL PROFESIONAL Y ESPECÍFICO</h4>
                <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                    <div><b>Universidad:</b><input type="text" id="ev_universidad" value="${ci.evaluacion?.universidad || ''}" onchange="actualizarEvaluacionCampo('universidad', this.value)"></div>
                    <div><b>Carrera:</b><input type="text" id="ev_carrera" value="${ci.evaluacion?.carrera || ''}" onchange="actualizarEvaluacionCampo('carrera', this.value)"></div>
                    <div><b>Horas requeridas:</b><input type="text" id="ev_horas_requeridas" value="${ci.evaluacion?.horas_requeridas || ''}" onchange="actualizarEvaluacionCampo('horas_requeridas', this.value)"></div>
                    <div><b>Días disponibles:</b><input type="text" id="ev_dias_disponibles" value="${ci.evaluacion?.dias_disponibles || ''}" placeholder="Ej: Lunes a Viernes" onchange="actualizarEvaluacionCampo('dias_disponibles', this.value)"></div>
                    <div><b>Horario tentativo:</b><input type="text" id="ev_horario_tentativo" value="${ci.evaluacion?.horario_tentativo || ''}" onchange="actualizarEvaluacionCampo('horario_tentativo', this.value)"></div>
                    <div><b>Horas por semana:</b><input type="text" id="ev_horas_por_semana" value="${ci.evaluacion?.horas_por_semana || ''}" onchange="actualizarEvaluacionCampo('horas_por_semana', this.value)"></div>
                    
                    <!-- SEGURO FACULTATIVO CONDICIONAL -->
                    <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #cbd5e1;">
                        <b>Seguro Facultativo:</b>
                        <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                            <label style="cursor:pointer;"><input type="radio" name="ev_seguro_radio" value="no" ${!ci.evaluacion?.seguro_facultativo || ci.evaluacion?.seguro_facultativo==='no' ? 'checked' : ''} onchange="toggleSeguroOp('no')"> No</label>
                            <label style="cursor:pointer;"><input type="radio" name="ev_seguro_radio" value="si" ${ci.evaluacion?.seguro_facultativo==='si' ? 'checked' : ''} onchange="toggleSeguroOp('si')"> Sí</label>
                        </div>
                        <div id="wrapper_seguro_cual" style="display: ${ci.evaluacion?.seguro_facultativo==='si' ? 'block' : 'none'}; margin-top:10px;">
                            <b style="color:#1e3a8a;">¿Cuál seguro tiene?</b>
                            <input type="text" id="ev_seguro_cual_txt" value="${ci.evaluacion?.seguro_cual || ''}" placeholder="Escribe cuál (ej. IMSS Facultativo N° XXXXX)" onchange="actualizarEvaluacionCampo('seguro_cual', this.value)">
                        </div>
                    </div>
                </div>

                <!-- ÁREA DE INTERÉS (SELECCIÓN ÚNICA - RADIO BUTTONS) -->
                <div style="margin-top:14px;">
                    <b>Área de Interés (Seleccionar 1 área única):</b>
                    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:6px; font-size:12px; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                        ${ ['Admin', 'Contaduría', 'Electricidad', 'Enfermería', 'IA', 'IT', 'Laboratorio', 'Mantenimiento', 'Marketing', 'Medicina', 'Radiología', 'Recursos Humanos'].map(area => `
                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer; font-weight:600;">
                                <input type="radio" name="ev_area_unica" value="${area}" onchange="actualizarEvaluacionAreaUnica('${area}')" ${ (ci.evaluacion?.area_unica === area || (ci.evaluacion?.areas || [])[0] === area) ? 'checked' : '' }> ${area}
                            </label>
                        `).join('') }
                    </div>
                </div>

                <hr style="margin:15px 0; border:0; border-top:1px solid #e2e8f0;">

                <!-- Servicio Social & Prácticas -->
                <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:15px;">
                    <div>
                        <h5 style="margin:0 0 6px; color:#1e3a8a; font-weight:bold;">Servicio Social:</h5>
                        <b>Lugar:</b><input type="text" id="ev_ss_lugar" value="${ci.evaluacion?.ss_lugar || ''}" onchange="actualizarEvaluacionCampo('ss_lugar', this.value)">
                        <div style="display:flex; gap:10px; margin-top:4px;">
                            <div><b>De:</b><input type="date" id="ev_ss_fecha_de" value="${ci.evaluacion?.ss_fecha_de || ''}" onchange="actualizarEvaluacionCampo('ss_fecha_de', this.value)"></div>
                            <div><b>A:</b><input type="date" id="ev_ss_fecha_a" value="${ci.evaluacion?.ss_fecha_a || ''}" onchange="actualizarEvaluacionCampo('ss_fecha_a', this.value)"></div>
                        </div>
                        <b style="margin-top:4px; display:block;">Actividades:</b><input type="text" id="ev_ss_actividades" value="${ci.evaluacion?.ss_actividades || ''}" onchange="actualizarEvaluacionCampo('ss_actividades', this.value)">
                    </div>

                    <div>
                        <h5 style="margin:0 0 6px; color:#1e3a8a; font-weight:bold;">Prácticas Profesionales:</h5>
                        <b>Lugar:</b><input type="text" id="ev_pp_lugar" value="${ci.evaluacion?.pp_lugar || ''}" onchange="actualizarEvaluacionCampo('pp_lugar', this.value)">
                        <div style="display:flex; gap:10px; margin-top:4px;">
                            <div><b>De:</b><input type="date" id="ev_pp_fecha_de" value="${ci.evaluacion?.pp_fecha_de || ''}" onchange="actualizarEvaluacionCampo('pp_fecha_de', this.value)"></div>
                            <div><b>A:</b><input type="date" id="ev_pp_fecha_a" value="${ci.evaluacion?.pp_fecha_a || ''}" onchange="actualizarEvaluacionCampo('pp_fecha_a', this.value)"></div>
                        </div>
                        <b style="margin-top:4px; display:block;">Actividades:</b><input type="text" id="ev_pp_actividades" value="${ci.evaluacion?.pp_actividades || ''}" onchange="actualizarEvaluacionCampo('pp_actividades', this.value)">
                    </div>
                </div>
            </div>

            <!-- IV. EXPERIENCIAS LABORALES -->
            <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
                <h4 style="margin:0 0 10px; color:#1e3a8a; font-size:14px; font-weight:bold;">IV. EXPERIENCIAS LABORALES</h4>
                <textarea id="ev_exp_laboral" rows="3" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:8px;" placeholder="Detalla empleos o proyectos anteriores..." onchange="actualizarEvaluacionCampo('exp_laboral', this.value)">${ci.evaluacion?.exp_laboral || ''}</textarea>
            </div>

            <!-- V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS CON OBSERVACIONES ANCHAS -->
            <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1;">
                <h4 style="margin:0 0 10px; color:#1e3a8a; font-size:14px; font-weight:bold;">V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS</h4>
                <table class="rh-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="padding:10px; width:110px;">Prueba</th>
                            <th style="padding:10px; width:130px;">Tiempo</th>
                            <th style="padding:10px;">Observaciones detalladas de la prueba</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${ ['Cleaver', 'Zavic', 'Moss', 'Terman', 'Lüscher', 'Beta', 'Machover', '16PF', 'Dominó', 'HTP'].map(prueba => {
                            let pKey = 'ev_' + prueba.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                            let pData = ci.evaluacion?.psicometricas?.[pKey] || {};
                            return '<tr>'+
                                '<td style="font-weight:bold; padding:10px; vertical-align:middle;">'+prueba+'</td>'+
                                '<td style="padding:8px;"><input type="text" value="'+(pData.tiempo || '')+'" placeholder="Ej: 15 min" onchange="actualizarEvaluacionPsicometrica(\''+pKey+'\', \'tiempo\', this.value)"></td>'+
                                '<td style="padding:8px;"><input type="text" style="width:100%; box-sizing:border-box;" value="'+(pData.obs || '')+'" placeholder="Observaciones de '+prueba+'..." onchange="actualizarEvaluacionPsicometrica(\''+pKey+'\', \'obs\', this.value)"></td>'+
                            '</tr>';
                        }).join('') }
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- VISTA 2B: FORMULARIO ENFERMERÍA -->
<!-- ========================================================= -->
<div id="bloque_form_enfermero" style="display: ${ ci.evaluacion && ci.evaluacion.tipo==='Enfermero' ? 'block' : 'none' }; background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #cbd5e1; margin-top:15px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #0f766e; padding-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px; color:#0f766e; font-weight:bold; text-transform:uppercase;">FICHA TÉCNICA</h2>
            <span style="font-size:14px; font-style:italic; color:#475569;">Área de la Salud — ENFERMERÍA</span>
        </div>
        <div>
            <button class="btn-ver" onclick="imprimirFichaEnfermeriaPDF()" style="background:#0f766e; font-weight:bold;">
                <i class="bi bi-printer me-1"></i> Imprimir / Descargar PDF
            </button>
        </div>
    </div>

    <!-- I. DATOS DE CONTROL Y ENTREVISTA -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#0f766e; font-size:14px; font-weight:bold;">I. DATOS DE CONTROL Y ENTREVISTA</h4>
        <div class="empleado-grid" style="grid-template-columns: repeat(4, 1fr); gap:10px;">
            <div><b>Candidato para:</b><input type="text" id="enf_candidato_para" value="${ci.evaluacion?.enf_candidato_para || ci.puesto || ''}" onchange="actualizarEvaluacionCampo('enf_candidato_para', this.value)"></div>
            <div><b>Por:</b><input type="text" id="enf_por" value="${ci.evaluacion?.enf_por || ci.entrevistador_rh || ''}" onchange="actualizarEvaluacionCampo('enf_por', this.value)"></div>
            <div><b>Fecha:</b><input type="date" id="enf_fecha" value="${ci.evaluacion?.enf_fecha || ci.fecha || ''}" onchange="actualizarEvaluacionCampo('enf_fecha', this.value)"></div>
            <div><b>Sueldo propuesto:</b><input type="text" id="enf_sueldo_propuesto" value="${ci.evaluacion?.enf_sueldo_propuesto || ''}" placeholder="$" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" onchange="actualizarEvaluacionCampo('enf_sueldo_propuesto', this.value)"></div>
            <div><b>Días:</b><input type="text" id="enf_dias" value="${ci.evaluacion?.enf_dias || ''}" placeholder="Lun-Vie" onchange="actualizarEvaluacionCampo('enf_dias', this.value)"></div>
            <div><b>Horario:</b><input type="text" id="enf_horario" value="${ci.evaluacion?.enf_horario || ''}" placeholder="Ej: 8:00-14:00" onchange="actualizarEvaluacionCampo('enf_horario', this.value)"></div>
            <div><b>Disponibilidad:</b><input type="text" id="enf_disponibilidad" value="${ci.evaluacion?.enf_disponibilidad || ''}" placeholder="Inmediata" onchange="actualizarEvaluacionCampo('enf_disponibilidad', this.value)"></div>
        </div>
    </div>

    <!-- II. DATOS PERSONALES Y FAMILIARES -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#0f766e; font-size:14px; font-weight:bold;">II. DATOS PERSONALES Y FAMILIARES</h4>
        <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:12px;">
            <div><b>Edad:</b><input type="text" id="enf_edad" value="${ci.evaluacion?.enf_edad || ''}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" onchange="actualizarEvaluacionCampo('enf_edad', this.value)"></div>
            <div><b>A qué se dedica papá:</b><input type="text" id="enf_papa_dedica" value="${ci.evaluacion?.enf_papa_dedica || ''}" onchange="actualizarEvaluacionCampo('enf_papa_dedica', this.value)"></div>
            <div><b>Vive en:</b><input type="text" id="enf_vive_en" value="${ci.evaluacion?.enf_vive_en || ''}" placeholder="Colonia / Zona" onchange="actualizarEvaluacionCampo('enf_vive_en', this.value)"></div>
            <div><b>A qué se dedica mamá:</b><input type="text" id="enf_mama_dedica" value="${ci.evaluacion?.enf_mama_dedica || ''}" onchange="actualizarEvaluacionCampo('enf_mama_dedica', this.value)"></div>
            <div><b>Vive con:</b><input type="text" id="enf_vive_con" value="${ci.evaluacion?.enf_vive_con || ''}" placeholder="Padres, pareja, solo..." onchange="actualizarEvaluacionCampo('enf_vive_con', this.value)"></div>
            <div><b>Hermanos (A qué se dedican):</b><input type="text" id="enf_hermanos_dedican" value="${ci.evaluacion?.enf_hermanos_dedican || ''}" onchange="actualizarEvaluacionCampo('enf_hermanos_dedican', this.value)"></div>
            <div>
                <b>Estado Civil:</b>
                <select id="enf_estado_civil" onchange="actualizarEvaluacionCampo('enf_estado_civil', this.value)">
                    <option value="Soltero(a)" ${ci.evaluacion?.enf_estado_civil==='Soltero(a)'?'selected':''}>Soltero(a)</option>
                    <option value="Casado(a)" ${ci.evaluacion?.enf_estado_civil==='Casado(a)'?'selected':''}>Casado(a)</option>
                    <option value="Unión Libre" ${ci.evaluacion?.enf_estado_civil==='Unión Libre'?'selected':''}>Unión Libre</option>
                    <option value="Otro" ${ci.evaluacion?.enf_estado_civil==='Otro'?'selected':''}>Otro</option>
                </select>
            </div>
            <div></div>

            <!-- MEDIO DE TRANSPORTE -->
            <div style="grid-column: span 2; background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1;">
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; font-size:13px;">
                    <b style="color:#0f766e;">Medio de transporte:</b>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="enf_transp_auto" ${ci.evaluacion?.enf_transp_auto ? 'checked' : ''} onchange="actualizarEvaluacionCheck('enf_transp_auto', this.checked)"> Auto propio</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="enf_transp_uber" ${ci.evaluacion?.enf_transp_uber ? 'checked' : ''} onchange="actualizarEvaluacionCheck('enf_transp_uber', this.checked)"> Uber/Didi</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="enf_transp_publico" ${ci.evaluacion?.enf_transp_publico ? 'checked' : ''} onchange="actualizarEvaluacionCheck('enf_transp_publico', this.checked)"> Transp. Público</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="enf_transp_mueven_chk" ${ci.evaluacion?.enf_transp_mueven_chk ? 'checked' : ''} onchange="toggleEnfTranspMueven(this.checked)"> Lo mueven</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="enf_transp_otro_chk" ${ci.evaluacion?.enf_transp_otro_chk ? 'checked' : ''} onchange="toggleEnfTranspOtro(this.checked)"> Otro</label>
                    <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                        <b style="color:#0f766e; white-space:nowrap;">Tiempo para llegar:</b>
                        <input type="text" id="enf_tiempo_llegar" value="${ci.evaluacion?.enf_tiempo_llegar || ''}" placeholder="Ej: 30 min" style="width:130px; padding:5px 8px;" onchange="actualizarEvaluacionCampo('enf_tiempo_llegar', this.value)">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                    <div id="enf_wrapper_mueven" style="display:${ci.evaluacion?.enf_transp_mueven_chk ? 'block' : 'none'};">
                        <b style="color:#0f766e;">¿Quién lo mueve?</b>
                        <input type="text" id="enf_transp_mueven_quien" value="${ci.evaluacion?.enf_transp_mueven_quien || ''}" placeholder="Escribe quién..." onchange="actualizarEvaluacionCampo('enf_transp_mueven_quien', this.value)">
                    </div>
                    <div id="enf_wrapper_otro" style="display:${ci.evaluacion?.enf_transp_otro_chk ? 'block' : 'none'};">
                        <b style="color:#0f766e;">Especificar otro:</b>
                        <input type="text" id="enf_transp_otro_txt" value="${ci.evaluacion?.enf_transp_otro_txt || ''}" placeholder="Especificar..." onchange="actualizarEvaluacionCampo('enf_transp_otro_txt', this.value)">
                    </div>
                </div>
            </div>

            <!-- HIJOS -->
            <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #cbd5e1;">
                <b>Hijos:</b>
                <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="enf_hijos_radio" value="no" ${!ci.evaluacion?.enf_tiene_hijos || ci.evaluacion?.enf_tiene_hijos==='no' ? 'checked' : ''} onchange="toggleEnfHijos('no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_hijos_radio" value="si" ${ci.evaluacion?.enf_tiene_hijos==='si' ? 'checked' : ''} onchange="toggleEnfHijos('si')"> Sí</label>
                </div>
                <div id="enf_wrapper_hijos" style="display:${ci.evaluacion?.enf_tiene_hijos==='si' ? 'block' : 'none'}; margin-top:10px;">
                    <div style="max-width:220px; margin-bottom:10px;">
                        <b style="color:#0f766e;">¿Cuántos hijos?</b>
                        <input type="number" min="1" max="10" id="enf_hijos_num" value="${ci.evaluacion?.enf_hijos_num || 1}" onchange="renderizarCamposHijosEnf(parseInt(this.value)||1)" oninput="renderizarCamposHijosEnf(parseInt(this.value)||1)">
                    </div>
                    <div id="enf_hijos_lista" style="display:flex; flex-direction:column; gap:8px;"></div>
                </div>
            </div>

            <!-- SUELDO Y MOTIVO -->
            <div><b>Sueldo último trabajo:</b><input type="text" id="enf_sueldo_ultimo" value="${ci.evaluacion?.enf_sueldo_ultimo || ''}" placeholder="$" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" onchange="actualizarEvaluacionCampo('enf_sueldo_ultimo', this.value)"></div>
            <div><b>Motivo de salida:</b><input type="text" id="enf_motivo_salida" value="${ci.evaluacion?.enf_motivo_salida || ''}" onchange="actualizarEvaluacionCampo('enf_motivo_salida', this.value)"></div>
        </div>
    </div>

    <!-- III. PERFIL PROFESIONAL Y ESPECÍFICO -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#0f766e; font-size:14px; font-weight:bold;">III. PERFIL PROFESIONAL Y ESPECÍFICO</h4>
        <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:12px;">

            <!-- Título -->
            <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Título:</b>
                <div style="display:flex; gap:16px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="enf_titulo_radio" value="si" ${ci.evaluacion?.enf_titulo==='si'?'checked':''} onchange="actualizarEvaluacionCampo('enf_titulo', 'si')"> Sí</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_titulo_radio" value="no" ${ci.evaluacion?.enf_titulo==='no'?'checked':''} onchange="actualizarEvaluacionCampo('enf_titulo', 'no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_titulo_radio" value="tramite" ${ci.evaluacion?.enf_titulo==='tramite'?'checked':''} onchange="actualizarEvaluacionCampo('enf_titulo', 'tramite')"> En trámite</label>
                </div>
            </div>

            <!-- Cédula -->
            <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Cédula:</b>
                <div style="display:flex; gap:16px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="enf_cedula_radio" value="si" ${ci.evaluacion?.enf_cedula==='si'?'checked':''} onchange="actualizarEvaluacionCampo('enf_cedula', 'si')"> Sí</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_cedula_radio" value="no" ${ci.evaluacion?.enf_cedula==='no'?'checked':''} onchange="actualizarEvaluacionCampo('enf_cedula', 'no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_cedula_radio" value="tramite" ${ci.evaluacion?.enf_cedula==='tramite'?'checked':''} onchange="actualizarEvaluacionCampo('enf_cedula', 'tramite')"> En trámite</label>
                </div>
            </div>

            <div><b>Universidad:</b><input type="text" id="enf_universidad" value="${ci.evaluacion?.enf_universidad || ''}" onchange="actualizarEvaluacionCampo('enf_universidad', this.value)"></div>
            <div><b>Fecha de llegada aprox. (si aplica):</b><input type="date" id="enf_fecha_llegada" value="${ci.evaluacion?.enf_fecha_llegada || ''}" onchange="actualizarEvaluacionCampo('enf_fecha_llegada', this.value)"></div>
            <div><b>Terminó estudios el:</b><input type="text" id="enf_termino_estudios" value="${ci.evaluacion?.enf_termino_estudios || ''}" placeholder="Mes / Año" onchange="actualizarEvaluacionCampo('enf_termino_estudios', this.value)"></div>

            <!-- Disponibilidad de banca -->
            <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Disponibilidad de banca:</b>
                <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="enf_banca_radio" value="si" ${ci.evaluacion?.enf_banca==='si'?'checked':''} onchange="actualizarEvaluacionCampo('enf_banca', 'si')"> Sí</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_banca_radio" value="no" ${ci.evaluacion?.enf_banca==='no'?'checked':''} onchange="actualizarEvaluacionCampo('enf_banca', 'no')"> No</label>
                </div>
            </div>

            <!-- Zonas de disponibilidad -->
            <div style="grid-column: span 2; background:#f8fafc; padding:10px 16px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Zonas de disponibilidad:</b>
                <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="enf_zona_radio" value="TRC" ${ci.evaluacion?.enf_zona==='TRC'?'checked':''} onchange="actualizarEvaluacionCampo('enf_zona', 'TRC')"> TRC</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_zona_radio" value="Gómez" ${ci.evaluacion?.enf_zona==='Gómez'?'checked':''} onchange="actualizarEvaluacionCampo('enf_zona', 'Gómez')"> Gómez</label>
                    <label style="cursor:pointer;"><input type="radio" name="enf_zona_radio" value="Todas" ${ci.evaluacion?.enf_zona==='Todas'?'checked':''} onchange="actualizarEvaluacionCampo('enf_zona', 'Todas')"> Todas</label>
                </div>
            </div>
        </div>
    </div>

    <!-- IV. OBSERVACIONES EXTRA Y EXPERIENCIAS LABORALES -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 12px; color:#0f766e; font-size:14px; font-weight:bold;">IV. OBSERVACIONES EXTRA</h4>
        <div style="margin-bottom:14px;">
            <b style="font-size:13px; color:#334155;">Observaciones Extra:</b>
            <textarea id="enf_observaciones_extra" rows="4" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:8px; margin-top:6px; resize:vertical; box-sizing:border-box;" placeholder="Observaciones generales sobre la entrevista..." onchange="actualizarEvaluacionCampo('enf_observaciones_extra', this.value)">${ci.evaluacion?.enf_observaciones_extra || ''}</textarea>
        </div>
        <div>
            <b style="font-size:13px; color:#334155;">Experiencias Laborales:</b>
            <textarea id="enf_exp_laboral" rows="4" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:8px; margin-top:6px; resize:vertical; box-sizing:border-box;" placeholder="Detalla empleos anteriores (lugar, puesto, tiempo, motivo de salida)..." onchange="actualizarEvaluacionCampo('enf_exp_laboral', this.value)">${ci.evaluacion?.enf_exp_laboral || ''}</textarea>
        </div>
    </div>

    <!-- V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1;">
        <h4 style="margin:0 0 10px; color:#0f766e; font-size:14px; font-weight:bold;">V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS</h4>
        <table class="rh-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="padding:10px; width:110px;">Prueba</th>
                    <th style="padding:10px; width:130px;">Tiempo</th>
                    <th style="padding:10px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                ${ ['DFH', 'PBL', 'Familia', 'Árbol', 'Casa'].map(prueba => {
                    let pKey = 'enf_' + prueba.toLowerCase().normalize("NFD").replace(/[\\u0300-\\u036f]/g, "");
                    let pData = ci.evaluacion?.enf_psicometricas?.[pKey] || {};
                    return '<tr>'+
                        '<td style="font-weight:bold; padding:10px; vertical-align:middle;">'+prueba+'</td>'+
                        '<td style="padding:8px;"><input type="text" value="'+(pData.tiempo || '')+'" placeholder="Ej: 15 min" onchange="actualizarEvaluacionPsicometricaEnf(\\\''+pKey+'\\\', \\\'tiempo\\\', this.value)"></td>'+
                        '<td style="padding:8px;"><input type="text" style="width:100%; box-sizing:border-box;" value="'+(pData.obs || '')+'" placeholder="Observaciones de '+prueba+'..." onchange="actualizarEvaluacionPsicometricaEnf(\\\''+pKey+'\\\', \\\'obs\\\', this.value)"></td>'+
                    '</tr>';
                }).join('') }
            </tbody>
        </table>
    </div>
</div>
   
<!-- ========================================================= -->
<!-- VISTA 2C: FORMULARIO MÉDICO -->
<!-- ========================================================= -->
<div id="bloque_form_medico" style="display: ${ ci.evaluacion && ci.evaluacion.tipo==='Medico' ? 'block' : 'none' }; background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #cbd5e1; margin-top:15px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #4338ca; padding-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px; color:#4338ca; font-weight:bold; text-transform:uppercase;">FICHA TÉCNICA</h2>
            <span style="font-size:14px; font-style:italic; color:#475569;">Área de la Salud — MÉDICO</span>
        </div>
        <div>
            <button class="btn-ver" onclick="alert('Impresión de Médico en desarrollo')" style="background:#4338ca; font-weight:bold;">
                <i class="bi bi-printer me-1"></i> Imprimir / Descargar PDF
            </button>
        </div>
    </div>

    <!-- I. DATOS DE CONTROL Y ENTREVISTA -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#4338ca; font-size:14px; font-weight:bold;">I. DATOS DE CONTROL Y ENTREVISTA</h4>
        <div class="empleado-grid" style="grid-template-columns: repeat(4, 1fr); gap:10px;">
            <div><b>Candidato para:</b><input type="text" id="med_candidato_para" value="${ci.evaluacion?.med_candidato_para || ci.puesto || ''}" onchange="actualizarEvaluacionCampo('med_candidato_para', this.value)"></div>
            <div><b>Por:</b><input type="text" id="med_por" value="${ci.evaluacion?.med_por || ci.entrevistador_rh || ''}" onchange="actualizarEvaluacionCampo('med_por', this.value)"></div>
            <div><b>Fecha:</b><input type="date" id="med_fecha" value="${ci.evaluacion?.med_fecha || ci.fecha || ''}" onchange="actualizarEvaluacionCampo('med_fecha', this.value)"></div>
            <div><b>Sueldo propuesto:</b><input type="text" id="med_sueldo_propuesto" value="${ci.evaluacion?.med_sueldo_propuesto || ''}" placeholder="$" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" onchange="actualizarEvaluacionCampo('med_sueldo_propuesto', this.value)"></div>
            <div><b>Días:</b><input type="text" id="med_dias" value="${ci.evaluacion?.med_dias || ''}" placeholder="Ej: Lun-Vie" onchange="actualizarEvaluacionCampo('med_dias', this.value)"></div>
            <div><b>Horario:</b><input type="text" id="med_horario" value="${ci.evaluacion?.med_horario || ''}" placeholder="Ej: 8:00-14:00" onchange="actualizarEvaluacionCampo('med_horario', this.value)"></div>
            <div><b>Disponibilidad:</b><input type="text" id="med_disponibilidad" value="${ci.evaluacion?.med_disponibilidad || ''}" placeholder="Ej: Inmediata" onchange="actualizarEvaluacionCampo('med_disponibilidad', this.value)"></div>
        </div>
    </div>

    <!-- II. DATOS PERSONALES Y FAMILIARES -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#4338ca; font-size:14px; font-weight:bold;">II. DATOS PERSONALES Y FAMILIARES</h4>
        <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:12px;">
            <div><b>Edad:</b><input type="text" id="med_edad" value="${ci.evaluacion?.med_edad || ''}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" onchange="actualizarEvaluacionCampo('med_edad', this.value)"></div>
            <div><b>A qué se dedica papá:</b><input type="text" id="med_papa_dedica" value="${ci.evaluacion?.med_papa_dedica || ''}" onchange="actualizarEvaluacionCampo('med_papa_dedica', this.value)"></div>
            <div><b>Vive en:</b><input type="text" id="med_vive_en" value="${ci.evaluacion?.med_vive_en || ''}" placeholder="Colonia / Zona" onchange="actualizarEvaluacionCampo('med_vive_en', this.value)"></div>
            <div><b>A qué se dedica mamá:</b><input type="text" id="med_mama_dedica" value="${ci.evaluacion?.med_mama_dedica || ''}" onchange="actualizarEvaluacionCampo('med_mama_dedica', this.value)"></div>
            <div><b>Vive con:</b><input type="text" id="med_vive_con" value="${ci.evaluacion?.med_vive_con || ''}" placeholder="Padres, pareja, solo..." onchange="actualizarEvaluacionCampo('med_vive_con', this.value)"></div>
            <div><b>Hermanos (A qué se dedican):</b><input type="text" id="med_hermanos_dedican" value="${ci.evaluacion?.med_hermanos_dedican || ''}" onchange="actualizarEvaluacionCampo('med_hermanos_dedican', this.value)"></div>
            <div>
                <b>Estado Civil:</b>
                <select id="med_estado_civil" onchange="actualizarEvaluacionCampo('med_estado_civil', this.value)">
                    <option value="Soltero(a)" ${ci.evaluacion?.med_estado_civil==='Soltero(a)'?'selected':''}>Soltero(a)</option>
                    <option value="Casado(a)" ${ci.evaluacion?.med_estado_civil==='Casado(a)'?'selected':''}>Casado(a)</option>
                    <option value="Unión Libre" ${ci.evaluacion?.med_estado_civil==='Unión Libre'?'selected':''}>Unión Libre</option>
                    <option value="Otro" ${ci.evaluacion?.med_estado_civil==='Otro'?'selected':''}>Otro</option>
                </select>
            </div>
            <div></div>

            <!-- MEDIO DE TRANSPORTE -->
            <div style="grid-column: span 2; background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1;">
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; font-size:13px;">
                    <b style="color:#4338ca;">Medio de transporte:</b>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="med_transp_auto" ${ci.evaluacion?.med_transp_auto ? 'checked' : ''} onchange="actualizarEvaluacionCheck('med_transp_auto', this.checked)"> Auto propio</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="med_transp_uber" ${ci.evaluacion?.med_transp_uber ? 'checked' : ''} onchange="actualizarEvaluacionCheck('med_transp_uber', this.checked)"> Uber/Didi</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="med_transp_publico" ${ci.evaluacion?.med_transp_publico ? 'checked' : ''} onchange="actualizarEvaluacionCheck('med_transp_publico', this.checked)"> Transp. Público</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="med_transp_mueven_chk" ${ci.evaluacion?.med_transp_mueven_chk ? 'checked' : ''} onchange="toggleMedTranspMueven(this.checked)"> Lo mueven</label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="med_transp_otro_chk" ${ci.evaluacion?.med_transp_otro_chk ? 'checked' : ''} onchange="toggleMedTranspOtro(this.checked)"> Otro</label>
                    
                    <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                        <b style="color:#4338ca; white-space:nowrap;">Tiempo para llegar:</b>
                        <input type="text" id="med_tiempo_llegar" value="${ci.evaluacion?.med_tiempo_llegar || ''}" placeholder="Ej: 30 min" style="width:130px; padding:5px 8px;" onchange="actualizarEvaluacionCampo('med_tiempo_llegar', this.value)">
                    </div>
                </div>
                <!-- Campos dinámicos de transporte -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                    <div id="med_wrapper_mueven" style="display:${ci.evaluacion?.med_transp_mueven_chk ? 'block' : 'none'};">
                        <b style="color:#4338ca;">¿Quién lo mueve?</b>
                        <input type="text" id="med_transp_mueven_quien" value="${ci.evaluacion?.med_transp_mueven_quien || ''}" placeholder="Escribe quién..." onchange="actualizarEvaluacionCampo('med_transp_mueven_quien', this.value)">
                    </div>
                    <div id="med_wrapper_otro" style="display:${ci.evaluacion?.med_transp_otro_chk ? 'block' : 'none'};">
                        <b style="color:#4338ca;">Especificar otro:</b>
                        <input type="text" id="med_transp_otro_txt" value="${ci.evaluacion?.med_transp_otro_txt || ''}" placeholder="Especificar..." onchange="actualizarEvaluacionCampo('med_transp_otro_txt', this.value)">
                    </div>
                </div>
            </div>

            <!-- HIJOS -->
            <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #cbd5e1;">
                <b>Hijos:</b>
                <div style="display:flex; gap:20px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="med_hijos_radio" value="no" ${!ci.evaluacion?.med_tiene_hijos || ci.evaluacion?.med_tiene_hijos==='no' ? 'checked' : ''} onchange="toggleMedHijos('no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="med_hijos_radio" value="si" ${ci.evaluacion?.med_tiene_hijos==='si' ? 'checked' : ''} onchange="toggleMedHijos('si')"> Sí</label>
                </div>
                <div id="med_wrapper_hijos" style="display:${ci.evaluacion?.med_tiene_hijos==='si' ? 'block' : 'none'}; margin-top:10px;">
                    <div style="max-width:220px; margin-bottom:10px;">
                        <b style="color:#4338ca;">¿Cuántos hijos?</b>
                        <input type="number" min="1" max="10" id="med_hijos_num" value="${ci.evaluacion?.med_hijos_num || 1}" onchange="renderizarCamposHijosMed(parseInt(this.value)||1)" oninput="renderizarCamposHijosMed(parseInt(this.value)||1)">
                    </div>
                    <div id="med_hijos_lista" style="display:flex; flex-direction:column; gap:8px;"></div>
                </div>
            </div>

            <!-- SUELDOS Y MOTIVO -->
            <div><b>Sueldo esperado:</b><input type="text" id="med_sueldo_esperado" value="${ci.evaluacion?.med_sueldo_esperado || ''}" placeholder="$" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" onchange="actualizarEvaluacionCampo('med_sueldo_esperado', this.value)"></div>
            <div><b>Sueldo último trabajo:</b><input type="text" id="med_sueldo_ultimo" value="${ci.evaluacion?.med_sueldo_ultimo || ''}" placeholder="$" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" onchange="actualizarEvaluacionCampo('med_sueldo_ultimo', this.value)"></div>
            <div style="grid-column: span 2;"><b>Motivo de salida:</b><input type="text" id="med_motivo_salida" value="${ci.evaluacion?.med_motivo_salida || ''}" onchange="actualizarEvaluacionCampo('med_motivo_salida', this.value)"></div>
        </div>
    </div>

    <!-- III. PERFIL PROFESIONAL Y ESPECÍFICO -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#4338ca; font-size:14px; font-weight:bold;">III. PERFIL PROFESIONAL Y ESPECÍFICO</h4>
        <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:12px;">

            <!-- Título y Cédula -->
            <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Título:</b>
                <div style="display:flex; gap:16px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="med_titulo_radio" value="si" ${ci.evaluacion?.med_titulo==='si'?'checked':''} onchange="actualizarEvaluacionCampo('med_titulo', 'si')"> Sí</label>
                    <label style="cursor:pointer;"><input type="radio" name="med_titulo_radio" value="no" ${ci.evaluacion?.med_titulo==='no'?'checked':''} onchange="actualizarEvaluacionCampo('med_titulo', 'no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="med_titulo_radio" value="tramite" ${ci.evaluacion?.med_titulo==='tramite'?'checked':''} onchange="actualizarEvaluacionCampo('med_titulo', 'tramite')"> En trámite</label>
                </div>
            </div>

            <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
                <b>Cédula:</b>
                <div style="display:flex; gap:16px; margin-top:6px; font-size:13px;">
                    <label style="cursor:pointer;"><input type="radio" name="med_cedula_radio" value="si" ${ci.evaluacion?.med_cedula==='si'?'checked':''} onchange="actualizarEvaluacionCampo('med_cedula', 'si')"> Sí</label>
                    <label style="cursor:pointer;"><input type="radio" name="med_cedula_radio" value="no" ${ci.evaluacion?.med_cedula==='no'?'checked':''} onchange="actualizarEvaluacionCampo('med_cedula', 'no')"> No</label>
                    <label style="cursor:pointer;"><input type="radio" name="med_cedula_radio" value="tramite" ${ci.evaluacion?.med_cedula==='tramite'?'checked':''} onchange="actualizarEvaluacionCampo('med_cedula', 'tramite')"> En trámite</label>
                </div>
            </div>

            <div><b>Universidad:</b><input type="text" id="med_universidad" value="${ci.evaluacion?.med_universidad || ''}" onchange="actualizarEvaluacionCampo('med_universidad', this.value)"></div>
            <div><b>Fecha de llegada aprox. (si aplica):</b><input type="date" id="med_fecha_llegada" value="${ci.evaluacion?.med_fecha_llegada || ''}" onchange="actualizarEvaluacionCampo('med_fecha_llegada', this.value)"></div>
            <div style="grid-column: span 2;"><b>Terminó estudios el:</b><input type="text" id="med_termino_estudios" value="${ci.evaluacion?.med_termino_estudios || ''}" placeholder="Mes / Año" onchange="actualizarEvaluacionCampo('med_termino_estudios', this.value)"></div>

            <hr style="grid-column: span 2; margin:10px 0; border:0; border-top:1px solid #e2e8f0;">

            <!-- Internado y Servicio Social -->
            <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                <h5 style="margin:0 0 8px; color:#4338ca; font-weight:bold;">Internado:</h5>
                <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                    <div><b>Lugar:</b><input type="text" value="${ci.evaluacion?.med_internado_lugar || ''}" onchange="actualizarEvaluacionCampo('med_internado_lugar', this.value)"></div>
                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;"><b>De:</b><input type="date" value="${ci.evaluacion?.med_internado_de || ''}" onchange="actualizarEvaluacionCampo('med_internado_de', this.value)"></div>
                        <div style="flex:1;"><b>A:</b><input type="date" value="${ci.evaluacion?.med_internado_a || ''}" onchange="actualizarEvaluacionCampo('med_internado_a', this.value)"></div>
                    </div>
                    <div style="grid-column: span 2;"><b>Actividades:</b><input type="text" value="${ci.evaluacion?.med_internado_actividades || ''}" onchange="actualizarEvaluacionCampo('med_internado_actividades', this.value)"></div>
                </div>
            </div>

            <div style="grid-column: span 2; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                <h5 style="margin:0 0 8px; color:#4338ca; font-weight:bold;">Servicio Social:</h5>
                <div class="empleado-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                    <div><b>Lugar:</b><input type="text" value="${ci.evaluacion?.med_ss_lugar || ''}" onchange="actualizarEvaluacionCampo('med_ss_lugar', this.value)"></div>
                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;"><b>De:</b><input type="date" value="${ci.evaluacion?.med_ss_de || ''}" onchange="actualizarEvaluacionCampo('med_ss_de', this.value)"></div>
                        <div style="flex:1;"><b>A:</b><input type="date" value="${ci.evaluacion?.med_ss_a || ''}" onchange="actualizarEvaluacionCampo('med_ss_a', this.value)"></div>
                    </div>
                    <div style="grid-column: span 2;"><b>Actividades:</b><input type="text" value="${ci.evaluacion?.med_ss_actividades || ''}" onchange="actualizarEvaluacionCampo('med_ss_actividades', this.value)"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- IV. EXPERIENCIAS LABORALES -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:15px;">
        <h4 style="margin:0 0 10px; color:#4338ca; font-size:14px; font-weight:bold;">IV. EXPERIENCIAS LABORALES</h4>
        <textarea id="med_exp_laboral" rows="4" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:8px; resize:vertical; box-sizing:border-box;" placeholder="Detalla empleos anteriores..." onchange="actualizarEvaluacionCampo('med_exp_laboral', this.value)">${ci.evaluacion?.med_exp_laboral || ''}</textarea>
    </div>

    <!-- V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS -->
    <div style="background:#fff; padding:15px; border-radius:8px; border:1px solid #cbd5e1;">
        <h4 style="margin:0 0 10px; color:#4338ca; font-size:14px; font-weight:bold;">V. RESULTADOS DE PRUEBAS PSICOMÉTRICAS</h4>
        <table class="rh-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="padding:10px; width:110px;">Prueba</th>
                    <th style="padding:10px; width:130px;">Tiempo</th>
                    <th style="padding:10px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                ${ ['DFH', 'PBL', 'Familia', 'Árbol', 'Casa'].map(prueba => {
                    let pKey = 'med_' + prueba.toLowerCase().normalize("NFD").replace(/[\\u0300-\\u036f]/g, "");
                    let pData = ci.evaluacion?.med_psicometricas?.[pKey] || {};
                    return '<tr>'+
                        '<td style="font-weight:bold; padding:10px; vertical-align:middle;">'+prueba+'</td>'+
                        '<td style="padding:8px;"><input type="text" value="'+(pData.tiempo || '')+'" placeholder="Ej: 15 min" onchange="actualizarEvaluacionPsicometricaMed(\\\''+pKey+'\\\', \\\'tiempo\\\', this.value)"></td>'+
                        '<td style="padding:8px;"><input type="text" style="width:100%; box-sizing:border-box;" value="'+(pData.obs || '')+'" placeholder="Observaciones de '+prueba+'..." onchange="actualizarEvaluacionPsicometricaMed(\\\''+pKey+'\\\', \\\'obs\\\', this.value)"></td>'+
                    '</tr>';
                }).join('') }
            </tbody>
        </table>
    </div>
</div>



</div>
</div>

<!-- ========================================================= -->
<!-- VISTA 2: EXPEDIENTE (Documentos y Notas) -->
<!-- ========================================================= -->
<div id="vista_modo_expediente_cita" style="display: ${ (ci.modo_actual==='expediente') ? 'block' : 'none' };">
    <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:stretch;">
        <div class="col" style="flex: 1; min-width:300px; max-width:400px;">
            <div class="rh-card" style="height:100%; border-left:4px solid #16a34a;">
                <h3 style="margin:0 0 15px; color:#16a34a;"><i class="bi bi-folder2-open me-2"></i>Expediente</h3>
                
                <input type="file" id="fileUploadCita" style="display:none" onchange="subirArchivoCita(this)">
                
                <div style="display:flex; flex-direction:column; gap:8px;">
                    ${ [
                        'Ficha tecnica.pdf', 
                        'CV / Solicitud de Empleo', 
                        'Identificación Oficial (INE)', 
                        'Acta de Nacimiento', 
                        'CURP', 
                        'Constancia de Situación Fiscal (RFC)', 
                        'Comprobante de Domicilio', 
                        'Número de Seguridad Social (NSS)'
                      ].map(req => {
                        let docName = req === 'Ficha tecnica.pdf' ? `${ci.nombre || 'Candidato'} Ficha tecnica.pdf` : req;
                        let doc = (ci.documentos || []).find(d => d.nombre === docName);
                        
                        if(doc) {
                            return '<div style="display:flex; align-items:center; justify-content:space-between; background:#f0fdf4; padding:10px 14px; border:1px solid #bbf7d0; border-radius:8px; cursor:pointer; transition:0.2s;" onclick="verDocumento(\''+doc.url+'\', \''+doc.tipo+'\', \''+doc.nombre+'\')" onmouseover="this.style.borderColor=\'#22c55e\'" onmouseout="this.style.borderColor=\'#bbf7d0\'">'+
                                '<div style="display:flex; align-items:center; gap:8px; overflow:hidden;">'+
                                    '<i class="bi bi-check-circle-fill" style="font-size:18px; color:#22c55e;"></i>'+
                                    '<span style="font-weight:600; font-size:13px; color:#166534; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="'+docName+'">'+docName+'</span>'+
                                '</div>'+
                                '<i class="bi bi-eye" style="color:#22c55e;"></i>'+
                            '</div>';
                        } else {
                            let clickAction = req === 'Ficha tecnica.pdf' ? 'alert(\'La ficha técnica se genera automáticamente al guardar la evaluación.\')' : 'solicitarArchivoCita(\''+docName+'\')';
                            return '<div style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; padding:10px 14px; border:1px dashed #cbd5e1; border-radius:8px; cursor:pointer; transition:0.2s;" onclick="'+clickAction+'" onmouseover="this.style.borderColor=\'#94a3b8\'" onmouseout="this.style.borderColor=\'#cbd5e1\'">'+
                                '<div style="display:flex; align-items:center; gap:8px; overflow:hidden;">'+
                                    '<i class="bi bi-circle" style="font-size:18px; color:#94a3b8;"></i>'+
                                    '<span style="font-weight:500; font-size:13px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="'+docName+'">'+docName+'</span>'+
                                '</div>'+
                                '<i class="bi bi-upload" style="color:#94a3b8; font-size:12px;"></i>'+
                            '</div>';
                        }
                    }).join('') }
                </div>
            </div>
        </div>
        
        <!-- VISUALIZADOR DE DOCUMENTO -->
        <div class="col" style="flex: 2; min-width:400px; display:flex; flex-direction:column;">
            <div class="rh-card" style="height:100%; display:flex; flex-direction:column; padding:0; overflow:hidden; border:1px solid #cbd5e1;">
                <div style="background:#f1f5f9; padding:12px 16px; border-bottom:1px solid #cbd5e1; display:flex; justify-content:space-between; align-items:center;">
                    <h3 id="preview_title" style="margin:0; font-size:14px; color:#334155;"><i class="bi bi-file-earmark-text me-2"></i>Visor de Documento</h3>
                    <div id="preview_actions" style="display:none; gap:8px;">
                        <button class="btn-ver" style="background:#2563eb; padding:5px 12px; font-size:12px; font-weight:bold;" onclick="descargarVistaPrevia()"><i class="bi bi-download me-1"></i> Descargar</button>
                        <button class="btn-ver" style="background:#475569; padding:5px 12px; font-size:12px; font-weight:bold;" onclick="imprimirVistaPrevia()"><i class="bi bi-printer me-1"></i> Imprimir</button>
                    </div>
                </div>
                
                <div id="preview_container" style="flex-grow:1; min-height:500px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; position:relative;">
                    <div id="preview_empty" style="text-align:center; color:#94a3b8;">
                        <i class="bi bi-file-earmark-pdf" style="font-size:48px;"></i>
                        <p style="margin-top:10px; font-weight:500;">Selecciona un documento para visualizarlo</p>
                    </div>
                    <!-- El iframe/img se inyecta por JS aquí -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BARRA DE ACCIONES CON ESTILO MEJORADO -->
<div class="rh-card sticky-acciones" style="display: ${ (ci.modo_actual==='expediente') ? 'none' : 'block' }; margin-top:14px;">
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn-ver" style="background:#2563eb;" onclick="guardarCambiosFicha()" title="Guardar Cambios">
                <i class="bi bi-floppy-fill me-1"></i> Guardar
            </button>
            ${ !citas.find(c=>c.id==ci.id) ? '' : `
            <button class="btn-ver" style="background:#16a34a;" onclick="marcarRealizadaYConvertir()" title="Marcar como Realizada">
                <i class="bi bi-check-circle-fill me-1"></i> Realizada
            </button>
            <button class="btn-ver" style="background:#475569;" onclick="noSePresentoCita()" title="No se presentó">
                <i class="bi bi-person-x-fill me-1"></i> No se presentó
            </button>
            `}
            <button class="btn-ver" style="background:#ef4444;" onclick="eliminarRegistro('cita')" title="Eliminar Registro">
                <i class="bi bi-trash-fill"></i>
            </button>
        </div>
        <!-- Botón de convertir a candidato removido temporalmente por solicitud de la fase actual -->
    </div>
</div>
