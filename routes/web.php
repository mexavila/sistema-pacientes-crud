<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\PacienteWebController;

// Autenticación Web


    Route::get('/', [WebAuthController::class, 'showLoginForm']);
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);



// Área Protegida
Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    
    // Rutas de pacientes con paginación
    Route::get('/pacientes', [PacienteWebController::class, 'index'])
        ->name('pacientes.index')
        ->defaults('page', 1);
        
    Route::get('/pacientes/page/{page}', [PacienteWebController::class, 'index'])
        ->where('page', '[0-9]+');

    Route::get('/pacientes/crear', [PacienteWebController::class, 'create'])->name('pacientes.create');
    Route::get('/pacientes/{id}/editar', [PacienteWebController::class, 'edit'])->name('pacientes.edit');
    Route::put('/pacientes/{id}', [PacienteWebController::class, 'update'])->name('pacientes.update');
    Route::delete('/pacientes/{id}', [PacienteWebController::class, 'destroy'])->name('pacientes.destroy');
});

Route::get('/check-auth', function() {
    return [
        'isAuthenticated' => auth()->check(),
        'user' => auth()->user(),
        'session' => session()->all()
    ];
});