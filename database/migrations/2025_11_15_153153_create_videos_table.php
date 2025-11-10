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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->integer('duracion_segundos')->default(0);
            
            $table->unsignedBigInteger('categoria_video_id');
            $table->foreign('categoria_video_id')
                  ->references('id')
                  ->on('categoria_videos')
                  ->onDelete('restrict');

            $table->string('nivel')->nullable(); // Ej: Básico, Intermedio
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};