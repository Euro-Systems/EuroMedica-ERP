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
        Schema::create('rh_practicante_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practicante_id')->constrained('rh_practicantes')->onDelete('cascade');
            $table->string('inicio')->nullable();
            $table->string('fin')->nullable();
            $table->integer('dias')->nullable();
            $table->string('tipo')->nullable();
            $table->string('cobertura')->nullable();
            $table->string('estado')->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rh_practicante_permisos');
    }
};
