<?php

namespace Icatech\PermisoRolMenu\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'tblz_roles';

    protected $fillable = [
        'nombre',
        'ruta_corta',
        'descripcion',
        'especial',
    ];

    public $timestamps = false;

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'tblz_permiso_rol', 'rol_id', 'permiso_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\User::class, 'tblz_usuario_rol', 'rol_id', 'usuario_id');
    }
    
    public function menus()
    {
        return $this->permisos()->whereNotNull('clave_orden');
    }
}
