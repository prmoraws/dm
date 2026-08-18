<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fila persistente do TSE: o botão do painel apenas cria a solicitação.
// Para funcionar em produção, o servidor precisa executar `php artisan schedule:run`
// a cada minuto (cron). O comando é leve quando não há trabalho pendente.
if ((bool) config('politica.tse.automation.queue_enabled', true)) {
    Schedule::command('politica:tse-processar-solicitacoes --limite=1')
        ->everyMinute()
        ->withoutOverlapping(30);
}

// Candidaturas oficiais de 2026: atualização automática diária, enfileirada.
// Resultados/apuração continuam em fluxo separado e protegido.
if ((bool) config('politica.tse.automation.automatic_enabled', true)) {
    $ano = (int) config('politica.tse.automation.year', 2026);
    $uf = strtoupper((string) config('politica.tse.automation.uf', 'BA'));
    $cron = (string) config('politica.tse.automation.cron', '17 3 * * *');
    $timezone = (string) config('politica.tse.automation.timezone', 'America/Bahia');

    Schedule::command("politica:tse-solicitar-atualizacao {$ano} --uf={$uf} --escopo=espelho --somente=candidaturas --origem=automatica")
        ->cron($cron)
        ->timezone($timezone)
        ->withoutOverlapping(10);
}

// Apuração ao vivo permanece duplamente protegida. Mesmo registrada no scheduler,
// o comando exige POLITICA_APURACAO_LIVE_ENABLED=true para consultar o TSE.
if ((bool) config('politica.apuracao.schedule_enabled', false)) {
    Schedule::command('politica:apuracao-coletar --ano=2026 --turno=1 --uf=BA')
        ->everyMinute()
        ->withoutOverlapping();
}
