<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TarefaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        DB::table('tarefas')->insert([
            [
                'descricao' => 'Tarefa 1 do Projeto A',
                'id_projeto' => 1,
                'data_inicio' => now(),
                'data_fim' => now()->addDays(5),
                'id_tarefa_predecessora' => null,
                'status' => 'concluida'
            ],
            [
                'descricao' => 'Tarefa 2 do Projeto A',
                'id_projeto' => 1,
                'data_inicio' => now()->addDays(6),
                'data_fim' => now()->addDays(10),
                'id_tarefa_predecessora' => 1,
                'status' => 'nao_concluida'
            ],
            [
                'descricao' => 'Tarefa 1 do Projeto B',
                'id_projeto' => 2,
                'data_inicio' => now(),
                'data_fim' => now()->addDays(7),
                'id_tarefa_predecessora' => null,
                'status' => 'nao_concluida'
            ],
            [
                'descricao' => 'Tarefa 1 do Projeto C',
                'id_projeto' => 3,
                'data_inicio' => now(),
                'data_fim' => now()->addDays(5),
                'id_tarefa_predecessora' => null,
                'status' => 'concluida'
            ],
            [
                'descricao' => 'Tarefa 2 do Projeto C',
                'id_projeto' => 3,
                'data_inicio' => now()->addDays(6),
                'data_fim' => now()->addDays(12),
                'id_tarefa_predecessora' => 4,
                'status' => 'nao_concluida'
            ],
        ]);
    }
}
