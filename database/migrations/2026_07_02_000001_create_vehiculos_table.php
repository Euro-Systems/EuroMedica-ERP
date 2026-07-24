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
        if (!Schema::hasTable('vehiculos')) {
            Schema::create('vehiculos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre'); // e.g. "Unidad 1"
                $table->string('marca')->nullable();
                $table->string('modelo')->nullable();
                $table->string('placas')->nullable();
                $table->string('color')->nullable();
                $table->string('transmision')->nullable();
                $table->string('numero_serie')->nullable();
                $table->string('numero_economico')->nullable();
                $table->date('fecha_compra')->nullable();
                $table->string('seguro_auto')->nullable();
                $table->string('telefono_seguro')->nullable();
                $table->date('inicio_seguro')->nullable();
                $table->date('caducidad_seguro')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('vehiculos', function (Blueprint $table) {
                if (!Schema::hasColumn('vehiculos', 'nombre')) {
                    $table->string('nombre')->nullable()->after('id');
                }
            });

            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE vehiculos MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
            } catch (\Throwable $e) {
                // ignore if already bigint or not supported
            }

            \Illuminate\Support\Facades\DB::table('vehiculos')->whereNull('nombre')->orWhere('nombre', '')->get()->each(function ($v) {
                $nombre = $v->numero_economico ?: (trim(($v->marca ?? '') . ' ' . ($v->modelo ?? '')));
                if (empty(trim($nombre))) {
                    $nombre = 'Vehículo ' . $v->id;
                }
                \Illuminate\Support\Facades\DB::table('vehiculos')->where('id', $v->id)->update(['nombre' => $nombre]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
