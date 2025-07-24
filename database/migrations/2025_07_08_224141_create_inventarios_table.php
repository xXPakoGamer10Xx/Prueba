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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->string('num_serie', 50)->nullable();
            $table->string('num_serie_sicopa', 50)->nullable();
            $table->string('num_serie_sia', 50)->nullable();
            $table->enum('pertenencia', ['propia', 'prestamo', 'comodato']);
            $table->enum('status', ['funcionando', 'sin funcionar', 'parcialmente funcional', 'proceso de baja']);

            // --- LLAVES FORÁNEAS CORREGIDAS ---
            // Este método asegura que el tipo de dato es correcto.
            $table->foreignId('id_equipo_fk')->constrained('equipos', 'id_equipo');
            
            // Esta es la línea que causaba el error, ahora corregida.
            $table->foreignId('id_area_fk')->constrained('areas', 'id_area');
            
            $table->foreignId('id_garantia_fk')->nullable()->constrained('garantias', 'id_garantia')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
