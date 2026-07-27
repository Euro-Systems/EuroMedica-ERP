<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;

use App\Models\Administracion;
use Illuminate\Http\Request;

class AdministracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administracion.index');
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Administracion $administracion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Administracion $administracion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administracion $administracion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administracion $administracion)
    {
        //
    }
}

