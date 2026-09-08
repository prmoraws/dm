<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CursoUnpAcessoTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioNoTime(string $nome): User
    {
        $usuario = User::factory()->create();
        $time = Team::query()->create([
            'user_id' => $usuario->id,
            'name' => $nome,
            'personal_team' => false,
        ]);
        $usuario->forceFill(['current_team_id' => $time->id])->save();

        return $usuario->fresh();
    }

    #[Test]
    public function wizard_de_captacao_permanece_publico(): void
    {
        $this->get(route('curso-unp.inscricao'))->assertOk();
    }

    #[Test]
    public function usuario_unp_acessa_todas_as_paginas_internas(): void
    {
        $usuario = $this->usuarioNoTime('Unp');

        foreach (['curso-unp.dashboard', 'curso-unp.captacoes', 'curso-unp.turmas', 'curso-unp.acompanhamento'] as $rota) {
            $this->actingAs($usuario)->get(route($rota))->assertOk();
        }
    }

    #[Test]
    public function administrador_acessa_todas_as_paginas_internas(): void
    {
        $usuario = $this->usuarioNoTime('Adm');

        foreach (['curso-unp.dashboard', 'curso-unp.captacoes', 'curso-unp.turmas', 'curso-unp.acompanhamento'] as $rota) {
            $this->actingAs($usuario)->get(route($rota))->assertOk();
        }
    }

    #[Test]
    public function outro_time_nao_acessa_paginas_internas(): void
    {
        $usuario = $this->usuarioNoTime('Eventos');

        $this->actingAs($usuario)
            ->from('/dashboard')
            ->get(route('curso-unp.dashboard'))
            ->assertRedirect('/dashboard')
            ->assertSessionHas('unauthorized_access');
    }
}
