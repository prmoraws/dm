@section('title','Cadastros TDA')
<div class="tda-scope text-gray-900 dark:text-gray-100">
<style>.dark .tda-scope .bg-white{background-color:#1f2937!important}.dark .tda-scope .bg-gray-50,.dark .tda-scope .bg-gray-100,.dark .tda-scope .bg-gray-200{background-color:#374151!important}.dark .tda-scope .text-gray-500,.dark .tda-scope .text-gray-600,.dark .tda-scope .text-gray-700,.dark .tda-scope .text-gray-800,.dark .tda-scope .text-gray-900{color:#e5e7eb!important}.dark .tda-scope input,.dark .tda-scope textarea,.dark .tda-scope select{background:#1f2937!important;color:#f3f4f6!important;border-color:#4b5563!important}.dark .tda-scope table tr{border-color:#4b5563!important}</style>
<x-slot name="header">
<div class="flex items-center gap-3">
<img src="{{ asset('images/tda/tda-logo.svg') }}" alt="TDA" class="h-10 w-20 object-contain">
<h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Cadastros TDA</h2>
</div>
</x-slot>
<div class="min-h-screen bg-gray-100 px-4 py-10 dark:bg-gray-900">
<div class="mx-auto max-w-7xl">
@if(session('message'))<div class="mb-4 rounded-lg border-l-4 border-green-600 bg-green-50 p-4 text-green-800">{{ session('message') }}</div>@endif
@if(session('error'))<div class="mb-4 rounded-lg border-l-4 border-red-600 bg-red-50 p-4 text-red-800 dark:bg-red-900/40 dark:text-red-200">{{ session('error') }}</div>@endif
<div class="rounded-xl bg-white p-6 shadow-lg">
<div class="mb-6 grid gap-3 md:grid-cols-2">
<input wire:model.live.debounce.400ms="search" class="rounded-lg border-gray-300" placeholder="Buscar por Nome ou Celular...">
<select wire:model.live="filtro_bloco_id" class="rounded-lg border-gray-300">
<option value="">Todos os blocos</option>@foreach($blocos as $bloco)<option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>@endforeach</select>
</div>
<div class="hidden overflow-x-auto rounded-lg border md:block">
<table class="w-full text-sm">
<thead class="bg-gray-200 text-left uppercase text-gray-600">
<tr>
<th class="p-4">Nome</th>
<th class="p-4">Bloco</th>
<th class="p-4">Função</th>
<th class="p-4 text-center">Ações</th>
</tr>
</thead>
<tbody>@forelse($results as $item)<tr wire:key="cadastro-tda-{{ $item->id }}" class="border-b hover:bg-gray-50">
<td class="p-4 font-semibold">{{ $item->nome }}</td>
<td class="p-4">{{ $item->bloco->nome??'—' }}</td>
<td class="p-4">{{ ucfirst($item->condicao_atual) }}</td>
<td class="p-4">
<div class="flex justify-center gap-3">
<a target="_blank" title="Imprimir PDF" href="{{ route('universal.cadastros-tda.pdf.visualizar',$item) }}" class="text-gray-600 hover:text-black">Imprimir</a>
@if($item->termos_aceitos_count)<a target="_blank" title="Visualizar termos" href="{{ route('universal.cadastros-tda.termos.visualizar-todos',$item) }}" class="font-semibold text-purple-700">Termos</a>@endif
<button wire:click="visualizar({{ $item->id }})" class="text-green-700">Visualizar</button>
<button wire:click="editar({{ $item->id }})" class="text-blue-700">Editar</button>
<button wire:click="excluir({{ $item->id }})" wire:confirm="Excluir este cadastro?" class="text-red-700">Excluir</button>
</div>
</td>
</tr>@empty<tr>
<td colspan="4" class="p-10 text-center text-gray-500">Nenhum cadastro aprovado encontrado.</td>
</tr>@endforelse</tbody>
</table>
</div>
<div class="space-y-4 md:hidden">@forelse($results as $item)<article class="rounded-lg border p-5 shadow-sm">
<div class="flex gap-4">@if($item->foto)<img src="{{ Storage::disk('public_disk')->url($item->foto) }}" class="h-16 w-16 rounded-full object-cover">@endif<div class="flex-1">
<h3 class="font-bold">{{ $item->nome }}</h3>
<p class="text-sm text-gray-500">Bloco: {{ $item->bloco->nome??'—' }}</p>
<p class="text-sm text-gray-500">Função: {{ ucfirst($item->condicao_atual) }}</p>
</div>
</div>
<div class="mt-4 flex flex-wrap gap-2">
<a target="_blank" href="{{ route('universal.cadastros-tda.pdf.visualizar',$item) }}" class="rounded border px-3 py-2">Imprimir</a>
@if($item->termos_aceitos_count)<a target="_blank" href="{{ route('universal.cadastros-tda.termos.visualizar-todos',$item) }}" class="rounded bg-purple-700 px-3 py-2 text-white">Termos</a>@endif
<button wire:click="visualizar({{ $item->id }})" class="rounded bg-green-600 px-3 py-2 text-white">Ver</button>
<button wire:click="editar({{ $item->id }})" class="rounded bg-blue-600 px-3 py-2 text-white">Editar</button>
</div>
</article>@empty<p class="p-8 text-center">Nenhum cadastro.</p>@endforelse</div>
<div class="mt-6">{{ $results->links() }}</div>
</div>
</div>
</div>

@if($modalVisualizar && $selecionado)<div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-75 backdrop-blur-md" wire:click.self="fecharModais">
<div class="flex min-h-full items-center justify-center p-2 sm:p-6">
<div class="flex w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white text-gray-900 shadow-2xl dark:bg-gray-900 dark:text-gray-100" style="height:calc(100vh - 1rem);max-height:900px">
<header class="flex shrink-0 justify-between border-b border-gray-200 p-4 dark:border-gray-700 sm:p-6">
<div class="flex min-w-0 items-center gap-3 sm:gap-4">@if($selecionado->foto)<img src="{{ Storage::disk('public_disk')->url($selecionado->foto) }}" alt="Foto de {{ $selecionado->nome }}" class="shrink-0 rounded-full border-4 border-white object-cover shadow dark:border-gray-700" style="display:block;width:96px;height:96px;min-width:96px;max-width:96px;object-fit:cover">@endif<div class="min-w-0">
<h3 class="truncate text-lg font-bold sm:text-2xl">{{ $selecionado->nome }}</h3>
<p class="truncate text-sm text-gray-500 dark:text-gray-400 sm:text-base">{{ $selecionado->igreja->nome??'—' }} · {{ $selecionado->bloco->nome??'—' }}</p>
</div>
</div>
<button wire:click="fecharModais" class="shrink-0 p-2 text-2xl text-gray-500 dark:text-gray-300">×</button>
</header>
<div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 pb-5 text-gray-700 dark:text-gray-300 sm:px-6">
@php($grupos=['Dados pessoais'=>['Nascimento'=>'data_nascimento','Estado civil'=>'estado_civil','RG/CIN'=>'rg','CPF'=>'cpf','Celular'=>'celular','E-mail'=>'email','Facebook'=>'facebook','Instagram'=>'instagram','Escolaridade'=>'escolaridade','Profissão'=>'profissao'],'Endereço'=>['Endereço'=>'endereco','Número'=>'numero','CEP'=>'cep','Bairro'=>'bairro','Cidade'=>'cidade.nome','Estado'=>'estado.nome'],'Igreja e grupo'=>['Região'=>'regiao.nome','Ingresso no grupo'=>'data_ingresso_grupo','Função no grupo'=>'funcao_grupo'],'Dados espirituais'=>['Condição'=>'condicao_atual','Início na IURD'=>'inicio_iurd','Batizado nas águas'=>'batizado_aguas','Data batismo águas'=>'data_batismo_aguas','Batizado Espírito Santo'=>'batizado_espirito_santo','Data batismo Espírito Santo'=>'data_batismo_espirito_santo','Já se afastou'=>'ja_se_afastou'],'Família e emergência'=>['Tem filhos'=>'tem_filhos','Quantidade'=>'quantidade_filhos','Idades'=>'idade_filhos','Contato de emergência'=>'emergencia_nome','Celular emergência'=>'emergencia_celular']])
@foreach($grupos as $titulo=>$campos)<section class="mt-6">
<h4 class="mb-3 border-b pb-2 font-bold text-blue-600">{{ $titulo }}</h4>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($campos as $rotulo=>$campo)@php($valor=data_get($selecionado,$campo))<div>
<p class="text-xs uppercase text-gray-500">{{ $rotulo }}</p>
<p class="font-medium">@if(is_bool($valor)){{ $valor?'Sim':'Não' }}@elseif($valor instanceof \Carbon\CarbonInterface){{ $valor->format('d/m/Y') }}@else{{ filled($valor)?$valor:'—' }}@endif</p>
</div>@endforeach</div>
</section>@endforeach

@if($selecionado->termosAceitos->isNotEmpty())
<section class="mt-6">
<div class="mb-3 flex flex-col gap-3 border-b pb-3 sm:flex-row sm:items-center sm:justify-between">
<h4 class="font-bold text-purple-700 dark:text-purple-300">Termos assinados</h4>
<div class="flex flex-wrap gap-2">
<a target="_blank" href="{{ route('universal.cadastros-tda.termos.visualizar-todos',$selecionado) }}" class="rounded-lg bg-purple-700 px-3 py-2 text-sm font-semibold text-white">Visualizar todos</a>
<a href="{{ route('universal.cadastros-tda.termos.baixar-todos',$selecionado) }}" class="rounded-lg bg-green-700 px-3 py-2 text-sm font-semibold text-white">Baixar todos</a>
</div>
</div>
<div class="space-y-3">
@foreach($selecionado->termosAceitos->sortBy('id') as $aceite)
@php($termoConfig=config('tda.termos.'.$aceite->tipo))
@if($termoConfig)
<article class="flex flex-col gap-3 rounded-lg border p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
<div><p class="font-semibold">{{ $termoConfig['titulo'] }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Versão {{ $aceite->versao }} · Aceito em {{ $aceite->aceito_em?->format('d/m/Y H:i') }}</p></div>
<div class="flex shrink-0 gap-2">
<a target="_blank" href="{{ route('universal.cadastros-tda.termos.visualizar',[$selecionado,$aceite->tipo]) }}" class="rounded border px-3 py-2 text-sm font-semibold dark:border-gray-600">Visualizar</a>
<a href="{{ route('universal.cadastros-tda.termos.baixar',[$selecionado,$aceite->tipo]) }}" class="rounded bg-green-700 px-3 py-2 text-sm font-semibold text-white">Baixar</a>
</div>
</article>
@endif
@endforeach
</div>
</section>
@endif
</div>
<footer class="grid shrink-0 grid-cols-2 gap-2 border-t border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:flex sm:flex-wrap sm:justify-end sm:gap-3 sm:p-4">
<button wire:click="fecharModais" class="rounded-lg bg-gray-200 px-5 py-3 font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-200">Fechar</button>
<button wire:click="editar({{ $selecionado->id }})" class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white" style="background:#2563eb;color:#fff">Editar</button>
<a target="_blank" href="{{ route('universal.cadastros-tda.pdf.visualizar',$selecionado) }}" class="rounded-lg bg-gray-800 px-5 py-3 text-center font-semibold text-white" style="background:#1f2937;color:#fff">Visualizar PDF</a>
<a href="{{ route('universal.cadastros-tda.pdf.baixar',$selecionado) }}" class="rounded-lg bg-green-700 px-5 py-3 text-center font-semibold text-white" style="background:#15803d;color:#fff">Baixar PDF</a>
</footer>
</div>
</div>
</div>@endif

@if($modalEditar)<div class="fixed inset-0 z-50 overflow-y-auto bg-black/70" wire:click.self="fecharModais">
<div class="flex min-h-full items-center justify-center p-2 sm:p-6">
<form wire:submit.prevent="salvar" class="flex h-[calc(100dvh-1rem)] max-h-[920px] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl sm:h-[calc(100dvh-3rem)]">
<header class="flex shrink-0 justify-between border-b p-4 sm:p-6">
<h3 class="text-lg font-bold sm:text-xl">Editar Cadastro TDA</h3>
<button type="button" wire:click="fecharModais" class="p-2 text-2xl">×</button>
</header>
<div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 pb-5 sm:px-6">
@if($errors->any())<div class="mt-5 rounded-lg border-l-4 border-red-600 bg-red-50 p-4 text-red-700 dark:bg-red-900/40 dark:text-red-200"><p class="font-bold">Revise os campos destacados antes de atualizar.</p></div>@endif
<h4 class="mb-3 mt-5 border-b pb-2 font-bold text-blue-600">Igreja e localização</h4>
<div class="grid gap-4 md:grid-cols-3">
<x-tda-select campo="form.bloco_id" rotulo="Bloco" obrigatorio>
<option value="">Selecione</option>@foreach($blocos as $x)<option value="{{ $x->id }}">{{ $x->nome }}</option>@endforeach</x-tda-select>
<x-tda-select campo="form.regiao_id" rotulo="Região" obrigatorio>
<option value="">Selecione</option>@foreach($regiaos as $x)<option value="{{ $x->id }}">{{ $x->nome }}</option>@endforeach</x-tda-select>
<x-tda-select campo="form.igreja_id" rotulo="Igreja" obrigatorio>
<option value="">Selecione</option>@foreach($igrejas as $x)<option value="{{ $x->id }}">{{ $x->nome }}</option>@endforeach</x-tda-select>
<x-tda-select campo="form.estado_id" rotulo="Estado" obrigatorio>
<option value="">Selecione</option>@foreach($allEstados as $x)<option value="{{ $x->id }}">{{ $x->nome }}</option>@endforeach</x-tda-select>
<x-tda-select campo="form.cidade_id" rotulo="Cidade" obrigatorio>
<option value="">Selecione</option>@foreach($cidades as $x)<option value="{{ $x->id }}">{{ $x->nome }}</option>@endforeach</x-tda-select>
</div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Dados pessoais</h4>
<div class="grid gap-4 md:grid-cols-3"><div class="md:col-span-2"><x-tda-input campo="form.nome" rotulo="Nome completo" obrigatorio/></div><x-tda-input campo="form.data_nascimento" rotulo="Data de nascimento" tipo="date" obrigatorio/><x-tda-select campo="form.estado_civil" rotulo="Estado civil" obrigatorio><option value="solteiro">Solteiro(a)</option><option value="casado">Casado(a)</option><option value="divorciado">Divorciado(a)</option><option value="viuvo">Viúvo(a)</option><option value="uniao_estavel">União estável</option></x-tda-select><x-tda-input campo="form.rg" rotulo="RG/CIN"/><x-tda-input campo="form.cpf" rotulo="CPF" obrigatorio/><x-tda-select campo="form.sexo" rotulo="Sexo" obrigatorio><option value="feminino">Feminino</option><option value="masculino">Masculino</option></x-tda-select><x-tda-input campo="form.data_ingresso_grupo" rotulo="Data de ingresso no grupo" tipo="date"/><x-tda-input campo="form.funcao_grupo" rotulo="Função no grupo" obrigatorio/></div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Contato e endereço</h4>
<div class="grid gap-4 md:grid-cols-3"><x-tda-input campo="form.celular" rotulo="Celular/WhatsApp" obrigatorio/><x-tda-input campo="form.email" rotulo="E-mail" tipo="email"/><x-tda-input campo="form.facebook" rotulo="Facebook"/><x-tda-input campo="form.instagram" rotulo="Instagram"/><div class="md:col-span-2"><x-tda-input campo="form.endereco" rotulo="Endereço" obrigatorio/></div><x-tda-input campo="form.numero" rotulo="Número" obrigatorio/><x-tda-input campo="form.cep" rotulo="CEP"/><x-tda-input campo="form.bairro" rotulo="Bairro" obrigatorio/></div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Família e emergência</h4>
<div class="grid gap-4 md:grid-cols-3"><x-tda-input campo="form.escolaridade" rotulo="Grau de escolaridade" obrigatorio/><x-tda-input campo="form.profissao" rotulo="Profissão"/><x-tda-radio-sim-nao campo="form.tem_filhos" rotulo="Tem filhos?"/>@if(data_get($form,'tem_filhos'))<x-tda-input campo="form.quantidade_filhos" rotulo="Quantidade de filhos" tipo="number" obrigatorio/><x-tda-input campo="form.idade_filhos" rotulo="Idades dos filhos" obrigatorio/>@endif<div class="md:col-span-3 mt-2 border-t pt-4 font-bold">Contato de emergência</div><x-tda-input campo="form.emergencia_nome" rotulo="Nome" obrigatorio/><x-tda-input campo="form.emergencia_celular" rotulo="Celular/WhatsApp" obrigatorio/><x-tda-input campo="form.emergencia_facebook" rotulo="Facebook"/><x-tda-input campo="form.emergencia_instagram" rotulo="Instagram"/></div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Dados espirituais</h4>
<div class="grid gap-4 md:grid-cols-3"><x-tda-select campo="form.condicao_atual" rotulo="Condição atual" obrigatorio>@foreach(['membro'=>'Membro','cpo'=>'CPO','colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita','auxiliar'=>'Auxiliar'] as $valor=>$rotulo)<option value="{{ $valor }}">{{ $rotulo }}</option>@endforeach</x-tda-select><x-tda-input campo="form.inicio_iurd" rotulo="Início na IURD" tipo="date" obrigatorio/><x-tda-radio-sim-nao campo="form.batizado_aguas" rotulo="Batizado nas águas na IURD?"/>@if(data_get($form,'batizado_aguas'))<x-tda-input campo="form.data_batismo_aguas" rotulo="Data do batismo nas águas" tipo="date" obrigatorio/>@endif<x-tda-radio-sim-nao campo="form.batizado_espirito_santo" rotulo="Batizado com o Espírito Santo?"/>@if(data_get($form,'batizado_espirito_santo'))<x-tda-input campo="form.data_batismo_espirito_santo" rotulo="Data do batismo com o Espírito Santo" tipo="date" obrigatorio/>@endif<x-tda-radio-sim-nao campo="form.ja_se_afastou" rotulo="Já se afastou?"/></div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Participação semanal</h4>
<x-tda-dias campo="form.dias_reunioes" rotulo="Em quais dias participa das reuniões da igreja?"/><x-tda-dias campo="form.dias_evangelizacao" rotulo="Em quais dias evangeliza?"/>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Atividades e graduações</h4>
@if(data_get($form,'sexo')==='feminino')<div class="grid gap-4 md:grid-cols-2"><x-tda-radio-sim-nao campo="form.godllywood_autoajuda" rotulo="Participa do Godllywood Autoajuda?"/><x-tda-radio-sim-nao campo="form.meditacao_univer" rotulo="Assiste à meditação da Palavra no Univer?"/></div>@else<div class="grid gap-4 md:grid-cols-2"><x-tda-radio-sim-nao campo="form.intellimen_reunioes" rotulo="Participa das reuniões do IntelliMen?"/><x-tda-radio-sim-nao campo="form.intellimen_desafios" rotulo="Já fez ou faz os desafios do IntelliMen?"/></div>@endif
<div class="mt-5 space-y-3">@foreach(['colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita'] as $campo=>$rotulo)<div class="grid items-end gap-4 md:grid-cols-2"><label class="flex items-center gap-3 rounded-lg border p-3 dark:border-gray-600"><input type="checkbox" wire:model.live="form.{{ $campo }}" class="rounded text-red-700 focus:ring-red-600"><span>{{ $rotulo }}</span></label>@if(data_get($form,$campo))<x-tda-input campo="form.data_graduacao_{{ $campo }}" rotulo="Data da graduação" tipo="date" obrigatorio/>@endif</div>@endforeach</div><div class="mt-5"><x-tda-dias campo="form.dias_trabalho_reuniao" rotulo="Em quais dias trabalha na reunião?" opcional/></div>

<h4 class="mb-3 mt-6 border-b pb-2 font-bold text-blue-600">Foto</h4>
<div class="flex flex-col gap-4 sm:flex-row sm:items-center">@if($novaFoto)<img src="{{ $novaFoto->temporaryUrl() }}" alt="Nova foto" class="h-24 w-24 rounded-full object-cover shadow" style="width:96px;height:96px;object-fit:cover">@elseif(!empty($form['foto']))<img src="{{ Storage::disk('public_disk')->url($form['foto']) }}" alt="Foto atual" class="h-24 w-24 rounded-full object-cover shadow" style="width:96px;height:96px;object-fit:cover">@endif<div><input type="file" wire:model="novaFoto" accept="image/jpeg,image/png" class="block w-full text-sm"><p class="mt-1 text-xs text-gray-500">Deixe sem arquivo para manter a foto atual.</p><div wire:loading wire:target="novaFoto" class="mt-1 text-sm text-blue-600">Carregando pré-visualização...</div></div></div>@error('novaFoto')<p class="mt-1 text-red-600">{{ $message }}</p>@enderror
</div>
<footer class="grid shrink-0 grid-cols-2 gap-2 border-t bg-white p-3 sm:flex sm:justify-end sm:gap-3 sm:p-4">
<button type="button" wire:click="fecharModais" class="rounded border px-4 py-3 sm:px-5">Cancelar</button>
<button type="submit" wire:loading.attr="disabled" wire:target="salvar,novaFoto" class="rounded bg-blue-600 px-4 py-3 font-bold text-white disabled:cursor-not-allowed disabled:opacity-60 sm:px-5"><span wire:loading.remove wire:target="salvar">Atualizar</span><span wire:loading wire:target="salvar">Atualizando...</span></button>
</footer>
</form>
</div>
</div>@endif
</div>
