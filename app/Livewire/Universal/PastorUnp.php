<?php

namespace App\Livewire\Universal;

// CORREÇÃO: Imports ajustados para os Models corretos
use App\Models\Universal\Bloco;
use App\Models\Universal\PastorUnp as PastorUnpModel; // Usando um alias para o Model
use App\Models\Universal\Regiao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PastorUnp extends Component
{
    use WithPagination, WithFileUploads;

    // Propriedades do modelo
    public $pastor_id, $bloco_id, $regiao_id, $nome, $nascimento, $email, $whatsapp, $telefone, $cargo, $chegada, $entrada, $preso;
    public $nome_esposa, $email_esposa, $whatsapp_esposa, $telefone_esposa, $obra, $casado, $consagrada_esposa, $preso_esposa;

    // Propriedades para uploads de arquivo
    public $foto, $foto_esposa;
    public $foto_atual, $foto_esposa_atual;

    // Propriedades para checkboxes (JSON)
    public $trabalho = [];
    public $trabalho_esposa = [];

    // Propriedades de controle da UI
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $selectedPastor;

    // Propriedades para selects dinâmicos
    public $blocoOptions = [], $regiaoOptions = [];

    protected function rules()
    {
        return [
            'bloco_id' => 'required|exists:blocos,id',
            'regiao_id' => 'required|exists:regiaos,id',
            'nome' => 'required|string|max:255',
            'nascimento' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'required|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'required|in:responsavel,auxiliar',
            'chegada' => 'nullable|string|max:255',
            'entrada' => 'nullable|date',
            'preso' => 'required|boolean',
            'trabalho' => 'nullable|array',
            'foto' => 'nullable|image|max:2048', // 2MB Max
            'nome_esposa' => 'nullable|string|max:255',
            'email_esposa' => 'nullable|email|max:255',
            'whatsapp_esposa' => 'nullable|string|max:20',
            'telefone_esposa' => 'nullable|string|max:20',
            'obra' => 'nullable|string|max:255',
            'casado' => 'nullable|string|max:255',
            'consagrada_esposa' => 'nullable|boolean',
            'preso_esposa' => 'nullable|boolean',
            'trabalho_esposa' => 'nullable|array',
            'foto_esposa' => 'nullable|image|max:2048', // 2MB Max
        ];
    }

    public function mount()
    {
        $this->blocoOptions = Bloco::orderBy('nome')->get();
    }

    public function updatedBlocoId($blocoId)
    {
        if ($blocoId) {
            $this->regiaoOptions = Regiao::where('bloco_id', $blocoId)->orderBy('nome')->get();
        } else {
            $this->regiaoOptions = [];
        }
        $this->reset('regiao_id');
    }

    public function render()
    {
        return view('livewire.universal.pastor-unp', [
            'results' => PastorUnpModel::with(['bloco', 'regiao'])
                ->when($this->searchTerm, fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'))
                ->latest()
                ->paginate(10)
        ]);
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
        $this->resetErrorBag();
    }
    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function store()
    {
        $validatedData = $this->validate();

        // Lida com o upload da foto do pastor
        if ($this->foto) {
            if ($this->pastor_id && $this->foto_atual) {
                // Usa o disco 'public_disk' para deletar o arquivo antigo
                Storage::disk('public_disk')->delete($this->foto_atual);
            }
            // Usa o disco 'public_disk' para salvar o novo arquivo em 'public/pastor'
            $validatedData['foto'] = $this->foto->store('pastor', 'public_disk');
        }

        // Lida com o upload da foto da esposa
        if ($this->foto_esposa) {
            if ($this->pastor_id && $this->foto_esposa_atual) {
                Storage::disk('public_disk')->delete($this->foto_esposa_atual);
            }
            $validatedData['foto_esposa'] = $this->foto_esposa->store('pastor', 'public_disk');
        }

        PastorUnpModel::updateOrCreate(['id' => $this->pastor_id], $validatedData);

        session()->flash('message', $this->pastor_id ? 'Pastor atualizado.' : 'Pastor criado.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $pastor = PastorUnpModel::findOrFail($id);
        $this->pastor_id = $id;

        // Preenche todos os campos, EXCETO os de upload de arquivo
        $this->fill($pastor->except(['foto', 'foto_esposa']));

        // Carrega as opções de região para o bloco selecionado
        $this->updatedBlocoId($pastor->bloco_id);
        $this->regiao_id = $pastor->regiao_id;

        // Formata as datas para o formulário
        $this->nascimento = $pastor->nascimento ? $pastor->nascimento->format('Y-m-d') : null;
        $this->entrada = $pastor->entrada ? $pastor->entrada->format('Y-m-d') : null;

        // Armazena os caminhos das fotos atuais para exibição
        $this->foto_atual = $pastor->foto;
        $this->foto_esposa_atual = $pastor->foto_esposa;

        $this->isOpen = true;
    }

    public function view($id)
    {
        $this->selectedPastor = PastorUnpModel::with(['bloco', 'regiao'])->findOrFail($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $pastor = PastorUnpModel::find($this->confirmDeleteId);
            if ($pastor) {
                // Deleta as fotos do disco 'public_disk'
                if ($pastor->foto)
                    Storage::disk('public_disk')->delete($pastor->foto);
                if ($pastor->foto_esposa)
                    Storage::disk('public_disk')->delete($pastor->foto_esposa);

                $pastor->delete();
                session()->flash('message', 'Pastor deletado com sucesso.');
            }
            $this->confirmDeleteId = null;
        }
    }
}