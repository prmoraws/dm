<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Teams extends Component
{
    use WithPagination;

    public $team_id, $name;
    public $isOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';

    public function render()
    {
        $query = Team::query()->with('owner')
            ->when($this->searchTerm, function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            });

        return view('livewire.adm.teams', [
            'teams' => $query->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $this->team_id,
        ]);

        // Ao criar um novo time, ele é associado ao administrador logado.
        $data = array_merge($validatedData, [
            'user_id' => Auth::id(),
            'personal_team' => false
        ]);

        Team::updateOrCreate(['id' => $this->team_id], $data);

        session()->flash('message', $this->team_id ? 'Time atualizado com sucesso!' : 'Time criado com sucesso!');
        $this->closeModal();
    }

    public function edit(Team $team)
    {
        $this->team_id = $team->id;
        $this->name = $team->name;
        $this->isOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $team = Team::find($this->confirmDeleteId);

            // Previne a exclusão do time Adm
            if (strtolower($team->name) === 'adm') {
                session()->flash('error', 'Não é permitido excluir o time de Administradores.');
                $this->confirmDeleteId = null;
                return;
            }

            $team->delete();
            session()->flash('message', 'Time deletado com sucesso!');
            $this->confirmDeleteId = null;
        }
    }

    private function resetInputFields()
    {
        $this->reset(['team_id', 'name']);
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
}
