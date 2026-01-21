<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.client-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|email', // Assuming 'username' in form corresponds to email
            'password' => 'required',
        ]);

        // Map 'username' to 'email' if necessary, or just use email in form
        $credentials = [
            'email' => $request->username,
            'password' => $request->password
        ];

        $remember = $request->has('remember');

        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('welcome'));
        }

        return back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showRegisterForm()
    {
        return view('auth.client-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:usuario_aplicacions',
            'password' => 'required|string|min:8|confirmed',
            'cli_ced_ruc' => 'required|string|min:10|max:13|unique:clientes,CLI_Ced_Ruc',
            'cli_nombre' => 'required|string|max:60|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/',
            'cli_telefono' => 'required|string|size:10|regex:/^[0-9]+$/',
            'cli_direccion' => 'required|string|max:150',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = \App\Models\UsuarioAplicacion::create([
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);

            \App\Models\Cliente::create([
                'CLI_Ced_Ruc' => $request->cli_ced_ruc,
                'CLI_Nombre' => $request->cli_nombre,
                'CLI_Telefono' => $request->cli_telefono,
                'CLI_Correo' => $request->email, // Sync email
                'CLI_Direccion' => $request->cli_direccion,
                'usuario_aplicacion_id' => $user->id,
            ]);

            Auth::guard('client')->login($user);
        });

        return redirect(route('welcome'));
    }
}
