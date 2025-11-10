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
        Schema::create('premios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('puntos_requeridos')->default(0);
            $table->integer('stock')->default(0);
            
            $table->unsignedBigInteger('categoria_premio_id');
            $table->foreign('categoria_premio_id')
                  ->references('id')
                  ->on('categoria_premios')
                  ->onDelete('restrict');

            $table->string('imagen_url')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premios');
    }
};