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
        // Usando == para flexibilizar a comparação entre string e integer
        return $user->bloco_id == $pessoa->bloco_id;
    }

    public function update(User $user, Pessoa $pessoa)
    {
        return $user->bloco_id == $pessoa->bloco_id;
    }

    public function delete(User $user, Pessoa $pessoa)
    {
        return $user->bloco_id == $pessoa->bloco_id;
    }
}
