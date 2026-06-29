<?php

namespace App\Livewire\Evento;

use App\Models\Evento\Instituicao;
use App\Models\Universal\Bloco;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Instituicoes extends Component
{
    use WithPagination;

    // Propriedades para os modais
    public $nome, $contato, $bairro, $convidados, $onibus, $bloco, $iurd, $pastor, $telefone, $endereco, $localização, $instituicao_id;
    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $selectedInstituicao;

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
            ->map(fn ($nome) => strtoupper($nome))
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
        
        $results = Instituicao::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('bairro', 'like', '%' . $this->search . '%')
                    ->orWhere('contato', 'like', '%' . $this->search . '%')
                    ->orWhere('bloco', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.evento.instituicoes', [
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
        $this->selectedInstituicao = null;
    }

    private function resetInputFields()
    {
        $this->fill([
            'nome' => '', 'contato' => '', 'bairro' => '', 'convidados' => '',
            'onibus' => '', 'bloco' => '', 'iurd' => '', 'pastor' => '',
            'telefone' => '', 'endereco' => '', 'localização' => '',
            'instituicao_id' => null, 'errorMessage' => ''
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
                'convidados' => 'required|string|max:255',
                'onibus' => 'required|string|max:255',
                'bloco' => 'required|in:' . implode(',', $this->blocoOptions),
                'iurd' => 'required|string|max:255',
                'pastor' => 'required|string|max:255',
                'telefone' => 'required|string|max:255',
                'endereco' => 'required|string|max:255',
                'localização' => 'required|string|max:255',
            ]);

            Instituicao::updateOrCreate(['id' => $this->instituicao_id], $validatedData);

            session()->flash('message', $this->instituicao_id ? 'Instituição atualizada com sucesso!' : 'Instituição criada com sucesso!');
            $this->closeModal();

        } catch (ValidationException $e) {
            $this->errorMessage = 'Erro de validação: ' . implode(' ', $e->validator->errors()->all());
            Log::error('Erro de validação ao salvar instituição', ['errors' => $e->validator->errors()->all()]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado ao salvar. Por favor, tente novamente.';
            Log::error('Erro inesperado ao salvar instituição', ['exception' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        try {
            $instituicao = Instituicao::findOrFail($id);
            $this->instituicao_id = $id;
            $this->nome = $instituicao->nome;
            $this->contato = $instituicao->contato;
            $this->bairro = $instituicao->bairro;
            $this->convidados = $instituicao->convidados;
            $this->onibus = $instituicao->onibus;
            $this->bloco = in_array(strtoupper($instituicao->bloco), $this->blocoOptions) ? strtoupper($instituicao->bloco) : '';
            $this->iurd = $instituicao->iurd;
            $this->pastor = $instituicao->pastor;
            $this->telefone = $instituicao->telefone;
            $this->endereco = $instituicao->endereco;
            $this->localização = $instituicao->localização;
            $this->errorMessage = '';
            $this->openModal();
        } catch (\Exception $e) {
            Log::error('Erro ao editar instituição', ['id' => $id, 'exception' => $e->getMessage()]);
            session()->flash('error', 'Não foi possível carregar a instituição para edição.');
        }
    }

    public function view($id)
    {
        Log::info('View method called', ['id' => $id]);
        try {
            $this->selectedInstituicao = Instituicao::findOrFail($id);
            $this->openViewModal();
        } catch (\Exception $e) {
            Log::error('Erro ao visualizar instituição', ['id' => $id, 'exception' => $e->getMessage()]);
            session()->flash('error', 'Não foi possível carregar os dados da instituição.');
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
                Instituicao::findOrFail($this->confirmDeleteId)->delete();
                session()->flash('message', 'Instituição deletada com sucesso!');
                $this->confirmDeleteId = null;
            } catch (\Exception $e) {
                Log::error('Erro ao deletar instituição', ['id' => $this->confirmDeleteId, 'exception' => $e->getMessage()]);
                session()->flash('error', 'Erro ao deletar a instituição.');
            }
        }
    }
}
