<?php

namespace Icatech\PermisoRolMenu\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Icatech\PermisoRolMenu\Middleware\PermisoMiddleware;

class PermisoRolMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Publicar migraciones
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'icatech-permiso-rol-menu-migrations');
        }

        // Cargar migraciones automáticamente si el usuario lo prefiere
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Registrar Gates para el sistema de permisos
        $this->registerGates();

        // Registrar middleware
        $this->registerMiddleware();
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
