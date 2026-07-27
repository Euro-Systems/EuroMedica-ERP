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
        // Fallback for Contratos if Model exists
        $contratos = class_exists(\App\Models\RhContrato::class) ? \App\Models\RhContrato::all() : collect([]);

        return view('administracion.recursos_humanos.index', compact('empleados', 'practicantes', 'candidatos', 'citas', 'vacaciones', 'vacacionesAnuales', 'contratos'));
    }

    public function sync(Request $request)
    {
        try {
            file_put_contents(storage_path("logs/rh_sync_debug.json"), json_encode($request->all(), JSON_PRETTY_PRINT));
            
            // 1. EMPLEADOS
            $empleados = collect($request->input('empleados', []));
            $empIds = $empleados->pluck('id')->filter();
            RhEmpleado::whereNotIn('id', $empIds)->delete();
            $colsEmp = \Illuminate\Support\Facades\Schema::getColumnListing('rh_empleados');
            foreach ($empleados as $item) {
                $item = array_intersect_key($item, array_flip($colsEmp));
                // Laravel casts to array natively
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    RhEmpleado::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    RhEmpleado::create($item);
                }
            }

            // 2. PRACTICANTES
            $practicantes = collect($request->input('practicantes', []));
            $pracIds = $practicantes->pluck('id')->filter();
            RhPracticante::whereNotIn('id', $pracIds)->delete();
            $colsPrac = \Illuminate\Support\Facades\Schema::getColumnListing('rh_practicantes');
            foreach ($practicantes as $item) {
                $item = array_intersect_key($item, array_flip($colsPrac));
                // Laravel casts to array natively
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    RhPracticante::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    RhPracticante::create($item);
                }
            }

            // 3. CANDIDATOS
            $candidatos = collect($request->input('candidatos', []));
            $candIds = $candidatos->pluck('id')->filter();
            RhCandidato::whereNotIn('id', $candIds)->delete();
            $colsCand = \Illuminate\Support\Facades\Schema::getColumnListing('rh_candidatos');
            foreach ($candidatos as $item) {
                $item = array_intersect_key($item, array_flip($colsCand));
                // Laravel casts to array natively
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    RhCandidato::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    RhCandidato::create($item);
                }
            }

            // 4. CITAS
            $citas = collect($request->input('citas', []));
            $citaIds = $citas->pluck('id')->filter();
            RhCita::whereNotIn('id', $citaIds)->delete();
            $colsCita = \Illuminate\Support\Facades\Schema::getColumnListing('rh_citas');
            foreach ($citas as $item) {
                $item = array_intersect_key($item, array_flip($colsCita));
                // Laravel casts to array natively
                unset($item['created_at'], $item['updated_at']);
                if (!empty($item['id'])) {
                    RhCita::updateOrCreate(['id' => $item['id']], $item);
                } else {
                    RhCita::create($item);
                }
            }

            // 5. VACACIONES (empleados) // Note: this model might be rh_vacaciones
            $vacaciones = collect($request->input('vacaciones', []));
            if(class_exists(\App\Models\RhVacacion::class)){
                $vacIds = $vacaciones->pluck('id')->filter();
                RhVacacion::whereNotIn('id', $vacIds)->delete();
                $colsVac = \Illuminate\Support\Facades\Schema::getColumnListing('rh_vacaciones');
                foreach ($vacaciones as $item) {
                    $item = array_intersect_key($item, array_flip($colsVac));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        RhVacacion::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        RhVacacion::create($item);
                    }
                }
            }

            // 6. VACACIONES ANUALES
            $vacacionesAnuales = collect($request->input('vacacionesAnuales', []));
            if(class_exists(\App\Models\RhVacacionAnual::class)){
                $vaIds = $vacacionesAnuales->pluck('id')->filter();
                RhVacacionAnual::whereNotIn('id', $vaIds)->delete();
                $colsVa = \Illuminate\Support\Facades\Schema::getColumnListing('rh_vacaciones_anuales');
                foreach ($vacacionesAnuales as $item) {
                    $item = array_intersect_key($item, array_flip($colsVa));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        RhVacacionAnual::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        RhVacacionAnual::create($item);
                    }
                }
            }

            // 7. CONTRATOS (Si el modelo existe)
            $contratos = collect($request->input('contratos', []));
            if(class_exists(\App\Models\RhContrato::class)){
                $contrIds = $contratos->pluck('id')->filter();
                \App\Models\RhContrato::whereNotIn('id', $contrIds)->delete();
                $colsContratos = \Illuminate\Support\Facades\Schema::getColumnListing('rh_contratos');
                foreach ($contratos as $item) {
                    $item = array_intersect_key($item, array_flip($colsContratos));
                    unset($item['created_at'], $item['updated_at']);
                    if (!empty($item['id'])) {
                        \App\Models\RhContrato::updateOrCreate(['id' => $item['id']], $item);
                    } else {
                        \App\Models\RhContrato::create($item);
                    }
                }
            }

            return response()->json(['success' => true]);
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
