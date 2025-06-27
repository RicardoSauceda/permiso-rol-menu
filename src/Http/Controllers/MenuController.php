<?php

namespace Icatech\PermisoRolMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Icatech\PermisoRolMenu\Models\Permiso;

class MenuController extends Controller
{
    public function create() {}

    public function store(Request $request)
    {
        try {
            $menu = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'icono' => 'nullable|string|max:255',
                'clave_orden' => 'nullable|digits:6|unique:tblz_permisos,clave_orden',
                'activo' => 'boolean',
            ]);

            $menu = Permiso::create($menu);
            return redirect()->back()->with('success', 'Menú creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el menú: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $menu = Permiso::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'icono' => 'nullable|string|max:255',
                'clave_orden' => 'nullable|digits:6|unique:tblz_permisos,clave_orden,' . $id,
                'activo' => 'boolean',
            ]);

            $menu->update($data);
            return redirect()->back()->with('success', 'Menú actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el menú: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $menu = Permiso::findOrFail($id);
            $menu->delete();
            return redirect()->back()->with('success', 'Menú eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el menú: ' . $e->getMessage());
        }
    }
}
