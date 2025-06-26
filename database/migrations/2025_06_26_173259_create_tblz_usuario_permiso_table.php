<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblzUsuarioPermisoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblz_usuario_permiso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tblz_usuarios')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('tblz_permisos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblz_usuario_permiso');
    }
}
