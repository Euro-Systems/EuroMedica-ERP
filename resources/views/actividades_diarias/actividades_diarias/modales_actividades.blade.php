@php
    if (!isset($empleadosRH)) {
        $currentUser = auth()->user();
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
<!-- ============================================== -->
<!-- MODAL: NUEVA ACTIVIDAD -->
<!-- ============================================== -->
<div id="modalNueva" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalNueva')">&times;</span>
        <h2 style="margin-bottom: 20px; margin-top:0;"><i class="bi bi-journal-plus me-2"></i>Asignar Nueva Actividad</h2>
        <form action="{{ route('actividades.store') }}" method="POST" id="formNuevaActividad">
            @csrf

            <!-- PREGUNTA 1: ¿Es sencilla? -->
            <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:16px 20px; margin-bottom:16px;">
                <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#075985;">
                    <i class="bi bi-question-circle-fill me-1"></i> ¿Es una actividad sencilla?
                </p>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bae6fd; font-weight:600; background:#fff; transition:all 0.15s;">
                        <input type="radio" name="_sencilla" value="si" onchange="toggleSencilla('si')" style="accent-color:#0284c7;"> Sí
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bae6fd; font-weight:600; background:#fff; transition:all 0.15s;">
                        <input type="radio" name="_sencilla" value="no" onchange="toggleSencilla('no')" style="accent-color:#0284c7;"> No
                    </label>
                </div>
            </div>

            <!-- Título y Descripción (siempre visibles una vez respondido) -->
            <div id="bloque_base" style="display:none;">
                <div class="empleado-grid">
                    <div style="grid-column: span 2;">
                        <b>Empleado Asignado *</b>
                        <select name="empleado_id" id="nueva_empleado_id" required>
                            <option value="">Selecciona un empleado</option>
                            @if(auth()->check())
                                <option value="{{ auth()->id() }}" data-area="{{ auth()->user()->area_id ?? '' }}" style="font-weight:bold;color:#1e3a8a;">YO ({{ auth()->user()->name }})</option>
                            @endif
                            @foreach ($empleadosRH as $emp)
                                @if(($emp['id'] ?? $emp->id) !== auth()->id())
                                    <option value="{{ $emp['id'] ?? $emp->id }}" data-area="{{ $emp['area_id'] ?? $emp->area_id ?? '' }}">
                                        {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div style="grid-column: span 2; margin-top: 10px;">
                        <b>Título de la Actividad *</b>
                        <input type="text" name="titulo" required placeholder="Ej: Revisión de teléfonos">
                    </div>
                    <div style="grid-column: span 2;">
                        <b>Descripción *</b>
                        <textarea name="descripcion" rows="3" required placeholder="Describe a detalle lo que se debe hacer..."></textarea>
                    </div>
                </div>

                <!-- Campos adicionales solo si NO es sencilla -->
                <div id="bloque_completo" style="display:none; margin-top:10px;">

                    <!-- PREGUNTA 2: ¿Es compartida? -->
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 20px; margin:14px 0;">
                        <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#166534;">
                            <i class="bi bi-question-circle-fill me-1"></i> ¿Es una actividad compartida?
                        </p>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_compartida" value="si" onchange="toggleCompartida('si')" style="accent-color:#16a34a;"> Sí
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_compartida" value="no" onchange="toggleCompartida('no')" style="accent-color:#16a34a;"> No
                            </label>
                        </div>
                    </div>

                    <!-- Lista de empleados adicionales (si es compartida) -->
                    <div id="bloque_compartida" style="display:none; margin-bottom:10px;">
                        <b>Empleados adicionales que participarán *</b>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; max-height:160px; overflow-y:auto; margin-top:4px;">
                            @foreach ($empleadosRH as $emp)
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer; font-size:14px;">
                                    <input type="checkbox" name="empleados_compartidos[]" value="{{ $emp['id'] ?? $emp->id }}" style="accent-color:#16a34a; width:16px; height:16px;">
                                    {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Prioridad -->
                    <div class="empleado-grid" style="margin-bottom:10px;">
                        <div>
                            <b>Prioridad *</b>
                            <select name="prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <!-- PREGUNTA 3: ¿Tardará más de un día? -->
                    <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:16px 20px; margin:14px 0;">
                        <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#92400e;">
                            <i class="bi bi-question-circle-fill me-1"></i> ¿Tardará más de un día?
                        </p>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_mas_un_dia" value="si" onchange="toggleDuracion('si')" style="accent-color:#d97706;"> Sí
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_mas_un_dia" value="no" onchange="toggleDuracion('no')" style="accent-color:#d97706;"> No
                            </label>
                        </div>
                    </div>

                    <!-- 3 bloques PLANOS e independientes -->
                    <input type="hidden" name="modalidad" id="nueva_modalidad_hidden" value="un_dia">

                    <div id="bloque_fecha_inicio" style="display:none; margin-bottom:10px;">
                        <b>Fecha de Inicio *</b>
                        <input type="date" name="fecha_inicio" id="nueva_fecha_inicio" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                    <div id="bloque_fecha_fin" style="display:none; margin-bottom:10px;">
                        <b>Fecha Estimada Fin *</b>
                        <input type="date" name="fecha_estimada_fin" id="nueva_fecha_fin" disabled style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                    <div id="bloque_tiempo" style="display:none; margin-bottom:10px;">
                        <b>Tiempo Estimado <span style="font-weight:400;color:#6b7280;">(Opcional)</span></b>
                        <input type="text" name="tiempo_estimado" id="nueva_tiempo_estimado" disabled placeholder="Ej: 2 horas, 30 min" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                </div>

                <!-- Botones siempre visibles en bloque_base -->
                <div id="bloque_acciones" style="margin-top: 25px; text-align: right;">
                    <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalNueva')">Cancelar</button>
                    <button type="submit" class="btn-form" id="btn_asignar" style="display:none;">Asignar Actividad</button>
                    <button type="submit" class="btn-form" id="btn_asignar_sencilla" style="display:none;">Asignar Actividad</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
window.APP_BASE_URL = "{{ url('/') }}";
window.authId = {{ auth()->id() ?? 0 }};
var authId = window.authId;

function toggleSencilla(val) {
    document.getElementById('bloque_base').style.display = 'block';
    const completo    = document.getElementById('bloque_completo');
    const btnSencilla = document.getElementById('btn_asignar_sencilla');
    const btnCompleto = document.getElementById('btn_asignar');

    if (val === 'si') {
        completo.style.display = 'none';
        completo.querySelectorAll('input,select,textarea').forEach(el => {
            el.disabled = true;
        });
        btnSencilla.style.display = 'inline-block';
        btnCompleto.style.display = 'none';
    } else {
        completo.style.display = 'block';
        completo.querySelectorAll('input,select,textarea').forEach(el => el.disabled = false);
        // Ocultar los 3 bloques de fecha hasta que el usuario responda Q3
        document.getElementById('bloque_fecha_inicio').style.display = 'none';
        document.getElementById('bloque_fecha_fin').style.display    = 'none';
        document.getElementById('bloque_tiempo').style.display       = 'none';
        document.getElementById('nueva_fecha_inicio').disabled  = true;
        document.getElementById('nueva_fecha_fin').disabled     = true;
        document.getElementById('nueva_tiempo_estimado').disabled = true;
        btnSencilla.style.display = 'none';
        btnCompleto.style.display = 'none';
    }
}

function toggleCompartida(val) {
    const bloque = document.getElementById('bloque_compartida');
    bloque.style.display = val === 'si' ? 'block' : 'none';
    bloque.querySelectorAll('input').forEach(el => el.disabled = val !== 'si');
}

function toggleDuracion(val) {
    const btn = document.getElementById('btn_asignar');
    const hidden = document.getElementById('nueva_modalidad_hidden');

    if (val === 'si') {
        // Sí: mostrar fecha inicio + fecha fin
        document.getElementById('bloque_fecha_inicio').style.display = 'block';
        document.getElementById('nueva_fecha_inicio').disabled        = false;
        document.getElementById('bloque_fecha_fin').style.display    = 'block';
        document.getElementById('nueva_fecha_fin').disabled           = false;
        document.getElementById('bloque_tiempo').style.display        = 'none';
        document.getElementById('nueva_tiempo_estimado').disabled     = true;
        if (hidden) hidden.value = 'varios_dias';
    } else {
        // No: SOLO tiempo estimado, sin fechas
        document.getElementById('bloque_fecha_inicio').style.display = 'none';
        document.getElementById('nueva_fecha_inicio').disabled        = true;
        document.getElementById('bloque_fecha_fin').style.display    = 'none';
        document.getElementById('nueva_fecha_fin').disabled           = true;
        document.getElementById('bloque_tiempo').style.display        = 'block';
        document.getElementById('nueva_tiempo_estimado').disabled     = false;
        if (hidden) hidden.value = 'un_dia';
    }

    btn.style.display = 'inline-block';
}

function enableBlock(id) {
    document.getElementById(id).querySelectorAll('input,select,textarea').forEach(el => el.disabled = false);
}
function disableBlock(id) {
    document.getElementById(id).querySelectorAll('input,select,textarea').forEach(el => el.disabled = true);
}

// Edit Modal Toggles
function toggleEditSencilla(val) {
    document.getElementById('edit_bloque_base').style.display = 'block';
    const completo = document.getElementById('edit_bloque_completo');
    if (val === 'si') {
        completo.style.display = 'none';
        completo.querySelectorAll('input,select,textarea').forEach(el => el.disabled = true);
    } else {
        completo.style.display = 'block';
        completo.querySelectorAll('input,select,textarea').forEach(el => el.disabled = false);
    }
}

function toggleEditCompartida(val) {
    const bloque = document.getElementById('edit_bloque_compartida');
    bloque.style.display = val === 'si' ? 'block' : 'none';
    bloque.querySelectorAll('input').forEach(el => el.disabled = val !== 'si');
}

function toggleEditDuracion(val) {
    const hidden = document.getElementById('edit_modalidad_hidden');
    if (val === 'si') {
        document.getElementById('edit_bloque_fecha_inicio').style.display = 'block';
        document.getElementById('edit_fecha_inicio').disabled = false;
        document.getElementById('edit_bloque_fecha_fin').style.display = 'block';
        document.getElementById('edit_fecha_fin').disabled = false;
        document.getElementById('edit_bloque_tiempo').style.display = 'none';
        document.getElementById('edit_tiempo_estimado').disabled = true;
        if (hidden) hidden.value = 'varios_dias';
    } else {
        document.getElementById('edit_bloque_fecha_inicio').style.display = 'none';
        document.getElementById('edit_fecha_inicio').disabled = true;
        document.getElementById('edit_bloque_fecha_fin').style.display = 'none';
        document.getElementById('edit_fecha_fin').disabled = true;
        document.getElementById('edit_bloque_tiempo').style.display = 'block';
        document.getElementById('edit_tiempo_estimado').disabled = false;
        if (hidden) hidden.value = 'un_dia';
    }
}
</script>

<!-- ============================================== -->
<!-- MODAL: EDITAR ACTIVIDAD -->
<!-- ============================================== -->
<div id="modalEditar" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalEditar')">&times;</span>
        <h2 style="margin-bottom: 20px; margin-top:0;"><i class="bi bi-pencil-square me-2"></i>Editar Actividad</h2>
        <form id="formEditar" action="" method="POST">
            @csrf
            @method('PUT')

            <!-- PREGUNTA 1: ¿Es sencilla? -->
            <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:16px 20px; margin-bottom:16px;">
                <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#075985;">
                    <i class="bi bi-question-circle-fill me-1"></i> ¿Es una actividad sencilla?
                </p>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bae6fd; font-weight:600; background:#fff; transition:all 0.15s;">
                        <input type="radio" name="_sencilla_edit" id="edit_sencilla_si" value="si" onchange="toggleEditSencilla('si')" style="accent-color:#0284c7;"> Sí
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bae6fd; font-weight:600; background:#fff; transition:all 0.15s;">
                        <input type="radio" name="_sencilla_edit" id="edit_sencilla_no" value="no" onchange="toggleEditSencilla('no')" style="accent-color:#0284c7;"> No
                    </label>
                </div>
            </div>

            <!-- Título y Descripción -->
            <div id="edit_bloque_base" style="display:block;">
                <div class="empleado-grid">
                    <div style="grid-column: span 2;">
                        <b>Empleado Asignado *</b>
                        <select name="empleado_id" id="edit_empleado" required>
                            <option value="">Selecciona un empleado</option>
                            @if(auth()->check())
                                <option value="{{ auth()->id() }}" style="font-weight:bold;color:#1e3a8a;">YO ({{ auth()->user()->name }})</option>
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
                    <div style="grid-column: span 2; margin-top: 10px;">
                        <b>Título de la Actividad *</b>
                        <input type="text" name="titulo" id="edit_titulo" required placeholder="Ej: Revisión de teléfonos">
                    </div>
                    <div style="grid-column: span 2;">
                        <b>Descripción *</b>
                        <textarea name="descripcion" id="edit_descripcion" rows="3" required placeholder="Describe a detalle lo que se debe hacer..."></textarea>
                    </div>
                </div>

                <!-- Campos adicionales solo si NO es sencilla -->
                <div id="edit_bloque_completo" style="margin-top:10px;">

                    <!-- PREGUNTA 2: ¿Es compartida? -->
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 20px; margin:14px 0;">
                        <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#166534;">
                            <i class="bi bi-question-circle-fill me-1"></i> ¿Es una actividad compartida?
                        </p>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_compartida_edit" id="edit_compartida_si" value="si" onchange="toggleEditCompartida('si')" style="accent-color:#16a34a;"> Sí
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_compartida_edit" id="edit_compartida_no" value="no" onchange="toggleEditCompartida('no')" style="accent-color:#16a34a;"> No
                            </label>
                        </div>
                    </div>

                    <div id="edit_bloque_compartida" style="display:none; margin-bottom:10px;">
                        <b>Empleados adicionales que participarán *</b>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; max-height:160px; overflow-y:auto; margin-top:4px;">
                            @foreach ($empleadosRH as $emp)
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer; font-size:14px;">
                                    <input type="checkbox" name="empleados_compartidos[]" class="edit_emp_compartido" value="{{ $emp['id'] ?? $emp->id }}" style="accent-color:#16a34a; width:16px; height:16px;">
                                    {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Prioridad -->
                    <div class="empleado-grid" style="margin-bottom:10px;">
                        <div>
                            <b>Prioridad *</b>
                            <select name="prioridad" id="edit_prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <!-- PREGUNTA 3: ¿Tardará más de un día? -->
                    <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:16px 20px; margin:14px 0;">
                        <p style="margin:0 0 10px; font-weight:700; font-size:15px; color:#92400e;">
                            <i class="bi bi-question-circle-fill me-1"></i> ¿Tardará más de un día?
                        </p>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_mas_un_dia_edit" id="edit_mas_un_dia_si" value="si" onchange="toggleEditDuracion('si')" style="accent-color:#d97706;"> Sí
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 18px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff; transition:all 0.15s;">
                                <input type="radio" name="_mas_un_dia_edit" id="edit_mas_un_dia_no" value="no" onchange="toggleEditDuracion('no')" style="accent-color:#d97706;"> No
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="modalidad" id="edit_modalidad_hidden" value="un_dia">

                    <div id="edit_bloque_fecha_inicio" style="display:none; margin-bottom:10px;">
                        <b>Fecha de Inicio *</b>
                        <input type="date" name="fecha_inicio" id="edit_fecha_inicio" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                    <div id="edit_bloque_fecha_fin" style="display:none; margin-bottom:10px;">
                        <b>Fecha Estimada Fin *</b>
                        <input type="date" name="fecha_estimada_fin" id="edit_fecha_fin" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                    <div id="edit_bloque_tiempo" style="display:none; margin-bottom:10px;">
                        <b>Tiempo Estimado <span style="font-weight:400;color:#6b7280;">(Opcional)</span></b>
                        <input type="text" name="tiempo_estimado" id="edit_tiempo_estimado" placeholder="Ej: 2 horas, 30 min" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;">
                    </div>

                </div>

                <!-- Botones y Estado del Edit -->
                <div style="margin-top: 15px; border-top:1px solid #e2e8f0; padding-top:15px;">
                    <div style="margin-bottom:15px;">
                        <b>Estado *</b>
                        <select name="estado" id="edit_estado" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="en_pausa">En Pausa</option>
                            <option value="finalizada">Finalizada</option>
                            <option value="atrasada">Atrasada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div style="text-align: right;">
                        <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalEditar')">Cancelar</button>
                        <button type="submit" class="btn-form">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: VER FICHA Y AVANCES (SHOW) -->
<!-- ============================================== -->
<div id="modalFicha" class="rh-modal">
    <div class="rh-modal-content" style="max-width: 900px; padding: 30px;">
        <span class="rh-modal-close" onclick="cerrarModal('modalFicha')">&times;</span>
        
        <div style="display:flex; gap:15px; align-items:flex-start; margin-top:10px;">
            <!-- FICHA ACTIVIDAD -->
            <div class="rh-card" style="flex:1; margin-bottom:0; box-shadow:none; padding:0;">
                <h2 style="margin-bottom: 20px; font-size:22px; font-weight:bold;">
                    Ficha de la Actividad
                </h2>
                <div style="background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
                    <p style="margin:0; display:flex; align-items:center; gap:10px;">
                        <span id="ficha_prioridad" style="background:#1e3a8a;color:#fff;padding:2px 8px;border-radius:4px;font-size:12px;"></span>
                        <select id="ficha_estado_select" onchange="actualizarEstadoRapido(this.value)" style="padding:2px 8px; border-radius:4px; font-size:11px; font-weight:bold; border:1px solid #cbd5e1; outline:none; cursor:pointer;" {{ auth()->check() && in_array(auth()->user()->rol, ['jefe', 'admin']) ? '' : 'disabled' }}>
                            <option value="pendiente">PENDIENTE</option>
                            <option value="en_proceso">EN PROCESO</option>
                            <option value="en_pausa">EN PAUSA</option>
                            <option value="finalizada">TERMINADA</option>
                            <option value="atrasada">ATRASADA</option>
                            <option value="cancelada">CANCELADA</option>
                        </select>
                    </p>
                    <h3 id="ficha_titulo" style="margin:10px 0; font-size:20px;"></h3>
                    <p id="ficha_descripcion" style="color:#475569;margin-bottom:15px;font-size:14px;"></p>
                    
                    <div class="empleado-grid" style="grid-template-columns: 1fr; gap:10px;">
                        <div><b style="font-size:12px;">Impacto</b><p id="ficha_impacto" style="margin:0;font-size:14px;"></p></div>
                        <div><b style="font-size:12px;">Fechas</b><p id="ficha_fechas" style="margin:0;font-size:14px;"></p></div>
                        <div style="grid-column: span 2;">
                            <form action="" method="POST" id="form-slider-avance" style="margin:0;">
                                @csrf
                                @method('PUT')
                                <b style="font-size:12px; color:#475569;">Avance General: <span id="ficha_avance_text_val">0</span>%</b>
                                <input type="range" name="porcentaje_avance" id="slider_avance" min="0" max="100" style="width:100%; margin-top:5px; accent-color:#22c55e;" oninput="document.getElementById('ficha_avance_text_val').textContent=this.value" onchange="updatePorcentaje(this)" {{ auth()->check() && in_array(auth()->user()->rol, ['jefe', 'admin']) ? '' : 'disabled' }}>
                            </form>
                            
                            @if(auth()->check() && in_array(auth()->user()->rol, ['jefe', 'admin']))
                            <div style="margin-top: 15px;">
                                <button type="button" id="ficha_btn_completar" class="btn-ver" style="background:#10b981; color:white; border:none; width:100%; padding:8px; font-size:14px; font-weight:bold; border-radius:6px; cursor:pointer;" onclick="aprobarActividadDesdeFicha()">
                                    <i class="bi bi-check-circle me-1"></i> Completada
                                </button>
                                <button type="button" id="ficha_btn_reabrir" class="btn-ver" style="background:#6b7280; color:white; border:none; width:100%; padding:8px; font-size:14px; font-weight:bold; border-radius:6px; cursor:pointer; display:none;" onclick="reabrirActividadDesdeFicha()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reabrir
                                </button>
                            </div>
                            @endif

                            <div style="display:flex; justify-content:space-between; margin-top:15px; gap:10px;">
                                <button type="button" id="ficha_btn_editar" class="btn-ver" style="background:#10b981; color:#fff; flex:1; font-weight:bold; border-radius:6px; padding:8px;" onclick="openEditModal(this)">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </button>
                                <form action="" method="POST" id="form_delete_actividad" style="margin:0; flex:1;" onsubmit="return confirm('¿Seguro que deseas eliminar esta actividad definitivamente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ver" style="background:#ef4444; color:#fff; width:100%; font-weight:bold; border-radius:6px; padding:8px;">
                                        <i class="bi bi-trash-fill me-1"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REGISTRAR AVANCE -->
            <div class="rh-card" style="flex:1.5; margin-bottom:0; box-shadow:none; padding:0; padding-left:15px; border-left:1px solid #e2e8f0; border-radius:0;">
                <h2 style="color:#16a34a; margin-bottom:10px; font-size:18px;"><i class="bi bi-plus-circle-fill me-2"></i>Registrar Nuevo Avance</h2>
                <form action="{{ route('avances-actividad.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="actividad_id" id="avance_actividad_id">
                    <div class="empleado-grid" style="gap:10px;">
                        <div style="grid-column: span 2;">
                            <b style="font-size:12px;">¿Qué se hizo exactamente? *</b>
                            <textarea name="que_se_hizo" rows="4" required style="padding:6px; font-family:inherit; border-radius:6px; border:1px solid #d1d5db;"></textarea>
                        </div>
                        
                        <div style="grid-column: span 2;">
                            <b style="font-size:12px;">Problema Detectado / Acciones Reales</b>
                            <textarea name="acciones_realizadas" rows="2" style="padding:6px;"></textarea>
                        </div>

                        <div style="grid-column: span 2;">
                            <b style="font-size:12px;">Resultado Final / Observaciones *</b>
                            <textarea name="resultado_final" rows="2" required style="padding:6px;"></textarea>
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top:20px;">
                        <button type="submit" style="background:#2563eb; color:#fff; border:none; border-radius:6px; padding:10px 20px; font-weight:bold; cursor:pointer; font-size:14px; transition:background 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'"><i class="bi bi-save2-fill me-1"></i> Guardar Avance en Historial</button>
                    </div>
                </form>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:15px;">
            <!-- HISTORIAL DE AVANCES -->
            <div class="rh-card" style="flex:1; margin-bottom:0; box-shadow:none; border:1px solid #e2e8f0; padding:15px;">
                <h3 style="font-size:16px; margin-top:0; margin-bottom:15px; text-align:center;"><i class="bi bi-clock-history me-1"></i> Historial de Avances</h3>
                <div style="max-height: 250px; overflow-y:auto; border-radius:6px; border: 1px solid #e2e8f0;">
                    <table class="rh-table" style="font-size:13px; margin:0; width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#1e3a8a; color:white;">
                                <th style="padding:8px 12px; font-weight:bold; border:none; text-align:center;">Fecha</th>
                                <th style="padding:8px 12px; font-weight:bold; border:none;">Descripción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_avances" style="background:white;">
                            <!-- Llenado vía JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: NUEVA RUTINA -->
<!-- ============================================== -->
<div id="modalNuevaRutina" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalNuevaRutina')">&times;</span>
        <h2 style="margin-bottom:20px; margin-top:0;"><i class="bi bi-arrow-repeat me-2"></i>Crear Nueva Rutina Diaria</h2>
        <form action="{{ route('rutinas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="frecuencia" value="diaria">

            <!-- Título -->
            <div style="margin-bottom:12px;">
                <b>Título de la Rutina *</b>
                <input type="text" name="titulo" required placeholder="Ej: Respaldo de Base de Datos"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Descripción -->
            <div style="margin-bottom:12px;">
                <b>Descripción</b>
                <textarea name="descripcion" rows="3" placeholder="Describe la rutina a detalle..."
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Empleado Responsable -->
            <div style="margin-bottom:14px;">
                <b>Empleado Responsable *</b>
                <select name="empleado_id" required
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
                    <option value="">Selecciona un empleado</option>
                    @if(auth()->check())
                        <option value="{{ auth()->id() }}" style="font-weight:bold;color:#1e3a8a;">YO ({{ auth()->user()->name }})</option>
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

            <!-- ¿Es compartida? -->
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 18px; margin-bottom:14px;">
                <p style="margin:0 0 10px; font-weight:700; font-size:14px; color:#166534;">
                    <i class="bi bi-question-circle-fill me-1"></i> ¿Es una rutina compartida?
                </p>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff;">
                        <input type="radio" name="_rutina_compartida" value="si" onchange="toggleRutinaCompartida('si')" style="accent-color:#16a34a;"> Sí
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff;">
                        <input type="radio" name="_rutina_compartida" value="no" onchange="toggleRutinaCompartida('no')" style="accent-color:#16a34a;"> No
                    </label>
                </div>
            </div>

            <!-- Lista de compañeros (si es compartida) -->
            <div id="bloque_rutina_compartida" style="display:none; margin-bottom:14px;">
                <b>¿Con quién(es) la compartirás?</b>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; max-height:140px; overflow-y:auto; margin-top:6px;">
                    @foreach ($empleadosRH as $emp)
                        <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer; font-size:14px;">
                            <input type="checkbox" name="rutina_compartidos[]" value="{{ $emp['id'] ?? $emp->id }}" disabled style="accent-color:#16a34a; width:15px; height:15px;">
                            {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- ¿Se hará más de una vez al día? -->
            <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; margin-bottom:14px;">
                <p style="margin:0 0 10px; font-weight:700; font-size:14px; color:#92400e;">
                    <i class="bi bi-question-circle-fill me-1"></i> ¿Se hará más de una vez al día?
                </p>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff;">
                        <input type="radio" name="_rutina_repetida" value="si" onchange="toggleRutinaRepetida('si')" style="accent-color:#d97706;"> Sí
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #fde68a; font-weight:600; background:#fff;">
                        <input type="radio" name="_rutina_repetida" value="no" onchange="toggleRutinaRepetida('no')" style="accent-color:#d97706;"> No
                    </label>
                </div>
            </div>

            <!-- Cuántas veces al día (si es repetida) -->
            <div id="bloque_rutina_veces" style="display:none; margin-bottom:14px;">
                <b>¿Cuántas veces al día? *</b>
                <input type="number" name="veces_al_dia" id="rutina_veces_input" min="2" max="20" placeholder="Ej: 3" disabled
                    style="width:100%;padding:8px;border:1px solid #fde68a;border-radius:6px;box-sizing:border-box;margin-top:4px;">
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalNuevaRutina')">Cancelar</button>
                <button type="submit" class="btn-form">Crear Rutina</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRutinaCompartida(val) {
    const bloque = document.getElementById('bloque_rutina_compartida');
    bloque.style.display = val === 'si' ? 'block' : 'none';
    bloque.querySelectorAll('input[type=checkbox]').forEach(el => el.disabled = val !== 'si');
}

function toggleRutinaRepetida(val) {
    const bloque = document.getElementById('bloque_rutina_veces');
    const input  = document.getElementById('rutina_veces_input');
    bloque.style.display = val === 'si' ? 'block' : 'none';
    input.disabled = val !== 'si';
    if (val !== 'si') input.value = '';
}
</script>


<!-- ============================================== -->
<!-- MODAL: NUEVA ACTIVIDAD IMPREVISTA -->
<!-- ============================================== -->
<div id="modalNuevaImprevista" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalNuevaImprevista')">&times;</span>
        <h2 style="margin-bottom: 20px; margin-top:0; color:#d97706;"><i class="bi bi-lightning-fill me-2"></i>Registrar Actividad Imprevista</h2>
        <form action="{{ route('actividades-imprevistas.store') }}" method="POST">
            @csrf

            <!-- Título -->
            <div style="margin-bottom:12px;">
                <b>Título *</b>
                <input type="text" name="titulo" required placeholder="Ej: Falla de internet masiva"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Descripción Detallada -->
            <div style="margin-bottom:12px;">
                <b>Descripción Detallada *</b>
                <textarea name="descripcion_detallada" rows="3" required placeholder="¿Qué pasó exactamente?"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Motivo -->
            <div style="margin-bottom:12px;">
                <b>Motivo *</b>
                <input type="text" name="motivo" required placeholder="¿Por qué tuviste que atenderlo?"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Resultado Obtenido -->
            <div style="margin-bottom:12px;">
                <b>Resultado Obtenido *</b>
                <textarea name="resultado_obtenido" rows="2" required placeholder="Ej: El internet regresó a la normalidad en todo el piso"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Estado -->
            <div style="margin-bottom:12px;">
                <b>Estado *</b>
                <select name="estado" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
                    <option value="finalizada" selected>Terminada / Completada al momento</option>
                    <option value="pendiente">Se quedó pendiente de terminar</option>
                    <option value="en_proceso">Sigue en proceso de atención</option>
                </select>
            </div>

            <!-- Empleado Asignado -->
            <div style="margin-bottom:12px;">
                <b>Empleado Asignado *</b>
                <select name="empleado_id" required
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
                    <option value="">Selecciona un empleado</option>
                    @if(auth()->check())
                        <option value="{{ auth()->id() }}" style="font-weight:bold;color:#1e3a8a;">YO ({{ auth()->user()->name }})</option>
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

            <!-- ¿Alguien más colaboró? -->
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 18px; margin-bottom:14px;">
                <p style="margin:0 0 10px; font-weight:700; font-size:14px; color:#166534;">
                    <i class="bi bi-question-circle-fill me-1"></i> ¿Alguien más colaboró?
                </p>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff;">
                        <input type="radio" name="_colaboro" value="si" onchange="toggleColaboradores('si')" style="accent-color:#16a34a;"> Sí
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; padding:7px 16px; border-radius:8px; border:2px solid #bbf7d0; font-weight:600; background:#fff;">
                        <input type="radio" name="_colaboro" value="no" onchange="toggleColaboradores('no')" style="accent-color:#16a34a;"> No
                    </label>
                </div>
            </div>

            <!-- Lista de colaboradores (si respondió Sí) -->
            <div id="bloque_colaboradores" style="display:none; margin-bottom:14px;">
                <b>¿Quién(es) te ayudaron?</b>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; max-height:140px; overflow-y:auto; margin-top:6px;">
                    @foreach ($empleadosRH as $emp)
                        <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer; font-size:14px;">
                            <input type="checkbox" name="colaboradores[]" value="{{ $emp['id'] ?? $emp->id }}" style="accent-color:#16a34a; width:15px; height:15px;">
                            {{ $emp['name'] ?? $emp['nombre'] ?? 'Usuario' }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Hora Inicio / Hora Fin / Tiempo calculado -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:8px;">
                <div>
                    <b>Hora Inicio</b>
                    <input type="time" name="hora_inicio" id="imp_hora_inicio" oninput="calcImpTime()"
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;margin-top:4px;">
                </div>
                <div>
                    <b>Hora Fin</b>
                    <input type="time" name="hora_fin" id="imp_hora_fin" oninput="calcImpTime()"
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;margin-top:4px;">
                </div>
            </div>
            <!-- Tiempo calculado automáticamente -->
            <div style="margin-bottom:14px; font-size:13px; color:#6b7280; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-stopwatch"></i>
                <span id="imp_tiempo_calc">Configura las horas para calcular el tiempo invertido...</span>
            </div>
            <!-- Hidden: horas_invertidas calculadas -->
            <input type="hidden" name="horas_invertidas" id="imp_horas_hidden" value="0">

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalNuevaImprevista')">Cancelar</button>
                <button type="submit" class="btn-form" style="background:#f59e0b; color:#1a1a1a;">Guardar Imprevisto</button>
            </div>
        </form>
    </div>
<!-- ============================================== -->
<!-- MODAL: EDITAR RUTINA -->
<!-- ============================================== -->
<div id="modalEditarRutina" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalEditarRutina')">&times;</span>
        <h2 style="margin-bottom:20px; margin-top:0;"><i class="bi bi-pencil-square me-2"></i>Editar Rutina Diaria</h2>
        <form action="" method="POST" id="formEditarRutina">
            @csrf
            @method('PUT')

            <!-- Título -->
            <div style="margin-bottom:12px;">
                <b>Título de la Rutina *</b>
                <input type="text" name="titulo" id="edit_rutina_titulo" required
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Descripción -->
            <div style="margin-bottom:12px;">
                <b>Descripción</b>
                <textarea name="descripcion" id="edit_rutina_descripcion" rows="3" placeholder="Describe la rutina..."
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Veces al día -->
            <div style="margin-bottom:12px;">
                <b>¿Cuántas veces al día se repetirá? *</b>
                <input type="number" name="veces_al_dia" id="edit_rutina_veces" min="1" max="20" required
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;margin-top:4px;">
            </div>
            
            <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
                <button type="button" onclick="confirmDeleteRutinaModal()" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:6px; font-weight:bold; cursor:pointer;">
                    <i class="bi bi-trash-fill me-1"></i> Eliminar Rutina
                </button>
                <div>
                    <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalEditarRutina')">Cancelar</button>
                    <button type="submit" class="btn-form">Guardar Cambios</button>
                </div>
            </div>
        </form>

        <form action="" method="POST" id="formEliminarRutina" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: EDITAR ACTIVIDAD IMPREVISTA -->
<!-- ============================================== -->
<div id="modalEditarImprevista" class="rh-modal">
    <div class="rh-modal-content">
        <span class="rh-modal-close" onclick="cerrarModal('modalEditarImprevista')">&times;</span>
        <h2 style="margin-bottom: 20px; margin-top:0; color:#d97706;"><i class="bi bi-pencil-square me-2"></i>Editar Actividad Imprevista</h2>
        <form action="" method="POST" id="formEditarImprevista">
            @csrf
            @method('PUT')

            <!-- Título -->
            <div style="margin-bottom:12px;">
                <b>Título *</b>
                <input type="text" name="titulo" id="edit_imprevista_titulo" required placeholder="Ej: Falla de internet masiva"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Descripción Detallada -->
            <div style="margin-bottom:12px;">
                <b>Descripción Detallada *</b>
                <textarea name="descripcion_detallada" id="edit_imprevista_descripcion" rows="3" required placeholder="¿Qué pasó exactamente?"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Motivo -->
            <div style="margin-bottom:12px;">
                <b>Motivo *</b>
                <input type="text" name="motivo" id="edit_imprevista_motivo" required placeholder="¿Por qué tuviste que atenderlo?"
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
            </div>

            <!-- Resultado Obtenido -->
            <div style="margin-bottom:12px;">
                <b>Resultado Obtenido *</b>
                <textarea name="resultado_obtenido" id="edit_imprevista_resultado" rows="2" required placeholder="Resultado..."
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;"></textarea>
            </div>

            <!-- Estado -->
            <div style="margin-bottom:12px;">
                <b>Estado *</b>
                <select name="estado" id="edit_imprevista_estado" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
                    <option value="finalizada">Terminada / Completada</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="en_pausa">En Pausa</option>
                </select>
            </div>

            <!-- Empleado Asignado -->
            <div style="margin-bottom:12px;">
                <b>Empleado Asignado *</b>
                <select name="empleado_id" id="edit_imprevista_empleado" required
                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box;font-family:inherit;margin-top:4px;">
                    <option value="">Selecciona un empleado</option>
                    @if(auth()->check())
                        <option value="{{ auth()->id() }}" style="font-weight:bold;color:#1e3a8a;">YO ({{ auth()->user()->name }})</option>
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

            <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
                <button type="button" onclick="confirmDeleteImprevistaModal()" class="btn-ver" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:6px; font-weight:bold; cursor:pointer;">
                    <i class="bi bi-trash-fill me-1"></i> Eliminar Imprevisto
                </button>
                <div>
                    <button type="button" class="btn-ver" style="background:#6b7280; margin-right:10px;" onclick="cerrarModal('modalEditarImprevista')">Cancelar</button>
                    <button type="submit" class="btn-form">Guardar Cambios</button>
                </div>
            </div>
        </form>

        <form action="" method="POST" id="formEliminarImprevista" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
function toggleColaboradores(val) {
    const bloque = document.getElementById('bloque_colaboradores');
    if (bloque) {
        bloque.style.display = val === 'si' ? 'block' : 'none';
        bloque.querySelectorAll('input[type=checkbox]').forEach(el => el.disabled = val !== 'si');
    }
}

function toggleRutinaCompartida(val) {
    const bloque = document.getElementById('bloque_rutina_compartida');
    if (bloque) {
        bloque.style.display = val === 'si' ? 'block' : 'none';
        bloque.querySelectorAll('input[type=checkbox]').forEach(el => el.disabled = val !== 'si');
    }
}

function toggleRutinaRepetida(val) {
    const bloque = document.getElementById('bloque_rutina_veces');
    const input = document.getElementById('rutina_veces_input');
    if (bloque && input) {
        if (val === 'si') {
            bloque.style.display = 'block';
            input.disabled = false;
            input.required = true;
            if (!input.value || input.value < 2) input.value = 2;
        } else {
            bloque.style.display = 'none';
            input.disabled = true;
            input.required = false;
            input.value = 1;
        }
    }
}

function calcImpTime() {
    const t1 = document.getElementById('imp_hora_inicio')?.value;
    const t2 = document.getElementById('imp_hora_fin')?.value;
    const label = document.getElementById('imp_tiempo_calc');
    const hidden = document.getElementById('imp_horas_hidden');

    if (t1 && t2) {
        const d1 = new Date("1970-01-01T" + t1 + ":00");
        const d2 = new Date("1970-01-01T" + t2 + ":00");
        let diffMs = d2 - d1;
        if (diffMs < 0) diffMs += 24 * 60 * 60 * 1000;

        const totalMin = Math.floor(diffMs / (1000 * 60));
        const hrs = (totalMin / 60).toFixed(1);
        if (label) label.textContent = `${hrs} Horas (Calculado automáticamente)`;
        if (hidden) hidden.value = hrs;
    } else {
        if (label) label.textContent = '0 Horas';
        if (hidden) hidden.value = 0;
    }
}

function safeBtoa(str) {
    return btoa(unescape(encodeURIComponent(str)));
}
function safeAtob(str) {
    if (!str) return '';
    try {
        if (str.trim().startsWith('{') || str.trim().startsWith('[')) {
            return str;
        }
        return decodeURIComponent(escape(atob(str)));
    } catch (e1) {
        try {
            let binary = atob(str);
            let bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                bytes[i] = binary.charCodeAt(i);
            }
            return new TextDecoder().decode(bytes);
        } catch (e2) {
            try {
                return atob(str);
            } catch(e3) {
                return str;
            }
        }
    }
}

function abrirModal(id) {
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

function openEditModalFromRow(btn) {
    let elWithData = null;
    if (btn) {
        elWithData = (btn.dataset && btn.dataset.actividad) ? btn : (btn.closest ? btn.closest('[data-actividad]') : null);
        while (elWithData && (!elWithData.dataset || !elWithData.dataset.actividad)) {
            elWithData = elWithData.parentElement ? elWithData.parentElement.closest('[data-actividad]') : null;
        }
    }
    let rawData = (elWithData && elWithData.dataset && elWithData.dataset.actividad) ? elWithData.dataset.actividad : '';
    openEditModal({ dataset: { actividad: rawData } });
}

function openEditModal(btn) {
    cerrarModal('modalFicha');
    let actividad = null;
    let rawData = '';

    if (btn) {
        let elWithData = (btn.dataset && btn.dataset.actividad) ? btn : (btn.closest ? btn.closest('[data-actividad]') : null);
        while (elWithData && (!elWithData.dataset || !elWithData.dataset.actividad)) {
            elWithData = elWithData.parentElement ? elWithData.parentElement.closest('[data-actividad]') : null;
        }
        if (elWithData && elWithData.dataset && elWithData.dataset.actividad) {
            rawData = elWithData.dataset.actividad;
        }
    }

    if (rawData) {
        try {
            let decoded = safeAtob(rawData);
            actividad = JSON.parse(decoded);
        } catch(e) {
            console.warn("Could not parse rawData for openEditModal", e);
        }
    }

    if (!actividad && window.currentActividad) {
        actividad = window.currentActividad;
    }

    if (!actividad) {
        alert("No se encontró la información de la actividad para editar.");
        return;
    }

    try {
        let baseUrl = getAppBaseUrl();
        let formEdit = document.getElementById('formEditar');
        if (formEdit) {
            formEdit.action = `${baseUrl}/actividades/${actividad.id}`;
        }
        
        if (document.getElementById('edit_titulo')) {
            document.getElementById('edit_titulo').value = actividad.titulo || '';
        }
        if (document.getElementById('edit_descripcion')) {
            document.getElementById('edit_descripcion').value = actividad.descripcion || '';
        }
        if (document.getElementById('edit_empleado')) {
            document.getElementById('edit_empleado').value = actividad.empleado_id || '';
        }
        if (document.getElementById('edit_estado')) {
            document.getElementById('edit_estado').value = actividad.estado || 'pendiente';
        }
        if (document.getElementById('edit_prioridad')) {
            document.getElementById('edit_prioridad').value = actividad.prioridad || 'media';
        }
        
        // Sencilla:
        if (document.getElementById('edit_sencilla_no')) {
            document.getElementById('edit_sencilla_no').checked = true;
            toggleEditSencilla('no');
        }

        // Compartida:
        if (document.getElementById('edit_compartida_no')) {
            document.getElementById('edit_compartida_no').checked = true;
            toggleEditCompartida('no');
        }

        // Duracion:
        if (actividad.modalidad === 'varios_dias' || (actividad.fecha_inicio && actividad.fecha_estimada_fin && actividad.fecha_inicio !== actividad.fecha_estimada_fin)) {
            if (document.getElementById('edit_mas_un_dia_si')) {
                document.getElementById('edit_mas_un_dia_si').checked = true;
                toggleEditDuracion('si');
            }
            if (document.getElementById('edit_fecha_inicio')) {
                document.getElementById('edit_fecha_inicio').value = actividad.fecha_inicio ? actividad.fecha_inicio.substring(0,10) : '';
            }
            if (document.getElementById('edit_fecha_fin')) {
                document.getElementById('edit_fecha_fin').value = actividad.fecha_estimada_fin ? actividad.fecha_estimada_fin.substring(0,10) : '';
            }
        } else {
            if (document.getElementById('edit_mas_un_dia_no')) {
                document.getElementById('edit_mas_un_dia_no').checked = true;
                toggleEditDuracion('no');
            }
            if (document.getElementById('edit_tiempo_estimado')) {
                document.getElementById('edit_tiempo_estimado').value = actividad.tiempo_estimado || '';
            }
        }
        
        abrirModal('modalEditar');
    } catch(err) {
        console.error("Error al abrir modal de edición de actividad:", err);
        alert("Ocurrió un error al cargar los datos para editar la actividad.");
    }
}

function openEditRutinaModal(btn) {
    let rutina = null;
    let rawData = '';

    if (btn) {
        let elWithData = (btn.dataset && btn.dataset.rutina) ? btn : (btn.closest ? btn.closest('[data-rutina]') : null);
        while (elWithData && (!elWithData.dataset || !elWithData.dataset.rutina)) {
            elWithData = elWithData.parentElement ? elWithData.parentElement.closest('[data-rutina]') : null;
        }
        if (elWithData && elWithData.dataset && elWithData.dataset.rutina) {
            rawData = elWithData.dataset.rutina;
        }
    }

    if (rawData) {
        try {
            let decoded = safeAtob(rawData);
            rutina = JSON.parse(decoded);
        } catch(e) {
            console.warn("Could not parse rawData rutina", e);
        }
    }

    if (!rutina && window.currentRutina) {
        rutina = window.currentRutina;
    }

    if (!rutina) {
        alert("No se pudo encontrar la información de la rutina para editar.");
        return;
    }

    try {
        window.currentRutina = rutina;
        let baseUrl = getAppBaseUrl();
        
        let formEdit = document.getElementById('formEditarRutina');
        if (formEdit) {
            formEdit.action = `${baseUrl}/rutinas/${rutina.id}`;
        }

        let formDelete = document.getElementById('formEliminarRutina');
        if (formDelete) {
            formDelete.action = `${baseUrl}/rutinas/${rutina.id}`;
        }
        
        if (document.getElementById('edit_rutina_titulo')) {
            document.getElementById('edit_rutina_titulo').value = rutina.titulo || '';
        }
        if (document.getElementById('edit_rutina_descripcion')) {
            document.getElementById('edit_rutina_descripcion').value = rutina.descripcion || '';
        }
        if (document.getElementById('edit_rutina_veces')) {
            document.getElementById('edit_rutina_veces').value = rutina.veces_al_dia || rutina.veces || 1;
        }
        
        abrirModal('modalEditarRutina');
    } catch(err) {
        console.error("Error al abrir modal de edición de rutina:", err);
        alert("Ocurrió un error al cargar la rutina para editar.");
    }
}

function getAppBaseUrl() {
    let appUrl = "{{ url('/') }}".replace(/\/$/, '');
    try {
        let parsedApp = new URL(appUrl);
        let path = parsedApp.pathname;
        if (path === '/') path = '';
        return window.location.origin + path;
    } catch(e) {
        return window.location.origin;
    }
}

function openShowModal(btn) {
    let targetEl = (btn && btn.closest) ? (btn.closest('[data-id], [data-actividad]') || btn) : btn;
    let actividadId = targetEl ? (targetEl.dataset ? targetEl.dataset.id : null) : null;
    if (!actividadId && targetEl && targetEl.dataset && targetEl.dataset.actividad) {
        let strData = targetEl.dataset.actividad;
        if (strData) {
            try {
                let actObj = JSON.parse(safeAtob(strData));
                actividadId = actObj ? actObj.id : null;
            } catch(e) {}
        }
    }
    if (!actividadId) return;

    let fetchUrl = `${getAppBaseUrl()}/actividades/${actividadId}/details`;

    fetch(fetchUrl, {
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(r => {
        if (!r.ok) {
            throw new Error(`HTTP ${r.status}: ${r.statusText}`);
        }
        return r.json();
    })
    .then(actividad => {
        window.currentActividad = actividad;
        let btnEdit = document.getElementById('ficha_btn_editar');
        if (btnEdit) btnEdit.dataset.actividad = safeBtoa(JSON.stringify(actividad));

        let formDel = document.getElementById('form_delete_actividad');
        if (formDel) formDel.action = `${getAppBaseUrl()}/actividades/${actividad.id}`;
        
        let avanceId = document.getElementById('avance_actividad_id');
        if (avanceId) avanceId.value = actividad.id;

        let formSlider = document.getElementById('form-slider-avance');
        if (formSlider) formSlider.action = `${getAppBaseUrl()}/actividades/${actividad.id}`;
        
        let elPrioridad = document.getElementById('ficha_prioridad');
        if (elPrioridad) elPrioridad.textContent = 'Prioridad: ' + (actividad.prioridad ? actividad.prioridad.toUpperCase() : '');
        
        let sel = document.getElementById('ficha_estado_select');
        if (sel) {
            sel.value = actividad.estado;
            let colorMap = {
                'pendiente': '#fef3c7', 'en_proceso': '#dbeafe', 'en_pausa': '#f3f4f6', 'finalizada': '#dcfce7', 'atrasada': '#fee2e2', 'cancelada': '#f1f5f9'
            };
            let txtMap = {
                'pendiente': '#92400e', 'en_proceso': '#1e40af', 'en_pausa': '#475569', 'finalizada': '#166534', 'atrasada': '#991b1b', 'cancelada': '#64748b'
            };
            sel.style.backgroundColor = colorMap[actividad.estado] || '#f1f5f9';
            sel.style.color = txtMap[actividad.estado] || '#475569';
        }
        
        let elTitulo = document.getElementById('ficha_titulo');
        if (elTitulo) elTitulo.textContent = actividad.titulo || '';

        let elDesc = document.getElementById('ficha_descripcion');
        if (elDesc) elDesc.textContent = actividad.descripcion || '';

        let elImp = document.getElementById('ficha_impacto');
        if (elImp) elImp.textContent = actividad.impacto || '';
        
        let fIni = actividad.fecha_inicio ? actividad.fecha_inicio.substring(0,10) : '';
        let fFin = actividad.fecha_estimada_fin ? actividad.fecha_estimada_fin.substring(0,10) : '';
        let elFechas = document.getElementById('ficha_fechas');
        if (elFechas) elFechas.textContent = `Del ${fIni} al ${fFin} (${actividad.tiempo_estimado || 'N/A'})`;
        
        let slider = document.getElementById('slider_avance');
        if (slider) slider.value = actividad.porcentaje_avance || 0;

        let elAvanceVal = document.getElementById('ficha_avance_text_val');
        if (elAvanceVal) elAvanceVal.textContent = actividad.porcentaje_avance || 0;

        // Show/Hide completada/reabrir buttons conditionally based on status
        let btnCompletar = document.getElementById('ficha_btn_completar');
        let btnReabrir = document.getElementById('ficha_btn_reabrir');
        if (btnCompletar && btnReabrir) {
            if (actividad.estado === 'finalizada') {
                btnCompletar.style.display = 'none';
                btnReabrir.style.display = 'block';
            } else {
                btnCompletar.style.display = 'block';
                btnReabrir.style.display = 'none';
            }
        }

        const tbody = document.getElementById('tabla_avances');
        if (tbody) {
            tbody.innerHTML = '';
            if (actividad.avances && actividad.avances.length > 0) {
                actividad.avances.forEach(av => {
                    let row = document.createElement('tr');
                    
                    let aprobacionCell = '';
                    if (av.estado === 'pendiente_aprobacion') {
                        if (window.isBoss) {
                            aprobacionCell = `
                                <div style="display:flex; gap:5px;">
                                    <button type="button" class="btn-ver" style="background:#10b981; padding:3px 8px; font-size:10px;" onclick="aprobarAvance(${av.id})">Aprobar</button>
                                    <button type="button" class="btn-ver" style="background:#ef4444; padding:3px 8px; font-size:10px;" onclick="rechazarAvance(${av.id})">Rechazar</button>
                                </div>
                            `;
                        } else {
                            aprobacionCell = '<span style="color:#d97706; font-weight:bold;">Pendiente de Aprobación</span>';
                        }
                    } else if (av.estado === 'aprobado') {
                        aprobacionCell = '<span style="color:#10b981; font-weight:bold;">Aprobado</span>';
                    } else {
                        aprobacionCell = `<span style="color:#ef4444; font-weight:bold;" title="${av.comentario_jefe || ''}">Rechazado</span>`;
                    }

                    row.innerHTML = `
                        <td style="padding:4px;">${av.fecha_avance ? av.fecha_avance.substring(0,10) : ''}<br><small style="color:#6b7280;">${av.hora_inicio} - ${av.hora_fin}</small></td>
                        <td style="padding:4px;">${av.horas_trabajadas || '0'} hrs</td>
                        <td style="padding:4px;">
                            <strong>Hizo:</strong> ${av.que_se_hizo}<br>
                            <strong>Resultado:</strong> ${av.resultado_final}
                        </td>
                        <td style="padding:4px;">${aprobacionCell}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:#6b7280; padding:15px;">Aún no se registran avances.</td></tr>`;
            }
        }

        const chatBox = document.getElementById('chat_mensajes');
        if (chatBox) {
            chatBox.innerHTML = '';
            if (actividad.mensajes && actividad.mensajes.length > 0) {
                actividad.mensajes.forEach(msj => {
                    let bubble = document.createElement('div');
                    bubble.style.marginBottom = '8px';
                    bubble.style.padding = '6px 10px';
                    bubble.style.borderRadius = '8px';
                    bubble.style.maxWidth = '85%';
                    
                    let senderName = msj.remitente ? msj.remitente.name : (msj.user ? msj.user.name : 'Usuario');
                    let currentAuthId = (typeof authId !== 'undefined') ? authId : 0;
                    let isMe = msj.remitente_id == currentAuthId || msj.user_id == currentAuthId;
                    
                    if (isMe) {
                        bubble.style.background = '#dbeafe';
                        bubble.style.color = '#1e3a8a';
                        bubble.style.marginLeft = 'auto';
                    } else {
                        bubble.style.background = '#f1f5f9';
                        bubble.style.color = '#334155';
                    }
                    
                    bubble.innerHTML = `
                        <div style="font-size:11px; font-weight:bold; margin-bottom:2px;">${senderName}</div>
                        <div style="font-size:13px; line-height:1.4;">${msj.mensaje}</div>
                        <div style="font-size:9px; text-align:right; margin-top:3px; opacity:0.7;">${msj.created_at ? msj.created_at.substring(11,16) : ''}</div>
                    `;
                    chatBox.appendChild(bubble);
                });
            } else {
                chatBox.innerHTML = `<div style="text-align:center; color:#94a3b8; padding:20px; font-size:13px;">No hay mensajes en este chat.</div>`;
            }
        }
        
        abrirModal('modalFicha');
        if (chatBox) {
            setTimeout(() => {
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 100);
        }
    })
    .catch(err => {
        console.error("Error al obtener detalles de la actividad:", err);
        alert("Ocurrió un error al cargar la ficha de la actividad (" + err.message + ").");
    });
}

function enviarMensaje(ev) {
    ev.preventDefault();
    let input = document.getElementById('chat_input_mensaje');
    let mensajeText = input.value;
    let actividadId = document.getElementById('avance_actividad_id').value;

    if (!mensajeText.trim()) return;

    fetch(`${window.APP_BASE_URL}/actividades/${actividadId}/mensajes`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ mensaje: mensajeText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            fetch(`${window.APP_BASE_URL}/actividades/${actividadId}/details`)
                .then(r => r.json())
                .then(act => {
                    openShowModal({ dataset: { id: act.id } });
                });
        }
    });
}

function aprobarAvance(id) {
    let comentario = prompt("Escribe un comentario opcional para el empleado:");
    if (comentario === null) return;
    
    fetch(`${window.APP_BASE_URL}/avances-actividad/${id}/aprobar`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ comentario_jefe: comentario })
    })
    .then(r => {
        window.location.reload();
    });
}

function rechazarAvance(id) {
    let comentario = prompt("Comentario de rechazo (Obligatorio):");
    if (!comentario) {
        if (comentario === "") alert("El comentario es obligatorio para rechazar un avance.");
        return;
    }
    
    fetch(`${window.APP_BASE_URL}/avances-actividad/${id}/rechazar`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ comentario_jefe: comentario })
    })
    .then(r => {
        window.location.reload();
    });
}

function updatePorcentaje(slider) {
    let form = document.getElementById('form-slider-avance');
    let fd = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    }).then(r => r.json()).then(data => {
        // Updated successfully
    });
}

function calcTime() {
    let t1 = document.getElementById('modal_hi').value;
    let t2 = document.getElementById('modal_hf').value;
    if(t1 && t2) {
        let d1 = new Date("1970-01-01T" + t1 + "Z");
        let d2 = new Date("1970-01-01T" + t2 + "Z");
        let diff = (d2 - d1) / 60000;
        if(diff < 0) diff += 1440;
        let hrs = Math.floor(diff/60);
        let mins = diff % 60;
        let str = "";
        if (hrs > 0) str += hrs + " hora(s) ";
        if (mins > 0) str += mins + " minuto(s)";
        document.getElementById('label_tiempo_calc').innerHTML = '<span style="color:#059669; font-weight:bold;">' + str + '</span>';
    }
}

function aprobarActividadDesdeFicha() {
    let id = document.getElementById('avance_actividad_id').value;
    if (!id) return;
    if (!confirm('¿Marcar esta actividad como finalizada/aprobada?')) return;
    
    fetch(`${window.APP_BASE_URL}/actividades/${id}/aprobar`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No tienes permisos.'));
        }
    });
}

function reabrirActividadDesdeFicha() {
    let id = document.getElementById('avance_actividad_id').value;
    if (!id) return;
    if (!confirm('¿Reabrir esta actividad?')) return;
    
    fetch(`${window.APP_BASE_URL}/actividades/${id}/reabrir`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No tienes permisos.'));
        }
    });
}

function openEditRutinaModal(btn) {
    let rutina = null;
    let rawData = btn ? (btn.dataset ? btn.dataset.rutina : '') : '';
    if (!rawData && btn && btn.closest) {
        let row = btn.closest('[data-rutina], tr');
        rawData = row ? (row.dataset ? row.dataset.rutina : '') : '';
    }

    if (rawData) {
        try {
            let decoded = safeAtob(rawData);
            rutina = JSON.parse(decoded);
        } catch(e) {
            console.warn("Could not parse rawData rutina", e);
        }
    }

    if (!rutina && window.currentRutina) {
        rutina = window.currentRutina;
    }

    if (!rutina) {
        alert("No se pudo encontrar la información de la rutina para editar.");
        return;
    }

    try {
        window.currentRutina = rutina;
        let baseUrl = getAppBaseUrl();
        
        let formEdit = document.getElementById('formEditarRutina');
        if (formEdit) {
            formEdit.action = `${baseUrl}/rutinas/${rutina.id}`;
        }

        let formDelete = document.getElementById('formEliminarRutina');
        if (formDelete) {
            formDelete.action = `${baseUrl}/rutinas/${rutina.id}`;
        }
        
        if (document.getElementById('edit_rutina_titulo')) {
            document.getElementById('edit_rutina_titulo').value = rutina.titulo || '';
        }
        if (document.getElementById('edit_rutina_descripcion')) {
            document.getElementById('edit_rutina_descripcion').value = rutina.descripcion || '';
        }
        if (document.getElementById('edit_rutina_veces')) {
            document.getElementById('edit_rutina_veces').value = rutina.veces_al_dia || rutina.veces || 1;
        }
        
        abrirModal('modalEditarRutina');
    } catch(err) {
        console.error("Error al abrir modal de edición de rutina:", err);
        alert("Ocurrió un error al cargar la rutina para editar.");
    }
}

function confirmDeleteRutinaModal() {
    let formDelete = document.getElementById('formEliminarRutina');
    if (formDelete && formDelete.action) {
        if (confirm('¿Seguro que deseas eliminar esta rutina definitivamente?')) {
            formDelete.submit();
        }
    } else {
        alert('No se encontró la información para eliminar la rutina.');
    }
}

function openEditImprevistaModal(btn) {
    let imprevisto = null;
    let rawData = '';

    if (btn) {
        let elWithData = (btn.dataset && btn.dataset.imprevisto) ? btn : (btn.closest ? btn.closest('[data-imprevisto]') : null);
        while (elWithData && (!elWithData.dataset || !elWithData.dataset.imprevisto)) {
            elWithData = elWithData.parentElement ? elWithData.parentElement.closest('[data-imprevisto]') : null;
        }
        if (elWithData && elWithData.dataset && elWithData.dataset.imprevisto) {
            rawData = elWithData.dataset.imprevisto;
        }
    }

    if (rawData) {
        try {
            let decoded = safeAtob(rawData);
            imprevisto = JSON.parse(decoded);
        } catch(e) {
            console.warn("Could not parse rawData imprevisto", e);
        }
    }

    if (!imprevisto && window.currentImprevisto) {
        imprevisto = window.currentImprevisto;
    }

    if (!imprevisto) {
        alert("No se pudo encontrar la información del imprevisto para editar.");
        return;
    }

    try {
        window.currentImprevisto = imprevisto;
        let baseUrl = getAppBaseUrl();
        
        let formEdit = document.getElementById('formEditarImprevista');
        if (formEdit) {
            formEdit.action = `${baseUrl}/actividades-imprevistas/${imprevisto.id}`;
        }

        let formDelete = document.getElementById('formEliminarImprevista');
        if (formDelete) {
            formDelete.action = `${baseUrl}/actividades-imprevistas/${imprevisto.id}`;
        }
        
        if (document.getElementById('edit_imprevista_titulo')) {
            document.getElementById('edit_imprevista_titulo').value = imprevisto.titulo || '';
        }
        if (document.getElementById('edit_imprevista_descripcion')) {
            document.getElementById('edit_imprevista_descripcion').value = imprevisto.descripcion_detallada || imprevisto.descripcion || '';
        }
        if (document.getElementById('edit_imprevista_motivo')) {
            document.getElementById('edit_imprevista_motivo').value = imprevisto.motivo || '';
        }
        if (document.getElementById('edit_imprevista_resultado')) {
            document.getElementById('edit_imprevista_resultado').value = imprevisto.resultado_obtenido || '';
        }
        if (document.getElementById('edit_imprevista_estado')) {
            document.getElementById('edit_imprevista_estado').value = imprevisto.estado || 'finalizada';
        }
        if (document.getElementById('edit_imprevista_empleado')) {
            document.getElementById('edit_imprevista_empleado').value = imprevisto.empleado_id || '';
        }
        
        abrirModal('modalEditarImprevista');
    } catch(err) {
        console.error("Error al abrir modal de edición de imprevisto:", err);
        alert("Ocurrió un error al cargar el imprevisto para editar.");
    }
}

function confirmDeleteImprevistaModal() {
    let formDelete = document.getElementById('formEliminarImprevista');
    if (formDelete && formDelete.action) {
        if (confirm('¿Seguro que deseas eliminar este imprevisto definitivamente?')) {
            formDelete.submit();
        }
    } else {
        alert('No se encontró la información para eliminar el imprevisto.');
    }
}

function handleRutinaCheck(cb, event) {
    event.stopPropagation();
    let rutinaId = cb.dataset.id;
    let val = parseInt(cb.value);
    let isChecked = cb.checked;
    
    // Determine new quantity of executions
    let newQty = isChecked ? val : val - 1;
    
    // Select all checkboxes for this routine in the parent container
    let container = cb.parentElement;
    let cbs = container.querySelectorAll('.rutina-check-box');
    cbs.forEach(item => {
        let itemVal = parseInt(item.value);
        if (itemVal <= newQty) {
            item.checked = true;
        } else {
            item.checked = false;
        }
    });

    let baseUrl = getAppBaseUrl();

    // Send update request to server
    fetch(`${baseUrl}/rutinas/${rutinaId}/set-ejecuciones`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ cantidad: newQty, cantidad_ejecuciones: newQty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Checkboxes already updated on UI side.
        } else {
            alert('Error al registrar ejecución: ' + (data.message || 'No se pudo guardar.'));
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de red al registrar ejecución.');
        location.reload();
    });
}

function actualizarEstadoRapido(nuevoEstado) {
    let actividadId = document.getElementById('avance_actividad_id').value;
    if (!actividadId) return;

    let sel = document.getElementById('ficha_estado_select');
    let colorMap = { 'pendiente': '#fef3c7', 'en_proceso': '#dbeafe', 'en_pausa': '#f3f4f6', 'finalizada': '#dcfce7', 'atrasada': '#fee2e2', 'cancelada': '#f1f5f9' };
    let txtMap = { 'pendiente': '#92400e', 'en_proceso': '#1e40af', 'en_pausa': '#475569', 'finalizada': '#166534', 'atrasada': '#991b1b', 'cancelada': '#64748b' };
    if (sel) {
        sel.style.backgroundColor = colorMap[nuevoEstado] || '#f1f5f9';
        sel.style.color = txtMap[nuevoEstado] || '#475569';
    }

    let baseUrl = getAppBaseUrl();

    fetch(`${baseUrl}/actividades/${actividadId}/estado`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ estado: nuevoEstado })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(err => {
        alert("Ocurrió un error al cambiar el estado.");
    });
}
</script>
