<?php

namespace App\Http\Controllers\Proveedores;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    public function index()
    {
        return view('proveedores.index');
    }
}

