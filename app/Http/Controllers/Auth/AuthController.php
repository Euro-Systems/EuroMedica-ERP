<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }
        return view('auth.login');
    }

    /**
     * Procesa la solicitud de login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string', // Acepta tanto correo como nombre de usuario (ej. Admin, Kevin)
            'password' => 'required|string',
        ]);

        $loginValue = $request->input('email'); // Sigue viniendo del input con name="email"
        
        $credentials = [
            'name' => $loginValue,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            // Regenerar la sesión para prevenir secuestros de sesión
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'El usuario o contraseña ingresados no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Procesa el logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

