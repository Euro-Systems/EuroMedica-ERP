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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
