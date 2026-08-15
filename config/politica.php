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

    'tse' => [
        // Dados Abertos oficiais. Os arquivos são baixados uma vez, processados em CLI
        // e mantidos fora de public/ para não sobrecarregar o servidor web.
        'disk' => env('POLITICA_TSE_DISK', 'local'),
        'path' => env('POLITICA_TSE_PATH', 'politica/tse'),
        'connect_timeout' => (int) env('POLITICA_TSE_CONNECT_TIMEOUT', 10),
        'download_timeout' => (int) env('POLITICA_TSE_DOWNLOAD_TIMEOUT', 300),
        'cleanup_after_success' => (bool) env('POLITICA_TSE_CLEANUP_AFTER_SUCCESS', true),
        'scope' => [
            'partido_prioritario' => env('POLITICA_PARTIDO_PRIORITARIO', 'REPUBLICANOS'),
            'somente_partido' => [
                'Vereador',
                'Prefeito',
                'Deputado Estadual',
                'Deputado Federal',
                'Senador',
            ],
            'todos' => [
                'Governador',
                'Presidente',
            ],
        ],
        'storage' => [
            'warning_mb' => (int) env('POLITICA_TSE_STORAGE_WARNING_MB', 500),
            'hard_limit_mb' => (int) env('POLITICA_TSE_STORAGE_HARD_LIMIT_MB', 900),
            // Estimativas conservadoras já incluindo margem para índices/overhead do InnoDB.
            'candidate_row_estimate_bytes' => (int) env('POLITICA_TSE_CANDIDATE_ROW_ESTIMATE_BYTES', 4096),
            'result_row_estimate_bytes' => (int) env('POLITICA_TSE_RESULT_ROW_ESTIMATE_BYTES', 900),
        ],
        'sources' => [
            'candidaturas' => env(
                'POLITICA_TSE_CANDIDATURAS_URL',
                'https://cdn.tse.jus.br/estatistica/sead/odsele/consulta_cand/consulta_cand_%d.zip'
            ),
            'resultados' => env(
                'POLITICA_TSE_RESULTADOS_URL',
                'https://cdn.tse.jus.br/estatistica/sead/odsele/votacao_candidato_munzona/votacao_candidato_munzona_%d.zip'
            ),
        ],
        // Nomes oficiais/nomes de urna usados para vincular a fonte TSE aos oito
        // acompanhamentos já existentes, sem criar pessoas duplicadas.
        'prioritarios_aliases' => [
            'rogeria-santos' => [
                'ROGERIA DE ALMEIDA PEREIRA DOS SANTOS',
                'ROGERIA SANTOS',
            ],
            'marcio-marinho' => [
                'MARCIO CARLOS MARINHO',
                'MARCIO MARINHO',
            ],
            'jurailton-santos' => [
                'JURAILTON DE SOUSA SANTOS',
                'JURAILTON SANTOS',
            ],
            'jose-de-arimateia' => [
                'JOSE DE ARIMATEIA CORIOLANO DE PAIVA',
                'JOSE DE ARIMATEIA',
            ],
            'lula' => [
                'LUIZ INACIO LULA DA SILVA',
                'LULA',
            ],
            'flavio-bolsonaro' => [
                'FLAVIO NANTES BOLSONARO',
                'FLAVIO BOLSONARO',
            ],
            'jeronimo-rodrigues' => [
                'JERONIMO RODRIGUES SOUZA',
                'JERONIMO RODRIGUES',
            ],
            'acm-neto' => [
                'ANTONIO CARLOS PEIXOTO DE MAGALHAES NETO',
                'ACM NETO',
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
            'Prefeito',
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
