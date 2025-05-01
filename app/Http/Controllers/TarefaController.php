<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TarefaController extends Controller
{
    private function verificarPermissao($role)
    {
        if ($role !== 'admin') {
            return response()->json(['message' => 'Apenas administradores tem permissão.'], 403);
        }

        return null;
    }

    public function inicio()
    {
        return view('tarefas');
    }

    public function index(Request $request)
    {
        try {
            $query = Tarefa::with(['projeto', 'predecessora']);

            if ($request->descricao) {
                $query->where('descricao', 'like', '%' . $request->descricao . '%');
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->id_projeto) {
                $query->where('id_projeto', $request->id_projeto);
            }

            return response()->json($query->get());
        } catch (\Exception $e) {
            Log::error('Erro ao listar tarefas: ' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível carregar as tarefas.'], 500);
        }
    }

    public function store(Request $request)
    {
        $this->verificarPermissao($request->role);

        try {
            $validated = $request->validate([
                'descricao' => 'required|string|max:255',
                'id_projeto' => 'required|exists:projetos,id',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date|after_or_equal:data_inicio',
                'id_tarefa_predecessora' => 'nullable|exists:tarefas,id',
                'status' => 'required|in:concluida,nao_concluida',
            ]);

            $tarefa = Tarefa::create($validated);

            return response()->json($tarefa, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar tarefa: ' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível criar a tarefa.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $tarefa = Tarefa::findOrFail($id);
            $data_inicio = $tarefa->data_inicio ? \Carbon\Carbon::parse($tarefa->data_inicio) : null;
            $data_fim = $tarefa->data_fim ? \Carbon\Carbon::parse($tarefa->data_fim) : null;

            return response()->json([
                'id' => $tarefa->id,
                'descricao' => $tarefa->descricao,
                'id_projeto' => $tarefa->id_projeto,
                'data_inicio' => $data_inicio ? $data_inicio->format('Y-m-d') : null,
                'data_fim' => $data_fim ? $data_fim->format('Y-m-d') : null,
                'data_inicio_formatada' => $data_inicio ? $data_inicio->format('d/m/Y') : null,
                'data_fim_formatada' => $data_fim ? $data_fim->format('d/m/Y') : null,
                'id_tarefa_predecessora' => $tarefa->id_tarefa_predecessora,
                'status' => $tarefa->status
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao mostrar tarefa ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['erro' => 'Tarefa não encontrada'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $this->verificarPermissao($request->role);

        try {
            $tarefa = Tarefa::findOrFail($id);

            $validated = $request->validate([
                'descricao' => 'required|string|max:255',
                'id_projeto' => 'required|exists:projetos,id',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date|after_or_equal:data_inicio',
                'id_tarefa_predecessora' => 'nullable|exists:tarefas,id|not_in:' . $id,
                'status' => 'required|in:concluida,nao_concluida',
            ]);

            $tarefa->update($validated);

            return response()->json($tarefa);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar tarefa ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível atualizar a tarefa.'], 500);
        }
    }

    public function destroy($id)
    {
        $this->verificarPermissao($request->role);

        try {
            $tarefa = Tarefa::findOrFail($id);

            if ($tarefa->sucessoras()->count() > 0) {
                return response()->json(['error' => 'Não é possível excluir uma tarefa que é predecessora de outra(s).'], 400);
            }

            $tarefa->delete();

            return response()->json(['message' => 'Tarefa excluída com sucesso.']);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir tarefa ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível excluir a tarefa.'], 500);
        }
    }

    public function predecessoras()
    {
        try {
            $tarefas = Tarefa::select('id', 'descricao')->orderBy('descricao')->get();
            return response()->json($tarefas);
        } catch (\Exception $e) {
            Log::error('Erro ao listar predecessoras: ' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível carregar as predecessoras.'], 500);
        }
    }
}
