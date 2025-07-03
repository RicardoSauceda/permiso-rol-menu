<?php

namespace Icatech\PermisoRolMenu\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Icatech\PermisoRolMenu\Middleware\PermisoMiddleware;
use Icatech\PermisoRolMenu\View\MenuComposer;

class PermisoRolMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Registrar archivo de configuración
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/permiso-rol-menu.php',
            'permiso-rol-menu'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Cargar vistas
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'permiso-rol-menu');

        // Publicar archivos en modo consola
        if ($this->app->runningInConsole()) {
            // Publicar migraciones
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'permiso-rol-menu-migrations');

            // Publicar vistas
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/permiso-rol-menu'),
            ], 'permiso-rol-menu-views');

            // Publicar configuración
            $this->publishes([
                __DIR__ . '/../../config/permiso-rol-menu.php' => config_path('permiso-rol-menu.php'),
            ], 'permiso-rol-menu-config');

            // Publicar assets CSS y JS
            $this->publishes([
                __DIR__ . '/../../resources/assets/css' => public_path('vendor/permiso-rol-menu/css'),
            ], 'permiso-rol-menu-css');

            $this->publishes([
                __DIR__ . '/../../resources/assets/js' => public_path('vendor/permiso-rol-menu/js'),
            ], 'permiso-rol-menu-js');

            // Publicar todos los assets juntos
            $this->publishes([
                __DIR__ . '/../../resources/assets' => public_path('vendor/permiso-rol-menu'),
            ], 'permiso-rol-menu-assets');
        }

        // Cargar migraciones automáticamente si el usuario lo prefiere
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Cargar rutas
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        // Registrar view composer para menu dinámico
        $this->registerViewComposers();

        // Registrar Gates para el sistema de permisos
        $this->registerGates();

        // Registrar middleware
        $this->registerMiddleware();
    }

    /**
     * Registrar view composers
     */
    protected function registerViewComposers()
    {
        // Aplicar el composer a la vista del paquete
        View::composer('permiso-rol-menu::navbar', MenuComposer::class);

        // También aplicar a las vistas personalizadas del usuario si existen
        View::composer([
            'theme.sivyc.menuDinamico',
            'vendor.permiso-rol-menu.navbar',
            'layouts.navbar'
        ], MenuComposer::class);
    }

    /**
     * Registrar Gates para el sistema de permisos
     */
    protected function registerGates()
    {
        Gate::define('*', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        // Gate alternativo más específico para rutas
        Gate::before(function ($user, $ability) {
            // Este gate se ejecuta antes que cualquier otro
            // Permite usar @can('nombre-ruta') directamente
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
        });
    }

    /**
     * Registrar middleware personalizado
     */
    protected function registerMiddleware()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('permiso', PermisoMiddleware::class);
    }
}
