<?php

namespace Icatech\PermisoRolMenu\View;

use Illuminate\View\View;
use Icatech\PermisoRolMenu\Models\Permiso;
use Icatech\PermisoRolMenu\Helpers\MenuHelper;

class MenuComposer
{
    /**
     * Crear una instancia del view composer del menú.
     */
    public function __construct()
    {
        //
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = auth()->user();
        $menu = [];
        
        if ($user && method_exists($user, 'roles') && method_exists($user, 'permissions')) {
            // Si tiene rol all-access, mostrar todos los permisos activos
            if ($user->roles()->where('especial', 'all-access')->exists()) {
                $allPermissions = Permiso::where('activo', true)
                    ->whereNotNull('clave_orden')
                    ->get()
                    ->sortBy('clave_orden');
            } else {
                // Permisos directos
                $directPermissions = $user->permissions()
                    ->where('activo', true)
                    ->whereNotNull('clave_orden')
                    ->get();

                // Permisos por roles
                $rolePermissions = Permiso::whereHas('roles', function ($q) use ($user) {
                    $q->whereIn('tblz_roles.id', $user->roles->pluck('id'));
                })
                    ->where('activo', true)
                    ->whereNotNull('clave_orden')
                    ->get();

                // Unir y eliminar duplicados
                $allPermissions = $directPermissions->merge($rolePermissions)->unique('id')->sortBy('clave_orden');
            }

            $menu = MenuHelper::buildMenu($allPermissions);
        }
        
        $view->with('menuDinamico', $menu);
    }
}
