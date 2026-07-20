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
        Schema::create('servicios_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->date('fecha')->nullable();
            $table->string('solicitud_servicio')->nullable();
            $table->text('cotizacion_opciones')->nullable(); // JSON string
            $table->string('cotizacion_aceptada')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->date('fecha_realizacion')->nullable();
            $table->text('observacion')->nullable();
            $table->string('proveedor')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->string('factura')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_vehiculo');
    }
};
