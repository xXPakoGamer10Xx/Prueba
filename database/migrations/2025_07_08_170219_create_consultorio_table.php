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
        Schema::create('consultorio', function (Blueprint $table) {
            $table->id('id_insumo_consultorio');
            
            // Clave foránea para 'id_insumo_fk'
            $table->foreignId('id_insumo_fk')
                  ->notNull()
                  ->constrained('insumos', 'id_insumo')
                  ->onDelete('restrict');

            $table->integer('cantidad')->notNull()->default(0);

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultorio');
    }
};