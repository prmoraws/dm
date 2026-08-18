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
        'historico_especial' => [
            // Exceção deliberada ao recorte econômico: somente estes quatro políticos
            // terão todas as candidaturas históricas localizadas no TSE, independentemente
            // de cargo ou partido. O restante da base continua enxuto.
            'slugs' => [
                'marcio-marinho',
                'rogeria-santos',
                'jose-de-arimateia',
                'jurailton-santos',
            ],
            // Varredura dos ciclos eleitorais desde 1998. Anos sem candidatura são
            // descartados após a leitura e não geram resultados nem crescimento relevante.
            'anos' => [
                1998, 2000, 2002, 2004, 2006, 2008, 2010, 2012,
                2014, 2016, 2018, 2020, 2022, 2024, 2026,
            ],
            // Filiação partidária é independente do mandato. Os períodos abaixo
            // usam ano, não datas artificiais. Para José de Arimateia, a ALBA é a fonte
            // institucional principal e a candidatura TSE de 2004 confirma o PSL naquele pleito.
            'filiacoes' => [
                'jose-de-arimateia' => [
                    [
                        'partido' => 'PMDB', 'ano_inicio' => 1997, 'ano_fim' => 2001,
                        'periodo_texto' => '1997–2001',
                        'observacoes' => 'A biografia atual da ALBA usa a sigla MDB; o histórico eleitoral oficial de 1998 registra PMDB, sigla vigente naquele pleito.',
                        'fonte' => 'ALBA_TSE', 'fonte_id' => '915859|1998',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'partido' => 'PFL', 'ano_inicio' => 2001, 'ano_fim' => 2003,
                        'periodo_texto' => '2001–2003',
                        'observacoes' => 'Filiação partidária registrada pela Assembleia Legislativa da Bahia.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'partido' => 'PSL', 'ano_inicio' => 2004, 'ano_fim' => 2007,
                        'periodo_texto' => '2004–2007',
                        'observacoes' => 'A ALBA resume o período como 2005–2007; a candidatura oficial TSE de 2004, já importada no sistema, confirma José de Arimateia pelo PSL naquele pleito.',
                        'fonte' => 'TSE_ALBA', 'fonte_id' => 'candidatos-2004|915859',
                        'fonte_url' => 'https://dadosabertos.tse.jus.br/dataset/candidatos-2004',
                    ],
                    [
                        'partido' => 'PRB', 'ano_inicio' => 2007, 'ano_fim' => 2019,
                        'periodo_texto' => '2007–2019',
                        'observacoes' => 'Partido Republicano Brasileiro, conforme histórico partidário da ALBA.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'partido' => 'REPUBLICANOS', 'ano_inicio' => 2019, 'ano_fim' => null,
                        'periodo_texto' => '2019–atual',
                        'observacoes' => 'Continuidade partidária após a alteração de nomenclatura do PRB para Republicanos.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                ],
            ],
            'filiacoes_notas' => [
                'jose-de-arimateia' => 'A referência secundária que indica “sem partido” em 2003–2004 não foi gravada como fato oficial. A ALBA confirma PFL até 2003 e o TSE confirma candidatura pelo PSL em 2004; sem documento primário para o intervalo exato, o sistema preserva apenas os vínculos oficialmente demonstráveis.',
            ],

            // Registros antigos já gravados e posteriormente corrigidos por uma
            // fonte institucional mais específica. A remoção é exata e restrita ao
            // político/cargo/período/fonte informado; não há limpeza ampla do histórico.
            'mandatos_obsoletos' => [
                'jose-de-arimateia' => [
                    [
                        'cargo' => 'Deputado Estadual',
                        'ano_inicio' => 2007,
                        'ano_fim' => 2011,
                        'fonte' => 'ALBA',
                    ],
                ],
            ],
            // Mandatos/cargos institucionais são registrados somente quando uma fonte
            // oficial legislativa descreve expressamente o período. Não inferimos posse
            // a partir do resultado eleitoral.
            'mandatos' => [
                'marcio-marinho' => [
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'PL', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2003, 'ano_fim' => 2007,
                        'periodo_texto' => '2003–2007', 'situacao' => 'concluido',
                        'detalhes' => 'Eleito deputado estadual pelo PL.',
                        'fonte' => 'ALBA', 'fonte_id' => '907276',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/907276',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'PR', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2008, 'ano_fim' => 2011,
                        'periodo_texto' => '2008–2011', 'situacao' => 'concluido',
                        'detalhes' => 'Suplente; assumiu o mandato em outubro de 2008 e efetivou-se em dezembro de 2008.',
                        'fonte' => 'ALBA', 'fonte_id' => '907276',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/907276',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2011, 'ano_fim' => 2015,
                        'periodo_texto' => '2011–2015', 'situacao' => 'concluido',
                        'detalhes' => 'Mandato de deputado federal.',
                        'fonte' => 'ALBA', 'fonte_id' => '907276',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/907276',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2015, 'ano_fim' => 2019,
                        'periodo_texto' => '2015–2019', 'situacao' => 'concluido',
                        'detalhes' => 'Mandato de deputado federal.',
                        'fonte' => 'ALBA', 'fonte_id' => '907276',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/907276',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2019, 'ano_fim' => 2023,
                        'periodo_texto' => '2019–2023', 'situacao' => 'concluido',
                        'detalhes' => 'Mandato de deputado federal; o partido passou a adotar o nome Republicanos em 2019.',
                        'fonte' => 'ALBA', 'fonte_id' => '907276',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/907276',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2023, 'ano_fim' => 2027,
                        'periodo_texto' => '2023–2027', 'situacao' => 'ativo',
                        'detalhes' => 'Mandato de deputado federal em exercício.',
                        'fonte' => 'CAMARA_DOS_DEPUTADOS', 'fonte_id' => '150418',
                        'fonte_url' => 'https://www.camara.leg.br/deputados/150418',
                    ],
                ],
                'rogeria-santos' => [
                    [
                        'cargo' => 'Vereador', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'municipal', 'uf' => 'BA', 'ano_inicio' => 2017, 'ano_fim' => 2020,
                        'periodo_texto' => '2017–2020', 'situacao' => 'concluido',
                        'detalhes' => 'Vereadora de Salvador.',
                        'fonte' => 'CAMARA_DOS_DEPUTADOS', 'fonte_id' => '220695',
                        'fonte_url' => 'https://www.camara.leg.br/deputados/220695/biografia',
                    ],
                    [
                        'cargo' => 'Secretária Municipal', 'partido' => null, 'tipo' => 'cargo_publico',
                        'esfera' => 'municipal', 'uf' => 'BA', 'ano_inicio' => 2019, 'ano_fim' => 2020,
                        'periodo_texto' => '2019–2020', 'situacao' => 'concluido',
                        'detalhes' => 'Secretária Municipal de Políticas para Mulheres, Infância e Juventude da Prefeitura de Salvador.',
                        'fonte' => 'CAMARA_DOS_DEPUTADOS', 'fonte_id' => '220695',
                        'fonte_url' => 'https://www.camara.leg.br/deputados/220695/biografia',
                    ],
                    [
                        'cargo' => 'Deputado Federal', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'federal', 'uf' => 'BA', 'ano_inicio' => 2023, 'ano_fim' => 2027,
                        'periodo_texto' => '2023–2027', 'data_inicio' => '2023-02-01', 'situacao' => 'ativo',
                        'detalhes' => 'Mandato de deputada federal; posse em 01/02/2023.',
                        'fonte' => 'CAMARA_DOS_DEPUTADOS', 'fonte_id' => '220695',
                        'fonte_url' => 'https://www.camara.leg.br/deputados/220695/biografia',
                    ],
                ],
                'jose-de-arimateia' => [
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'PMDB', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 1999, 'ano_fim' => 2003,
                        'periodo_texto' => '1999–2003', 'situacao' => 'concluido',
                        'detalhes' => 'Eleito em 1998 pelo PMDB. A filiação partidária registrada pela ALBA passou ao PFL em 2001, durante este mandato.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'cargo' => 'Vereador', 'partido' => 'PSL', 'tipo' => 'mandato',
                        'esfera' => 'municipal', 'uf' => 'BA', 'ano_inicio' => 2005, 'ano_fim' => 2008,
                        'periodo_texto' => '2005–2008', 'situacao' => 'concluido',
                        'detalhes' => 'Vereador de Feira de Santana, eleito em 2004 pelo PSL. A filiação partidária passou ao PRB em 2007.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'cargo' => 'Vereador', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'municipal', 'uf' => 'BA', 'ano_inicio' => 2009, 'ano_fim' => 2011,
                        'periodo_texto' => '2009–2011', 'situacao' => 'concluido',
                        'detalhes' => 'Segundo mandato de vereador em Feira de Santana. Em sessão da ALBA de 02/12/2010, José de Arimateia foi registrado como vereador e deputado estadual eleito.',
                        'fonte' => 'ALBA', 'fonte_id' => 'SesEsp0212101',
                        'fonte_url' => 'https://www.al.ba.gov.br/fserver/%3AimagensAlbanet%3APDFsSessao%3ASesEsp0212101.pdf',
                    ],
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2011, 'ano_fim' => 2015,
                        'periodo_texto' => '2011–2015', 'situacao' => 'concluido',
                        'detalhes' => 'Eleito deputado estadual em 2010 pelo PRB, retornando à ALBA após exercer mandato de vereador em Feira de Santana.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859', 'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2015, 'ano_fim' => 2019,
                        'periodo_texto' => '2015–2019', 'situacao' => 'concluido',
                        'detalhes' => 'Reeleito deputado estadual pelo PRB.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859', 'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2019, 'ano_fim' => 2023,
                        'periodo_texto' => '2019–2023', 'situacao' => 'concluido',
                        'detalhes' => 'Reeleito em 2018 pelo PRB. Em 2019, a legenda passou a se chamar Republicanos.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859', 'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2023, 'ano_fim' => 2027,
                        'periodo_texto' => '2023–2027', 'situacao' => 'ativo',
                        'detalhes' => 'Reeleito deputado estadual pelo Republicanos; mandato em exercício.',
                        'fonte' => 'ALBA', 'fonte_id' => '915859', 'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/915859',
                    ],
                ],
                'jurailton-santos' => [
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'PRB', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2019, 'ano_fim' => 2023,
                        'periodo_texto' => '2019–2023', 'situacao' => 'concluido',
                        'detalhes' => 'Eleito deputado estadual pelo PRB.',
                        'fonte' => 'ALBA', 'fonte_id' => '926897',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/926897',
                    ],
                    [
                        'cargo' => 'Deputado Estadual', 'partido' => 'REPUBLICANOS', 'tipo' => 'mandato',
                        'esfera' => 'estadual', 'uf' => 'BA', 'ano_inicio' => 2023, 'ano_fim' => 2027,
                        'periodo_texto' => '2023–2027', 'situacao' => 'ativo',
                        'detalhes' => 'Reeleito deputado estadual pelo Republicanos.',
                        'fonte' => 'ALBA', 'fonte_id' => '926897',
                        'fonte_url' => 'https://www.al.ba.gov.br/deputados/deputado-estadual/926897',
                    ],
                ],
            ],
        ],

        'registro_2026' => [
            'timezone' => env('POLITICA_TSE_REGISTRO_2026_TIMEZONE', 'America/Bahia'),
            'prazo' => env('POLITICA_TSE_REGISTRO_2026_PRAZO', '2026-08-15 19:00:00'),
        ],
        'photos' => [
            'enabled' => (bool) env('POLITICA_TSE_FOTOS_ENABLED', true),
            'url_template' => env(
                'POLITICA_TSE_FOTOS_URL_TEMPLATE',
                'https://cdn.tse.jus.br/estatistica/sead/eleicoes/eleicoes%d/fotos/foto_cand%d_%s_div.zip'
            ),
            'public_path' => env('POLITICA_TSE_FOTOS_PUBLIC_PATH', 'images/politica/oficiais'),
        ],
        'automation' => [
            // O navegador apenas enfileira. O Laravel Scheduler executa o trabalho pesado.
            'queue_enabled' => (bool) env('POLITICA_TSE_QUEUE_ENABLED', true),
            'automatic_enabled' => (bool) env('POLITICA_TSE_AUTO_SYNC_ENABLED', true),
            'year' => (int) env('POLITICA_TSE_AUTO_SYNC_YEAR', 2026),
            'uf' => env('POLITICA_TSE_AUTO_SYNC_UF', 'BA'),
            'cron' => env('POLITICA_TSE_AUTO_SYNC_CRON', '17 3 * * *'),
            'timezone' => env('POLITICA_TSE_AUTO_SYNC_TIMEZONE', 'America/Bahia'),
            'manual_cooldown_minutes' => (int) env('POLITICA_TSE_MANUAL_COOLDOWN_MINUTES', 10),
            'stale_minutes' => (int) env('POLITICA_TSE_REQUEST_STALE_MINUTES', 30),
            'processor_lock_seconds' => (int) env('POLITICA_TSE_PROCESSOR_LOCK_SECONDS', 1800),
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


    'qualidade' => [
        // Auditoria local: somente leitura e cache curto para reduzir custo no servidor compartilhado.
        'cache_minutes' => (int) env('POLITICA_QUALIDADE_CACHE_MINUTES', 10),
        'espelho_revisao_dias' => (int) env('POLITICA_QUALIDADE_ESPELHO_REVISAO_DIAS', 120),
        'limite_itens_por_regra' => (int) env('POLITICA_QUALIDADE_LIMITE_ITENS', 30),
    ],

    'apuracao' => [
        // Segurança primeiro: o coletor real permanece desligado até habilitação explícita.
        // O TSE 2026 ainda definirá a cadência recomendada nos simulados; usamos 60 s,
        // que também era a recomendação operacional documentada em 2024.
        'live_enabled' => (bool) env('POLITICA_APURACAO_LIVE_ENABLED', false),
        'schedule_enabled' => (bool) env('POLITICA_APURACAO_SCHEDULE_ENABLED', false),
        'base_url' => env('POLITICA_APURACAO_BASE_URL', 'https://resultados.tse.jus.br'),
        'ambiente' => env('POLITICA_APURACAO_AMBIENTE', 'oficial'),
        'ciclo' => env('POLITICA_APURACAO_CICLO', 'ele2026'),
        'poll_seconds' => max(60, (int) env('POLITICA_TSE_POLL_SECONDS', 60)),
        'lock_seconds' => (int) env('POLITICA_APURACAO_LOCK_SECONDS', 55),
        'recheck_grace_seconds' => (int) env('POLITICA_APURACAO_RECHECK_GRACE_SECONDS', 180),
        'historico_checkpoint_seconds' => (int) env('POLITICA_APURACAO_CHECKPOINT_SECONDS', 60),
        'snapshot_ttl_seconds' => (int) env('POLITICA_SNAPSHOT_TTL_SECONDS', 15),
        // EA14 + até cinco EA20 (Presidente, Governador, Senador, Dep. Federal e Estadual).
        'max_requests_per_cycle' => min(6, (int) env('POLITICA_TSE_MAX_REQUESTS_PER_CYCLE', 6)),
        'connect_timeout' => (int) env('POLITICA_APURACAO_CONNECT_TIMEOUT', 3),
        'request_timeout' => (int) env('POLITICA_APURACAO_REQUEST_TIMEOUT', 8),
        'snapshot_top' => (int) env('POLITICA_APURACAO_SNAPSHOT_TOP', 50),
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
