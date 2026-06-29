<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTeamAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$teamPrefixes
     */
    public function handle(Request $request, Closure $next, ...$teamPrefixes): Response
    {
        $user = Auth::user();

        // Se o usuário não estiver logado ou não tiver um time, redireciona para o login
        if (!$user || !$user->currentTeam) {
            return redirect('login');
        }

        $currentTeamName = strtolower($user->currentTeam->name);

        // O time 'Adm' tem acesso a tudo.
        if ($currentTeamName === 'adm') {
            return $next($request);
        }

        // Verifica se o time do usuário corresponde a algum dos prefixos da rota
        foreach ($teamPrefixes as $prefix) {
            if ($currentTeamName === strtolower($prefix)) {
                return $next($request);
            }
        }

        // Se não tiver permissão, redireciona para a página anterior com uma mensagem de erro.
        session()->flash('unauthorized_access', 'Você não tem permissão para acessar esta área.');
        return redirect()->back();
    }
}
