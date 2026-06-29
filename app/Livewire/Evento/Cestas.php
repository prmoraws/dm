<?php

namespace App\Livewire\Evento;

use App\Models\Evento\Cesta;
use App\Models\Evento\Instituicao;
use App\Models\Evento\Terreiro;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Cestas extends Component
{
    use WithFileUploads, WithPagination;

    // Propriedades do Componente
    public $nome = '', $searchNome = '', $identificado = '', $contato = '', $cestas = '', $observacao = '';
    public $foto = null, $fotoAtual = null;
    public $terreiros = [], $instituicoes = [];
    public $editId = null, $confirmDeleteId = null, $selectedCesta = null;
    public $isOpen = false, $isViewOpen = false;
    public $errorMessage = '';

    // Propriedades para busca e ordenação
    public $search = ''; // Padronizado
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search' => ['except' => ''], 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->loadNomes();
        Log::info('Componente Cestas inicializado');
    }

    // --- Lógica de Busca e Ordenação ---

    public function updatedSearch()
    {
        Log::info('Pesquisa geral atualizada', ['search' => $this->search]);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
        Log::info('Ordenação aplicada', ['field' => $this->sortField, 'direction' => $this->sortDirection]);
    }

    // --- Lógica para o Formulário Dinâmico ---

    public function updatedSearchNome()
    {
        $this->loadNomes();
    }

    public function loadNomes()
    {
        $this->terreiros = Terreiro::where('nome', 'like', '%' . $this->searchNome . '%')->orderBy('nome')->pluck('nome')->toArray();
        $this->instituicoes = Instituicao::where('nome', 'like', '%' . $this->searchNome . '%')->orderBy('nome')->pluck('nome')->toArray();
    }

    public function updatedNome($value)
    {
        $this->identificado = '';
        $this->contato = '';

        if (!empty($value)) {
            $terreiro = Terreiro::where('nome', $value)->first();
            if ($terreiro) {
                $this->identificado = $terreiro->terreiro ?? '';
                $this->contato = $terreiro->contato ?? '';
            } else {
                $instituicao = Instituicao::where('nome', $value)->first();
                if ($instituicao) {
                    $this->identificado = $instituicao->nome ?? '';
                    $this->contato = $instituicao->contato ?? '';
                }
            }
        }
    }

    // --- Renderização ---

    public function render()
    {
        Log::info('Renderizando componente Cestas', ['search' => $this->search]);
        $cestasList = Cesta::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('terreiro', 'like', '%' . $this->search . '%') // 'terreiro' é o campo 'identificado'
                    ->orWhere('contato', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.evento.cestas', [
            'cestasList' => $cestasList,
        ])->layout('layouts.app');
    }

    // --- Ações de CRUD e Modais ---

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
        $this->selectedCesta = null;
    }

    private function resetInputFields()
    {
        $this->fill([
            'nome' => '',
            'searchNome' => '',
            'identificado' => '',
            'contato' => '',
            'cestas' => '',
            'observacao' => '',
            'foto' => null,
            'editId' => null,
            'fotoAtual' => null,
            'errorMessage' => ''
        ]);
        Log::info('Campos de entrada resetados');
    }

    public function save()
    {
        try {
            $validatedData = $this->validate([
                'nome' => 'required|string|max:255',
                'identificado' => 'required|string|max:255',
                'contato' => 'required|string|max:255',
                'cestas' => 'required|numeric|min:1',
                'observacao' => 'nullable|string|max:255',
                'foto' => $this->editId ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240' : 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $foto_caminho = $this->fotoAtual;

            if ($this->foto && is_object($this->foto)) {
                // Deleta a foto antiga se existir
                if ($this->fotoAtual && Storage::disk('public_disk')->exists($this->fotoAtual)) {
                    Storage::disk('public_disk')->delete($this->fotoAtual);
                }
                // Salva a nova foto no diretório 'cestas'
                $foto_caminho = $this->foto->store('cestas', 'public_disk');
            }

            Cesta::updateOrCreate(
                ['id' => $this->editId],
                array_merge($validatedData, ['foto' => $foto_caminho, 'terreiro' => $this->identificado])
            );

            session()->flash('message', $this->editId ? 'Entrega atualizada com sucesso!' : 'Entrega cadastrada com sucesso!');
            $this->closeModal();
        } catch (ValidationException $e) {
            $this->errorMessage = 'Erro de validação: ' . implode(' ', $e->validator->errors()->all());
            Log::error('Erro de validação ao salvar cesta', ['errors' => $e->validator->errors()->all()]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado ao salvar.';
            Log::error('Erro inesperado ao salvar cesta', ['exception' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $cesta = Cesta::findOrFail($id);
            $this->editId = $cesta->id;
            $this->nome = $cesta->nome;
            $this->identificado = $cesta->terreiro;
            $this->contato = $cesta->contato;
            $this->cestas = $cesta->cestas;
            $this->observacao = $cesta->observacao;
            $this->fotoAtual = $cesta->foto; // O caminho já inclui 'cestas/'
            $this->foto = null;
            $this->errorMessage = '';
            $this->openModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar a entrega para edição.');
        }
    }

    public function view($id)
    {
        try {
            $this->selectedCesta = Cesta::findOrFail($id);
            $this->openViewModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar os dados da entrega.');
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            try {
                $cesta = Cesta::findOrFail($this->confirmDeleteId);
                // Garante que estamos usando o disco 'public' para deletar
                if ($cesta->foto && Storage::disk('public')->exists($cesta->foto)) {
                    Storage::disk('public')->delete($cesta->foto);
                    Log::info('Foto excluída ao deletar cesta', ['caminho' => $cesta->foto]);
                }
                $cesta->delete();
                session()->flash('message', 'Entrega deletada com sucesso!');
                $this->confirmDeleteId = null;
            } catch (\Exception $e) {
                session()->flash('error', 'Erro ao deletar a entrega.');
            }
        }
    }
}
