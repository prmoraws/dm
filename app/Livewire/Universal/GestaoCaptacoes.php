<?php

namespace App\Livewire\Universal;

use App\Models\Universal\CaptacaoPessoa;
use App\Models\Universal\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoCaptacoes extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'pendente';
    public string $sortBy = 'created_at';
    public string $sortDir = 'DESC';

    public ?CaptacaoPessoa $selectedCaptacao = null;
    public bool $isViewOpen = false;
    public bool $isRejectionModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'pendente'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setFilterStatus(string $status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }
    
    public function view(CaptacaoPessoa $captacao)
    {
        $this->selectedCaptacao = $captacao->load('bloco', 'regiao', 'igreja', 'categoria', 'cargo', 'grupo', 'cidade.estado');
        $this->isViewOpen = true;
    }
    
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedCaptacao = null;
    }

    public function confirmRejection(CaptacaoPessoa $captacao)
    {
        $this->selectedCaptacao = $captacao;
        $this->isRejectionModalOpen = true;
    }

    public function reject()
    {
        if (!$this->selectedCaptacao) return;

        try {
            if ($this->selectedCaptacao->foto && Storage::disk('public_disk')->exists($this->selectedCaptacao->foto)) {
                Storage::disk('public_disk')->delete($this->selectedCaptacao->foto);
            }
            $this->selectedCaptacao->delete();
            session()->flash('message', 'Captação rejeitada e excluída com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar captação: ' . $e->getMessage());
            session()->flash('error', 'Ocorreu um erro ao excluir o registro.');
        }

        $this->isRejectionModalOpen = false;
        $this->selectedCaptacao = null;
    }

    public function approve(CaptacaoPessoa $captacao)
    {
        try {
            DB::transaction(function () use ($captacao) {
                $attributes = $captacao->getAttributes();
                
                        // Mover a foto
                if ($captacao->foto && Storage::disk('public_disk')->exists($captacao->foto)) {
                    $newPath = str_replace('captacao_temp/', 'pessoas/', $captacao->foto);
                    Storage::disk('public_disk')->move($captacao->foto, $newPath);
                    $attributes['foto'] = $newPath;
                }

                        // Mover a assinatura
                if ($captacao->assinatura && Storage::disk('public_disk')->exists($captacao->assinatura)) {
                    $newPath = str_replace('captacao_temp/signatures/', 'pessoas/signatures/', $captacao->assinatura);
                    Storage::disk('public_disk')->move($captacao->assinatura, $newPath);
                    $attributes['assinatura'] = $newPath;
                }

                unset(
                    $attributes['id'], $attributes['status'], $attributes['motivo_rejeicao'],
                    $attributes['revisado_por'], $attributes['revisado_em'],
                    $attributes['created_at'], $attributes['updated_at']
                );
                
                $attributes['telefone']   = $attributes['telefone'] ?? '';
                $attributes['email']      = $attributes['email'] ?? '';
                $attributes['cep']        = $attributes['cep'] ?? '';
                $attributes['profissao']  = $attributes['profissao'] ?? '';
                $attributes['aptidoes']   = $attributes['aptidoes'] ?? '';
                $attributes['testemunho'] = $attributes['testemunho'] ?? '';
                $attributes['foto']       = $attributes['foto'] ?? '';
                
                // ==================================================================
                // CORREÇÃO: Define um valor padrão para 'grupo_id' se ele for nulo.
                // Assumimos que o grupo com ID 1 é um grupo geral/padrão.
                // ==================================================================
                $attributes['grupo_id']   = $attributes['grupo_id'] ?? 1;

                Pessoa::create($attributes);
                
                $captacao->update([
                    'status' => 'aprovado',
                    'revisado_por' => auth()->id(),
                    'revisado_em' => now(),
                ]);
            });

            session()->flash('message', 'Captação aprovada com sucesso! Um novo registro de Pessoa foi criado.');
        } catch (\Exception $e) {
            Log::critical('Falha ao aprovar captação e criar pessoa: ' . $e->getMessage());
            session()->flash('error', 'Ocorreu um erro crítico. A operação foi revertida. Verifique os logs.');
        }
    }

    public function render()
    {
        $captacoes = CaptacaoPessoa::query()
            ->with(['igreja'])
            ->when($this->search, fn ($q) => $q->where('nome', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->where('status', $this->filterStatus)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);

        return view('livewire.universal.gestao-captacoes', [
            'captacoes' => $captacoes,
        ]);
    }
}