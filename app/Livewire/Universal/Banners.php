<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Banner;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Banners extends Component
{
    use WithPagination, WithFileUploads;

    public $nome, $descricao, $foto, $banner_id;
    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedBanner;

    public function render()
    {
        $query = Banner::query();

        if ($this->searchTerm !== '') {
            $query->where('nome', 'like', '%' . $this->searchTerm . '%');
        }

        $query->orderBy($this->sortField, $this->sortDirection);
        $results = $query->paginate(10); // Paginação ajustada para 10 para melhor visualização da animação

        return view('livewire.universal.banners', [
            'results' => $results,
        ]);
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

    private function resetInputFields()
    {
        $this->banner_id = null;
        $this->nome = '';
        $this->descricao = '';
        $this->foto = null;
    }

    public function store()
    {
        $this->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'nome' => $this->nome,
                'descricao' => $this->descricao,
            ];

            if ($this->foto) { // Verifica se uma nova foto foi enviada
                // CORREÇÃO: Usando o novo disco e o novo caminho 'banner'
                if ($this->banner_id) {
                    $banner_antigo = Banner::find($this->banner_id);
                    // Apaga a foto antiga do disco correto
                    if ($banner_antigo && $banner_antigo->foto) {
                        Storage::disk('public_disk')->delete($banner_antigo->foto);
                    }
                }
                // Salva a nova foto em 'public/banner/' e armazena o caminho
                $data['foto'] = $this->foto->store('banner', 'public_disk');
            }

            Banner::updateOrCreate(['id' => $this->banner_id], $data);

            session()->flash('message', $this->banner_id ? 'Banner atualizado com sucesso!' : 'Banner criado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar o banner: ' . $e->getMessage());
        }

        $this->closeModal();
    }

    public function view($id)
    {
        try {
            $this->selectedBanner = Banner::findOrFail($id);
            $this->isViewOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar os dados do banner: ' . $e->getMessage());
        }
    }

    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedBanner = null;
    }

    public function edit($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $this->banner_id = $id;
            $this->nome = $banner->nome;
            $this->descricao = $banner->descricao;
            $this->foto = null;

            $this->openModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar o banner para edição: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $banner = Banner::find($this->confirmDeleteId);
            if ($banner) {
                // CORREÇÃO: Usando o novo disco para apagar a foto
                if ($banner->foto) {
                    Storage::disk('public_disk')->delete($banner->foto);
                }
                $banner->delete();
                session()->flash('message', 'Banner deletado com sucesso!');
            } else {
                session()->flash('error', 'Banner não encontrado.');
            }
            $this->confirmDeleteId = null;
        }
    }
}
