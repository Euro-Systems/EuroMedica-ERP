<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rh_citas') && !Schema::hasColumn('rh_citas', 'evaluacion')) {
            Schema::table('rh_citas', function (Blueprint $table) {
                $table->longText('evaluacion')->nullable()->after('documentos');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rh_citas') && Schema::hasColumn('rh_citas', 'evaluacion')) {
            Schema::table('rh_citas', function (Blueprint $table) {
                $table->dropColumn('evaluacion');
            });
        }
    }
};
