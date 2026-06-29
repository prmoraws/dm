<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\OficioFormatura as Oficio;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response; // ADICIONADO
use Spatie\Browsershot\Browsershot;     // ADICIONADO

class OficioFormatura extends Component
{
    use WithPagination;

    // Propriedades do modelo
    public $oficio_id, $data_oficio, $presidio_id, $curso_id, $dia_hora_evento, $materiais, $comunicacao;

    // Propriedades de controle de estado do formulário
    public $diretor_nome = '';
    public $isPresidioSelected = false;
    public $isCursoSelected = false;

    // Opções para os seletores
    public $presidioOptions = [];
    public $cursoOptions = [];

    // Propriedades da view
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $sortField = 'data_oficio', $sortDirection = 'desc';
    public $selectedOficio;

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedPresidioId($presidioId)
    {
        $this->reset(['curso_id', 'diretor_nome', 'isCursoSelected', 'cursoOptions']);
        if ($presidioId) {
            $presidio = Presidio::find($presidioId);
            $this->diretor_nome = $presidio->diretor ?? 'Diretor não encontrado';
            $this->cursoOptions = Curso::where('presidio_id', $presidioId)
                ->where('status', 'CERTIFICANDO')
                ->orderBy('nome')
                ->pluck('nome', 'id')
                ->toArray();
            $this->isPresidioSelected = true;
        } else {
            $this->isPresidioSelected = false;
        }
    }

    public function render()
    {
        $query = Oficio::with(['presidio', 'curso'])
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'))
                    ->orWhereHas('curso', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.unp.oficios.oficio-formatura', [
            'results' => $query->paginate(10),
        ]);
    }

    private function resetInputFields()
    {
        $this->resetExcept('presidioOptions');
        $this->isPresidioSelected = false;
        $this->isCursoSelected = false;
        $this->materiais = "4 toalhas brancas, 4 toalhas vermelhas, pregos de aço, fita adesiva transparente, 20 becas, 20 capelos, 20 certificados, arranjo grande de flores, copos descartáveis, pratos descartáveis, 3 suqueiras, 1 tapete grande, 10 bandejas, salgados, bolos, 3 bandejas de vidro, 2 malhas brancas, 30 embalagens de marmita de isopor, 3 garrafões com suco, 2 facas para cortar bolo, colheres descartáveis, 2 porta-guardanapos, 2 jarros grandes de vidro, 6 toucas e 6 aventais UNP, rolo de saco plástico, papel toalha, rolo de papel alumínio, rolo de papel filme.";
        $this->comunicacao = "1 caixa de som, 2 microfones sem fio, 1 fonte de alimentação, cabos de conexão, 1 pendrive, banners, 1 câmera fotográfica completa, 1 microfone para celular, 2 celulares e 1 filmadora completa.";
        $this->resetErrorBag();
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
    }
    public function openViewModal()
    {
        $this->isViewOpen = true;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedOficio = null;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
    public function search()
    {
        $this->resetPage();
    }

    public function store()
    {
        $validatedData = $this->validate([
            'data_oficio' => 'required|date',
            'presidio_id' => 'required|exists:presidios,id',
            'curso_id' => 'required|exists:cursos,id',
            'dia_hora_evento' => 'required|date',
            'materiais' => 'required|string',
            'comunicacao' => 'required|string',
        ]);

        try {
            Oficio::updateOrCreate(['id' => $this->oficio_id], $validatedData);
            session()->flash('message', $this->oficio_id ? 'Ofício atualizado!' : 'Ofício criado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar o ofício: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->resetInputFields();
        $this->oficio_id = $id;
        $this->fill($oficio);

        $this->data_oficio = Carbon::parse($oficio->data_oficio)->format('Y-m-d\TH:i');
        $this->dia_hora_evento = Carbon::parse($oficio->dia_hora_evento)->format('Y-m-d\TH:i');

        $this->updatedPresidioId($this->presidio_id);
        $this->curso_id = $oficio->curso_id;

        $this->isPresidioSelected = true;
        $this->openModal();
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'curso'])->findOrFail($id);

        $oficio->numero_oficio = 'Ofício Nº ' . (100 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: FORMATURA DO CURSO DE ' . mb_strtoupper($oficio->curso->nome, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . $oficio->presidio->diretor; // Correção: Adicionado DR.:

        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = 'formatura do Curso de ' . $oficio->curso->nome . ', dia ' . $eventoData->translatedFormat('d \d\e F \d\e Y') . ', às ' . $eventoData->format('H:i') . 'h';

        return $oficio;
    }

    // MÉTODO VIEW ATUALIZADO
    public function view($id)
    {
        try {
            $this->selectedOficio = $this->prepareOficioData($id);
            $this->openViewModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar os dados do ofício: ' . $e->getMessage());
        }
    }

    // Adicione este novo método que retornará o HTML para o JavaScript
    public function getOficioHtmlForPdf($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            // Renderiza a view Blade e retorna a string HTML
            return view('livewire.unp.oficios.pdf.oficio-formatura-pdf', ['selectedOficio' => $oficio])->render();
        } catch (\Exception $e) {
            // Em caso de erro, você pode logar ou retornar uma string vazia/erro
            \Log::error('Erro ao renderizar HTML do ofício para PDF: ' . $e->getMessage());
            return ''; // Retorna string vazia ou um JSON de erro, dependendo de como você quer tratar no JS
        }
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
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Oficio::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Ofício deletado com sucesso!');
            $this->confirmDeleteId = null;
        }
    }
}