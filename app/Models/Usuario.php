<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cpf',
        'senha',
        'nome',
        'role',
        'status'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    // Usar "senha" como campo de senha
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Dizer que o campo de login é o CPF
    public function getAuthIdentifierName()
    {
        return 'cpf';
    }

    /**
     * Métodos obrigatórios do JWTSubject
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // ID do usuário
    }

    public function getJWTCustomClaims()
    {
        return []; // Você pode adicionar mais claims aqui se quiser
    }
}
