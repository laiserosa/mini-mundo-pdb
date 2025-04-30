<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', [App\Http\Controllers\AuthController::class, 'logar'])->name('logar');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/projetos', [App\Http\Controllers\ProjetoController::class, 'inicio'])->name('projetos');
Route::get('/tarefas', [App\Http\Controllers\TarefaController::class, 'inicio'])->name('tarefas');
