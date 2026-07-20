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
        Schema::create('rh_contratos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        $table->string('tipo');
        $table->date('mes1')->nullable();
        $table->date('mes2')->nullable();
        $table->date('mes3')->nullable();
        $table->date('indefinido')->nullable();
            $table->timestamps();
        });
    }





    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rh_contratos');
    }
};
