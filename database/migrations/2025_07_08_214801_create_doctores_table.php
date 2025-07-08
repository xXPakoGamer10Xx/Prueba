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
        Schema::create('doctores', function (Blueprint $table) {
            $table->string('id_doctor', 10)->primary();
            $table->string('nombre_doctor', 40)->nullable();
            $table->string('apellido1_doctor', 30)->nullable();
            $table->string('apellido2_doctor', 30)->nullable();
            $table->enum('Turno', ['matutino', 'vespertino', 'nocturno'])->notNull();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctores');
    }
};