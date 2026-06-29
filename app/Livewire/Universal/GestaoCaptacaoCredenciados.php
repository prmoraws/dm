<?php

namespace App\Livewire\Universal;

use App\Models\Universal\CaptacaoCredenciado;
use App\Models\Universal\Credenciado;
use App\Models\Universal\CredencialPresidio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoCaptacaoCredenciados extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCaptacao;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $confirmingDeletion = false; // Controle do modal
    public $captacaoIdBeingDeleted = null;

    protected $queryString = ['search' => ['except' => '']];

    public function render()
    {
        $results = CaptacaoCredenciado::where('nome', 'like', '%' . $this->search . '%')
            ->where('status', 'pendente')
            ->latest()
            ->paginate(10);

        return view('livewire.universal.gestao-captacao-credenciados', [
            'results' => $results,
        ]); // Removido qualquer ->layout() para usar o padrão App
    }

    public function view($id)
    {
        $this->selectedCaptacao = CaptacaoCredenciado::findOrFail($id);
        $this->isViewOpen = true;
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $captacao = CaptacaoCredenciado::findOrFail($id);

            // 1. Mover Foto de Perfil
            $fotoFinal = $this->moveFile($captacao->foto, 'credenciados/foto');

            // 2. Mover Documentos de Identidade
            $idFrenteFinal = $this->moveFile($captacao->identidade_frente, 'credenciados/documento');
            $idVersoFinal = $this->moveFile($captacao->identidade_verso, 'credenciados/documento');

            // 3. Criar o registro definitivo de Credenciado
            $credenciado = Credenciado::create([
                'nome' => $captacao->nome,
                'celular' => $captacao->celular,
                'email' => $captacao->email,
                'bloco_id' => $captacao->bloco_id,
                'regiao_id' => $captacao->regiao_id,
                'igreja_id' => $captacao->igreja_id,
                'categoria_id' => $captacao->categoria_id,
                'cargo_id' => $captacao->cargo_id,
                'grupo_id' => $captacao->grupo_id,
                'estado_id' => $captacao->estado_id,
                'cidade_id' => $captacao->cidade_id,
                'endereco' => $captacao->endereco,
                'bairro' => $captacao->bairro,
                'cep' => $captacao->cep,
                'profissao' => $captacao->profissao,
                'aptidoes' => $captacao->aptidoes,
                'conversao' => $captacao->conversao,
                'obra' => $captacao->obra,
                'testemunho' => $captacao->testemunho,
                'trabalho' => $captacao->trabalho,
                'batismo' => $captacao->batismo,
                'preso' => $captacao->preso,
                'foto' => $fotoFinal,
                'identidade_frente' => $idFrenteFinal,
                'identidade_verso' => $idVersoFinal,
            ]);

            // 4. Mover e Criar as Credenciais (Máx 10)
            if ($captacao->credenciais_payload) {
                foreach ($captacao->credenciais_payload as $item) {
                    $cresFrente = $this->moveFile($item['foto_frente'], 'credenciados/credencial');
                    $cresVerso = $this->moveFile($item['foto_verso'], 'credenciados/credencial');

                    CredencialPresidio::create([
                        'credenciado_id' => $credenciado->id,
                        'presidio_id' => $item['presidio_id'],
                        'foto_frente' => $cresFrente,
                        'foto_verso' => $cresVerso,
                    ]);
                }
            }

            // 5. Deletar a captação temporária
            $captacao->delete();

            DB::commit();
            session()->flash('message', 'Captação aprovada e movida para Credenciados com sucesso!');
            $this->isViewOpen = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erro ao processar aprovação: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->captacaoIdBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        if (!$this->captacaoIdBeingDeleted) return;

        try {
            $captacao = \App\Models\Universal\CaptacaoCredenciado::findOrFail($this->captacaoIdBeingDeleted);

            // 1. Apagar arquivos da pasta CAPTURA (importante para não encher o host)
            $this->deleteFile($captacao->foto);
            $this->deleteFile($captacao->identidade_frente);
            $this->deleteFile($captacao->identidade_verso);

            // 2. Apagar fotos das credenciais que estão no JSON
            if ($captacao->credenciais_payload) {
                foreach ($captacao->credenciais_payload as $item) {
                    $this->deleteFile($item['foto_frente'] ?? null);
                    $this->deleteFile($item['foto_verso'] ?? null);
                }
            }

            // 3. Excluir o registro do banco
            $captacao->delete();

            session()->flash('message', 'Captação rejeitada e arquivos excluídos com sucesso.');

            $this->confirmingDeletion = false;
            $this->captacaoIdBeingDeleted = null;
            $this->isViewOpen = false; // Fecha o modal de revisão também, se estiver aberto

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares para movimentação de arquivos
    private function moveFile($path, $destinationFolder)
    {
        if (!$path || !Storage::disk('public_disk')->exists($path)) return null;

        $fileName = basename($path);
        $newPath = $destinationFolder . '/' . $fileName;

        Storage::disk('public_disk')->move($path, $newPath);
        return $newPath;
    }

    private function deleteFile($path)
    {
        if ($path && \Illuminate\Support\Facades\Storage::disk('public_disk')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public_disk')->delete($path);
        }
    }
}
