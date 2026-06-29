<?php

namespace App\Livewire\Unp;

use App\Models\Universal\Bloco;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use App\Models\Unp\Instrutor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Instrutores extends Component
{
    use WithPagination, WithFileUploads;

    public $instrutor_id, $bloco_id, $categoria_id, $foto, $nome, $telefone, $rg, $cpf, $igreja_id, $profissao, $batismo = [], $testemunho, $carga, $certificado = false, $inscricao = false;

    public $igrejas = [];

    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $searchTerm = '';
    public $selectedInstrutor;
    public $blocoOptions = [];
    public $categoriaOptions = [];

    public function mount()
    {
        $this->blocoOptions = Bloco::orderBy('nome')->pluck('nome', 'id')->toArray();
        $this->categoriaOptions = Categoria::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedBlocoId($blocoId)
    {
        if ($blocoId) {
            $this->igrejas = Igreja::where('bloco_id', $blocoId)->orderBy('nome')->get();
        } else {
            $this->igrejas = [];
        }
        $this->reset('igreja_id');
    }

    public function render()
    {
        $query = Instrutor::query()->with(['bloco', 'categoria', 'igreja']);

        if ($this->searchTerm !== '') {
            $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('telefone', 'like', '%' . $this->searchTerm . '%')
                ->orWhereHas('bloco', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
        }

        $results = $query->latest()->paginate(10);

        return view('livewire.unp.instrutores', [
            'results' => $results,
        ]);
    }

    public function search()
    {
        $this->resetPage();
    }
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
    public function openModal()
    {
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
    public function openViewModal()
    {
        $this->isViewOpen = true;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedInstrutor = null;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    private function resetInputFields()
    {
        $this->instrutor_id = null;
        $this->bloco_id = '';
        $this->categoria_id = '';
        $this->foto = null;
        $this->nome = '';
        $this->telefone = '';
        $this->rg = '';
        $this->cpf = '';
        $this->igreja_id = '';
        $this->igrejas = [];
        $this->profissao = '';
        $this->batismo = [];
        $this->testemunho = '';
        $this->carga = '';
        $this->certificado = false;
        $this->inscricao = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'bloco_id' => 'required|exists:blocos,id',
            'categoria_id' => 'required|exists:categorias,id',
            'igreja_id' => 'required|exists:igrejas,id',
            'telefone' => 'nullable|string|max:20',
            'rg' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'profissao' => 'nullable|string|max:255',
            'batismo' => 'nullable|array',
            'testemunho' => 'nullable|string',
            'carga' => 'nullable|string|max:255',
            'certificado' => 'boolean',
            'inscricao' => 'boolean',
        ];

        $validatedData = $this->validate($rules);

        $validatedData['batismo'] = json_encode($this->batismo);

        try {
            if ($this->foto) {
                if ($this->instrutor_id) {
                    $existing = Instrutor::find($this->instrutor_id);
                    if ($existing && $existing->foto) {
                        Storage::disk('public_disk')->delete($existing->foto);
                    }
                }
                $validatedData['foto'] = $this->foto->store('instrutores', 'public_disk');
            }

            Instrutor::updateOrCreate(['id' => $this->instrutor_id], $validatedData);

            session()->flash('message', $this->instrutor_id ? 'Instrutor atualizado com sucesso!' : 'Instrutor criado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar o instrutor: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $instrutor = Instrutor::findOrFail($id);
        $this->instrutor_id = $id;
        $this->fill($instrutor);

        $this->igrejas = Igreja::where('bloco_id', $instrutor->bloco_id)->orderBy('nome')->get();

        $this->certificado = (bool)$instrutor->certificado;
        $this->inscricao = (bool)$instrutor->inscricao;
        $this->batismo = is_array($instrutor->batismo) ? $instrutor->batismo : json_decode($instrutor->batismo, true) ?? [];
        $this->selectedInstrutor = $instrutor;
        $this->foto = null;

        $this->openModal();
    }

    public function view($id)
    {
        $this->selectedInstrutor = Instrutor::with(['bloco', 'categoria', 'igreja'])->findOrFail($id);
        $batismo = is_array($this->selectedInstrutor->batismo) ? $this->selectedInstrutor->batismo : json_decode($this->selectedInstrutor->batismo, true) ?? [];
        $this->selectedInstrutor->batismoAguas = in_array('aguas', $batismo);
        $this->selectedInstrutor->batismoEspirito = in_array('espirito', $batismo);

        $this->openViewModal();
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $instrutor = Instrutor::find($this->confirmDeleteId);
            if ($instrutor) {
                if ($instrutor->foto) {
                    Storage::disk('public_disk')->delete($instrutor->foto);
                }
                $instrutor->delete();
                session()->flash('message', 'Instrutor deletado com sucesso.');
            }
            $this->confirmDeleteId = null;
        }
    }
}
