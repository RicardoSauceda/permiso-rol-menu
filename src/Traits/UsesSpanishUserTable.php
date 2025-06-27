<?php

namespace Icatech\PermisoRolMenu\Traits;

trait UsesSpanishUserTable
{
    /**
     * Boot the trait.
     */
    public static function bootUsesSpanishUserTable()
    {
        // Aquí puedes agregar cualquier lógica de inicialización si es necesaria
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? 'tblz_usuarios';
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        $originalFillable = parent::getFillable();
        
        // Reemplazar 'name' con 'nombre' si existe
        if (in_array('name', $originalFillable)) {
            $key = array_search('name', $originalFillable);
            $originalFillable[$key] = 'nombre';
        }
        
        // Agregar 'nombre' si no está presente
        if (!in_array('nombre', $originalFillable)) {
            $originalFillable[] = 'nombre';
        }
        
        return $originalFillable;
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
}
