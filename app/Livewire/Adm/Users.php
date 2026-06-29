<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $user_id, $name, $email, $team_id;
    public $isOpen = false;
    public $searchTerm = '';
    public $allTeams = [];

    public function mount()
    {
        $this->allTeams = Team::all();
    }

    public function render()
    {
        $query = User::with('currentTeam')
            // CORREÇÃO: A linha abaixo que excluía seu usuário foi removida.
            // ->where('id', '!=', Auth::id()) 
            ->when($this->searchTerm, function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });

        return view('livewire.adm.users', [
            'users' => $query->latest()->paginate(15)
        ]);
    }

    private function resetInputFields()
    {
        $this->reset(['user_id', 'name', 'email', 'team_id']);
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function manageUser(User $user)
    {
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->team_id = $user->current_team_id;
        $this->isOpen = true;
    }

    public function updateUserTeam()
    {
        $this->validate(['team_id' => 'required|exists:teams,id']);
        $userToUpdate = User::find($this->user_id);
        $teamToAssign = Team::find($this->team_id);

        if ($userToUpdate && $teamToAssign) {
            if (!$userToUpdate->belongsToTeam($teamToAssign)) {
                $userToUpdate->teams()->attach($teamToAssign, ['role' => 'editor']);
            }
            $userToUpdate->switchTeam($teamToAssign);
            session()->flash('message', 'Time do usuário atualizado com sucesso!');
        } else {
            session()->flash('error', 'Usuário ou Time não encontrado.');
        }
        $this->closeModal();
    }
}
