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
        Schema::table('procesos_baja', function (Blueprint $table) {
            // Agrega la columna 'fecha_baja' de tipo date
            // Puedes decidir si es nullable o no. Si es 'required' en tu modelo, debería ser notNullable.
            $table->date('fecha_baja')->after('estado')->nullable(); // O ->nullable(false) si siempre es requerida
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procesos_baja', function (Blueprint $table) {
            // Elimina la columna si se hace un rollback
            $table->dropColumn('fecha_baja');
        });
    }
};
