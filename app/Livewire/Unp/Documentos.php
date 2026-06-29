<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Documento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// CORREÇÃO: Importando as classes necessárias do Google API Client
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;

class Documentos extends Component
{
    use WithPagination, WithFileUploads;

    public $documento_id, $nome, $classe, $titulo, $descricao;
    public $upload = [];
    public $existing_files = []; // Armazenará pares de [fileId => fileName]
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $selectedDocumento;
    public $classeOptions = ['oficio' => 'Ofício', 'lista' => 'Lista', 'doação' => 'Doação', 'outros' => 'Outros'];

    protected function rules()
    {
        return [
            'nome' => 'required|string|max:255|unique:documentos,nome,' . $this->documento_id,
            'classe' => 'required|in:oficio,lista,doação,outros',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'upload.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240'
        ];
    }

    public function render()
    {
        return view('livewire.unp.documentos', [
            'results' => Documento::where('nome', 'like', '%' . $this->searchTerm . '%')->latest()->paginate(10),
        ]);
    }

    public function store()
    {
        $this->validate();

        try {
            $currentFiles = $this->existing_files;

            if (!empty($this->upload)) {
                // Pega a instância do serviço do Google Drive
                $drive = Storage::disk('google')->getAdapter()->getService();
                $folderId = config('filesystems.disks.google.folderId');

                foreach ($this->upload as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileContent = file_get_contents($file->getRealPath());

                    $fileMetadata = new DriveFile([
                        'name' => $fileName,
                        'parents' => [$folderId], // Define a pasta de destino
                    ]);

                    $createdFile = $drive->files->create($fileMetadata, [
                        'data' => $fileContent,
                        'mimeType' => $file->getMimeType(),
                        'uploadType' => 'multipart',
                        'fields' => 'id, name', // Solicita o ID e o nome do arquivo criado
                    ]);

                    // Torna o arquivo público para qualquer pessoa com o link
                    $permission = new Permission(['type' => 'anyone', 'role' => 'reader']);
                    $drive->permissions->create($createdFile->id, $permission);

                    // Salva o ID real e o nome do arquivo
                    $currentFiles[$createdFile->id] = $createdFile->name;
                }
            }

            $validatedData = [
                'nome' => $this->nome,
                'classe' => $this->classe,
                'titulo' => $this->titulo,
                'descricao' => $this->descricao,
                'upload' => $currentFiles,
            ];

            Documento::updateOrCreate(['id' => $this->documento_id], $validatedData);
            session()->flash('message', 'Documento salvo com sucesso!');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erro no upload: ' . $e->getMessage());
            Log::error('Falha no Módulo Documentos: ' . $e->getMessage());
        }
    }

    public function removeFile($fileId)
    {
        try {
            $drive = Storage::disk('google')->getAdapter()->getService();
            $drive->files->delete($fileId); // Google API direto

            unset($this->existing_files[$fileId]);

            if ($this->documento_id) {
                $doc = Documento::find($this->documento_id);
                if ($doc) {
                    $doc->upload = $this->existing_files;
                    $doc->save();
                }
            }
            session()->flash('message', 'Arquivo removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível remover o arquivo.');
            Log::error('Erro ao remover do Drive: ' . $e->getMessage());
        }
    }


    public function delete()
    {
        if ($this->confirmDeleteId) {
            $doc = Documento::find($this->confirmDeleteId);
            if ($doc && is_array($doc->upload)) {
                try {
                    $drive = Storage::disk('google')->getAdapter()->getService();
                    foreach (array_keys($doc->upload) as $fileId) {
                        $drive->files->delete($fileId);
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao deletar arquivos do Drive: ' . $e->getMessage());
                }
            }

            $doc?->delete();
            session()->flash('message', 'Documento e arquivos associados deletados.');
            $this->confirmDeleteId = null;
        }
    }


    public function edit($id)
    {
        $doc = Documento::findOrFail($id);
        $this->documento_id = $id;
        $this->fill($doc);
        $this->existing_files = $doc->upload ?? [];
        $this->isOpen = true;
    }
    public function view($id)
    {
        $this->selectedDocumento = Documento::findOrFail($id);
        $this->isViewOpen = true;
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
    private function resetInputFields()
    {
        $this->reset();
    }
}