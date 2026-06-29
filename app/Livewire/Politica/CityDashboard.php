<?php

namespace App\Livewire\Politica;

use App\Models\Politica\Cidade;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class CityDashboard extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $cidades = Cidade::query()
            ->when($this->search, fn($query) => $query->where('nome', 'like', '%' . $this->search . '%'))
            ->orderBy('nome')
            ->paginate(15); // Define 15 itens por página

        return view('livewire.politica.city-dashboard', [
            'cidades' => $cidades,
        ]);
    }
}