<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\CarroUnp as Carro;
use App\Models\Universal\PastorUnp;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CarroUnp extends Component
{
    use WithPagination, WithFileUploads;

    public $carro_id, $pastor_unp_id, $bloco_id, $modelo, $ano, $placa, $km, $demanda;
    public $foto_frente, $foto_tras, $foto_direita, $foto_esquerda, $foto_dentro, $foto_cambio;
    public $foto_frente_atual, $foto_tras_atual, $foto_direita_atual, $foto_esquerda_atual, $foto_dentro_atual, $foto_cambio_atual;
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $selectedCarro;
    public $pastorOptions = [], $blocoOptions = [];
    public $yearOptions = [];

    protected function rules()
    {
        return [
            'pastor_unp_id' => 'required|exists:pastor_unps,id',
            'bloco_id' => 'required|exists:blocos,id',
            'modelo' => 'required|string|max:255',
            'ano' => 'required|string|max:255',
            'placa' => 'required|string|max:10|unique:carro_unps,placa,' . $this->carro_id,
            'km' => 'nullable|integer',
            'demanda' => 'nullable|string',
            'foto_frente' => $this->carro_id ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'foto_tras' => $this->carro_id ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'foto_direita' => 'nullable|image|max:2048',
            'foto_esquerda' => 'nullable|image|max:2048',
            'foto_dentro' => 'nullable|image|max:2048',
            'foto_cambio' => 'nullable|image|max:2048',
        ];
    }

    public function mount()
    {
        $this->pastorOptions = PastorUnp::orderBy('nome')->get();
        $this->blocoOptions = Bloco::orderBy('nome')->get();

        $currentYear = date('Y');
        for ($i = 0; $i <= 20; $i++) {
            $this->yearOptions[] = $currentYear - $i;
        }
    }

    public function render()
    {
        return view('livewire.universal.carro-unp', [
            'results' => Carro::with(['pastorUnp', 'bloco'])
                ->when($this->searchTerm, fn($q) => $q->where('modelo', 'like', '%' . $this->searchTerm . '%')->orWhere('placa', 'like', '%' . $this->searchTerm . '%'))
                ->latest()
                ->paginate(10)
        ]);
    }

    public function store()
    {
        $validatedData = $this->validate();

        $photoFields = ['frente', 'tras', 'direita', 'esquerda', 'dentro', 'cambio'];

        foreach ($photoFields as $field) {
            $property = 'foto_' . $field; // ex: $this->foto_frente
            $current_property = 'foto_' . $field . '_atual'; // ex: $this->foto_frente_atual

            if ($this->$property) {
                // Se uma nova foto foi enviada, deleta a antiga e salva a nova
                if ($this->carro_id && $this->$current_property) {
                    Storage::disk('public_disk')->delete($this->$current_property);
                }
                $validatedData[$property] = $this->$property->store('carros', 'public_disk');
            } elseif ($this->carro_id) {
                // CORREÇÃO: Se nenhuma foto nova foi enviada durante uma edição,
                // mantém o valor da foto que já existia no banco de dados.
                $validatedData[$property] = $this->$current_property;
            }
        }

        Carro::updateOrCreate(['id' => $this->carro_id], $validatedData);

        session()->flash('message', $this->carro_id ? 'Carro atualizado.' : 'Carro cadastrado.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $carro = Carro::findOrFail($id);
        $this->carro_id = $id;

        // Preenche todos os campos, EXCETO os de upload de arquivo para evitar o erro
        $this->fill($carro->except(['foto_frente', 'foto_tras', 'foto_direita', 'foto_esquerda', 'foto_dentro', 'foto_cambio']));

        // Armazena os caminhos das fotos atuais para exibição
        $this->foto_frente_atual = $carro->foto_frente;
        $this->foto_tras_atual = $carro->foto_tras;
        $this->foto_direita_atual = $carro->foto_direita;
        $this->foto_esquerda_atual = $carro->foto_esquerda;
        $this->foto_dentro_atual = $carro->foto_dentro;
        $this->foto_cambio_atual = $carro->foto_cambio;

        $this->isOpen = true;
    }

    public function view($id)
    {
        $this->selectedCarro = Carro::with(['pastorUnp', 'bloco'])->findOrFail($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $carro = Carro::find($this->confirmDeleteId);
            if ($carro) {
                // Deleta todas as fotos associadas do disco 'public_disk'
                $photoFields = ['foto_frente', 'foto_tras', 'foto_direita', 'foto_esquerda', 'foto_dentro', 'foto_cambio'];
                foreach ($photoFields as $field) {
                    if ($carro->$field) {
                        Storage::disk('public_disk')->delete($carro->$field);
                    }
                }
                $carro->delete();
                session()->flash('message', 'Carro deletado com sucesso.');
            }
            $this->confirmDeleteId = null;
        }
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
}