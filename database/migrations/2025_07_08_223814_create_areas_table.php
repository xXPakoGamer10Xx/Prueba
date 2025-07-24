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
        Schema::create('areas', function (Blueprint $table) {
            // CAMBIO: Usa id() para la llave primaria.
            $table->id('id_area');
            $table->string('nombre', 100);

            // Usa foreignId() para asegurar que los tipos de datos coincidan.
            $table->foreignId('id_encargado_area_fk')
                  ->nullable()
                  ->constrained('encargados_area', 'id_encargado_area')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
