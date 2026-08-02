<?php

namespace App\Livewire\Universal;

use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoCaptacoesTda extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $status = 'pendente';
    public $selecionado = null;
    public bool $modalVisualizar = false;
    public bool $modalRejeitar = false;
    public ?int $acaoId = null;
    public string $motivo_rejeicao = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pendente'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function visualizar(int $id): void
    {
        $this->selecionado = $this->escopo(CaptacaoTda::with(['bloco','regiao','igreja','cidade','estado']))->findOrFail($id);
        $this->authorize('view', $this->selecionado);
        $this->modalVisualizar = true;
    }

    public function aprovar(int $id): void
    {
        $captacao = $this->escopo(CaptacaoTda::query())->where('status','pendente')->findOrFail($id);
        $this->authorize('review', $captacao);

        $obrigatorios = ['bloco_id','regiao_id','igreja_id','estado_id','cidade_id','data_ingresso_grupo','funcao_grupo','foto','nome','data_nascimento','estado_civil','rg','cpf','celular','endereco','cep','bairro','escolaridade','emergencia_nome','emergencia_celular','condicao_atual'];
        $faltantes = collect($obrigatorios)->filter(fn($campo) => blank($captacao->{$campo}));
        if ($faltantes->isNotEmpty()) {
            session()->flash('error','Complete antes de aprovar: '.$faltantes->implode(', ').'.');
            return;
        }
        if (CadastroTda::where('cpf',$captacao->cpf)->exists()) {
            session()->flash('error','Já existe um cadastro aprovado com este CPF.');
            return;
        }

        $disk = Storage::disk('public_disk');
        if (!$disk->exists($captacao->foto)) {
            session()->flash('error','A foto da solicitação não foi encontrada.');
            return;
        }
        $fotoOriginal = $captacao->foto;
        $extensao = pathinfo($fotoOriginal, PATHINFO_EXTENSION) ?: 'jpg';
        $fotoFinal = 'tda/cadastros/'.Str::uuid().'.'.$extensao;
        if (!$disk->copy($fotoOriginal,$fotoFinal)) {
            session()->flash('error','Não foi possível preparar a foto definitiva.');
            return;
        }

        try {
            DB::transaction(function() use ($captacao,$fotoFinal) {
                $dados = collect((new CadastroTda)->getFillable())->mapWithKeys(fn($campo)=>[$campo=>$captacao->{$campo}])->all();
                $dados['foto']=$fotoFinal;
                CadastroTda::create($dados);
                $captacao->update(['status'=>'aprovado','revisado_por'=>auth()->id(),'revisado_em'=>now(),'motivo_rejeicao'=>null,'foto'=>$fotoFinal]);
            });
            $disk->delete($fotoOriginal);
            $this->fecharModal();
            session()->flash('message','Solicitação aprovada. O cadastro já está disponível para o gestor.');
        } catch (\Throwable $e) {
            $disk->delete($fotoFinal);
            Log::error('Falha ao aprovar TDA',['tipo'=>$e::class,'mensagem'=>$e->getMessage()]);
            session()->flash('error','Não foi possível concluir a aprovação.');
        }
    }

    public function abrirRejeicao(int $id): void
    {
        $captacao=$this->escopo(CaptacaoTda::query())->where('status','pendente')->findOrFail($id);
        $this->authorize('review',$captacao);
        $this->acaoId=$id;
        $this->motivo_rejeicao='';
        $this->modalRejeitar=true;
    }

    public function rejeitar(): void
    {
        $this->validate(['motivo_rejeicao'=>['required','string','min:5','max:1000']]);
        $captacao=$this->escopo(CaptacaoTda::query())->where('status','pendente')->findOrFail($this->acaoId);
        $this->authorize('review',$captacao);
        $captacao->update(['status'=>'rejeitado','motivo_rejeicao'=>trim($this->motivo_rejeicao),'revisado_por'=>auth()->id(),'revisado_em'=>now()]);
        $this->fecharModal();
        session()->flash('message','Solicitação rejeitada.');
    }

    public function excluir(int $id): void
    {
        $captacao=$this->escopo(CaptacaoTda::query())->findOrFail($id);
        $this->authorize('delete',$captacao);
        $foto=$captacao->foto;
        $captacao->delete();
        if($foto && !CadastroTda::where('foto',$foto)->exists()) Storage::disk('public_disk')->delete($foto);
        session()->flash('message','Solicitação excluída.');
    }

    public function fecharModal(): void
    {
        $this->reset(['selecionado','modalVisualizar','modalRejeitar','acaoId','motivo_rejeicao']);
        $this->resetErrorBag();
    }

    private function escopo($query)
    {
        $time = strtolower((string) auth()->user()->currentTeam?->name);
        if (!in_array($time, ['tda', 'adm'], true)) {
            $query->where('bloco_id', auth()->user()->bloco_id);
        }
        return $query;
    }

    public function render()
    {
        $query=$this->escopo(CaptacaoTda::with(['igreja','bloco']))
            ->when($this->status,fn($q)=>$q->where('status',$this->status))
            ->when($this->search,function($q){$t='%'.trim($this->search).'%';$q->where(fn($s)=>$s->where('nome','like',$t)->orWhere('email','like',$t)->orWhere('celular','like',$t));})
            ->latest();
        return view('livewire.universal.gestao-captacoes-tda',['results'=>$query->paginate(10)]);
    }
}
