<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function username()
    {
        return 'cpf';
    }

    public function logar()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('cpf', 'senha');
        $usuario = Usuario::where('cpf', $credentials['cpf'])->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        if (!Hash::check($credentials['senha'], $usuario->senha)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $token = JWTAuth::claims(['role' => $usuario->role])->fromUser($usuario);

        return response()->json([
            'token' => $token,
            // 'token_type' => 'bearer'
        ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate($request->bearerToken());

            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha no logout.'], 500);
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
