<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $tda = DB::table('teams')
                ->whereRaw('LOWER(name) = ?', ['tda'])
                ->first();

            if ($tda) {
                return;
            }

            $adm = DB::table('teams')
                ->whereRaw('LOWER(name) IN (?, ?)', ['adm', 'admin'])
                ->orderByRaw("CASE WHEN LOWER(name) = 'adm' THEN 0 ELSE 1 END")
                ->first();

            $ownerId = $adm?->user_id;

            if (!$ownerId) {
                $ownerId = DB::table('team_user')
                    ->join('teams', 'teams.id', '=', 'team_user.team_id')
                    ->whereRaw('LOWER(teams.name) IN (?, ?)', ['adm', 'admin'])
                    ->where('team_user.role', 'admin')
                    ->value('team_user.user_id');
            }

            if (!$ownerId) {
                $ownerId = DB::table('users')->orderBy('id')->value('id');
            }

            // Em bancos vazios de teste não há usuário que possa ser o dono.
            // Nesse cenário a migration permanece válida e não cria vínculo órfão.
            if (!$ownerId) {
                return;
            }

            $agora = now();
            $teamId = DB::table('teams')->insertGetId([
                'user_id' => $ownerId,
                'name' => 'TDA',
                'personal_team' => false,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);

            DB::table('team_user')->insertOrIgnore([
                'team_id' => $teamId,
                'user_id' => $ownerId,
                'role' => 'admin',
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);
        });
    }

    public function down(): void
    {
        // Não removemos automaticamente o time, pois após o deploy ele pode ter
        // usuários vinculados. A reversão destrutiva deve ser uma decisão manual.
    }
};
