<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GestionUsuarios\UsersController;
use App\Http\Controllers\ParqueVehicular\VehiculosController;
use App\Http\Controllers\Administracion\AdministracionController;
use App\Http\Controllers\Administracion\RecursosHumanosController;
use App\Http\Controllers\Administracion\NominaController;
use App\Http\Controllers\Administracion\ComprasController;
use App\Http\Controllers\Proveedores\ProveedoresController;
use App\Http\Controllers\ActividadesDiarias\ActividadesController;
use App\Http\Controllers\ActividadesDiarias\AvancesActividadController;
use App\Http\Controllers\ActividadesDiarias\ActividadesImprevistasController;
use App\Http\Controllers\ActividadesDiarias\BitacoraDiariaController;
use App\Http\Controllers\ActividadesDiarias\MensajeActividadController;
use App\Http\Controllers\ActividadesDiarias\RutinasController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Ruta de Inicio (Dashboard principal)
    Route::get('/', function () {
        return view('inicio');
    })->name('inicio');

    // Módulo de Vehículos
    Route::middleware(['permission:vehiculos'])->group(function () {
        Route::resource('vehiculos', VehiculosController::class);
        Route::post('vehiculos/{id}/servicios', [VehiculosController::class, 'storeServicio'])->name('vehiculos.servicios.store');
        Route::put('vehiculos/servicios/{id}', [VehiculosController::class, 'updateServicio'])->name('vehiculos.servicios.update');
        Route::delete('vehiculos/servicios/{id}', [VehiculosController::class, 'destroyServicio'])->name('vehiculos.servicios.destroy');
    });

    // Módulo de Administración
    Route::middleware(['permission:administracion'])->group(function () {
        Route::resource('administracion', AdministracionController::class);
    });

    // Módulo de Recursos Humanos (RH)
    Route::middleware(['permission:rh'])->group(function () {
        Route::post('rh/sync', [RecursosHumanosController::class, 'sync'])->name('rh.sync');
        Route::resource('rh', RecursosHumanosController::class);
       
    });

    // Módulo de Nómina
    Route::middleware(['permission:nomina'])->group(function () {
        Route::resource('nomina', NominaController::class);
    });

    // Módulo de Compras
    Route::middleware(['permission:compras'])->group(function () {
        Route::get('compras', function () {
            return view('administracion.compras.menu');
        })->name('compras.index');

        Route::prefix('compras')->group(function () {
            // Rutas para cada tarjeta 
            Route::get('/medicamentos', [ComprasController::class, 'index'])->name('medicamentos.index');
            Route::get('/instalaciones', function () { return view('administracion.compras.instalaciones'); })->name('compras.instalaciones');
            Route::get('/administrativos', function () { return view('administracion.compras.administrativos'); })->name('compras.administrativos');

            // Rutas de acciones (POST)
            Route::post('/lotes/store', [ComprasController::class, 'storeLote'])->name('medicamentos.storeLote');
            Route::post('/laboratorio/guardar', [ComprasController::class, 'storeLaboratorio'])->name('laboratorio.store');
    
            // Rutas de eliminación
            Route::post('/lotes/eliminar/{index}', [ComprasController::class, 'eliminarLote'])->name('lotes.eliminar');
            Route::post('/medicamentos/eliminar/{index}', [ComprasController::class, 'eliminarMedicamento'])->name('medicamentos.eliminar');
        });
    });

    // Módulo de Proveedores
    Route::middleware(['permission:proveedores'])->group(function () {
        Route::resource('proveedores', ProveedoresController::class);
    });

    // Módulos de Actividades y Bitácoras
    Route::middleware(['permission:actividades'])->group(function () {
        Route::get('mis-actividades', [ActividadesController::class, 'misActividades'])->name('actividades.mias');
        Route::post('mis-actividades/comida', [ActividadesController::class, 'registrarComida'])->name('actividades.registrarComida');
        

        // Rutas de Aprobación de Avances
        Route::post('avances-actividad/{id}/aprobar', [AvancesActividadController::class, 'aprobar'])->name('avances.aprobar');
        Route::post('avances-actividad/{id}/rechazar', [AvancesActividadController::class, 'rechazar'])->name('avances.rechazar');

        // Rutas de Conversación
        Route::post('actividades/{actividad}/mensajes', [MensajeActividadController::class, 'store'])->name('mensajes.store');

        // Detalles dinámicos AJAX
        Route::get('actividades/{id}/details', [ActividadesController::class, 'details'])->name('actividades.details');

        // Exportar PDF completo
        Route::get('actividades/{id}/pdf', [ActividadesController::class, 'exportPdf'])->name('actividades.pdf');

        // Rutinas
        Route::resource('rutinas', RutinasController::class);
        Route::post('rutinas/{id}/ejecutar', [RutinasController::class, 'ejecutar'])->name('rutinas.ejecutar');
        Route::post('rutinas/{id}/set-ejecuciones', [RutinasController::class, 'setEjecuciones'])->name('rutinas.set_ejecuciones');

        Route::get('actividades/area/select/{id}', [ActividadesController::class, 'selectArea'])->name('actividades.area.select');
        Route::put('actividades/{id}/estado', [ActividadesController::class, 'actualizarEstado'])->name('actividades.estado');
        Route::get('actividades/area/{id}', [ActividadesController::class, 'areaWorkspace'])->name('actividades.area.workspace');
        Route::get('actividades-resumen', [ActividadesController::class, 'resumen'])->name('actividades.resumen');
        Route::post('actividades/{id}/aprobar', [ActividadesController::class, 'aprobarRapido'])->name('actividades.aprobar');
        Route::post('actividades-imprevistas/{id}/aprobar', [ActividadesImprevistasController::class, 'aprobarRapido'])->name('actividades-imprevistas.aprobar');
        Route::post('actividades/{id}/reabrir', [ActividadesController::class, 'reabrirRapido'])->name('actividades.reabrir');
        Route::post('actividades-imprevistas/{id}/reabrir', [ActividadesImprevistasController::class, 'reabrirRapido'])->name('actividades-imprevistas.reabrir');
        Route::resource('actividades', ActividadesController::class);
        Route::resource('avances-actividad', AvancesActividadController::class);
        Route::resource('actividades-imprevistas', ActividadesImprevistasController::class);
        Route::get('bitacora-diaria/{empleado}', [BitacoraDiariaController::class, 'usuarioFechas'])->name('bitacora.usuario');
        Route::get('bitacora-diaria', [BitacoraDiariaController::class, 'index'])->name('bitacora.index');
        Route::get('bitacora-diaria/{empleado}/{fecha}/pdf', [BitacoraDiariaController::class, 'exportPdf'])->name('bitacora.pdf');
        Route::get('bitacora-diaria/{empleado}/{fecha}', [BitacoraDiariaController::class, 'show'])->name('bitacora.show');
    });

    // Módulo de Gestión de Usuarios (Sólo Administrador)
    Route::middleware(['permission:users'])->group(function () {
        Route::resource('users', UsersController::class);
    });



});