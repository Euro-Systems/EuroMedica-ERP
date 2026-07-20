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
        Schema::table('avances_actividad', function (Blueprint $table) {
            $table->string('estado_aprobacion')->default('pendiente'); // pendiente, aprobado, rechazado
            $table->foreignId('aprobado_por_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha_aprobacion')->nullable();
            $table->time('hora_aprobacion')->nullable();
            $table->text('comentario_jefe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avances_actividad', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por_id']);
            $table->dropColumn(['estado_aprobacion', 'aprobado_por_id', 'fecha_aprobacion', 'hora_aprobacion', 'comentario_jefe']);
        });
    }
};
