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
        Schema::create('encargados_mantenimiento', function (Blueprint $table) {
            // CAMBIO: Usa id() para crear una llave primaria auto-incremental.
            $table->id('id_encargado_man');
            
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('contacto', 100)->nullable();
            $table->string('cargo', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargados_mantenimiento');
    }
};
