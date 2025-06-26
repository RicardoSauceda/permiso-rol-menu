<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblzPermisoRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblz_permiso_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permiso_id')->constrained('tblz_permisos')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('tblz_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblz_permiso_rol');
    }
}
