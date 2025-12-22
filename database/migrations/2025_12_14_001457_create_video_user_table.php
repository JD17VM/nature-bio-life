<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            
            // RF-087: Guardar el segundo exacto donde se detuvo
            $table->integer('segundo_actual')->default(0);
            
            // RF-088: Estado de completado
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_completado')->nullable();
            
            $table->timestamps();

            // Evitar duplicados: Un usuario solo tiene un registro por video
            $table->unique(['user_id', 'video_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_user');
    }
};