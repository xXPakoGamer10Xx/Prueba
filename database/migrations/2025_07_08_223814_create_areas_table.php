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
        Schema::create('areas', function (Blueprint $table) {
            $table->integer('id_area')->primary();
        
            $table->integer('id_encargado_area_fk')->nullable();
        
            $table->foreign('id_encargado_area_fk')
                  ->references('id_encargado_area')
                  ->on('encargados_area')
                  ->onDelete('set null');
        
            $table->string('nombre', 100)->notNull();
        
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
