<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar temporalmente los checks de claves foráneas para truncar de manera limpia
        DB::statement('PRAGMA foreign_keys = OFF;');
        User::truncate();
        Area::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Crear las áreas correspondientes
        $tiArea = Area::create(['nombre' => 'TI', 'activo' => true]);
        $addArea = Area::create(['nombre' => 'ADD', 'activo' => true]);
        $mktArea = Area::create(['nombre' => 'MKT', 'activo' => true]);
        $rhArea = Area::create(['nombre' => 'Recursos Humanos', 'activo' => true]);
        $adeArea = Area::create(['nombre' => 'ADE', 'activo' => true]);
        $nominaArea = Area::create(['nombre' => 'Nómina', 'activo' => true]);
        $operacionesArea = Area::create(['nombre' => 'Operaciones', 'activo' => true]);
        $adminArea = Area::create(['nombre' => 'Administración', 'activo' => true]);

        // 1. Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => null,
            'password' => Hash::make('19041022'),
            'password_plain' => Crypt::encryptString('19041022'),
            'area_id' => $adminArea->id,
            'rol' => 'admin',
            'activo' => true,
            'departamento' => 'Administración',
            'permisos' => 'todos',
        ]);

        // 2. Kevin (Jefe TI, ADD, MKT)
        $kevin = User::create([
            'name' => 'Kevin',
            'email' => null,
            'password' => Hash::make('EuroMedica'),
            'password_plain' => Crypt::encryptString('EuroMedica'),
            'area_id' => null, // Jefes no tienen un área fija única
            'rol' => 'jefe',
            'activo' => true,
            'departamento' => 'TI / ADD / MKT',
            'permisos' => 'sistemas,administracion,otros',
        ]);

        // 3. Jose (Practicante TI, bajo Kevin)
        User::create([
            'name' => 'Jose',
            'email' => null,
            'password' => Hash::make('EuroMedica'),
            'password_plain' => Crypt::encryptString('EuroMedica'),
            'area_id' => $tiArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'TI',
            'permisos' => 'sistemas',
            'jefe_id' => $kevin->id,
        ]);

        // 4. Andrea (Practicante ADD, bajo Kevin)
        User::create([
            'name' => 'Andrea',
            'email' => null,
            'password' => Hash::make('euroMEDICA'),
            'password_plain' => Crypt::encryptString('euroMEDICA'),
            'area_id' => $addArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'ADD',
            'permisos' => 'administracion',
            'jefe_id' => $kevin->id,
        ]);

        // 5. Jose Diaz (Practicante TI, bajo Kevin)
        User::create([
            'name' => 'Jose Diaz',
            'email' => null,
            'password' => Hash::make('EUROmedica'),
            'password_plain' => Crypt::encryptString('EUROmedica'),
            'area_id' => $tiArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'TI',
            'permisos' => 'sistemas',
            'jefe_id' => $kevin->id,
        ]);

        // 6. Edwin (Jefe RH)
        $edwin = User::create([
            'name' => 'Edwin',
            'email' => null,
            'password' => Hash::make('Euromedica321'),
            'password_plain' => Crypt::encryptString('Euromedica321'),
            'area_id' => null,
            'rol' => 'jefe',
            'activo' => true,
            'departamento' => 'Recursos Humanos',
            'permisos' => 'rh,nomina',
        ]);

        // 7. Samantha (Empleada RH, bajo Edwin)
        User::create([
            'name' => 'Samantha',
            'email' => null,
            'password' => Hash::make('Euromedica123'),
            'password_plain' => Crypt::encryptString('Euromedica123'),
            'area_id' => $rhArea->id,
            'rol' => 'empleado',
            'activo' => true,
            'departamento' => 'Recursos Humanos',
            'permisos' => 'rh,nomina',
            'jefe_id' => $edwin->id,
        ]);

        // 8. Fernanda (Practicante RH, bajo Edwin)
        User::create([
            'name' => 'Fernanda',
            'email' => null,
            'password' => Hash::make('Euromedica12'),
            'password_plain' => Crypt::encryptString('Euromedica12'),
            'area_id' => $rhArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'Recursos Humanos',
            'permisos' => 'rh,nomina',
            'jefe_id' => $edwin->id,
        ]);

        // 9. Jesus (Practicante RH, bajo Edwin)
        User::create([
            'name' => 'Jesus',
            'email' => null,
            'password' => Hash::make('Euromedica21'),
            'password_plain' => Crypt::encryptString('Euromedica21'),
            'area_id' => $rhArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'Recursos Humanos',
            'permisos' => 'rh,nomina',
            'jefe_id' => $edwin->id,
        ]);

        // 10. Arturo (Jefe ADE)
        $arturo = User::create([
            'name' => 'Arturo',
            'email' => null,
            'password' => Hash::make('medicaeuro'),
            'password_plain' => Crypt::encryptString('medicaeuro'),
            'area_id' => null,
            'rol' => 'jefe',
            'activo' => true,
            'departamento' => 'ADE',
            'permisos' => 'proveedores,compras',
        ]);

        // 11. Luisa (Practicante ADE, bajo Arturo)
        User::create([
            'name' => 'Luisa',
            'email' => null,
            'password' => Hash::make('EuRoMeDiCa'),
            'password_plain' => Crypt::encryptString('EuRoMeDiCa'),
            'area_id' => $adeArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'ADE',
            'permisos' => 'proveedores,compras',
            'jefe_id' => $arturo->id,
        ]);

        // 12. Leobardo (Practicante ADE, bajo Arturo)
        User::create([
            'name' => 'Leobardo',
            'email' => null,
            'password' => Hash::make('EuMedica'),
            'password_plain' => Crypt::encryptString('EuMedica'),
            'area_id' => $adeArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'ADE',
            'permisos' => 'proveedores,compras',
            'jefe_id' => $arturo->id,
        ]);

        // 13. Lorena (Jefe Nomina)
        $lorena = User::create([
            'name' => 'Lorena',
            'email' => null,
            'password' => Hash::make('MedicaEuro'),
            'password_plain' => Crypt::encryptString('MedicaEuro'),
            'area_id' => null,
            'rol' => 'jefe',
            'activo' => true,
            'departamento' => 'Nómina',
            'permisos' => 'nomina',
        ]);

        // 14. Maria Fernanda (Practicante Nomina, bajo Lorena)
        User::create([
            'name' => 'Maria Fernanda',
            'email' => null,
            'password' => Hash::make('MediEuro'),
            'password_plain' => Crypt::encryptString('MediEuro'),
            'area_id' => $nominaArea->id,
            'rol' => 'practicante',
            'activo' => true,
            'departamento' => 'Nómina',
            'permisos' => 'nomina',
            'jefe_id' => $lorena->id,
        ]);
    }
}
