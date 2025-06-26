<?php

namespace Icatech\PermisoRolMenu\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

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
}
