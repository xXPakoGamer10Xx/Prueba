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
        Schema::create('materiales_externos', function (Blueprint $table) {
            $table->id('id_material'); // Clave primaria auto-incrementable
            $table->string('descripcion', 255)->notNull(); // 'VARCHAR(255) NOT NULL'
            $table->integer('cantidad')->notNull(); // 'INT NOT NULL'
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales_externos');
    }
};