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
        Schema::create('cirugiasgeneral', function (Blueprint $table) {
        
            $table->string('id_cirugia_general', 10)->primary();
            $table->date('fecha_ingreso')->notNull();
            $table->date('fecha_egreso')->nullable();
            $table->string('pieza_patologica', 10)->nullable();
            $table->string('region_anatomica', 100)->notNull();
            $table->enum('tipoCirugia', ['programada', 'urgencia'])->nullable();

            $table->string('id_doctor', 10)->notNull();
            $table->foreign('id_doctor')->references('id_doctor')->on('doctores')->onDelete('restrict');

            $table->string('id_paciente', 10)->notNull();
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('restrict');

            $table->string('id_diagnostico', 10)->notNull();
            $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('restrict');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugiasgeneral');
    }
};