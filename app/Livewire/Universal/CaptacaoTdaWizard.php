<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Universal\Bloco;
use App\Models\Universal\CaptacaoTda;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use App\Models\Universal\TdaResponsavelLegal;
use App\Models\Universal\TdaTermoAceite;
use Carbon\Carbon;
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
    public int $totalSteps = 14;
    public bool $enviado = false;
    public bool $lgpd_aceito = false;
    public bool $aceite_adesao = false;
    public bool $aceite_imagem_voz = false;
    public bool $aceite_uniforme = false;
    public string $formToken = '';

    public $bloco_id, $regiao_id, $igreja_id, $estado_id, $cidade_id;
    public $data_ingresso_grupo, $funcao_grupo, $foto, $endereco_igreja;
    public $nome, $nacionalidade = 'Brasileira', $data_nascimento, $estado_civil, $rg, $cpf, $celular;
    public $facebook, $instagram, $endereco, $numero, $complemento, $cep, $bairro, $email;
    public $assinatura, $testemunha_nome, $testemunha_rg, $testemunha_assinatura;
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
    public $responsavel_nome, $responsavel_nacionalidade = 'Brasileira', $responsavel_estado_civil;
    public $responsavel_profissao, $responsavel_rg, $responsavel_cpf, $responsavel_endereco;
    public $responsavel_numero, $responsavel_complemento, $responsavel_bairro, $responsavel_cep;
    public $responsavel_estado_id, $responsavel_cidade_id, $responsavel_data_nascimento;
    public $responsavelCidades = [];

    public function mount(): void
    {
        $this->formToken = (string) Str::uuid();
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allEstados = Estado::orderBy('nome')->get();

        if ($bahia = $this->allEstados->firstWhere('uf', 'BA')) {
            $this->estado_id = $bahia->id;
            $this->cidades = Cidade::where('estado_id', $bahia->id)->orderBy('nome')->get();
            $this->cidade_id = optional($this->cidades->firstWhere('nome', 'Salvador'))->id;
            $this->responsavel_estado_id = $bahia->id;
            $this->responsavelCidades = $this->cidades;
            $this->responsavel_cidade_id = $this->cidade_id;
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
                'endereco_igreja' => ['required', 'string', 'max:255'],
                'data_ingresso_grupo' => ['nullable', 'date', 'before_or_equal:today'],
                'funcao_grupo' => ['required', Rule::in(['Membro', 'Secretaria', 'Mídia', 'Obreiro'])],
            ],
            3 => [
                'nome' => ['required', 'string', 'min:3', 'max:255'],
                'nacionalidade' => ['required', 'string', 'max:100'],
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
                'complemento' => ['nullable', 'string', 'max:100'],
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
            9 => ['aceite_adesao' => ['accepted']],
            10 => array_merge(['aceite_imagem_voz' => ['accepted']], $this->menorDeIdade() ? $this->responsavelRules() : []),
            11 => ['aceite_uniforme' => ['accepted']],
            12 => ['assinatura' => ['required', 'string']],
            13 => [
                'testemunha_nome' => ['required', 'string', 'min:3', 'max:255'],
                'testemunha_rg' => ['required', 'string', 'max:30'],
                'testemunha_assinatura' => ['required', 'string'],
            ],
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
            'assinatura.required' => 'Faça sua assinatura para continuar.',
            'testemunha_assinatura.required' => 'A testemunha deve assinar para continuar.',
            'aceite_adesao.accepted' => 'É necessário aceitar o Termo de Adesão.',
            'aceite_imagem_voz.accepted' => 'É necessário aceitar o Termo de Imagem e Voz.',
            'aceite_uniforme.accepted' => 'É necessário aceitar o Termo de Uniforme.',
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

    public function updatedResponsavelEstadoId($value): void
    {
        $this->responsavelCidades = $value ? Cidade::where('estado_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('responsavel_cidade_id');
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
        if (in_array($this->step, [9, 10, 11], true)) {
            return;
        }
        $this->validate();
        if ($this->step < $this->totalSteps) $this->step++;
    }

    public function aceitarTermo(string $tipo): void
    {
        $mapa = [
            TdaTermoAceite::ADESAO => ['step' => 9, 'campo' => 'aceite_adesao'],
            TdaTermoAceite::IMAGEM_VOZ => ['step' => 10, 'campo' => 'aceite_imagem_voz'],
            TdaTermoAceite::UNIFORME => ['step' => 11, 'campo' => 'aceite_uniforme'],
        ];

        abort_unless(isset($mapa[$tipo]) && $this->step === $mapa[$tipo]['step'], 422);
        if ($tipo === TdaTermoAceite::IMAGEM_VOZ && $this->menorDeIdade()) {
            $this->validate($this->responsavelRules());
        }

        $campo = $mapa[$tipo]['campo'];
        $this->{$campo} = true;
        session()->put($this->chaveAceites().'.'.$tipo, now()->toIso8601String());
        $this->step++;
    }

    public function menorDeIdade(): bool
    {
        if (blank($this->data_nascimento)) return false;
        try {
            return Carbon::parse($this->data_nascimento)->age < 18;
        } catch (\Throwable) {
            return false;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function submit(): void
    {
        if ($this->step !== 14 || $this->enviado) return;

        $limiteChave = 'captacao-tda:envio:'.request()->ip();
        if (RateLimiter::tooManyAttempts($limiteChave, 3)) {
            $segundos = RateLimiter::availableIn($limiteChave);
            session()->flash('error', "Muitas tentativas. Aguarde {$segundos} segundos e tente novamente.");
            return;
        }

        for ($etapa = 1; $etapa <= 13; $etapa++) {
            $this->step = $etapa;
            $this->validate();
        }
        $this->step = 14;

        $aceites = session()->get($this->chaveAceites(), []);
        $tiposObrigatorios = [TdaTermoAceite::ADESAO, TdaTermoAceite::IMAGEM_VOZ, TdaTermoAceite::UNIFORME];
        if (collect($tiposObrigatorios)->contains(fn ($tipo) => empty($aceites[$tipo]))) {
            session()->flash('error', 'A sessão dos termos expirou. Volte e aceite novamente os três documentos.');
            return;
        }

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
        $assinaturaArmazenada = null;
        $assinaturaTestemunhaArmazenada = null;

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
            [$assinaturaArmazenada, $hashAssinatura] = $this->armazenarAssinatura();
            $dados['assinatura'] = $assinaturaArmazenada;
            [$assinaturaTestemunhaArmazenada, $hashAssinaturaTestemunha] = $this->armazenarImagemAssinatura(
                $this->testemunha_assinatura,
                'tda/captacao/assinaturas-testemunhas'
            );
            $dados['testemunha_nome'] = Str::title(trim((string) $this->testemunha_nome));
            $dados['testemunha_rg'] = trim((string) $this->testemunha_rg);
            $dados['testemunha_assinatura'] = $assinaturaTestemunhaArmazenada;

            if ($this->sexo === 'feminino') {
                $dados['intellimen_reunioes'] = $dados['intellimen_desafios'] = null;
            } else {
                $dados['godllywood_autoajuda'] = $dados['meditacao_univer'] = null;
            }

            DB::transaction(function () use ($dados, $aceites, $tiposObrigatorios, $hashAssinatura, $hashAssinaturaTestemunha): void {
                $captacao = CaptacaoTda::create($dados);

                if ($this->menorDeIdade()) {
                    $captacao->responsavelLegal()->create($this->dadosResponsavel());
                }

                $snapshot = $this->dadosSnapshot($dados, $hashAssinaturaTestemunha);
                foreach ($tiposObrigatorios as $tipo) {
                    $termo = config("tda.termos.{$tipo}");
                    $captacao->termosAceitos()->create([
                        'tipo' => $tipo,
                        'versao' => $termo['versao'],
                        'hash_documento' => $termo['hash_documento'],
                        'hash_assinatura' => $hashAssinatura,
                        'aceito_em' => Carbon::parse($aceites[$tipo]),
                        'ip_hash' => hash_hmac('sha256', (string) request()->ip(), (string) config('app.key')),
                        'user_agent' => Str::limit((string) request()->userAgent(), 500, ''),
                        'dados_snapshot' => $snapshot,
                    ]);
                }
            });
            session()->forget($this->chaveAceites());
            $this->enviado = true;
        } catch (\Throwable $e) {
            if ($fotoArmazenada) {
                Storage::disk('public_disk')->delete($fotoArmazenada);
            }
            if ($assinaturaArmazenada) {
                Storage::disk('public_disk')->delete($assinaturaArmazenada);
            }
            if ($assinaturaTestemunhaArmazenada) {
                Storage::disk('public_disk')->delete($assinaturaTestemunhaArmazenada);
            }

            Log::error('Erro no cadastro público TDA', [
                'tipo' => $e::class,
                'mensagem' => $e->getMessage(),
            ]);
            session()->flash('error', 'Não foi possível enviar o cadastro. Tente novamente.');
        }
    }

    private function responsavelRules(): array
    {
        return [
            'responsavel_nome' => ['required', 'string', 'min:3', 'max:255'],
            'responsavel_nacionalidade' => ['required', 'string', 'max:100'],
            'responsavel_estado_civil' => [
                'required',
                Rule::in(['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel']),
            ],
            'responsavel_profissao' => ['required', 'string', 'max:255'],
            'responsavel_rg' => ['required', 'string', 'max:30'],
            'responsavel_cpf' => [
                'required', 'string', 'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $this->cpfValido((string) $value)) $fail('Informe um CPF válido para o responsável.');
                },
            ],
            'responsavel_endereco' => ['required', 'string', 'max:255'],
            'responsavel_numero' => ['required', 'string', 'max:30'],
            'responsavel_complemento' => ['nullable', 'string', 'max:100'],
            'responsavel_bairro' => ['required', 'string', 'max:255'],
            'responsavel_cep' => ['required', 'string', 'max:10'],
            'responsavel_estado_id' => ['required', 'exists:estados,id'],
            'responsavel_cidade_id' => [
                'required',
                Rule::exists('cidades', 'id')->where(
                    fn ($query) => $query->where('estado_id', $this->responsavel_estado_id)
                ),
            ],
            'responsavel_data_nascimento' => ['required', 'date', 'before:today'],
        ];
    }

    private function dadosResponsavel(): array
    {
        $dados = [];
        foreach ((new TdaResponsavelLegal())->getFillable() as $campo) {
            $propriedade = 'responsavel_'.$campo;
            if (property_exists($this, $propriedade)) $dados[$campo] = $this->{$propriedade};
        }
        $dados['nome'] = Str::title(trim((string) $this->responsavel_nome));
        $dados['cpf'] = preg_replace('/\D+/', '', (string) $this->responsavel_cpf);
        return $dados;
    }

    private function armazenarAssinatura(): array
    {
        return $this->armazenarImagemAssinatura($this->assinatura, 'tda/captacao/assinaturas');
    }

    private function armazenarImagemAssinatura(mixed $assinatura, string $diretorio): array
    {
        if (! preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=\r\n]+)$/', (string) $assinatura, $partes)) {
            throw new \RuntimeException('Formato de assinatura inválido.');
        }
        $binario = base64_decode($partes[1], true);
        if ($binario === false || strlen($binario) > 2 * 1024 * 1024 || @getimagesizefromstring($binario) === false) {
            throw new \RuntimeException('Imagem de assinatura inválida.');
        }
        $caminho = $diretorio.'/'.Str::uuid().'.png';
        if (! Storage::disk('public_disk')->put($caminho, $binario)) {
            throw new \RuntimeException('Não foi possível armazenar a assinatura.');
        }
        return [$caminho, hash('sha256', $binario)];
    }

    private function dadosSnapshot(array $dados, string $hashAssinaturaTestemunha): array
    {
        $testemunha = [
            'nome' => $dados['testemunha_nome'],
            'rg' => $dados['testemunha_rg'],
            'hash_assinatura' => $hashAssinaturaTestemunha,
        ];
        unset($dados['foto'], $dados['assinatura'], $dados['testemunha_assinatura'], $dados['status']);
        return [
            'cadastro' => $dados,
            'menor_de_idade' => $this->menorDeIdade(),
            'responsavel_legal' => $this->menorDeIdade() ? $this->dadosResponsavel() : null,
            'pastor_responsavel' => config('tda.pastor_responsavel'),
            'testemunha' => $testemunha,
        ];
    }

    private function chaveAceites(): string
    {
        return 'tda.termos.'.$this->formToken;
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
