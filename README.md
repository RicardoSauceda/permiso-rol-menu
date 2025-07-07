# Paquete Permiso Rol Menu - ICATECH

Este paquete proporciona funcionalidades para manejar permisos, roles y menús con convenciones en español.

## Compatibilidad

- ✅ **Laravel 9.x** - Compatible
- ✅ **Laravel 10.x** - Compatible  
- ✅ **Laravel 11.x** - Compatible
- ✅ **Laravel 12.x** - Compatible ⭐ **NUEVO**
- ✅ **PHP 8.1+** - Requerido para Laravel 12
- ✅ **PHP 8.2** - Compatible
- ✅ **PHP 8.3** - Compatible

## Características Principales

- ✅ **Sistema de permisos y roles** con convenciones en español
- ✅ **Menú dinámico jerárquico** basado en permisos
- ✅ **View Composer automático** para vistas de navegación
- ✅ **Vistas template** personalizables
- ✅ **Middleware de permisos** integrado
- ✅ **Gates automáticos** para Laravel

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

## Funcionalidades del Menú Dinámico

El paquete incluye un sistema completo de menú dinámico que se construye automáticamente basado en los permisos del usuario.

### Uso Básico del Menú

El paquete registra automáticamente un view composer que proporciona la variable `$menuDinamico` a las siguientes vistas:

- `theme.sivyc.menuDinamico`
- `vendor.permiso-rol-menu.navbar`
- `layouts.navbar`
- `permiso-rol-menu::navbar`

### Opción 1: Usar la vista incluida

```blade
@include('permiso-rol-menu::navbar')
```

### Opción 2: Personalizar la vista

Publica las vistas para personalizarlas:

```bash
php artisan vendor:publish --tag=icatech-permiso-rol-menu-views
```

Luego edita el archivo en `resources/views/vendor/permiso-rol-menu/navbar.blade.php`.

### Opción 3: Usar el view composer en tu vista existente

El view composer se aplica automáticamente, por lo que puedes usar `$menuDinamico` directamente:

```blade
<ul class="navbar-nav mr-auto">
    @foreach($menuDinamico as $main)
    @if($main['permiso'])
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" data-toggle="dropdown">
            {{ $main['permiso']->nombre }}
        </a>
        @if(count($main['submenus']))
        <div class="dropdown-menu">
            @foreach($main['submenus'] as $sub)
            @if($sub['permiso'])
            <a class="dropdown-item" href="{{ route($sub['permiso']->ruta_corta) }}">
                {{ $sub['permiso']->nombre }}
            </a>
            @endif
            @endforeach
        </div>
        @endif
    </li>
    @endif
    @endforeach
</ul>
```

## Configuración Avanzada

### Publicar configuración

```bash
php artisan vendor:publish --tag=icatech-permiso-rol-menu-config
```

En `config/permiso-rol-menu.php` puedes configurar:

```php
return [
    // Vista por defecto del menú
    'navbar_view' => 'permiso-rol-menu::navbar',
    
    // Aplicar view composer automáticamente
    'auto_compose' => true,
    
    // Vistas que recibirán automáticamente el view composer
    'auto_compose_views' => [
        'theme.sivyc.menuDinamico',
        'vendor.permiso-rol-menu.navbar',
        'layouts.navbar',
        'permiso-rol-menu::navbar'
    ],
    
    // Configuración de tablas
    'user_table' => 'tblz_usuarios',
    'user_model' => \App\Models\User::class,
];
```

## Estructura del Menú

El sistema de menú utiliza un código jerárquico en el campo `clave_orden`:

- **Menú principal**: `XX0000` (ej: `010000`, `020000`)
- **Submenú**: `XXXX00` (ej: `010100`, `010200`)  
- **Sub-submenú**: `XXXXXX` (ej: `010101`, `010102`)

### Ejemplo de estructura de permisos:

```
010000 - Administración (menú principal)
├── 010100 - Usuarios (submenú)
│   ├── 010101 - Crear Usuario (sub-submenú)
│   └── 010102 - Editar Usuario (sub-submenú)
└── 010200 - Configuración (submenú)
    ├── 010201 - Configuración General
    └── 010202 - Configuración Avanzada
```

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