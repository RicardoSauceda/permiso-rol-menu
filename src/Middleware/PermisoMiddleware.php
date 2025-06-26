<?php

namespace Icatech\PermisoRolMenu\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermisoMiddleware
{
    public function handle(Request $request, Closure $next, $permisos)
    {
        if (!Auth::check()) {
            abort(401, 'No autorizado');
        }

        $user = Auth::user();

        if (!method_exists($user, 'hasPermission')) {
            abort(403, 'El modelo de usuario no tiene el trait de permisos configurado');
        }

        if (!$user->hasPermission($permisos)) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
