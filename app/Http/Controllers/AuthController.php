<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'senha' => 'required|string',
        ]);
    
        $user = Usuario::where('cpf', $request->cpf)->first();
        // dd(!$user, !Hash::check($request->senha, $user->senha));
        if (!$user || !Hash::check($request->senha, $user->senha)) {
            return response()->json(['message' => 'CPF ou senha inválidos'], 401);
        }
    
        // $token = JWTAuth::fromUser($user);
        $token = JWTAuth::claims([
            'role' => $user->role,
            'cpf' => $user->cpf,
        ])->fromUser($user);
        
        return response()->json([
            'token' => $token,
            'usuario' => $user,
        ]);



        // $credentials = $request->only('cpf', 'senha');
        // $token = JWTAuth::attempt($credentials);
        
        // try {
        //     if (!$token) {
        //         return response()->json(['error' => 'CPF ou Senha inválidos!'], 401);
        //     }

        //     $user = auth()->user();
        //     $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

        //     return response()->json(compact('token'));
        // } catch (JWTException $e) {
        //     return response()->json(['error' => 'Não foi possível criar token'], 500);
        // } catch (JWTException $e) {
        //     return response()->json(['error' => 'Erro inesperado ao tentar realizar a autenticação.'], 500);
        // }





        // $acesso = $request->only('cpf', 'senha');
        // $usuario = Usuario::where('cpf', $acesso['cpf'])->first();
        
        // if (!$usuario || !Hash::check($acesso['senha'], $usuario->senha)) {
        //     return response()->json(['message' => 'CPF ou senha inválidos'], 401);
        // }

        // $token = JWTAuth::fromUser($usuario);

        // return response()->json([
        //     'token' => $token,
        //     'usuario' => [
        //         'id' => $usuario->id,
        //         'nome' => $usuario->nome,
        //         'cpf' => $usuario->cpf
        //     ]
        // ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Falha no logout'], 500);
        }
    }

    public function me()
    {
        try {
            $usuario = JWTAuth::parseToken()->authenticate();
            return response()->json($usuario);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
    }
}
