# Paquete Permiso Rol Menu - ICATECH

Este paquete proporciona funcionalidades para manejar permisos, roles y menús con convenciones en español.

## Instalación

```bash
composer require icatech/permiso-rol-menu
```

**¡No necesitas registrar manualmente el Service Provider!** Laravel lo detectará automáticamente gracias al auto-discovery.

## Configuración

### 1. Ejecutar migraciones

Las migraciones se cargan automáticamente:

```bash
php artisan migrate
```

O si prefieres publicarlas primero (opcional):

```bash
php artisan vendor:publish --tag=icatech-permiso-rol-menu-migrations
php artisan migrate
```

### 2. Configurar el modelo User

En tu modelo `User`, agrega el trait `ConfiguresSpanishUserModel`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Icatech\PermisoRolMenu\Traits\ConfiguresSpanishUserModel;

class User extends Authenticatable
{
    use ConfiguresSpanishUserModel;
    
    // Tu configuración adicional...
}
```

## Características

- **Auto-discovery**: Se configura automáticamente sin necesidad de registrar Service Providers
- **Tabla de usuarios en español**: Automáticamente configura la tabla como `tblz_usuarios`
- **Campo nombre**: Reemplaza `name` por `nombre` manteniendo compatibilidad
- **Compatibilidad**: Los accessors/mutators mantienen compatibilidad con código existente que use `name`
- **Migraciones automáticas**: Se cargan automáticamente al ejecutar `php artisan migrate`

## Uso

### Crear usuarios
```php
$user = User::create([
    'nombre' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => bcrypt('password')
]);
```

### Buscar usuarios
```php
$usuarios = User::byNombre('Juan')->get();
```

### Acceder al nombre (ambas formas funcionan)
```php
echo $user->nombre;  // Recomendado
echo $user->name;    // Mantiene compatibilidad
```

## Sistema de Permisos

El paquete incluye un sistema completo de permisos que se integra con el sistema de autorización de Laravel.

### Verificar permisos con @can

El paquete registra automáticamente Gates que permiten usar la directiva `@can` de Blade con la columna `ruta_corta` de los permisos:

```blade
@can('usuarios.crear')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        Crear Usuario
    </a>
@endcan

@can('reportes.ventas')
    <div class="report-section">
        <!-- Contenido del reporte -->
    </div>
@endcan
```

### Verificar permisos en controladores

```php
// En un controlador
public function create()
{
    $this->authorize('usuarios.crear');
    
    // Lógica del método...
}

// O usando el helper can()
public function index()
{
    if (auth()->user()->can('usuarios.listar')) {
        // Mostrar lista de usuarios
    }
}
```

### Proteger rutas con middleware

El paquete incluye un middleware `permiso` que puedes usar para proteger rutas:

```php
// En routes/web.php
Route::middleware(['auth', 'permiso:usuarios.crear'])->group(function () {
    Route::get('/usuarios/create', [UserController::class, 'create']);
    Route::post('/usuarios', [UserController::class, 'store']);
});

// O en un controlador
public function __construct()
{
    $this->middleware('permiso:usuarios.editar')->only(['edit', 'update']);
    $this->middleware('permiso:usuarios.eliminar')->only('destroy');
}
```

### Métodos disponibles en el modelo User

```php
// Verificar si tiene un permiso específico
$user->hasPermission('usuarios.crear');

// Verificar si tiene un rol específico
$user->hasRole('administrador');

// Obtener todos los permisos del usuario
$permisos = $user->permissions;

// Obtener todos los roles del usuario
$roles = $user->roles;
```

### Tipos de roles especiales

El sistema soporta roles especiales:

- **all-access**: Usuarios con este rol tienen acceso a todo
- **no-access**: Usuarios con este rol no tienen acceso a nada (útil para suspender cuentas)

```php
// Crear un rol con acceso total
$adminRole = Rol::create([
    'nombre' => 'Super Administrador',
    'especial' => 'all-access'
]);

// Crear un rol sin acceso
$suspendedRole = Rol::create([
    'nombre' => 'Suspendido',
    'especial' => 'no-access'
]);
```

## Ejemplos Prácticos

### Ejemplo 1: Seeder para Permisos y Roles

```php
// database/seeders/PermisosSeeder.php
use Icatech\PermisoRolMenu\Models\Permiso;
use Icatech\PermisoRolMenu\Models\Rol;

public function run()
{
    // Crear permisos
    $permisos = [
        ['nombre' => 'Listar Usuarios', 'ruta_corta' => 'usuarios.listar'],
        ['nombre' => 'Crear Usuarios', 'ruta_corta' => 'usuarios.crear'],
        ['nombre' => 'Editar Usuarios', 'ruta_corta' => 'usuarios.editar'],
        ['nombre' => 'Eliminar Usuarios', 'ruta_corta' => 'usuarios.eliminar'],
    ];

    foreach ($permisos as $permiso) {
        Permiso::firstOrCreate(['ruta_corta' => $permiso['ruta_corta']], $permiso);
    }

    // Crear rol y asignar permisos
    $editorRole = Rol::firstOrCreate(['nombre' => 'Editor']);
    $permisosEditor = ['usuarios.listar', 'usuarios.crear', 'usuarios.editar'];
    $editorRole->permisos()->sync(
        Permiso::whereIn('ruta_corta', $permisosEditor)->pluck('id')
    );
}
```

### Ejemplo 2: Controlador con Permisos

```php
class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permiso:usuarios.crear')->only(['create', 'store']);
        $this->middleware('permiso:usuarios.editar')->only(['edit', 'update']);
    }

    public function index()
    {
        $this->authorize('usuarios.listar');
        // Lógica del controlador...
    }
}
```

### Ejemplo 3: Vista con @can

```blade
@can('usuarios.crear')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        Crear Usuario
    </a>
@endcan

@cannot('usuarios.eliminar')
    <p class="text-muted">No tienes permisos para eliminar usuarios</p>
@endcannot
```

## Estructura de la Base de Datos

El paquete crea las siguientes tablas:

- `tblz_usuarios` - Tabla de usuarios (en lugar de `users`)
- `tblz_permisos` - Permisos del sistema
- `tblz_roles` - Roles de usuario  
- `tblz_usuario_rol` - Relación usuarios-roles
- `tblz_permiso_rol` - Relación permisos-roles
- `tblz_usuario_permiso` - Permisos directos a usuarios

## Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)  
5. Crea un Pull Request