<?php

return [
    'apuracao' => [
        // O intervalo final deve ser ajustado conforme a orientação técnica do TSE para o pleito.
        'poll_seconds' => (int) env('POLITICA_TSE_POLL_SECONDS', 10),
        'historico_checkpoint_seconds' => (int) env('POLITICA_APURACAO_CHECKPOINT_SECONDS', 60),
        'snapshot_ttl_seconds' => (int) env('POLITICA_SNAPSHOT_TTL_SECONDS', 15),
        'max_requests_per_cycle' => (int) env('POLITICA_TSE_MAX_REQUESTS_PER_CYCLE', 50),
        'snapshot_disk' => env('POLITICA_SNAPSHOT_DISK', 'local'),
        'snapshot_path' => env('POLITICA_SNAPSHOT_PATH', 'politica/apuracao'),
    ],


    'migracao_v1' => [
        'chunk' => (int) env('POLITICA_MIGRACAO_V1_CHUNK', 1000),
        'prioritarios_legacy' => [
            4623 => 'rogeria-santos',
            4624 => 'marcio-marinho',
            4625 => 'jurailton-santos',
            4626 => 'jose-de-arimateia',
        ],
        'cargos' => [
            'PRESIDENTE' => 'Presidente',
            'GOVERNADOR' => 'Governador',
            'SENADOR' => 'Senador',
            'DEPUTADO FEDERAL' => 'Deputado Federal',
            'DEPUTADO ESTADUAL' => 'Deputado Estadual',
            'PREFEITO' => 'Prefeito',
            'VEREADOR' => 'Vereador',
        ],
    ],

    'prioritarios' => [
        [
            'nome_completo' => 'Rogéria Santos',
            'nome_publico' => 'Rogéria Santos',
            'grupo' => 'federal_ba',
            'prioridade' => 1,
            'ordem' => 10,
        ],
        [
            'nome_completo' => 'Márcio Marinho',
            'nome_publico' => 'Márcio Marinho',
            'grupo' => 'federal_ba',
            'prioridade' => 1,
            'ordem' => 20,
        ],
        [
            'nome_completo' => 'Jurailton Santos',
            'nome_publico' => 'Jurailton Santos',
            'grupo' => 'estadual_ba',
            'prioridade' => 1,
            'ordem' => 30,
        ],
        [
            'nome_completo' => 'José de Arimatéia',
            'nome_publico' => 'José de Arimatéia',
            'grupo' => 'estadual_ba',
            'prioridade' => 1,
            'ordem' => 40,
        ],
        [
            'nome_completo' => 'Luiz Inácio Lula da Silva',
            'nome_publico' => 'Lula',
            'grupo' => 'presidencia',
            'prioridade' => 1,
            'ordem' => 50,
        ],
        [
            'nome_completo' => 'Flávio Bolsonaro',
            'nome_publico' => 'Flávio Bolsonaro',
            'grupo' => 'presidencia',
            'prioridade' => 1,
            'ordem' => 60,
        ],
        [
            'nome_completo' => 'Jerônimo Rodrigues',
            'nome_publico' => 'Jerônimo Rodrigues',
            'grupo' => 'governo_ba',
            'prioridade' => 1,
            'ordem' => 70,
        ],
        [
            'nome_completo' => 'ACM Neto',
            'nome_publico' => 'ACM Neto',
            'grupo' => 'governo_ba',
            'prioridade' => 1,
            'ordem' => 80,
        ],
    ],
];
