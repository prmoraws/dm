<?php

namespace App\Livewire\Evento;

use App\Models\Evento\Terreiro;
use App\Models\Universal\Bloco;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Terreiros extends Component
{
    use WithPagination;

    // Propriedades para os modais
    public $nome, $contato, $bairro, $terreiro, $convidados, $onibus, $bloco, $iurd, $pastor, $telefone, $endereco, $localização, $terreiro_id;
    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $selectedTerreiro;

    // Propriedades para busca e ordenação
    public $search = ''; // Variável de busca padronizada
    public $sortField = 'nome';
    public $sortDirection = 'asc';

    // Propriedades para UI
    public $errorMessage = '';
    public $blocoOptions = [];

    // Mapeia as propriedades para a query string da URL
    protected $queryString = ['search' => ['except' => ''], 'sortField', 'sortDirection'];

    /**
     * Carrega as opções de Bloco quando o componente é inicializado.
     */
    public function mount()
    {
        $this->blocoOptions = Bloco::orderBy('nome')
            ->pluck('nome')
            ->map(fn($nome) => strtoupper($nome))
            ->toArray();
    }

    /**
     * Lifecycle hook que reseta a paginação sempre que a busca é alterada.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Altera a ordenação da tabela.
     */
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

    /**
     * Renderiza a view com os dados filtrados e paginados.
     */
    public function render()
    {
        Log::info('Render method called', ['search' => $this->search, 'sortField' => $this->sortField, 'sortDirection' => $this->sortDirection]);

        $results = Terreiro::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('bairro', 'like', '%' . $this->search . '%')
                    ->orWhere('terreiro', 'like', '%' . $this->search . '%')
                    ->orWhere('bloco', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.evento.terreiros', [
            'results' => $results,
            'blocoOptions' => $this->blocoOptions,
        ]);
    }

    // --- Métodos para o CRUD (Create, Read, Update, Delete) e Modais ---

    public function create()
    {
        Log::info('Create method called');
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        Log::info('CloseModal method called');
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function openViewModal()
    {
        $this->isViewOpen = true;
    }

    public function closeViewModal()
    {
        Log::info('CloseViewModal method called');
        $this->isViewOpen = false;
        $this->selectedTerreiro = null;
    }

    private function resetInputFields()
    {
        $this->fill([
            'nome' => '',
            'contato' => '',
            'bairro' => '',
            'terreiro' => '',
            'convidados' => '',
            'onibus' => '',
            'bloco' => '',
            'iurd' => '',
            'pastor' => '',
            'telefone' => '',
            'endereco' => '',
            'localização' => '',
            'terreiro_id' => null,
            'errorMessage' => ''
        ]);
        Log::info('Campos resetados');
    }

    public function store()
    {
        try {
            $validatedData = $this->validate([
                'nome' => 'required|string|max:255',
                'contato' => 'required|string|max:255',
                'bairro' => 'required|string|max:255',
                'terreiro' => 'required|string|max:255',
                'convidados' => 'required|string|max:255',
                'onibus' => 'required|string|max:255',
                'bloco' => 'required|in:' . implode(',', $this->blocoOptions),
                'iurd' => 'required|string|max:255',
                'pastor' => 'required|string|max:255',
                'telefone' => 'required|string|max:255',
                'endereco' => 'required|string|max:255',
                'localização' => 'required|string|max:255',
            ]);

            Terreiro::updateOrCreate(['id' => $this->terreiro_id], $validatedData);

            session()->flash('message', $this->terreiro_id ? 'Terreiro atualizado com sucesso!' : 'Terreiro criado com sucesso!');
            $this->closeModal();
        } catch (ValidationException $e) {
            $this->errorMessage = 'Erro de validação: ' . implode(' ', $e->validator->errors()->all());
            Log::error('Erro de validação ao salvar terreiro', ['errors' => $e->validator->errors()->all()]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado ao salvar. Por favor, tente novamente.';
            Log::error('Erro inesperado ao salvar terreiro', ['exception' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        try {
            $terreiro = Terreiro::findOrFail($id);
            $this->terreiro_id = $id;
            $this->nome = $terreiro->nome;
            $this->contato = $terreiro->contato;
            $this->bairro = $terreiro->bairro;
            $this->terreiro = $terreiro->terreiro;
            $this->convidados = $terreiro->convidados;
            $this->onibus = $terreiro->onibus;
            $this->bloco = in_array(strtoupper($terreiro->bloco), $this->blocoOptions) ? strtoupper($terreiro->bloco) : '';
            $this->iurd = $terreiro->iurd;
            $this->pastor = $terreiro->pastor;
            $this->telefone = $terreiro->telefone;
            $this->endereco = $terreiro->endereco;
            $this->localização = $terreiro->localização;
            $this->errorMessage = '';
            $this->openModal();
        } catch (\Exception $e) {
            Log::error('Erro ao editar terreiro', ['id' => $id, 'exception' => $e->getMessage()]);
            session()->flash('error', 'Não foi possível carregar o terreiro para edição.');
        }
    }

    public function view($id)
    {
        Log::info('View method called', ['id' => $id]);
        try {
            $this->selectedTerreiro = Terreiro::findOrFail($id);
            $this->openViewModal();
        } catch (\Exception $e) {
            Log::error('Erro ao visualizar terreiro', ['id' => $id, 'exception' => $e->getMessage()]);
            session()->flash('error', 'Não foi possível carregar os dados do terreiro.');
        }
    }

    public function confirmDelete($id)
    {
        Log::info('ConfirmDelete method called', ['id' => $id]);
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        Log::info('Delete method called', ['confirmDeleteId' => $this->confirmDeleteId]);
        if ($this->confirmDeleteId) {
            try {
                Terreiro::findOrFail($this->confirmDeleteId)->delete();
                session()->flash('message', 'Terreiro deletado com sucesso!');
                $this->confirmDeleteId = null;
            } catch (\Exception $e) {
                Log::error('Erro ao deletar terreiro', ['id' => $this->confirmDeleteId, 'exception' => $e->getMessage()]);
                session()->flash('error', 'Erro ao deletar o terreiro.');
            }
        }
    }
}
