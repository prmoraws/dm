<?php

namespace App\Livewire\Politica;

use App\Models\Politica\Cidade;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mapa Interativo da Bahia')]
class InteractiveMap extends Component
{
    public $citiesJson;

public function mount()
    {
        $cities = Cidade::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'nome', 'latitude', 'longitude', 'populacao']);

        // --- CORREÇÃO: Converte explicitamente as coordenadas para o tipo numérico (float) ---
        $citiesData = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'nome' => $city->nome,
                'latitude' => (float) $city->latitude,
                'longitude' => (float) $city->longitude,
                'populacao' => $city->populacao,
            ];
        });

        // Converte a coleção já tratada para JSON
        $this->citiesJson = $citiesData->toJson();
    }

    public function render()
    {
        return view('livewire.politica.interactive-map');
    }
}