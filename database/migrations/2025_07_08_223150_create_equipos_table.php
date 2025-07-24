<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            // CAMBIO: Usa id() para la llave primaria.
            $table->id('id_equipo');
            
            $table->string('nombre', 100);
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->integer('cantidad');
            $table->integer('frecuencia_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
