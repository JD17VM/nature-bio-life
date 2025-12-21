<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Puede ser nulo si los puntos son un regalo manual o por canje
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            
            $table->integer('puntos'); // Cantidad (ej: +100 o -500)
            $table->string('tipo'); // 'ingreso' (compra) o 'egreso' (canje)
            
            // Auditoría de saldo (opcional pero recomendado)
            $table->integer('balance_anterior')->default(0);
            $table->integer('balance_nuevo')->default(0);
            
            $table->timestamp('fecha')->useCurrent();
            $table->text('descripcion')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_puntos');
    }
};