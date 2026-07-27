<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (creación de tablas).
     */
    public function up(): void
    {
        // Creación de la tabla 'users' (usuarios de la aplicación)
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                       // Clave primaria auto-incremental (BIGINT)
            $table->string('name');                             // Nombre del usuario (VARCHAR)
            $table->string('email')->unique();                  // Correo electrónico único para evitar duplicados
            $table->timestamp('email_verified_at')->nullable(); // Fecha de verificación de cuenta (puede ser nula)
            $table->string('password');                         // Contraseña encriptada del usuario
            $table->rememberToken();                            // Campo VARCHAR equivalente para el token de "recordarme"
            $table->timestamps();                               // Crea automáticamente las columnas 'created_at' y 'updated_at'
        });

        // Creación de la tabla 'password_reset_tokens' (tokens para restablecer contraseñas)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();                 // El correo es la clave primaria (un token activo por correo)
            $table->string('token');                            // El token seguro generado para el restablecimiento
            $table->timestamp('created_at')->nullable();        // Fecha de creación del token para gestionar su expiración
        });

        // Creación de la tabla 'sessions' (almacenamiento de sesiones en base de datos)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                    // ID único de la sesión generado por Laravel
            $table->foreignId('user_id')->nullable()->index();  // ID del usuario autenticado (nulo si es visitante) con índice
            $table->string('ip_address', 45)->nullable();       // Dirección IP del dispositivo (soporta IPv4 e IPv6)
            $table->text('user_agent')->nullable();             // Información del navegador y sistema operativo del usuario
            $table->longText('payload');                        // Datos serializados almacenados dentro de la sesión
            $table->integer('last_activity')->index();          // Timestamp Unix de la última actividad del usuario con índice
        });
    }

    /**
     * Revierte las migraciones (eliminación de tablas en orden inverso).
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};