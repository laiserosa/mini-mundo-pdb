<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoController extends Controller
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
        return view('projetos');
    }

    public function index(Request $request)
    {
        try {
            $query = Projeto::query();

            // Filtrar por nome
            if ($request->has('nome') && $request->nome) {
                $query->where('nome', 'like', '%' . $request->nome . '%');
            }

            // Filtrar por status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filtrar por orçamento (min)
            if ($request->has('orcamento_min') && $request->orcamento_min) {
                $query->where('orcamento', '>=', $request->orcamento_min);
            }

            // Filtrar por orçamento (max)
            if ($request->has('orcamento_max') && $request->orcamento_max) {
                $query->where('orcamento', '<=', $request->orcamento_max);
            }

            // Buscar os projetos filtrados
            $projetos = $query->get();
            return response()->json($projetos);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 401);
        }
    }

    public function store(Request $request)
    {
        $this->verificarPermissao($request->role);

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:projetos,nome',
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
            'orcamento' => 'nullable|numeric',
        ]);

        try {
            $projeto = Projeto::create($validated);
            return response()->json($projeto, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado ao criar o projeto.',
                'erro' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id_projeto)
    {
        try {
            $projeto = Projeto::find($id_projeto);
    
            if (!$projeto) {
                return response()->json(['message' => 'Projeto não encontrado'], 404);
            }
    
            return response()->json($projeto);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado ao tentar recuperar o projeto.',
                'erro' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id_projeto)
    {
        $this->verificarPermissao($request->role);

        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:projetos,nome,' . $id_projeto,
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
            'orcamento' => 'nullable|numeric',
        ]);
        
        $projeto = Projeto::find($id_projeto);
    
        if (!$projeto) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }

        try {
            $projeto->update([
                'nome' => $request->input('nome'),
                'descricao' => $request->input('descricao'),
                'status' => $request->input('status'),
                'orcamento' => $request->input('orcamento'),
            ]);
            return response()->json($projeto);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado ao atualizar o projeto.',
                'erro' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id_projeto)
    {
        $this->verificarPermissao($request->role);

        $projeto = Projeto::find($id_projeto);

        if (!$projeto) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }

        try {
            $projeto->delete();
            return response()->json(['message' => 'Projeto deletado com sucesso']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Não é possível excluir o projeto porque existem tarefas vinculadas.'
                ], 409);
            }
    
            return response()->json([
                'message' => 'Erro de banco de dados ao excluir o projeto.',
                'erro' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado ao tentar excluir o projeto.',
                'erro' => $e->getMessage()
            ], 500);
        }
    }
}
