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
        Schema::create('encargados_area', function (Blueprint $table) {
            // CAMBIO: Usa id() para crear una llave primaria auto-incremental.
            // Esto le dice a la base de datos que genere el ID automáticamente.
            $table->id('id_encargado_area');

            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('cargo', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargados_area');
    }
};
