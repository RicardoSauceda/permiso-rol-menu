<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblzPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblz_permisos', function (Blueprint $table) {
            $table->id();
            $table->string('clave_orden')->nullable()->unique();
            $table->string('nombre');
            $table->string('ruta_corta');
            $table->string('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblz_permisos');
    }
}
