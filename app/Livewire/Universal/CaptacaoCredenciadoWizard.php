<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\Unp\Presidio;
use App\Models\Universal\{Bloco, CaptacaoCredenciado, Categoria, Igreja, Regiao};
use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Validation\{Rule, ValidationException};
use Livewire\{Component, WithFileUploads};

class CaptacaoCredenciadoWizard extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;
    public int $totalSteps = 7;
    public $nome, $celular, $telefone, $email, $cpf, $profissao, $aptidoes;
    public $bloco_id, $regiao_id, $igreja_id, $categoria_id, $cargo_id, $grupo_id;
    public $estado_id, $cidade_id, $endereco, $bairro, $cep;
    public $trabalho = [], $batismo = [], $preso = [], $conversao, $obra, $testemunho;
    public $foto, $identidade_frente, $identidade_verso;
    public array $credenciais = [];
    public bool $aceitePrivacidade = false;
    public string $errorMessage = '';
    public $allBlocos, $allEstados, $allCategorias, $allCargos, $allGrupos, $allPresidios;
    public $regiaos = [], $igrejas = [], $cidades = [];

    public function mount(): void
    {
        $this->allBlocos = Bloco::orderBy('nome')->get(['id', 'nome']);
        $this->allEstados = Estado::orderBy('nome')->get(['id', 'nome']);
        $this->allCategorias = Categoria::orderBy('nome')->get(['id', 'nome']);
        $this->allCargos = Cargo::orderBy('nome')->get(['id', 'nome']);
        $this->allGrupos = Grupo::orderBy('nome')->get(['id', 'nome']);
        $this->allPresidios = Presidio::orderBy('nome')->get(['id', 'nome']);
        $this->addCredencial();
    }

    public function updatedBlocoId($value): void
    {
        $this->regiaos = $value ? Regiao::where('bloco_id', $value)->orderBy('nome')->get(['id', 'nome']) : [];
        $this->igrejas = [];
        $this->reset('regiao_id', 'igreja_id');
    }

    public function updatedRegiaoId($value): void
    {
        $this->igrejas = $value ? Igreja::where('regiao_id', $value)->orderBy('nome')->get(['id', 'nome']) : [];
        $this->reset('igreja_id');
    }

    public function updatedEstadoId($value): void
    {
        $this->cidades = $value ? Cidade::where('estado_id', $value)->orderBy('nome')->get(['id', 'nome']) : [];
        $this->reset('cidade_id');
    }

    public function addCredencial(): void
    {
        if (count($this->credenciais) < 10) {
            $this->credenciais[] = [
                'presidio_id' => '', 'unidade_nao_faz' => false,
                'data_primeira_credencial' => null, 'data_renovacao' => null, 'data_vencimento' => null,
                'foto_frente' => null, 'foto_verso' => null,
            ];
        }
    }

    public function removeCredencial(int $index): void
    {
        unset($this->credenciais[$index]);
        $this->credenciais = array_values($this->credenciais);
    }

    public function nextStep(): void
    {
        $this->validateStep();
        $this->currentStep = min($this->totalSteps, $this->currentStep + 1);
        $this->errorMessage = '';
    }

    public function prevStep(): void
    {
        $this->currentStep = max(1, $this->currentStep - 1);
        $this->errorMessage = '';
    }

    public function validateStep(): void
    {
        $this->normalizar();
        $this->validate($this->rulesForStep($this->currentStep), [], $this->attributes());

        if ($this->currentStep === 2) {
            $this->validarCpf();
        }
    }

    public function submit()
    {
        $this->normalizar();
        $this->validate($this->allRules(), [], $this->attributes());
        $this->validarCpf();
        $this->validarPresidiosDuplicados();
        $arquivosNovos = [];

        try {
            $credenciaisFinal = [];
            foreach ($this->credenciais as $credencial) {
                if (empty($credencial['presidio_id'])) {
                    continue;
                }

                $credenciaisFinal[] = [
                    'presidio_id' => $credencial['presidio_id'],
                    'unidade_nao_faz' => (bool) ($credencial['unidade_nao_faz'] ?? false),
                    'data_primeira_credencial' => $credencial['data_primeira_credencial'] ?: null,
                    'data_renovacao' => $credencial['data_renovacao'] ?: null,
                    'data_vencimento' => $credencial['data_vencimento'] ?: null,
                    'foto_frente' => $this->armazenar($credencial['foto_frente'] ?? null, $arquivosNovos),
                    'foto_verso' => $this->armazenar($credencial['foto_verso'] ?? null, $arquivosNovos),
                ];
            }

            $foto = $this->armazenar($this->foto, $arquivosNovos);
            $idFrente = $this->armazenar($this->identidade_frente, $arquivosNovos);
            $idVerso = $this->armazenar($this->identidade_verso, $arquivosNovos);

            DB::transaction(function () use ($credenciaisFinal, $foto, $idFrente, $idVerso): void {
                CaptacaoCredenciado::create([
                    'nome' => $this->nome, 'celular' => $this->celular,
                    'telefone' => $this->telefone ?: null, 'email' => $this->email ?: null, 'cpf' => $this->cpf,
                    'bloco_id' => $this->bloco_id, 'regiao_id' => $this->regiao_id, 'igreja_id' => $this->igreja_id,
                    'categoria_id' => $this->categoria_id, 'cargo_id' => $this->cargo_id,
                    'grupo_id' => $this->grupo_id ?: null, 'estado_id' => $this->estado_id, 'cidade_id' => $this->cidade_id,
                    'endereco' => $this->endereco, 'bairro' => $this->bairro, 'cep' => $this->cep ?: null,
                    'profissao' => $this->profissao ?: null, 'aptidoes' => $this->aptidoes ?: null,
                    'conversao' => $this->conversao ?: null, 'obra' => $this->obra ?: null,
                    'testemunho' => $this->testemunho ?: null, 'trabalho' => $this->trabalho,
                    'batismo' => $this->batismo, 'preso' => $this->preso,
                    'foto' => $foto, 'identidade_frente' => $idFrente, 'identidade_verso' => $idVerso,
                    'credenciais_payload' => $credenciaisFinal, 'status' => 'pendente',
                ]);
            });

            return redirect()->route('captacao.sucesso');
        } catch (\Throwable $exception) {
            Storage::disk('public_disk')->delete($arquivosNovos);
            Log::error('Falha ao registrar captação de credenciado.', [
                'exception' => $exception, 'cpf_final' => substr($this->cpf, -4),
            ]);
            $this->errorMessage = 'Não foi possível concluir o envio. Revise os dados e tente novamente.';
        }
    }

    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'bloco_id' => ['required', 'integer', 'exists:blocos,id'],
                'regiao_id' => ['required', 'integer', Rule::exists('regiaos', 'id')->where('bloco_id', $this->bloco_id)],
                'igreja_id' => ['required', 'integer', Rule::exists('igrejas', 'id')->where('regiao_id', $this->regiao_id)],
                'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
                'cargo_id' => ['required', 'integer', 'exists:cargos,id'],
                'grupo_id' => ['nullable', 'integer', 'exists:grupos,id'],
            ],
            2 => [
                'nome' => ['required', 'string', 'min:3', 'max:255'],
                'cpf' => ['required', 'digits:11', 'unique:captacao_credenciados,cpf'],
                'celular' => ['required', 'digits_between:10,11'],
                'telefone' => ['nullable', 'digits_between:10,11'],
                'email' => ['nullable', 'email:rfc', 'max:255'],
            ],
            3 => [
                'estado_id' => ['required', 'integer', 'exists:estados,id'],
                'cidade_id' => ['required', 'integer', Rule::exists('cidades', 'id')->where('estado_id', $this->estado_id)],
                'endereco' => ['required', 'string', 'min:3', 'max:255'],
                'bairro' => ['required', 'string', 'min:2', 'max:255'],
                'cep' => ['nullable', 'digits:8'],
            ],
            4 => ['profissao' => ['nullable', 'string', 'max:255'], 'aptidoes' => ['nullable', 'string', 'max:2000']],
            5 => [
                'conversao' => ['nullable', 'date'], 'obra' => ['nullable', 'date'],
                'testemunho' => ['nullable', 'string', 'max:5000'], 'trabalho' => ['array'],
                'batismo' => ['array'], 'batismo.*' => ['in:aguas,espirito'],
                'preso' => ['array'], 'preso.*' => ['in:Sim,Não'],
            ],
            6 => [
                'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'identidade_frente' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'identidade_verso' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ],
            7 => [
                'credenciais' => ['array', 'max:10'],
                'credenciais.*.presidio_id' => ['nullable', 'integer', 'exists:presidios,id'],
                'credenciais.*.unidade_nao_faz' => ['boolean'],
                'credenciais.*.data_primeira_credencial' => ['nullable', 'date'],
                'credenciais.*.data_renovacao' => ['nullable', 'date'],
                'credenciais.*.data_vencimento' => ['nullable', 'date'],
                'credenciais.*.foto_frente' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'credenciais.*.foto_verso' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'aceitePrivacidade' => ['accepted'],
            ],
            default => [],
        };
    }

    private function allRules(): array
    {
        return array_merge(...array_map(fn (int $step) => $this->rulesForStep($step), range(1, 7)));
    }

    private function normalizar(): void
    {
        $this->nome = trim((string) $this->nome);
        $this->cpf = preg_replace('/\D+/', '', (string) $this->cpf);
        $this->celular = preg_replace('/\D+/', '', (string) $this->celular);
        $this->telefone = preg_replace('/\D+/', '', (string) $this->telefone);
        $this->cep = preg_replace('/\D+/', '', (string) $this->cep);
        $this->email = mb_strtolower(trim((string) $this->email));
    }

    private function validarCpf(): void
    {
        $cpf = $this->cpf;
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            throw ValidationException::withMessages(['cpf' => 'Informe um CPF válido.']);
        }

        for ($digito = 9; $digito < 11; $digito++) {
            $soma = 0;
            for ($indice = 0; $indice < $digito; $indice++) {
                $soma += (int) $cpf[$indice] * (($digito + 1) - $indice);
            }
            if ((int) $cpf[$digito] !== ((10 * $soma) % 11) % 10) {
                throw ValidationException::withMessages(['cpf' => 'Informe um CPF válido.']);
            }
        }
    }

    private function validarPresidiosDuplicados(): void
    {
        $ids = collect($this->credenciais)->pluck('presidio_id')->filter();
        if ($ids->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['credenciais' => 'Cada presídio deve ser informado apenas uma vez.']);
        }
    }

    private function armazenar($arquivo, array &$arquivosNovos): ?string
    {
        if (!$arquivo) {
            return null;
        }

        $caminho = $arquivo->store('credenciados/captura', 'public_disk');
        $arquivosNovos[] = $caminho;
        return $caminho;
    }

    private function attributes(): array
    {
        return [
            'bloco_id' => 'bloco', 'regiao_id' => 'região', 'igreja_id' => 'igreja',
            'categoria_id' => 'categoria', 'cargo_id' => 'cargo', 'estado_id' => 'estado',
            'cidade_id' => 'cidade', 'identidade_frente' => 'frente da identidade',
            'identidade_verso' => 'verso da identidade', 'aceitePrivacidade' => 'aceite de privacidade',
        ];
    }

    public function render()
    {
        return view('livewire.universal.captacao-credenciado-wizard', [
            'progressPercentage' => (int) round((($this->currentStep - 1) / $this->totalSteps) * 100),
            'stepTitles' => ['Igreja', 'Dados pessoais', 'Endereço', 'Perfil', 'Histórico', 'Documentos', 'Credenciais'],
        ])->layout('layouts.guest');
    }
}
