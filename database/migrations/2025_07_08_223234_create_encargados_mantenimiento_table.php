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
        Schema::create('encargados_mantenimiento', function (Blueprint $table) {
            $table->integer('id_encargado_man')->primary();
        
            $table->string('nombre', 100)->notNull();
            $table->string('apellidos', 100)->notNull();
        
            $table->string('contacto', 100)->nullable();
            $table->string('cargo', 100)->nullable();
        
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargados_mantenimiento');
    }
};
