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
        if (empty($request->input('titulo'))) {
            $tit = $request->input('titulo_imp_sencilla') ?: ($request->input('titulo_imp_avanzada') ?: ($request->input('motivo') ?: 'Actividad Personal'));
            $request->merge(['titulo' => $tit]);
        }
        if (empty($request->input('motivo'))) {
            $request->merge(['motivo' => $request->input('titulo')]);
        }
        if (empty($request->input('descripcion_detallada'))) {
            $desc = $request->input('descripcion_imp_realizada') ?: ($request->input('descripcion_imp_sencilla') ?: ($request->input('descripcion_imp_avanzada') ?: ($request->input('descripcion') ?: 'Sin descripción')));
            $request->merge(['descripcion_detallada' => $desc, 'descripcion' => $desc]);
        }
        if (empty($request->input('resultado_obtenido'))) {
            $request->merge(['resultado_obtenido' => 'Atendido y resuelto']);
        }
        if ($request->input('tiene_plazo') === 'no') {
            $request->merge(['estado' => 'pendiente']);
        } elseif (empty($request->input('estado'))) {
            $request->merge(['estado' => 'finalizada']);
        }

        $request->validate([
            'titulo'                => 'required|string|max:255',
            'descripcion_detallada' => 'required',
            'motivo'                => 'nullable',
            'horas_invertidas'      => 'nullable|numeric',
            'impacto'               => 'nullable',
            'resultado_obtenido'    => 'nullable',
            'estado'                => 'nullable|in:pendiente,en_proceso,en_pausa,finalizada,atrasada',
            'permitir_registro_avance' => 'nullable',
            'dirigido_a_id'      => 'nullable',
            'hora_inicio'        => 'nullable|string',
            'hora_fin'           => 'nullable|string',
            'acciones_realizadas'=> 'nullable|string',
            'dependencia_area'   => 'nullable|string',
            'dependencia_responsable' => 'nullable|string',
            'dependencia_motivo' => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'comentarios_dirigido' => 'nullable|string',
        ]);

        $this->checkDefaults();

        $data = $request->all();

        if ($request->input('tiene_plazo') === 'no') {
            $data['hora_inicio'] = null;
            $data['hora_fin'] = null;
            $data['horas_invertidas'] = 0;
            $data['estado'] = 'pendiente';
        }

        $currentUser = Auth::user();
        
        if ($request->filled('empleado_id')) {
            if ($request->empleado_id === 'self') {
                $data['empleado_id'] = Auth::id() ?? 1;
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

        $imprevisto = ActividadImprevista::create($data);

        $colabs = $request->input('colaboradores') ?: $request->input('empleados_compartidos');
        if (($request->input('_colaboro_imp_radio') === 'si' || $request->input('_colaboro') === 'si') && is_array($colabs)) {
            $creadorUser = \App\Models\User::find($data['empleado_id']);
            $nombreCreador = $creadorUser ? $creadorUser->name : 'un compañero';

            foreach ($colabs as $colabId) {
                if ($colabId != $data['empleado_id']) {
                    $colabData = $data;
                    $colabData['empleado_id'] = $colabId;
                    $colabData['titulo'] = '[Colaboración] ' . $data['titulo'];
                    $colabData['motivo'] = 'Colaboró en la actividad "' . $data['titulo'] . '" con ' . $nombreCreador;
                    $colabData['descripcion_detallada'] = 'Colaboró en la actividad "' . $data['titulo'] . '" con ' . $nombreCreador . '. ' . ($data['descripcion_detallada'] ?? '');

                    $colabEmpleado = \App\Models\User::find($colabId);
                    if ($colabEmpleado) {
                        $colabData['area_id'] = $colabEmpleado->area_id ?? $data['area_id'];
                    }
                    ActividadImprevista::create($colabData);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Actividad personal registrada con éxito.']);
        }

        return redirect()->back()->with('success', 'Actividad personal registrada con éxito.');
    }

    public function show(string $id)
    {
        $imprevisto = ActividadImprevista::with('empleado')->findOrFail($id);
        return view('actividades_diarias.actividades_diarias.actividades_imprevistas.show', compact('imprevisto'));
    }

    public function update(Request $request, $id)
    {
        $imprevisto = ActividadImprevista::findOrFail($id);

        $allowedKeys = [
            'titulo',
            'descripcion_detallada',
            'motivo',
            'resultado_obtenido',
            'horas_invertidas',
            'empleado_id',
            'estado',
            'porcentaje_avance',
            'hora_inicio',
            'hora_fin',
            'acciones_realizadas',
            'dependencia_area',
            'dependencia_responsable',
            'dependencia_motivo',
            'observaciones',
            'dirigido_a_id'
        ];

        $inputData = $request->only($allowedKeys);
        $data = [];

        foreach ($inputData as $key => $val) {
            if (!is_null($val) && $val !== '') {
                $data[$key] = $val;
            }
        }

        if ($request->has('empleado_id') && Auth::user() && in_array(Auth::user()->rol, ['jefe', 'admin'])) {
            $data['empleado_id'] = $request->empleado_id;
        }

        if ($request->has('porcentaje_avance')) {
            $porcentaje = max(0, min(100, intval($request->input('porcentaje_avance'))));
            $data['porcentaje_avance'] = $porcentaje;
            if ($porcentaje >= 100) {
                $data['estado'] = 'finalizada';
            } elseif ($porcentaje > 0) {
                $data['estado'] = 'en_proceso';
            } else {
                $data['estado'] = 'pendiente';
            }
        }

        $fileHtml = $this->processUploadedFiles($request);
        if ($request->filled('comentario_avance') || $fileHtml !== '') {
            $notaAvance = '[' . now()->format('H:i') . ' hrs] ' . ($request->input('comentario_avance') ?? '') . $fileHtml;
            if (!empty($imprevisto->resultado_obtenido) && $imprevisto->resultado_obtenido !== 'Atendido y resuelto') {
                $data['resultado_obtenido'] = $imprevisto->resultado_obtenido . "\n" . $notaAvance;
            } else {
                $data['resultado_obtenido'] = $notaAvance;
            }
        }

        if (empty($data['resultado_obtenido']) && empty($imprevisto->resultado_obtenido)) {
            $data['resultado_obtenido'] = 'Atendido y resuelto';
        }
        if (empty($data['motivo']) && empty($imprevisto->motivo)) {
            $data['motivo'] = 'Actividad personal';
        }
        if (empty($data['descripcion_detallada']) && empty($imprevisto->descripcion_detallada)) {
            $data['descripcion_detallada'] = 'Actividad personal';
        }

        $imprevisto->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Actividad personal actualizada con éxito.');
    }

    public function destroy($id)
    {
        ActividadImprevista::destroy($id);
        return back()->with('success', 'Actividad personal eliminada.');
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

    private function processUploadedFiles(Request $request) {
        $fileHtml = '';
        if ($request->hasFile('archivos_avance')) {
            $files = $request->file('archivos_avance');
            if (!is_array($files)) {
                $files = [$files];
            }

            $uploadPath = public_path('uploads/avances');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($uploadPath, $filename);

                    $fileUrl = asset('uploads/avances/' . $filename);
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

                    if (in_array($extension, $imageExtensions)) {
                        $fileHtml .= "\n" . '<div style="margin-top: 8px;"><a href="' . $fileUrl . '" target="_blank" style="text-decoration:none;"><img src="' . $fileUrl . '" style="max-width: 260px; max-height: 180px; border-radius: 8px; border: 2px solid #cbd5e1; object-fit: cover; display: block; margin-top: 4px;" alt="Evidencia"></a><span style="font-size:11px; color:#64748b; font-weight:600;">📷 ' . e($originalName) . '</span></div>';
                    } else {
                        $fileHtml .= "\n" . '<div style="margin-top: 8px;"><a href="' . $fileUrl . '" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #0f172a; border: 1.5px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"><i class="bi bi-file-earmark-arrow-down-fill" style="color:#2563eb;"></i> Documento: ' . e($originalName) . '</a></div>';
                    }
                }
            }
        }
        return $fileHtml;
    }

    public function devolver(Request $request, $id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin', 'directivo'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para devolver este imprevisto.'], 403);
        }

        $imprevisto = ActividadImprevista::findOrFail($id);
        $porcentajeAjustado = max(0, min(99, intval($request->input('porcentaje_avance', 50))));
        $comentarioJefe = trim($request->input('comentario_jefe') ?? '');

        if (empty($comentarioJefe)) {
            return response()->json(['success' => false, 'message' => 'Por favor indica qué faltó por completar o las instrucciones de corrección.'], 422);
        }

        $estadoNuevo = $porcentajeAjustado > 0 ? 'en_proceso' : 'pendiente';
        $notaFinal = "\n↩️ [Devuelta por el Jefe - Ajustada al {$porcentajeAjustado}%]: " . $comentarioJefe;

        $imprevisto->update([
            'porcentaje_avance' => $porcentajeAjustado,
            'estado' => $estadoNuevo,
            'resultado_obtenido' => ($imprevisto->resultado_obtenido ?? '') . $notaFinal
        ]);

        return response()->json(['success' => true, 'message' => 'Actividad personal devuelta al empleado con las observaciones registradas.']);
    }
}

