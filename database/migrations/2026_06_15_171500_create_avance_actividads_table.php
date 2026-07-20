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
        Schema::create('avances_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('users')->onDelete('cascade');
            $table->integer('porcentaje_avance');
            $table->decimal('horas_trabajadas', 8, 2);
            $table->text('comentario')->nullable();
            $table->text('que_se_hizo');
            $table->text('motivo');
            $table->text('problema_detectado')->nullable();
            $table->text('acciones_realizadas')->nullable();
            $table->text('resultado_final')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_avance');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avances_actividad');
    }
};
