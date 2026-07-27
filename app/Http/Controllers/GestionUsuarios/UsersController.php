<?php

namespace App\Http\Controllers\GestionUsuarios;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    // Listado de módulos del sistema
    protected $modules = [
        'administracion' => 'Administración',
        'operaciones' => 'Operaciones',
        'proveedores' => 'Proveedores',
        'actividades' => 'Actividades diarias',
        'vehiculos' => 'Parque Vehicular',
        'sistemas' => 'Sistemas y registros',
        'registros' => 'Registros médicos',
        'otros' => 'Otros (?)',
        'users' => 'Gestión de usuarios',
    ];

    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        $currentUser = Auth::user();

        if ($currentUser->rol === 'admin') {
            // El administrador puede ver a todos los usuarios
            $users = User::with(['jefe', 'area'])->get();
        } else {
            // El jefe solo puede ver a sus subordinados a cargo
            $users = User::with(['jefe', 'area'])->where('jefe_id', $currentUser->id)->get();
        }

        return view('gestion_usuarios.index', compact('users'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        $currentUser = Auth::user();
        
        // Obtener los jefes disponibles
        $jefes = User::where('rol', 'jefe')->get();

        // Obtener las áreas disponibles
        $areas = Area::where('activo', true)->get();

        // Filtrar los módulos que este usuario puede asignar
        $availableModules = [];
        foreach ($this->modules as $key => $label) {
            if ($currentUser->hasPermission($key)) {
                $availableModules[$key] = $label;
            }
        }

        // Obtener subordinados libres (sin jefe)
        $subordinados = User::whereIn('rol', ['empleado', 'practicante'])->whereNull('jefe_id')->get();

        return view('gestion_usuarios.create', compact('jefes', 'availableModules', 'areas', 'subordinados'));
    }

    /**
     * Almacena un nuevo usuario.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Validaciones
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'password' => 'required|string|min:4',
            'rol' => 'required|string|in:admin,jefe,directivo,empleado,practicante',
            'jefe_id' => 'nullable|exists:users,id',
            'area_id' => 'nullable|exists:areas,id',
            'departamentos_jefe' => 'nullable|array',
        ]);

        $rol = $request->rol;
        $jefe_id = $request->jefe_id;
        $area_id = $request->area_id;
        $departamento = null;

        // Si el usuario creador es Jefe, forzar jerarquía:
        // Solo puede crear empleados/practicantes y el jefe es él mismo.
        if ($currentUser->rol === 'jefe') {
            $rol = in_array($request->rol, ['empleado', 'practicante']) ? $request->rol : 'practicante';
            $jefe_id = $currentUser->id;
        }

        // Determinar departamento y jefe según el rol
        if ($rol === 'jefe') {
            $departamento = implode(' / ', $request->input('departamentos_jefe', []));
            $area_id = null; // Un jefe no tiene una sola área única
        } else if ($rol === 'admin') {
            $departamento = 'Administración';
            $adminArea = Area::where('nombre', 'Administración')->first();
            $area_id = $adminArea ? $adminArea->id : null;
            $jefe_id = null;
        } else if ($rol === 'directivo') {
            $departamento = 'Dirección';
            $area_id = null;
            $jefe_id = null;
        } else {
            // Empleado o Practicante: departamento se deriva del área seleccionada
            if ($area_id) {
                $area = Area::find($area_id);
                if ($area) {
                    $departamento = $area->nombre;
                    // Buscar automáticamente el Jefe de este departamento si el creador no es Jefe
                    if ($currentUser->rol !== 'jefe') {
                        $jefe = User::obtenerJefeDeArea($area_id);
                        if ($jefe) {
                            $jefe_id = $jefe->id;
                        }
                    }
                }
            }
        }

        // Si es administrador o jefe, no puede ser su propio jefe
        if ($jefe_id == $currentUser->id && $currentUser->rol !== 'jefe') {
            $jefe_id = null;
        }

        // Filtrar y procesar los permisos asignados mediante checkboxes
        $assignedPerms = $request->input('permisos', []);
        if ($currentUser->rol === 'jefe') {
            // Un jefe sólo puede otorgar permisos que él mismo tiene permitidos
            $assignedPerms = array_filter($assignedPerms, function($perm) use ($currentUser) {
                return $currentUser->hasPermission($perm);
            });
        }
        $permisosString = implode(',', $assignedPerms);

        $newUser = User::create([
            'name' => $request->name,
            'email' => null, // Quitamos correo definitivamente
            'password' => Hash::make($request->password),
            'password_plain' => Crypt::encryptString($request->password),
            'area_id' => $area_id,
            'rol' => $rol,
            'jefe_id' => $jefe_id,
            'activo' => true,
            'departamento' => $departamento,
            'permisos' => $permisosString,
        ]);

        if ($rol === 'jefe') {
            // Desvincular a los antiguos (en este caso es nuevo jefe, pero por consistencia)
            User::where('jefe_id', $newUser->id)->update(['jefe_id' => null]);
            // Reconectar a los seleccionados manualmente si es que hay
            if ($request->has('subordinados')) {
                User::whereIn('id', $request->input('subordinados'))->update(['jefe_id' => $newUser->id]);
            }
            // Auto-asignar según áreas
            $newUser->asignarEmpleadosDeAreas();
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(User $user)
    {
        $currentUser = Auth::user();

        // Si es jefe, sólo puede editar sus propios subordinados
        if ($currentUser->rol === 'jefe' && $user->jefe_id !== $currentUser->id) {
            abort(403, 'No tienes permisos para editar este usuario.');
        }

        $jefes = User::where('rol', 'jefe')->get();
        $areas = Area::where('activo', true)->get();

        // Módulos que el creador/editor puede asignar
        $availableModules = [];
        foreach ($this->modules as $key => $label) {
            if ($currentUser->hasPermission($key)) {
                $availableModules[$key] = $label;
            }
        }

        // Módulos que el usuario ya tiene asignados
        $userPermissions = array_map('trim', explode(',', strtolower($user->permisos)));

        // Subordinados libres + los que ya tiene
        $subordinados = User::whereIn('rol', ['empleado', 'practicante'])
                            ->where(function($q) use ($user) {
                                $q->whereNull('jefe_id')->orWhere('jefe_id', $user->id);
                            })->get();

        return view('gestion_usuarios.edit', compact('user', 'jefes', 'availableModules', 'userPermissions', 'areas', 'subordinados'));
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Si es jefe, sólo puede editar sus propios subordinados
        if ($currentUser->rol === 'jefe' && $user->jefe_id !== $currentUser->id) {
            abort(403, 'No tienes permisos para editar este usuario.');
        }

        // Validaciones
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'password' => 'nullable|string|min:4',
            'rol' => 'required|string|in:admin,jefe,directivo,empleado,practicante',
            'jefe_id' => 'nullable|exists:users,id',
            'area_id' => 'nullable|exists:areas,id',
            'departamentos_jefe' => 'nullable|array',
        ]);

        $rol = $request->rol;
        $jefe_id = $request->jefe_id;
        $area_id = $request->area_id;
        $departamento = null;

        // Si el editor es Jefe, no puede cambiar el rol a administrador/jefe
        if ($currentUser->rol === 'jefe') {
            $rol = in_array($request->rol, ['empleado', 'practicante']) ? $request->rol : $user->rol;
            $jefe_id = $currentUser->id;
        }

        // Determinar departamento y jefe según el rol
        if ($rol === 'jefe') {
            $departamento = implode(' / ', $request->input('departamentos_jefe', []));
            $area_id = null;
        } else if ($rol === 'admin') {
            $departamento = 'Administración';
            $adminArea = Area::where('nombre', 'Administración')->first();
            $area_id = $adminArea ? $adminArea->id : null;
            $jefe_id = null;
        } else if ($rol === 'directivo') {
            $departamento = 'Dirección';
            $area_id = null;
            $jefe_id = null;
        } else {
            // Empleado o Practicante: departamento se deriva del área seleccionada
            if ($area_id) {
                $area = Area::find($area_id);
                if ($area) {
                    $departamento = $area->nombre;
                    // Buscar automáticamente el Jefe de este departamento si el editor no es Jefe
                    if ($currentUser->rol !== 'jefe') {
                        $jefe = User::obtenerJefeDeArea($area_id);
                        if ($jefe) {
                            $jefe_id = $jefe->id;
                        }
                    }
                }
            }
        }

        // Filtrar y procesar permisos
        $assignedPerms = $request->input('permisos', []);
        if ($currentUser->rol === 'jefe') {
            $assignedPerms = array_filter($assignedPerms, function($perm) use ($currentUser) {
                return $currentUser->hasPermission($perm);
            });
        }
        $permisosString = implode(',', $assignedPerms);

        $data = [
            'name' => $request->name,
            'email' => null, // Quitamos correo definitivamente
            'departamento' => $departamento,
            'area_id' => $area_id,
            'rol' => $rol,
            'jefe_id' => $jefe_id,
            'permisos' => $permisosString,
        ];

        // Cambiar contraseña solo si se ingresa una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_plain'] = Crypt::encryptString($request->password);
        }

        $user->update($data);

        // Actualizar subordinados si es que aplica
        if ($rol === 'jefe') {
            // Desvincular a los antiguos
            User::where('jefe_id', $user->id)->update(['jefe_id' => null]);
            // Reconectar a los seleccionados
            if ($request->has('subordinados')) {
                User::whereIn('id', $request->input('subordinados'))->update(['jefe_id' => $user->id]);
            }
            // Auto-asignar según áreas
            $user->asignarEmpleadosDeAreas();
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // Si es jefe, sólo puede eliminar a sus subordinados
        if ($currentUser->rol === 'jefe' && $user->jefe_id !== $currentUser->id) {
            abort(403, 'No tienes permisos para eliminar este usuario.');
        }

        // Evitar que el administrador principal se auto-elimine
        if ($user->rol === 'admin') {
            return redirect()->route('users.index')->withErrors('No se puede eliminar el administrador principal.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}

