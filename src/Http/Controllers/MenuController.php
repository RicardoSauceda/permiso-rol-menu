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

    // Funciones para manejar el árbol de menús

    public function treeStore(Request $request)
    {
        $data = $request->validate([
            'permisoName'        => 'required|string|max:255',
            'rutaCorta'        => 'required|string|max:255',
            'permisoDescripcion' => 'nullable|string',
            'clave_orden_padre'  => 'nullable|string|max:6',
        ]);

        $menu = new Permiso;
        $menu->nombre = trim($data['permisoName']);
        $menu->ruta_corta = trim($data['rutaCorta']);
        $menu->descripcion = trim($data['permisoDescripcion'] ?? '');

        $parentClave = $data['clave_orden_padre'] ?? null;
        $parent = null;
        if ($parentClave) {
            $parent = Permiso::where('clave_orden', $parentClave)->first();
        }
        if ($parent && $parent->clave_orden) {
            $parentClave = $parent->clave_orden;

            if (substr($parentClave, 2, 2) === '00' && substr($parentClave, 4, 2) === '00') {
                $prefixLen = 2;
            } elseif (substr($parentClave, 4, 2) === '00') {
                $prefixLen = 4;
            } else {
                $prefixLen = 6;
            }
            $childPos = $prefixLen;
            $prefix = substr($parentClave, 0, $prefixLen);
            $suffix = ($childPos + 2 < strlen($parentClave)) ? substr($parentClave, $childPos + 2) : '';
            // Obtener claves de hermanos
            $siblings = Permiso::where('clave_orden', 'like', $prefix . '%')->pluck('clave_orden');
            $max = 0;
            foreach ($siblings as $clave) {
                if (strlen($clave) >= $childPos + 2) {
                    $seg = substr($clave, $childPos, 2);
                    if (is_numeric($seg)) {
                        $max = max($max, (int) $seg);
                    }
                }
            }
            $newSeg = str_pad($max + 1, 2, '0', STR_PAD_LEFT);
            $menu->clave_orden = $prefix . $newSeg . $suffix;
        } else {
            // Fallback: sin padre o sin clave asignada
            $menu->clave_orden = '010000';
        }
        $menu->activo = true;
        $menu->save();
        return redirect()->route('menus.index')
            ->with('success', 'Menú agregado correctamente.');
    }


    private function buildMenuTree($menus, $nivel = 1, $claveProveniente = '')
    {
        $tree = [];

        $filtered = $menus->filter(function ($menu) use ($nivel, $claveProveniente) {
            $segmentos = str_split($menu->clave_orden, 2);
            if ($nivel === 1) {
                return $segmentos[0] !== '00' && $segmentos[1] === '00' && $segmentos[2] === '00';
            }
            $parentSeg = str_split($claveProveniente, 2);
            if ($nivel === 2) {
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] !== '00' && $segmentos[2] === '00';
            }
            if ($nivel === 3) {
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] === $parentSeg[1] && $segmentos[2] !== '00';
            }
            return false;
        });

        foreach ($filtered as $menu) {
            $item = $menu->toArray();
            $submenu = $this->buildMenuTree($menus, $nivel + 1, $menu->clave_orden);
            if (!empty($submenu)) {
                $item['submenu'] = $submenu;
            }
            $tree[] = $item;
        }

        return $tree;
    }

    public function statusUpdate(Request $request, $id)
    {
        $menu = Permiso::find($id);
        if ($menu) {
            $newStatus = !$menu->activo;
            $menu->activo = $newStatus;
            $menu->save();

            $updatedChildIds = $this->updateSubmenusStatus($menu, $newStatus);
            // Incluir el ID del menú padre
            $allUpdated = array_merge([$menu->id], $updatedChildIds);
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'updatedIds' => $allUpdated
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Menu no encontrado'], 404);
    }

    /**
     * Actualiza el estado de los submenus y devuelve IDs actualizados.
     *
     * @param Permiso $menu
     * @param bool $status
     * @return array
     */
    private function updateSubmenusStatus(Permiso $menu, bool $status): array
    {
        // * Determinar nivel y prefijo según nomenclatura de clave_orden
        $clave = $menu->clave_orden;
        if (substr($clave, 2, 2) === '00' && substr($clave, 4, 2) === '00') {
            // * Nivel 1: prefix 2 caracteres
            $prefixLen = 2;
        } elseif (substr($clave, 4, 2) === '00') {
            // * Nivel 2: prefix 4 caracteres
            $prefixLen = 4;
        } else {
            // * Nivel 3 o más: todo clave (6 caracteres)
            $prefixLen = strlen($clave);
        }
        $prefix = substr($clave, 0, $prefixLen);
        // Obtener IDs de todos los descendientes que comiencen con el prefijo
        $query = Permiso::where('clave_orden', 'like', $prefix . '%')
            ->where('id', '!=', $menu->id);
        $ids = $query->pluck('id')->toArray();
        // Actualizar estado
        $query->update(['activo' => $status]);
        return $ids;
    }
}
