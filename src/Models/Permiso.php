<?php

namespace Icatech\PermisoRolMenu\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'tblz_permisos';

    protected $fillable = [
        'clave_orden',
        'nombre',
        'ruta_corta',
        'descripcion',
        'icono',
        'activo'
    ];

    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tblz_permiso_rol', 'permiso_id', 'rol_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\User::class, 'tblz_usuario_permiso', 'permiso_id', 'usuario_id');
    }

    public function padre()
    {
        $claveOrden = $this->clave_orden;

        // Si es nivel raíz (termina en 000000), no tiene padre
        if (substr($claveOrden, 2) === '000000') {
            return $this->belongsTo(Permiso::class, 'clave_orden', 'clave_orden')->whereRaw('1 = 0');
        }

        // Si es nivel 2 (termina en 0000), el padre es nivel 1
        if (substr($claveOrden, 4) === '0000') {
            $clavePadre = substr($claveOrden, 0, 2) . '000000';
        }
        // Si es nivel 3 (termina en 00), el padre es nivel 2
        elseif (substr($claveOrden, 6) === '00') {
            $clavePadre = substr($claveOrden, 0, 4) . '0000';
        }
        // Si es nivel 4 (último dígito diferente de 0), el padre es nivel 3
        else {
            $clavePadre = substr($claveOrden, 0, 6) . '00';
        }

        return $this->belongsTo(Permiso::class, 'id', 'id')->where('clave_orden', $clavePadre);
    }

    public function hijos()
    {
        $claveOrden = $this->clave_orden;

        // Dependiendo del nivel, buscar hijos con el patrón correcto
        if (substr($claveOrden, 2) === '000000') {
            // Nivel 1: buscar hijos que empiecen con los primeros 2 dígitos y terminen en 0000
            $patron = substr($claveOrden, 0, 2) . '%0000';
            return $this->hasMany(Permiso::class, 'clave_orden', 'clave_orden')
                ->where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden);
        } elseif (substr($claveOrden, 4) === '0000') {
            // Nivel 2: buscar hijos que empiecen con los primeros 4 dígitos y terminen en 00
            $patron = substr($claveOrden, 0, 4) . '%00';
            return $this->hasMany(Permiso::class, 'clave_orden', 'clave_orden')
                ->where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden);
        } elseif (substr($claveOrden, 6) === '00') {
            // Nivel 3: buscar hijos que empiecen con los primeros 6 dígitos
            $patron = substr($claveOrden, 0, 6) . '%';
            return $this->hasMany(Permiso::class, 'clave_orden', 'clave_orden')
                ->where('clave_orden', 'LIKE', $patron)
                ->where('clave_orden', '!=', $claveOrden)
                ->where('clave_orden', 'NOT LIKE', substr($claveOrden, 0, 6) . '00');
        } else {
            // Nivel 4: no tiene hijos
            return $this->hasMany(Permiso::class, 'clave_orden', 'clave_orden')->whereRaw('1 = 0');
        }
    }

    public function getClavePadreAttribute()
    {
        $claveOrden = $this->clave_orden;

        // Si es nivel raíz (termina en 000000), no tiene padre
        if (substr($claveOrden, 2) === '000000') {
            return null;
        }

        // Si es nivel 2 (termina en 0000), el padre es nivel 1
        if (substr($claveOrden, 4) === '0000') {
            return substr($claveOrden, 0, 2) . '000000';
        }
        // Si es nivel 3 (termina en 00), el padre es nivel 2
        elseif (substr($claveOrden, 6) === '00') {
            return substr($claveOrden, 0, 4) . '0000';
        }
        // Si es nivel 4 (último dígito diferente de 0), el padre es nivel 3
        else {
            return substr($claveOrden, 0, 6) . '00';
        }
    }
}
