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
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('prioridad')->default('media');
            $table->string('impacto');
            $table->foreignId('empleado_id')->constrained('users')->onDelete('cascade');
            $table->string('frecuencia')->default('diaria');
            $table->timestamps();
        });

        Schema::create('ejecuciones_rutina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutinas')->onDelete('cascade');
            $table->date('fecha');
            $table->integer('cantidad_ejecuciones')->default(0);
            $table->text('horas_registro')->nullable(); // JSON array of H:i
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejecuciones_rutina');
        Schema::dropIfExists('rutinas');
    }
};
