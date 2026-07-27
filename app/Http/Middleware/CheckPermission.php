<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Si no está autenticado, redirigir al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Si es el módulo de usuarios, verificar si es administrador o jefe
        if ($module === 'users') {
            if (Auth::user()->email === 'admin@admin.com' || Auth::user()->rol === 'admin' || Auth::user()->rol === 'jefe') {
                return $next($request);
            }
            abort(403, 'No tienes permisos para gestionar usuarios.');
        }

        // Si se solicita rh, nomina o compras, son sub-secciones de administracion
        if ($module === 'rh') {
            if (Auth::user()->hasPermission('administracion_rh') || Auth::user()->hasPermission('administracion')) {
                return $next($request);
            }
            abort(403, 'No tienes permisos para acceder a Recursos Humanos.');
        }

        if ($module === 'nomina') {
            if (Auth::user()->hasPermission('administracion_nomina') || Auth::user()->hasPermission('administracion')) {
                return $next($request);
            }
            abort(403, 'No tienes permisos para acceder a Nómina.');
        }

        if ($module === 'compras') {
            if (Auth::user()->hasPermission('administracion_compras') || Auth::user()->hasPermission('administracion')) {
                return $next($request);
            }
            abort(403, 'No tienes permisos para acceder a Compras.');
        }

        // Si se solicita administracion, permitir si tiene el modulo completo o cualquiera de sus secciones
        if ($module === 'administracion') {
            if (Auth::user()->hasPermission('administracion') || 
                Auth::user()->hasPermission('administracion_rh') || 
                Auth::user()->hasPermission('administracion_nomina') || 
                Auth::user()->hasPermission('administracion_compras')) {
                return $next($request);
            }
            abort(403, 'No tienes permisos para acceder a Administración.');
        }

        // Para otros módulos, verificar permisos específicos del usuario
        if (!Auth::user()->hasPermission($module)) {
            abort(403, 'No tienes permisos para acceder al módulo de ' . ucfirst($module) . '.');
        }

        return $next($request);
    }
}
