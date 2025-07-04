<?php

namespace Icatech\PermisoRolMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Icatech\PermisoRolMenu\Models\Rol;
use Icatech\PermisoRolMenu\Models\Permiso;

class RolController extends Controller
{
    public function create()
    {
        // Implementar lógica para mostrar formulario de creación si es necesario
    }

    public function showMenus($id)
    {
        $rol = Rol::findOrFail($id);
        $menus = Permiso::whereNotNull('clave_orden')
            ->select('id', 'nombre', 'ruta_corta', 'descripcion', 'activo', 'clave_orden')
            ->orderBy('clave_orden')
            ->get();

        // Utilizar directamente la lógica de buildMenuTree
        $menuController = new MenuController();
        $menuTree = $menuController->buildMenuTree($menus);

        return view('permiso-rol-menu::rol.permisos', compact('menuTree', 'rol'));
    }

    public function showPermisosMenus($id)
    {
        $rol = Rol::findOrFail($id);
        $permisos = Permiso::where('rol_id', $id)
            ->select('id', 'nombre', 'ruta_corta', 'descripcion', 'activo', 'clave_orden')
            ->orderBy('clave_orden')
            ->get();

        return view('permiso-rol-menu::rol.permisos_menus', compact('permisos', 'rol'));
    }

    public function store(Request $request)
    {
        try {
            $rol = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'especial' => 'nullable|sometimes|string|max:255',
            ]);

            $rol = Rol::create($rol);
            return redirect()->back()->with('success', 'Rol creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el rol: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rol = Rol::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'especial' => 'nullable|sometimes|string|max:255',
            ]);

            $rol->update($data);
            return redirect()->back()->with('success', 'Rol actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el rol: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            $rol->delete();
            return redirect()->back()->with('success', 'Rol eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }
}
