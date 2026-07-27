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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('objetivo')->nullable();
            $table->foreignId('empleado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jefe_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_estimada_fin');
            $table->string('tiempo_estimado');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['pendiente', 'en_proceso', 'finalizada', 'atrasada', 'cancelada'])->default('pendiente');
            $table->enum('impacto', ['Ninguno', 'Pacientes', 'Sistemas', 'Administración', 'Recursos Humanos', 'Medicina Laboral', 'Laboratorio', 'Operaciones', 'Dirección']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
