<?php

use Illuminate\Support\Facades\Route;
use Icatech\PermisoRolMenu\Http\Controllers\PermisoController;
use Icatech\PermisoRolMenu\Http\Controllers\RolController;
use Icatech\PermisoRolMenu\Http\Controllers\MenuController;

Route::prefix('permiso-rol-menu')->name('permiso-rol-menu.')->group(function () {
    // Rutas para Permisos
    Route::post('permisos', [PermisoController::class, 'store'])->name('permisos.store');
    Route::put('permisos/{id}', [PermisoController::class, 'update'])->name('permisos.update');
    Route::delete('permisos/{id}', [PermisoController::class, 'destroy'])->name('permisos.destroy');

    // Rutas para Roles
    Route::post('roles', [RolController::class, 'store'])->name('roles.store');
    Route::put('roles/{id}', [RolController::class, 'update'])->name('roles.update');
    Route::delete('roles/{id}', [RolController::class, 'destroy'])->name('roles.destroy');

    // Rutas para Menús (tipo de permiso)
    Route::post('/menus/{id}/status-update', [MenuController::class, 'statusUpdate'])->name('tree.menus.status.update');
    Route::post('/menus/store', [MenuController::class, 'treeStore'])->name('tree.menus.store');

    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::put('menus/{id}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('menus/{id}', [MenuController::class, 'destroy'])->name('menus.destroy');
});
