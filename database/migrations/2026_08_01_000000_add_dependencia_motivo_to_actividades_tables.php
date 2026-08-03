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
        if (Schema::hasTable('actividades') && !Schema::hasColumn('actividades', 'dependencia_motivo')) {
            Schema::table('actividades', function (Blueprint $table) {
                $table->text('dependencia_motivo')->nullable()->after('dependencia_responsable');
            });
        }

        if (Schema::hasTable('rutinas') && !Schema::hasColumn('rutinas', 'dependencia_motivo')) {
            Schema::table('rutinas', function (Blueprint $table) {
                $table->text('dependencia_motivo')->nullable()->after('dependencia_responsable');
            });
        }

        if (Schema::hasTable('actividades_imprevistas') && !Schema::hasColumn('actividades_imprevistas', 'dependencia_motivo')) {
            Schema::table('actividades_imprevistas', function (Blueprint $table) {
                $table->text('dependencia_motivo')->nullable()->after('dependencia_responsable');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actividades') && Schema::hasColumn('actividades', 'dependencia_motivo')) {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropColumn('dependencia_motivo');
            });
        }

        if (Schema::hasTable('rutinas') && Schema::hasColumn('rutinas', 'dependencia_motivo')) {
            Schema::table('rutinas', function (Blueprint $table) {
                $table->dropColumn('dependencia_motivo');
            });
        }

        if (Schema::hasTable('actividades_imprevistas') && Schema::hasColumn('actividades_imprevistas', 'dependencia_motivo')) {
            Schema::table('actividades_imprevistas', function (Blueprint $table) {
                $table->dropColumn('dependencia_motivo');
            });
        }
    }
};
