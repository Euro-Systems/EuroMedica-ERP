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
        Schema::table('rutinas', function (Blueprint $table) {
            $table->integer('veces_al_dia')->default(1)->after('frecuencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rutinas', function (Blueprint $table) {
            $table->dropColumn('veces_al_dia');
        });
    }
};
