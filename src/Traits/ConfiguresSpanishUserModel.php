<?php

namespace Icatech\PermisoRolMenu\Traits;

use Icatech\PermisoRolMenu\Models\Rol;
use Icatech\PermisoRolMenu\Models\Permiso;

trait ConfiguresSpanishUserModel
{
    /**
     * Boot the trait.
     */
    public static function bootConfiguresSpanishUserModel()
    {
        // Configurar automáticamente la tabla
        static::creating(function ($user) {
            // Cualquier lógica adicional antes de crear el usuario
        });
    }

    /**
     * Initialize the trait.
     */
    public function initializeConfiguresSpanishUserModel()
    {
        // Configurar la tabla
        $this->table = 'tblz_usuarios';

        // Agregar campos fillable específicos
        $this->fillable = array_merge($this->fillable, ['nombre']);

        // Remover 'name' de fillable si existe y agregar 'nombre'
        $this->fillable = array_unique(
            array_map(function ($field) {
                return $field === 'name' ? 'nombre' : $field;
            }, $this->fillable)
        );
    }

    /**
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tblz_usuario_rol', 'usuario_id', 'rol_id');
    }

    /**
     * Relación muchos a muchos con permisos
     */
    public function permissions()
    {
        return $this->belongsToMany(Permiso::class, 'tblz_usuario_permiso', 'usuario_id', 'permiso_id');
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        return $this->roles()->where('nombre', $role)->exists();
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function hasPermission($permission)
    {
        // Si algún rol tiene all-access, retorna true
        if ($this->roles()->where('especial', 'all-access')->exists()) {
            return true;
        }

        // Si algún rol tiene no-access, retorna false
        if ($this->roles()->where('especial', 'no-access')->exists()) {
            return false;
        }

        // Permisos directos o por rol
        return $this->permissions()->where('ruta_corta', $permission)->exists() ||
            $this->roles()->whereHas('permisos', function ($q) use ($permission) {
                $q->where('ruta_corta', $permission);
            })->exists();
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeByNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    /**
     * Accessor para nombre (mantiene compatibilidad con 'name')
     */
    public function getNameAttribute()
    {
        return $this->attributes['nombre'] ?? null;
    }

    /**
     * Mutator para nombre (mantiene compatibilidad con 'name')
     */
    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }
}
