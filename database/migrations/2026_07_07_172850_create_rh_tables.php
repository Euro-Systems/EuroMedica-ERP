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
        Schema::create('rh_citas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('puesto')->nullable();
            $table->string('tipo')->nullable();
            $table->string('fecha')->nullable();
            $table->string('hora')->nullable();
            $table->string('entrevistador_rh')->nullable();
            $table->string('jefe_depto')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->text('notas')->nullable();
            $table->string('estado')->nullable()->default('Pendiente');
            $table->longText('documentos')->nullable(); // Guardará JSON con base64/paths
            $table->timestamps();
        });

        Schema::create('rh_candidatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('ap')->nullable();
            $table->string('am')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('puesto_deseado')->nullable();
            $table->string('expectativa_salarial')->nullable();
            $table->string('canal_captacion')->nullable();
            $table->string('fecha_postulacion')->nullable();
            $table->string('tipo_candidatura')->nullable();
            $table->string('estatus_reclutamiento')->nullable();
            $table->text('entrevista')->nullable();
            $table->text('evaluacion')->nullable();
            $table->longText('documentos')->nullable();
            $table->longText('observaciones')->nullable();
            $table->longText('evaluacion_details')->nullable();
            $table->string('fecha_agendado')->nullable();
            $table->string('fecha_entrevista')->nullable();
            $table->string('horarios_disponibles')->nullable();
            $table->integer('calificacion')->nullable()->default(0);
            $table->string('estado')->nullable();
            $table->timestamps();
        });

        Schema::create('rh_practicantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('ap')->nullable();
            $table->string('am')->nullable();
            $table->string('empresa')->nullable();
            $table->string('fecha_inicio')->nullable();
            $table->string('fecha_termino')->nullable();
            $table->string('horas_requeridas')->nullable();
            $table->string('horas_llevadas')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('nacimiento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('nivel_ingles')->nullable();
            $table->string('talla_uniforme')->nullable();
            $table->string('tipo_sangre')->nullable();
            $table->string('alergias')->nullable();
            $table->string('escuela_procedencia')->nullable();
            $table->string('egreso')->nullable();
            $table->string('motivo')->nullable();
            $table->string('puesto')->nullable();
            $table->string('puesto_solicitado')->nullable();
            $table->boolean('destacado')->default(false);
            $table->longText('documentos')->nullable();
            $table->longText('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('rh_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('ap')->nullable();
            $table->string('am')->nullable();
            $table->string('empresa')->nullable();
            $table->string('nss')->nullable();
            $table->string('rfc')->nullable();
            $table->string('curp')->nullable();
            $table->string('sexo')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('nacimiento')->nullable();
            $table->string('fecha')->nullable();
            $table->string('puesto')->nullable();
            $table->string('salario')->nullable();
            $table->string('departamento')->nullable();
            $table->string('fecha_conversion')->nullable();
            $table->string('alta_imss')->nullable();
            $table->string('egreso')->nullable();
            $table->string('motivo')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->string('parentesco')->nullable();
            $table->string('tel_emergencia1')->nullable();
            $table->string('tel_emergencia2')->nullable();
            $table->string('talla_uniforme')->nullable();
            $table->string('tipo_sangre')->nullable();
            $table->string('alergias')->nullable();
            $table->string('clabe_bancaria')->nullable();
            $table->string('estado')->nullable();
            $table->longText('documentos')->nullable();
            $table->longText('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('rh_vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('rh_empleados')->onDelete('cascade');
            $table->string('inicio')->nullable();
            $table->string('fin')->nullable();
            $table->integer('dias')->nullable();
            $table->string('tipo')->nullable();
            $table->string('cobertura')->nullable();
            $table->string('estado')->default('Pendiente');
            $table->timestamps();
        });

        Schema::create('rh_vacaciones_anuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('rh_empleados')->onDelete('cascade');
            $table->integer('anio')->nullable();
            $table->integer('dias_totales')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rh_vacaciones_anuales');
        Schema::dropIfExists('rh_vacaciones');
        Schema::dropIfExists('rh_empleados');
        Schema::dropIfExists('rh_practicantes');
        Schema::dropIfExists('rh_candidatos');
        Schema::dropIfExists('rh_citas');
    }
};
