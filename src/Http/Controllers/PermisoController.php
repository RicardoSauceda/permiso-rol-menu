<?php

namespace Icatech\PermisoRolMenu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Icatech\PermisoRolMenu\Models\Permiso;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::orderBy('clave_orden')->paginate(10);
        return view('permiso-rol-menu::permisos.index', compact('permisos'));
    }

    public function create($claveOrdenPadre)
    {
        $menuPadre = Permiso::where('clave_orden', $claveOrdenPadre)->first();

        if (!$menuPadre) {
            return redirect()->back()->with('error', 'Menú padre no encontrado.');
        }

        return view('permiso-rol-menu::permisos.create', compact('menuPadre', 'claveOrdenPadre'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'clave_orden_padre' => 'required|string|max:8'
            ]);

            // Verificar que existe el menú padre
            $menuPadre = Permiso::where('clave_orden', $data['clave_orden_padre'])->first();
            if (!$menuPadre) {
                return redirect()->back()->with('error', 'Menú padre no encontrado.')->withInput();
            }

            // Generar nueva clave_orden para el permiso específico
            $claveBase = substr($data['clave_orden_padre'], 0, 6); // Solo los primeros 6 dígitos

            // Buscar el siguiente número disponible en los últimos 2 dígitos
            $existingPermisos = Permiso::where('clave_orden', 'like', $claveBase . '%')
                ->where('clave_orden', '!=', $data['clave_orden_padre'])
                ->where('clave_orden', 'not like', '%00')
                ->pluck('clave_orden')
                ->toArray();

            $nextNumber = 1;
            for ($i = 1; $i <= 99; $i++) {
                $testClave = $claveBase . str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!in_array($testClave, $existingPermisos)) {
                    $nextNumber = $i;
                    break;
                }
            }

            $newClave = $claveBase . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            Permiso::create([
                'nombre' => $data['nombre'],
                'ruta_corta' => $data['ruta_corta'],
                'descripcion' => $data['descripcion'],
                'clave_orden' => $newClave,
                'activo' => true
            ]);

            return redirect()->route('permiso-rol-menu.permisos.arbol', $data['clave_orden_padre'])
                ->with('success', 'Permiso creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el permiso: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $permiso = Permiso::findOrFail($id);
        return view('permiso-rol-menu::permisos.edit', compact('permiso'));
    }

    public function update(Request $request, $id)
    {
        try {
            $permiso = Permiso::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruta_corta' => 'nullable|sometimes|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'clave_orden' => 'required|string|max:8|unique:tblz_permisos,clave_orden,' . $id
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
            return redirect()->route('permiso-rol-menu.permisos.index')->with('success', 'Permiso eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el permiso: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar permisos específicos de un menú (últimos 2 dígitos)
     */
    public function showPermisosMenu($claveOrdenPadre = null)
    {
        $menuPadre = null;

        if ($claveOrdenPadre) {
            // Buscar el menú padre
            $menuPadre = Permiso::where('clave_orden', $claveOrdenPadre)->first();

            if (!$menuPadre) {
                return redirect()->back()->with('error', 'Menú padre no encontrado.');
            }

            $seisDigitosCoincidir = substr($claveOrdenPadre, 0, 6);

            $permisos = Permiso::where('clave_orden', 'like', $seisDigitosCoincidir . '%')
                ->where('clave_orden', '!=', $claveOrdenPadre)
                ->orderBy('clave_orden')
                ->get();
        }

        return view('permiso-rol-menu::permisos_menu', compact('menuPadre', 'permisos', 'claveOrdenPadre'));
    }

    /**
     * Construir árbol de permisos específicos para un menú padre
     */
    private function buildPermisosTree($claveOrdenPadre)
    {
        // Buscar todos los permisos que empiecen con la clave padre
        // pero que los últimos 2 dígitos NO sean '00'
        $permisos = Permiso::where('clave_orden', 'like', $claveOrdenPadre . '%')
            ->where('clave_orden', '!=', $claveOrdenPadre)
            ->where('clave_orden', 'not like', '%00')
            ->orderBy('clave_orden')
            ->get();

        return $permisos->toArray();
    }

    /**
     * Almacenar nuevo permiso específico
     */
    public function storePermiso(Request $request)
    {
        $data = $request->validate([
            'permisoName'        => 'required|string|max:255',
            'rutaCorta'          => 'required|string|max:255',
            'permisoDescripcion' => 'nullable|string',
            'clave_orden_padre'  => 'required|string|max:6',
        ]);

        $menuPadre = Permiso::where('clave_orden', $data['clave_orden_padre'])->first();

        if (!$menuPadre) {
            return redirect()->back()->with('error', 'Menú padre no encontrado.');
        }

        // Generar nueva clave_orden para el permiso específico
        $claveBase = $data['clave_orden_padre']; // Los primeros 6 dígitos

        // Buscar el siguiente número disponible en los últimos 2 dígitos
        $existingPermisos = Permiso::where('clave_orden', 'like', $claveBase . '%')
            ->where('clave_orden', '!=', $claveBase)
            ->where('clave_orden', 'not like', '%00')
            ->pluck('clave_orden')
            ->toArray();

        $nextNumber = 1;
        for ($i = 1; $i <= 99; $i++) {
            $testClave = $claveBase . str_pad($i, 2, '0', STR_PAD_LEFT);
            if (!in_array($testClave, $existingPermisos)) {
                $nextNumber = $i;
                break;
            }
        }

        $permiso = new Permiso;
        $permiso->nombre = trim($data['permisoName']);
        $permiso->ruta_corta = trim($data['rutaCorta']);
        $permiso->descripcion = trim($data['permisoDescripcion'] ?? '');
        $permiso->clave_orden = $claveBase . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $permiso->activo = true;
        $permiso->save();

        return redirect()->route('permiso-rol-menu.permisos.arbol', $data['clave_orden_padre'])
            ->with('success', 'Permiso agregado correctamente.');
    }

    /**
     * Actualizar estado de permiso específico
     */
    public function updatePermisoStatus(Request $request, $id)
    {
        $permiso = Permiso::find($id);
        if ($permiso) {
            $newStatus = !$permiso->activo;
            $permiso->activo = $newStatus;
            $permiso->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado del permiso actualizado correctamente',
                'newStatus' => $newStatus ? 'Activo' : 'Inactivo'
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Permiso no encontrado'], 404);
    }
}
