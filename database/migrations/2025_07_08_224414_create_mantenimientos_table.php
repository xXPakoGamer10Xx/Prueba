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
        Schema::create('mantenimientos', function (Blueprint $table) {        
            $table->integer('id_mantenimiento')->primary();
                    
            $table->integer('id_inventario')->notNull();
            $table->foreign('id_inventario')
                  ->references('id_inventario')
                  ->on('inventarios')
                  ->onDelete('restrict');
        
            $table->integer('id_encargado_man')->notNull();
            $table->foreign('id_encargado_man')
                  ->references('id_encargado_man')
                  ->on('encargados_mantenimiento')
                  ->onDelete('restrict');
        
            $table->date('fecha')->notNull();        
            $table->text('refacciones_material')->nullable();
            $table->enum('tipo', ['preventivo', 'correctivo'])->notNull();
            $table->text('observaciones')->nullable();
        
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
