<?php

namespace Icatech\PermisoRolMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Icatech\PermisoRolMenu\Models\Rol;

class RolController extends Controller
{
    public function create()
    {
        // Implementar lógica para mostrar formulario de creación si es necesario
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
