<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use App\Models\User;
use App\Policies\CadastroTdaPolicy;
use App\Policies\CaptacaoTdaPolicy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TdaTeamAccessTest extends TestCase
{
    #[Test]
    public function time_tda_recebe_acesso_total_nas_policies_do_modulo(): void
    {
        $user = new User();
        $user->setRelation('currentTeam', new Team(['name' => 'TDA']));

        $cadastroPolicy = new CadastroTdaPolicy();
        $captacaoPolicy = new CaptacaoTdaPolicy();

        $this->assertTrue($cadastroPolicy->before($user, 'view'));
        $this->assertTrue($cadastroPolicy->before($user, 'update'));
        $this->assertTrue($cadastroPolicy->before($user, 'delete'));
        $this->assertTrue($captacaoPolicy->before($user, 'view'));
        $this->assertTrue($captacaoPolicy->before($user, 'review'));
        $this->assertTrue($captacaoPolicy->before($user, 'delete'));
    }
}
