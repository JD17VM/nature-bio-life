<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canje_premios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('premio_id')->constrained('premios'); // Asumiendo que tabla 'premios' ya existe
            
            $table->integer('puntos_utilizados');
            $table->string('estado')->default('pendiente'); // pendiente, aprobado, entregado, rechazado
            
            $table->timestamp('fecha_canje')->useCurrent();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canje_premios');
    }
};