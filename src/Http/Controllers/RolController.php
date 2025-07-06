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
        $menuTree = $menuController->buildMenuTree($menus, 1, null, true);

        return view('permiso-rol-menu::rol.permisos', compact('menuTree', 'rol'));
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
    
    public function togglePermiso(Request $request, $rolId, $permisoId)
    {
        try {
            $rol = Rol::findOrFail($rolId);
            $permiso = Permiso::findOrFail($permisoId);
            
            $action = $request->input('action'); // 'attach' o 'detach'
            
            if ($action === 'attach') {
                // Verificar si el permiso ya está asignado
                if ($rol->permisos->contains($permisoId)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'El permiso ya estaba asignado',
                        'attached_ids' => []
                    ]);
                }
                
                // Agregar el permiso al rol
                $rol->permisos()->syncWithoutDetaching([$permisoId]);
                
                // Agregar todos los permisos padre (hacia arriba)
                $attachedParents = $this->attachParentPermisos($rol, $permiso);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Permiso asignado correctamente',
                    'attached_ids' => array_merge([$permisoId], $attachedParents)
                ]);
            } else {
                // Verificar si el permiso está asignado
                if (!$rol->permisos->contains($permisoId)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'El permiso ya estaba desasignado',
                        'detached_ids' => []
                    ]);
                }
                
                // Quitar el permiso del rol
                $rol->permisos()->detach($permisoId);
                
                // Quitar todos los permisos hijo (hacia abajo)
                $detachedChildren = $this->detachChildPermisos($rol, $permiso);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Permiso desasignado correctamente',
                    'detached_ids' => array_merge([$permisoId], $detachedChildren)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function attachParentPermisos($rol, $permiso)
    {
        $attachedIds = [];
        $clavePadre = $permiso->clave_padre;
        
        while ($clavePadre) {
            $padre = Permiso::where('clave_orden', $clavePadre)->first();
            if ($padre && !$rol->permisos->contains($padre->id)) {
                $rol->permisos()->syncWithoutDetaching([$padre->id]);
                $attachedIds[] = $padre->id;
                // Recargar la relación para incluir el nuevo permiso
                $rol->load('permisos');
                $clavePadre = $padre->clave_padre;
            } else {
                break;
            }
        }
        
        return $attachedIds;
    }
    
    private function detachChildPermisos($rol, $permiso)
    {
        $detachedIds = [];
        $this->detachChildPermisosRecursive($rol, $permiso, $detachedIds);
        return $detachedIds;
    }
    
    private function detachChildPermisosRecursive($rol, $permiso, &$detachedIds)
    {
        $claveOrden = $permiso->clave_orden;
        
        // Buscar todos los hijos basándose en la clave_orden
        $hijos = $this->getChildPermisos($claveOrden);
        
        foreach ($hijos as $hijo) {
            if ($rol->permisos->contains($hijo->id)) {
                $rol->permisos()->detach($hijo->id);
                $detachedIds[] = $hijo->id;
                
                // Recursivamente quitar los hijos de este hijo
                $this->detachChildPermisosRecursive($rol, $hijo, $detachedIds);
            }
        }
    }
    
    private function getChildPermisos($claveOrden)
    {
        // Dependiendo del nivel, buscar hijos con el patrón correcto
        if (substr($claveOrden, 2) === '000000') {
            // Nivel 1: buscar hijos que empiecen con los primeros 2 dígitos y terminen en 0000
            $patron = substr($claveOrden, 0, 2) . '%0000';
            return Permiso::where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden)
                ->get();
        } elseif (substr($claveOrden, 4) === '0000') {
            // Nivel 2: buscar hijos que empiecen con los primeros 4 dígitos y terminen en 00
            $patron = substr($claveOrden, 0, 4) . '%00';
            return Permiso::where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden)
                ->get();
        } elseif (substr($claveOrden, 6) === '00') {
            // Nivel 3: buscar hijos que empiecen con los primeros 6 dígitos
            $patron = substr($claveOrden, 0, 6) . '%';
            return Permiso::where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden)
                ->where('clave_orden', 'NOT LIKE', substr($claveOrden, 0, 6) . '00')
                ->get();
        } else {
            // Nivel 4: no tiene hijos
            return collect();
        }
    }
    
    private function getAttachedIds($rol, $permiso)
    {
        $attachedIds = [];
        
        // Obtener IDs de permisos padre que fueron adjuntados
        $clavePadre = $permiso->clave_padre;
        while ($clavePadre) {
            $padre = Permiso::where('clave_orden', $clavePadre)->first();
            if ($padre && $rol->permisos->contains($padre->id)) {
                $attachedIds[] = $padre->id;
                $clavePadre = $padre->clave_padre;
            } else {
                break;
            }
        }
        
        return $attachedIds;
    }
}
