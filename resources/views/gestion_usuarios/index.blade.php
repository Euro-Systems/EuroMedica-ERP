<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #edf1f5;
            color: #1e293b;
        }
        .container-fluid {
            display: flex;
            height: 100vh;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            overflow: hidden;
        }
        .menu {
            width: 230px;
            background: linear-gradient(180deg,#1e3a8a,#3b82f6);
            padding: 25px;
            color: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 18px rgba(0,0,0,0.18);
            margin: 0;
        }
        .menu-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .menu h2 {
            font-size: 20px;
            font-weight: 500;
            color: #ffffff;
            margin-bottom: 20px;
            margin-top: 0;
        }
        .nav-item {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            color: #ffffff;
            font-size: 15px;
            text-decoration: none;
            display: block;
            border-left: none;
            transition: all 0.25s ease;
        }
        .nav-item:hover {
            background-color: #ffffff;
            color: #1e3a8a;
            font-weight: bold;
        }
        .btn-add {
            background-color: #22c55e;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: normal;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            border: none;
            box-shadow: none;
        }
        .btn-add:hover {
            background-color: #16a34a;
            color: white;
        }
        .content {
            flex: 1;
            padding: 14px;
            overflow-y: auto;
        }
        .card {
            background: #fff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
            margin-bottom: 10px;
            width: 100%;
            border: none;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-shrink: 0;
        }
        .tab {
            padding: 10px 16px;
            border-radius: 10px 10px 0 0;
            background: #e5e7eb;
            cursor: pointer;
        }
        .tab.active {
            background: #fff;
            color: #1e3a8a;
            border-bottom: 3px solid #3b82f6;
        }
        .rh-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rh-table th, .rh-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .rh-table th {
            background: #1e3a8a;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex p-0">
        <aside class="menu">
            <div class="menu-content" style="display:flex; flex-direction:column; height:100%;">
                <h2>Gestión de<br>Usuarios</h2>
                
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
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="tabs">
                <div class="tab active">Usuarios Registrados</div>
            </div>
            <div class="card">
                <h2 style="display:flex; justify-content:space-between; align-items:center;">
                    Lista de Usuarios
                    <a href="{{ route('users.create') }}" class="btn-add">+ Agregar Usuario</a>
                </h2>
                <div class="table-responsive mt-3">
                    <table class="rh-table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre / Usuario</th>
                                <th>Rol</th>
                                <th>Jefe Directo</th>
                                <th>Departamento / Área</th>
                                <th>Contraseña</th>
                                <th>Módulos Permitidos</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td class="fw-bold">{{ $user->name }}</td>
                                    <td>
                                        @if($user->rol === 'admin')
                                            <span style="color:#dc3545; font-weight:700;">Administrador</span>
                                        @elseif($user->rol === 'jefe')
                                            <span style="color:#0d6efd; font-weight:700;">Jefe</span>
                                        @elseif($user->rol === 'empleado')
                                            <span style="color:#198754; font-weight:700;">Empleado</span>
                                        @else
                                            <span style="color:#6c757d; font-weight:700;">Practicante</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->jefe)
                                            <span class="text-secondary fw-semibold">{{ $user->jefe->name }}</span>
                                        @else
                                            <span class="text-muted italic">Ninguno</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->rol === 'jefe')
                                            {{ $user->departamento ?? 'Sin asignar' }}
                                        @else
                                            {{ $user->area ? $user->area->nombre : ($user->departamento ?? 'Sin asignar') }}
                                        @endif
                                    </td>
                                    <td>
                                        <code class="px-2 py-1 bg-light rounded text-dark" style="font-size: 0.9rem;">
                                            {{ $user->plain_password ?: 'No disponible' }}
                                        </code>
                                    </td>
                                    <td>
                                        @php
                                            $permList = explode(',', $user->permisos);
                                            $permList = array_map('trim', $permList);
                                            $permList = array_map('strtolower', $permList);
                                            $permList = array_filter($permList);

                                            if (empty($permList)) {
                                                $displayPerms = [];
                                            } elseif (in_array('todos', $permList)) {
                                                $displayPerms = ['Todos'];
                                            } else {
                                                $groups = [];
                                                $categoryNames = [
                                                    'administracion' => 'Administración',
                                                    'operaciones' => 'Operaciones',
                                                    'proveedores' => 'Proveedores',
                                                    'actividades' => 'Actividades',
                                                    'vehiculos' => 'Vehículos',
                                                    'sistemas' => 'Sistemas',
                                                    'registros' => 'Registros Médicos',
                                                    'users' => 'Usuarios',
                                                    'otros' => 'Otros',
                                                ];

                                                foreach ($permList as $perm) {
                                                    if (strpos($perm, 'administracion_') === 0) {
                                                        $sub = str_replace('administracion_', '', $perm);
                                                        $subNames = ['rh' => 'RH', 'nomina' => 'Nómina', 'compras' => 'Compras'];
                                                        $groups['administracion'][] = $subNames[$sub] ?? ucfirst($sub);
                                                    } elseif (strpos($perm, '_') !== false) {
                                                        $parts = explode('_', $perm);
                                                        $parent = $parts[0];
                                                        $action = $parts[1];
                                                        $actionAbbr = ['ver' => 'V', 'crear' => 'C', 'eliminar' => 'E'];
                                                        $groups[$parent][] = $actionAbbr[$action] ?? ucfirst($action);
                                                    } else {
                                                        if (!isset($groups[$perm])) {
                                                            $groups[$perm] = [];
                                                        }
                                                    }
                                                }

                                                $displayPerms = [];
                                                foreach ($groups as $parent => $subs) {
                                                    $parentLabel = $categoryNames[$parent] ?? ucfirst($parent);
                                                    if (!empty($subs)) {
                                                        sort($subs);
                                                        $displayPerms[] = $parentLabel . ' (' . implode(',', $subs) . ')';
                                                    } else {
                                                        $displayPerms[] = $parentLabel;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if(empty($displayPerms))
                                            <span class="text-muted small">Ninguno</span>
                                        @else
                                            <div style="display:flex; flex-wrap:wrap; gap:4px; max-width: 320px;">
                                                @foreach($displayPerms as $dp)
                                                    <span class="badge bg-info text-dark" style="font-size:11px; padding:3px 6px;">{{ $dp }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm d-flex align-items-center justify-content-center" style="background:#facc15;color:black;width:28px;height:28px;padding:0;border-radius:6px;" title="Ver/Editar Usuario">
                                                <i class="bi bi-pencil-fill" style="font-size:12px;"></i>
                                            </a>
                                            @if($user->rol !== 'admin')
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Está seguro de eliminar a este usuario?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" style="width:28px;height:28px;padding:0;border-radius:6px;" title="Eliminar Usuario">
                                                        <i class="bi bi-trash" style="font-size:13px;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
