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
        if (Schema::hasTable('actividades') && !Schema::hasColumn('actividades', 'porcentaje_avance')) {
            Schema::table('actividades', function (Blueprint $table) {
                $table->integer('porcentaje_avance')->default(0);
            });
        }

        if (Schema::hasTable('rutinas') && !Schema::hasColumn('rutinas', 'porcentaje_avance')) {
            Schema::table('rutinas', function (Blueprint $table) {
                $table->integer('porcentaje_avance')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actividades') && Schema::hasColumn('actividades', 'porcentaje_avance')) {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropColumn('porcentaje_avance');
            });
        }

        if (Schema::hasTable('rutinas') && Schema::hasColumn('rutinas', 'porcentaje_avance')) {
            Schema::table('rutinas', function (Blueprint $table) {
                $table->dropColumn('porcentaje_avance');
            });
        }
    }
};
