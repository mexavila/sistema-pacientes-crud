<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});



// Proteger rutas de pacientes
Route::middleware('jwt.auth')->group(function () {
    Route::prefix('pacientes')->group(function () {
        Route::get('/', [PacienteController::class, 'index']);
        Route::post('/', [PacienteController::class, 'store']);
        Route::get('/{id}', [PacienteController::class, 'show']);
        Route::put('/{id}', [PacienteController::class, 'update']);
        Route::delete('/{id}', [PacienteController::class, 'destroy']);
        Route::post('/{id}/foto', [PacienteController::class, 'uploadPhoto']);
    });

    Route::get('/departamentos/{id}/municipios', function($id) {
        return response()->json([
            'data' => \App\Models\Municipio::where('departamento_id', $id)->get()
        ]);
    });
});

Route::middleware('jwt.auth')->group(function () {
    // Datos para formulario
    Route::get('/form-data', function() {
        return response()->json([
            'tipos_documento' => \App\Models\TipoDocumento::all(),
            'generos' => \App\Models\Genero::all(),
            'departamentos' => \App\Models\Departamento::all()
        ]);
    });
});
