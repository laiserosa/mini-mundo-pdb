<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projeto extends Model
{
    use HasFactory;

    protected $table = 'projetos';

    protected $fillable = [
        'nome',
        'descricao',
        'status',
        'orcamento',
    ];

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class, 'id_projeto');
    }
}
