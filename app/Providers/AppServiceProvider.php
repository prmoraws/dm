<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       // CORREÇÃO: Lógica do Super Administrador adicionada aqui
        Gate::before(function ($user, $ability) {
            // Verifique se o e-mail do usuário é o do administrador.
            if ($user->email === 'prjmoraes@icloud.com') {
                return true; // Concede acesso total e imediato.
            }
        });
        
    }
}
