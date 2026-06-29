<?php

namespace App\Livewire\Adm;

use App\Models\Team;
use App\Models\User;
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

    // Propriedades para os gráficos
    public $chartUsersByTeam;
    public $chartNewUsersDaily;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Cards
        $this->totalUsers = User::count();
        $this->newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $this->totalTeams = Team::count();

        try {
            if (Schema::hasTable('sessions')) {
                $this->activeSessions = DB::table('sessions')
                    ->where('user_id', '!=', null)
                    ->where('last_activity', '>', now()->subMinutes(5)->getTimestamp())
                    ->count();
            } else {
                $this->activeSessions = 0; // Tabela de sessões não existe
            }
        } catch (\Exception $e) {
            $this->activeSessions = 0; // Trata outros erros de banco de dados
        }

        // Gráfico 1: Usuários por Time
        $usersByTeam = Team::withCount('users')->orderBy('users_count', 'desc')->get();
        $this->chartUsersByTeam = [
            'labels' => $usersByTeam->pluck('name')->toArray(),
            'data' => $usersByTeam->pluck('users_count')->toArray(),
        ];

        // Gráfico 2: Novos Usuários por Dia (últimos 30 dias)
        $newUsers = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $this->chartNewUsersDaily = [
            'labels' => $newUsers->map(fn($item) => Carbon::parse($item->date)->format('d/m'))->toArray(),
            'data' => $newUsers->pluck('count')->toArray(),
        ];
    }

    public function redirectTo($routeName)
    {
        return redirect()->route($routeName);
    }

    public function render()
    {
        return view('livewire.adm.dashboard');
    }
}
