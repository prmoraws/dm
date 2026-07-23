<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Teams extends Component
{
    use WithPagination;

    public $team_id, $name, $owner_id;
    public $isOpen = false;
    public $confirmDeleteId = null;
    public $searchTerm = '';

    // Lista de usuários para atribuir como Dono do time
    public $allUsers = [];

    public function mount()
    {
        // Carrega os usuários apenas com id e name para otimizar a memória
        $this->allUsers = User::select('id', 'name')->orderBy('name')->get();
    }

    public function render()
    {
        $query = Team::query()->with(['owner', 'users'])
            ->when($this->searchTerm, function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest();

        return view('livewire.adm.teams', [
            'teams' => $query->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        // O criador logado é o dono padrão na criação
        $this->owner_id = Auth::id();
        $this->isOpen = true;
    }

    public function edit(Team $team)
    {
        $this->team_id = $team->id;
        $this->name = $team->name;
        $this->owner_id = $team->user_id; // Puxa o Dono atual do banco
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $this->team_id,
            'owner_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'O nome do time é obrigatório.',
            'name.unique' => 'Já existe um time com este nome.',
            'owner_id.required' => 'É necessário designar um dono para o time.',
        ]);

        DB::beginTransaction();

        try {
            // 1. Atualiza ou cria os atributos na tabela `teams`
            $team = Team::updateOrCreate(['id' => $this->team_id], [
                'name' => $this->name,
                'user_id' => $this->owner_id, // Define quem tem o nível de acesso máximo (Owner)
                'personal_team' => false,
            ]);

            // 2. Garante que o Dono também exista na tabela de permissões (`team_user`) com a role de admin
            $owner = User::find($this->owner_id);
            if ($owner && !$owner->belongsToTeam($team)) {
                // Se ele não fazia parte do time, nós o anexamos
                $owner->teams()->attach($team, ['role' => 'admin']);
            }

            DB::commit();
            session()->flash('message', $this->team_id ? 'Time atualizado com sucesso!' : 'Time criado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Ocorreu um erro ao salvar o time: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $team = Team::find($this->confirmDeleteId);

            if ($team) {
                // Trava de Segurança do Sistema
                if (strtolower(trim($team->name)) === 'adm' || strtolower(trim($team->name)) === 'admin' || strtolower(trim($team->name)) === 'secretaria') {
                    session()->flash('error', 'Por segurança, não é permitido excluir times vitais do sistema.');
                    $this->confirmDeleteId = null;
                    return;
                }

                // O Jetstream cuida de limpar a tabela team_user automaticamente se os relacionamentos no Model estiverem corretos, 
                // mas caso seja deletado, usuários com current_team_id deste time precisarão ser reatribuídos.
                $team->delete();
                session()->flash('message', 'Time deletado com sucesso!');
            }
            $this->confirmDeleteId = null;
        }
    }

    private function resetInputFields()
    {
        $this->reset(['team_id', 'name', 'owner_id']);
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
}
