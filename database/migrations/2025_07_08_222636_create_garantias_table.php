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
        Schema::create('garantias', function (Blueprint $table) {
            $table->integer('id_garantia')->primary();
        
            $table->enum('status', ['activa', 'terminada'])->notNull();
        
            $table->string('empresa', 100)->nullable();
            $table->string('contacto', 100)->nullable();
        
            $table->date('fecha_garantia')->nullable();
        
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
