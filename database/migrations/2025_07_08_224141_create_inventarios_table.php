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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->integer('id_inventario')->primary(); 
            
            $table->integer('id_equipo_fk')->notNull(); 
            $table->foreign('id_equipo_fk')
                  ->references('id_equipo')
                  ->on('equipos')
                  ->onDelete('restrict'); 
            
            $table->string('num_serie', 50)->nullable(); 
            $table->string('num_serie_sicopa', 50)->nullable(); 
            $table->string('num_serie_sia', 50)->nullable(); 
            
            $table->enum('pertenencia', ['propia', 'prestamo', 'comodato'])->notNull(); 
            
            $table->enum('status', ['funcionando', 'sin funcionar', 'parcialmente funcional', 'proceso de baja'])->notNull(); 
            
            $table->integer('id_area_fk')->notNull(); 
            $table->foreign('id_area_fk')
                  ->references('id_area')
                  ->on('areas')
                  ->onDelete('restrict'); 
            
            $table->integer('id_garantia_fk')->nullable(); 
            $table->foreign('id_garantia_fk')
                  ->references('id_garantia')
                  ->on('garantias')
                  ->onDelete('set null'); 
            
            // $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
