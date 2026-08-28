<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Universal\Bloco;
use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CadastrosTda extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    public string $search='';
    public string $filtro_bloco_id='';
    public $blocos=[],$allEstados=[],$regiaos=[],$igrejas=[],$cidades=[];
    public $selecionado=null,$novaFoto=null;
    public array $form=[];
    public bool $modalVisualizar=false,$modalEditar=false;
    public ?int $editarId=null;

    protected $queryString=['search'=>['except'=>''],'filtro_bloco_id'=>['except'=>'']];

    public function mount():void
    {
        $this->blocos=Bloco::orderBy('nome')->get();
        $this->allEstados=Estado::orderBy('nome')->get();
    }
    public function updatedSearch():void{$this->resetPage();}
    public function updatedFiltroBlocoId():void{$this->resetPage();}

    public function visualizar(int $id):void
    {
        $this->selecionado=$this->escopo(CadastroTda::with(['bloco','regiao','igreja','cidade','estado','responsavelLegal','termosAceitos']))->findOrFail($id);
        $this->authorize('view',$this->selecionado);
        $this->modalVisualizar=true;
    }

    public function editar(int $id):void
    {
        $item=$this->escopo(CadastroTda::query())->findOrFail($id);
        $this->authorize('update',$item);
        $this->editarId=$id;
        $this->form=$item->toArray();
        foreach($item->getCasts() as $campo=>$cast) if($cast==='date' && $item->{$campo}) $this->form[$campo]=$item->{$campo}->format('Y-m-d');
        $this->regiaos=Regiao::where('bloco_id',$item->bloco_id)->orderBy('nome')->get();
        $this->igrejas=Igreja::where('regiao_id',$item->regiao_id)->orderBy('nome')->get();
        $this->cidades=Cidade::where('estado_id',$item->estado_id)->orderBy('nome')->get();
        $this->novaFoto=null;
        $this->modalVisualizar=false;
        $this->modalEditar=true;
    }

    protected function rules():array
    {
        $temFilhos=$this->booleano(data_get($this->form,'tem_filhos'));
        $batizadoAguas=$this->booleano(data_get($this->form,'batizado_aguas'));
        $batizadoEspirito=$this->booleano(data_get($this->form,'batizado_espirito_santo'));
        $sexo=data_get($this->form,'sexo');
        return [
            'form.bloco_id'=>['required','exists:blocos,id'],'form.regiao_id'=>['required',Rule::exists('regiaos','id')->where('bloco_id',data_get($this->form,'bloco_id'))],'form.igreja_id'=>['required',Rule::exists('igrejas','id')->where('regiao_id',data_get($this->form,'regiao_id'))],'form.estado_id'=>['required','exists:estados,id'],'form.cidade_id'=>['required',Rule::exists('cidades','id')->where('estado_id',data_get($this->form,'estado_id'))],
            'form.nome'=>['required','string','min:3','max:255'],'form.cpf'=>['required','string','size:11',function($attribute,$value,$fail){if(!$this->cpfValido($value))$fail('O CPF informado não é válido.');},Rule::unique('cadastro_tdas','cpf')->ignore($this->editarId)],'form.rg'=>['nullable','string','max:30'],'form.celular'=>['required','string',function($attribute,$value,$fail){if(!$this->telefoneValido($value))$fail('O celular deve conter 10 ou 11 dígitos.');}],'form.email'=>['nullable','email','max:255'],
            'form.data_nascimento'=>['required','date','before:today'],'form.data_ingresso_grupo'=>['nullable','date','before_or_equal:today'],'form.funcao_grupo'=>['required','string','max:255'],'form.endereco'=>['required','string','max:255'],'form.numero'=>['required','string','max:30'],'form.cep'=>['nullable','string','max:10'],'form.bairro'=>['required','string','max:255'],
            'form.estado_civil'=>['required','string','max:50'],'form.escolaridade'=>['required','string','max:255'],'form.emergencia_nome'=>['required','string','max:255'],'form.emergencia_celular'=>['required','string',function($attribute,$value,$fail){if(!$this->telefoneValido($value))$fail('O celular de emergência deve conter 10 ou 11 dígitos.');}],'form.condicao_atual'=>['required',Rule::in(['membro','cpo','colaborador','obreiro','levita','auxiliar'])],
            'form.tem_filhos'=>['required','boolean'],'form.quantidade_filhos'=>[Rule::requiredIf($temFilhos),'nullable','integer','min:1','max:30'],'form.idade_filhos'=>[Rule::requiredIf($temFilhos),'nullable','string','max:255'],
            'form.inicio_iurd'=>['required','date','before_or_equal:today'],'form.batizado_aguas'=>['required','boolean'],'form.data_batismo_aguas'=>[Rule::requiredIf($batizadoAguas),'nullable','date','before_or_equal:today'],'form.batizado_espirito_santo'=>['required','boolean'],'form.data_batismo_espirito_santo'=>[Rule::requiredIf($batizadoEspirito),'nullable','date','before_or_equal:today'],'form.ja_se_afastou'=>['required','boolean'],
            'form.sexo'=>['required',Rule::in(['feminino','masculino'])],'form.godllywood_autoajuda'=>[Rule::requiredIf($sexo==='feminino'),'nullable','boolean'],'form.meditacao_univer'=>[Rule::requiredIf($sexo==='feminino'),'nullable','boolean'],'form.intellimen_reunioes'=>[Rule::requiredIf($sexo==='masculino'),'nullable','boolean'],'form.intellimen_desafios'=>[Rule::requiredIf($sexo==='masculino'),'nullable','boolean'],
            'form.colaborador'=>['required','boolean'],'form.obreiro'=>['required','boolean'],'form.levita'=>['required','boolean'],'form.data_graduacao_colaborador'=>[Rule::requiredIf($this->booleano(data_get($this->form,'colaborador'))),'nullable','date','before_or_equal:today'],'form.data_graduacao_obreiro'=>[Rule::requiredIf($this->booleano(data_get($this->form,'obreiro'))),'nullable','date','before_or_equal:today'],'form.data_graduacao_levita'=>[Rule::requiredIf($this->booleano(data_get($this->form,'levita'))),'nullable','date','before_or_equal:today'],
            'form.dias_reunioes'=>['required','array','min:1'],'form.dias_reunioes.*'=>[Rule::in($this->diasValidos())],'form.dias_evangelizacao'=>['required','array','min:1'],'form.dias_evangelizacao.*'=>[Rule::in($this->diasValidos())],'form.dias_trabalho_reuniao'=>['nullable','array'],'form.dias_trabalho_reuniao.*'=>[Rule::in($this->diasValidos())],'novaFoto'=>['nullable','image','mimes:jpeg,jpg,png','max:5120'],
        ];
    }

    public function updatedFormBlocoId($id):void{$this->regiaos=$id?Regiao::where('bloco_id',$id)->orderBy('nome')->get():collect();$this->form['regiao_id']=$this->form['igreja_id']=null;$this->igrejas=collect();}
    public function updatedFormRegiaoId($id):void{$this->igrejas=$id?Igreja::where('regiao_id',$id)->orderBy('nome')->get():collect();$this->form['igreja_id']=null;}
    public function updatedFormEstadoId($id):void{$this->cidades=$id?Cidade::where('estado_id',$id)->orderBy('nome')->get():collect();$this->form['cidade_id']=null;}
    public function updatedFormTemFilhos($valor):void{if(!$this->booleano($valor)){$this->form['quantidade_filhos']=$this->form['idade_filhos']=null;}}
    public function updatedFormBatizadoAguas($valor):void{if(!$this->booleano($valor))$this->form['data_batismo_aguas']=null;}
    public function updatedFormBatizadoEspiritoSanto($valor):void{if(!$this->booleano($valor))$this->form['data_batismo_espirito_santo']=null;}
    public function updatedFormColaborador($valor):void{if(!$this->booleano($valor))$this->form['data_graduacao_colaborador']=null;}
    public function updatedFormObreiro($valor):void{if(!$this->booleano($valor))$this->form['data_graduacao_obreiro']=null;}
    public function updatedFormLevita($valor):void{if(!$this->booleano($valor))$this->form['data_graduacao_levita']=null;}
    public function updatedFormSexo($valor):void
    {
        if($valor==='feminino')$this->form['intellimen_reunioes']=$this->form['intellimen_desafios']=null;
        if($valor==='masculino')$this->form['godllywood_autoajuda']=$this->form['meditacao_univer']=null;
    }

    public function salvar():void
    {
        foreach(['cpf','celular','emergencia_celular'] as $campo){
            if(array_key_exists($campo,$this->form))$this->form[$campo]=preg_replace('/\D/','',(string)$this->form[$campo]);
        }
        $this->validate();
        $dados=collect((new CadastroTda)->getFillable())
            ->filter(fn($campo)=>array_key_exists($campo,$this->form))
            ->mapWithKeys(fn($campo)=>[$campo=>$this->form[$campo]])
            ->all();
        $item=$this->escopo(CadastroTda::query())->findOrFail($this->editarId);
        $this->authorize('update',$item);
        $datasOpcionais=['data_ingresso_grupo','data_batismo_aguas','data_batismo_espirito_santo','data_graduacao_colaborador','data_graduacao_obreiro','data_graduacao_levita'];
        foreach($datasOpcionais as $campo)if(array_key_exists($campo,$dados)&&blank($dados[$campo]))$dados[$campo]=null;
        foreach(['tem_filhos','batizado_aguas','batizado_espirito_santo','ja_se_afastou','godllywood_autoajuda','meditacao_univer','intellimen_reunioes','intellimen_desafios','colaborador','obreiro','levita'] as $campo)if(array_key_exists($campo,$dados)&&$dados[$campo]!==null)$dados[$campo]=$this->booleano($dados[$campo]);
        if(!$dados['tem_filhos'])$dados['quantidade_filhos']=$dados['idade_filhos']=null;
        if(!$dados['batizado_aguas'])$dados['data_batismo_aguas']=null;
        if(!$dados['batizado_espirito_santo'])$dados['data_batismo_espirito_santo']=null;
        foreach(['colaborador','obreiro','levita'] as $campo)if(!$dados[$campo])$dados['data_graduacao_'.$campo]=null;
        if(($dados['sexo']??null)==='feminino')$dados['intellimen_reunioes']=$dados['intellimen_desafios']=null;else $dados['godllywood_autoajuda']=$dados['meditacao_univer']=null;

        $disk=Storage::disk('public_disk');
        $fotoAntiga=$item->foto;
        $fotoNova=null;
        try{
            if($this->novaFoto){
                $fotoNova=$this->novaFoto->store('tda/cadastros','public_disk');
                if(!$fotoNova)throw new \RuntimeException('Falha ao armazenar a nova foto.');
                $dados['foto']=$fotoNova;
            }else{
                unset($dados['foto']);
            }
            DB::transaction(function()use($item,$dados,$fotoAntiga,$fotoNova){
                $item->update($dados);
                if($fotoNova&&$fotoAntiga)CaptacaoTda::where('foto',$fotoAntiga)->update(['foto'=>$fotoNova]);
            });
            if($fotoNova&&$fotoAntiga&&$fotoAntiga!==$fotoNova&&!CadastroTda::where('foto',$fotoAntiga)->exists()&&!CaptacaoTda::where('foto',$fotoAntiga)->exists())$disk->delete($fotoAntiga);
            $this->fecharModais();
            session()->flash('message','Cadastro atualizado com sucesso.');
        }catch(\Throwable $e){
            if($fotoNova)$disk->delete($fotoNova);
            Log::error('Falha ao atualizar Cadastro TDA',['cadastro_id'=>$item->id,'tipo'=>$e::class,'mensagem'=>$e->getMessage()]);
            session()->flash('error','Não foi possível atualizar o cadastro. Nenhuma alteração foi concluída.');
        }
    }

    public function excluir(int $id):void
    {
        $item=$this->escopo(CadastroTda::query())->findOrFail($id);
        $this->authorize('delete',$item);
        $foto=$item->foto;
        $assinatura=$item->assinatura;
        $assinaturaTestemunha=$item->testemunha_assinatura;
        $item->delete();
        if($foto && !CaptacaoTda::where('foto',$foto)->exists()) Storage::disk('public_disk')->delete($foto);
        if($assinatura && !CaptacaoTda::where('assinatura',$assinatura)->exists()) Storage::disk('public_disk')->delete($assinatura);
        if($assinaturaTestemunha && !CaptacaoTda::where('testemunha_assinatura',$assinaturaTestemunha)->exists()) Storage::disk('public_disk')->delete($assinaturaTestemunha);
        session()->flash('message','Cadastro excluído.');
    }

    public function fecharModais():void{$this->reset(['selecionado','form','editarId','novaFoto','modalVisualizar','modalEditar']);$this->resetErrorBag();}
    private function escopo($q)
    {
        $time = strtolower((string) auth()->user()->currentTeam?->name);
        if (!in_array($time, ['tda', 'adm'], true)) {
            $q->where('bloco_id', auth()->user()->bloco_id);
        }
        return $q;
    }
    private function booleano($valor):bool{return filter_var($valor,FILTER_VALIDATE_BOOLEAN);}
    private function diasValidos():array{return ['segunda','terca','quarta','quinta','sexta','sabado','domingo'];}
    private function telefoneValido($valor):bool{return preg_match('/^\d{10,11}$/',(string)$valor)===1;}
    private function cpfValido($valor):bool
    {
        $cpf=preg_replace('/\D/','',(string)$valor);
        if(strlen($cpf)!==11 || preg_match('/^(\d)\1{10}$/',$cpf))return false;
        for($t=9;$t<11;$t++){
            $soma=0;
            for($i=0;$i<$t;$i++)$soma+=(int)$cpf[$i]*(($t+1)-$i);
            $digito=((10*$soma)%11)%10;
            if((int)$cpf[$t]!==$digito)return false;
        }
        return true;
    }

    public function render()
    {
        $query=$this->escopo(CadastroTda::with(['bloco','igreja'])->withCount('termosAceitos'))
            ->when($this->filtro_bloco_id,fn($q)=>$q->where('bloco_id',$this->filtro_bloco_id))
            ->when($this->search,function($q){$t='%'.trim($this->search).'%';$q->where(fn($s)=>$s->where('nome','like',$t)->orWhere('celular','like',$t));})
            ->orderBy('nome');
        return view('livewire.universal.cadastros-tda',['results'=>$query->paginate(10)]);
    }
}
