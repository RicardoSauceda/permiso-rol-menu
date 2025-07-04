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
            'rutaCorta'          => 'required|string|max:255',
            'permisoDescripcion' => 'nullable|string',
            'clave_orden_padre'  => 'nullable|string|max:6',
        ]);

        $menu = new Permiso;
        $menu->nombre = trim($data['permisoName']);
        $menu->ruta_corta = trim($data['rutaCorta']);
        $menu->descripcion = trim($data['permisoDescripcion'] ?? '');

        $clavePadre = $data['clave_orden_padre'] ?? null;
        $parent = null;
        if ($clavePadre) {
            $parent = Permiso::where('clave_orden', $clavePadre)->first();
        }
        if ($parent && $parent->clave_orden) {
            $clavePadre = $parent->clave_orden;

            if (substr($clavePadre, 2, 6) === '000000') { // XX000000
                $prefixLen = 2;
                $maxItems = 99;
            } elseif (substr($clavePadre, 4, 4) === '0000') { // XXXX0000  
                $prefixLen = 4;
                $maxItems = 99;
            } elseif (substr($clavePadre, 6, 2) === '00') { // XXXXXX00
                $prefixLen = 6;
                $maxItems = 99;
            } else { // XXXXXXXX - Los nuevos 2 dígitos
                $prefixLen = 8;
                $maxItems = 99;
            }

            $prefix = substr($clavePadre, 0, $prefixLen);

            // Buscar el siguiente número disponible
            $existingItems = Permiso::where('clave_orden', 'like', $prefix . '%')
                ->where('clave_orden', '!=', $clavePadre)
                ->pluck('clave_orden')
                ->toArray();

            $nextNumber = 1;
            $paddingLen = ($prefixLen === 8) ? 0 : 8 - $prefixLen - 2; // Calcular padding restante

            for ($i = 1; $i <= $maxItems; $i++) {
                $testClave = $prefix . str_pad($i, 2, '0', STR_PAD_LEFT) . str_repeat('0', $paddingLen);
                if (!in_array($testClave, $existingItems)) {
                    $nextNumber = $i;
                    break;
                }
            }

            $menu->clave_orden = $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT) . str_repeat('0', $paddingLen);
        } else {
            $menu->clave_orden = '01000000'; // Primer menú principal
        }
        $menu->activo = true;
        $menu->save();
        return redirect()->route('menus.index')
            ->with('success', 'Menú agregado correctamente.');
    }


    public function buildMenuTree($menus, $nivel = 1, $claveProveniente = '', $mostrarPermisos = false)
    {
        $tree = [];

        $filtered = $menus->filter(function ($menu) use ($nivel, $claveProveniente, $mostrarPermisos) {
            $clave = $menu->clave_orden;

            if (!$mostrarPermisos) {
                // Filtrar elementos de 8 dígitos completos que NO terminan en 00
                // ✅ SÍ mostrar: 01010100 (8 dígitos pero termina en 00)
                // ❌ NO mostrar: 01010101, 01010102, 01010199 (8 dígitos que NO terminan en 00)
                if (preg_match('/^\d{8}$/', $clave) && !preg_match('/^\d{6}00$/', $clave)) {
                    return false;
                }
            }


            $segmentos = str_split($clave, 2);

            if ($nivel === 1) {
                return $segmentos[0] !== '00' && $segmentos[1] === '00' && $segmentos[2] === '00' && $segmentos[3] === '00';
            }

            $parentSeg = str_split($claveProveniente, 2);

            if ($nivel === 2) {
                // Solo acepta claves tipo XXYY0000 (ej: 09020000)
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] !== '00' && $segmentos[2] === '00' && $segmentos[3] === '00';
            }

            if ($nivel === 3) {
                // Nivel 3 tradicional: XXYYZZ00
                $nivel3Tradicional = $segmentos[0] === $parentSeg[0] && $segmentos[1] === $parentSeg[1] && $segmentos[2] !== '00' && $segmentos[3] === '00';

                // Nivel 3 flexible: Si el padre es nivel 2 (XXYY0000), acepta también XXYY00ZZ cuando mostrarPermisos
                $nivel3Flexible = $mostrarPermisos &&
                    $parentSeg[2] === '00' && $parentSeg[3] === '00' && // Padre es nivel 2
                    $segmentos[0] === $parentSeg[0] && $segmentos[1] === $parentSeg[1] &&
                    $segmentos[2] === '00' && $segmentos[3] !== '00'; // Hijo es XXYY00ZZ

                return $nivel3Tradicional || $nivel3Flexible;
            }

            if ($mostrarPermisos && $nivel === 4) {
                return $segmentos[0] === $parentSeg[0] && $segmentos[1] === $parentSeg[1] && $segmentos[2] === $parentSeg[2] && $segmentos[3] !== '00';
            }

            return false;
        });

        foreach ($filtered as $menu) {
            $item = $menu->toArray();

            // Detectar si es acción (nivel 4): clave de 8 dígitos y NO termina en 00
            $esAccion = (preg_match('/^\d{8}$/', $menu->clave_orden) && !preg_match('/^\d{6}00$/', $menu->clave_orden));

            // Solo agregar submenús si NO es acción
            if (($nivel < 4 || $mostrarPermisos) && !$esAccion) {
                $submenu = $this->buildMenuTree($menus, $nivel + 1, $menu->clave_orden, $mostrarPermisos);
                if (!empty($submenu)) {
                    $item['submenu'] = $submenu;
                }
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
        $clave = $menu->clave_orden;

        // Determinar el nivel y prefijo
        if (substr($clave, 2, 6) === '000000') { // XX000000 - Nivel 1
            $prefixLen = 2;
        } elseif (substr($clave, 4, 4) === '0000') { // XXXX0000 - Nivel 2
            $prefixLen = 4;
        } elseif (substr($clave, 6, 2) === '00') { // XXXXXX00 - Nivel 3
            $prefixLen = 6;
        } else { // XXXXXXXX - Nivel 4 (acciones)
            $prefixLen = 8; // Solo afecta a sí mismo, no tiene hijos
        }

        $prefix = substr($clave, 0, $prefixLen);

        $query = Permiso::where('clave_orden', 'like', $prefix . '%')
            ->where('id', '!=', $menu->id);

        // IMPORTANTE: Si es una acción (nivel 4), no buscar hijos
        if ($prefixLen === 8) {
            return []; // Las acciones no tienen hijos
        }

        $ids = $query->pluck('id')->toArray();
        $query->update(['activo' => $status]);

        return $ids;
    }
}
