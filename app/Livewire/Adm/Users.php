<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use App\Models\User;
use App\Models\Universal\Bloco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // <-- Trait necessária para upload de arquivos

class Users extends Component
{
    use WithPagination, WithFileUploads;

    public $user_id, $name, $email, $password, $team_id, $bloco_id;
    public $photo; // <-- Nova propriedade para o upload da foto
    public $current_profile_photo_url; // <-- Para exibir a foto atual na edição
    
    public $isOpen = false;
    public $confirmDeleteId = null;
    public $searchTerm = '';
    
    public $allTeams = [];
    public $allBlocos = [];

    public function mount()
    {
        $this->allTeams = Team::all();
        $this->allBlocos = Bloco::orderBy('nome')->get();
    }

    public function render()
    {
        $query = User::with(['currentTeam', 'bloco'])
            ->when($this->searchTerm, function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest();

        return view('livewire.adm.users', [
            'users' => $query->paginate(15)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->team_id = $user->current_team_id;
        $this->bloco_id = $user->bloco_id;
        $this->current_profile_photo_url = $user->profile_photo_url; // <-- Pega a URL atual da foto
        $this->password = ''; 
        
        $this->isOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'team_id' => 'required|exists:teams,id',
            'bloco_id' => 'nullable|exists:blocos,id',
            'photo' => 'nullable|image|max:2048', // <-- Validação da foto (máx 2MB)
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|min:8'; 
        } else {
            $rules['password'] = 'nullable|min:8'; 
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'bloco_id' => empty($this->bloco_id) ? null : $this->bloco_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->user_id], $data);

        // <-- Lógica para salvar a foto
        if ($this->photo) {
            // Se o sistema usa Jetstream nativo, este método apaga a antiga e salva a nova:
            if (method_exists($user, 'updateProfilePhoto')) {
                $user->updateProfilePhoto($this->photo);
            } else {
                // Fallback seguro caso o trait não esteja no modelo
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $path = $this->photo->store('profile-photos', 'public');
                $user->update(['profile_photo_path' => $path]);
            }
        }

        $teamToAssign = Team::find($this->team_id);
        if ($teamToAssign) {
            if (!$user->belongsToTeam($teamToAssign)) {
                $user->teams()->attach($teamToAssign, ['role' => 'editor']);
            }
            $user->switchTeam($teamToAssign);
        }

        session()->flash('message', $this->user_id ? 'Usuário atualizado com sucesso!' : 'Usuário criado com sucesso!');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            if (Auth::id() == $this->confirmDeleteId) {
                session()->flash('error', 'Você não pode excluir o seu próprio usuário logado.');
                $this->confirmDeleteId = null;
                return;
            }

            $user = User::findOrFail($this->confirmDeleteId);
            
            // Apaga a foto do storage ao deletar o usuário
            if (method_exists($user, 'deleteProfilePhoto')) {
                $user->deleteProfilePhoto();
            } elseif ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $user->delete();
            
            session()->flash('message', 'Usuário excluído com sucesso!');
            $this->confirmDeleteId = null;
        }
    }

    private function resetInputFields()
    {
        $this->reset(['user_id', 'name', 'email', 'password', 'team_id', 'bloco_id', 'photo', 'current_profile_photo_url']);
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
}