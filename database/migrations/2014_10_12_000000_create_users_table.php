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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Campos de tus requisitos
            $table->string('nombre_completo');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('dni')->unique()->nullable();
            $table->string('password');
            $table->string('codigo_referido')->unique()->nullable();
            $table->text('direccion')->nullable();
            $table->text('info_bancaria_encriptada')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('intentos_login_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            
            // Relación de patrocinador (auto-referencia)
            $table->unsignedBigInteger('patrocinador_id')->nullable();
            $table->foreign('patrocinador_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Si el patrocinador se elimina, se pone nulo

            // Campos estándar de Laravel
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps(); // fecha_registro (created_at) y ultimo_acceso (updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};