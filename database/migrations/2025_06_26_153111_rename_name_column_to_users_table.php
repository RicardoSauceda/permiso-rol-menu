<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameNameColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->renameColumn('name', 'nombre');
            }
            // * registro_id es para relacionar con la funcionarios
            $table->foreignId('registro_id')->after('id');
            $table->string('registro_type')->nullable()->after('registro_id');
            $table->boolean('activo')->default(true);
            $table->date('fecha_caducidad')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nombre')) {
                $table->renameColumn('nombre', 'name');
            }
        });
    }
}
