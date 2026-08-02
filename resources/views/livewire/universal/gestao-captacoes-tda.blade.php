@section('title','Gestão de Captações TDA')
<div class="tda-scope text-gray-900 dark:text-gray-100">
<style>.dark .tda-scope .bg-white{background-color:#1f2937!important}.dark .tda-scope .bg-gray-50,.dark .tda-scope .bg-gray-100,.dark .tda-scope .bg-gray-200{background-color:#374151!important}.dark .tda-scope .text-gray-500,.dark .tda-scope .text-gray-600,.dark .tda-scope .text-gray-700,.dark .tda-scope .text-gray-800,.dark .tda-scope .text-gray-900{color:#e5e7eb!important}.dark .tda-scope input,.dark .tda-scope textarea,.dark .tda-scope select{background:#1f2937!important;color:#f3f4f6!important;border-color:#4b5563!important}.dark .tda-scope table tr{border-color:#4b5563!important}</style>
<x-slot name="header">
<div class="flex items-center gap-3"><img src="{{ asset('images/tda/tda-logo.svg') }}" alt="TDA" class="h-10 w-20 object-contain"><h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Gestão de Captações TDA</h2></div>
</x-slot>
<div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-900">
<div class="mx-auto max-w-7xl">
@if(session('message'))<div class="mb-4 rounded-lg border-l-4 border-green-600 bg-green-50 p-4 text-green-800">{{ session('message') }}</div>@endif
@if(session('error'))<div class="mb-4 rounded-lg border-l-4 border-red-600 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>@endif
<div class="rounded-xl bg-white p-6 shadow dark:bg-gray-800">
<div class="mb-5 flex flex-col justify-between gap-4 md:flex-row">
<input wire:model.live.debounce.400ms="search" class="w-full rounded-lg border-gray-300 md:max-w-md" placeholder="Buscar por nome, e-mail ou celular...">
<div class="inline-flex rounded-lg bg-gray-100 p-1">@foreach(['pendente'=>'Pendentes','aprovado'=>'Aprovados','rejeitado'=>'Rejeitados'] as $v=>$r)<button wire:click="$set('status','{{ $v }}')" class="rounded-md px-4 py-2 text-sm font-semibold {{ $status===$v?'bg-blue-600 text-white shadow':'text-gray-600' }}">{{ $r }}</button>@endforeach</div>
</div>
<div class="hidden overflow-x-auto md:block">
<table class="w-full text-sm">
<thead class="bg-gray-50 text-left uppercase text-gray-500">
<tr>
<th class="p-4">Nome</th>
<th class="p-4">Celular / Igreja</th>
<th class="p-4">Data</th>
<th class="p-4 text-center">Ações</th>
</tr>
</thead>
<tbody>@forelse($results as $item)<tr wire:key="captacao-{{ $item->id }}" class="border-b">
<td class="p-4 font-semibold">{{ $item->nome }}</td>
<td class="p-4">{{ $item->celular }}<br>
<small>{{ $item->igreja->nome??'—' }}</small>
</td>
<td class="p-4">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
<td class="p-4">
<div class="flex justify-center gap-3">
<button wire:click="visualizar({{ $item->id }})" title="Visualizar" class="text-blue-600">Visualizar</button>@if($item->status==='pendente')<button wire:click="aprovar({{ $item->id }})" wire:confirm="Aprovar esta solicitação?" class="text-green-700">Aprovar</button>
<button wire:click="abrirRejeicao({{ $item->id }})" class="text-red-700">Rejeitar</button>@endif<button wire:click="excluir({{ $item->id }})" wire:confirm="Excluir esta solicitação?" class="text-gray-500">Excluir</button>
</div>
</td>
</tr>@empty<tr>
<td colspan="4" class="p-10 text-center text-gray-500">Nenhuma solicitação encontrada.</td>
</tr>@endforelse</tbody>
</table>
</div>
<div class="space-y-3 md:hidden">@forelse($results as $item)<article class="rounded-lg border p-4">
<div class="flex items-center gap-3">@if($item->foto)<img src="{{ Storage::disk('public_disk')->url($item->foto) }}" class="h-14 w-14 rounded-full object-cover">@endif<div>
<b>{{ $item->nome }}</b>
<p class="text-sm text-gray-500">{{ $item->celular }} · {{ $item->igreja->nome??'—' }}</p>
</div>
</div>
<div class="mt-3 flex flex-wrap gap-2">
<button wire:click="visualizar({{ $item->id }})" class="rounded bg-blue-600 px-3 py-2 text-white">Ver</button>@if($item->status==='pendente')<button wire:click="aprovar({{ $item->id }})" wire:confirm="Aprovar?" class="rounded bg-green-700 px-3 py-2 text-white">Aprovar</button>
<button wire:click="abrirRejeicao({{ $item->id }})" class="rounded bg-red-700 px-3 py-2 text-white">Rejeitar</button>@endif</div>
</article>@empty<p class="p-8 text-center">Nenhuma solicitação.</p>@endforelse</div>
<div class="mt-5">{{ $results->links() }}</div>
</div>
</div>
</div>

@if($modalVisualizar && $selecionado)<div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-75 backdrop-blur-md" wire:click.self="fecharModal">
<div class="flex min-h-full items-center justify-center p-2 sm:p-6">
<div class="flex w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white text-gray-900 shadow-2xl dark:bg-gray-900 dark:text-gray-100" style="height:calc(100vh - 1rem);max-height:900px">
<header class="flex shrink-0 items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700 sm:p-5">
<div class="flex min-w-0 items-center gap-3 sm:gap-4">@if($selecionado->foto)<img src="{{ Storage::disk('public_disk')->url($selecionado->foto) }}" alt="Foto de {{ $selecionado->nome }}" class="shrink-0 rounded-full border-4 border-white object-cover shadow-lg dark:border-gray-700" style="display:block;width:96px;height:96px;min-width:96px;max-width:96px;object-fit:cover">@endif<div class="min-w-0">
<h3 class="truncate text-lg font-bold sm:text-xl">{{ $selecionado->nome }}</h3>
<span class="rounded-full bg-yellow-100 px-3 py-1 text-xs text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">{{ ucfirst($selecionado->status) }}</span>
</div>
</div>
<button wire:click="fecharModal" class="shrink-0 p-2 text-2xl text-gray-500 dark:text-gray-300">×</button>
</header>
<div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 text-gray-700 dark:text-gray-300 sm:p-6">
@php($grupos=['Pessoal e contato'=>['Nascimento'=>'data_nascimento','Estado civil'=>'estado_civil','RG/CIN'=>'rg','CPF'=>'cpf','Celular'=>'celular','E-mail'=>'email','Facebook'=>'facebook','Instagram'=>'instagram','Escolaridade'=>'escolaridade','Profissão'=>'profissao'],'Igreja e endereço'=>['Bloco'=>'bloco.nome','Região'=>'regiao.nome','Igreja'=>'igreja.nome','Ingresso no grupo'=>'data_ingresso_grupo','Função no grupo'=>'funcao_grupo','Endereço'=>'endereco','Número'=>'numero','CEP'=>'cep','Bairro'=>'bairro','Cidade'=>'cidade.nome','Estado'=>'estado.nome'],'Dados espirituais'=>['Condição atual'=>'condicao_atual','Início na IURD'=>'inicio_iurd','Batizado nas águas'=>'batizado_aguas','Data batismo águas'=>'data_batismo_aguas','Batizado Espírito Santo'=>'batizado_espirito_santo','Data batismo Espírito Santo'=>'data_batismo_espirito_santo','Já se afastou'=>'ja_se_afastou'],'Família e emergência'=>['Tem filhos'=>'tem_filhos','Quantidade de filhos'=>'quantidade_filhos','Idades'=>'idade_filhos','Contato de emergência'=>'emergencia_nome','Celular de emergência'=>'emergencia_celular']])
@foreach($grupos as $titulo=>$campos)<section class="mb-6">
<h4 class="mb-3 border-b pb-2 font-bold text-blue-600">{{ $titulo }}</h4>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($campos as $rotulo=>$campo)@php($valor=data_get($selecionado,$campo))<div>
<p class="text-xs uppercase text-gray-500">{{ $rotulo }}</p>
<p class="font-medium">@if(is_bool($valor)){{ $valor?'Sim':'Não' }}@elseif($valor instanceof \Carbon\CarbonInterface){{ $valor->format('d/m/Y') }}@else{{ filled($valor)?$valor:'—' }}@endif</p>
</div>@endforeach</div>
</section>@endforeach
@if($selecionado->motivo_rejeicao)<div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-900/50 dark:text-red-200">
<b>Motivo da rejeição:</b> {{ $selecionado->motivo_rejeicao }}</div>@endif</div>
<footer class="grid shrink-0 gap-2 border-t border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800 sm:grid-cols-3 sm:gap-3 sm:p-4">
<button wire:click="fecharModal" class="rounded-lg bg-gray-200 px-5 py-3 font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-200">Fechar</button>@if($selecionado->status==='pendente')<button wire:click="abrirRejeicao({{ $selecionado->id }})" class="rounded-lg bg-red-600 px-5 py-3 font-semibold text-white" style="display:block;background:#dc2626;color:#fff">Rejeitar</button>
<button wire:click="aprovar({{ $selecionado->id }})" class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white" style="display:block;background:#16a34a;color:#fff">Aprovar</button>@endif</footer>
</div>
</div>
</div>@endif
@if($modalRejeitar)<div class="fixed inset-0 z-[60] overflow-y-auto bg-black/60">
<div class="flex min-h-full items-center justify-center p-3 sm:p-6">
<div class="flex max-h-[calc(100dvh-1.5rem)] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
<header class="flex shrink-0 items-center justify-between border-b p-4 sm:p-5">
<h3 class="text-lg font-bold sm:text-xl">Motivo da rejeição</h3>
<button wire:click="fecharModal" class="p-2 text-2xl">×</button>
</header>
<div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
<textarea wire:model="motivo_rejeicao" rows="5" class="w-full rounded-lg border-gray-300">
</textarea>@error('motivo_rejeicao')<p class="mt-1 text-red-600">{{ $message }}</p>@enderror</div>
<footer class="grid shrink-0 grid-cols-2 gap-2 border-t bg-gray-50 p-3 sm:p-4">
<button wire:click="fecharModal" class="rounded border bg-white px-4 py-3">Cancelar</button>
<button wire:click="rejeitar" class="rounded bg-red-700 px-4 py-3 font-semibold text-white">Confirmar</button>
</footer>
</div>
</div>
</div>@endif
</div>
