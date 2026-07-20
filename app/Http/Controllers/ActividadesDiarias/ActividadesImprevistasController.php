<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ActividadImprevista;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActividadesImprevistasController extends Controller
{
    private function checkDefaults() {
        if (session('defaults_checked_v2')) {
            return;
        }
        if (!\App\Models\Area::where('id', 1)->exists()) {
            \App\Models\Area::forceCreate(['id' => 1, 'nombre' => 'Área General']);
        }
        if (!User::where('id', 1)->exists()) {
            User::forceCreate([
                'id' => 1,
                'name' => 'Usuario de Pruebas',
                'email' => 'admin@test.com',
                'password' => bcrypt('1234'),
                'area_id' => 1,
                'rol' => 'jefe',
                'activo' => true
            ]);
        }
        session(['defaults_checked_v2' => true]);
    }

    public function index()
    {
        $imprevistos = ActividadImprevista::with('empleado')->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->get();
        return view('actividades_diarias.actividades_diarias.actividades_imprevistas.index', compact('imprevistos'));
    }

    public function create()
    {
        return view('actividades_diarias.actividades_diarias.actividades_imprevistas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'                => 'required|string|max:255',
            'descripcion_detallada' => 'required',
            'motivo'                => 'required',
            'horas_invertidas'      => 'nullable|numeric',
            'impacto'               => 'nullable',
            'resultado_obtenido'    => 'required',
            'estado'                => 'required|in:pendiente,en_proceso,en_pausa,finalizada,atrasada'
        ]);

        $this->checkDefaults();

        $data = $request->all();

        // Defaults para campos eliminados del formulario
        if (empty($data['impacto'])) {
            $data['impacto'] = 'Sistemas';
        }
        if (empty($data['horas_invertidas'])) {
            $data['horas_invertidas'] = 0;
        }

        $currentUser = Auth::user();
        
        if ($request->has('empleado_id') && $currentUser && in_array($currentUser->rol, ['jefe', 'admin'])) {
            if ($request->empleado_id === 'self') {
                $data['empleado_id'] = $currentUser->id;
            } else {
                $data['empleado_id'] = $request->empleado_id;
            }
        } else {
            $data['empleado_id'] = Auth::id() ?? 1;
        }
        
        $data['fecha'] = now()->toDateString();
        
        $empleado = User::find($data['empleado_id']);
        if ($empleado) {
            $data['area_id'] = $empleado->area_id ?? session('active_area_id', 1);
        } else {
            $data['area_id'] = session('active_area_id', 1);
        }

        ActividadImprevista::create($data);

        return redirect()->back()->with('success', 'Imprevisto registrado con éxito.');
    }

    public function show(string $id)
    {
        $imprevisto = ActividadImprevista::with('empleado')->findOrFail($id);
        return view('actividades_diarias.actividades_diarias.actividades_imprevistas.show', compact('imprevisto'));
    }

    public function destroy($id)
    {
        ActividadImprevista::destroy($id);
        return back()->with('success', 'Imprevisto eliminado.');
    }

    public function aprobarRapido($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para aprobar este imprevisto.'], 403);
        }
        $imprevisto = ActividadImprevista::findOrFail($id);
        $imprevisto->update(['estado' => 'finalizada']);
        return response()->json(['success' => true]);
    }

    public function reabrirRapido($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para reabrir este imprevisto.'], 403);
        }
        $imprevisto = ActividadImprevista::findOrFail($id);
        $imprevisto->update(['estado' => 'pendiente']);
        return response()->json(['success' => true]);
    }
}

