<?php

namespace Icatech\PermisoRolMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Icatech\PermisoRolMenu\Models\Permiso;

class PermisoController extends Controller
{
    public function create()
    {
        // Implementar lógica para mostrar formulario de creación si es necesario
    }

    public function store(Request $request)
    {
        try {
            $permiso = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500'
            ]);

            $permiso = Permiso::create($permiso);
            return redirect()->back()->with('success', 'Permiso creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el permiso: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $permiso = Permiso::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500'
            ]);

            $permiso->update($data);
            return redirect()->back()->with('success', 'Permiso actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el permiso: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->delete();
            return redirect()->back()->with('success', 'Permiso eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el permiso: ' . $e->getMessage());
        }
    }
}
