<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `actividades` MODIFY COLUMN `impacto` ENUM('Ninguno', 'Pacientes', 'Sistemas', 'Administración', 'Recursos Humanos', 'Medicina Laboral', 'Laboratorio', 'Operaciones', 'Dirección') NOT NULL DEFAULT 'Ninguno'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `actividades` MODIFY COLUMN `impacto` ENUM('Pacientes', 'Sistemas', 'Administración', 'Recursos Humanos', 'Medicina Laboral', 'Laboratorio', 'Operaciones', 'Dirección') NOT NULL");
    }
};
