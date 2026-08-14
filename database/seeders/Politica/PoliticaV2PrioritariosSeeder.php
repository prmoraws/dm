<?php

namespace Database\Seeders\Politica;

use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Politico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PoliticaV2PrioritariosSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('politica.prioritarios', []) as $item) {
            $slug = Str::slug($item['nome_publico']);

            $politico = Politico::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'nome_completo' => $item['nome_completo'],
                    'nome_publico' => $item['nome_publico'],
                    'ativo' => true,
                ]
            );

            Acompanhamento::query()->updateOrCreate(
                ['politico_id' => $politico->id],
                [
                    'grupo' => $item['grupo'],
                    'prioridade' => $item['prioridade'] ?? 1,
                    'ordem' => $item['ordem'] ?? 100,
                    'ativo' => true,
                ]
            );
        }
    }
}
