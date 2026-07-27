<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComprasController extends Controller
{
    // Esta función centraliza la carga de datos para la vista de gestión
    public function index()
    {
        // Obtenemos los datos de la sesión (o arreglo vacío si no existen)
        $medicamentos = session('mis_medicamentos', []);
        $inventario = array_map(fn($item) => (object)$item, $medicamentos);
        
        $laboratorios = session('mis_laboratorios', []);
        $lotes = session('mis_lotes', []); 

        // Retornamos una sola vista con toda la información necesaria
        return view('administracion.compras.medicamentos', compact('inventario', 'laboratorios', 'lotes'));
    }

    // --- LOTES ---
    public function storeLote(Request $request)
    {
        $lotes = session('mis_lotes', []);
        $lotes[] = [
            'id'        => uniqid(),
            'farmaco'   => $request->nombre,
            'cantidad'  => $request->cantidad,
            'lote'      => $request->lote,
            'caducidad' => $request->caducidad,
            'ingreso'   => date('Y-m-d')
        ];

        session(['mis_lotes' => $lotes]);
        return redirect()->route('medicamentos.index')->with('success', 'Lote registrado');
    }

    public function eliminarLote($index)
    {
        $lotes = session('mis_lotes', []);
        if (isset($lotes[$index])) {
            unset($lotes[$index]);
            session(['mis_lotes' => array_values($lotes)]);
        }
        return redirect()->route('medicamentos.index')->with('success', 'Lote eliminado');
    }

    // --- LABORATORIO ---
    public function storeLaboratorio(Request $request)
    {
        $laboratorios = session('mis_laboratorios', []);
        $laboratorios[] = [
            'nombre_lab'    => $request->nombre_lab,
            'analisis'      => $request->analisis,
            'fecha'         => $request->fecha,
            'estado'        => $request->estado,
            'observaciones' => $request->observaciones,
            'paciente'      => $request->paciente,
        ];
        
        session(['mis_laboratorios' => $laboratorios]);
        return redirect()->route('medicamentos.index')->with('success', 'Análisis registrado');
    }

    // --- ELIMINAR MEDICAMENTO ---
    public function eliminarMedicamento($index)
    {
        $medicamentos = session('mis_medicamentos', []);
        if (isset($medicamentos[$index])) {
            unset($medicamentos[$index]);
            session(['mis_medicamentos' => array_values($medicamentos)]);
        }
        return redirect()->route('medicamentos.index')->with('success', 'Medicamento eliminado');
    }
}
