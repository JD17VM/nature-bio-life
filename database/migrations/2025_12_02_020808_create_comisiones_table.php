<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('vendedor_id')->constrained('users'); // Quien gana la comisión
            $table->foreignId('comprador_id')->constrained('users'); // Quien hizo la compra
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            
            // Datos financieros
            $table->decimal('monto_compra', 10, 2); // Base del cálculo
            $table->decimal('porcentaje', 5, 2); // Ej: 10.00%
            $table->decimal('monto_comision', 10, 2); // El resultado (dinero ganado)
            
            // Estado y Fechas
            $table->string('estado')->default('pendiente'); // pendiente, aprobada, pagada, anulada
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->timestamp('fecha_pago')->nullable();
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};