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
        Schema::table('actividades', function (Blueprint $table) {
            if (Schema::hasColumn('actividades', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->change();
            }
            if (Schema::hasColumn('actividades', 'fecha_estimada_fin')) {
                $table->date('fecha_estimada_fin')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            if (Schema::hasColumn('actividades', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable(false)->change();
            }
            if (Schema::hasColumn('actividades', 'fecha_estimada_fin')) {
                $table->date('fecha_estimada_fin')->nullable(false)->change();
            }
        });
    }
};
