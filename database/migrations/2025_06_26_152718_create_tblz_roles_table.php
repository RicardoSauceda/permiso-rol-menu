<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblzRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblz_roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruta_corta')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('especial')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblz_roles');
    }
}
