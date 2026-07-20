<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Area;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('departamento')->nullable();
            $table->text('permisos')->nullable();
        });

        // Aseguramos que exista al menos una área para asociar por defecto
        $area = Area::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Área General', 'activo' => true]
        );

        // Creamos el administrador por defecto
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin'),
                'area_id' => $area->id,
                'rol' => 'admin',
                'activo' => true,
                'departamento' => 'Administración',
                'permisos' => 'todos'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['departamento', 'permisos']);
        });
    }
};
