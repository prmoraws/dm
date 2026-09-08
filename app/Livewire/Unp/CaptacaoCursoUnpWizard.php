<?php

namespace App\Livewire\Unp;

use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Universal\Bloco;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CaptacaoCursoUnpWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 5;
    public bool $enviado = false;
    public bool $lgpd_aceito = false;

    public $bloco_id = null;
    public $regiao_id = null;
    public $igreja_id = null;
    public $foto = null;
    public string $nome = '';
    public string $celular = '';
    public bool $batizado_aguas = false;
    public $data_batismo_aguas = null;
    public bool $batizado_espirito_santo = false;
    public $data_batismo_espirito_santo = null;
    public string $estado_civil = '';
    public bool $casado_civil = false;
    public bool $casado_igreja = false;
    public string $endereco_completo = '';
    public $mes_ingresso_igreja = null;
    public $ano_ingresso_igreja = null;
    public ?string $protocolo = null;

    public $blocos;
    public $regioes;
    public $igrejas;

    public function mount(): void
    {
        $this->blocos = Bloco::query()->orderBy('nome')->get();
        $this->regioes = collect();
        $this->igrejas = collect();
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
            ],
            3 => [
                'nome' => ['required', 'string', 'min:3', 'max:255'],
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
                'foto' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            ],
            4 => [
                'batizado_aguas' => ['required', 'boolean'],
                'data_batismo_aguas' => ['nullable', 'required_if:batizado_aguas,1', 'date', 'before_or_equal:today'],
                'batizado_espirito_santo' => ['required', 'boolean'],
                'data_batismo_espirito_santo' => ['nullable', 'required_if:batizado_espirito_santo,1', 'date', 'before_or_equal:today'],
                'mes_ingresso_igreja' => ['required', 'integer', 'between:1,12'],
                'ano_ingresso_igreja' => ['required', 'integer', 'min:1900', 'max:'.now()->year],
            ],
            5 => [
                'estado_civil' => ['required', Rule::in(['solteiro', 'casado', 'viuvo', 'divorciado', 'namorando'])],
                'casado_civil' => ['required', 'boolean'],
                'casado_igreja' => ['required', 'boolean'],
                'endereco_completo' => ['required', 'string', 'min:10', 'max:1000'],
            ],
            default => [],
        };
    }

    protected function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'required_if' => 'Informe a data para a opção selecionada.',
            'lgpd_aceito.accepted' => 'É necessário autorizar o uso dos dados para continuar.',
            'foto.image' => 'A foto deve ser uma imagem válida.',
            'foto.mimes' => 'Envie uma foto em JPG, JPEG ou PNG.',
            'foto.max' => 'A foto pode ter no máximo 5 MB.',
            'ano_ingresso_igreja.max' => 'O ano de ingresso não pode ser futuro.',
        ];
    }

    public function updatedBlocoId($value): void
    {
        $this->regioes = $value
            ? Regiao::query()->where('bloco_id', $value)->orderBy('nome')->get()
            : collect();

        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
    }

    public function updatedRegiaoId($value): void
    {
        $this->igrejas = $value
            ? Igreja::query()->where('regiao_id', $value)->orderBy('nome')->get()
            : collect();

        $this->reset('igreja_id');
    }

    public function updatedBatizadoAguas($value): void
    {
        if (! $value) {
            $this->reset('data_batismo_aguas');
        }
    }

    public function updatedBatizadoEspiritoSanto($value): void
    {
        if (! $value) {
            $this->reset('data_batismo_espirito_santo');
        }
    }

    public function updatedEstadoCivil($value): void
    {
        if ($value !== 'casado') {
            $this->casado_civil = false;
            $this->casado_igreja = false;
        }
    }

    public function nextStep(): void
    {
        $this->validate();

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit(): void
    {
        if ($this->step !== $this->totalSteps || $this->enviado) {
            return;
        }

        $limiteChave = 'curso-unp:captacao:'.request()->ip();
        if (RateLimiter::tooManyAttempts($limiteChave, 3)) {
            session()->flash(
                'error',
                'Muitas tentativas. Aguarde '.RateLimiter::availableIn($limiteChave).' segundos e tente novamente.'
            );
            return;
        }

        for ($etapa = 1; $etapa <= $this->totalSteps; $etapa++) {
            $this->step = $etapa;
            $this->validate();
        }
        $this->step = $this->totalSteps;

        $celular = preg_replace('/\D+/', '', $this->celular);

        if (CursoUnpCaptacao::query()
            ->where('celular', $celular)
            ->whereIn('status', ['pendente', 'aprovado'])
            ->exists()) {
            $this->addError('celular', 'Já existe uma inscrição ativa com este celular.');
            return;
        }

        RateLimiter::hit($limiteChave, 15 * 60);
        $fotoArmazenada = null;

        try {
            $fotoArmazenada = $this->foto->store('curso-unp/captacoes', 'public_disk');
            $this->protocolo = (string) Str::uuid();

            CursoUnpCaptacao::create([
                'protocolo' => $this->protocolo,
                'bloco_id' => $this->bloco_id,
                'regiao_id' => $this->regiao_id,
                'igreja_id' => $this->igreja_id,
                'foto' => $fotoArmazenada,
                'nome' => Str::title(trim($this->nome)),
                'celular' => $celular,
                'batizado_aguas' => $this->batizado_aguas,
                'data_batismo_aguas' => $this->data_batismo_aguas,
                'batizado_espirito_santo' => $this->batizado_espirito_santo,
                'data_batismo_espirito_santo' => $this->data_batismo_espirito_santo,
                'estado_civil' => $this->estado_civil,
                'casado_civil' => $this->casado_civil,
                'casado_igreja' => $this->casado_igreja,
                'endereco_completo' => trim($this->endereco_completo),
                'mes_ingresso_igreja' => $this->mes_ingresso_igreja,
                'ano_ingresso_igreja' => $this->ano_ingresso_igreja,
                'status' => 'pendente',
                'lgpd_aceito_em' => now(),
                'ip_hash' => hash_hmac('sha256', (string) request()->ip(), (string) config('app.key')),
                'user_agent' => Str::limit((string) request()->userAgent(), 500, ''),
            ]);

            $this->enviado = true;
        } catch (\Throwable $e) {
            if ($fotoArmazenada) {
                Storage::disk('public_disk')->delete($fotoArmazenada);
            }

            Log::error('Falha na captação pública do Curso UNP', [
                'tipo' => $e::class,
                'mensagem' => $e->getMessage(),
            ]);

            session()->flash('error', 'Não foi possível concluir sua inscrição. Tente novamente.');
        }
    }

    public function render()
    {
        return view('livewire.unp.captacao-curso-unp-wizard', [
            'whatsappUrl' => config('curso_unp.whatsapp_url'),
        ])->layout('layouts.guest');
    }
}
