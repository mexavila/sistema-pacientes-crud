<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Métodos API JWT
    public function apiLogin(Request $request) { /* ... */ }
    public function apiLogout() { /* ... */ }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Usar JWTAuth directamente en lugar de auth()
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->input('refresh_token');

            // Verifica que el token tenga el claim 'refresh' = true
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            
            if (!$payload->get('refresh')) {
                throw new \Exception('Token no es un refresh token válido');
            }

            // Obtiene el usuario asociado al token
            $user = JWTAuth::setToken($refreshToken)->authenticate();

            // Invalida el refresh token usado (opcional, pero recomendado)
            JWTAuth::invalidate($refreshToken);

            // Genera un nuevo access token
            $newToken = JWTAuth::fromUser($user);

            // Genera un nuevo refresh token
            $newRefreshToken = JWTAuth::customClaims([
                'exp' => now()->addDays(7)->timestamp,
                'refresh' => true
            ])->fromUser($user);

            return response()->json([
                'token' => $newToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'invalid_refresh_token',
                'message' => 'Refresh token inválido o expirado: ' . $e->getMessage()
            ], 401);
        }
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Usar JWTAuth directamente
            'user' => JWTAuth::user()
        ]);
    }

    // Métodos Web
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/pacientes');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas',
        ]);
    }

    public function webLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}