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
        Schema::create('procesos_baja', function (Blueprint $table) {
            // CAMBIO: Usa id() para la llave primaria auto-incremental.
            $table->id('id_proceso_baja');
        
            // CAMBIO: Usa foreignId() para las llaves foráneas.
            $table->foreignId('id_inventario_fk')
                  ->constrained('inventarios', 'id_inventario') // Apunta a la tabla y columna correctas
                  ->onDelete('restrict');
        
            $table->foreignId('id_mantenimiento_fk')
                  ->nullable()
                  ->constrained('mantenimientos', 'id_mantenimiento') // Apunta a la tabla y columna correctas
                  ->onDelete('set null');
        
            $table->text('motivo');
            $table->enum('estado', ['en proceso', 'baja completa', 'cancelado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos_baja');
    }
};
