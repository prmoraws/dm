<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use App\Models\User;
use App\Models\Universal\Bloco;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{
    // Propriedades para os cards
    public $totalUsers;
    public $newUsersThisMonth;
    public $totalTeams;
    public $activeSessions;
    public $totalBlocos;

    // Propriedades para os gráficos
    public $chartUsersByTeam;
    public $chartUsersByBloco;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Métricas rápidas (Cards)
        $this->totalUsers = User::count();
        $this->newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $this->totalTeams = Team::count();
        $this->totalBlocos = Bloco::count();

        // Sessões Ativas (usuários online recentemente)
        try {
            if (Schema::hasTable('sessions')) {
                $this->activeSessions = DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->where('last_activity', '>', now()->subMinutes(15)->getTimestamp())
                    ->count();
            } else {
                $this->activeSessions = 0;
            }
        } catch (\Exception $e) {
            $this->activeSessions = 0;
        }

        // Gráfico 1: Distribuição de Usuários por Time
        $usersByTeam = Team::withCount('users')->orderBy('users_count', 'desc')->get();
        $this->chartUsersByTeam = [
            'labels' => $usersByTeam->pluck('name')->toArray(),
            'data' => $usersByTeam->pluck('users_count')->toArray(),
        ];

        // Gráfico 2: Distribuição de Usuários por Bloco (Estratégico)
        $usersByBloco = Bloco::withCount('users')->orderBy('users_count', 'desc')->get();
        $this->chartUsersByBloco = [
            'labels' => $usersByBloco->pluck('nome')->toArray(),
            'data' => $usersByBloco->pluck('users_count')->toArray(),
        ];
    }

    public function redirectTo($routeName)
    {
        return redirect()->route($routeName);
    }

    public function render()
    {
        // Envia os 5 últimos usuários cadastrados para a view para auditoria rápida
        $recentUsers = User::with(['currentTeam', 'bloco'])->latest()->take(5)->get();

        return view('livewire.adm.dashboard', [
            'recentUsers' => $recentUsers
        ]);
    }
}
