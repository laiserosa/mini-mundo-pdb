<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    public function index()
    {
        $projetos = Projeto::all();
        return response()->json($projetos);
    }

    public function store(Request $request)
    {
        $projeto = Projeto::create($request->all());
        return response()->json($projeto, 201); // 201: Created
    }

    public function show($id)
    {
        $projeto = Projeto::find($id);

        if (!$projeto) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }

        return response()->json($projeto);
    }

    public function update(Request $request, $id)
    {
        $projeto = Projeto::find($id);

        if (!$projeto) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }

        $projeto->update($request->all());
        return response()->json($projeto);
    }

    public function destroy($id)
    {
        $projeto = Projeto::find($id);

        if (!$projeto) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }

        $projeto->delete();
        return response()->json(['message' => 'Projeto deletado com sucesso']);
    }
}
