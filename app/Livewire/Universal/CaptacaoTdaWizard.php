<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Universal\Bloco;
use App\Models\Universal\CaptacaoTda;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CaptacaoTdaWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 9;
    public bool $enviado = false;
    public bool $lgpd_aceito = false;

    public $bloco_id, $regiao_id, $igreja_id, $estado_id, $cidade_id;
    public $data_ingresso_grupo, $funcao_grupo, $foto;
    public $nome, $data_nascimento, $estado_civil, $rg, $cpf, $celular;
    public $facebook, $instagram, $endereco, $numero, $cep, $bairro, $email;
    public $escolaridade, $profissao, $tem_filhos, $quantidade_filhos, $idade_filhos;
    public $emergencia_nome, $emergencia_celular, $emergencia_facebook, $emergencia_instagram;
    public $condicao_atual, $inicio_iurd, $batizado_aguas, $data_batismo_aguas;
    public $batizado_espirito_santo, $data_batismo_espirito_santo, $ja_se_afastou;
    public array $dias_reunioes = [], $dias_evangelizacao = [], $dias_trabalho_reuniao = [];
    public $sexo, $godllywood_autoajuda, $meditacao_univer;
    public $intellimen_reunioes, $intellimen_desafios;
    public bool $colaborador = false, $obreiro = false, $levita = false;
    public $data_graduacao_colaborador, $data_graduacao_obreiro, $data_graduacao_levita;

    public $allBlocos, $allEstados;
    public $regiaos = [], $igrejas = [], $cidades = [];

    public function mount(): void
    {
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allEstados = Estado::orderBy('nome')->get();

        if ($bahia = $this->allEstados->firstWhere('uf', 'BA')) {
            $this->estado_id = $bahia->id;
            $this->cidades = Cidade::where('estado_id', $bahia->id)->orderBy('nome')->get();
            $this->cidade_id = optional($this->cidades->firstWhere('nome', 'Salvador'))->id;
        }
    }

    protected function rules(): array
    {
        return match ($this->step) {
            1 => ['lgpd_aceito' => ['accepted']],
            2 => [
                'bloco_id' => ['required', 'exists:blocos,id'],
                'regiao_id' => [
                    'required',
                    Rule::exists('regiaos', 'id')->where(
                        fn ($query) => $query->where('bloco_id', $this->bloco_id)
                    ),
                ],
                'igreja_id' => [
                    'required',
                    Rule::exists('igrejas', 'id')->where(
                        fn ($query) => $query->where('regiao_id', $this->regiao_id)
                    ),
                ],
                'data_ingresso_grupo' => ['nullable', 'date', 'before_or_equal:today'],
                'funcao_grupo' => ['required', 'string', 'max:255'],
            ],
            3 => [
                'nome' => ['required', 'string', 'min:3', 'max:255'],
                'data_nascimento' => ['required', 'date', 'before:today'],
                'estado_civil' => ['required', Rule::in(['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'])],
                'rg' => ['nullable', 'string', 'max:30'],
                'cpf' => [
                    'required',
                    'string',
                    'max:20',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        if (! $this->cpfValido((string) $value)) {
                            $fail('Informe um CPF válido.');
                        }
                    },
                ],
                'sexo' => ['required', Rule::in(['feminino', 'masculino'])],
                'foto' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            ],
            4 => [
                'celular' => [
                    'required',
                    'string',
                    'max:20',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $numero = preg_replace('/\D+/', '', (string) $value);
                        if (! in_array(strlen($numero), [10, 11], true)) {
                            $fail('Informe um celular com DDD válido.');
                        }
                    },
                ],
                'email' => ['nullable', 'email', 'max:255'],
                'facebook' => ['nullable', 'string', 'max:255'],
                'instagram' => ['nullable', 'string', 'max:255'],
                'estado_id' => ['required', 'exists:estados,id'],
                'cidade_id' => [
                    'required',
                    Rule::exists('cidades', 'id')->where(
                        fn ($query) => $query->where('estado_id', $this->estado_id)
                    ),
                ],
                'endereco' => ['required', 'string', 'max:255'],
                'numero' => ['required', 'string', 'max:30'],
                'cep' => ['nullable', 'string', 'max:10'],
                'bairro' => ['required', 'string', 'max:255'],
            ],
            5 => [
                'escolaridade' => ['required', 'string', 'max:255'],
                'profissao' => ['nullable', 'string', 'max:255'],
                'tem_filhos' => ['required', 'boolean'],
                'quantidade_filhos' => ['nullable', 'required_if:tem_filhos,1', 'integer', 'min:1', 'max:30'],
                'idade_filhos' => ['nullable', 'required_if:tem_filhos,1', 'string', 'max:255'],
                'emergencia_nome' => ['required', 'string', 'max:255'],
                'emergencia_celular' => [
                    'required',
                    'string',
                    'max:20',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $numero = preg_replace('/\D+/', '', (string) $value);
                        if (! in_array(strlen($numero), [10, 11], true)) {
                            $fail('Informe um celular de emergência com DDD válido.');
                        }
                    },
                ],
                'emergencia_facebook' => ['nullable', 'string', 'max:255'],
                'emergencia_instagram' => ['nullable', 'string', 'max:255'],
            ],
            6 => [
                'condicao_atual' => ['required', Rule::in(['membro', 'cpo', 'colaborador', 'obreiro', 'levita', 'auxiliar'])],
                'inicio_iurd' => ['required', 'date', 'before_or_equal:today'],
                'batizado_aguas' => ['required', 'boolean'],
                'data_batismo_aguas' => ['nullable', 'required_if:batizado_aguas,1', 'date', 'before_or_equal:today'],
                'batizado_espirito_santo' => ['required', 'boolean'],
                'data_batismo_espirito_santo' => ['nullable', 'required_if:batizado_espirito_santo,1', 'date', 'before_or_equal:today'],
                'ja_se_afastou' => ['required', 'boolean'],
            ],
            7 => [
                'dias_reunioes' => ['required', 'array', 'min:1'],
                'dias_reunioes.*' => [Rule::in($this->diasValidos())],
                'dias_evangelizacao' => ['required', 'array', 'min:1'],
                'dias_evangelizacao.*' => [Rule::in($this->diasValidos())],
            ],
            8 => array_merge([
                'colaborador' => ['boolean'], 'obreiro' => ['boolean'], 'levita' => ['boolean'],
                'data_graduacao_colaborador' => ['nullable', 'required_if:colaborador,1', 'date', 'before_or_equal:today'],
                'data_graduacao_obreiro' => ['nullable', 'required_if:obreiro,1', 'date', 'before_or_equal:today'],
                'data_graduacao_levita' => ['nullable', 'required_if:levita,1', 'date', 'before_or_equal:today'],
                'dias_trabalho_reuniao' => ['nullable', 'array'],
                'dias_trabalho_reuniao.*' => [Rule::in($this->diasValidos())],
            ], $this->sexo === 'feminino' ? [
                'godllywood_autoajuda' => ['required', 'boolean'],
                'meditacao_univer' => ['required', 'boolean'],
            ] : [
                'intellimen_reunioes' => ['required', 'boolean'],
                'intellimen_desafios' => ['required', 'boolean'],
            ]),
            default => [],
        };
    }

    protected function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'required_if' => 'O campo :attribute é obrigatório para a opção selecionada.',
            'lgpd_aceito.accepted' => 'É necessário aceitar a Política de Privacidade para continuar.',
            'foto.image' => 'A foto deve ser uma imagem válida.',
            'foto.mimes' => 'Envie a foto em JPG, JPEG ou PNG.',
            'foto.max' => 'A foto pode ter no máximo 5 MB.',
        ];
    }

    public function updatedBlocoId($value): void
    {
        $this->regiaos = $value ? Regiao::where('bloco_id', $value)->orderBy('nome')->get() : collect();
        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
    }

    public function updatedRegiaoId($value): void
    {
        $this->igrejas = $value ? Igreja::where('regiao_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('igreja_id');
    }

    public function updatedEstadoId($value): void
    {
        $this->cidades = $value ? Cidade::where('estado_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('cidade_id');
    }

    public function updatedTemFilhos($value): void
    {
        if (! $value) $this->reset(['quantidade_filhos', 'idade_filhos']);
    }

    public function updatedBatizadoAguas($value): void
    {
        if (! $value) $this->reset('data_batismo_aguas');
    }

    public function updatedBatizadoEspiritoSanto($value): void
    {
        if (! $value) $this->reset('data_batismo_espirito_santo');
    }

    public function nextStep(): void
    {
        $this->validate();
        if ($this->step < $this->totalSteps) $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function submit(): void
    {
        if ($this->step !== 9 || $this->enviado) return;

        $limiteChave = 'captacao-tda:envio:'.request()->ip();
        if (RateLimiter::tooManyAttempts($limiteChave, 3)) {
            $segundos = RateLimiter::availableIn($limiteChave);
            session()->flash('error', "Muitas tentativas. Aguarde {$segundos} segundos e tente novamente.");
            return;
        }

        for ($etapa = 1; $etapa <= 8; $etapa++) {
            $this->step = $etapa;
            $this->validate();
        }
        $this->step = 9;

        $cpf = preg_replace('/\D+/', '', (string) $this->cpf);
        $celular = preg_replace('/\D+/', '', (string) $this->celular);
        RateLimiter::hit($limiteChave, 15 * 60);

        if (CaptacaoTda::where('status', 'pendente')->where(function ($q) use ($cpf, $celular) {
            $q->where('cpf', $cpf)->orWhere('celular', $celular);
        })->exists()) {
            $this->addError('cpf', 'Já existe uma solicitação pendente com este CPF ou celular.');
            return;
        }

        $fotoArmazenada = null;

        try {
            $dados = [];
            foreach ((new CaptacaoTda())->getFillable() as $campo) {
                if (property_exists($this, $campo)) {
                    $dados[$campo] = $this->{$campo};
                }
            }
            $dados['nome'] = Str::title(trim($this->nome));
            $dados['cpf'] = $cpf;
            $dados['celular'] = $celular;
            $dados['email'] = filled($this->email) ? Str::lower(trim($this->email)) : null;
            $dados['status'] = 'pendente';
            $fotoArmazenada = $this->foto->store('tda/captacao', 'public_disk');
            $dados['foto'] = $fotoArmazenada;

            if ($this->sexo === 'feminino') {
                $dados['intellimen_reunioes'] = $dados['intellimen_desafios'] = null;
            } else {
                $dados['godllywood_autoajuda'] = $dados['meditacao_univer'] = null;
            }

            DB::transaction(fn () => CaptacaoTda::create($dados));
            $this->enviado = true;
        } catch (\Throwable $e) {
            if ($fotoArmazenada) {
                Storage::disk('public_disk')->delete($fotoArmazenada);
            }

            Log::error('Erro no cadastro público TDA', [
                'tipo' => $e::class,
                'mensagem' => $e->getMessage(),
            ]);
            session()->flash('error', 'Não foi possível enviar o cadastro. Tente novamente.');
        }
    }

    private function diasValidos(): array
    {
        return ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
    }

    private function cpfValido(string $cpf): bool
    {
        $cpf = preg_replace('/\D+/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digito = 9; $digito < 11; $digito++) {
            $soma = 0;
            for ($indice = 0; $indice < $digito; $indice++) {
                $soma += ((int) $cpf[$indice]) * (($digito + 1) - $indice);
            }

            $verificador = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$digito] !== $verificador) {
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        return view('livewire.universal.captacao-tda-wizard')->layout('layouts.guest');
    }
}
