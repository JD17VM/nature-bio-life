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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            
            // Código único para identificar el pedido (ej: ORD-001)
            $table->string('numero_pedido')->unique();
            
            // Relación con Usuario (Si borras el usuario, se borran sus pedidos)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Datos económicos (Decimales para dinero)
            $table->decimal('subtotal', 10, 2);
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Puntos que gana el usuario por esta compra
            $table->integer('puntos_ganados')->default(0);
            
            // Estado actual (pendiente, pagado, etc)
            $table->string('estado')->default('pendiente'); 
            $table->text('notas')->nullable(); // Notas opcionales del cliente
            
            // Fechas de control
            $table->timestamp('fecha_pedido')->useCurrent(); // Fecha de creación
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
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
