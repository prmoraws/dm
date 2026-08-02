<?php
namespace App\Policies;
use App\Models\Universal\CadastroTda;
use App\Models\User;
class CadastroTdaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        $time = strtolower((string) $user->currentTeam?->name);

        return in_array($time, ['tda', 'adm'], true) ? true : null;
    }
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, CadastroTda $item): bool { return (int)$user->bloco_id===(int)$item->bloco_id; }
    public function update(User $user, CadastroTda $item): bool { return $this->view($user,$item); }
    public function delete(User $user, CadastroTda $item): bool { return $this->view($user,$item); }
}
