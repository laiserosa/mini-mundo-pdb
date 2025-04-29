<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function index()
    {
        $tarefas = Tarefa::all();
        return response()->json($tarefas);
    }

    public function store(Request $request)
    {
        $tarefa = Tarefa::create($request->all());
        return response()->json($tarefa, 201); // 201: Created
    }

    public function show($id)
    {
        $tarefa = Tarefa::find($id);

        if (!$tarefa) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        return response()->json($tarefa);
    }

    public function update(Request $request, $id)
    {
        $tarefa = Tarefa::find($id);

        if (!$tarefa) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        $tarefa->update($request->all());
        return response()->json($tarefa);
    }

    public function destroy($id)
    {
        $tarefa = Tarefa::find($id);

        if (!$tarefa) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        $tarefa->delete();
        return response()->json(['message' => 'Tarefa deletada com sucesso']);
    }
}
