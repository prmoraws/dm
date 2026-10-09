<?php

namespace App\Services\Unp;

use App\Models\Unp\Oficios\Convidado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvidadosEmMassa
{
    private const TIPOS = [
        'cop' => [\App\Models\Unp\Oficios\OficioCop::class, 'COP'],
        'evento' => [\App\Models\Unp\Oficios\OficioEvento::class, 'Evento'],
        'formatura' => [\App\Models\Unp\OficioFormatura::class, 'Formatura'],
        'curso' => [\App\Models\Unp\Oficios\OficioCurso::class, 'Curso'],
        'trabalho' => [\App\Models\Unp\Oficios\OficioTrabalho::class, 'Trabalho'],
        'geral' => [\App\Models\Unp\Oficios\OficioGeral::class, 'Geral'],
        'credencial' => [\App\Models\Unp\Oficios\OficioCredencial::class, 'Credencial'],
    ];
    private const CLASSES = ['esposas', 'convidados', 'comunicação', 'organização'];

    public function oficios(int $presidio): array
    {
        $lista = [];
        foreach (self::TIPOS as $tipo => [$model, $label]) {
            foreach ($model::where('presidio_id', $presidio)->get() as $oficio) {
                $data = $oficio->data_oficio ?? $oficio->created_at;
                $date = $data ? Carbon::parse($data) : null;
                $assunto = $oficio->assunto ?? $oficio->evento ?? $oficio->nome ?? '';
                $lista[] = [
                    'value' => $tipo . ':' . $oficio->id,
                    'text' => ($date ? $date->format('d/m/Y') : 'Sem data') . ' — ' . $label . ' #' . $oficio->id . ($assunto ? ' — ' . $assunto : ''),
                    'ordem' => $date ? $date->getTimestamp() : 0,
                    'id' => $oficio->id,
                ];
            }
        }
        usort($lista, fn ($a, $b) => ($b['ordem'] <=> $a['ordem']) ?: ($b['id'] <=> $a['id']) ?: strcmp($a['value'], $b['value']));
        return $lista;
    }

    private function destino(int $presidio, string $chave, bool $lock = false): array
    {
        $partes = explode(':', $chave);
        if (count($partes) !== 2 || !isset(self::TIPOS[$partes[0]]) || !ctype_digit($partes[1])) {
            throw ValidationException::withMessages(['massaOficio' => 'Selecione um ofício válido.']);
        }
        [$tipo, $id] = $partes;
        $model = self::TIPOS[$tipo][0];
        $query = $model::where('presidio_id', $presidio)->whereKey((int) $id);
        if ($lock) {
            $query->lockForUpdate();
        }
        $oficio = $query->first();
        if (!$oficio) {
            throw ValidationException::withMessages(['massaOficio' => 'O ofício não pertence ao presídio selecionado ou foi excluído.']);
        }
        return [$tipo, $oficio];
    }

    private function cpfValido(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * ($t + 1 - $i);
            }
            $digito = ($soma * 10) % 11;
            if (($digito === 10 ? 0 : $digito) !== (int) $cpf[$t]) {
                return false;
            }
        }
        return true;
    }

    public function linhas(string $texto, string $classePadrao): array
    {
        if (!in_array($classePadrao, self::CLASSES, true)) {
            throw ValidationException::withMessages(['massaClasse' => 'Selecione uma classe válida.']);
        }
        if (strlen($texto) > 200000) {
            throw ValidationException::withMessages(['massaTexto' => 'A lista é muito grande. Use até 500 pessoas por vez.']);
        }
        $linhas = preg_split('/\r\n|\r|\n/', $texto);
        $pessoas = [];
        $cpfs = [];
        $erros = [];
        foreach ($linhas as $numero => $linha) {
            if (trim($linha) === '') {
                continue;
            }
            $campos = str_getcsv($linha, str_contains($linha, "\t") ? "\t" : ';', '"', '');
            $campos = array_map(fn ($v) => trim((string) $v), $campos);
            if (count($campos) < 2 || count($campos) > 4) {
                $erros[] = 'Linha ' . ($numero + 1) . ': use Nome;CPF;RG;Classe.';
                continue;
            }
            [$nome, $cpfOriginal] = $campos;
            $rg = $campos[2] ?? '';
            $classe = mb_strtolower($campos[3] ?? '') ?: $classePadrao;
            $cpf = preg_replace('/[.\-\s]/u', '', $cpfOriginal);
            if (mb_strlen($nome) < 3 || mb_strlen($nome) > 255 || mb_strlen($rg) > 255
                || !ctype_digit($cpf) || !$this->cpfValido($cpf) || !in_array($classe, self::CLASSES, true)) {
                $erros[] = 'Linha ' . ($numero + 1) . ': confira nome, CPF, RG e classe.';
                continue;
            }
            if (isset($cpfs[$cpf])) {
                $erros[] = 'Linha ' . ($numero + 1) . ': CPF repetido na lista.';
                continue;
            }
            $cpfs[$cpf] = true;
            $pessoas[] = ['nome' => mb_strtoupper($nome), 'cpf' => $cpf, 'rg' => $rg ?: null, 'classe' => $classe];
        }
        if (count($pessoas) > 500) {
            $erros[] = 'Use até 500 pessoas por vez.';
        }
        if (!$pessoas && !$erros) {
            $erros[] = 'Cole pelo menos uma pessoa.';
        }
        if ($erros) {
            throw ValidationException::withMessages(['massaTexto' => implode(' ', array_slice($erros, 0, 12))]);
        }
        return $pessoas;
    }

    private function existente(string $cpf): ?Convidado
    {
        $matches = Convidado::whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?", [$cpf])->limit(2)->get();
        if ($matches->count() > 1) {
            throw ValidationException::withMessages(['massaTexto' => 'Há cadastros duplicados para um CPF informado. Corrija-os antes de importar.']);
        }
        return $matches->first();
    }

    public function previa(int $presidio, string $chave, string $texto, string $classe): array
    {
        $this->destino($presidio, $chave);
        return array_map(function ($pessoa) {
            $existing = $this->existente($pessoa['cpf']);
            if ($existing) {
                $pessoa['nome'] = $existing->nome;
                $pessoa['rg'] = $existing->rg;
                $pessoa['classe'] = $existing->classe;
            }
            $pessoa['situacao'] = $existing ? 'Cadastro existente; dados preservados' : 'Novo cadastro';
            return $pessoa;
        }, $this->linhas($texto, $classe));
    }

    public function salvar(int $presidio, string $chave, string $texto, string $classe): array
    {
        $pessoas = $this->linhas($texto, $classe);
        return DB::transaction(function () use ($presidio, $chave, $pessoas) {
            [$tipo, $oficio] = $this->destino($presidio, $chave, true);
            $novos = 0;
            $vinculados = 0;
            $ids = [];
            foreach ($pessoas as $pessoa) {
                $convidado = $this->existente($pessoa['cpf']);
                if (!$convidado) {
                    $cpf = $pessoa['cpf'];
                    $pessoa['cpf'] = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
                    $convidado = Convidado::create($pessoa);
                    $novos++;
                }
                $ids[] = $convidado->id;
                $keys = ['oficio_tipo' => $tipo, 'oficio_id' => $oficio->id, 'convidado_id' => $convidado->id];
                if (!DB::table('unp_oficio_convidados')->where($keys)->exists()) {
                    DB::table('unp_oficio_convidados')->insert($keys + ['created_at' => now(), 'updated_at' => now()]);
                    $vinculados++;
                }
            }
            if ($tipo === 'cop') {
                $oficio->convidados = array_values(array_unique(array_map('intval', array_merge($oficio->convidados ?? [], $ids))));
                $oficio->save();
            }
            return ['novos' => $novos, 'vinculados' => $vinculados, 'total' => count($pessoas)];
        }, 3);
    }

    public function convidados(int $presidio, string $chave)
    {
        [$tipo, $oficio] = $this->destino($presidio, $chave);
        $ids = DB::table('unp_oficio_convidados')->where('oficio_tipo', $tipo)->where('oficio_id', $oficio->id)->pluck('convidado_id')->all();
        if ($tipo === 'cop') {
            $ids = array_merge($ids, $oficio->convidados ?? []);
        }
        return Convidado::whereIn('id', $ids)->orderBy('nome')->get();
    }
}
