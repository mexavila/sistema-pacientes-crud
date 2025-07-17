<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\AuthController;

class WebAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Crear una instancia de AuthController
        $authController = new AuthController();
        
        // Llamar al método login y retornar su respuesta
        $jsonResponse = $authController->login( $request );

        $data = $jsonResponse->getData();

        if( isset( $data->access_token ) )
        {
            $cookie = cookie(
                'api_token',                    // Nombre
                $data->access_token,            // Valor (token JWT)
                $data->expires_in / 60,         // Tiempo en minutos (3600 segs = 60 mins)
                '/',                            // Ruta (accessible en toda la app)
                null,                           // Dominio (actual)
                false,                          // Secure (true en producción con HTTPS)
                true,                           // HTTP-only (bloquea acceso desde JS)
                false,                          // Raw (no codificar el valor)
                'Lax'                           // SameSite (protección CSRF)
            );

            session( ['api_token' => $data->access_token] );

            return redirect()->route('pacientes.index')->withCookie( $cookie );
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas',
        ]);
    }

    public function logout(Request $request)
    {
        // Invalidar el token JWT
        try {
            if ($token = $request->cookie('api_token')) {
                JWTAuth::setToken($token)->invalidate();
            }
        } catch (\Exception $e) {
            // Token ya expirado o inválido
        }

        Auth::logout();
        $request->session()->forget('api_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->withoutCookie('api_token');
    }
}