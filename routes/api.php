<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\TarefaController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/usuario', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::get('/projetos', [ProjetoController::class, 'index']);
    Route::post('/projetos', [ProjetoController::class, 'store']);
    Route::get('/projetos/{id}', [ProjetoController::class, 'show']);
    Route::put('/projetos/{id}', [ProjetoController::class, 'update']);
    Route::delete('/projetos/{id}', [ProjetoController::class, 'destroy']);

    Route::get('/tarefas', [TarefaController::class, 'index']);
    Route::post('/tarefas', [TarefaController::class, 'store']);
    Route::get('/tarefas/{id}', [ProjetoController::class, 'show']);
    Route::put('/tarefas/{id}', [TarefaController::class, 'update']);
    Route::delete('/tarefas/{id}', [TarefaController::class, 'destroy']);
});
