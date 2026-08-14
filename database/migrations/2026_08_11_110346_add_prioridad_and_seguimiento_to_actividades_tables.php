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
        // 1. Modificaciones para la tabla 'actividades'
        Schema::table('actividades', function (Blueprint $table) {
            $table->string('prioridad')->nullable()->change();
            $table->boolean('en_seguimiento')->default(false)->after('estado');
            $table->date('fecha_seguimiento')->nullable()->after('en_seguimiento');
        });

        // 2. Modificaciones para la tabla 'actividades_imprevistas'
        Schema::table('actividades_imprevistas', function (Blueprint $table) {
            $table->string('prioridad')->nullable()->after('resultado_obtenido');
            $table->boolean('en_seguimiento')->default(false)->after('estado');
            $table->date('fecha_seguimiento')->nullable()->after('en_seguimiento');
        });

        // 3. Modificaciones para la tabla 'rutinas'
        Schema::table('rutinas', function (Blueprint $table) {
            $table->boolean('en_seguimiento')->default(false)->after('porcentaje_avance');
            $table->date('fecha_seguimiento')->nullable()->after('en_seguimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->string('prioridad')->nullable(false)->change();
            $table->dropColumn(['en_seguimiento', 'fecha_seguimiento']);
        });

        Schema::table('actividades_imprevistas', function (Blueprint $table) {
            $table->dropColumn(['prioridad', 'en_seguimiento', 'fecha_seguimiento']);
        });

        Schema::table('rutinas', function (Blueprint $table) {
            $table->dropColumn(['en_seguimiento', 'fecha_seguimiento']);
        });
    }
};
