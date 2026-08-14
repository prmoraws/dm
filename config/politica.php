<?php

return [
    'data_sources' => [
        'ibge' => [
            'url' => env('POLITICA_IBGE_API_URL', 'https://servicodados.ibge.gov.br/api/v1/'),
            'malhas_url' => env('POLITICA_IBGE_MALHAS_URL', 'https://servicodados.ibge.gov.br/api/v3/malhas/municipios'),
            'uf_code' => (int) env('POLITICA_IBGE_UF_CODE', 29),
            'expected_municipalities' => (int) env('POLITICA_IBGE_EXPECTED_MUNICIPALITIES', 417),
            // Alias de grafia legado. A linha com dados/relacionamentos existentes continua sendo a canônica.
            'name_aliases' => [
                'SANTA TERESINHA' => 'SANTA TEREZINHA',
            ],
        ],
    ],

    'apuracao' => [
        // O intervalo final deve ser ajustado conforme a orientação técnica do TSE para o pleito.
        'poll_seconds' => (int) env('POLITICA_TSE_POLL_SECONDS', 10),
        'historico_checkpoint_seconds' => (int) env('POLITICA_APURACAO_CHECKPOINT_SECONDS', 60),
        'snapshot_ttl_seconds' => (int) env('POLITICA_SNAPSHOT_TTL_SECONDS', 15),
        'max_requests_per_cycle' => (int) env('POLITICA_TSE_MAX_REQUESTS_PER_CYCLE', 50),
        'snapshot_disk' => env('POLITICA_SNAPSHOT_DISK', 'local'),
        'snapshot_path' => env('POLITICA_SNAPSHOT_PATH', 'politica/apuracao'),
    ],



    'espelho' => [
        'partido_prioritario' => env('POLITICA_PARTIDO_PRIORITARIO', 'REPUBLICANOS'),
        'cargos_partido_prioritario' => [
            'Vereador',
            'Deputado Estadual',
            'Deputado Federal',
            'Senador',
        ],
        'ranking_por_pagina' => (int) env('POLITICA_ESPELHO_RANKING_POR_PAGINA', 25),
    ],

    'mapa' => [
        'max_candidaturas_seletor' => (int) env('POLITICA_MAPA_MAX_CANDIDATURAS', 150),
    ],

    'migracao_v1' => [
        'chunk' => (int) env('POLITICA_MIGRACAO_V1_CHUNK', 1000),
        'prioritarios_legacy' => [
            4623 => 'rogeria-santos',
            4624 => 'marcio-marinho',
            4625 => 'jurailton-santos',
            4626 => 'jose-de-arimateia',
        ],
        // Os quatro prioritários de 2022 vieram da V1 sem partido preenchido.
        // Mantemos a correção explícita e rastreável até a sincronização oficial do TSE substituir o legado.
        'partidos_legacy' => [
            4623 => 'REPUBLICANOS',
            4624 => 'REPUBLICANOS',
            4625 => 'REPUBLICANOS',
            4626 => 'REPUBLICANOS',
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
