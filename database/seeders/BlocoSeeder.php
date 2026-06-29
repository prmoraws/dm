<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlocoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocos = [
            [
                "nome" => "Alagoinhas"
            ],
            [
                "nome" => "Barreiras"
            ],
            [
                "nome" => "Camaçari"
            ],
            [
                "nome" => "Caminho de Areia"
            ],
            [
                "nome" => "Cajazeiras X"
            ],
            [
                "nome" => "Candeias"
            ],
            [
                "nome" => "Vitória da Conquista"
            ],
            [
                "nome" => "Dois Leões"
            ],
            [
                "nome" => "Fazenda Grande"
            ],
            [
                "nome" => "Feira de Santana"
            ],
            [
                "nome" => "Feira de Santana II"
            ],
            [
                "nome" => "Itabuna"
            ],
            [
                "nome" => "Itapuã"
            ],
            [
                "nome" => "Juazeiro"
            ],
            [
                "nome" => "Pernambues"
            ],
            [
                "nome" => "Santo Antônio de Jesus"
            ],
            [
                "nome" => "Simões Filho"
            ],
            [
                "nome" => "Tancredo Neves"
            ],
            [
                "nome" => "Teixeira de Freitas"
            ],
            [
                "nome" => "Vilas do Atlântico"
            ],
            [
                "nome" => "Catedral"
            ],


        ];
        DB::table('blocos')->insert($blocos);
    }
}
