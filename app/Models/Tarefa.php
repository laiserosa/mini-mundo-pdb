<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarefa extends Model
{
    use HasFactory;

    protected $table = 'tarefas';

    protected $fillable = [
        'descricao',
        'id_projeto',
        'data_inicio',
        'data_fim',
        'id_tarefa_predecessora',
        'status'
    ];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class, 'id_projeto');
    }

    public function predecessora()
    {
        return $this->belongsTo(Tarefa::class, 'id_tarefa_predecessora');
    }

    public function sucessoras()
    {
        return $this->hasMany(Tarefa::class, 'id_tarefa_predecessora');
    }
}
