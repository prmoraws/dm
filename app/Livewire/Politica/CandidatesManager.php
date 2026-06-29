<?php

namespace App\Livewire\Politica;

use App\Models\Politica\Candidato;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Gerenciar Candidatos')]
class CandidatesManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $candidateId = null;

    public string $nome = '';
    public string $partido = '';

    public function rules()
    {
        return [
            'nome' => 'required|string|min:3|max:255',
            'partido' => 'nullable|string|max:100',
        ];
    }

    public function create()
    {
        $this->reset(['candidateId', 'nome', 'partido']);
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Candidato::create([
            'nome' => $this->nome,
            'partido' => $this->partido,
        ]);

        session()->flash('success', 'Candidato criado com sucesso.');
        $this->closeModal();
    }

    public function edit(Candidato $candidate)
    {
        $this->candidateId = $candidate->id;
        $this->nome = $candidate->nome;
        $this->partido = $candidate->partido;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $candidate = Candidato::findOrFail($this->candidateId);
        $candidate->update([
            'nome' => $this->nome,
            'partido' => $this->partido,
        ]);

        session()->flash('success', 'Candidato atualizado com sucesso.');
        $this->closeModal();
    }

    public function delete(Candidato $candidate)
    {
        $candidate->delete();
        session()->flash('success', 'Candidato excluído com sucesso.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['candidateId', 'nome', 'partido']);
    }
    
    public function render()
    {
        $candidatos = Candidato::query()
            ->when($this->search, fn($q) => $q->where('nome', 'like', "%{$this->search}%"))
            ->orderBy('nome')
            ->paginate(10);

        return view('livewire.politica.candidates-manager', [
            'candidatos' => $candidatos
        ]);
    }
}