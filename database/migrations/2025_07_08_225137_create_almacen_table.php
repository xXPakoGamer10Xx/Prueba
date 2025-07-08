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
        Schema::create('almacen', function (Blueprint $table) {
            $table->id('id_insumo_almacen');
            
            $table->foreignId('id_insumo_fk')
                  ->notNull()
                  ->constrained('insumos', 'id_insumo')
                  ->onDelete('restrict');
            
            $table->integer('cantidad')->notNull()->default(0);
           
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen');
    }
};
