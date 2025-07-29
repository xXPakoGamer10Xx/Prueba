<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Revisa si existe la columna con el nombre antiguo ('id_inventario')
            // y si NO existe la columna con el nombre nuevo ('id_inventario_fk').
            if (Schema::hasColumn('mantenimientos', 'id_inventario') && !Schema::hasColumn('mantenimientos', 'id_inventario_fk')) {
                // Si se cumplen las condiciones, renombra la columna.
                $table->renameColumn('id_inventario', 'id_inventario_fk');
            }

            // Hacemos lo mismo para la clave foránea del encargado, por si acaso.
            if (Schema::hasColumn('mantenimientos', 'id_encargado_man') && !Schema::hasColumn('mantenimientos', 'id_encargado_man_fk')) {
                $table->renameColumn('id_encargado_man', 'id_encargado_man_fk');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Este método revierte los cambios si es necesario.
            if (Schema::hasColumn('mantenimientos', 'id_inventario_fk')) {
                $table->renameColumn('id_inventario_fk', 'id_inventario');
            }
            if (Schema::hasColumn('mantenimientos', 'id_encargado_man_fk')) {
                $table->renameColumn('id_encargado_man_fk', 'id_encargado_man');
            }
        });
    }
};
