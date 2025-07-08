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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id('id_insumo');
            $table->string('clave', 30)->nullable();
            $table->string('descripcion', 255)->notNull();

            // Clave foránea para 'id_laboratorio'
            $table->foreignId('id_laboratorio')
                  ->notNull()
                  ->constrained('laboratorios', 'id_laboratorio')
                  ->onDelete('restrict');

            // Clave foránea para 'id_presentacion'
            $table->foreignId('id_presentacion')
                  ->notNull()
                  ->constrained('presentaciones', 'id_presentacion')
                  ->onDelete('restrict');
                                        
            $table->string('contenido', 50)->nullable();
            $table->date('caducidad')->nullable();

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};