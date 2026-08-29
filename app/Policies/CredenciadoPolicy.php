<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Universal\Credenciado;
use Illuminate\Auth\Access\HandlesAuthorization;

class CredenciadoPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Se for do bloco 21 (Admin), tem acesso total
        if ($user->bloco_id == 21) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->bloco_id !== null;
    }

    public function create(User $user): bool
    {
        return $user->bloco_id !== null;
    }

    public function view(User $user, Credenciado $credenciado): bool
    {
        return $user->bloco_id == $credenciado->bloco_id;
    }

    public function update(User $user, Credenciado $credenciado): bool
    {
        return $user->bloco_id == $credenciado->bloco_id;
    }

    public function delete(User $user, Credenciado $credenciado): bool
    {
        return $user->bloco_id == $credenciado->bloco_id;
    }
}
