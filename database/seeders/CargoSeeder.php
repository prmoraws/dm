<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            [
                "nome" => "Lider"
            ],
            [
                "nome" => "Vice-líder"
            ],
            [
                "nome" => "Coordenador"
            ],
            [
                "nome" => "Secretária"
            ],
            [
                "nome" => "Mídia"
            ],
            [
                "nome" => "Instrutor de Curso"
            ],
            [
                "nome" => "Integrante"
            ],
            [
                "nome" => "Apoiador"
            ],
        ];
        DB::table('cargos')->insert($cargos);
    }
}
