<?php
namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RhEmpleado;
use App\Models\RhPracticante;
use App\Models\RhCandidato;
use App\Models\RhCita;
use App\Models\RhVacacion;
use App\Models\RhVacacionAnual;
use App\Models\RhPracticantePermiso;

class RecursosHumanosController extends Controller
{
    public function index()
    {
        $empleados = RhEmpleado::all();
        $practicantes = RhPracticante::all();
        $candidatos = RhCandidato::all();
        $citas = RhCita::all();
        $vacaciones = RhVacacion::all();
        $vacacionesAnuales = RhVacacionAnual::all();
        $practicantesPermisos = RhPracticantePermiso::all();
        // Fallback for Contratos if Model exists
        $contratos = class_exists(\App\Models\RhContrato::class) ? \App\Models\RhContrato::all() : collect([]);

        return view('administracion.recursos_humanos.index', compact('empleados', 'practicantes', 'candidatos', 'citas', 'vacaciones', 'vacacionesAnuales', 'contratos', 'practicantesPermisos'));
    }

    public function sync(Request $request)
    {
        try {
            // 1. EMPLEADOS
            $empleados = collect($request->input('empleados', []));
            $empIds = $empleados->pluck('id')->filter();
            RhEmpleado::whereNotIn('id', $empIds)->delete();
            $colsEmp = \Illuminate\Support\Facades\Schema::getColumnListing('rh_empleados');
            $savedEmpleados = [];
            foreach ($empleados as $item) {
                $item = array_intersect_key($item, array_flip($colsEmp));
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    $rec = RhEmpleado::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    $rec = RhEmpleado::create($item);
                }
                $savedEmpleados[] = $rec;
            }

            // 2. PRACTICANTES
            $practicantes = collect($request->input('practicantes', []));
            $pracIds = $practicantes->pluck('id')->filter();
            RhPracticante::whereNotIn('id', $pracIds)->delete();
            $colsPrac = \Illuminate\Support\Facades\Schema::getColumnListing('rh_practicantes');
            $savedPracticantes = [];
            foreach ($practicantes as $item) {
                $item = array_intersect_key($item, array_flip($colsPrac));
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    $rec = RhPracticante::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    $rec = RhPracticante::create($item);
                }
                $savedPracticantes[] = $rec;
            }

            // 3. CANDIDATOS
            $candidatos = collect($request->input('candidatos', []));
            $candIds = $candidatos->pluck('id')->filter();
            RhCandidato::whereNotIn('id', $candIds)->delete();
            $colsCand = \Illuminate\Support\Facades\Schema::getColumnListing('rh_candidatos');
            $savedCandidatos = [];
            foreach ($candidatos as $item) {
                $item = array_intersect_key($item, array_flip($colsCand));
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    $rec = RhCandidato::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    $rec = RhCandidato::create($item);
                }
                $savedCandidatos[] = $rec;
            }

            // 4. CITAS
            $citas = collect($request->input('citas', []));
            $citaIds = $citas->pluck('id')->filter();
            RhCita::whereNotIn('id', $citaIds)->delete();
            $colsCita = \Illuminate\Support\Facades\Schema::getColumnListing('rh_citas');
            $savedCitas = [];
            foreach ($citas as $item) {
                $item = array_intersect_key($item, array_flip($colsCita));
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    $rec = RhCita::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    $rec = RhCita::create($item);
                }
                $savedCitas[] = $rec;
            }

            // 5. VACACIONES
            $vacaciones = collect($request->input('vacaciones', []));
            $savedVacaciones = [];
            if(class_exists(\App\Models\RhVacacion::class)){
                $vacIds = $vacaciones->pluck('id')->filter();
                RhVacacion::whereNotIn('id', $vacIds)->delete();
                $colsVac = \Illuminate\Support\Facades\Schema::getColumnListing('rh_vacaciones');
                foreach ($vacaciones as $item) {
                    $item = array_intersect_key($item, array_flip($colsVac));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        $rec = RhVacacion::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        $rec = RhVacacion::create($item);
                    }
                    $savedVacaciones[] = $rec;
                }
            }

            // 6. VACACIONES ANUALES
            $vacacionesAnuales = collect($request->input('vacacionesAnuales', []));
            $savedVacacionesAnuales = [];
            if(class_exists(\App\Models\RhVacacionAnual::class)){
                $vaIds = $vacacionesAnuales->pluck('id')->filter();
                RhVacacionAnual::whereNotIn('id', $vaIds)->delete();
                $colsVa = \Illuminate\Support\Facades\Schema::getColumnListing('rh_vacaciones_anuales');
                foreach ($vacacionesAnuales as $item) {
                    $item = array_intersect_key($item, array_flip($colsVa));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        $rec = RhVacacionAnual::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        $rec = RhVacacionAnual::create($item);
                    }
                    $savedVacacionesAnuales[] = $rec;
                }
            }

            // 7. CONTRATOS
            $contratos = collect($request->input('contratos', []));
            $savedContratos = [];
            if(class_exists(\App\Models\RhContrato::class)){
                $contrIds = $contratos->pluck('id')->filter();
                \App\Models\RhContrato::whereNotIn('id', $contrIds)->delete();
                $colsContratos = \Illuminate\Support\Facades\Schema::getColumnListing('rh_contratos');
                foreach ($contratos as $item) {
                    $item = array_intersect_key($item, array_flip($colsContratos));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        $rec = \App\Models\RhContrato::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        $rec = \App\Models\RhContrato::create($item);
                    }
                    $savedContratos[] = $rec;
                }
            }

            // 8. PERMISOS PRACTICANTES
            $practicantesPermisos = collect($request->input('practicantesPermisos', []));
            $savedPracticantesPermisos = [];
            if(class_exists(\App\Models\RhPracticantePermiso::class)){
                $vpIds = $practicantesPermisos->pluck('id')->filter();
                RhPracticantePermiso::whereNotIn('id', $vpIds)->delete();
                $colsVp = \Illuminate\Support\Facades\Schema::getColumnListing('rh_practicante_permisos');
                foreach ($practicantesPermisos as $item) {
                    $item = array_intersect_key($item, array_flip($colsVp));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        $rec = RhPracticantePermiso::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        $rec = RhPracticantePermiso::create($item);
                    }
                    $savedPracticantesPermisos[] = $rec;
                }
            }

            return response()->json([
                'success' => true,
                'empleados' => $savedEmpleados,
                'practicantes' => $savedPracticantes,
                'candidatos' => $savedCandidatos,
                'citas' => $savedCitas,
                'vacaciones' => $savedVacaciones,
                'vacacionesAnuales' => $savedVacacionesAnuales,
                'contratos' => $savedContratos,
                'practicantesPermisos' => $savedPracticantesPermisos
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in DB Sync: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
}
