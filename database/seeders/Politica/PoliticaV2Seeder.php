<?php

namespace Database\Seeders\Politica;

use Illuminate\Database\Seeder;

class PoliticaV2Seeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PoliticaV2ReferenceSeeder::class,
            PoliticaV2PrioritariosSeeder::class,
        ]);
    }
}
