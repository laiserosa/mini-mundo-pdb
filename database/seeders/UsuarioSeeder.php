<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'cpf' => '12345678901',
                'nome' => 'Usuário 1',
                'senha' => Hash::make('senha123'),
                'token' => Str::random(60),
                'role' => 'usuario',
                'status' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cpf' => '23456789012',
                'nome' => 'Usuário 2',
                'senha' => Hash::make('senha123'),
                'token' => Str::random(60),
                'role' => 'admin',
                'status' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cpf' => '34567890123',
                'nome' => 'Usuário 3',
                'senha' => Hash::make('senha123'),
                'token' => Str::random(60),
                'role' => 'usuario',
                'status' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
