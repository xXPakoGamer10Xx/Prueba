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
        Schema::create('procesos_baja', function (Blueprint $table) {
            $table->integer('id_proceso_baja')->primary();
        
            $table->integer('id_inventario_fk')->notNull();
            $table->foreign('id_inventario_fk')
                  ->references('id_inventario')
                  ->on('inventarios')
                  ->onDelete('restrict');
        
            $table->integer('id_mantenimiento_fk')->nullable();
            $table->foreign('id_mantenimiento_fk')
                  ->references('id_mantenimiento')
                  ->on('mantenimientos')
                  ->onDelete('set null');
        
            $table->text('motivo')->notNull();
        
            $table->enum('estado', ['en proceso', 'baja completa', 'cancelado'])->notNull();

            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos_baja');
    }
};
