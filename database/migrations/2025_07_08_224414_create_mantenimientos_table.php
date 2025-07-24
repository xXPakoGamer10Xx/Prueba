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
        Schema::create('mantenimientos', function (Blueprint $table) {
            // CAMBIO: Usa id() para la llave primaria.
            $table->id('id_mantenimiento');

            // CAMBIO: Usa foreignId() para las llaves foráneas.
            $table->foreignId('id_inventario')->constrained('inventarios', 'id_inventario')->onDelete('restrict');
            $table->foreignId('id_encargado_man')->constrained('encargados_mantenimiento', 'id_encargado_man')->onDelete('restrict');
        
            $table->date('fecha');
            $table->text('refacciones_material')->nullable();
            $table->enum('tipo', ['preventivo', 'correctivo']);
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
