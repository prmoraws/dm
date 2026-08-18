<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\FonteEstado;
use App\Models\Politica\V2\TseImportacao;
use App\Models\Politica\V2\TseSolicitacao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PoliticaQualidadeDadosService
{
    private const CACHE_KEY = 'politica:v2:qualidade-dados:v1';

    /** @return array<string,mixed> */
    public function painel(string $nivel = 'todos', string $grupo = 'todos', string $busca = '', bool $forcar = false): array
    {
        $snapshot = $this->snapshot($forcar);
        $nivel = in_array($nivel, ['todos', 'critico', 'alerta', 'info'], true) ? $nivel : 'todos';
        $gruposValidos = ['todos', 'eleitoral', 'territorio', 'espelho', 'identidade', 'integracoes'];
        $grupo = in_array($grupo, $gruposValidos, true) ? $grupo : 'todos';
        $busca = trim($busca);

        $achados = collect($snapshot['achados'])
            ->when($nivel !== 'todos', fn (Collection $itens) => $itens->where('nivel', $nivel))
            ->when($grupo !== 'todos', fn (Collection $itens) => $itens->where('grupo', $grupo))
            ->when($busca !== '', function (Collection $itens) use ($busca) {
                $needle = Str::lower($busca);

                return $itens->filter(function (array $item) use ($needle) {
                    $texto = Str::lower(implode(' ', [
                        $item['codigo'] ?? '',
                        $item['titulo'] ?? '',
                        $item['descricao'] ?? '',
                        $item['contexto'] ?? '',
                    ]));

                    return Str::contains($texto, $needle);
                });
            })
            ->values();

        $contagensFiltradas = [
            'critico' => $achados->where('nivel', 'critico')->count(),
            'alerta' => $achados->where('nivel', 'alerta')->count(),
            'info' => $achados->where('nivel', 'info')->count(),
            'total' => $achados->count(),
        ];

        $resumoExport = array_merge((array) $snapshot['resumo_linhas'], [
            ['Filtro de nível', $nivel],
            ['Filtro de grupo', $grupo],
            ['Busca', $busca !== '' ? $busca : '—'],
            ['Achados no filtro', $contagensFiltradas['total']],
            ['Críticos no filtro', $contagensFiltradas['critico']],
            ['Alertas no filtro', $contagensFiltradas['alerta']],
        ]);

        return $snapshot + [
            'filtros' => ['nivel' => $nivel, 'grupo' => $grupo, 'busca' => $busca],
            'achadosFiltrados' => $achados->all(),
            'contagensFiltradas' => $contagensFiltradas,
            'gruposDisponiveis' => $this->gruposDisponiveis(),
            'niveisDisponiveis' => $this->niveisDisponiveis(),
            'resumo_export_linhas' => $resumoExport,
            'achados_linhas' => $this->achadosLinhas($achados),
        ];
    }

    /** @return array<string,mixed> */
    public function snapshot(bool $forcar = false): array
    {
        if ($forcar) {
            Cache::forget(self::CACHE_KEY);
        }

        $minutos = max(1, (int) config('politica.qualidade.cache_minutes', 10));

        return Cache::remember(self::CACHE_KEY, now()->addMinutes($minutos), function (): array {
            $achados = collect();
            $metricas = $this->metricasBase();

            $this->auditarTerritorio($achados, $metricas);
            $this->auditarCandidaturas($achados, $metricas);
            $this->auditarZonas($achados, $metricas);
            $this->auditarIdentidade($achados, $metricas);
            $this->auditarAcompanhados($achados, $metricas);
            $this->auditarEspelho($achados, $metricas);
            $this->auditarIntegracoes($achados, $metricas);

            $achados = $achados
                ->sortByDesc(fn (array $item) => sprintf('%d-%s', $this->pesoNivel($item['nivel']), $item['codigo']))
                ->values();

            $contagens = [
                'critico' => $achados->where('nivel', 'critico')->count(),
                'alerta' => $achados->where('nivel', 'alerta')->count(),
                'info' => $achados->where('nivel', 'info')->count(),
                'total' => $achados->count(),
            ];

            $status = $contagens['critico'] > 0 ? 'critico' : ($contagens['alerta'] > 0 ? 'atencao' : 'ok');

            $dados = [
                'status' => $status,
                'contagens' => $contagens,
                'metricas' => $metricas,
                'achados' => $achados->all(),
                'gerado_em' => now(),
                'metodologia_linhas' => $this->metodologiaLinhas(),
            ];

            return $dados + [
                'resumo_linhas' => $this->resumoLinhas($dados),
            ];
        });
    }

    public function limparCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string,int|mixed> */
    private function metricasBase(): array
    {
        return [
            'candidaturas' => DB::table('politica_candidaturas')->count(),
            'resultados_municipais' => DB::table('politica_resultados_municipais')->count(),
            'resultados_zonas' => DB::table('politica_resultados_zonas')->count(),
            'municipios_total' => Cidade::query()->count(),
            'municipios_oficiais' => Cidade::query()->whereNotNull('ibge_code')->count(),
            'municipios_auxiliares_legado' => Cidade::query()->whereNull('ibge_code')->count(),
            'espelhos_operacionais' => EspelhoOperacional::query()->count(),
            'acompanhados_ativos' => Acompanhamento::query()->where('ativo', true)->count(),
            'fontes' => FonteEstado::query()->count(),
            'importacoes' => TseImportacao::query()->count(),
            'solicitacoes' => TseSolicitacao::query()->count(),
        ];
    }

    private function auditarTerritorio(Collection $achados, array &$metricas): void
    {
        $esperados = (int) config('politica.data_sources.ibge.expected_municipalities', 417);
        $oficiais = (int) $metricas['municipios_oficiais'];

        if ($oficiais !== $esperados) {
            $achados->push($this->issue(
                'critico', 'territorio', 'territorio.quantidade_oficial',
                'Quantidade de municípios oficiais diferente do esperado',
                "A base identificada por código IBGE possui {$oficiais} município(s); o recorte Bahia espera {$esperados}.",
                "Oficiais={$oficiais} · Esperados={$esperados}",
                route('politica.cidades')
            ));
        }

        if ((int) $metricas['municipios_auxiliares_legado'] > 0) {
            $achados->push($this->issue(
                'info', 'territorio', 'territorio.auxiliares_legado',
                'Registros territoriais auxiliares do legado preservados',
                'Linhas sem código IBGE permanecem separadas da malha oficial para auditoria e reconciliação; não são contadas como municípios oficiais.',
                number_format((int) $metricas['municipios_auxiliares_legado'], 0, ',', '.').' registro(s) auxiliar(es)',
                route('politica.cidades')
            ));
        }

        $semTse = Cidade::query()->whereNotNull('ibge_code')->whereNull('tse_codigo')->count();
        $semCoordenadas = Cidade::query()
            ->whereNotNull('ibge_code')
            ->where(fn ($q) => $q->whereNull('latitude')->orWhereNull('longitude'))
            ->count();

        $metricas['municipios_sem_tse_codigo'] = $semTse;
        $metricas['municipios_sem_coordenadas'] = $semCoordenadas;

        if ($semTse > 0) {
            $nomes = Cidade::query()->whereNotNull('ibge_code')->whereNull('tse_codigo')->orderBy('nome')->limit(12)->pluck('nome')->implode(', ');
            $achados->push($this->issue(
                'alerta', 'territorio', 'territorio.sem_codigo_tse',
                'Municípios oficiais sem código TSE',
                'O código IBGE existe, mas o identificador territorial do TSE ainda não foi reconciliado para todas as cidades.',
                "{$semTse} município(s) · exemplos: {$nomes}",
                route('politica.dados-oficiais')
            ));
        }

        if ($semCoordenadas > 0) {
            $nomes = Cidade::query()
                ->whereNotNull('ibge_code')
                ->where(fn ($q) => $q->whereNull('latitude')->orWhereNull('longitude'))
                ->orderBy('nome')->limit(12)->pluck('nome')->implode(', ');
            $achados->push($this->issue(
                'alerta', 'territorio', 'territorio.sem_coordenadas',
                'Municípios oficiais sem coordenadas completas',
                'O mapa e algumas leituras territoriais ficam incompletos enquanto latitude ou longitude estiverem ausentes.',
                "{$semCoordenadas} município(s) · exemplos: {$nomes}",
                route('politica.mapa')
            ));
        }
    }

    private function auditarCandidaturas(Collection $achados, array &$metricas): void
    {
        $agregados = DB::table('politica_candidaturas as c')
            ->join('politica_politicos as p', 'p.id', '=', 'c.politico_id')
            ->join('politica_eleicoes as e', 'e.id', '=', 'c.eleicao_id')
            ->join('politica_cargos as ca', 'ca.id', '=', 'c.cargo_id')
            ->leftJoin('politica_partidos as pa', 'pa.id', '=', 'c.partido_id')
            ->leftJoin('politica_resultados_municipais as rm', 'rm.candidatura_id', '=', 'c.id')
            ->select([
                'c.id', 'c.votos_total', 'c.uf', 'c.origem', 'c.tse_sq_candidato', 'c.tse_chave_historica',
                'c.partido_id', 'c.sincronizado_em', 'p.nome_publico', 'p.slug', 'e.ano', 'e.turno',
                'ca.nome as cargo_nome', 'pa.sigla as partido_sigla',
            ])
            ->selectRaw('COUNT(rm.id) as municipios_linhas')
            ->selectRaw('COALESCE(SUM(rm.votos), 0) as soma_municipios')
            ->groupBy([
                'c.id', 'c.votos_total', 'c.uf', 'c.origem', 'c.tse_sq_candidato', 'c.tse_chave_historica',
                'c.partido_id', 'c.sincronizado_em', 'p.nome_publico', 'p.slug', 'e.ano', 'e.turno',
                'ca.nome', 'pa.sigla',
            ])
            ->get();

        $metricas['candidaturas_com_resultado_municipal'] = $agregados->filter(fn ($r) => (int) $r->municipios_linhas > 0)->count();
        $metricas['candidaturas_total_municipal_divergente'] = 0;

        foreach ($agregados as $row) {
            $linhas = (int) $row->municipios_linhas;
            if ($linhas > 0 && strtoupper((string) $row->uf) !== 'BR') {
                $soma = (int) $row->soma_municipios;
                $total = (int) $row->votos_total;

                if ($soma !== $total) {
                    $metricas['candidaturas_total_municipal_divergente']++;
                    $maior = $soma > $total;
                    $achados->push($this->issue(
                        $maior ? 'critico' : 'alerta',
                        'eleitoral',
                        $maior ? 'eleitoral.soma_municipal_maior_total' : 'eleitoral.soma_municipal_menor_total',
                        $maior ? 'Soma municipal maior que o total da candidatura' : 'Soma municipal menor que o total da candidatura',
                        $maior
                            ? 'A soma dos resultados municipais ultrapassa o total gravado na candidatura e indica inconsistência objetiva que precisa ser corrigida.'
                            : 'A soma municipal é menor que o total gravado. Isso pode representar cobertura histórica incompleta ou importação ainda parcial; não é tratado automaticamente como voto zero.',
                        $this->contextoCandidatura($row)." · total={$total} · soma municipal={$soma} · municípios={$linhas}",
                        route('politica.politicos.show', $row->slug)
                    ));
                }
            }

            if ((string) $row->origem === 'tse_dados_abertos') {
                if (! $row->partido_id) {
                    $achados->push($this->issue(
                        'alerta', 'eleitoral', 'eleitoral.tse_sem_partido',
                        'Candidatura oficial TSE sem partido associado',
                        'O registro está marcado como oficial, porém não possui relacionamento partidário.',
                        $this->contextoCandidatura($row), route('politica.politicos.show', $row->slug)
                    ));
                }

                if (! filled($row->tse_sq_candidato) && ! filled($row->tse_chave_historica)) {
                    $achados->push($this->issue(
                        'critico', 'eleitoral', 'eleitoral.tse_sem_identificador',
                        'Candidatura oficial TSE sem identificador rastreável',
                        'Registros oficiais devem possuir SQ_CANDIDATO ou chave histórica suficiente para reencontrar a origem.',
                        $this->contextoCandidatura($row), route('politica.dados-oficiais')
                    ));
                }
            }
        }

        $municipaisForaDaCidadeQuery = DB::table('politica_resultados_municipais as rm')
            ->join('politica_candidaturas as c', 'c.id', '=', 'rm.candidatura_id')
            ->join('politica_politicos as p', 'p.id', '=', 'c.politico_id')
            ->join('politica_eleicoes as e', 'e.id', '=', 'c.eleicao_id')
            ->join('politica_cargos as ca', 'ca.id', '=', 'c.cargo_id')
            ->join('politica_cidades as ci', 'ci.id', '=', 'rm.cidade_id')
            ->whereNotNull('c.cidade_id')
            ->whereColumn('rm.cidade_id', '<>', 'c.cidade_id');

        $metricas['resultados_municipais_fora_cidade_candidatura'] = (clone $municipaisForaDaCidadeQuery)->count();
        $municipaisForaDaCidade = (clone $municipaisForaDaCidadeQuery)
            ->select('c.id', 'p.nome_publico', 'p.slug', 'e.ano', 'ca.nome as cargo_nome', 'ci.nome as cidade_resultado')
            ->limit($this->limite())
            ->get();
        foreach ($municipaisForaDaCidade as $row) {
            $achados->push($this->issue(
                'critico', 'eleitoral', 'eleitoral.candidatura_local_fora_cidade',
                'Candidatura municipal com resultado associado a outra cidade',
                'Candidaturas vinculadas a um município não podem receber linhas municipais de outro território.',
                "{$row->nome_publico} · {$row->cargo_nome} · {$row->ano} · resultado em {$row->cidade_resultado}",
                route('politica.politicos.show', $row->slug)
            ));
        }
    }

    private function auditarZonas(Collection $achados, array &$metricas): void
    {
        $agregados = DB::table('politica_resultados_zonas as rz')
            ->join('politica_zonas as z', 'z.id', '=', 'rz.zona_id')
            ->join('politica_candidaturas as c', 'c.id', '=', 'rz.candidatura_id')
            ->join('politica_politicos as p', 'p.id', '=', 'c.politico_id')
            ->join('politica_eleicoes as e', 'e.id', '=', 'rz.eleicao_id')
            ->join('politica_cargos as ca', 'ca.id', '=', 'c.cargo_id')
            ->join('politica_cidades as ci', 'ci.id', '=', 'z.cidade_id')
            ->leftJoin('politica_resultados_municipais as rm', function ($join): void {
                $join->on('rm.candidatura_id', '=', 'rz.candidatura_id')
                    ->on('rm.eleicao_id', '=', 'rz.eleicao_id')
                    ->on('rm.cidade_id', '=', 'z.cidade_id');
            })
            ->select([
                'rz.candidatura_id', 'rz.eleicao_id', 'z.cidade_id', 'p.nome_publico', 'p.slug',
                'e.ano', 'ca.nome as cargo_nome', 'ci.nome as cidade_nome', 'rm.votos as votos_municipio',
            ])
            ->selectRaw('COUNT(rz.id) as zonas_linhas')
            ->selectRaw('COALESCE(SUM(rz.votos), 0) as soma_zonas')
            ->groupBy([
                'rz.candidatura_id', 'rz.eleicao_id', 'z.cidade_id', 'p.nome_publico', 'p.slug',
                'e.ano', 'ca.nome', 'ci.nome', 'rm.votos',
            ])
            ->get();

        $divergentes = 0;
        foreach ($agregados as $row) {
            if ($row->votos_municipio === null) {
                $divergentes++;
                $achados->push($this->issue(
                    'alerta', 'eleitoral', 'eleitoral.zona_sem_resumo_municipal',
                    'Resultado por zona sem linha municipal correspondente',
                    'Há votos por zona, mas o resumo municipal da mesma candidatura/eleição/cidade não foi localizado.',
                    "{$row->nome_publico} · {$row->cargo_nome} · {$row->ano} · {$row->cidade_nome} · zonas=".(int) $row->zonas_linhas,
                    route('politica.politicos.show', $row->slug)
                ));
                continue;
            }

            $soma = (int) $row->soma_zonas;
            $municipio = (int) $row->votos_municipio;
            if ($soma !== $municipio) {
                $divergentes++;
                $achados->push($this->issue(
                    $soma > $municipio ? 'critico' : 'alerta',
                    'eleitoral',
                    $soma > $municipio ? 'eleitoral.zonas_maior_municipio' : 'eleitoral.zonas_menor_municipio',
                    $soma > $municipio ? 'Soma das zonas maior que o resultado municipal' : 'Soma das zonas menor que o resultado municipal',
                    $soma > $municipio
                        ? 'As zonas ultrapassam o total municipal e indicam inconsistência objetiva.'
                        : 'As zonas não cobrem todo o total municipal. O painel sinaliza cobertura parcial sem completar a diferença artificialmente.',
                    "{$row->nome_publico} · {$row->cargo_nome} · {$row->ano} · {$row->cidade_nome} · município={$municipio} · zonas={$soma}",
                    route('politica.espelho.inteligente', $row->cidade_id)
                ));
            }
        }

        $metricas['contextos_zona_municipio_auditados'] = $agregados->count();
        $metricas['contextos_zona_municipio_divergentes'] = $divergentes;
    }

    private function auditarIdentidade(Collection $achados, array &$metricas): void
    {
        $duplicados = DB::table('politica_politicos')
            ->whereNotNull('identidade_publica_hash')
            ->where('identidade_publica_hash', '<>', '')
            ->select('identidade_publica_hash')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('identidade_publica_hash')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $metricas['hashes_identidade_duplicados'] = $duplicados->count();

        foreach ($duplicados->take($this->limite()) as $row) {
            $nomes = DB::table('politica_politicos')
                ->where('identidade_publica_hash', $row->identidade_publica_hash)
                ->orderBy('nome_publico')
                ->pluck('nome_publico')
                ->implode(', ');

            $achados->push($this->issue(
                'critico', 'identidade', 'identidade.hash_duplicado',
                'Possível duplicidade de pessoa política',
                'Mais de um registro compartilha a mesma identidade pública normalizada e deve ser revisado antes de novas importações.',
                $nomes,
                route('politica.acompanhamento')
            ));
        }
    }

    private function auditarAcompanhados(Collection $achados, array &$metricas): void
    {
        $acompanhados = Acompanhamento::query()
            ->where('ativo', true)
            ->with('politico:id,nome_publico,slug,foto_url')
            ->orderBy('ordem')
            ->get();

        $semFoto = 0;
        $fotoQuebrada = 0;

        foreach ($acompanhados as $acompanhamento) {
            $politico = $acompanhamento->politico;
            if (! $politico) {
                continue;
            }

            if (! filled($politico->foto_url)) {
                $semFoto++;
                $achados->push($this->issue(
                    'alerta', 'identidade', 'identidade.acompanhado_sem_foto',
                    'Acompanhado ativo sem foto oficial',
                    'O registro prioritário está sem imagem vinculada. Isso não afeta votos, mas reduz a qualidade visual e a conferência de identidade.',
                    $politico->nome_publico,
                    route('politica.politicos.show', $politico->slug)
                ));
                continue;
            }

            if ($this->fotoLocalQuebrada((string) $politico->foto_url)) {
                $fotoQuebrada++;
                $achados->push($this->issue(
                    'alerta', 'identidade', 'identidade.foto_local_ausente',
                    'Foto oficial aponta para arquivo local inexistente',
                    'A URL está gravada no banco, mas o arquivo correspondente não foi localizado em public/.',
                    $politico->nome_publico.' · '.$politico->foto_url,
                    route('politica.politicos.show', $politico->slug)
                ));
            }
        }

        $metricas['acompanhados_sem_foto'] = $semFoto;
        $metricas['acompanhados_foto_local_quebrada'] = $fotoQuebrada;
    }

    private function auditarEspelho(Collection $achados, array &$metricas): void
    {
        $oficiaisSemEspelho = Cidade::query()
            ->whereNotNull('ibge_code')
            ->whereDoesntHave('espelhoOperacional')
            ->orderBy('nome');

        $totalSemEspelho = (clone $oficiaisSemEspelho)->count();
        $metricas['municipios_oficiais_sem_espelho'] = $totalSemEspelho;
        if ($totalSemEspelho > 0) {
            $nomes = (clone $oficiaisSemEspelho)->limit(12)->pluck('nome')->implode(', ');
            $achados->push($this->issue(
                'alerta', 'espelho', 'espelho.municipio_sem_operacional',
                'Municípios oficiais sem Espelho Operacional',
                'A base territorial oficial possui cidades sem registro correspondente em politica_espelho_operacional.',
                "{$totalSemEspelho} município(s) · exemplos: {$nomes}",
                route('politica.cidades')
            ));
        }

        $dias = max(1, (int) config('politica.qualidade.espelho_revisao_dias', 120));
        $limiteData = now()->subDays($dias);
        $desatualizadosQuery = EspelhoOperacional::query()
            ->with('cidade:id,nome')
            ->where(fn ($q) => $q->whereNull('revisado_em')->orWhere('revisado_em', '<', $limiteData));
        $desatualizados = (clone $desatualizadosQuery)->count();
        $metricas['espelhos_revisao_vencida'] = $desatualizados;

        if ($desatualizados > 0) {
            $nomes = (clone $desatualizadosQuery)->limit(12)->get()->pluck('cidade.nome')->filter()->implode(', ');
            $achados->push($this->issue(
                'alerta', 'espelho', 'espelho.revisao_vencida',
                'Espelhos operacionais sem revisão recente',
                "Há registros sem revisão ou revisados há mais de {$dias} dias. O alerta trata qualidade do dado interno e não altera resultados eleitorais.",
                "{$desatualizados} espelho(s) · exemplos: {$nomes}",
                route('politica.cidades')
            ));
        }

        $favoritosDuplicados = DB::table('politica_espelho_inteligencia as ei')
            ->join('politica_candidaturas as c', 'c.id', '=', 'ei.candidatura_id')
            ->join('politica_cidades as ci', 'ci.id', '=', 'ei.cidade_id')
            ->join('politica_cargos as ca', 'ca.id', '=', 'c.cargo_id')
            ->where('ei.classificacao', 'favorito')
            ->select('ei.cidade_id', 'ei.eleicao_id', 'c.cargo_id', 'ci.nome as cidade_nome', 'ca.nome as cargo_nome')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('ei.cidade_id', 'ei.eleicao_id', 'c.cargo_id', 'ci.nome', 'ca.nome')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $metricas['favoritos_conflitantes'] = $favoritosDuplicados->count();
        foreach ($favoritosDuplicados->take($this->limite()) as $row) {
            $achados->push($this->issue(
                'critico', 'espelho', 'espelho.multiplos_favoritos',
                'Mais de um candidato favorito no mesmo recorte do Espelho',
                'O município/eleição/cargo deve possuir no máximo um favorito persistente; os demais podem permanecer como acompanhamento.',
                "{$row->cidade_nome} · {$row->cargo_nome} · ".(int) $row->total.' favoritos',
                route('politica.espelho.inteligente', $row->cidade_id)
            ));
        }

        $inconsistenciasQuery = DB::table('politica_espelho_inteligencia as ei')
            ->join('politica_candidaturas as c', 'c.id', '=', 'ei.candidatura_id')
            ->join('politica_cidades as ci', 'ci.id', '=', 'ei.cidade_id')
            ->join('politica_politicos as p', 'p.id', '=', 'c.politico_id')
            ->whereNotNull('ei.eleicao_id')
            ->whereColumn('ei.eleicao_id', '<>', 'c.eleicao_id');

        $metricas['espelho_eleicao_inconsistente'] = (clone $inconsistenciasQuery)->count();
        $inconsistencias = (clone $inconsistenciasQuery)
            ->select('ei.id', 'ei.cidade_id', 'ci.nome as cidade_nome', 'p.nome_publico')
            ->limit($this->limite())
            ->get();
        foreach ($inconsistencias as $row) {
            $achados->push($this->issue(
                'critico', 'espelho', 'espelho.eleicao_inconsistente',
                'Inteligência do Espelho ligada à eleição errada',
                'O eleicao_id do acompanhamento interno é diferente da eleição pertencente à candidatura selecionada.',
                "{$row->cidade_nome} · {$row->nome_publico}",
                route('politica.espelho.inteligente', $row->cidade_id)
            ));
        }

        $cidadeInconsistenteQuery = DB::table('politica_espelho_inteligencia as ei')
            ->join('politica_candidaturas as c', 'c.id', '=', 'ei.candidatura_id')
            ->join('politica_cidades as ci', 'ci.id', '=', 'ei.cidade_id')
            ->join('politica_politicos as p', 'p.id', '=', 'c.politico_id')
            ->whereNotNull('c.cidade_id')
            ->whereColumn('c.cidade_id', '<>', 'ei.cidade_id');

        $metricas['espelho_candidatura_local_outra_cidade'] = (clone $cidadeInconsistenteQuery)->count();
        $cidadeInconsistente = (clone $cidadeInconsistenteQuery)
            ->select('ei.cidade_id', 'ci.nome as cidade_nome', 'p.nome_publico')
            ->limit($this->limite())
            ->get();
        foreach ($cidadeInconsistente as $row) {
            $achados->push($this->issue(
                'critico', 'espelho', 'espelho.candidatura_local_outra_cidade',
                'Espelho acompanha candidatura municipal de outra cidade',
                'Uma candidatura com cidade própria está vinculada a um Espelho de município diferente.',
                "{$row->cidade_nome} · {$row->nome_publico}",
                route('politica.espelho.inteligente', $row->cidade_id)
            ));
        }
    }

    private function auditarIntegracoes(Collection $achados, array &$metricas): void
    {
        $fontesErro = FonteEstado::query()->where('erros_consecutivos', '>', 0)->orderByDesc('erros_consecutivos')->get();
        $metricas['fontes_com_erro'] = $fontesErro->count();
        foreach ($fontesErro->take($this->limite()) as $fonte) {
            $achados->push($this->issue(
                'alerta', 'integracoes', 'integracoes.fonte_com_erro',
                'Fonte oficial com erros consecutivos',
                'A última sequência de consultas registrou falhas. Os dados já importados não são apagados, mas a atualização da fonte deve ser conferida.',
                $fonte->chave.' · erros='.(int) $fonte->erros_consecutivos.' · HTTP '.($fonte->http_status ?: '—'),
                route('politica.dados-oficiais')
            ));
        }

        $importacoesErro = TseImportacao::query()->where('status', 'erro')->orderByDesc('id')->limit($this->limite())->get();
        $metricas['importacoes_com_erro'] = TseImportacao::query()->where('status', 'erro')->count();
        foreach ($importacoesErro as $importacao) {
            $achados->push($this->issue(
                'alerta', 'integracoes', 'integracoes.importacao_tse_erro',
                'Importação TSE encerrada com erro',
                'Uma execução de importação não foi concluída. O histórico do erro é mantido para rastreabilidade.',
                "{$importacao->ano}/{$importacao->uf} · {$importacao->tipo} · ".Str::limit((string) $importacao->ultimo_erro, 180),
                route('politica.dados-oficiais')
            ));
        }

        $minutos = max(5, (int) config('politica.tse.automation.stale_minutes', 30));
        $corte = now()->subMinutes($minutos);
        $travadasQuery = TseSolicitacao::query()
            ->whereIn('status', ['pendente', 'executando'])
            ->where(function ($q) use ($corte): void {
                $q->where('solicitada_em', '<', $corte)
                    ->orWhere(fn ($sub) => $sub->whereNull('solicitada_em')->where('created_at', '<', $corte));
            });
        $metricas['solicitacoes_tse_possivelmente_travadas'] = (clone $travadasQuery)->count();
        $travadas = (clone $travadasQuery)->orderBy('solicitada_em')->limit($this->limite())->get();

        foreach ($travadas as $pedido) {
            $achados->push($this->issue(
                'alerta', 'integracoes', 'integracoes.solicitacao_tse_antiga',
                'Solicitação TSE pendente/executando há tempo acima do esperado',
                'O processador possui recuperação automática, mas esta solicitação deve ser observada se permanecer no mesmo estado.',
                "#{$pedido->id} · {$pedido->ano}/{$pedido->uf} · {$pedido->somente} · {$pedido->status}",
                route('politica.dados-oficiais')
            ));
        }
    }

    /** @return array<string,mixed> */
    private function issue(string $nivel, string $grupo, string $codigo, string $titulo, string $descricao, string $contexto = '', ?string $url = null): array
    {
        return compact('nivel', 'grupo', 'codigo', 'titulo', 'descricao', 'contexto', 'url');
    }

    private function contextoCandidatura(object $row): string
    {
        return trim("{$row->nome_publico} · {$row->cargo_nome} · {$row->ano} · ".($row->partido_sigla ?: 'sem partido'));
    }

    private function fotoLocalQuebrada(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '' || ! str_starts_with($path, '/')) {
            return false;
        }

        return ! is_file(public_path(ltrim($path, '/')));
    }

    private function limite(): int
    {
        return max(10, min((int) config('politica.qualidade.limite_itens_por_regra', 30), 100));
    }

    private function pesoNivel(string $nivel): int
    {
        return match ($nivel) {
            'critico' => 3,
            'alerta' => 2,
            default => 1,
        };
    }

    /** @return array<string,string> */
    private function gruposDisponiveis(): array
    {
        return [
            'todos' => 'Todos os grupos',
            'eleitoral' => 'Integridade eleitoral',
            'territorio' => 'Território',
            'espelho' => 'Espelho operacional',
            'identidade' => 'Identidade e fotos',
            'integracoes' => 'Integrações TSE',
        ];
    }

    /** @return array<string,string> */
    private function niveisDisponiveis(): array
    {
        return [
            'todos' => 'Todos os níveis',
            'critico' => 'Crítico',
            'alerta' => 'Alerta',
            'info' => 'Informativo',
        ];
    }

    /** @return array<int,array{0:string,1:mixed}> */
    private function resumoLinhas(array $dados): array
    {
        return [
            ['Status geral', strtoupper((string) $dados['status'])],
            ['Achados críticos', $dados['contagens']['critico']],
            ['Alertas', $dados['contagens']['alerta']],
            ['Informativos', $dados['contagens']['info']],
            ['Candidaturas', $dados['metricas']['candidaturas']],
            ['Resultados municipais', $dados['metricas']['resultados_municipais']],
            ['Resultados por zona', $dados['metricas']['resultados_zonas']],
            ['Municípios oficiais', $dados['metricas']['municipios_oficiais']],
            ['Registros auxiliares legado', $dados['metricas']['municipios_auxiliares_legado']],
            ['Espelhos operacionais', $dados['metricas']['espelhos_operacionais']],
            ['Acompanhados ativos', $dados['metricas']['acompanhados_ativos']],
            ['Gerado em', $dados['gerado_em']->format('d/m/Y H:i')],
        ];
    }

    /** @return array<int,array<int,mixed>> */
    private function achadosLinhas(Collection $achados): array
    {
        return $achados->map(fn (array $item) => [
            strtoupper($item['nivel']),
            $this->gruposDisponiveis()[$item['grupo']] ?? $item['grupo'],
            $item['codigo'],
            $item['titulo'],
            $item['contexto'],
            $item['descricao'],
        ])->all();
    }

    /** @return array<int,array{0:string,1:string}> */
    private function metodologiaLinhas(): array
    {
        return [
            ['Integridade', 'O centro não corrige dados automaticamente. Ele somente detecta divergências e aponta o recorte afetado.'],
            ['Ausência não é zero', 'Falta de linha municipal ou zonal é tratada como ausência/cobertura parcial, nunca como voto zero inventado.'],
            ['Total x municípios', 'Para candidaturas com escopo UF/município e linhas municipais existentes, a soma é comparada ao total gravado. Escopo BR não é comparado com um recorte territorial parcial.'],
            ['Zonas x município', 'A soma das zonas é comparada ao resumo municipal apenas quando ambos existem.'],
            ['Espelho', 'Dados operacionais, favoritos e metas são auditados separadamente dos resultados oficiais.'],
            ['Fontes', 'Falhas de importação e de fonte são preservadas para rastreabilidade; o centro não baixa arquivos externos.'],
            ['Carga', 'O snapshot é agregado e fica em cache por alguns minutos para reduzir custo no servidor compartilhado.'],
        ];
    }
}
