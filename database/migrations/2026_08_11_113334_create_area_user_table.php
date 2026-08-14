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
        Schema::create('area_user', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'area_id']);
        });

        // Migración de datos de jefes existentes
        $jefes = DB::table('users')->where('rol', 'jefe')->get();
        foreach ($jefes as $jefe) {
            if (empty($jefe->departamento)) {
                continue;
            }
            $depts = array_map('trim', explode('/', $jefe->departamento));
            foreach ($depts as $deptName) {
                if (empty($deptName)) continue;

                $mapping = [
                    'Sistemas' => 'TI',
                    'Analisis de datos' => 'ADD',
                    'Análisis de datos' => 'ADD',
                    'Marketing' => 'MKT',
                    'Administracion' => 'Administración',
                    'Administración de empresas' => 'ADE',
                    'Administracion de empresas' => 'ADE',
                    'Nomina' => 'Nómina',
                    'RH' => 'Recursos Humanos',
                ];
                $mappedName = isset($mapping[$deptName]) ? $mapping[$deptName] : $deptName;

                $area = DB::table('areas')->where('nombre', $mappedName)->first();
                if ($area) {
                    $exists = DB::table('area_user')
                        ->where('user_id', $jefe->id)
                        ->where('area_id', $area->id)
                        ->exists();
                    if (!$exists) {
                        DB::table('area_user')->insert([
                            'user_id' => $jefe->id,
                            'area_id' => $area->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_user');
    }
};

