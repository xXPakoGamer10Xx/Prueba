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
        Schema::create('garantias', function (Blueprint $table) {
            // CAMBIO: Usa id() para crear una llave primaria UNSIGNED BIGINT.
            $table->id('id_garantia');
        
            $table->enum('status', ['activa', 'terminada']);
            $table->string('empresa', 100)->nullable();
            $table->string('contacto', 100)->nullable();
            $table->date('fecha_garantia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
