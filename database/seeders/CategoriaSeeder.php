<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importa a classe DB

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                "nome" => "Pastor",
                "descricao" => "Pastor que é responsável ou auxiliar no trabalho da UNP."
            ],
            [
                "nome" => "Missionária",
                "descricao" => "Esposa de pastor da UNP, ou pessoa consagrada à missionária pela Igreja."
            ],
            [
                "nome" => "Obreiro",
                "descricao" => "Obreiro que faz parte do grupo UNP."
            ],
            [
                "nome" => "Membro",
                "descricao" => "Membro da igreja que concluiu o curso de voluntário, e ajuda no grupo UNP."
            ],
            [
                "nome" => "Bispo",
                "descricao" => "Bispo que é responsável ou auxiliar no trabalho da UNP."
            ],
            [
                "nome" => "Nâo é da igreja.",
                "descricao" => "Pessoa que não pertence a Igreja."
            ],
        ];
        DB::table('categorias')->insert($categorias);
    }
}
