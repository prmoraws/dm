<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = [
            [
                "nome" => "Abraão",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Davi",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Debora",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Ester",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Fogo Abrasador",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Gênesis",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Gideão",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Isaque",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Israel",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
            [
                "nome" => "Josué",
                "descricao" => "Sub-grupo UNP Catedral"
            ],
        ];
        DB::table('grupos')->insert($grupos);
    }
}
