<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area_id',
        'rol',
        'activo',
        'departamento',
        'puesto',
        'permisos',
        'jefe_id',
        'password_plain',
    ];

    /**
     * Relación con el jefe directo del usuario.
     */
    public function jefe()
    {
        return $this->belongsTo(User::class, 'jefe_id');
    }

    /**
     * Relación con los subordinados (empleados/practicantes) a cargo.
     */
    public function subordinados()
    {
        return $this->hasMany(User::class, 'jefe_id');
    }

    /**
     * Accesor para obtener la contraseña en texto claro (desencriptada).
     */
    public function getPlainPasswordAttribute()
    {
        if (!$this->password_plain) {
            return '';
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->password_plain);
        } catch (\Exception $e) {
            return 'No disponible';
        }
    }

    /**
     * Verifica si el usuario tiene permiso para acceder a un módulo específico.
     *
     * @param string $module
     * @return bool
     */
    public function hasPermission($module)
    {
        if ($this->email === 'admin@admin.com' || $this->rol === 'admin') {
            return true;
        }

        if (!$this->permisos) {
            return false;
        }

        $allowed = array_map('trim', explode(',', strtolower($this->permisos)));

        if (in_array('todos', $allowed)) {
            return true;
        }

        $moduleLower = strtolower($module);

        if (in_array($moduleLower, $allowed)) {
            return true;
        }

        // Si se tiene 'administracion' completo, concede acceso a cualquier sub-permiso de administración o rh
        if (in_array('administracion', $allowed)) {
            if ($moduleLower === 'administracion' || str_starts_with($moduleLower, 'administracion_') || str_starts_with($moduleLower, 'rh_') || $moduleLower === 'rh') {
                return true;
            }
        }

        // Si se tiene 'administracion_rh', concede acceso a cualquier sub-permiso 'rh_*'
        if (in_array('administracion_rh', $allowed)) {
            if ($moduleLower === 'rh' || $moduleLower === 'administracion_rh' || $moduleLower === 'administracion' || str_starts_with($moduleLower, 'rh_')) {
                return true;
            }
        }

        // Si se verifica un sub-permiso de rh (ej. rh_agendar_citas)
        if (str_starts_with($moduleLower, 'rh_') && (in_array('administracion_rh', $allowed) || in_array('administracion', $allowed))) {
            return true;
        }

        // Si se verifica el módulo general 'administracion' o 'administracion_rh'
        if ($moduleLower === 'administracion' || $moduleLower === 'administracion_rh') {
            foreach ($allowed as $p) {
                if (str_starts_with($p, 'administracion') || str_starts_with($p, 'rh_')) {
                    return true;
                }
            }
        }

        // Si se tiene 'actividades' completo, concede acceso a cualquier sub-permiso de actividades
        if (in_array('actividades', $allowed)) {
            if ($moduleLower === 'actividades' || str_starts_with($moduleLower, 'actividades_')) {
                return true;
            }
        }

        // Si se verifica un permiso específico de área (ej: actividades_area_1)
        if (str_starts_with($moduleLower, 'actividades_area_')) {
            $areaId = str_replace('actividades_area_', '', $moduleLower);
            return $this->canViewArea($areaId);
        }

        // Si se verifica un sub-permiso de actividades
        if (str_starts_with($moduleLower, 'actividades_') && (in_array('actividades', $allowed) || in_array('actividades_ver_areas', $allowed))) {
            return true;
        }

        // Si se verifica el módulo general 'actividades', conceder acceso si tiene cualquier sub-permiso
        if ($moduleLower === 'actividades') {
            foreach ($allowed as $p) {
                if (str_starts_with($p, 'actividades')) {
                    return true;
                }
            }
        }

        // Si se verifica un sub-permiso de vehículos y el usuario tiene el permiso global 'vehiculos'
        if (str_starts_with($moduleLower, 'vehiculos_') && in_array('vehiculos', $allowed)) {
            return true;
        }

        // Si se verifica el módulo general 'vehiculos', conceder acceso si el usuario tiene cualquier sub-permiso de vehículos
        if ($moduleLower === 'vehiculos') {
            foreach ($allowed as $p) {
                if (str_starts_with($p, 'vehiculos')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canViewArea($areaId)
    {
        if ($this->email === 'admin@admin.com' || $this->rol === 'admin') {
            return true;
        }

        if (!$this->permisos) {
            return false;
        }

        $allowed = array_map('trim', explode(',', strtolower($this->permisos)));

        if (in_array('todos', $allowed) || in_array('actividades', $allowed) || in_array('actividades_ver_areas', $allowed)) {
            return true;
        }

        return in_array('actividades_area_' . $areaId, $allowed);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación de muchos a muchos con las áreas que un jefe supervisa.
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_user');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'empleado_id');
    }

    public function actividadesAsignadas()
    {
        return $this->hasMany(Actividad::class, 'jefe_id');
    }

    public function avances()
    {
        return $this->hasMany(AvanceActividad::class, 'empleado_id');
    }

    public function actividadesImprevistas()
    {
        return $this->hasMany(ActividadImprevista::class, 'empleado_id');
    }

    public function rutinas()
    {
        return $this->hasMany(Rutina::class, 'empleado_id');
    }

    public function mensajesConversacion()
    {
        return $this->hasMany(MensajeActividad::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determina si el usuario es jefe y es responsable de una determinada área.
     */
    public function isJefeForArea($area)
    {
        if ($this->rol !== 'jefe') {
            return false;
        }

        $areaId = is_object($area) ? $area->id : null;
        $areaName = is_object($area) ? $area->nombre : $area;

        if ($areaId) {
            return $this->areas->contains($areaId);
        }

        return $this->areas->contains('nombre', $areaName);
    }

    /**
     * Sincroniza subordinados para este jefe según sus departamentos.
     */
    public function asignarEmpleadosDeAreas()
    {
        if ($this->rol !== 'jefe') {
            return;
        }

        $targetAreaIds = $this->areas()->pluck('areas.id')->toArray();

        if (empty($targetAreaIds)) {
            User::where('jefe_id', $this->id)->update(['jefe_id' => null]);
            return;
        }

        // 1. Desvincular de este jefe a todos los empleados de las áreas que ya no maneja
        User::where('jefe_id', $this->id)
            ->whereNotIn('area_id', $targetAreaIds)
            ->update(['jefe_id' => null]);

        // 2. Asignar todos los empleados/practicantes de estas áreas a este jefe
        User::whereIn('rol', ['empleado', 'practicante'])
            ->whereIn('area_id', $targetAreaIds)
            ->update(['jefe_id' => $this->id]);
    }

    /**
     * Busca al jefe responsable de una determinada área.
     */
    public static function obtenerJefeDeArea($areaId)
    {
        if (!$areaId) return null;

        return self::where('rol', 'jefe')
            ->where('activo', true)
            ->whereHas('areas', function($q) use ($areaId) {
                $q->where('areas.id', $areaId);
            })
            ->first();
    }
}

