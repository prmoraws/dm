<?php

namespace App\Livewire\Adm;

use App\Models\Universal\Captacao;
use App\Models\Universal\PastorUnp;
use App\Models\Universal\CarroUnp;
use App\Models\Universal\Bloco;
use App\Models\Universal\Regiao;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Captacoes extends Component
{
    use WithPagination;

    public $captacao_id;
    public array $state = []; // Armazena os dados do formulário para edição

    // Opções para selects no modal de edição
    public $blocoOptions = [], $regiaoOptions = [];

    // Controle da UI
    public $isOpen = false, $isViewOpen = false, $confirmActionId = null;
    public $actionType = ''; // 'approve' ou 'reject'
    public $selectedCaptacao;
    public $searchTerm = '';

    // Regras de validação para o modal de edição
    protected function rules()
    {
        return [
            'state.nome' => 'required|string|max:255',
            'state.cpf' => 'required|string|max:20',
            'state.bloco_id' => 'required|exists:blocos,id',
            'state.regiao_id' => 'required|exists:regiaos,id',
            'state.cargo' => 'required|in:responsavel,auxiliar',
            // Adicione outras regras se precisar validar mais campos na edição
        ];
    }

    public function mount()
    {
        $this->blocoOptions = Bloco::orderBy('nome')->get();
    }

    public function updatedStateBlocoId($blocoId)
    {
        $this->regiaoOptions = $blocoId ? Regiao::where('bloco_id', $blocoId)->orderBy('nome')->get() : [];
        $this->state['regiao_id'] = '';
    }

    public function render()
    {
        return view('livewire.adm.captacoes', [
            'results' => Captacao::where('status', 'pendente')
                ->when($this->searchTerm, fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%')->orWhere('cpf', 'like', '%' . $this->searchTerm . '%'))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function view(Captacao $captacao)
    {
        $this->selectedCaptacao = $captacao;
        $this->isViewOpen = true;
    }

    public function edit(Captacao $captacao)
    {
        $this->captacao_id = $captacao->id;
        $this->state = $captacao->payload;
        $this->updatedStateBlocoId($this->state['bloco_id'] ?? null);
        $this->isOpen = true;
    }

    public function update()
    {
        $this->validate();

        $captacao = Captacao::find($this->captacao_id);
        if ($captacao) {
            $payload = $captacao->payload;
            // Atualiza apenas os campos editáveis, mantendo o resto do payload intacto
            $payload['bloco_id'] = $this->state['bloco_id'];
            $payload['regiao_id'] = $this->state['regiao_id'];
            $payload['cargo'] = $this->state['cargo'];
            // Adicione outras atualizações aqui se necessário

            $captacao->payload = $payload;
            $captacao->save();
            session()->flash('message', 'Captação atualizada com sucesso. Agora você pode aprová-la.');
        }

        $this->closeModal();
    }

    public function confirmAction($id, $type)
    {
        $this->confirmActionId = $id;
        $this->actionType = $type;
    }

    public function performAction()
    {
        if ($this->actionType === 'approve') {
            $this->approve();
        } elseif ($this->actionType === 'reject') {
            $this->reject();
        }
        $this->reset(['confirmActionId', 'actionType']);
    }

    public function approve()
    {
        try {
            $captacao = Captacao::findOrFail($this->confirmActionId);
            $data = $captacao->payload;

            $pastorData = $this->prepareAndMoveFiles($data, ['foto', 'foto_esposa'], 'pastor');
            $pastor = PastorUnp::create($pastorData);

            if (isset($data['modelo']) && !empty($data['modelo'])) {
                $carroData = $this->prepareAndMoveFiles($data, ['foto_frente', 'foto_tras', 'foto_direita', 'foto_esquerda', 'foto_dentro', 'foto_cambio'], 'carros');
                $carroData['pastor_unp_id'] = $pastor->id;
                $carroData['bloco_id'] = $pastor->bloco_id;
                CarroUnp::create($carroData);
            }

            $captacao->status = 'aprovado';
            $captacao->save();

            session()->flash('message', 'Captação aprovada e registros criados com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao aprovar captação: ' . $e->getMessage());
            session()->flash('error', 'Ocorreu um erro ao aprovar a captação: ' . $e->getMessage());
        }
    }

    public function reject()
    {
        try {
            $captacao = Captacao::findOrFail($this->confirmActionId);
            $payload = $captacao->payload;
            $photoFields = ['foto', 'foto_esposa', 'foto_frente', 'foto_tras', 'foto_direita', 'foto_esquerda', 'foto_dentro', 'foto_cambio'];

            foreach ($photoFields as $field) {
                if (!empty($payload[$field])) {
                    // CORREÇÃO: Deleta do disco que aponta para a pasta public
                    Storage::disk('public_disk')->delete($payload[$field]);
                }
            }

            $captacao->status = 'rejeitado';
            $captacao->save();

            session()->flash('message', 'Captação rejeitada e arquivos temporários removidos.');
        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar captação: ' . $e->getMessage());
            session()->flash('error', 'Ocorreu um erro ao rejeitar a captação.');
        }
    }

    private function prepareAndMoveFiles(array $data, array $photoFields, string $destinationFolder): array
    {
        foreach ($photoFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $tempPath = $data[$field]; // ex: captacao_temp/xyz.jpg
                $fileName = basename($tempPath);
                $newPath = "{$destinationFolder}/{$fileName}"; // ex: pastor/xyz.jpg

                // CORREÇÃO: Move o arquivo dentro do mesmo disco 'public_disk'
                if (Storage::disk('public_disk')->exists($tempPath)) {
                    Storage::disk('public_disk')->move($tempPath, $newPath);
                    $data[$field] = $newPath;
                } else {
                    $data[$field] = null;
                }
            }
        }
        return $data;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedCaptacao = null;
    }
    private function resetInputFields()
    {
        $this->reset('captacao_id', 'state');
        $this->resetErrorBag();
    }
}