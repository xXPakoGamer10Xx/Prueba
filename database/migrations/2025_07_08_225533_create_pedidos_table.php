<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ¡Importa la fachada DB para usar CURRENT_DATE!

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            
            $table->foreignId('id_insumo_almacen_fk')
                  ->notNull()
                  ->constrained('almacen', 'id_insumo_almacen')
                  ->onDelete('restrict');

            $table->integer('cantidad_solicitada')->notNull();
            $table->integer('cantidad_autorizada')->default(0)->nullable();

            $table->enum('estado_pedido', ['Pendiente', 'Entregado', 'Cancelado'])->notNull();

            $table->date('fecha_pedido')->default(DB::raw('CURRENT_DATE'));
            
            $table->date('fecha_entrega')->nullable();

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};