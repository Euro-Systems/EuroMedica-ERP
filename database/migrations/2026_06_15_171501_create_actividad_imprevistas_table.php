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
        Schema::create('actividades_imprevistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion_detallada');
            $table->text('motivo');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->decimal('horas_invertidas', 8, 2);
            $table->text('resultado_obtenido');
            $table->text('observaciones')->nullable();
            $table->enum('impacto', ['Ninguno', 'Pacientes', 'Sistemas', 'Administración', 'Recursos Humanos', 'Medicina Laboral', 'Laboratorio', 'Operaciones', 'Dirección']);
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades_imprevistas');
    }
};
