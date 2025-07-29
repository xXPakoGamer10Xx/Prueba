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
            $table->foreignId('id_equipo_fk')->constrained(table: 'equipos', column: 'id_equipo')->onDelete('cascade');
            $table->foreignId('id_area_fk')->constrained(table: 'areas', column: 'id_area')->onDelete('cascade');

            $table->string('num_serie', 50)->unique()->nullable();
            $table->string('num_serie_sicopa', 50)->unique()->nullable();
            $table->string('num_serie_sia', 50)->unique()->nullable();
            $table->enum('pertenencia', ['propia', 'comodato']);
            $table->enum('status', ['funcionando', 'sin funcionar', 'parcialmente funcional', 'proceso de baja', 'baja']);
            $table->foreignId('id_garantia_fk')->nullable()->constrained(table: 'garantias', column: 'id_garantia')->onDelete('set null');

            $table->timestamps();
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
