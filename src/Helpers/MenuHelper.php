<?php

namespace Icatech\PermisoRolMenu\Helpers;

class MenuHelper
{
    /**
     * Construye el menú jerárquico basado en los permisos
     * Utiliza la lógica original del sistema ICATECH
     *
     * @param \Illuminate\Support\Collection $permissions
     * @return array
     */
    public static function buildMenu($permissions)
    {
        $menu = [];

        foreach ($permissions as $perm) {
            $clave = $perm->clave_orden;
            if (!$clave) continue;

            // Filtrar elementos de 8 dígitos completos:
            // ✅ SÍ mostrar en navbar: 01010100 (8 dígitos pero termina en 00)
            // ❌ NO mostrar en navbar: 01010101, 01010102, 01010199 (8 dígitos que NO terminan en 00)
            if (preg_match('/^\d{8}$/', $clave) && !preg_match('/^\d{6}00$/', $clave)) {
                continue;
            }

            // Solo procesar elementos del menú navbar (hasta 6 dígitos + 00)
            // Menú principal: XX000000
            if (preg_match('/^\d{2}000000$/', $clave)) {
                $menu[$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
            // Submenú: XXXX0000
            elseif (preg_match('/^\d{4}0000$/', $clave)) {
                $main = substr($clave, 0, 2) . '000000';
                if (!isset($menu[$main])) $menu[$main] = ['permiso' => null, 'submenus' => []];
                $menu[$main]['submenus'][$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
            // Sub-submenú: XXXXXX00
            elseif (preg_match('/^\d{6}00$/', $clave)) {
                $main = substr($clave, 0, 2) . '000000';
                $sub = substr($clave, 0, 4) . '0000';
                if (!isset($menu[$main])) $menu[$main] = ['permiso' => null, 'submenus' => []];
                if (!isset($menu[$main]['submenus'][$sub])) $menu[$main]['submenus'][$sub] = ['permiso' => null, 'submenus' => []];
                $menu[$main]['submenus'][$sub]['submenus'][$clave] = [
                    'permiso' => $perm,
                    'submenus' => []
                ];
            }
        }

        // Ordenar por clave_orden
        ksort($menu);
        foreach ($menu as &$m) {
            ksort($m['submenus']);
            foreach ($m['submenus'] as &$sm) {
                ksort($sm['submenus']);
            }
        }

        return $menu;
    }
}
