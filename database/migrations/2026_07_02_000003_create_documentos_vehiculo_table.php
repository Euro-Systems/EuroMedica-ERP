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
        if (!Schema::hasTable('documentos_vehiculo')) {
            Schema::create('documentos_vehiculo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
                $table->string('nombre');
                $table->string('ruta');
                $table->string('tipo'); // 'pdf' or 'imagen'
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_vehiculo');
    }
};
