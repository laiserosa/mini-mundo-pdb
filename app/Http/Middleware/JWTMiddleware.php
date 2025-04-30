<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use App\Models\Usuario;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Não autorizado'], 401);
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $usuario = Usuario::find($payload['sub']);

            if (!$usuario) {
                Log::warning('JWTMiddleware: Usuário não encontrado.', [
                    'token_sub' => $payload['sub']
                ]);
                return response()->json(['error' => 'Não autorizado'], 401);
            }

        } catch (TokenExpiredException $e) {
            Log::error('JWTMiddleware: Token expirado.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Token expirado'], 401);

        } catch (TokenInvalidException $e) {
            Log::error('JWTMiddleware: Token inválido.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Token inválido'], 401);

        } catch (JWTException $e) {
            Log::error('JWTMiddleware: Erro JWT.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        return $next($request);
    }
}
