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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->string('id_paciente', 10)->primary();
            $table->string('nombre_paciente', 40)->notNull();
            $table->string('apellido1_paciente', 30)->nullable();
            $table->string('apellido2_paciente', 30)->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('genero_paciente', 20)->notNull();
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
