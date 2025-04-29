<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\TarefaController;

Route::post('/login', [AuthController::class, 'login']);

// Route::middleware(['jwt.auth'])->group(function () {
Route::middleware(['auth:api'])->group(function () {
    Route::get('/usuario', [AuthController::class, 'usuarioAutenticado']);
    
    Route::get('/projetos', [ProjetoController::class, 'index']);
    Route::post('/projetos', [ProjetoController::class, 'store']);
    Route::put('/projetos/{id_projeto}', [ProjetoController::class, 'update']);
    Route::delete('/projetos/{id_projeto}', [ProjetoController::class, 'destroy']);

    Route::get('/tarefas', [TarefaController::class, 'index']);
    Route::post('/tarefas', [TarefaController::class, 'store']);
    Route::put('/tarefas/{id}', [TarefaController::class, 'update']);
    Route::delete('/tarefas/{id}', [TarefaController::class, 'destroy']);
});
