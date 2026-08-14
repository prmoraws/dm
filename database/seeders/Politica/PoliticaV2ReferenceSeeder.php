<?php

namespace Database\Seeders\Politica;

use App\Models\Politica\V2\Cargo;
use Illuminate\Database\Seeder;

class PoliticaV2ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            ['tse_codigo' => '0001', 'nome' => 'Presidente', 'esfera' => 'federal', 'abrangencia' => 'nacional', 'ordem' => 10],
            ['tse_codigo' => '0003', 'nome' => 'Governador', 'esfera' => 'estadual', 'abrangencia' => 'uf', 'ordem' => 20],
            ['tse_codigo' => '0005', 'nome' => 'Senador', 'esfera' => 'federal', 'abrangencia' => 'uf', 'ordem' => 30],
            ['tse_codigo' => '0006', 'nome' => 'Deputado Federal', 'esfera' => 'federal', 'abrangencia' => 'uf', 'ordem' => 40],
            ['tse_codigo' => '0007', 'nome' => 'Deputado Estadual', 'esfera' => 'estadual', 'abrangencia' => 'uf', 'ordem' => 50],
            ['tse_codigo' => '0011', 'nome' => 'Prefeito', 'esfera' => 'municipal', 'abrangencia' => 'municipio', 'ordem' => 60],
            ['tse_codigo' => '0013', 'nome' => 'Vereador', 'esfera' => 'municipal', 'abrangencia' => 'municipio', 'ordem' => 70],
        ];

        foreach ($cargos as $cargo) {
            Cargo::query()->updateOrCreate(
                ['tse_codigo' => $cargo['tse_codigo']],
                $cargo + ['ativo' => true]
            );
        }
    }
}
