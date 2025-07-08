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
        Schema::create('equipos', function (Blueprint $table) {
            $table->integer('id_equipo')->primary();
            
            $table->string('nombre', 100)->notNull();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
           
            $table->integer('cantidad')->notNull();
            $table->integer('frecuencia_mantenimiento')->notNull();
           
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
