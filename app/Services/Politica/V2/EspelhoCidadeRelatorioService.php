<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\ResultadoZona;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class EspelhoCidadeRelatorioService
{
    /** @return array<string,mixed> */
    public function gerar(Cidade $cidade, int $candidaturaId): array
    {
        $cidade->loadMissing('espelhoOperacional');

        $candidatura = Candidatura::query()
            ->with(['politico', 'partido', 'cargo', 'eleicao'])
            ->findOrFail($candidaturaId);

        if ($candidatura->cidade_id !== null && (int) $candidatura->cidade_id !== (int) $cidade->id) {
            throw (new ModelNotFoundException())->setModel(Candidatura::class, [$candidaturaId]);
        }

        $resultado = ResultadoMunicipal::query()
            ->where('cidade_id', $cidade->id)
            ->where('candidatura_id', $candidatura->id)
            ->first();

        $inteligencia = EspelhoInteligencia::query()
            ->where('cidade_id', $cidade->id)
            ->where('candidatura_id', $candidatura->id)
            ->first();

        $historico = $this->historicoCidade($cidade, $candidatura);
        $zonas = $this->zonas($cidade, $candidatura);
        $ranking = $this->ranking($cidade, $candidatura, 20);
        $auditoria = $this->auditoria($cidade, $candidatura, $resultado, $zonas);

        $votosMunicipio = $resultado ? (int) $resultado->votos : null;
        $votosTotal = (int) $candidatura->votos_total;
        $participacaoNosVotos = $votosMunicipio !== null && $votosTotal > 0
            ? round(($votosMunicipio / $votosTotal) * 100, 4)
            : null;

        $dados = [
            'cidade' => [
                'id' => $cidade->id,
                'nome' => $cidade->nome,
                'ibge_code' => $cidade->ibge_code,
                'tse_codigo' => $cidade->tse_codigo,
                'populacao' => $cidade->populacao !== null ? (int) $cidade->populacao : null,
                'cadeiras_camara' => $cidade->cadeiras_camara !== null ? (int) $cidade->cadeiras_camara : null,
                'latitude' => $cidade->latitude !== null ? (float) $cidade->latitude : null,
                'longitude' => $cidade->longitude !== null ? (float) $cidade->longitude : null,
            ],
            'operacional' => $cidade->espelhoOperacional ? [
                'presidente_local' => $cidade->espelhoOperacional->presidente_local,
                'indicacao_bispo' => $cidade->espelhoOperacional->indicacao_bispo,
                'filiados_republicanos' => $cidade->espelhoOperacional->filiados_republicanos,
                'observacoes' => $cidade->espelhoOperacional->observacoes,
                'revisado_em' => $cidade->espelhoOperacional->revisado_em,
            ] : null,
            'candidato' => [
                'candidatura_id' => $candidatura->id,
                'politico_id' => $candidatura->politico_id,
                'nome' => $candidatura->politico?->nome_publico ?: $candidatura->nome_urna ?: 'Candidato',
                'nome_completo' => $candidatura->politico?->nome_completo,
                'foto_url' => $candidatura->politico?->foto_url ?: $candidatura->foto_url,
                'partido' => $candidatura->partido?->sigla,
                'numero' => $candidatura->numero_urna,
                'cargo' => $candidatura->cargo?->nome,
                'ano' => $candidatura->eleicao?->ano ? (int) $candidatura->eleicao->ano : null,
                'turno' => $candidatura->eleicao?->turno ? (int) $candidatura->eleicao->turno : null,
                'eleicao' => $candidatura->eleicao?->descricao,
                'situacao_registro' => $candidatura->situacaoRegistroExibicao(),
                'situacao_eleicao' => $candidatura->situacao_eleicao,
                'eleito' => (bool) $candidatura->eleito,
                'votos_total' => $votosTotal,
                'percentual_total' => $candidatura->percentual_total !== null ? (float) $candidatura->percentual_total : null,
                'votos_municipio' => $votosMunicipio,
                'percentual_municipio' => $resultado?->percentual !== null ? (float) $resultado->percentual : null,
                'posicao_municipio' => $resultado?->posicao ?: $this->posicaoNoMunicipio($cidade, $candidatura, $votosMunicipio),
                'participacao_nos_votos_do_candidato' => $participacaoNosVotos,
                'secoes_total' => $resultado?->secoes_total,
                'secoes_totalizadas' => $resultado?->secoes_totalizadas,
                'eleitores' => $resultado?->eleitores,
                'comparecimento' => $resultado?->comparecimento,
                'abstencoes' => $resultado?->abstencoes,
                'origem' => $candidatura->origem,
                'registro_oficial_tse' => $candidatura->isRegistroOficialTse(),
                'tse_sq_candidato' => $candidatura->tse_sq_candidato,
                'tse_chave_historica' => $candidatura->tse_chave_historica,
                'sincronizado_em' => $candidatura->sincronizado_em,
            ],
            'favorito' => $inteligencia ? [
                'classificacao' => $inteligencia->classificacao,
                'prioridade' => $inteligencia->prioridade,
                'meta_votos' => $inteligencia->meta_votos,
                'meta_percentual' => $inteligencia->meta_percentual !== null ? (float) $inteligencia->meta_percentual : null,
                'observacoes' => $inteligencia->observacoes,
                'revisado_em' => $inteligencia->revisado_em,
            ] : null,
            'historico' => $historico,
            'zonas' => $zonas,
            'ranking' => $ranking,
            'auditoria' => $auditoria,
            'gerado_em' => now(),
        ];

        return $dados + [
            'resumo_linhas' => $this->resumoLinhas($dados),
            'operacional_linhas' => $this->operacionalLinhas($dados),
            'favorito_linhas' => $this->favoritoLinhas($dados),
            'historico_linhas' => $this->historicoLinhas($historico),
            'zonas_linhas' => $this->zonasLinhas($zonas),
            'ranking_linhas' => $this->rankingLinhas($ranking),
            'auditoria_linhas' => $this->auditoriaLinhas($auditoria),
            'metodologia_linhas' => $this->metodologiaLinhas(),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function historicoCidade(Cidade $cidade, Candidatura $atual): array
    {
        $candidaturas = Candidatura::query()
            ->where('politico_id', $atual->politico_id)
            ->where('cargo_id', $atual->cargo_id)
            ->with([
                'eleicao:id,ano,turno,descricao,status',
                'partido:id,sigla',
                'resultadosMunicipais' => fn ($q) => $q->where('cidade_id', $cidade->id),
            ])
            ->get()
            ->sortBy(fn (Candidatura $c) => sprintf('%04d-%02d', (int) ($c->eleicao?->ano ?? 0), (int) ($c->eleicao?->turno ?? 0)))
            ->values();

        $anteriorComDado = null;

        return $candidaturas->map(function (Candidatura $candidatura) use (&$anteriorComDado, $atual) {
            $resultado = $candidatura->resultadosMunicipais->first();
            $votos = $resultado ? (int) $resultado->votos : null;
            $delta = null;
            $deltaPercentual = null;

            if ($votos !== null && $anteriorComDado !== null) {
                $delta = $votos - $anteriorComDado;
                $deltaPercentual = $anteriorComDado > 0
                    ? round(($delta / $anteriorComDado) * 100, 2)
                    : null;
            }

            if ($votos !== null) {
                $anteriorComDado = $votos;
            }

            return [
                'candidatura_id' => $candidatura->id,
                'atual' => (int) $candidatura->id === (int) $atual->id,
                'ano' => $candidatura->eleicao?->ano ? (int) $candidatura->eleicao->ano : null,
                'turno' => $candidatura->eleicao?->turno ? (int) $candidatura->eleicao->turno : null,
                'partido' => $candidatura->partido?->sigla,
                'numero' => $candidatura->numero_urna,
                'votos' => $votos,
                'percentual' => $resultado?->percentual !== null ? (float) $resultado->percentual : null,
                'posicao' => $resultado?->posicao,
                'delta' => $delta,
                'delta_percentual' => $deltaPercentual,
                'situacao_eleicao' => $candidatura->situacao_eleicao,
                'origem' => $candidatura->origem,
                'tem_linha_municipal' => $resultado !== null,
            ];
        })->all();
    }

    /** @return array<int,array<string,mixed>> */
    private function zonas(Cidade $cidade, Candidatura $candidatura): array
    {
        return ResultadoZona::query()
            ->where('candidatura_id', $candidatura->id)
            ->whereHas('zona', fn ($q) => $q->where('cidade_id', $cidade->id))
            ->with('zona:id,cidade_id,numero,tse_codigo')
            ->get()
            ->sortBy(fn (ResultadoZona $r) => (int) ($r->zona?->numero ?? 0))
            ->values()
            ->map(fn (ResultadoZona $r) => [
                'zona' => $r->zona?->numero,
                'tse_codigo' => $r->zona?->tse_codigo,
                'votos' => (int) $r->votos,
                'percentual' => $r->percentual !== null ? (float) $r->percentual : null,
                'secoes_total' => $r->secoes_total,
                'secoes_totalizadas' => $r->secoes_totalizadas,
            ])
            ->all();
    }

    /** @return array<int,array<string,mixed>> */
    private function ranking(Cidade $cidade, Candidatura $candidatura, int $limite): array
    {
        $query = Candidatura::query()
            ->select('politica_candidaturas.*')
            ->selectRaw('rm.votos as votos_no_municipio')
            ->selectRaw('rm.percentual as percentual_no_municipio')
            ->leftJoin('politica_resultados_municipais as rm', function ($join) use ($cidade, $candidatura): void {
                $join->on('rm.candidatura_id', '=', 'politica_candidaturas.id')
                    ->where('rm.cidade_id', '=', $cidade->id)
                    ->where('rm.eleicao_id', '=', $candidatura->eleicao_id);
            })
            ->where('politica_candidaturas.eleicao_id', $candidatura->eleicao_id)
            ->where('politica_candidaturas.cargo_id', $candidatura->cargo_id)
            ->where(function ($q) use ($cidade): void {
                $q->whereNull('politica_candidaturas.cidade_id')
                    ->orWhere('politica_candidaturas.cidade_id', $cidade->id);
            })
            ->with(['politico:id,nome_publico,slug', 'partido:id,sigla'])
            ->orderByRaw('CASE WHEN rm.votos IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('rm.votos')
            ->orderBy('politica_candidaturas.id');

        $top = $query->limit(max(5, min($limite, 100)))->get();

        if (! $top->contains('id', $candidatura->id)) {
            $favorito = (clone $query)->where('politica_candidaturas.id', $candidatura->id)->first();
            if ($favorito) {
                $top->push($favorito);
            }
        }

        return $top->values()->map(function (Candidatura $item, int $index) use ($candidatura) {
            return [
                'posicao_lista' => $index + 1,
                'favorito' => (int) $item->id === (int) $candidatura->id,
                'candidatura_id' => $item->id,
                'nome' => $item->politico?->nome_publico ?: $item->nome_urna ?: 'Candidato',
                'partido' => $item->partido?->sigla,
                'numero' => $item->numero_urna,
                'votos' => $item->votos_no_municipio !== null ? (int) $item->votos_no_municipio : null,
                'percentual' => $item->percentual_no_municipio !== null ? (float) $item->percentual_no_municipio : null,
            ];
        })->all();
    }

    /** @return array<string,mixed> */
    private function auditoria(Cidade $cidade, Candidatura $candidatura, ?ResultadoMunicipal $resultado, array $zonas): array
    {
        $municipiosCount = ResultadoMunicipal::query()->where('candidatura_id', $candidatura->id)->count();
        $municipiosSoma = (int) ResultadoMunicipal::query()->where('candidatura_id', $candidatura->id)->sum('votos');
        $zonasSoma = (int) collect($zonas)->sum('votos');
        $votosTotal = (int) $candidatura->votos_total;

        $escopoTotalComparavel = strtoupper((string) $candidatura->uf) !== 'BR';

        return [
            'tem_resultado_municipal' => $resultado !== null,
            'municipios_com_linha' => $municipiosCount,
            'soma_municipios' => $municipiosSoma,
            'votos_total_candidatura' => $votosTotal,
            'escopo_total_comparavel' => $escopoTotalComparavel,
            'municipios_conferem_total' => $municipiosCount > 0 && $escopoTotalComparavel ? $municipiosSoma === $votosTotal : null,
            'zonas_na_cidade' => count($zonas),
            'soma_zonas_cidade' => $zonasSoma,
            'zonas_conferem_municipio' => count($zonas) > 0 && $resultado !== null
                ? $zonasSoma === (int) $resultado->votos
                : null,
            'ibge_identificado' => filled($cidade->ibge_code),
            'fonte_oficial_tse' => $candidatura->isRegistroOficialTse(),
        ];
    }

    private function posicaoNoMunicipio(Cidade $cidade, Candidatura $candidatura, ?int $votos): ?int
    {
        if ($votos === null) {
            return null;
        }

        $maiores = ResultadoMunicipal::query()
            ->where('cidade_id', $cidade->id)
            ->where('eleicao_id', $candidatura->eleicao_id)
            ->whereHas('candidatura', fn ($q) => $q->where('cargo_id', $candidatura->cargo_id))
            ->where('votos', '>', $votos)
            ->count();

        return $maiores + 1;
    }

    /** @return array<int,array{0:string,1:mixed}> */
    private function resumoLinhas(array $dados): array
    {
        $cidade = $dados['cidade'];
        $candidato = $dados['candidato'];
        $favorito = $dados['favorito'];

        return [
            ['Município', $cidade['nome']],
            ['Código IBGE', $cidade['ibge_code']],
            ['População', $cidade['populacao']],
            ['Candidato selecionado', $candidato['nome']],
            ['Partido / número', trim(($candidato['partido'] ?: '—').' / '.($candidato['numero'] ?: '—'))],
            ['Cargo', $candidato['cargo']],
            ['Eleição', trim(($candidato['ano'] ?: '—').' · '.($candidato['turno'] ?: '—').'º turno')],
            ['Votos no município', $candidato['votos_municipio']],
            ['Percentual oficial no município', $candidato['percentual_municipio']],
            ['Posição no município', $candidato['posicao_municipio']],
            ['Votos totais da candidatura', $candidato['votos_total']],
            ['Participação do município nos votos do candidato (%)', $candidato['participacao_nos_votos_do_candidato']],
            ['Situação da eleição', $candidato['situacao_eleicao']],
            ['Favorito / classificação', $favorito['classificacao'] ?? 'Selecionado para o relatório'],
            ['Prioridade interna', $favorito['prioridade'] ?? null],
            ['Origem', $candidato['origem']],
            ['SQ candidato TSE', $candidato['tse_sq_candidato']],
            ['Gerado em', $dados['gerado_em']->format('d/m/Y H:i')],
        ];
    }

    /** @return array<int,array{0:string,1:mixed}> */
    private function operacionalLinhas(array $dados): array
    {
        $o = $dados['operacional'];
        if (! $o) {
            return [['Situação', 'Espelho operacional não cadastrado']];
        }

        return [
            ['Presidente local', $o['presidente_local']],
            ['Indicação', $o['indicacao_bispo']],
            ['Filiados Republicanos', $o['filiados_republicanos']],
            ['Observações', $o['observacoes']],
            ['Revisado em', $o['revisado_em']?->format('d/m/Y H:i')],
        ];
    }

    /** @return array<int,array{0:string,1:mixed}> */
    private function favoritoLinhas(array $dados): array
    {
        $f = $dados['favorito'];
        if (! $f) {
            return [['Situação', 'Candidato selecionado para exportação; ainda não marcado como favorito persistente.']];
        }

        return [
            ['Classificação', $f['classificacao']],
            ['Prioridade interna', $f['prioridade']],
            ['Meta de votos', $f['meta_votos']],
            ['Meta percentual', $f['meta_percentual']],
            ['Observações do acompanhamento', $f['observacoes']],
            ['Revisado em', $f['revisado_em']?->format('d/m/Y H:i')],
        ];
    }

    /** @return array<int,array<int,mixed>> */
    private function historicoLinhas(array $historico): array
    {
        return collect($historico)->map(fn (array $item) => [
            $item['ano'], $item['turno'], $item['partido'], $item['numero'], $item['votos'], $item['percentual'],
            $item['posicao'], $item['delta'], $item['delta_percentual'], $item['situacao_eleicao'],
            $item['tem_linha_municipal'] ? 'Sim' : 'Sem linha oficial', $item['origem'],
        ])->all();
    }

    /** @return array<int,array<int,mixed>> */
    private function zonasLinhas(array $zonas): array
    {
        return collect($zonas)->map(fn (array $item) => [
            $item['zona'], $item['tse_codigo'], $item['votos'], $item['percentual'], $item['secoes_total'], $item['secoes_totalizadas'],
        ])->all();
    }

    /** @return array<int,array<int,mixed>> */
    private function rankingLinhas(array $ranking): array
    {
        return collect($ranking)->map(fn (array $item) => [
            $item['posicao_lista'], $item['favorito'] ? 'SIM' : '', $item['nome'], $item['partido'], $item['numero'], $item['votos'], $item['percentual'],
        ])->all();
    }

    /** @return array<int,array{0:string,1:mixed}> */
    private function auditoriaLinhas(array $a): array
    {
        $simNao = fn ($valor) => $valor === null ? 'Não aplicável' : ($valor ? 'Sim' : 'Não');

        return [
            ['Resultado municipal localizado', $simNao($a['tem_resultado_municipal'])],
            ['Municípios com linha para a candidatura', $a['municipios_com_linha']],
            ['Soma dos votos municipais', $a['soma_municipios']],
            ['Votos totais da candidatura', $a['votos_total_candidatura']],
            ['Escopo permite conferir soma municipal x total', $simNao($a['escopo_total_comparavel'])],
            ['Soma municipal confere com total', $simNao($a['municipios_conferem_total'])],
            ['Zonas localizadas nesta cidade', $a['zonas_na_cidade']],
            ['Soma das zonas nesta cidade', $a['soma_zonas_cidade']],
            ['Soma das zonas confere com município', $simNao($a['zonas_conferem_municipio'])],
            ['Município com código IBGE', $simNao($a['ibge_identificado'])],
            ['Registro oficial TSE', $simNao($a['fonte_oficial_tse'])],
        ];
    }

    /** @return array<int,array{0:string,1:string}> */
    private function metodologiaLinhas(): array
    {
        return [
            ['Escopo', 'Relatório restrito ao município e ao candidato selecionado no Espelho Inteligente.'],
            ['Ausência de dados', 'Quando não existe linha oficial para o município ou ano, o relatório exibe ausência de linha; não converte a ausência em zero.'],
            ['Histórico', 'Compara apenas candidaturas do mesmo político e do mesmo cargo. Delta só é calculado quando os dois pleitos possuem linha municipal.'],
            ['Zonas', 'A distribuição por zona é exibida somente quando existem resultados oficiais por zona vinculados ao município.'],
            ['Ranking', 'O ranking serve como contexto do mesmo cargo e eleição; o candidato selecionado é destacado mesmo se estiver fora do Top 20.'],
            ['Operacional', 'Presidente local, indicação, filiados, metas e observações são dados internos separados dos resultados oficiais.'],
            ['Auditoria', 'Conferências de soma são indicadores de integridade local; em candidaturas de abrangência BR a soma municipal armazenada localmente não é comparada automaticamente com o total nacional.'],
        ];
    }
}
