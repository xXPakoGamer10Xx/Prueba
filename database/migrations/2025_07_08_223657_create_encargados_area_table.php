<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('encargados_area', function (Blueprint $table) {
            $table->integer('id_encargado_area')->primary();
        
            $table->string('nombre', 100)->notNull();
            $table->string('apellidos', 100)->notNull();
            $table->string('cargo', 100)->notNull();

            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargados_area');
    }
};
