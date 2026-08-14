<?php

namespace App\Http\Controllers\ActividadesDiarias;

use App\Http\Controllers\Controller;

use App\Models\Rutina;
use App\Models\EjecucionRutina;
use App\Http\Requests\StoreRutinaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutinasController extends Controller
{
    public function store(StoreRutinaRequest $request)
    {
        $data = $request->validated();
        $data['prioridad'] = $data['prioridad'] ?? 'media';
        $data['impacto'] = $data['impacto'] ?? 'Ninguno';
        $data['frecuencia'] = $data['frecuencia'] ?? 'diaria';
        $data['permitir_registro_avance'] = $request->has('permitir_registro_avance') ? 1 : 0;
        
        $empleadosAsig = $request->input('empleados_asig_checkboxes');
        if (is_array($empleadosAsig) && count($empleadosAsig) > 0) {
            foreach ($empleadosAsig as $empId) {
                $rutinaData = $data;
                $rutinaData['empleado_id'] = $empId;
                Rutina::create($rutinaData);
            }
        } else {
            Rutina::create($data);
        }

        // If it is shared, also create for other employees
        if (($data['_rutina_compartida'] ?? null) === 'si' && is_array($data['rutina_compartidos'] ?? null)) {
            foreach ($data['rutina_compartidos'] as $compartidoId) {
                if ($compartidoId != $data['empleado_id']) {
                    $sharedData = $data;
                    $sharedData['empleado_id'] = $compartidoId;
                    Rutina::create($sharedData);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Rutina creada con éxito.']);
        }

        return redirect()->back()->with('success', 'Rutina creada con éxito.');
    }

    public function ejecutar($id)
    {
        $rutina = Rutina::findOrFail($id);
        $hoy = now()->toDateString();
        $horaActual = now()->format('H:i');

        $ejecucion = EjecucionRutina::firstOrCreate(
            ['rutina_id' => $rutina->id, 'fecha' => $hoy],
            ['cantidad_ejecuciones' => 0, 'horas_registro' => []]
        );

        $horas = $ejecucion->horas_registro ?? [];
        $horas[] = $horaActual;

        $ejecucion->cantidad_ejecuciones += 1;
        $ejecucion->horas_registro = $horas;
        $ejecucion->save();

        return response()->json([
            'success' => true,
            'cantidad_ejecuciones' => $ejecucion->cantidad_ejecuciones,
            'ultima_hora' => $horaActual
        ]);
    }

    public function update(Request $request, $id)
    {
        $rutina = Rutina::findOrFail($id);

        if ($request->has('cantidad_ejecuciones_rutina') || $request->has('comentario_avance')) {
            return $this->setEjecuciones($request, $id);
        }
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'veces_al_dia' => 'nullable|integer|min:1',
            'empleado_id' => 'nullable',
        ]);

        $updateData = [
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'veces_al_dia' => max(1, intval($request->input('veces_al_dia', $rutina->veces_al_dia ?? 1))),
            'acciones_realizadas' => $request->input('acciones_realizadas'),
            'dependencia_area' => $request->input('dependencia_area'),
            'dependencia_responsable' => $request->input('dependencia_responsable'),
            'dependencia_motivo' => $request->input('dependencia_motivo'),
        ];

        if ($request->has('tiene_prioridad')) {
            if ($request->input('tiene_prioridad') === 'si') {
                $updateData['prioridad'] = $request->input('prioridad') ?: 'media';
            } else {
                $updateData['prioridad'] = null;
            }
        }

        if ($request->filled('empleado_id') && \App\Models\User::where('id', $request->input('empleado_id'))->exists()) {
            $updateData['empleado_id'] = $request->input('empleado_id');
        }

        $rutina->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Rutina actualizada con éxito.']);
        }

        return redirect()->back()->with('success', 'Rutina actualizada con éxito.');
    }

    public function setEjecuciones(Request $request, $id)
    {
        $rutina = Rutina::findOrFail($id);
        $cantidad = (int) ($request->input('cantidad_ejecuciones_rutina') ?? $request->input('cantidad') ?? $request->input('cantidad_ejecuciones', 0));
        $hoy = now()->toDateString();
        $horaActual = now()->format('H:i');
        $comentario = $request->input('comentario_avance');
        $fileHtml = $this->processUploadedFiles($request);
        if ($fileHtml !== '') {
            $comentario = ($comentario ?? '') . $fileHtml;
        }

        $ejecucion = EjecucionRutina::firstOrCreate(
            ['rutina_id' => $rutina->id, 'fecha' => $hoy],
            ['cantidad_ejecuciones' => 0, 'horas_registro' => []]
        );

        $horas = is_array($ejecucion->horas_registro) ? $ejecucion->horas_registro : [];

        if ($cantidad > count($horas)) {
            while (count($horas) < $cantidad) {
                $idx = count($horas) + 1;
                $notaVal = $comentario ? $comentario : ("Ejecución " . $idx . " de " . $rutina->veces_al_dia . " realizada");
                $horas[] = [
                    'hora' => $horaActual,
                    'hora_inicio' => $request->has('sin_horario') ? null : $request->input('hora_inicio'),
                    'hora_fin' => $request->has('sin_horario') ? null : $request->input('hora_fin'),
                    'nota' => $notaVal
                ];
            }
        } elseif ($cantidad < count($horas)) {
            $horas = array_slice($horas, 0, $cantidad);
        } elseif ($cantidad > 0 && $comentario) {
            $lastIndex = count($horas) - 1;
            if (isset($horas[$lastIndex])) {
                if (is_array($horas[$lastIndex])) {
                    $horas[$lastIndex]['nota'] = $comentario;
                    $horas[$lastIndex]['hora'] = $horaActual;
                    $horas[$lastIndex]['hora_inicio'] = $request->has('sin_horario') ? null : $request->input('hora_inicio');
                    $horas[$lastIndex]['hora_fin'] = $request->has('sin_horario') ? null : $request->input('hora_fin');
                } else {
                    $horas[$lastIndex] = [
                        'hora' => $horaActual, 
                        'hora_inicio' => $request->has('sin_horario') ? null : $request->input('hora_inicio'),
                        'hora_fin' => $request->has('sin_horario') ? null : $request->input('hora_fin'),
                        'nota' => $comentario
                    ];
                }
            }
        }

        $ejecucion->update([
            'cantidad_ejecuciones' => $cantidad,
            'horas_registro' => $horas
        ]);

        $porcentajeCalculado = $rutina->veces_al_dia > 0 ? round(($ejecucion->cantidad_ejecuciones / $rutina->veces_al_dia) * 100) : 100;
        $rutina->update([
            'porcentaje_avance' => $porcentajeCalculado
        ]);

        return response()->json([
            'success' => true,
            'cantidad_ejecuciones' => $ejecucion->cantidad_ejecuciones,
            'horas_registro' => $horas,
            'porcentaje' => $porcentajeCalculado
        ]);
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return back()->with('error', 'Debes iniciar sesión.');
        }

        $rutina = Rutina::findOrFail($id);

        if (in_array($currentUser->rol, ['admin', 'jefe', 'directivo']) || $rutina->empleado_id === $currentUser->id || $currentUser->hasPermission('actividades')) {
            $rutina->delete();
            return back()->with('success', 'Rutina eliminada.');
        }

        return back()->with('error', 'No tienes permiso para eliminar esta rutina.');
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
        $currentUser = Auth::user();
        if (!$currentUser || !in_array($currentUser->rol, ['jefe', 'admin', 'directivo'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para devolver esta rutina.'], 403);
        }

        $rutina = Rutina::findOrFail($id);
        $ejecucionAjustada = max(0, intval($request->input('cantidad_ejecuciones', 0)));
        $comentarioJefe = trim($request->input('comentario_jefe') ?? '');

        if (empty($comentarioJefe)) {
            return response()->json(['success' => false, 'message' => 'Por favor indica qué faltó por completar o las observaciones.'], 422);
        }

        $hoy = now()->toDateString();
        $ejecucion = EjecucionRutina::firstOrCreate(
            ['rutina_id' => $rutina->id, 'fecha' => $hoy],
            ['cantidad_ejecuciones' => 0, 'horas_registro' => []]
        );

        $horas = is_array($ejecucion->horas_registro) ? $ejecucion->horas_registro : [];
        $horas = array_slice($horas, 0, $ejecucionAjustada);

        $horaDevolucion = $request->input('hora_devolucion', now()->format('H:i'));

        $horas[] = [
            'hora_inicio' => $horaDevolucion,
            'hora_fin' => $horaDevolucion,
            'hora' => $horaDevolucion,
            'nota' => "↩️ [Devuelta por el Jefe]: " . $comentarioJefe
        ];

        $ejecucion->update([
            'cantidad_ejecuciones' => count($horas),
            'horas_registro' => $horas
        ]);

        return response()->json(['success' => true, 'message' => 'Rutina devuelta al empleado con las observaciones registradas.']);
    }
}

