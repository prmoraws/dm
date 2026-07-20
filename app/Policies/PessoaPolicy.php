<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Universal\Pessoa;
use Illuminate\Auth\Access\HandlesAuthorization;

class PessoaPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Se for do bloco 21, tem acesso total
        if ($user->bloco_id == 21) {
            return true;
        }
    }

    public function view(User $user, Pessoa $pessoa)
    {
        return $this->checkBlocoAccess($user, $pessoa);
    }

    public function update(User $user, Pessoa $pessoa)
    {
        return $this->checkBlocoAccess($user, $pessoa);
    }

    public function delete(User $user, Pessoa $pessoa)
    {
        return $this->checkBlocoAccess($user, $pessoa);
    }

    // Método auxiliar para não repetir código
    private function checkBlocoAccess(User $user, Pessoa $pessoa)
    {
        // Se o usuário for editor, ele só acessa os registros do próprio bloco
        if ($user->hasRole('editor')) {
            return $user->bloco_id === $pessoa->bloco_id;
        }

        // Retorne true para outras roles que têm acesso liberado
        return true;
    }
}
