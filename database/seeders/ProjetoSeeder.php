<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('projetos')->insert([
            [
                'nome' => 'Projeto 1',
                'descricao' => 'Descrição do Projeto 1',
                'status' => 'ativo',
                'orcamento' => 10000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Projeto 2',
                'descricao' => 'Descrição do Projeto 2',
                'status' => 'ativo',
                'orcamento' => 15000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Projeto 3',
                'descricao' => 'Descrição do Projeto 3',
                'status' => 'inativo',
                'orcamento' => 8000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
