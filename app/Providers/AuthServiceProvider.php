<?php

namespace App\Providers;

use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Adicione este import

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // CORREÇÃO: Define um "Super Administrador" global
        // Este código é executado antes de qualquer outra verificação de permissão.
        Gate::before(function ($user, $ability) {
            // Verifique se o e-mail do usuário é o do administrador.
            // Você pode adicionar mais e-mails aqui se precisar de mais administradores.
            if ($user->email === 'prjmoraes@icloud.com') {
                return true; // Concede acesso total e imediato.
            }
        });
    }
}
