<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\TarefaController;

Route::post('/login', [AuthController::class, 'login']);

// Route::middleware(['auth:api'])->group(function () {
// Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/usuario', [AuthController::class, 'me']);
    
    Route::get('/projetos', [ProjetoController::class, 'index']);
    Route::post('/projetos', [ProjetoController::class, 'store']);
    Route::get('/projetos/{id_projeto}', [ProjetoController::class, 'show']);
    Route::put('/projetos/{id_projeto}', [ProjetoController::class, 'update']);
    Route::delete('/projetos/{id_projeto}', [ProjetoController::class, 'destroy']);

    Route::get('/tarefas', [TarefaController::class, 'index']);
    Route::post('/tarefas', [TarefaController::class, 'store']);
    Route::put('/tarefas/{id_tarefa}', [TarefaController::class, 'update']);
    Route::delete('/tarefas/{id_tarefa}', [TarefaController::class, 'destroy']);
// });
