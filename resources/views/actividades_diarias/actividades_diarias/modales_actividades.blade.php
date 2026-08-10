@php
    $currentUser = auth()->user();
    $isBossOrAdmin = $currentUser && in_array($currentUser->rol, ['jefe', 'admin', 'directivo']);

    if (!isset($empleadosRH)) {
        if ($currentUser && $currentUser->rol === 'jefe') {
            $empleadosRH = \App\Models\User::where('jefe_id', $currentUser->id)->orWhere('id', $currentUser->id)->get();
        } else {
            $empleadosRH = \App\Models\User::all();
        }
    }

    if (isset($area)) {
        $empleadosRH = $empleadosRH->filter(function($emp) use ($area) {
            return ($emp->area_id == $area->id) || ($emp->rol === 'jefe') || ($emp->id === auth()->id());
        });
    }
@endphp

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ============================================== -->
<!-- MODAL UNIFICADO: CREAR ACTIVIDAD -->
<!-- ============================================== -->
<div id="modalCrearActividad" class="rh-modal">
    <div class="rh-modal-content" id="modalCrearContent" style="max-width: 680px; background: #f0fdf4; border: 3px solid #15803d; box-shadow: 0 10px 25px rgba(0,0,0,0.25); transition: all 0.3s;">
        <span class="rh-modal-close" onclick="cerrarModal('modalCrearActividad')">&times;</span>
        
        <div style="margin-bottom: 20px;">
            <h2 style="margin: 0 0 8px 0; color: #166534; font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px;" id="crearModalHeaderTitle">
                <i class="bi bi-check2-circle" id="crearModalHeaderIcon" style="color: #15803d;"></i> Nueva Actividad Asignada
            </h2>
            <p style="margin: 0; font-size: 13px; color: #334155; font-weight: 600;" id="crearModalSubtitle">
                {{ $isBossOrAdmin ? 'Selecciona el tipo de actividad que asignarás a tu personal:' : 'Selecciona el tipo de actividad que registrarás en tu jornada:' }}
            </p>
        </div>

        <!-- Selector de Tipo de Actividad -->
        <div id="box_selector_tipo_actividad" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 22px;">
            <button type="button" id="btnTipoAsignada" onclick="seleccionarTipoCreacion('asignada')"
                    style="padding: 12px; border-radius: 10px; border: 3px solid #15803d; background: #dcfce7; color: #166534; font-weight: 800; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; font-size: 13px; transition: all 0.2s;">
                <span><i class="bi bi-check2-circle me-1"></i> Asignada</span>
                <span style="font-size: 10px; font-weight: 600; opacity: 0.85;">{{ $isBossOrAdmin ? 'Asignar a un empleado' : 'Tarea individual' }}</span>
            </button>
            <button type="button" id="btnTipoImprevista" onclick="seleccionarTipoCreacion('imprevista')"
                    style="padding: 12px; border-radius: 10px; border: 2px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 800; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; font-size: 13px; transition: all 0.2s;">
                <span><i class="bi bi-person-fill me-1"></i> Personal</span>
                <span style="font-size: 10px; font-weight: 600; opacity: 0.85;">Actividad propia</span>
            </button>
            <button type="button" id="btnTipoRutinaria" onclick="seleccionarTipoCreacion('rutinaria')"
                    style="padding: 12px; border-radius: 10px; border: 2px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 800; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; font-size: 13px; transition: all 0.2s;">
                <span><i class="bi bi-arrow-repeat me-1"></i> Rutinaria</span>
                <span style="font-size: 10px; font-weight: 600; opacity: 0.85;">Tarea periódica / diaria</span>
            </button>
        </div>

        <form action="{{ route('actividades.store') }}" method="POST" id="formCrearActividad">
            @csrf
            <input type="hidden" name="tipo_actividad_form" id="crear_tipo_actividad" value="asignada">

            <!-- Responsable(s) y Dirigido a -->
            <div id="container_top_responsables_dirigido" style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;" id="labelEmpleadoCrear">
                        {{ $isBossOrAdmin ? '¿A quién le asignas esta actividad? *' : 'Empleado Responsable *' }}
                    </label>

                    <!-- SELECCIÓN MÚLTIPLE DE EMPLEADOS (PARA ASIGNADAS) -->
                    <div id="box_multiselect_empleados_asig">
                        <input type="hidden" name="empleado_id" id="crear_empleado_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="_colaboro_asig_radio" id="crear_colaboro_asig_radio" value="no">

                        <div id="bloque_seleccion_empleados_asig" style="background: #ffffff; border: 2px solid #15803d; border-radius: 8px; padding: 10px; max-height: 140px; overflow-y: auto;">
                            <span id="spanHeaderMultiselect" style="font-size: 11px; font-weight: 800; color: #166534; display: block; margin-bottom: 6px;">Marca los empleados asignados:</span>
                            @if(auth()->check())
                                <label class="asig-emp-label" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; font-size: 13px; color: #166534; font-weight: 700;">
                                    <input type="checkbox" name="empleados_asig_checkboxes[]" value="{{ auth()->id() }}" checked onchange="actualizarSeleccionEmpleadosAsig()" class="asig-emp-input" style="accent-color: #15803d; width: 16px; height: 16px;">
                                    YO ({{ auth()->user()->name }})
                                </label>
                            @endif
                            @foreach ($empleadosRH as $emp)
                                @if(($emp['id'] ?? $emp->id) !== auth()->id())
                                    <label class="asig-emp-label" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; font-size: 13px; color: #166534; font-weight: 600;">
                                        <input type="checkbox" name="empleados_asig_checkboxes[]" value="{{ $emp['id'] ?? $emp->id }}" onchange="actualizarSeleccionEmpleadosAsig()" class="asig-emp-input" style="accent-color: #15803d; width: 16px; height: 16px;">
                                        {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        <div id="container_hidden_empleados_compartidos"></div>
                    </div>

                    <!-- SELECT ÚNICO (PARA ACTIVIDADES PERSONALES / IMPREVISTAS Y RUTINAS) -->
                    <div id="box_single_select_empleado" style="display: none;">
                        <select id="crear_empleado_id_single" onchange="document.getElementById('crear_empleado_id').value = this.value" style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; background: white; font-weight: 700; color: #1e293b;">
                            <option value="">Selecciona un empleado</option>
                            @if(auth()->check())
                                <option value="{{ auth()->id() }}" selected style="font-weight: bold; color: #1e3a8a;">YO ({{ auth()->user()->name }})</option>
                            @endif
                            @foreach ($empleadosRH as $emp)
                                @if(($emp['id'] ?? $emp->id) !== auth()->id())
                                    <option value="{{ $emp['id'] ?? $emp->id }}">
                                        {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="box_col_dirigido_a">
                    <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;" id="labelDirigidoCrear">
                        Dirigido a (Jefe / Destinatario)
                    </label>
                    <select name="dirigido_a_id" id="crear_dirigido_a_id" style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; background: white; font-weight: 700; color: #1e293b;">
                        <option value="">Selecciona destinatario (opcional)</option>
                        @foreach ($empleadosRH as $emp)
                            <option value="{{ $emp['id'] ?? $emp->id }}">
                                {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- CAMPOS ESPECÍFICOS PARA ASIGNADAS -->
            <div id="seccion_campos_asignada">
                <!-- 2. ¿Es una actividad sencilla? (Va primero) -->
                <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;" id="boxPreguntaSencilla">
                    <label style="font-weight: 800; font-size: 14px; color: #166534; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <i class="bi bi-question-circle-fill" style="color: #15803d; font-size: 16px;"></i> ¿Es una actividad sencilla?
                    </label>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;">
                            <input type="radio" name="_sencilla" id="crear_sencilla_si" value="si" checked onchange="toggleSencillaNueva('si')" style="accent-color: #15803d; width: 16px; height: 16px;"> Sí, registrar solo título y descripción
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;">
                            <input type="radio" name="_sencilla" id="crear_sencilla_no" value="no" onchange="toggleSencillaNueva('no')" style="accent-color: #15803d; width: 16px; height: 16px;"> No, desplegar configuración avanzada
                        </label>
                    </div>
                </div>

                <!-- 3. Título y Descripción de la actividad -->
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;" id="labelTituloCrear">Tarea / Título de la Actividad *</label>
                    <input type="text" name="titulo" id="crear_titulo" required placeholder="Ej: Capacitación del nuevo practicante de Sistemas"
                           style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
                </div>

                <div id="seccion_descripcion_comun" style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;" id="label_descripcion">Descripción de la actividad *</label>
                    <textarea name="descripcion" id="crear_descripcion" rows="3" required placeholder="Explica detalladamente el objetivo o alcance de esta actividad..."
                              style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
                </div>

                <!-- BLOQUE DESPLEGABLE: Configuración Avanzada (Pasos 4 a 7) -->
                <div id="bloque_avanzado_asignada" style="display: none; background: #ffffff; border: 2px solid #15803d; border-radius: 10px; padding: 16px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 14px 0; color: #166534; font-size: 14px; font-weight: 800; border-bottom: 1px dashed #86efac; padding-bottom: 6px;">
                        <i class="bi bi-sliders me-1"></i> Configuración Avanzada de la Actividad
                    </h4>

                    <!-- 4. Acciones a realizar y Notas y Observaciones -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div>
                            <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelAccionesRealizadas">Acciones a realizar</label>
                            <textarea name="acciones_realizadas" id="crear_acciones_realizadas" rows="2" placeholder="Acciones específicas a ejecutar..."
                                      style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                        </div>
                        <div id="boxObservaciones">
                            <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelObservaciones">Notas y Observaciones</label>
                            <textarea name="observaciones" id="crear_observaciones" rows="2" placeholder="Notas u observaciones relevantes..."
                                      style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                        </div>
                    </div>

                    <!-- Prioridad -->
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;">Prioridad *</label>
                        <select name="prioridad" id="crear_prioridad" required style="width: 100%; padding: 9px; border: 2px solid #15803d; border-radius: 8px; background: white; font-weight: 700; color: #166534;">
                            <option value="" disabled selected>-- Selecciona Prioridad * --</option>
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>

                    <!-- 5. PREGUNTA: ¿Tiene plazo la actividad? -->
                    <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;" id="boxPreguntaTienePlazo">
                        <label style="font-weight: 800; font-size: 13px; color: #166534; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;" id="labelPreguntaTienePlazo">
                            <i class="bi bi-clock-history" style="color: #15803d;" id="iconPreguntaTienePlazo"></i> ¿Tiene plazo la actividad? *
                        </label>
                        <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;" id="labelPlazoSi">
                                <input type="radio" name="tiene_plazo" id="crear_plazo_si" value="si" onchange="toggleTienePlazo(this.value)" style="accent-color: #15803d; width: 16px; height: 16px;"> Sí, tiene plazo definido
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;" id="labelPlazoNo">
                                <input type="radio" name="tiene_plazo" id="crear_plazo_no" value="no" onchange="toggleTienePlazo(this.value)" style="accent-color: #15803d; width: 16px; height: 16px;"> No, sin plazo (cuando se pueda)
                            </label>
                        </div>

                        <!-- SI MARCA SÍ: PREGUNTA SUB-TIPO (FECHA U HORA) -->
                        <div id="sub_box_tipo_plazo" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #86efac;">
                            <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 6px;">
                                <i class="bi bi-calendar2-range me-1"></i> ¿El plazo es por Fecha o por Horario? *
                            </label>
                            <div style="display: flex; gap: 14px; margin-bottom: 10px; flex-wrap: wrap;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #166534; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #86efac;">
                                    <input type="radio" name="tipo_plazo" id="plazo_tipo_fecha" value="fecha" onchange="toggleTipoPlazoSub(this.value)" style="accent-color: #15803d;"> Por Fecha (Días)
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #166534; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #86efac;">
                                    <input type="radio" name="tipo_plazo" id="plazo_tipo_hora" value="hora" onchange="toggleTipoPlazoSub(this.value)" style="accent-color: #15803d;"> Por Horario (Horas)
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #166534; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #86efac;">
                                    <input type="radio" name="tipo_plazo" id="plazo_tipo_ambos" value="ambos" onchange="toggleTipoPlazoSub(this.value)" style="accent-color: #15803d;"> Ambos (Fecha y Horario)
                                </label>
                            </div>

                            <!-- CAMPOS DE FECHA -->
                            <div id="seccion_fechas_asignada" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #86efac; margin-bottom: 8px;">
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;">Fecha de Inicio *</label>
                                    <input type="date" name="fecha_inicio" id="crear_fecha_inicio" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;">Fecha Estimada de Término *</label>
                                    <input type="date" name="fecha_estimada_fin" id="crear_fecha_fin" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                                </div>
                            </div>

                            <!-- CAMPOS DE HORARIO -->
                            <div id="boxHorarioEstimado" style="display: none; grid-template-columns: 1fr 1fr; gap: 14px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #86efac;">
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelHoraInicio">
                                        <i class="bi bi-clock me-1"></i> Horario Estimado - INICIO
                                    </label>
                                    <input type="time" name="hora_inicio" id="crear_hora_inicio" value="09:00" onchange="calcularDiferenciaHorasCreacion()" oninput="calcularDiferenciaHorasCreacion()"
                                           style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 6px; font-weight: 700; color: #1e293b; background: white;">
                                </div>
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelHoraFin">
                                        <i class="bi bi-clock-history me-1"></i> Horario Estimado - TÉRMINO
                                    </label>
                                    <input type="time" name="hora_fin" id="crear_hora_fin" value="10:00" onchange="calcularDiferenciaHorasCreacion()" oninput="calcularDiferenciaHorasCreacion()"
                                           style="width: 100%; padding: 8px; border: 1.5px solid #15803d; border-radius: 6px; font-weight: 700; color: #1e293b; background: white;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. PREGUNTA: ¿Se depende de alguien? -->
                    <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #166534; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                            <i class="bi bi-person-badge-fill" style="color: #15803d;"></i> ¿Se depende de alguien?
                        </label>
                        <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;">
                                <input type="radio" name="_depende_asig_radio" id="crear_depende_no" value="no" onchange="toggleDependenciaAsig('no')" style="accent-color: #15803d; width: 16px; height: 16px;"> No
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #166534;">
                                <input type="radio" name="_depende_asig_radio" id="crear_depende_si" value="si" onchange="toggleDependenciaAsig('si')" style="accent-color: #15803d; width: 16px; height: 16px;"> Sí, especificar responsable y motivo
                            </label>
                        </div>

                        <!-- Si es SÍ: Despliega Dependencia Responsable y Motivo -->
                        <div id="bloque_dependencia_asig" style="display: none; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 10px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #15803d;">
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelDepArea">
                                    Área
                                </label>
                                <input type="text" name="dependencia_area" id="crear_dependencia_area" placeholder="Ej: Sistemas..."
                                       style="width: 100%; padding: 9px; border: 1.5px solid #15803d; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelDepResp">
                                    Responsable
                                </label>
                                <input type="text" name="dependencia_responsable" id="crear_dependencia_responsable" placeholder="Ej: Ing. Juan Pérez..."
                                       style="width: 100%; padding: 9px; border: 1.5px solid #15803d; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #166534; display: block; margin-bottom: 4px;" id="labelDepMotivo">
                                    Motivo / Razón
                                </label>
                                <input type="text" name="dependencia_motivo" id="crear_dependencia_motivo" placeholder="Ej: Entrega de reporte, firma de autorización..."
                                       style="width: 100%; padding: 9px; border: 1.5px solid #15803d; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CAMPOS ESPECÍFICOS: IMPREVISTA / PERSONAL -->
            <div id="seccion_campos_imprevista" style="display: none;">
                
                <!-- 1. Empleado asignado / responsable * (Fijo e inamovible) -->
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">
                        Empleado asignado / responsable *
                    </label>
                    <div style="background: #fff7ed; border: 2px solid #fed7aa; border-radius: 8px; padding: 10px 14px; font-weight: 800; color: #9a3412; display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="bi bi-person-fill me-1" style="color: #c2410c;"></i> YO ({{ auth()->user()->name ?? 'Usuario' }})</span>
                        <span style="font-size: 11px; font-weight: 700; color: #ea580c; background: #ffedd5; padding: 2px 8px; border-radius: 6px; border: 1px solid #fed7aa;">Fijo / Inamovible</span>
                    </div>
                </div>

                <!-- 2. Dirigido a (Jefe / Destinatario) -->
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">
                        Dirigido a (Jefe / Destinatario)
                    </label>
                    <select name="dirigido_a_id" id="crear_dirigido_a_id_imp" style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 700; color: #1e293b;">
                        <option value="">Selecciona destinatario (opcional)</option>
                        @foreach ($empleadosRH as $emp)
                            <option value="{{ $emp['id'] ?? $emp->id }}">
                                {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. ¿Hubo colaboradores en esta actividad personal? -->
                <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                        <i class="bi bi-people-fill" style="color: #c2410c;"></i> ¿Hubo / Habrá colaboradores en esta actividad personal?
                    </label>
                    <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                            <input type="radio" name="_colaboro_imp_radio" id="crear_colaboro_imp_no" value="no" onchange="toggleColaboradoresImp('no')" style="accent-color: #c2410c; width: 16px; height: 16px;"> No
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                            <input type="radio" name="_colaboro_imp_radio" id="crear_colaboro_imp_si" value="si" onchange="toggleColaboradoresImp('si')" style="accent-color: #c2410c; width: 16px; height: 16px;"> Sí, hubo colaboradores
                        </label>
                    </div>

                    <div id="bloque_lista_colaboradores_imp" style="display: none; margin-top: 10px; background: #ffffff; border: 2px solid #c2410c; border-radius: 8px; padding: 10px; max-height: 140px; overflow-y: auto;">
                        <span style="font-size: 12px; font-weight: 800; color: #9a3412; display: block; margin-bottom: 6px;">Selecciona quiénes colaboraron:</span>
                        @foreach ($empleadosRH as $emp)
                            @if(($emp['id'] ?? $emp->id) !== auth()->id())
                                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer; font-size: 13px; color: #9a3412; font-weight: 600;">
                                    <input type="checkbox" name="empleados_compartidos[]" value="{{ $emp['id'] ?? $emp->id }}" style="accent-color: #c2410c; width: 16px; height: 16px;">
                                    {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Contenedor flotante de Hora Estimada -->
                <div id="wrapper_hora_estimada" style="display: none; background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                        <i class="bi bi-clock-history" style="color: #c2410c;"></i> Hora estimada *
                    </label>

                    <div id="imprevista_horas_box" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 10px;">
                        <div>
                            <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Hora de Inicio *</label>
                            <input type="time" name="hora_inicio" id="crear_imp_hora_inicio" value="{{ now()->format('H:i') }}" oninput="calcImpHorasInvertidas()" onchange="calcImpHorasInvertidas()" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                        </div>
                        <div>
                            <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Hora Estimada de Término *</label>
                            <input type="time" name="hora_fin" id="crear_imp_hora_fin" value="{{ now()->addHour()->format('H:i') }}" oninput="calcImpHorasInvertidas()" onchange="calcImpHorasInvertidas()" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                        </div>
                    </div>
                    
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412; margin-top: 8px;">
                        <input type="checkbox" name="sin_hora_estimada" id="crear_imp_sin_hora" value="1" onchange="toggleImprevistaHoras()" style="accent-color: #c2410c; width: 16px; height: 16px;"> No hay hora estimada
                    </label>
                </div>

                <!-- 5. PREGUNTA: ¿Se realizó / comenzó o está pendiente? -->
                <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 14px; color: #9a3412; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <i class="bi bi-question-circle-fill" style="color: #c2410c; font-size: 16px;"></i> ¿Se realizó / comenzó o está pendiente? *
                    </label>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                            <input type="radio" name="estado_personal_radio" value="realizada" onchange="toggleEstadoPersonal(this.value)" style="accent-color: #c2410c; width: 16px; height: 16px;"> Realizó / Comenzó
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                            <input type="radio" name="estado_personal_radio" value="pendiente" onchange="toggleEstadoPersonal(this.value)" style="accent-color: #c2410c; width: 16px; height: 16px;"> Pendiente
                        </label>
                    </div>
                    <input type="hidden" name="estado" id="crear_estado_imprevisto" value="pendiente">
                </div>

                <!-- A) SI MARCA "Realizó / Comenzó" -->
                <div id="bloque_personal_realizada" style="display: none;">
                    <!-- Tarea / Título de la Actividad * -->
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Tarea / Título de la Actividad *</label>
                        <input type="text" name="motivo" id="crear_motivo" placeholder="Indica el título de esta actividad personal..."
                               style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
                    </div>

                    <!-- Descripción de la actividad * -->
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Descripción de la actividad *</label>
                        <textarea name="descripcion_imp_realizada" id="crear_descripcion_imp_realizada" rows="3" placeholder="Explica detalladamente la actividad realizada o en proceso..."
                                  style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                    </div>

                    <div id="ph_hora_realizada"></div>

                    <!-- Porcentaje de Completitud -->
                    <div style="background: #ffffff; border: 2px solid #c2410c; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin: 0;">
                                <i class="bi bi-graph-up-arrow" style="color: #c2410c;"></i> Porcentaje de Completitud *
                            </label>
                            <span style="font-weight: 900; font-size: 15px; color: #c2410c;" id="display_porcentaje_imprevisto">100%</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <input type="range" name="porcentaje_avance" id="crear_porcentaje_imprevisto" min="0" max="100" step="5" value="100"
                                   oninput="updateImprevistoPorcentajeDisplay(this.value)"
                                   style="width: 100%; accent-color: #c2410c; cursor: pointer; height: 8px;">
                            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #64748b; font-weight: 700; margin-top: 4px;">
                                <span>0% (Sin empezar)</span>
                                <span>1-99% (En proceso)</span>
                                <span>100% (Completada)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones realizadas y Notas / Observaciones -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div>
                            <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Acciones a realizar</label>
                            <textarea name="acciones_realizadas" id="crear_acciones_realizadas_imp" rows="2" placeholder="Acciones ejecutadas..."
                                      style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                        </div>
                        <div>
                            <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Notas y Observaciones</label>
                            <textarea name="observaciones_imp" id="crear_observaciones_imp" rows="2" placeholder="Notas u observaciones..."
                                      style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                        </div>
                    </div>

                    <!-- Tiempo Invertido (Oculto) -->
                    <input type="hidden" name="horas_invertidas" id="crear_horas_invertidas" value="1.0">

                    <!-- Resultado Obtenido -->
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Resultados obtenidos al momento</label>
                        <textarea name="resultado_obtenido" id="crear_resultado_obtenido" rows="2" placeholder="¿Cuál fue el resultado obtenido o solución realizada?"
                                  style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
                    </div>

                    <!-- ¿Se depende de alguien? -->
                    <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                        <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                            <i class="bi bi-person-badge-fill" style="color: #c2410c;"></i> ¿Se depende de alguien?
                        </label>
                        <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                <input type="radio" name="_depende_imp_radio" value="no" onchange="toggleDependenciaImp('no')" style="accent-color: #c2410c; width: 16px; height: 16px;"> No
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                <input type="radio" name="_depende_imp_radio" value="si" onchange="toggleDependenciaImp('si')" style="accent-color: #c2410c; width: 16px; height: 16px;"> Sí, especificar responsable y motivo
                            </label>
                        </div>

                        <div id="bloque_dependencia_imp" style="display: none; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 10px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #c2410c;">
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Área</label>
                                <input type="text" name="dependencia_area" id="crear_dependencia_area_imp" placeholder="Área..."
                                       style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Responsable</label>
                                <input type="text" name="dependencia_responsable" id="crear_dependencia_responsable_imp" placeholder="Nombre de la persona..."
                                       style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Motivo / Razón</label>
                                <input type="text" name="dependencia_motivo" id="crear_dependencia_motivo_imp" placeholder="Ej: Entrega de reporte..."
                                       style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B) SI MARCA "Pendiente" -->
                <div id="bloque_personal_pendiente" style="display: none;">
                    <!-- Configuración de Actividad Pendiente -->
                    <div id="bloque_avanzado_imp_pendiente" style="display: block; background: #ffffff; border: 2px solid #c2410c; border-radius: 10px; padding: 16px; margin-bottom: 16px;">

                        <!-- Tarea / Título de la Actividad * -->
                        <div style="margin-bottom: 16px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Tarea / Título de la Actividad *</label>
                            <input type="text" name="titulo_imp_avanzada" id="crear_titulo_imp_avanzada" placeholder="Indica el título de esta actividad..."
                                   style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
                        </div>

                        <!-- Descripción de la actividad * -->
                        <div style="margin-bottom: 16px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Descripción de la actividad *</label>
                            <textarea name="descripcion_imp_avanzada" id="crear_descripcion_imp_avanzada" rows="3" placeholder="Explica detalladamente la actividad..."
                                      style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                        </div>
                        
                        <!-- Acciones a realizar & Notas y Observaciones -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Acciones a realizar</label>
                                <textarea name="acciones_realizadas_pend" rows="2" placeholder="Acciones específicas a ejecutar..."
                                          style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                            </div>
                            <div>
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Notas y Observaciones</label>
                                <textarea name="observaciones_pend" rows="2" placeholder="Notas u observaciones relevantes..."
                                          style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                            </div>
                        </div>

                        <!-- Prioridad -->
                        <div style="margin-bottom: 16px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Prioridad *</label>
                            <select name="prioridad_pend" style="width: 100%; padding: 9px; border: 2px solid #c2410c; border-radius: 8px; background: white; font-weight: 700; color: #9a3412;">
                                <option value="" disabled selected>-- Selecciona Prioridad * --</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>

                        <!-- Plazo (Fecha u Hora) -->
                        <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                <i class="bi bi-clock-history" style="color: #c2410c;"></i> ¿Tiene plazo la actividad? *
                            </label>
                            <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                    <input type="radio" name="tiene_plazo_imp" value="si" onchange="toggleTienePlazoImp(this.value)" style="accent-color: #c2410c; width: 16px; height: 16px;"> Sí, tiene plazo definido
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                    <input type="radio" name="tiene_plazo_imp" value="no" onchange="toggleTienePlazoImp(this.value)" style="accent-color: #c2410c; width: 16px; height: 16px;"> No, sin plazo (cuando se pueda)
                                </label>
                            </div>

                            <div id="sub_box_tipo_plazo_imp" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #fed7aa;">
                                <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 6px;">
                                    <i class="bi bi-calendar2-range me-1"></i> ¿El plazo es por Fecha o por Horario? *
                                </label>
                                <div style="display: flex; gap: 14px; margin-bottom: 10px; flex-wrap: wrap;">
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #9a3412; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #fed7aa;">
                                        <input type="radio" name="tipo_plazo_imp" value="fecha" onchange="toggleTipoPlazoSubImp(this.value)" style="accent-color: #c2410c;"> Por Fecha (Días)
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #9a3412; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #fed7aa;">
                                        <input type="radio" name="tipo_plazo_imp" value="hora" onchange="toggleTipoPlazoSubImp(this.value)" style="accent-color: #c2410c;"> Por Horario (Horas)
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 700; color: #9a3412; background: #ffffff; padding: 6px 12px; border-radius: 6px; border: 1px solid #fed7aa;">
                                        <input type="radio" name="tipo_plazo_imp" value="ambos" onchange="toggleTipoPlazoSubImp(this.value)" style="accent-color: #c2410c;"> Ambos (Fecha y Horario)
                                    </label>
                                </div>

                                <div id="seccion_fechas_imp" style="display: none; grid-template-columns: 1fr 1fr; gap: 14px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #fed7aa; margin-bottom: 8px;">
                                    <div>
                                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Fecha de Inicio *</label>
                                        <input type="date" name="fecha_inicio_imp" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                                    </div>
                                    <div>
                                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Fecha Estimada de Término *</label>
                                        <input type="date" name="fecha_estimada_fin_imp" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; background: white; font-weight: 700; color: #1e293b;">
                                    </div>
                                </div>

                                <div id="boxHorario_imp" style="display: none; grid-template-columns: 1fr 1fr; gap: 14px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #fed7aa;">
                                    <div>
                                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Horario Estimado - INICIO</label>
                                        <input type="time" name="hora_inicio_imp" value="09:00" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; font-weight: 700; color: #1e293b; background: white;">
                                    </div>
                                    <div>
                                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Horario Estimado - TÉRMINO</label>
                                        <input type="time" name="hora_fin_imp" value="10:00" style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 6px; font-weight: 700; color: #1e293b; background: white;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ¿Se depende de alguien? -->
                        <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                            <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                <i class="bi bi-person-badge-fill" style="color: #c2410c;"></i> ¿Se depende de alguien?
                            </label>
                            <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                    <input type="radio" name="_depende_imp_pend_radio" value="no" onchange="toggleDependenciaImpPend('no')" style="accent-color: #c2410c; width: 16px; height: 16px;"> No
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                                    <input type="radio" name="_depende_imp_pend_radio" value="si" onchange="toggleDependenciaImpPend('si')" style="accent-color: #c2410c; width: 16px; height: 16px;"> Sí, especificar responsable y motivo
                                </label>
                            </div>

                            <div id="bloque_dependencia_imp_pend" style="display: none; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 10px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #c2410c;">
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Área</label>
                                    <input type="text" name="dependencia_area" id="crear_dependencia_area_imp_pend" placeholder="Área..."
                                           style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Responsable</label>
                                    <input type="text" name="dependencia_responsable" id="crear_dependencia_responsable_imp_pend" placeholder="Nombre de la persona..."
                                           style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Motivo / Razón</label>
                                    <input type="text" name="dependencia_motivo" id="crear_dependencia_motivo_imp_pend" placeholder="Ej: Entrega de reporte..."
                                           style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMPOS ESPECÍFICOS: RUTINARIA -->
            <div id="seccion_campos_rutinaria" style="display: none;">
                <!-- 3. Tarea / Título de la Actividad * -->
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #1e3a8a; display: block; margin-bottom: 6px;">Tarea / Título de la Actividad *</label>
                    <input type="text" name="titulo_rutinaria" id="crear_titulo_rutinaria" placeholder="Ej: Limpieza y desinfección diaria de estación de trabajo"
                           style="width: 100%; padding: 10px; border: 2px solid #1e40af; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
                </div>

                <!-- 4. Descripción de la actividad * -->
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #1e3a8a; display: block; margin-bottom: 6px;">Descripción de la actividad *</label>
                    <textarea name="descripcion_rutinaria" id="crear_descripcion_rutinaria" rows="3" placeholder="Explica detalladamente el objetivo de esta rutina..."
                              style="width: 100%; padding: 10px; border: 2px solid #1e40af; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
                </div>

                <!-- 5 & 6. Acciones a realizar y Notas y Observaciones -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="font-weight: 800; font-size: 12px; color: #1e3a8a; display: block; margin-bottom: 4px;">Acciones a realizar</label>
                        <textarea name="acciones_realizadas_rutinaria" id="crear_acciones_realizadas_rutinaria" rows="2" placeholder="Acciones específicas a ejecutar..."
                                  style="width: 100%; padding: 8px; border: 1.5px solid #1e40af; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                    </div>
                    <div>
                        <label style="font-weight: 800; font-size: 12px; color: #1e3a8a; display: block; margin-bottom: 4px;">Notas y Observaciones</label>
                        <textarea name="observaciones_rutinaria" id="crear_observaciones_rutinaria" rows="2" placeholder="Notas u observaciones relevantes..."
                                  style="width: 100%; padding: 8px; border: 1.5px solid #1e40af; border-radius: 8px; font-family: inherit; background: white; font-weight: 500; color: #1e293b;"></textarea>
                    </div>
                </div>

                <!-- 7. ¿Cuántas veces al día debe repetirse esta rutina? * -->
                <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap;">
                    <label style="font-weight: 800; font-size: 13px; color: #1e3a8a; margin: 0;">
                        <i class="bi bi-arrow-repeat me-1" style="color: #1e40af;"></i> ¿Cuántas veces al día debe repetirse esta rutina? *
                    </label>
                    <input type="number" name="veces_al_dia" id="crear_veces_al_dia" min="1" max="20" value="1"
                           style="width: 90px; padding: 6px 10px; border: 2px solid #1e40af; border-radius: 8px; box-sizing: border-box; background: white; font-weight: 800; font-size: 15px; color: #1e3a8a; text-align: center;">
                </div>
            </div>

            <!-- CONFIGURACIÓN DE PERMISO DE REGISTRO DE AVANCE (SOLO ASIGNADAS, DESMARCADO POR DEFECTO) -->
            @if($isBossOrAdmin)
                <div id="boxPermitirAvance" style="background: #ffffff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #1e293b; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-shield-lock-fill" style="color: #2563eb;"></i> ¿Permitir que el empleado registre avances de porcentaje y notas?
                        </span>
                        <input type="checkbox" name="permitir_registro_avance" id="crear_permitir_registro_avance" value="1" style="width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer;">
                    </label>
                </div>
            @endif

            <!-- SECCIÓN: MARCAR COMO COMPLETADA AL EDITAR -->
            <div id="seccion_marcar_completada" style="display: none; background: #f0fdf4; border: 2px solid #166534; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #166534; display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 8px;">
                    <input type="checkbox" name="marcar_completada" id="chk_marcar_completada" value="1" style="width: 18px; height: 18px; accent-color: #166534;" onchange="toggleNotasCompletada()">
                    <i class="bi bi-check-circle-fill"></i> Marcar esta actividad como FINALIZADA / COMPLETADA
                </label>
                <div id="box_notas_completada" style="display: none; margin-top: 10px;">
                    <label style="font-weight: bold; font-size: 12px; color: #166534; display: block; margin-bottom: 6px;">Resultados / Explicación del trabajo finalizado *</label>
                    <textarea name="notas_completada" id="txt_notas_completada" rows="2" style="width: 100%; padding: 8px; border: 1px solid #166534; border-radius: 6px;"></textarea>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right; border-top: 2px solid #cbd5e1; padding-top: 16px;">
                <button type="button" class="btn-ver" style="background: #64748b; color: white; margin-right: 10px; font-weight: 700; padding: 10px 18px; border-radius: 8px;" onclick="cerrarModal('modalCrearActividad')">Cancelar</button>
                <button type="button" class="btn-form" id="btnSubmitCrear" onclick="guardarNuevaActividad(event)" style="background: #15803d; color: white; padding: 10px 24px; font-size: 14px; font-weight: 800; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: pointer;">Guardar Actividad</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL UNIFICADO: EDITAR ACTIVIDAD (EDITAR DATOS) -->
<!-- ============================================== -->
<div id="modalEditarActividad" class="rh-modal">
    <div class="rh-modal-content" id="modalEditarContent" style="max-width: 650px; background: #f0fdf4; border: 3px solid #15803d; box-shadow: 0 10px 25px rgba(0,0,0,0.25); transition: all 0.3s;">
        <span class="rh-modal-close" onclick="cerrarModal('modalEditarActividad')">&times;</span>
        
        <div style="margin-bottom: 20px;">
            <h2 style="margin: 0 0 6px 0; color: #166534; font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px;" id="tituloModalEditar">
                <i class="bi bi-pencil-square" id="iconEditModalTitle" style="color: #15803d;"></i> Editar Actividad
            </h2>
            <p style="margin: 0; font-size: 13px; color: #334155; font-weight: 600;" id="subtituloModalEditar">Modifica los detalles generales de la actividad seleccionada.</p>
        </div>

        <form action="" method="POST" id="formEditarActividad">
            @csrf
            @method('PUT')
            <input type="hidden" name="editar_tipo" id="edit_tipo_hidden" value="asignada">

            <!-- Empleado Asignado -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #334155; display: block; margin-bottom: 6px;" id="labelEditEmpleado">Empleado Asignado / Responsable *</label>
                <select name="empleado_id" id="edit_empleado_id" required style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; background: white; font-weight: 700; color: #1e293b;">
                    <option value="">Selecciona un empleado</option>
                    @if(auth()->check())
                        <option value="{{ auth()->id() }}" style="font-weight: bold; color: #1e3a8a;">YO ({{ auth()->user()->name }})</option>
                    @endif
                    @foreach ($empleadosRH as $emp)
                        @if(($emp['id'] ?? $emp->id) !== auth()->id())
                            <option value="{{ $emp['id'] ?? $emp->id }}">
                                {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Título -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #334155; display: block; margin-bottom: 6px;" id="labelEditTitulo">Título de la Actividad *</label>
                <input type="text" name="titulo" id="edit_titulo" required style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
            </div>

            <!-- Descripción (para asignadas y rutinas) -->
            <div id="edit_bloque_descripcion" style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #334155; display: block; margin-bottom: 6px;" id="labelEditDescripcion">Descripción Detallada</label>
                <textarea name="descripcion" id="edit_descripcion" rows="3" style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
            </div>

            <!-- CAMPOS COMUNES EDIT -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #475569; display: block; margin-bottom: 6px;" class="edit-dynamic-label">Acciones a Realizar *</label>
                <textarea name="acciones_realizadas" id="edit_acciones_realizadas" rows="2" style="width: 100%; padding: 10px; border: 2px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-weight: 500; color: #1e293b; resize: vertical;" required></textarea>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #475569; display: block; margin-bottom: 6px;" class="edit-dynamic-label">Notas y Observaciones Generales</label>
                <textarea name="observaciones" id="edit_observaciones" rows="2" style="width: 100%; padding: 10px; border: 2px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-weight: 500; color: #1e293b; resize: vertical;"></textarea>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;" class="edit-dynamic-label">
                    <input type="checkbox" id="edit_dependencia_checkbox" onchange="toggleEditDependenciaFields()" style="width: 18px; height: 18px;">
                    ¿Esta actividad depende de otra área o persona?
                </label>
                
                <div id="edit_dependencia_fields" style="display: none; background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px dashed #cbd5e1; gap: 10px; grid-template-columns: 1fr 1fr;">
                    <div style="grid-column: 1 / -1; margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold; color: #475569;">Motivo / Razón de la dependencia *</label>
                        <input type="text" name="dependencia_motivo" id="edit_dependencia_motivo" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #475569;">Área *</label>
                        <select name="dependencia_area" id="edit_dependencia_area" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">Seleccione área</option>
                            @php $areasList = $areas ?? \App\Models\Area::all(); @endphp
                            @foreach($areasList as $area)
                                <option value="{{ $area->nombre }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: #475569;">Responsable *</label>
                        <input type="text" name="dependencia_responsable" id="edit_dependencia_responsable" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>
            </div>

            <!-- CAMPOS EDITAR: ASIGNADA -->
            <div id="edit_seccion_asignada">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="font-weight: 800; font-size: 13px; color: #334155; display: block; margin-bottom: 6px;">Estado General</label>
                        <select name="estado" id="edit_estado" style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; background: white; font-weight: 700; color: #1e293b;">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="finalizada">Finalizada</option>
                            <option value="atrasada">Atrasada</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 800; font-size: 13px; color: #334155; display: block; margin-bottom: 6px;">Prioridad</label>
                        <select name="prioridad" id="edit_prioridad" style="width: 100%; padding: 10px; border: 2px solid #15803d; border-radius: 8px; background: white; font-weight: 700; color: #1e293b;">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CAMPOS EDITAR: IMPREVISTA -->
            <div id="edit_seccion_imprevista" style="display: none;">
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Descripción Detallada *</label>
                    <textarea name="descripcion_detallada" id="edit_descripcion_detallada" rows="3" style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Motivo / Justificación *</label>
                    <input type="text" name="motivo" id="edit_motivo" style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; box-sizing: border-box; background: white; font-weight: 600; color: #1e293b;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;">Resultado Obtenido *</label>
                    <textarea name="resultado_obtenido" id="edit_resultado_obtenido" rows="2" style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
                </div>
            </div>

            <!-- CAMPOS EDITAR: RUTINARIA -->
            <div id="edit_seccion_rutinaria" style="display: none;">
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 800; font-size: 13px; color: #1e3a8a; display: block; margin-bottom: 6px;">Veces al día *</label>
                    <input type="number" name="veces_al_dia" id="edit_veces_al_dia" min="1" max="20" style="width: 100%; padding: 10px; border: 2px solid #1e40af; border-radius: 8px; box-sizing: border-box; background: white; font-weight: 700; color: #1e293b;">
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right; border-top: 2px solid #cbd5e1; padding-top: 16px;">
                <button type="button" class="btn-ver" style="background: #64748b; color: white; margin-right: 10px; font-weight: 700; padding: 10px 18px; border-radius: 8px;" onclick="cerrarModal('modalEditarActividad')">Cancelar</button>
                <button type="submit" class="btn-form" id="btnSubmitEditar" style="background: #15803d; color: white; padding: 10px 24px; font-size: 14px; font-weight: 800; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL UNIVERSAL: REGISTRAR AVANCE Y NOTA (ASIGNADA, IMPREVISTA, RUTINARIA) -->
<!-- ============================================== -->
<div id="modalActualizarAvanceGenerico" class="rh-modal">
    <div class="rh-modal-content" id="contentModalAvanceGen" style="max-width: 540px; background: #fff7ed; border: 3px solid #c2410c; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); padding: 22px;">
        <span class="rh-modal-close" onclick="cerrarModal('modalActualizarAvanceGenerico')">&times;</span>
        
        <div style="margin-bottom: 18px;">
            <h2 style="margin: 0 0 6px 0; color: #9a3412; font-size: 19px; font-weight: 800; display: flex; align-items: center; gap: 8px;" id="titleAvanceGenHeader">
                <i class="bi bi-graph-up-arrow" style="color: #c2410c;"></i> Registrar Avance de Actividad
            </h2>
            <p id="avanceGenTituloText" style="margin: 0; font-size: 13px; color: #475569; font-weight: 700;">Título de la actividad</p>
        </div>

        <form action="" method="POST" id="formAvanceGenerico" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tipo_actividad_gen" id="avance_tipo_gen_hidden" value="asignada">

            <!-- HORARIOS DE INICIO Y FIN -->
            <div id="boxAvanceHorarioGen" style="background: #ffffff; border: 2px solid #c2410c; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <label id="labelAvanceHorarioHeader" style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                    <i id="iconAvanceHorarioHeader" class="bi bi-clock-history" style="color: #c2410c;"></i> Horario de la Actividad
                </label>
                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label id="labelHoraInicioGen" style="font-size: 11px; font-weight: 700; color: #c2410c; display: block; margin-bottom: 4px;">Hora Inicio</label>
                        <input type="time" name="hora_inicio" id="avance_hora_inicio" onchange="calcularHorasAvance()" style="width: 100%; padding: 8px; border: 1.5px solid #fed7aa; border-radius: 6px; font-family: inherit; font-weight: 600; color: #1e293b;" required>
                    </div>
                    <div style="flex: 1;">
                        <label id="labelHoraFinGen" style="font-size: 11px; font-weight: 700; color: #c2410c; display: block; margin-bottom: 4px;">Hora Término</label>
                        <input type="time" name="hora_fin" id="avance_hora_fin" onchange="calcularHorasAvance()" style="width: 100%; padding: 8px; border: 1.5px solid #fed7aa; border-radius: 6px; font-family: inherit; font-weight: 600; color: #1e293b;" required>
                    </div>
                </div>
                <div style="margin-bottom: 12px; font-size: 11px; font-weight: 700; color: #047857;" id="mensaje_calculo_horas"></div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" name="sin_horario" id="avance_sin_horario" value="true" onchange="toggleAvanceHorario()">
                        No se sabe con exactitud el horario (N/A)
                    </label>
                </div>
            </div>
            <script>
            function calcularHorasAvance() {
                let horaInicio = document.getElementById('avance_hora_inicio').value;
                let horaFin = document.getElementById('avance_hora_fin').value;
                let msgContainer = document.getElementById('mensaje_calculo_horas');
                let inputHoras = document.getElementById('input_avance_gen_horas');

                if (horaInicio && horaFin) {
                    let dInicio = new Date('1970-01-01T' + horaInicio + ':00');
                    let dFin = new Date('1970-01-01T' + horaFin + ':00');
                    
                    if (dFin < dInicio) {
                        dFin.setDate(dFin.getDate() + 1); // Cruce de medianoche
                    }

                    let diffMs = dFin - dInicio;
                    let diffHrs = diffMs / (1000 * 60 * 60);
                    let diffMins = Math.round((diffHrs % 1) * 60);
                    let fullHrs = Math.floor(diffHrs);

                    if (diffHrs > 0) {
                        inputHoras.value = diffHrs.toFixed(2);
                        msgContainer.innerHTML = '🕒 Tiempo calculado: ' + fullHrs + ' hrs y ' + diffMins + ' min (' + diffHrs.toFixed(2) + ' hrs). Se sumará al total.';
                    } else {
                        msgContainer.innerHTML = '';
                    }
                } else {
                    msgContainer.innerHTML = '';
                }
            }

            function toggleAvanceHorario() {
                let isChecked = document.getElementById('avance_sin_horario').checked;
                document.getElementById('avance_hora_inicio').disabled = isChecked;
                document.getElementById('avance_hora_fin').disabled = isChecked;
                if (isChecked) {
                    document.getElementById('avance_hora_inicio').required = false;
                    document.getElementById('avance_hora_fin').required = false;
                    document.getElementById('avance_hora_inicio').value = '';
                    document.getElementById('avance_hora_fin').value = '';
                    document.getElementById('mensaje_calculo_horas').innerHTML = '';
                } else {
                    document.getElementById('avance_hora_inicio').required = true;
                    document.getElementById('avance_hora_fin').required = true;
                }
            }
            </script>

            <!-- SECCIÓN DE DEPENDENCIA -->
            <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <i class="bi bi-person-badge-fill" style="color: #c2410c;"></i> ¿Depende de alguien más a partir de este avance?
                </label>
                <div style="display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                        <input type="radio" name="_depende_avance_radio" value="no" onchange="toggleDependenciaAvance('no')" checked style="accent-color: #c2410c; width: 16px; height: 16px;"> No / Ya estaba definida
                    </label>
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 700; color: #9a3412;">
                        <input type="radio" name="_depende_avance_radio" value="si" onchange="toggleDependenciaAvance('si')" style="accent-color: #c2410c; width: 16px; height: 16px;"> Sí, agregar o actualizar dependencia
                    </label>
                </div>

                <div id="bloque_dependencia_avance" style="display: none; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 10px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1.5px solid #c2410c;">
                    <div>
                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Área</label>
                        <input type="text" name="dependencia_area" id="avance_dependencia_area" placeholder="Área..."
                               style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                    </div>
                    <div>
                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Responsable</label>
                        <input type="text" name="dependencia_responsable" id="avance_dependencia_responsable" placeholder="Nombre de la persona..."
                               style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                    </div>
                    <div>
                        <label style="font-weight: 800; font-size: 12px; color: #9a3412; display: block; margin-bottom: 4px;">Motivo / Razón</label>
                        <input type="text" name="dependencia_motivo" id="avance_dependencia_motivo" placeholder="Ej: Entrega de reporte..."
                               style="width: 100%; padding: 8px; border: 1.5px solid #c2410c; border-radius: 8px; background: white; font-weight: 600; color: #1e293b;">
                    </div>
                </div>
            </div>
            <script>
            function toggleDependenciaAvance(val) {
                let bloque = document.getElementById('bloque_dependencia_avance');
                let inArea = document.getElementById('avance_dependencia_area');
                let inResp = document.getElementById('avance_dependencia_responsable');
                let inMotivo = document.getElementById('avance_dependencia_motivo');

                if (val === 'si') {
                    bloque.style.display = 'grid';
                } else {
                    bloque.style.display = 'none';
                    inArea.value = '';
                    inResp.value = '';
                    inMotivo.value = '';
                }
            }
            </script>
            
            <!-- PANEL SELECCIÓN DE PORCENTAJE (DESLIZANTE PARA ASIGNADAS/IMPREVISTAS) -->
            <div id="boxAvanceSliderGen" style="background: #ffffff; border: 2px solid #c2410c; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin: 0;" id="labelPorcentajeAvanceGen">
                        <i class="bi bi-percent" style="color: #c2410c;" id="iconPorcentajeAvanceGen"></i> Porcentaje de Avance
                    </label>
                    <span style="font-weight: 900; font-size: 16px; color: #c2410c;" id="display_avance_gen_val">50%</span>
                </div>
                <div style="margin-bottom: 12px;">
                    <input type="range" name="porcentaje_avance" id="input_avance_gen_range" min="0" max="100" step="5" value="50"
                           oninput="updateAvanceGenDisplay(this.value)"
                           style="width: 100%; accent-color: #c2410c; cursor: pointer; height: 8px;">
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px;">
                    <button type="button" class="quick-btn-avance" onclick="setAvanceGenQuickVal(25)" style="padding: 6px; font-size: 11px; font-weight: 800; border-radius: 6px; border: 1px solid #fed7aa; background: #fff7ed; color: #9a3412; cursor: pointer;">25%</button>
                    <button type="button" class="quick-btn-avance" onclick="setAvanceGenQuickVal(50)" style="padding: 6px; font-size: 11px; font-weight: 800; border-radius: 6px; border: 1px solid #fed7aa; background: #fff7ed; color: #9a3412; cursor: pointer;">50%</button>
                    <button type="button" class="quick-btn-avance" onclick="setAvanceGenQuickVal(75)" style="padding: 6px; font-size: 11px; font-weight: 800; border-radius: 6px; border: 1px solid #fed7aa; background: #fff7ed; color: #9a3412; cursor: pointer;">75%</button>
                    <button type="button" class="quick-btn-avance" onclick="setAvanceGenQuickVal(100)" style="padding: 6px; font-size: 11px; font-weight: 800; border-radius: 6px; border: 1px solid #ea580c; background: #ea580c; color: #ffffff; cursor: pointer;">100% ✓</button>
                </div>
            </div>

            <!-- PANEL RUTINAS: SELECCIÓN DIVIDIDA DE EJECUCIONES -->
            <div id="boxAvanceRutinaDividido" style="display: none; background: #ffffff; border: 2px solid #1e40af; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #1e3a8a; display: block; margin-bottom: 8px;" id="labelRutinaAvanceHeader">
                    <i class="bi bi-arrow-repeat me-1"></i> Seleccionar Ejecución Realizada de la Rutina:
                </label>
                <div id="containerOpcionesRutinaDiv" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- Opciones generadas por JS -->
                </div>
            </div>

            <!-- HORAS COMPUTADAS / INVERTIDAS EN EL AVANCE (SOLO ASIGNADAS) -->
            <div id="boxHorasTrabajadasGen" style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #166534; display: block; margin-bottom: 6px;" id="labelHorasTrabajadasGen">
                    <i class="bi bi-clock-history me-1"></i> Horas Computadas / Invertidas (Horas) *
                </label>
                <input type="number" step="0.5" min="0.0" name="horas_invertidas" id="input_avance_gen_horas" value="0.0"
                       style="width: 100%; padding: 9px; border: 2px solid #15803d; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 700; color: #1e293b;">
            </div>

            <!-- NOTA EXPLICATIVA O COMENTARIO DEL AVANCE -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: block; margin-bottom: 6px;" id="labelNotaAvanceGen">
                    Notas del Avance / Explicación del Trabajo Realizado *
                </label>
                <textarea name="comentario_avance" id="input_avance_gen_resultado" rows="3" placeholder="Explica detalladamente qué realizaste en este avance..."
                          style="width: 100%; padding: 10px; border: 2px solid #c2410c; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
            </div>

            <!-- ADJUNTAR ARCHIVOS / IMÁGENES / DOCUMENTOS -->
            <div style="margin-bottom: 18px; background: #ffffff; border: 2px dashed #c2410c; border-radius: 10px; padding: 14px 16px;" id="boxArchivoAvanceGen">
                <label style="font-weight: 800; font-size: 13px; color: #9a3412; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;" id="labelAdjuntoAvanceGen">
                    <i class="bi bi-paperclip" style="color: #c2410c;"></i> Adjuntar Evidencias (Imágenes o Documentos)
                </label>
                <p style="margin: 0 0 8px 0; font-size: 11px; color: #64748b; font-weight: 600;">
                    Sube imágenes de cualquier formato (PNG, JPG, WEBP, GIF...) o documentos (PDF, Word, Excel, ZIP...)
                </p>
                <input type="file" name="archivos_avance[]" id="input_avance_gen_archivos" multiple
                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar"
                       style="width: 100%; padding: 8px; border: 1.5px solid #fed7aa; border-radius: 6px; background: #fff7ed; font-size: 12px; font-weight: 600; color: #1e293b; cursor: pointer;">
            </div>

            <div style="text-align: right; border-top: 1.5px dashed #cbd5e1; padding-top: 14px;">
                <button type="button" class="btn-ver" style="background: #64748b; color: white; margin-right: 8px; font-weight: 700; padding: 9px 16px; border-radius: 6px;" onclick="cerrarModal('modalActualizarAvanceGenerico')">Cancelar</button>
                <button type="button" class="btn-form" id="btnSubmitAvanceGen" onclick="guardarAvanceGenerico(event)" style="background: #c2410c; color: white; padding: 9px 20px; font-size: 13px; font-weight: 800; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); cursor: pointer;">
                    <i class="bi bi-check-circle-fill me-1"></i> Guardar Avance y Nota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL VER DETALLE DE ACTIVIDAD (INFORMATIVO PURO CON BITÁCORA Y BOTÓN DE COMPLETAR) -->
<!-- ============================================== -->
<div id="modalVerDetalle" class="rh-modal">
    <div class="rh-modal-content" id="contentModalVerDetalle" style="max-width: 680px; background: #ffffff; border: 3px solid #15803d; box-shadow: 0 10px 25px rgba(0,0,0,0.25); transition: all 0.3s; padding: 24px; max-height: 85vh; overflow-y: auto;">
        <span class="rh-modal-close" onclick="cerrarModal('modalVerDetalle')">&times;</span>
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px;">
            <div>
                <span id="badgeTipoDetalle" style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: inline-block;">Actividad</span>
                <h2 id="tituloDetalle" style="margin: 4px 0; color: #1e293b; font-size: 18px; font-weight: 800;">Título de la actividad</h2>
            </div>
            <span id="badgeEstadoDetalle" style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800;">Estado</span>
        </div>

        <!-- Grid de Especificaciones -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #f8fafc; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 16px;">
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; display: block;">EMPLEADO RESPONSABLE</span>
                <strong id="empleadoDetalle" style="font-size: 13px; color: #1e293b;">-</strong>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; display: block;">ASIGNADO POR / REGISTRADO POR</span>
                <strong id="creadorDetalle" style="font-size: 13px; color: #1e293b;">-</strong>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; display: block;">FECHA / PLAZO DE ENTREGA</span>
                <strong id="fechaDetalle" style="font-size: 13px; color: #1e293b;">-</strong>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; display: block;">PRIORIDAD</span>
                <strong id="prioridadDetalle" style="font-size: 13px; color: #1e293b;">-</strong>
            </div>
        </div>

        <!-- Especificaciones / Descripción -->
        <div style="margin-bottom: 16px;">
            <label style="font-size: 12px; font-weight: 800; color: #475569; display: block; margin-bottom: 4px;">DESCRIPCIÓN Y ESPECIFICACIONES:</label>
            <div id="descripcionDetalle" style="background: #ffffff; border: 1.5px solid #cbd5e1; padding: 12px; border-radius: 8px; font-size: 13px; color: #334155; min-height: 50px; white-space: pre-wrap;">-</div>
        </div>

        <!-- Dependencia o Vinculación -->
        <div id="bloqueDependenciaDetalle" style="display: none; margin-bottom: 16px; background: #eff6ff; border: 1.5px solid #93c5fd; padding: 12px; border-radius: 8px;">
            <div style="font-weight: 800; font-size: 11px; color: #1e3a8a; margin-bottom: 4px; text-transform: uppercase; display: flex; align-items: center; gap: 5px;">
                <i class="bi bi-person-fill" style="color: #2563eb;"></i> Dependencia - Responsable y Motivo:
            </div>
            <div style="font-size: 13px; color: #1e293b; font-weight: 800;" id="depRespDetalle">-</div>
            <div style="font-size: 12px; color: #475569; font-style: italic; margin-top: 2px;" id="depMotivoDetalle"></div>
        </div>

        <!-- Extra Info (Motivo & Resultado para Imprevistas / Personales) -->
        <div id="bloqueExtraImprevistaDetalle" style="display: none; margin-bottom: 16px;">
            <div style="margin-bottom: 10px;">
                <label style="font-size: 12px; font-weight: 800; color: #9a3412; display: block; margin-bottom: 4px;">MOTIVO / DETALLE PERSONAL:</label>
                <div id="motivoDetalle" style="background: #fff7ed; border: 1px solid #fed7aa; padding: 10px; border-radius: 8px; font-size: 13px; color: #9a3412;">-</div>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 800; color: #9a3412; display: block; margin-bottom: 4px;">RESULTADO OBTENIDO:</label>
                <div id="resultadoDetalle" style="background: #fff7ed; border: 1px solid #fed7aa; padding: 10px; border-radius: 8px; font-size: 13px; color: #9a3412;">-</div>
            </div>
        </div>

        <!-- SECCIÓN NUEVA: BITÁCORA Y NOTAS DE PROGRESO POR FECHA -->
        <div style="margin-top: 20px; border-top: 2px solid #e2e8f0; padding-top: 16px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-clock-history" style="color: #2563eb;"></i> Bitácora e Historial de Avances Registrados
            </h3>
            <div id="containerBitacoraHistorial" style="display: flex; flex-direction: column; gap: 10px; max-height: 220px; overflow-y: auto; padding-right: 4px;">
                <!-- Avances cargados dinámicamente con JS -->
            </div>
        </div>

        <!-- BOTÓN DIRECTO DE ACCIÓN: COMPLETADA / SE COMPLETÓ -->
        <div id="bloqueAccionCompletar" style="border-radius: 10px; padding: 16px; margin-top: 16px; text-align: center; transition: all 0.2s;">
            <form action="" method="POST" id="formMarcarCompletadaDirecto">
                @csrf
                @method('PUT')
                <input type="hidden" name="porcentaje_avance" value="100">
                <input type="hidden" name="estado" value="finalizada">
                <button type="submit" id="btnMarcarCompletadaText" class="btn-form" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                    <i class="bi bi-check-circle-fill me-2"></i> Marcar como Completada
                </button>
            </form>
            <div id="badgeYaFinalizadaText" style="display: none; padding: 10px; font-weight: 800; font-size: 14px; border-radius: 8px;">
                <i class="bi bi-check-all me-1"></i> Esta actividad ya se encuentra completada al 100%.
            </div>
        </div>

        <div style="margin-top: 20px; text-align: right; display: flex; justify-content: space-between; align-items: center;">
            @if($isBossOrAdmin)
                <button type="button" id="btnDevolverDesdeDetalle" class="btn-ver" style="background: #dc2626; color: white; padding: 9px 18px; font-weight: 700; border-radius: 6px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;" onclick="openDevolverModalFromDetalle()">
                    <i class="bi bi-arrow-return-left"></i> Devolver / Solicitar Corrección
                </button>
            @else
                <div></div>
            @endif
            <button type="button" class="btn-ver" style="background: #64748b; color: white; padding: 9px 18px; font-weight: 700; border-radius: 6px;" onclick="cerrarModal('modalVerDetalle')">Cerrar</button>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: DEVOLVER ACTIVIDAD Y SOLICITAR CORRECCIÓN (SOLO JEFE / ADMIN) -->
<!-- ============================================== -->
<div id="modalDevolverActividad" class="rh-modal">
    <div class="rh-modal-content" style="max-width: 520px; background: #ffffff; border: 3px solid #dc2626; border-radius: 12px; box-shadow: 0 10px 25px rgba(220,38,38,0.25); padding: 22px;">
        <span class="rh-modal-close" onclick="cerrarModal('modalDevolverActividad')">&times;</span>
        
        <div style="margin-bottom: 16px;">
            <h2 style="margin: 0 0 6px 0; color: #991b1b; font-size: 19px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-arrow-return-left" style="color: #dc2626;"></i> Devolver Actividad al Empleado
            </h2>
            <p id="devolverTituloText" style="margin: 0; font-size: 13px; color: #475569; font-weight: 700;">Título de la actividad</p>
        </div>

        <form action="" method="POST" id="formDevolverActividad" onsubmit="guardarDevolucionActividad(event)">
            @csrf
            
            <!-- AJUSTAR PORCENTAJE REAL ESTIMADO POR EL JEFE -->
            <div style="background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-weight: 800; font-size: 13px; color: #991b1b; margin: 0;">
                        <i class="bi bi-sliders me-1"></i> Porcentaje Real Estimado por el Jefe (%):
                    </label>
                    <span style="font-weight: 900; font-size: 16px; color: #dc2626;" id="display_devolver_pct_val">50%</span>
                </div>
                <input type="range" name="porcentaje_avance" id="input_devolver_pct_range" min="0" max="95" step="5" value="50"
                       oninput="document.getElementById('display_devolver_pct_val').innerText = this.value + '%'"
                       style="width: 100%; accent-color: #dc2626; cursor: pointer; height: 8px;">
            </div>

            <!-- HORA DE LA DEVOLUCIÓN -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 800; font-size: 13px; color: #991b1b; display: block; margin-bottom: 6px;">
                    <i class="bi bi-clock-history me-1"></i> Hora de la Devolución *
                </label>
                <input type="time" name="hora_devolucion" id="input_devolver_hora" required
                       style="width: 100%; padding: 10px; border: 2px solid #dc2626; border-radius: 8px; font-family: inherit; font-weight: 600; color: #1e293b;">
            </div>

            <!-- INSTRUCCIONES Y OBSERVACIONES OBLIGATORIAS -->
            <div style="margin-bottom: 18px;">
                <label style="font-weight: 800; font-size: 13px; color: #991b1b; display: block; margin-bottom: 6px;">
                    Observaciones e Instrucciones de Corrección * (¿Qué faltó por hacer?)
                </label>
                <textarea name="comentario_jefe" id="input_devolver_comentario" rows="3" required placeholder="Ej: Te faltó realizar esto y esto para darla por completada..."
                          style="width: 100%; padding: 10px; border: 2px solid #dc2626; border-radius: 8px; font-family: inherit; box-sizing: border-box; background: white; font-weight: 500; color: #1e293b;"></textarea>
            </div>

            <div style="text-align: right; border-top: 1.5px dashed #cbd5e1; padding-top: 14px;">
                <button type="button" class="btn-ver" style="background: #64748b; color: white; margin-right: 8px; font-weight: 700; padding: 9px 16px; border-radius: 6px;" onclick="cerrarModal('modalDevolverActividad')">Cancelar</button>
                <button type="submit" class="btn-form" id="btnSubmitDevolver" style="background: #dc2626; color: white; padding: 9px 20px; font-size: 13px; font-weight: 800; border-radius: 6px; box-shadow: 0 4px 6px rgba(220,38,38,0.25);">
                    <i class="bi bi-arrow-return-left me-1"></i> Regresar Actividad con Observaciones
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL PRO: CONFIRMAR ELIMINACIÓN DE ACTIVIDAD -->
<!-- ============================================== -->
<div id="modalConfirmarEliminar" class="rh-modal">
    <div class="rh-modal-content" style="max-width: 450px; text-align: center; padding: 25px; border: 3px solid #ef4444; border-radius: 12px; box-shadow: 0 10px 25px rgba(239,68,68,0.25);">
        <i class="bi bi-exclamation-triangle-fill" style="font-size: 50px; color: #ef4444; display: block; margin-bottom: 12px;"></i>
        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 19px; font-weight: 800;">¿Estás seguro de eliminar esta actividad?</h3>
        <p style="color: #64748b; font-size: 13px; margin: 0 0 22px 0; line-height: 1.4;">Esta acción no se podrá deshacer. La actividad será eliminada permanentemente del sistema.</p>
        
        <form action="" method="POST" id="formEliminarActividad">
            @csrf
            @method('DELETE')
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="btn-ver" style="background: #64748b; color: white; padding: 10px 20px; font-weight: 700; border-radius: 8px; font-size: 13px;" onclick="cerrarModal('modalConfirmarEliminar')">Cancelar</button>
                <button type="submit" class="btn-ver" style="background: #ef4444; color: white; padding: 10px 20px; font-weight: 800; border-radius: 8px; font-size: 13px; box-shadow: 0 4px 6px rgba(239,68,68,0.25);">Eliminar Definitivamente</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT GLOBAL DEL MÓDULO DE ACTIVIDADES -->
<script>
window.APP_BASE_URL = "{{ url('/') }}";
window.IS_BOSS_OR_ADMIN = {{ $isBossOrAdmin ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    let formCrear = document.getElementById('formCrearActividad');
    if (formCrear) {
        formCrear.addEventListener('submit', function(e) {
            let tipo = document.getElementById('crear_tipo_actividad')?.value || 'asignada';
            if (tipo === 'asignada' && typeof actualizarSeleccionEmpleadosAsig === 'function') {
                actualizarSeleccionEmpleadosAsig();
            }

            let titulo = document.getElementById('crear_titulo')?.value.trim();
            let descripcion = document.getElementById('crear_descripcion')?.value.trim();
            let empleado = document.getElementById('crear_empleado_id')?.value;
            let prioridad = document.getElementById('crear_prioridad')?.value;

            if (!titulo || !descripcion || !empleado || (tipo === 'asignada' && !prioridad)) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos Obligatorios Incompletos',
                        html: '<p style="font-size:13px; color:#475569; margin-bottom:10px;">Por favor completa los campos requeridos (*):</p>' +
                              '<ul style="text-align:left; font-size:13px; font-weight:700; color:#dc2626; margin-top:6px; line-height:1.6;">' +
                              (!titulo ? '<li>• Título de la actividad *</li>' : '') +
                              (!descripcion ? '<li>• Descripción de la actividad *</li>' : '') +
                              (!empleado ? '<li>• Empleado asignado *</li>' : '') +
                              ((tipo === 'asignada' && !prioridad) ? '<li>• Prioridad * (Selecciona una prioridad)</li>' : '') +
                              '</ul>',
                        confirmButtonColor: '#15803d',
                        confirmButtonText: 'Entendido, completar campos'
                    });
                }
                return false;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Guardando actividad...',
                    text: 'Registrando la información en el sistema. Por favor espera un momento.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            }
        });
    }
});

function cerrarTodosLosModales() {
    document.querySelectorAll('.rh-modal').forEach(m => {
        m.classList.remove('active');
        m.style.display = 'none';
    });
}

function toggleNotasCompletada() {
    let chk = document.getElementById('chk_marcar_completada');
    let box = document.getElementById('box_notas_completada');
    let txt = document.getElementById('txt_notas_completada');
    if (chk && box && txt) {
        if (chk.checked) {
            box.style.display = 'block';
            txt.required = true;
        } else {
            box.style.display = 'none';
            txt.required = false;
        }
    }
}

function abrirModal(id) {
    cerrarTodosLosModales();
    let m = document.getElementById(id);
    if (m) {
        m.classList.add('active');
        m.style.display = 'flex';
    }
}

function cerrarModal(id) {
    let m = document.getElementById(id);
    if (m) {
        m.classList.remove('active');
        m.style.display = 'none';
    }
}

function updateImprevistoPorcentajeDisplay(val) {
    let disp = document.getElementById('display_porcentaje_imprevisto');
    if (disp) disp.innerText = `${val}%`;
    let hiddenEstado = document.getElementById('crear_estado_imprevisto');
    if (hiddenEstado) {
        if (parseInt(val) >= 100) {
            hiddenEstado.value = 'finalizada';
        } else if (parseInt(val) > 0) {
            hiddenEstado.value = 'en_proceso';
        } else {
            hiddenEstado.value = 'pendiente';
        }
    }
}

function updateAvanceGenDisplay(val) {
    let disp = document.getElementById('display_avance_gen_val');
    if (disp) disp.innerText = `${val}%`;
}

function setAvanceGenQuickVal(val) {
    let range = document.getElementById('input_avance_gen_range');
    if (range) {
        range.value = val;
        updateAvanceGenDisplay(val);
    }
}

function openAvanceGenericoModal(btn, event) {
    if (event) event.stopPropagation();
    if (!btn) return;
    let dataset = btn.dataset;
    let id = dataset.id;
    let tipo = dataset.tipo || 'asignada';
    if (!id) return;

    let baseUrl = window.APP_BASE_URL || '';
    let form = document.getElementById('formAvanceGenerico');
    let titleHeader = document.getElementById('titleAvanceGenHeader');
    let content = document.getElementById('contentModalAvanceGen');
    let submitBtn = document.getElementById('btnSubmitAvanceGen');
    let hiddenTipo = document.getElementById('avance_tipo_gen_hidden');

    let boxSlider = document.getElementById('boxAvanceSliderGen');
    let labelSlider = document.getElementById('labelPorcentajeAvanceGen');
    let iconSlider = document.getElementById('iconPorcentajeAvanceGen');
    let displayVal = document.getElementById('display_avance_gen_val');
    let rangeInput = document.getElementById('input_avance_gen_range');
    let quickBtns  = document.querySelectorAll('.quick-btn-avance');

    let boxRutina = document.getElementById('boxAvanceRutinaDividido');
    let labelNota = document.getElementById('labelNotaAvanceGen');
    let inputNota = document.getElementById('input_avance_gen_resultado');

    if (hiddenTipo) hiddenTipo.value = tipo;

    let themeColor = '#15803d';
    let textColor  = '#166534';
    let bgLight    = '#f0fdf4';
    let borderLight = '#86efac';

    if (tipo === 'imprevista') {
        themeColor = '#c2410c';
        textColor  = '#9a3412';
        bgLight    = '#fff7ed';
        borderLight = '#fed7aa';
    } else if (tipo === 'rutinaria') {
        themeColor = '#1e40af';
        textColor  = '#1e3a8a';
        bgLight    = '#eff6ff';
        borderLight = '#bfdbfe';
    }

    if (content)   { content.style.background = bgLight; content.style.border = `3px solid ${themeColor}`; }
    if (submitBtn) { submitBtn.style.background = themeColor; }
    if (labelNota) { labelNota.style.color = textColor; }
    if (inputNota) { inputNota.style.border = `2px solid ${themeColor}`; }

    let boxHorario = document.getElementById('boxAvanceHorarioGen');
    let labelHorarioHead = document.getElementById('labelAvanceHorarioHeader');
    let iconHorarioHead = document.getElementById('iconAvanceHorarioHeader');
    let labelHoraInicio = document.getElementById('labelHoraInicioGen');
    let labelHoraFin = document.getElementById('labelHoraFinGen');
    let inputHoraInicio = document.getElementById('avance_hora_inicio');
    let inputHoraFin = document.getElementById('avance_hora_fin');

    if (boxHorario) { boxHorario.style.border = `2px solid ${themeColor}`; }
    if (labelHorarioHead) { labelHorarioHead.style.color = textColor; }
    if (iconHorarioHead) { iconHorarioHead.style.color = themeColor; }
    if (labelHoraInicio) { labelHoraInicio.style.color = themeColor; }
    if (labelHoraFin) { labelHoraFin.style.color = themeColor; }
    if (inputHoraInicio) { inputHoraInicio.style.border = `1.5px solid ${borderLight}`; }
    if (inputHoraFin) { inputHoraFin.style.border = `1.5px solid ${borderLight}`; }

    let boxArchivo    = document.getElementById('boxArchivoAvanceGen');
    let labelAdjunto  = document.getElementById('labelAdjuntoAvanceGen');
    let inputArchivos = document.getElementById('input_avance_gen_archivos');
    let boxHoras      = document.getElementById('boxHorasTrabajadasGen');
    let labelHoras    = document.getElementById('labelHorasTrabajadasGen');
    let inputHoras    = document.getElementById('input_avance_gen_horas');

    if (boxArchivo)   { boxArchivo.style.border = `2px dashed ${themeColor}`; boxArchivo.style.background = '#ffffff'; }
    if (labelAdjunto) { 
        labelAdjunto.style.color = textColor; 
        let iconP = labelAdjunto.querySelector('i');
        if (iconP) iconP.style.color = themeColor;
    }
    if (inputArchivos){ inputArchivos.style.border = `1.5px solid ${borderLight}`; inputArchivos.style.background = bgLight; }

    if (boxHoras)     { boxHoras.style.display = (tipo === 'asignada') ? 'block' : 'none'; }
    if (labelHoras)   { labelHoras.style.color = textColor; }
    if (inputHoras)   { inputHoras.style.border = `2px solid ${themeColor}`; }

    if (form) {
        if (tipo === 'imprevista') {
            form.action = `${baseUrl}/actividades-imprevistas/${id}`;
            if (titleHeader) { titleHeader.style.color = textColor; titleHeader.innerHTML = `<i class="bi bi-graph-up-arrow" style="color: ${themeColor};"></i> Registrar Avance de Actividad Personal`; }
            if (boxSlider) { boxSlider.style.border = `2px solid ${themeColor}`; boxSlider.style.display = 'block'; }
            if (labelSlider) { labelSlider.style.color = textColor; labelSlider.innerHTML = `<i class="bi bi-percent" style="color: ${themeColor};"></i> Porcentaje de Avance`; }
            if (iconSlider) { iconSlider.style.color = themeColor; }
            if (displayVal) { displayVal.style.color = themeColor; }
            if (rangeInput) { rangeInput.style.accentColor = themeColor; }

            quickBtns.forEach((qb, idx) => {
                if (idx === 3) {
                    qb.style.background = themeColor; qb.style.borderColor = themeColor; qb.style.color = '#ffffff';
                } else {
                    qb.style.background = bgLight; qb.style.borderColor = borderLight; qb.style.color = textColor;
                }
            });

            if (boxRutina) boxRutina.style.display = 'none';
        } else if (tipo === 'rutinaria') {
            form.action = `${baseUrl}/rutinas/${id}`;
            if (titleHeader) { titleHeader.style.color = textColor; titleHeader.innerHTML = `<i class="bi bi-arrow-repeat" style="color: ${themeColor};"></i> Registrar Ejecución de Rutina`; }
            if (boxSlider) boxSlider.style.display = 'none';
            if (boxRutina) { boxRutina.style.border = `2px solid ${themeColor}`; boxRutina.style.display = 'block'; }
            let labelRut = document.getElementById('labelRutinaAvanceHeader');
            if (labelRut) labelRut.style.color = textColor;

            let veces = parseInt(dataset.veces || 1);
            let ejecHoy = parseInt(dataset.ejecuciones || 0);
            let container = document.getElementById('containerOpcionesRutinaDiv');
            if (container) {
                container.innerHTML = '';
                for (let i = 1; i <= veces; i++) {
                    let pct = Math.round((i / veces) * 100);
                    let isChecked = i <= ejecHoy ? 'checked' : '';
                    container.innerHTML += `
                        <label style="display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 10px 14px; border-radius: 8px; border: 1.5px solid ${borderLight}; cursor: pointer; font-size: 13px; font-weight: 700; color: ${textColor};">
                            <input type="radio" name="cantidad_ejecuciones_rutina" value="${i}" ${isChecked} onchange="setAvanceGenQuickVal(${pct})" style="accent-color: ${themeColor}; width: 18px; height: 18px;">
                            <span>Ejecución ${i} de ${veces} — <strong>${pct}%</strong></span>
                        </label>
                    `;
                }
            }
        } else { // asignada
            form.action = `${baseUrl}/actividades/${id}`;
            if (titleHeader) { titleHeader.style.color = textColor; titleHeader.innerHTML = `<i class="bi bi-graph-up-arrow" style="color: ${themeColor};"></i> Registrar Avance de Actividad Asignada`; }
            if (boxSlider) { boxSlider.style.border = `2px solid ${themeColor}`; boxSlider.style.display = 'block'; }
            if (labelSlider) { labelSlider.style.color = textColor; labelSlider.innerHTML = `<i class="bi bi-percent" style="color: ${themeColor};"></i> Porcentaje de Avance <span style="font-size: 11px; font-weight: 600; color: #475569; margin-left: 4px;">(A consideración del jefe)</span>`; }
            if (iconSlider) { iconSlider.style.color = themeColor; }
            if (displayVal) { displayVal.style.color = themeColor; }
            if (rangeInput) { rangeInput.style.accentColor = themeColor; }

            quickBtns.forEach((qb, idx) => {
                if (idx === 3) {
                    qb.style.background = themeColor; qb.style.borderColor = themeColor; qb.style.color = '#ffffff';
                } else {
                    qb.style.background = bgLight; qb.style.borderColor = borderLight; qb.style.color = textColor;
                }
            });

            if (boxRutina) boxRutina.style.display = 'none';
        }
    }

    let tituloText = document.getElementById('avanceGenTituloText');
    if (tituloText) tituloText.innerText = dataset.titulo || 'Actividad';

    let avanceVal = dataset.avance !== undefined ? parseInt(dataset.avance) : 50;
    let range = document.getElementById('input_avance_gen_range');
    if (range) range.value = avanceVal;
    updateAvanceGenDisplay(avanceVal);

    let resInput = document.getElementById('input_avance_gen_resultado');
    if (resInput) {
        resInput.value = '';
        if (typeof setupAutoNumbering === 'function') {
            setupAutoNumbering('input_avance_gen_resultado');
        }
    }

    abrirModal('modalActualizarAvanceGenerico');
}

function confirmarEliminarActividad(actionUrl, event) {
    if (event) event.stopPropagation();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Estás seguro de eliminar esta actividad?',
            text: 'Esta acción no se puede deshacer. La actividad será eliminada permanentemente del sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('formEliminarActividad');
                if (form) {
                    form.action = actionUrl;
                    Swal.fire({
                        title: 'Eliminando actividad...',
                        text: 'Procesando eliminación en el sistema.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            }
        });
    } else {
        let form = document.getElementById('formEliminarActividad');
        if (form) form.action = actionUrl;
        abrirModal('modalConfirmarEliminar');
    }
}

function openShowModalFromRow(elem) {
    if (!elem) return;
    let dataset = elem.dataset;
    let tipo = dataset.tipo || 'asignada';
    let id = dataset.id;
    if (!id) return;
    window.currentDetailDataset = dataset;

    let baseUrl = window.APP_BASE_URL || '';
    let content = document.getElementById('contentModalVerDetalle');
    let badgeTipo = document.getElementById('badgeTipoDetalle');
    let badgeEstado = document.getElementById('badgeEstadoDetalle');
    let titulo = document.getElementById('tituloDetalle');
    let empleado = document.getElementById('empleadoDetalle');
    let creador = document.getElementById('creadorDetalle');
    let fecha = document.getElementById('fechaDetalle');
    let prioridad = document.getElementById('prioridadDetalle');
    let descripcion = document.getElementById('descripcionDetalle');
    let bloqueExtraImp = document.getElementById('bloqueExtraImprevistaDetalle');
    let containerBitacora = document.getElementById('containerBitacoraHistorial');

    let formCompletar = document.getElementById('formMarcarCompletadaDirecto');
    let btnCompletar  = document.getElementById('btnMarcarCompletadaText');
    let badgeYaFinalizada = document.getElementById('badgeYaFinalizadaText');

    if (titulo) titulo.innerText = dataset.titulo || 'Sin título';
    if (empleado) empleado.innerText = dataset.empleadonombre || '-';
    if (creador) creador.innerText = dataset.creadornombre || '-';
    if (fecha) fecha.innerText = dataset.fechadisplay || '-';
    if (prioridad) prioridad.innerText = (dataset.prioridad || 'media').toUpperCase();
    if (descripcion) descripcion.innerText = dataset.descripcion || 'Sin descripción';

    let bloqueDep = document.getElementById('bloqueDependenciaDetalle');
    let depResp   = document.getElementById('depRespDetalle');
    let depMotivo = document.getElementById('depMotivoDetalle');

    if (dataset.depresp || dataset.depmotivo) {
        if (bloqueDep) bloqueDep.style.display = 'block';
        if (depResp) depResp.innerText = dataset.depresp || 'No especificado';
        if (depMotivo) depMotivo.innerText = dataset.depmotivo ? `Motivo: ${dataset.depmotivo}` : '';
    } else {
        if (bloqueDep) bloqueDep.style.display = 'none';
    }

    let est = dataset.estado || 'pendiente';
    if (badgeEstado) {
        badgeEstado.innerText = est.replace('_', ' ').toUpperCase();
        badgeEstado.style.background = est === 'finalizada' ? '#dcfce7' : (est === 'atrasada' ? '#fee2e2' : '#fef3c7');
        badgeEstado.style.color = est === 'finalizada' ? '#166534' : (est === 'atrasada' ? '#991b1b' : '#92400e');
    }

    let routeUpdate = `${baseUrl}/actividades/${id}`;
    if (tipo === 'imprevista') routeUpdate = `${baseUrl}/actividades-imprevistas/${id}`;
    if (tipo === 'rutinaria') routeUpdate = `${baseUrl}/rutinas/${id}`;

    if (formCompletar) formCompletar.action = routeUpdate;

    if (tipo === 'imprevista') {
        if (content) { content.style.border = '3px solid #c2410c'; }
        if (badgeTipo) { badgeTipo.innerText = 'PERSONAL'; badgeTipo.style.background = '#ffedd5'; badgeTipo.style.color = '#c2410c'; }
        if (btnCompletar) {
            btnCompletar.style.background = '#c2410c';
            btnCompletar.style.color = 'white';
            btnCompletar.innerHTML = `<i class="bi bi-question-circle-fill me-2"></i> ¿Se completó? (Marcar Solucionada)`;
        }
        if (bloqueExtraImp) {
            bloqueExtraImp.style.display = 'block';
            document.getElementById('motivoDetalle').innerText = dataset.motivo || 'N/A';
            document.getElementById('resultadoDetalle').innerText = dataset.resultado || 'N/A';
        }
    } else if (tipo === 'rutinaria') {
        if (content) { content.style.border = '3px solid #1e40af'; }
        if (badgeTipo) { badgeTipo.innerText = 'RUTINARIA'; badgeTipo.style.background = '#dbeafe'; badgeTipo.style.color = '#1e40af'; }
        if (btnCompletar) {
            btnCompletar.style.background = '#1e40af';
            btnCompletar.style.color = 'white';
            btnCompletar.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Marcar Rutina Completada`;
        }
        if (bloqueExtraImp) bloqueExtraImp.style.display = 'none';
    } else {
        if (content) { content.style.border = '3px solid #15803d'; }
        if (badgeTipo) { badgeTipo.innerText = 'ASIGNADA'; badgeTipo.style.background = '#dcfce7'; badgeTipo.style.color = '#15803d'; }
        if (btnCompletar) {
            btnCompletar.style.background = '#15803d';
            btnCompletar.style.color = 'white';
            btnCompletar.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Marcar como Completada`;
        }
        if (bloqueExtraImp) bloqueExtraImp.style.display = 'none';
    }

    if (est === 'finalizada' || parseInt(dataset.avance || 0) >= 100) {
        if (formCompletar) formCompletar.style.display = 'none';
        if (badgeYaFinalizada) {
            badgeYaFinalizada.style.display = 'block';
            badgeYaFinalizada.style.background = '#dcfce7';
            badgeYaFinalizada.style.color = '#166534';
        }
    } else {
        if (formCompletar) formCompletar.style.display = 'block';
        if (badgeYaFinalizada) badgeYaFinalizada.style.display = 'none';
    }

    // CARGAR BITÁCORA / HISTORIAL DE AVANCES CON FECHA Y NOTAS
    if (containerBitacora) {
        containerBitacora.innerHTML = '';
        let historialData = [];
        try {
            if (dataset.historial) {
                historialData = JSON.parse(dataset.historial);
            }
        } catch(e) { console.error("Error parseando historial:", e); }

        if (historialData && historialData.length > 0) {
            historialData.forEach(item => {
                let badgeBg = item.porcentaje >= 100 ? '#dcfce7' : '#fef3c7';
                let badgeColor = item.porcentaje >= 100 ? '#166534' : '#92400e';
                containerBitacora.innerHTML += `
                    <div style="background: #f8fafc; border-left: 4px solid ${badgeColor}; padding: 10px 14px; border-radius: 8px; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-size: 12px; font-weight: 800; color: #1e293b;">
                                <i class="bi bi-person-fill me-1"></i> ${item.empleado || 'Usuario'}
                            </span>
                            <span style="background: ${badgeBg}; color: ${badgeColor}; font-weight: 800; font-size: 11px; padding: 2px 8px; border-radius: 12px;">
                                ${item.porcentaje}% Avance
                            </span>
                        </div>
                        <p style="margin: 0 0 4px 0; font-size: 13px; color: #334155; font-weight: 500;">
                            "${item.nota || 'Sin notas registadas'}"
                        </p>
                        <span style="font-size: 10px; font-weight: 700; color: #64748b; display: block;">
                            <i class="bi bi-clock me-1"></i> Registrado el ${item.fecha || 'N/A'}
                        </span>
                    </div>
                `;
            });
        } else {
            containerBitacora.innerHTML = `
                <div style="text-align: center; padding: 15px; color: #64748b; font-size: 12px; font-style: italic;">
                    Aún no hay avances registrados para esta actividad.
                </div>
            `;
        }
    }

    abrirModal('modalVerDetalle');
}

function toggleSencillaNueva(val) {
    let bloqueAvanzado = document.getElementById('bloque_avanzado_asignada');
    if (bloqueAvanzado) {
        bloqueAvanzado.style.display = (val === 'no') ? 'block' : 'none';
    }
}

function toggleCrearFechas(val) {
    let sec = document.getElementById('seccion_fechas_asignada');
    if (sec) {
        sec.style.display = (val === 'varios_dias') ? 'grid' : 'none';
    }
}

function toggleColaboradoresAsig(val) {
    let bloque = document.getElementById('bloque_lista_colaboradores_asig');
    if (bloque) bloque.style.display = (val === 'si') ? 'block' : 'none';
}

function toggleColaboradoresImp(val) {
    let bloque = document.getElementById('bloque_lista_colaboradores_imp');
    if (bloque) bloque.style.display = (val === 'si') ? 'block' : 'none';
}

function calcularDiferenciaHorasCreacion() {
    let ini = document.getElementById('crear_hora_inicio')?.value;
    let fin = document.getElementById('crear_hora_fin')?.value;
    let inputHoras = document.getElementById('crear_horas_invertidas');

    if (!ini || !fin || !inputHoras) return;

    let [h1, m1] = ini.split(':').map(Number);
    let [h2, m2] = fin.split(':').map(Number);

    let totalMin1 = (h1 * 60) + m1;
    let totalMin2 = (h2 * 60) + m2;

    if (totalMin2 > totalMin1) {
        let diffMin = totalMin2 - totalMin1;
        let diffHoras = parseFloat((diffMin / 60).toFixed(2));
        inputHoras.value = diffHoras;
    }
}

function toggleImprevistaHoras() {
    let cb = document.getElementById('crear_imp_sin_hora');
    let box = document.getElementById('imprevista_horas_box');
    let inputHoras = document.getElementById('crear_horas_invertidas');
    if (cb && cb.checked) {
        if (box) box.style.display = 'none';
        if (inputHoras) inputHoras.value = 0;
    } else {
        if (box) box.style.display = 'grid';
        calcImpHorasInvertidas();
    }
}

function calcImpHorasInvertidas() {
    let h1 = document.getElementById('crear_imp_hora_inicio');
    let h2 = document.getElementById('crear_imp_hora_fin');
    let inputHoras = document.getElementById('crear_horas_invertidas');

    if (!h1 || !h2 || !inputHoras) return;
    if (!h1.value || !h2.value) return;

    let p1 = h1.value.split(':');
    let p2 = h2.value.split(':');

    let d1 = new Date(2000, 0, 1, parseInt(p1[0]), parseInt(p1[1]));
    let d2 = new Date(2000, 0, 1, parseInt(p2[0]), parseInt(p2[1]));

    if (d2 < d1) d2.setDate(d2.getDate() + 1);

    let diffMs = d2 - d1;
    let diffHoras = diffMs / (1000 * 60 * 60);

    inputHoras.value = parseFloat(diffHoras.toFixed(2));
}

function toggleDependenciaAsig(val) {
    let bloque = document.getElementById('bloque_dependencia_asig');
    if (bloque) {
        bloque.style.display = (val === 'si') ? 'grid' : 'none';
    }
    if (val === 'no') {
        let inputArea = document.getElementById('crear_dependencia_area');
        let inputResp = document.getElementById('crear_dependencia_responsable');
        let inputMot  = document.getElementById('crear_dependencia_motivo');
        if (inputArea) inputArea.value = '';
        if (inputResp) inputResp.value = '';
        if (inputMot)  inputMot.value = '';
    }
}

function toggleTipoPlazoSub(val) {
    let secFechas = document.getElementById('seccion_fechas_asignada');
    let boxHorario = document.getElementById('boxHorarioEstimado');

    if (val === 'fecha') {
        if (secFechas) secFechas.style.display = 'grid';
        if (boxHorario) boxHorario.style.display = 'none';
    } else if (val === 'hora') {
        if (secFechas) secFechas.style.display = 'none';
        if (boxHorario) boxHorario.style.display = 'grid';
    } else if (val === 'ambos') {
        if (secFechas) secFechas.style.display = 'grid';
        if (boxHorario) boxHorario.style.display = 'grid';
    }
}

function toggleTienePlazo(val) {
    let subBox = document.getElementById('sub_box_tipo_plazo');

    if (val === 'si') {
        if (subBox) subBox.style.display = 'block';
        let selTipo = document.querySelector('input[name="tipo_plazo"]:checked')?.value || 'fecha';
        toggleTipoPlazoSub(selTipo);
    } else {
        if (subBox) subBox.style.display = 'none';
    }
}

function actualizarSeleccionEmpleadosAsig() {
    let checkedElements = document.querySelectorAll('input[name="empleados_asig_checkboxes[]"]:checked');
    let containerHidden = document.getElementById('container_hidden_empleados_compartidos');
    let inputEmpleadoId = document.getElementById('crear_empleado_id');
    let inputColaboro = document.getElementById('crear_colaboro_asig_radio');

    if (!containerHidden || !inputEmpleadoId) return;

    containerHidden.innerHTML = '';

    if (checkedElements.length === 0) {
        inputEmpleadoId.value = '{{ auth()->id() }}';
        if (inputColaboro) inputColaboro.value = 'no';
        return;
    }

    inputEmpleadoId.value = checkedElements[0].value;

    if (checkedElements.length > 1) {
        if (inputColaboro) inputColaboro.value = 'si';
        checkedElements.forEach((el, index) => {
            if (index > 0) {
                let hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'empleados_compartidos[]';
                hidden.value = el.value;
                containerHidden.appendChild(hidden);
            }
        });
    } else {
        if (inputColaboro) inputColaboro.value = 'no';
    }
}

function seleccionarTipoCreacion(tipo) {
    document.getElementById('crear_tipo_actividad').value = tipo;
    
    let boxMultiEmp = document.getElementById('box_multiselect_empleados_asig');
    let boxSingleEmp = document.getElementById('box_single_select_empleado');

    if (tipo === 'asignada' || tipo === 'rutinaria') {
        if (boxMultiEmp) boxMultiEmp.style.display = 'block';
        if (boxSingleEmp) boxSingleEmp.style.display = 'none';
        actualizarSeleccionEmpleadosAsig();
    } else if (tipo === 'imprevista') {
        if (boxMultiEmp) boxMultiEmp.style.display = 'none';
        if (boxSingleEmp) boxSingleEmp.style.display = 'none';
    }
    
    let modalContent = document.getElementById('modalCrearContent');
    let headerTitle  = document.getElementById('crearModalHeaderTitle');
    let submitBtn    = document.getElementById('btnSubmitCrear');
    let subtitle     = document.getElementById('crearModalSubtitle');
    let labelEmp     = document.getElementById('labelEmpleadoCrear');
    let selectEmp    = document.getElementById('crear_empleado_id');
    let labelDirigido= document.getElementById('labelDirigidoCrear');
    let selectDirigido=document.getElementById('crear_dirigido_a_id');
    let labelTit     = document.getElementById('labelTituloCrear');
    let inputTitulo  = document.getElementById('crear_titulo');
    
    let boxPlazo     = document.getElementById('boxPreguntaTienePlazo');
    let labelPlazo   = document.getElementById('labelPreguntaTienePlazo');
    let iconPlazo    = document.getElementById('iconPreguntaTienePlazo');
    let radioSi      = document.getElementById('labelPlazoSi');
    let radioNo      = document.getElementById('labelPlazoNo');
    let chkRadioSi   = document.getElementById('crear_plazo_si');
    let chkRadioNo   = document.getElementById('crear_plazo_no');

    let boxHorario   = document.getElementById('boxHorarioEstimado');
    let labelHoraIni = document.getElementById('labelHoraInicio');
    let inputHoraIni = document.getElementById('crear_hora_inicio');
    let labelHoraFin = document.getElementById('labelHoraFin');
    let inputHoraFin = document.getElementById('crear_hora_fin');

    let labelDepArea = document.getElementById('labelDepArea');
    let inputDepArea = document.getElementById('crear_dependencia_area');
    let labelDepResp = document.getElementById('labelDepResp');
    let inputDepResp = document.getElementById('crear_dependencia_responsable');

    let labelDesc    = document.getElementById('label_descripcion');
    let inputDesc    = document.getElementById('crear_descripcion');

    let labelAcciones= document.getElementById('labelAccionesRealizadas');
    let inputAcciones= document.getElementById('crear_acciones_realizadas');

    let boxObs       = document.getElementById('boxObservaciones');
    let labelObs     = document.getElementById('labelObservaciones');
    let inputObs     = document.getElementById('crear_observaciones');

    let boxPermitir  = document.getElementById('boxPermitirAvance');

    let btnAsig = document.getElementById('btnTipoAsignada');
    let btnImp  = document.getElementById('btnTipoImprevista');
    let btnRut  = document.getElementById('btnTipoRutinaria');

    let form = document.getElementById('formCrearActividad');
    let secAsig = document.getElementById('seccion_campos_asignada');
    let secImp  = document.getElementById('seccion_campos_imprevista');
    let secRut  = document.getElementById('seccion_campos_rutinaria');

    let inputMotivo = document.getElementById('crear_motivo');
    let inputResultado = document.getElementById('crear_resultado_obtenido');

    // Reset styles on top buttons
    [btnAsig, btnImp, btnRut].forEach(b => {
        if(b) {
            b.style.border = '2px solid #cbd5e1';
            b.style.background = '#ffffff';
            b.style.color = '#475569';
        }
    });

    let themeColor  = '#15803d';
    let textColor   = '#166534';
    let bgLight     = '#f0fdf4';
    let borderLight = '#86efac';

    if (tipo === 'imprevista') {
        themeColor  = '#c2410c';
        textColor   = '#9a3412';
        bgLight     = '#fff7ed';
        borderLight = '#fed7aa';
    } else if (tipo === 'rutinaria') {
        themeColor  = '#1e40af';
        textColor   = '#1e3a8a';
        bgLight     = '#eff6ff';
        borderLight = '#bfdbfe';
    }

    if (modalContent) { modalContent.style.background = bgLight; modalContent.style.border = `3px solid ${themeColor}`; }
    if (submitBtn)    { submitBtn.style.background = themeColor; }

    if (labelEmp)     { labelEmp.style.color = textColor; }
    if (selectEmp)    { selectEmp.style.border = `2px solid ${themeColor}`; }
    if (labelDirigido){ labelDirigido.style.color = textColor; }
    if (selectDirigido){ selectDirigido.style.border = `2px solid ${themeColor}`; }

    if (labelTit)     { labelTit.style.color = textColor; }
    if (inputTitulo)  { inputTitulo.style.border = `2px solid ${themeColor}`; }

    if (boxPlazo)     { 
        boxPlazo.style.display = (tipo === 'rutinaria') ? 'none' : 'block'; 
        boxPlazo.style.background = '#ffffff'; 
        boxPlazo.style.border = `2px solid ${themeColor}`; 
    }
    if (labelPlazo)   { labelPlazo.style.color = textColor; }
    if (iconPlazo)    { iconPlazo.style.color = themeColor; }
    if (radioSi)      { radioSi.style.color = textColor; }
    if (radioNo)      { radioNo.style.color = textColor; }
    if (chkRadioSi)   { chkRadioSi.style.accentColor = themeColor; }
    if (chkRadioNo)   { chkRadioNo.style.accentColor = themeColor; }

    if (boxHorario)   { boxHorario.style.background = bgLight; boxHorario.style.border = `1.5px solid ${borderLight}`; }
    if (labelHoraIni) { labelHoraIni.style.color = textColor; }
    if (inputHoraIni) { inputHoraIni.style.border = `1.5px solid ${themeColor}`; }
    if (labelHoraFin) { labelHoraFin.style.color = textColor; }
    if (inputHoraFin) { inputHoraFin.style.border = `1.5px solid ${themeColor}`; }

    if (tipo === 'rutinaria') {
        if (boxHorario) boxHorario.style.display = 'none';
    } else {
        let valPlazo = document.querySelector('input[name="tiene_plazo"]:checked')?.value || 'si';
        toggleTienePlazo(valPlazo);
    }

    if (labelDepArea) { labelDepArea.style.color = textColor; }
    if (inputDepArea) { inputDepArea.style.border = `1.5px solid ${themeColor}`; }
    if (labelDepResp) { labelDepResp.style.color = textColor; }
    if (inputDepResp) { inputDepResp.style.border = `1.5px solid ${themeColor}`; }

    if (labelDesc)    { labelDesc.style.color = textColor; }
    if (inputDesc)    { inputDesc.style.border = `2px solid ${themeColor}`; }

    if (labelAcciones){ labelAcciones.innerText = 'Acciones a realizar'; labelAcciones.style.color = textColor; }
    if (inputAcciones){ inputAcciones.style.border = `1.5px solid ${themeColor}`; }

    if (boxObs)       { boxObs.style.display = (tipo === 'rutinaria') ? 'none' : 'block'; }
    if (labelObs)     { labelObs.style.color = textColor; }
    if (inputObs)     { inputObs.style.border = `1.5px solid ${themeColor}`; }

    if (boxPermitir)  { 
        boxPermitir.style.display = (tipo === 'asignada') ? 'block' : 'none'; 
        let chkP = document.getElementById('crear_permitir_registro_avance');
        if (chkP) chkP.checked = false;
    }

    let containerTop = document.getElementById('container_top_responsables_dirigido');
    let boxColDirigido = document.getElementById('box_col_dirigido_a');
    let dirTop = document.getElementById('crear_dirigido_a_id');
    let dirImp = document.getElementById('crear_dirigido_a_id_imp');
    let bloqueSeleccionAsig = document.getElementById('bloque_seleccion_empleados_asig');

    let titAsig = document.getElementById('crear_titulo');
    let descAsig = document.getElementById('crear_descripcion');
    let titRut = document.getElementById('crear_titulo_rutinaria');
    let descRut = document.getElementById('crear_descripcion_rutinaria');
    let accRut = document.getElementById('crear_acciones_realizadas_rutinaria');
    let obsRut = document.getElementById('crear_observaciones_rutinaria');

    if (tipo === 'imprevista') {
        if (containerTop) containerTop.style.display = 'none';
        if (dirTop) dirTop.disabled = true;
        if (dirImp) dirImp.disabled = false;

        if (titAsig) { titAsig.required = false; titAsig.name = 'titulo_asig'; }
        if (descAsig) { descAsig.required = false; descAsig.name = 'descripcion_asig'; }
        if (titRut) { titRut.required = false; titRut.name = 'titulo_rutinaria'; }
        if (descRut) { descRut.required = false; descRut.name = 'descripcion_rutinaria'; }
    } else if (tipo === 'rutinaria') {
        if (containerTop) {
            containerTop.style.display = 'flex';
            containerTop.style.flexDirection = 'column';
        }
        if (boxColDirigido) boxColDirigido.style.display = 'none';
        if (dirTop) dirTop.disabled = true;
        if (dirImp) dirImp.disabled = true;
        if (bloqueSeleccionAsig) {
            bloqueSeleccionAsig.style.border = `2px solid ${themeColor}`;
            let headerSpan = document.getElementById('spanHeaderMultiselect');
            if (headerSpan) headerSpan.style.color = textColor;
            let empLabels = bloqueSeleccionAsig.querySelectorAll('label');
            empLabels.forEach(lbl => { lbl.style.color = textColor; });
            let empInputs = bloqueSeleccionAsig.querySelectorAll('input[type="checkbox"]');
            empInputs.forEach(inp => { inp.style.accentColor = themeColor; });
        }

        if (titAsig) { titAsig.required = false; titAsig.name = 'titulo_asig'; }
        if (descAsig) { descAsig.required = false; descAsig.name = 'descripcion_asig'; }
        if (titRut) { titRut.required = true; titRut.name = 'titulo'; }
        if (descRut) { descRut.required = true; descRut.name = 'descripcion'; }
        if (accRut) accRut.name = 'acciones_realizadas';
        if (obsRut) obsRut.name = 'observaciones';
    } else {
        if (containerTop) {
            containerTop.style.display = 'flex';
            containerTop.style.flexDirection = 'column';
        }
        if (boxColDirigido) boxColDirigido.style.display = 'block';
        if (dirTop) dirTop.disabled = false;
        if (dirImp) dirImp.disabled = true;
        if (bloqueSeleccionAsig) {
            bloqueSeleccionAsig.style.border = `2px solid ${themeColor}`;
            let headerSpan = document.getElementById('spanHeaderMultiselect');
            if (headerSpan) headerSpan.style.color = textColor;
            let empLabels = bloqueSeleccionAsig.querySelectorAll('label');
            empLabels.forEach(lbl => { lbl.style.color = textColor; });
            let empInputs = bloqueSeleccionAsig.querySelectorAll('input[type="checkbox"]');
            empInputs.forEach(inp => { inp.style.accentColor = themeColor; });
        }

        if (titAsig) { titAsig.required = true; titAsig.name = 'titulo'; }
        if (descAsig) { descAsig.required = true; descAsig.name = 'descripcion'; }
        if (titRut) { titRut.required = false; titRut.name = 'titulo_rutinaria'; }
        if (descRut) { descRut.required = false; descRut.name = 'descripcion_rutinaria'; }
        if (accRut) accRut.name = 'acciones_realizadas_rutinaria';
        if (obsRut) obsRut.name = 'observaciones_rutinaria';
    }

    function toggleSectionDisabled(sec, isDisabled) {
        if (!sec) return;
        sec.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = isDisabled;
        });
    }

    if (tipo === 'asignada') {
        if (headerTitle)  { headerTitle.style.color = textColor; headerTitle.innerHTML = `<i class="bi bi-check2-circle" style="color:${themeColor};"></i> Nueva Actividad Asignada`; }
        if (subtitle)     { subtitle.innerText = '{{ $isBossOrAdmin ? "Configura la tarea que asignarás a tu personal:" : "Configura la tarea que realizarás:" }}'; }
        if (labelEmp)     { labelEmp.innerText = '{{ $isBossOrAdmin ? "¿A quién le asignas esta actividad? *" : "Empleado Responsable *" }}'; }

        if (btnAsig) {
            btnAsig.style.border = `3px solid ${themeColor}`;
            btnAsig.style.background = '#dcfce7';
            btnAsig.style.color = textColor;
        }
        if (form) form.action = "{{ route('actividades.store') }}";

        if (secAsig) secAsig.style.display = 'block';
        if (secImp)  secImp.style.display  = 'none';
        if (secRut)  secRut.style.display  = 'none';

        toggleSectionDisabled(secAsig, false);
        toggleSectionDisabled(secImp, true);
        toggleSectionDisabled(secRut, true);
    } else if (tipo === 'imprevista') {
        if (headerTitle)  { headerTitle.style.color = textColor; headerTitle.innerHTML = `<i class="bi bi-person-fill" style="color:${themeColor};"></i> Registrar Actividad Personal`; }
        if (subtitle)     { subtitle.innerText = 'Registra una actividad personal creada por ti en tu jornada:'; }
        if (labelEmp)     { labelEmp.innerText = 'Empleado asignado / responsable *'; }

        if (btnImp) {
            btnImp.style.border = `3px solid ${themeColor}`;
            btnImp.style.background = '#ffedd5';
            btnImp.style.color = textColor;
        }
        if (form) form.action = "{{ route('actividades-imprevistas.store') }}";

        if (secAsig) secAsig.style.display = 'none';
        if (secImp)  secImp.style.display  = 'block';
        if (secRut)  secRut.style.display  = 'none';

        toggleSectionDisabled(secAsig, true);
        toggleSectionDisabled(secImp, false);
        toggleSectionDisabled(secRut, true);

        // Restablecer slider de avance a 0%
        let slider = document.getElementById('crear_porcentaje_imprevisto');
        if (slider) { slider.value = 0; updateImprevistoPorcentajeDisplay(0); }

        if (inputHoraIni && inputHoraFin) {
            inputHoraIni.value = "09:00";
            inputHoraFin.value = "10:00";
            calcularDiferenciaHorasCreacion();
        }
    } else if (tipo === 'rutinaria') {
        if (headerTitle)  { headerTitle.style.color = textColor; headerTitle.innerHTML = `<i class="bi bi-arrow-repeat" style="color:${themeColor};"></i> Crear Rutina Diaria`; }
        if (subtitle)     { subtitle.innerText = 'Establece un hábito o tarea que se ejecutará periódicamente:'; }
        if (labelEmp)     { labelEmp.innerText = '{{ $isBossOrAdmin ? "¿A quién le asignas esta actividad? *" : "Empleado Responsable *" }}'; }

        if (btnRut) {
            btnRut.style.border = `3px solid ${themeColor}`;
            btnRut.style.background = '#dbeafe';
            btnRut.style.color = textColor;
        }
        if (form) form.action = "{{ route('rutinas.store') }}";

        if (secAsig) secAsig.style.display = 'none';
        if (secImp)  secImp.style.display  = 'none';
        if (secRut)  secRut.style.display  = 'block';

        toggleSectionDisabled(secAsig, true);
        toggleSectionDisabled(secImp, true);
        toggleSectionDisabled(secRut, false);
    }
}

function setupAutoNumbering(elementId) {
    let el = document.getElementById(elementId);
    if (!el || el.dataset.autonumAttached) return;
    el.dataset.autonumAttached = "true";

    el.addEventListener('focus', function() {
        if (!this.value || this.value.trim() === '') {
            this.value = '1. ';
        }
    });

    el.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let start = this.selectionStart;
            let end = this.selectionEnd;
            let val = this.value;

            let linesBefore = val.substring(0, start).split('\n');
            let nextNum = linesBefore.length + 1;
            let insertStr = '\n' + nextNum + '. ';

            this.value = val.substring(0, start) + insertStr + val.substring(end);
            this.selectionStart = this.selectionEnd = start + insertStr.length;
        }
    });
}

function toggleEstadoPersonal(val) {
    let blkRealizada = document.getElementById('bloque_personal_realizada');
    let blkPendiente = document.getElementById('bloque_personal_pendiente');
    let hiddenEstado = document.getElementById('crear_estado_imprevisto');
    let wrp = document.getElementById('wrapper_hora_estimada');
    let phReal = document.getElementById('ph_hora_realizada');

    if (val === 'realizada') {
        if (blkRealizada) {
            blkRealizada.style.display = 'block';
            blkRealizada.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        }
        if (blkPendiente) {
            blkPendiente.style.display = 'none';
            blkPendiente.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }
        if (hiddenEstado) hiddenEstado.value = 'finalizada';
        
        if (wrp && phReal) {
            phReal.appendChild(wrp);
            wrp.style.display = 'block';
            wrp.querySelectorAll('input').forEach(el => el.disabled = false);
        }
    } else {
        if (blkRealizada) {
            blkRealizada.style.display = 'none';
            blkRealizada.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }
        if (blkPendiente) {
            blkPendiente.style.display = 'block';
            blkPendiente.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        }
        if (hiddenEstado) hiddenEstado.value = 'pendiente';
        if (wrp) {
            wrp.style.display = 'none';
            wrp.querySelectorAll('input').forEach(el => el.disabled = true);
        }
    }
}
function toggleDependenciaImp(val) {
    let blk = document.getElementById('bloque_dependencia_imp');
    if (blk) blk.style.display = (val === 'si') ? 'grid' : 'none';
}

function toggleDependenciaImpPend(val) {
    let blk = document.getElementById('bloque_dependencia_imp_pend');
    if (blk) blk.style.display = (val === 'si') ? 'grid' : 'none';
}

function toggleTienePlazoImp(val) {
    let subBox = document.getElementById('sub_box_tipo_plazo_imp');
    if (val === 'si') {
        if (subBox) subBox.style.display = 'block';
        let selTipo = document.querySelector('input[name="tipo_plazo_imp"]:checked')?.value || 'fecha';
        toggleTipoPlazoSubImp(selTipo);
    } else {
        if (subBox) subBox.style.display = 'none';
    }
}

function toggleTipoPlazoSubImp(val) {
    let secFechas = document.getElementById('seccion_fechas_imp');
    let boxHorario = document.getElementById('boxHorario_imp');

    if (val === 'fecha') {
        if (secFechas) secFechas.style.display = 'grid';
        if (boxHorario) boxHorario.style.display = 'none';
    } else if (val === 'hora') {
        if (secFechas) secFechas.style.display = 'none';
        if (boxHorario) boxHorario.style.display = 'grid';
    } else if (val === 'ambos') {
        if (secFechas) secFechas.style.display = 'grid';
        if (boxHorario) boxHorario.style.display = 'grid';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupAutoNumbering('crear_acciones_realizadas');
    setupAutoNumbering('crear_observaciones');
    setupAutoNumbering('crear_acciones_realizadas_imp');
    setupAutoNumbering('crear_observaciones_imp');
});

function abrirModalCrearActividad(tipo = 'asignada', soloPersonal = false) {
    let boxSelector = document.getElementById('box_selector_tipo_actividad');
    if (boxSelector) boxSelector.style.display = soloPersonal ? 'none' : 'grid';

    let form = document.getElementById('formCrearActividad');
    if (form) {
        form.reset();
        let methodInput = document.getElementById('crear_method_spoof');
        if (methodInput) methodInput.remove();
    }

    // Uncheck all radios by default and hide all conditional blocks
    document.querySelectorAll('#modalCrearActividad input[type="radio"]').forEach(r => r.checked = false);

    let idsToHide = [
        'bloque_personal_realizada',
        'bloque_personal_pendiente',
        'sub_box_tipo_plazo_imp',
        'seccion_fechas_imp',
        'boxHorario_imp',
        'bloque_dependencia_imp',
        'bloque_dependencia_imp_pend',
        'bloque_lista_colaboradores_imp',
        'bloque_avanzado_asignada',
        'sub_box_tipo_plazo',
        'bloque_dependencia_asig'
    ];
    idsToHide.forEach(id => {
        let el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    let submitBtn = document.getElementById('btnSubmitCrear');
    if (submitBtn) submitBtn.innerText = 'Guardar Actividad';

    seleccionarTipoCreacion(tipo);
    abrirModal('modalCrearActividad');
    setupAutoNumbering('crear_acciones_realizadas');
    setupAutoNumbering('crear_observaciones');
    setupAutoNumbering('crear_acciones_realizadas_imp');
    setupAutoNumbering('crear_observaciones_imp');
}

function openCrearModal(tipo) {
    abrirModalCrearActividad(tipo);
}

function openEditModalFromRow(btn, event) {
    if (event) event.stopPropagation();
    if (!btn) return;
    let dataset = btn.dataset;
    let tipo = dataset.tipo || 'asignada';
    let id = dataset.id;

    if (!id) return;

    let boxSelector = document.getElementById('box_selector_tipo_actividad');
    if (boxSelector) boxSelector.style.display = 'none';

    let baseUrl = window.APP_BASE_URL || '';
    let form = document.getElementById('formCrearActividad');
    if (!form) return;

    form.reset();

    // Select the activity type section
    seleccionarTipoCreacion(tipo);

    if (tipo === 'asignada') {
        form.action = `${baseUrl}/actividades/${id}`;
    } else if (tipo === 'imprevista') {
        form.action = `${baseUrl}/actividades-imprevistas/${id}`;
    } else if (tipo === 'rutinaria') {
        form.action = `${baseUrl}/rutinas/${id}`;
    }

    let methodInput = document.getElementById('crear_method_spoof');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.id = 'crear_method_spoof';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    let headerTitle = document.getElementById('crearModalHeaderTitle');
    let submitBtn   = document.getElementById('btnSubmitCrear');
    let subtitle    = document.getElementById('crearModalSubtitle');

    let themeColor = (tipo === 'imprevista') ? '#c2410c' : ((tipo === 'rutinaria') ? '#1e40af' : '#15803d');

    if (headerTitle) headerTitle.innerHTML = `<i class="bi bi-pencil-square" style="color:${themeColor};"></i> Editar Actividad (${tipo === 'imprevista' ? 'Personal' : (tipo === 'rutinaria' ? 'Rutina' : 'Asignada')})`;
    if (subtitle)    subtitle.innerText = 'Modifica los datos de esta actividad:';
    if (submitBtn)   submitBtn.innerText = 'Actualizar Actividad';

    // Show "Marcar como Completada" for Asignadas that are not finalizada
    let secMarcar = document.getElementById('seccion_marcar_completada');
    if (secMarcar) {
        if (tipo === 'asignada' && dataset.estado !== 'finalizada' && dataset.estado !== 'realizada') {
            secMarcar.style.display = 'block';
            let chkMarcar = document.getElementById('chk_marcar_completada');
            if(chkMarcar) { chkMarcar.checked = false; toggleNotasCompletada(); }
            let txtMarcar = document.getElementById('txt_notas_completada');
            if(txtMarcar) txtMarcar.value = '';
        } else {
            secMarcar.style.display = 'none';
        }
    }

    // Set employee checkbox if present
    let empId = dataset.empleado;
    if (empId) {
        let chk = document.querySelector(`input[name="empleados_asig_checkboxes[]"][value="${empId}"]`);
        if (chk) {
            document.querySelectorAll('input[name="empleados_asig_checkboxes[]"]').forEach(c => c.checked = false);
            chk.checked = true;
            actualizarSeleccionEmpleadosAsig();
        }
    }

    if (tipo === 'rutinaria') {
        if (document.getElementById('crear_titulo_rutinaria')) document.getElementById('crear_titulo_rutinaria').value = dataset.titulo || '';
        if (document.getElementById('crear_descripcion_rutinaria')) document.getElementById('crear_descripcion_rutinaria').value = dataset.descripcion || '';
        if (document.getElementById('crear_acciones_realizadas_rutinaria')) document.getElementById('crear_acciones_realizadas_rutinaria').value = dataset.acciones || '';
        if (document.getElementById('crear_observaciones_rutinaria')) document.getElementById('crear_observaciones_rutinaria').value = dataset.observaciones || '';
        if (document.getElementById('crear_veces_al_dia')) document.getElementById('crear_veces_al_dia').value = dataset.veces || '1';
    } else if (tipo === 'imprevista') {
        let motVal = dataset.motivo || dataset.titulo || '';
        let descVal = dataset.descripcion || '';
        let accVal = dataset.acciones || '';
        let obsVal = dataset.observaciones || '';
        let resVal = dataset.resultado || '';
        let hrsVal = dataset.horas || '1.0';
        let estVal = dataset.estado || 'pendiente';
        let porcVal = dataset.porcentaje || (estVal === 'realizada' || estVal === 'finalizada' ? '100' : '0');

        if (document.getElementById('crear_dirigido_a_id_imp')) document.getElementById('crear_dirigido_a_id_imp').value = dataset.dirigido || '';
        if (document.getElementById('crear_motivo')) document.getElementById('crear_motivo').value = motVal;
        if (document.getElementById('crear_acciones_realizadas_imp')) document.getElementById('crear_acciones_realizadas_imp').value = accVal;
        if (document.getElementById('crear_observaciones_imp')) document.getElementById('crear_observaciones_imp').value = obsVal;
        if (document.getElementById('crear_resultado_obtenido')) document.getElementById('crear_resultado_obtenido').value = resVal;
        if (document.getElementById('crear_horas_invertidas')) document.getElementById('crear_horas_invertidas').value = hrsVal;

        let radRealizada = document.querySelector('input[name="estado_personal_radio"][value="realizada"]');
        let radPendiente = document.querySelector('input[name="estado_personal_radio"][value="pendiente"]');
        if (estVal === 'realizada' || estVal === 'finalizada') {
            if (radRealizada) radRealizada.checked = true;
            toggleEstadoPersonal('realizada');
        } else {
            if (radPendiente) radPendiente.checked = true;
            toggleEstadoPersonal('pendiente');
        }

        if (document.getElementById('crear_titulo_imp_avanzada')) document.getElementById('crear_titulo_imp_avanzada').value = motVal;
        if (document.getElementById('crear_descripcion_imp_avanzada')) document.getElementById('crear_descripcion_imp_avanzada').value = descVal;
        if (document.getElementById('crear_descripcion_imp_realizada')) document.getElementById('crear_descripcion_imp_realizada').value = descVal;

        let slider = document.getElementById('crear_porcentaje_imprevisto');
        if (slider) {
            slider.value = porcVal;
            updateImprevistoPorcentajeDisplay(porcVal);
        }
        
        // Dependencias imprevistas
        let depAreaVal = dataset.deparea || '';
        let depRespVal = dataset.depresp || '';
        let depMotVal  = dataset.depmotivo || '';
        let hasDep = (depAreaVal !== '' || depRespVal !== '' || depMotVal !== '');
        
        if (hasDep) {
            if (estVal === 'realizada' || estVal === 'finalizada') {
                let rdSi = document.querySelector('input[name="_depende_imp_radio"][value="si"]');
                if (rdSi) rdSi.checked = true;
                toggleDependenciaImp('si');
                if (document.getElementById('crear_dependencia_area_imp')) document.getElementById('crear_dependencia_area_imp').value = depAreaVal;
                if (document.getElementById('crear_dependencia_responsable_imp')) document.getElementById('crear_dependencia_responsable_imp').value = depRespVal;
                if (document.getElementById('crear_dependencia_motivo_imp')) document.getElementById('crear_dependencia_motivo_imp').value = depMotVal;
            } else {
                let rdSiP = document.querySelector('input[name="_depende_imp_pend_radio"][value="si"]');
                if (rdSiP) rdSiP.checked = true;
                toggleDependenciaImpPend('si');
                if (document.getElementById('crear_dependencia_area_imp_pend')) document.getElementById('crear_dependencia_area_imp_pend').value = depAreaVal;
                if (document.getElementById('crear_dependencia_responsable_imp_pend')) document.getElementById('crear_dependencia_responsable_imp_pend').value = depRespVal;
                if (document.getElementById('crear_dependencia_motivo_imp_pend')) document.getElementById('crear_dependencia_motivo_imp_pend').value = depMotVal;
            }
        } else {
            if (estVal === 'realizada' || estVal === 'finalizada') {
                let rdNo = document.querySelector('input[name="_depende_imp_radio"][value="no"]');
                if (rdNo) rdNo.checked = true;
                toggleDependenciaImp('no');
            } else {
                let rdNoP = document.querySelector('input[name="_depende_imp_pend_radio"][value="no"]');
                if (rdNoP) rdNoP.checked = true;
                toggleDependenciaImpPend('no');
            }
        }
    } else { // asignada
        if (document.getElementById('crear_titulo')) document.getElementById('crear_titulo').value = dataset.titulo || '';
        if (document.getElementById('crear_descripcion')) document.getElementById('crear_descripcion').value = dataset.descripcion || '';
        if (document.getElementById('crear_acciones_realizadas')) document.getElementById('crear_acciones_realizadas').value = dataset.acciones || '';
        if (document.getElementById('crear_observaciones')) document.getElementById('crear_observaciones').value = dataset.observaciones || '';
        if (document.getElementById('crear_prioridad')) document.getElementById('crear_prioridad').value = dataset.prioridad || 'media';
        if (document.getElementById('crear_dirigido_a_id')) document.getElementById('crear_dirigido_a_id').value = dataset.dirigido || '';
        if (document.getElementById('crear_fecha_inicio')) document.getElementById('crear_fecha_inicio').value = dataset.fechainicio || '';
        if (document.getElementById('crear_fecha_fin')) document.getElementById('crear_fecha_fin').value = dataset.fechafin || '';

        let chkPermitir = document.getElementById('crear_permitir_registro_avance');
        if (chkPermitir) {
            chkPermitir.checked = (dataset.permitiravance == '1' || dataset.permitiravance === 'true');
        }

        let horIni = dataset.horainicio;
        let horFin = dataset.horafin;
        if (document.getElementById('crear_hora_inicio')) document.getElementById('crear_hora_inicio').value = horIni || '09:00';
        if (document.getElementById('crear_hora_fin')) document.getElementById('crear_hora_fin').value = horFin || '10:00';

        let chkSi = document.getElementById('crear_plazo_si');
        let chkNo = document.getElementById('crear_plazo_no');
        if (horIni || dataset.tieneplazo === 'si') {
            if (chkSi) chkSi.checked = true;
            toggleTienePlazo('si');
        } else if (dataset.tieneplazo === 'no') {
            if (chkNo) chkNo.checked = true;
            toggleTienePlazo('no');
        }

        let depRespVal = dataset.depresp || '';
        let depMotVal  = dataset.depmotivo || '';
        let hasDep = (depRespVal !== '' || depMotVal !== '');
        let rDepSi = document.getElementById('crear_depende_si');
        let rDepNo = document.getElementById('crear_depende_no');
        if (hasDep) {
            if (rDepSi) rDepSi.checked = true;
            toggleDependenciaAsig('si');
            if (document.getElementById('crear_dependencia_area')) document.getElementById('crear_dependencia_area').value = dataset.deparea || '';
            if (document.getElementById('crear_dependencia_responsable')) document.getElementById('crear_dependencia_responsable').value = depRespVal;
            if (document.getElementById('crear_dependencia_motivo')) document.getElementById('crear_dependencia_motivo').value = depMotVal;
        } else {
            if (rDepNo) rDepNo.checked = true;
            toggleDependenciaAsig('no');
        }

        let hasAdv = (dataset.acciones || dataset.observaciones || hasDep || dataset.fechainicio || (dataset.tieneplazo === 'si'));
        let rSenSi = document.getElementById('crear_sencilla_si');
        let rSenNo = document.getElementById('crear_sencilla_no');
        if (hasAdv) {
            if (rSenNo) rSenNo.checked = true;
            toggleSencillaNueva('no');
        } else {
            if (rSenSi) rSenSi.checked = true;
            toggleSencillaNueva('si');
        }
    }

    abrirModal('modalCrearActividad');
}

// Aliases para retrocompatibilidad con las vistas
function openEditRutinaModal(btn, event) { openEditModalFromRow(btn, event); }
function openEditImprevistaModal(btn, event) { openEditModalFromRow(btn, event); }

function handleRutinaCheck(checkbox, event) {
    if (event) event.stopPropagation();
    let id = checkbox.dataset.id;
    let checkedCount = 0;
    
    document.querySelectorAll(`.rutina-check-box[data-id="${id}"]`).forEach(cb => {
        if (cb.checked) checkedCount++;
    });

    let baseUrl = window.APP_BASE_URL || '';
    fetch(`${baseUrl}/rutinas/${id}/set-ejecuciones`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cantidad: checkedCount })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let row = checkbox.closest('tr');
            if (row) {
                let badge = row.querySelector('.estado-badge-val');
                let bar = row.querySelector('.progreso-bar-val');
                let txt = row.querySelector('.progreso-txt-val');

                let porcentaje = data.porcentaje || 0;
                let estado = porcentaje >= 100 ? 'Finalizada' : (porcentaje > 0 ? 'En proceso' : 'Pendiente');
                let color = porcentaje >= 100 ? '#166534' : (porcentaje > 0 ? '#ca8a04' : '#475569');
                
                if (badge) {
                    badge.style.color = color;
                    badge.innerText = `${estado} (${porcentaje}%)`;
                }
                if (bar) {
                    bar.style.width = `${porcentaje}%`;
                }
                if (txt) {
                    txt.innerText = `${porcentaje}%`;
                }
                row.dataset.avance = porcentaje;
                row.dataset.estado = porcentaje >= 100 ? 'finalizada' : (porcentaje > 0 ? 'en_proceso' : 'pendiente');
            }
        }
    })
    .catch(err => console.error("Error actualizando ejecución de rutina:", err));
}

// HANDLERS DE EVENTOS DE ENVÍO DE FORMULARIOS POR DELEGACIÓN GLOBAL EN EL DOCUMENTO
(function() {
    if (window._modalesSubmitHandlersBound) return;
    window._modalesSubmitHandlersBound = true;

    document.addEventListener('submit', function(e) {
        let form = e.target;
        if (!form) return;

        if (form.id === 'formCrearActividad') {
            e.preventDefault();
            let submitBtn = document.getElementById('btnSubmitCrear');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Guardando...'; }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new FormData(form)
            })
            .then(async r => {
                let contentType = r.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    let data = await r.json();
                    if (r.ok && data.success) {
                        cerrarTodosLosModales();
                        window.location.reload();
                    } else {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Guardar Actividad'; }
                        let msg = data.message || 'Ocurrió un problema al guardar la actividad.';
                        if (data.errors) {
                            msg += '\n' + Object.values(data.errors).flat().join('\n');
                        }
                        alert(msg);
                    }
                } else {
                    // Si el servidor devolvió una respuesta exitosa en HTML o redirección
                    cerrarTodosLosModales();
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error("Error al enviar formulario via AJAX, realizando submit nativo:", err);
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Guardar Actividad'; }
                form.submit();
            });
        } else if (form.id === 'formEditarActividad') {
            e.preventDefault();
            let submitBtn = document.getElementById('btnSubmitEditar');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Actualizando...'; }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new FormData(form)
            })
            .then(async r => {
                let contentType = r.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    let data = await r.json();
                    if (r.ok && data.success) {
                        cerrarTodosLosModales();
                        window.location.reload();
                    } else {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Actualizar Cambios'; }
                        let msg = data.message || 'Ocurrió un problema al actualizar la actividad.';
                        if (data.errors) {
                            msg += '\n' + Object.values(data.errors).flat().join('\n');
                        }
                        alert(msg);
                    }
                } else {
                    cerrarTodosLosModales();
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error("Error al actualizar via AJAX, realizando submit nativo:", err);
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Actualizar Cambios'; }
                form.submit();
            });
        }
    });
})();

// FUNCIONES DE DISPARO DIRECTO DESDE BOTONES (ONCLICK FALLBACK / PRIMARY)
function guardarNuevaActividad(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    let form = document.getElementById('formCrearActividad');
    if (!form) return;

    let tipo = document.getElementById('crear_tipo_actividad')?.value || 'asignada';
    let empSelect = document.getElementById('crear_empleado_id');

    let tituloVal = '';
    let descVal = '';

    if (tipo === 'rutinaria') {
        let titRut = document.getElementById('crear_titulo_rutinaria');
        let descRut = document.getElementById('crear_descripcion_rutinaria');
        tituloVal = titRut ? titRut.value.trim() : '';
        descVal = descRut ? descRut.value.trim() : '';
    } else if (tipo === 'imprevista') {
        let mot = document.getElementById('crear_motivo');
        let titSen = document.getElementById('crear_titulo_imp_sencilla');
        let titAdv = document.getElementById('crear_titulo_imp_avanzada');
        let descReal = document.getElementById('crear_descripcion_imp_realizada');
        let descSen = document.getElementById('crear_descripcion_imp_sencilla');
        let descAdv = document.getElementById('crear_descripcion_imp_avanzada');

        tituloVal = (mot && mot.value.trim()) ? mot.value.trim() : ((titSen && titSen.value.trim()) ? titSen.value.trim() : ((titAdv && titAdv.value.trim()) ? titAdv.value.trim() : ''));
        descVal = (descReal && descReal.value.trim()) ? descReal.value.trim() : ((descSen && descSen.value.trim()) ? descSen.value.trim() : ((descAdv && descAdv.value.trim()) ? descAdv.value.trim() : ''));
    } else {
        let titAsig = document.getElementById('crear_titulo');
        let descAsig = document.getElementById('crear_descripcion');
        tituloVal = titAsig ? titAsig.value.trim() : '';
        descVal = descAsig ? descAsig.value.trim() : '';
    }

    if (empSelect && !empSelect.value) {
        alert('Por favor selecciona el empleado responsable.');
        return;
    }

    if (!tituloVal) {
        alert('Por favor ingresa un título para la actividad.');
        return;
    }

    let submitBtn = document.getElementById('btnSubmitCrear');
    if (submitBtn) { 
        submitBtn.disabled = true; 
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Guardando...'; 
    }

    let actionUrl = form.action || "{{ route('actividades.store') }}";

    fetch(actionUrl, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new FormData(form)
    })
    .then(async r => {
        let contentType = r.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            let data = await r.json();
            if (r.ok && data.success) {
                cerrarTodosLosModales();
                window.location.reload();
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Guardar Actividad'; }
                let msg = data.message || 'Ocurrió un problema al guardar la actividad.';
                if (data.errors) {
                    msg += '\n\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        } else {
            cerrarTodosLosModales();
            window.location.reload();
        }
    })
    .catch(err => {
        console.error("Error al guardar actividad via AJAX, intentando submit nativo:", err);
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Guardar Actividad'; }
        form.submit();
    });
}

function guardarEdicionActividad(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    let form = document.getElementById('formEditarActividad');
    if (!form) return;

    let submitBtn = document.getElementById('btnSubmitEditar');
    if (submitBtn) { 
        submitBtn.disabled = true; 
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Actualizando...'; 
    }

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new FormData(form)
    })
    .then(async r => {
        let contentType = r.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            let data = await r.json();
            if (r.ok && data.success) {
                cerrarTodosLosModales();
                window.location.reload();
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Actualizar Cambios'; }
                let msg = data.message || 'Ocurrió un problema al actualizar la actividad.';
                if (data.errors) {
                    msg += '\n\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        } else {
            cerrarTodosLosModales();
            window.location.reload();
        }
    })
    .catch(err => {
        console.error("Error al actualizar actividad via AJAX, intentando submit nativo:", err);
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Actualizar Cambios'; }
        form.submit();
    });
}

function guardarAvanceGenerico(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    let form = document.getElementById('formAvanceGenerico');
    if (!form) return;

    let notaInput = document.getElementById('input_avance_gen_resultado');
    if (notaInput && !notaInput.value.trim()) {
        let porc = document.getElementById('input_avance_gen_range')?.value || '50';
        notaInput.value = 'Avance registrado al ' + porc + '%';
    }

    let submitBtn = document.getElementById('btnSubmitAvanceGen');
    if (submitBtn) { 
        submitBtn.disabled = true; 
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Guardando...'; 
    }

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new FormData(form)
    })
    .then(async r => {
        let contentType = r.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            let data = await r.json();
            if (r.ok && data.success) {
                cerrarTodosLosModales();
                window.location.reload();
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Guardar Avance y Nota'; }
                let msg = data.message || 'Ocurrió un problema al guardar el avance.';
                if (data.errors) {
                    msg += '\n\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        } else {
            cerrarTodosLosModales();
            window.location.reload();
        }
    })
    .catch(err => {
        console.error("Error al guardar avance via AJAX, intentando submit nativo:", err);
        if (submitBtn) { submitBtn.disabled = false; }
        form.submit();
    });
}

function setupAutoNumbering(elementId) {
    let el = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
    if (!el || el.dataset.autonumAttached) return;
    el.dataset.autonumAttached = "true";

    el.addEventListener('focus', function() {
        if (!this.value || this.value.trim() === '') {
            this.value = '1. ';
        }
    });

    el.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let start = this.selectionStart;
            let end = this.selectionEnd;
            let val = this.value;

            let linesBefore = val.substring(0, start).split('\n');
            let nextNum = linesBefore.length + 1;
            let insertStr = '\n' + nextNum + '. ';

            this.value = val.substring(0, start) + insertStr + val.substring(end);
            this.selectionStart = this.selectionEnd = start + insertStr.length;
        }
    });
}

function openDevolverModalFromRow(btn, event, customDataset) {
    if (event) event.stopPropagation();
    let dataset = customDataset || (btn ? btn.dataset : null);
    if (!dataset) return;
    let id = dataset.id;
    let tipo = dataset.tipo || 'asignada';
    if (!id) return;

    let baseUrl = window.APP_BASE_URL || '';
    let form = document.getElementById('formDevolverActividad');
    if (!form) return;

    form.reset();

    if (tipo === 'imprevista') {
        form.action = `${baseUrl}/actividades-imprevistas/${id}/devolver`;
    } else if (tipo === 'rutinaria') {
        form.action = `${baseUrl}/rutinas/${id}/devolver`;
    } else {
        form.action = `${baseUrl}/actividades/${id}/devolver`;
    }

    let titleText = document.getElementById('devolverTituloText');
    if (titleText) titleText.innerText = dataset.titulo || 'Actividad';

    let range = document.getElementById('input_devolver_pct_range');
    let display = document.getElementById('display_devolver_pct_val');
    let currentPct = parseInt(dataset.avance || 50);
    let suggestPct = currentPct >= 100 ? 50 : Math.max(0, currentPct - 25);

    if (range) range.value = suggestPct;
    if (display) display.innerText = `${suggestPct}%`;

    let txt = document.getElementById('input_devolver_comentario');
    if (txt) txt.value = '';

    abrirModal('modalDevolverActividad');
}

function openDevolverModalFromDetalle() {
    if (!window.currentDetailDataset) return;
    cerrarModal('modalVerDetalle');
    openDevolverModalFromRow(null, null, window.currentDetailDataset);
}

function guardarDevolucionActividad(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    let form = document.getElementById('formDevolverActividad');
    if (!form) return;

    let comment = document.getElementById('input_devolver_comentario');
    if (comment && !comment.value.trim()) {
        alert('Por favor especifica lo que faltó por realizar.');
        comment.focus();
        return;
    }

    let submitBtn = document.getElementById('btnSubmitDevolver');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Enviando...'; }

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new FormData(form)
    })
    .then(async r => {
        let contentType = r.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            let data = await r.json();
            if (r.ok && data.success) {
                cerrarTodosLosModales();
                window.location.reload();
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-arrow-return-left me-1"></i> Regresar Actividad con Observaciones'; }
                alert(data.message || 'Ocurrió un error al devolver la actividad.');
            }
        } else {
            cerrarTodosLosModales();
            window.location.reload();
        }
    })
    .catch(err => {
        console.error("Error al devolver actividad:", err);
        if (submitBtn) { submitBtn.disabled = false; }
        form.submit();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    ['crear_acciones_realizadas', 'crear_observaciones', 'edit_acciones_realizadas', 'edit_observaciones', 'input_avance_gen_resultado'].forEach(id => {
        setupAutoNumbering(id);
    });
});
</script>
