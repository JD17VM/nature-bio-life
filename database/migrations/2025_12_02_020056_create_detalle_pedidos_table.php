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
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            
            // Si borras el pedido, se borran sus detalles
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            
            // Relación con Producto (NO usamos cascade, para mantener historial)
            $table->foreignId('producto_id')->constrained('productos'); 
            
            $table->integer('cantidad');
            
            // Guardamos el precio AL MOMENTO de la compra (por si cambia después)
            $table->decimal('precio_unitario', 10, 2); 
            $table->decimal('subtotal', 10, 2);
            $table->integer('puntos_unitarios')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }
};
