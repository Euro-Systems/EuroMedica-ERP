<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #edf1f5;
            color: #1e293b;
        }
        .container {
            display: flex;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        .menu {
            width: 320px;
            background: linear-gradient(180deg, #0d62b6, #2b87d8);
            box-shadow: 4px 0 18px rgba(0,0,0,0.18);
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        .menu-content {
            padding: 36px 26px;
            padding-left: 24px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .menu h2 {
            font-size: 16px;
            font-weight: 700;
            color: #e8f0f7;
            margin-bottom: 40px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .nav-item {
            background-color: rgba(255,255,255,0.09);
            color: #e8f0f7;
            padding: 18px 20px;
            margin-bottom: 14px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            border-left: 4px solid transparent;
            border-radius: 8px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: block;
        }
        .nav-item:hover {
            background-color: rgba(255,255,255,0.18);
            border-left: 4px solid #7dd3fc;
        }
        .content {
            flex: 1;
            padding: 45px;
            padding-right: 25px;
            padding-left: 30px;
            overflow-y: auto;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #dce3ec;
            padding: 28px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            animation: fadeSlide 0.4s ease-out;
            margin-bottom: 25px;
            width: 100%;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex p-0">
        <aside class="menu">
            <div class="menu-content" style="display:flex; flex-direction:column; height:100%;">
                <h2>Gestión de Usuarios</h2>
                <a href="{{ route('users.index') }}" class="nav-item">← Lista de Usuarios</a>
                
                <div style="margin-top: auto; display:flex; flex-direction:column; gap:8px; width:100%;">
                    <a href="{{ url('/') }}" class="nav-item" style="margin:0;">Volver al Inicio</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0; width:100%;">
                        @csrf
                        <button type="submit" class="nav-item btn btn-link text-white text-decoration-none" style="width:100%;text-align:left;border:none;margin:0;padding: 10px 15px;">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <main class="content">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <h2>Editar Usuario: {{ $user->name }}</h2>
                <p class="text-muted">Modifica los detalles de la cuenta, departamento/área y controla sus permisos de acceso.</p>
                
                <form method="POST" action="{{ route('users.update', $user) }}" class="mt-4">
                    @csrf
                    @method('PUT')

                    <!-- 1. Rol primero, y Nombre de usuario -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label fw-bold">Rol</label>
                            @if(Auth::user()->rol === 'admin')
                                <select class="form-select" id="rol" name="rol" onchange="handleRolChange()" required>
                                    <option value="empleado" {{ old('rol', $user->rol) == 'empleado' ? 'selected' : '' }}>Empleado</option>
                                    <option value="practicante" {{ old('rol', $user->rol) == 'practicante' ? 'selected' : '' }}>Practicante</option>
                                    <option value="jefe" {{ old('rol', $user->rol) == 'jefe' ? 'selected' : '' }}>Jefe</option>
                                    <option value="directivo" {{ old('rol', $user->rol) == 'directivo' ? 'selected' : '' }}>Directivo</option>
                                    <option value="admin" {{ old('rol', $user->rol) == 'admin' ? 'selected' : '' }}>Administrador (Servidor)</option>
                                </select>
                            @else
                                <select class="form-select" id="rol" name="rol" onchange="handleRolChange()" required>
                                    <option value="empleado" {{ old('rol', $user->rol) == 'empleado' ? 'selected' : '' }}>Empleado</option>
                                    <option value="practicante" {{ old('rol', $user->rol) == 'practicante' ? 'selected' : '' }}>Practicante</option>
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">Nombre / Usuario</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>

                    <!-- 2. Casillas condicionales de departamento o áreas con letrero de jefe responsable -->
                    <div id="rol-dependencias-container" class="mb-3">
                        
                        <!-- Si es Jefe: checkboxes de departamentos -->
                        <div id="departamentos-jefe-container" style="display: none;">
                            <label class="form-label fw-bold text-dark">Departamentos a cargo (Jefe)</label>
                            <p class="text-muted small">Selecciona los departamentos de los que este usuario será responsable (puedes marcar varios).</p>
                            <div class="row bg-light p-3 rounded border border-light-subtle mb-3">
                                @foreach(['TI', 'ADD', 'MKT', 'Recursos Humanos', 'ADE', 'Nómina', 'Operaciones', 'Administración'] as $dep)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input dept-checkbox" type="checkbox" name="departamentos_jefe[]" value="{{ $dep }}" id="dept_{{ str_replace(' ', '_', $dep) }}"
                                                {{ in_array($dep, array_map('trim', explode(' / ', $user->departamento))) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="dept_{{ str_replace(' ', '_', $dep) }}">
                                                {{ $dep }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <label class="form-label fw-bold text-dark">Asignar subordinados libres o actuales</label>
                                <p class="text-muted small">Selecciona los empleados o practicantes que pertenecerán a este jefe.</p>
                                <div class="row bg-white p-3 rounded border border-light-subtle mb-3">
                                    @forelse($subordinados as $sub)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subordinados[]" value="{{ $sub->id }}" id="sub_{{ $sub->id }}"
                                                    {{ (is_array(old('subordinados')) && in_array($sub->id, old('subordinados'))) || (!old('subordinados') && $sub->jefe_id === $user->id) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="sub_{{ $sub->id }}">
                                                    {{ $sub->name }} <span class="text-muted fw-normal">({{ ucfirst($sub->rol) }})</span>
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small">No hay empleados/practicantes disponibles.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Si es Empleado/Practicante: dropdown de áreas + letrero del responsable -->
                        <div id="area-empleado-container" style="display: none;" class="row">
                            <div class="col-md-6 mb-3">
                                <label for="area_id" class="form-label fw-bold">Área de Asignación</label>
                                <select class="form-select" id="area_id" name="area_id" onchange="updateJefeResponsable()">
                                    <option value="">Selecciona un área...</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" data-nombre="{{ $area->nombre }}" {{ old('area_id', $user->area_id) == $area->id ? 'selected' : '' }}>
                                            {{ $area->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Responsable / Jefe</label>
                                <div class="p-2 border rounded bg-light fw-bold text-primary" id="jefe-responsable-label" style="min-height: 38px; display: flex; align-items: center; font-size: 0.95rem;">
                                    Ninguno asignado
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Contraseña -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="password" class="form-label fw-bold">Contraseña</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="password" name="password" 
                                    value="{{ old('password', $user->plain_password) }}" required style="padding-right: 40px;">
                                <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; display: flex; align-items: center; padding: 0;">
                                    <span id="eye-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Permisos / Módulos de Acceso -->
                    <div class="mb-4 mt-2">
                        <label class="form-label d-block fw-bold mb-2">Permisos / Módulos de Acceso</label>
                        <p class="text-muted small">Selecciona los módulos principales (activar un módulo marcará todas sus opciones por defecto para ahorrar clics).</p>
                        <div class="bg-white rounded border border-light-subtle" style="overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            @forelse($availableModules as $key => $label)
                                <div class="sub-perms-wrapper-row" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px;">
                                    <div class="form-check" style="margin: 0; min-width: 220px;">
                                        <input class="form-check-input module-checkbox" type="checkbox" name="permisos[]" value="{{ $key }}" id="perm_{{ $key }}" 
                                            {{ in_array($key, $userPermissions) ? 'checked' : '' }} onchange="toggleSubPerms('{{ $key }}')">
                                        <label class="form-check-label fw-bold text-primary" for="perm_{{ $key }}" style="cursor: pointer;">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    <div class="sub-perms-container" id="sub_perms_{{ $key }}" style="display: flex; gap: 16px; flex-wrap: wrap;">
                                        @if($key === 'administracion')
                                            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                                <div style="display: flex; gap: 16px; flex-wrap: wrap; background: #f8fafc; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; align-items: center;">
                                                    <span class="fw-bold text-dark" style="font-size: 13px;">Secciones:</span>
                                                    <div class="form-check" style="margin: 0;">
                                                        <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="administracion_rh" id="perm_administracion_rh"
                                                            {{ in_array('administracion_rh', $userPermissions) ? 'checked' : '' }} onchange="toggleRhSubPerms(this.checked)">
                                                        <label class="form-check-label fw-bold text-primary" for="perm_administracion_rh" style="cursor: pointer; font-size: 13px;">
                                                            Recursos Humanos (RH)
                                                        </label>
                                                    </div>
                                                    <div class="form-check" style="margin: 0;">
                                                        <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="administracion_compras" id="perm_administracion_compras"
                                                            {{ in_array('administracion_compras', $userPermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold text-primary" for="perm_administracion_compras" style="cursor: pointer; font-size: 13px;">
                                                            Compras
                                                        </label>
                                                    </div>
                                                    <div class="form-check" style="margin: 0;">
                                                        <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="administracion_nomina" id="perm_administracion_nomina"
                                                            {{ in_array('administracion_nomina', $userPermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold text-primary" for="perm_administracion_nomina" style="cursor: pointer; font-size: 13px;">
                                                            Nómina
                                                        </label>
                                                    </div>
                                                </div>

                                                <div style="margin-left: 10px; border-left: 3px solid #3b82f6; padding-left: 12px;">
                                                    <div class="fw-bold text-secondary mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Permisos específicos de Recursos Humanos (RH):</div>
                                                    @php
                                                        $rhSubPerms = [
                                                            'rh_agendar_citas' => 'Agendar citas (+ Nueva Cita)',
                                                            'rh_aprobar_candidato' => 'Aprobar candidato',
                                                            'rh_ver_citas_realizadas' => 'Ver citas realizadas',
                                                            'rh_ver_historial' => 'Ver historial',
                                                            'rh_ver_editar_candidatos' => 'Ver / editar ficha de candidatos',
                                                            'rh_aprobar_convertir_trabajador' => 'Aprobar y convertir a trabajador / practicante',
                                                            'rh_ver_editar_empleados' => 'Ver / editar ficha de empleados o practicantes',
                                                            'rh_dar_baja_empleado' => 'Dar de baja a un empleado o practicante',
                                                            'rh_gestion_vacaciones' => 'Gestionar solicitudes de vacaciones',
                                                            'rh_gestion_contratos' => 'Agregar, editar o eliminar contratos',
                                                        ];
                                                    @endphp
                                                    <div style="display: flex; gap: 8px 16px; flex-wrap: wrap;">
                                                        @foreach($rhSubPerms as $subVal => $subLabel)
                                                            <div class="form-check" style="margin: 0;">
                                                                <input class="form-check-input sub-perm-checkbox rh-sub-perm" type="checkbox" name="permisos[]" value="{{ $subVal }}" id="perm_{{ $subVal }}"
                                                                    {{ in_array($subVal, $userPermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold text-secondary" for="perm_{{ $subVal }}" style="cursor: pointer; font-size: 13px;">
                                                                    {{ $subLabel }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($key === 'vehiculos')
                                            @php
                                                $vehiculosSubPerms = [
                                                    'vehiculos' => 'Ver módulo',
                                                    'vehiculos_agregar_unidad' => 'Agregar nueva unidad',
                                                    'vehiculos_editar_unidad' => 'Editar la unidad',
                                                    'vehiculos_eliminar_unidad' => 'Eliminar la unidad',
                                                    'vehiculos_ver_sidebar' => 'Ver unidad en sidebar',
                                                    'vehiculos_registrar_servicio' => 'Registrar nuevo servicio',
                                                    'vehiculos_editar_servicio' => 'Editar servicio',
                                                    'vehiculos_eliminar_servicio' => 'Eliminar servicio',
                                                ];
                                            @endphp
                                            <div style="display: flex; gap: 10px 16px; flex-wrap: wrap;">
                                                @foreach($vehiculosSubPerms as $subVal => $subLabel)
                                                    <div class="form-check" style="margin: 0;">
                                                        <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="{{ $subVal }}" id="perm_{{ $subVal }}"
                                                            {{ in_array($subVal, $userPermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold text-secondary" for="perm_{{ $subVal }}" style="cursor: pointer; font-size: 13px;">
                                                            {{ $subLabel }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($key === 'actividades')
                                            @php
                                                $actividadesSubPerms = [
                                                    'actividades' => 'Ver módulo',
                                                    'actividades_tablero' => 'Apartado de Actividades Diarias (Tablero)',
                                                    'actividades_resumen' => 'Resumen general',
                                                    'actividades_mis_actividades' => 'Mis actividades',
                                                    'actividades_reportes' => 'Reportes (propios si es empleado/practicante)',
                                                ];
                                                $allAreas = \App\Models\Area::all();
                                            @endphp
                                            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                                <div style="display: flex; gap: 10px 16px; flex-wrap: wrap;">
                                                    @foreach($actividadesSubPerms as $subVal => $subLabel)
                                                        <div class="form-check" style="margin: 0;">
                                                            <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="{{ $subVal }}" id="perm_{{ $subVal }}"
                                                                {{ in_array($subVal, $userPermissions) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-semibold text-secondary" for="perm_{{ $subVal }}" style="cursor: pointer; font-size: 13px;">
                                                                {{ $subLabel }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div style="background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                                        <span class="fw-bold text-dark" style="font-size: 13px;">Áreas disponibles a visualizar:</span>
                                                        <div class="form-check" style="margin: 0;">
                                                            <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="actividades_ver_areas" id="perm_actividades_ver_areas"
                                                                {{ in_array('actividades_ver_areas', $userPermissions) ? 'checked' : '' }} onchange="toggleAreaSubPerms(this.checked)">
                                                            <label class="form-check-label fw-bold text-primary" for="perm_actividades_ver_areas" style="cursor: pointer; font-size: 13px;">
                                                                ★ Ver todas las áreas
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; gap: 8px 16px; flex-wrap: wrap;">
                                                        @foreach($allAreas as $area)
                                                            @php $areaVal = 'actividades_area_' . $area->id; @endphp
                                                            <div class="form-check" style="margin: 0;">
                                                                <input class="form-check-input sub-perm-checkbox area-sub-perm" type="checkbox" name="permisos[]" value="{{ $areaVal }}" id="perm_{{ $areaVal }}"
                                                                    {{ in_array($areaVal, $userPermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold text-secondary" for="perm_{{ $areaVal }}" style="cursor: pointer; font-size: 13px;">
                                                                    {{ $area->nombre }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @foreach(['ver' => 'Ver', 'crear' => 'Crear/Editar', 'eliminar' => 'Eliminar'] as $subKey => $subLabel)
                                                @php $subVal = $key . '_' . $subKey; @endphp
                                                <div class="form-check" style="margin: 0;">
                                                    <input class="form-check-input sub-perm-checkbox" type="checkbox" name="permisos[]" value="{{ $subVal }}" id="perm_{{ $subVal }}"
                                                        {{ in_array($subVal, $userPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold text-secondary" for="perm_{{ $subVal }}" style="cursor: pointer; font-size: 13px;">
                                                        {{ $subLabel }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 text-muted italic">No tienes módulos disponibles para asignar.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="font-weight: 600;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const jefesList = @json($jefes);
        const currentUserId = {{ Auth::id() }};
        const currentUserRol = "{{ Auth::user()->rol }}";
        const currentUserName = "{{ Auth::user()->name }}";

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const wrapper = document.getElementById('eye-icon-wrapper');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                wrapper.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                `;
            } else {
                passwordInput.type = 'password';
                wrapper.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                `;
            }
        }

        function handleRolChange() {
            const rolSelect = document.getElementById('rol');
            const deptJefeContainer = document.getElementById('departamentos-jefe-container');
            const areaEmpleadoContainer = document.getElementById('area-empleado-container');
            
            if (!rolSelect) return;
            const rol = rolSelect.value;

            if (rol === 'jefe') {
                deptJefeContainer.style.display = 'block';
                areaEmpleadoContainer.style.display = 'none';
            } else if (rol === 'empleado' || rol === 'practicante') {
                deptJefeContainer.style.display = 'none';
                areaEmpleadoContainer.style.display = 'flex';
            } else {
                deptJefeContainer.style.display = 'none';
                areaEmpleadoContainer.style.display = 'none';
            }
            updateJefeResponsable();
        }

        function updateJefeResponsable() {
            const areaSelect = document.getElementById('area_id');
            const label = document.getElementById('jefe-responsable-label');
            if (!areaSelect || !label) return;

            if (currentUserRol === 'jefe') {
                label.textContent = currentUserName + ' (Jefe Directo)';
                return;
            }

            const selectedOption = areaSelect.options[areaSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                label.textContent = 'Ninguno asignado';
                return;
            }

            const areaName = selectedOption.getAttribute('data-nombre');
            
            // Buscar un jefe en la lista cuya propiedad "departamento" contenga el nombre del área
            const matchingJefe = jefesList.find(jefe => {
                if (!jefe.departamento) return false;
                const depts = jefe.departamento.split('/').map(d => d.trim().toLowerCase());
                return depts.includes(areaName.toLowerCase());
            });

            if (matchingJefe) {
                label.textContent = matchingJefe.name + ' (Jefe de ' + matchingJefe.departamento + ')';
            } else {
                label.textContent = 'Ningún Jefe asignado para esta área';
            }
        }

        function toggleSubPerms(moduleKey) {
            const isChecked = document.getElementById('perm_' + moduleKey).checked;
            const container = document.getElementById('sub_perms_' + moduleKey);
            if (container) {
                container.querySelectorAll('.sub-perm-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                });
            }
        }

        function toggleRhSubPerms(isChecked) {
            document.querySelectorAll('.rh-sub-perm').forEach(cb => {
                cb.checked = isChecked;
            });
        }

        function toggleAreaSubPerms(isChecked) {
            document.querySelectorAll('.area-sub-perm').forEach(cb => {
                cb.checked = isChecked;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sub-perm-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        const containerId = this.closest('.sub-perms-container').id;
                        const parentKey = containerId.replace('sub_perms_', '');
                        const parentCb = document.getElementById('perm_' + parentKey);
                        if (parentCb) {
                            parentCb.checked = true;
                        }
                    }
                });
            });
            handleRolChange();
        });
    </script>
</body>
</html>
