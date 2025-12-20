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
        Schema::create('estado_pedidos', function (Blueprint $table) {
            $table->id();
            
            // Relación con el pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            
            // El estado que tuvo en ese momento
            $table->string('estado'); 
            $table->text('observaciones')->nullable();
            
            // Fecha exacta del cambio
            $table->timestamp('fecha_cambio')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_pedidos');
    }
};
