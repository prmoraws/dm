@php($massaDados = $this->dadosMassa())
<section class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg text-gray-800 dark:text-gray-100">
    <h3 class="text-xl font-bold mb-2">Convidados por ofício · inclusão em massa</h3>
    <p class="text-sm mb-4">Selecione o presídio e o ofício. Os ofícios aparecem do mais recente ao mais antigo; nas credenciais, usamos a data do cadastro.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="massa-presidio" class="block font-semibold mb-1">Presídio</label>
            <select id="massa-presidio" wire:model.live="massaPresidio" class="w-full rounded-md dark:bg-gray-700">
                <option value="">Selecione o presídio...</option>
                @foreach($massaDados['presidios'] as $presidio)
                    <option value="{{ $presidio->id }}">{{ $presidio->nome }}</option>
                @endforeach
            </select>
            @error('massaPresidio')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="massa-oficio" class="block font-semibold mb-1">Ofício · mais recentes primeiro</label>
            <select id="massa-oficio" wire:model.live="massaOficio" wire:key="massa-oficios-{{ $massaPresidio }}" @disabled(!$massaPresidio) class="w-full rounded-md dark:bg-gray-700">
                <option value="">Selecione o ofício...</option>
                @foreach($massaDados['oficios'] as $oficio)
                    <option value="{{ $oficio['value'] }}">{{ $oficio['text'] }}</option>
                @endforeach
            </select>
            @if($massaPresidio && !$massaDados['oficios'])<p class="text-sm mt-1">Nenhum ofício neste presídio.</p>@endif
            @error('massaOficio')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
    </div>
    @if($massaDados['disponivel'])
        @if($massaOficio)
            <details class="mb-4" open>
                <summary class="font-semibold cursor-pointer">Convidados vinculados ({{ $massaDados['convidados']->count() }})</summary>
                <ul class="mt-2 max-h-64 overflow-y-auto text-sm space-y-1">
                    @forelse($massaDados['convidados'] as $pessoa)
                        <li>{{ $pessoa->nome }} · {{ $pessoa->cpf }} · {{ $pessoa->classe }}</li>
                    @empty
                        <li>Nenhum convidado vinculado.</li>
                    @endforelse
                </ul>
            </details>
        @endif
        <form wire:submit="revisarMassa" class="space-y-3">
            <div>
                <label for="massa-classe" class="block font-semibold mb-1">Classe padrão para linhas sem classe</label>
                <select id="massa-classe" wire:model.live="massaClasse" class="rounded-md dark:bg-gray-700">
                    @foreach($classeOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                </select>
                @error('massaClasse')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="massa-texto" class="block font-semibold mb-1">Lista de convidados</label>
                <p class="text-sm mb-2">Uma pessoa por linha: Nome;CPF;RG;Classe. RG e classe são opcionais. Também aceita colunas copiadas de planilha, sem cabeçalho. Máximo: 500 pessoas.</p>
                <textarea id="massa-texto" wire:model.live.debounce.500ms="massaTexto" rows="6" class="w-full rounded-md dark:bg-gray-700" placeholder="NOME COMPLETO;CPF&#10;NOME COMPLETO;CPF;RG;organização"></textarea>
                @error('massaTexto')<p role="alert" class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <p class="text-sm">CPFs já cadastrados serão reutilizados, preservando seus dados. A lista existente do ofício será mantida.</p>
            <button type="submit" wire:loading.attr="disabled" @disabled(!$massaOficio) class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg disabled:opacity-50">Revisar lista</button>
        </form>
        @if($massaPrevia)
            <div class="mt-4">
                <h4 class="font-bold mb-2">Confira as {{ count($massaPrevia) }} pessoas antes de salvar</h4>
                <div class="overflow-auto max-h-80">
                    <table class="w-full text-sm text-left">
                        <thead><tr><th class="p-2">Nome</th><th class="p-2">CPF</th><th class="p-2">RG</th><th class="p-2">Classe</th><th class="p-2">Cadastro</th></tr></thead>
                        <tbody>
                            @foreach($massaPrevia as $pessoa)
                                <tr class="border-t dark:border-gray-600">
                                    <td class="p-2">{{ $pessoa['nome'] }}</td><td class="p-2">{{ $pessoa['cpf'] }}</td><td class="p-2">{{ $pessoa['rg'] }}</td><td class="p-2">{{ $pessoa['classe'] }}</td><td class="p-2">{{ $pessoa['situacao'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" wire:click="salvarMassa" wire:loading.attr="disabled" class="mt-3 bg-green-700 text-white font-semibold px-4 py-2 rounded-lg disabled:opacity-50">Salvar e vincular ao ofício</button>
                <span wire:loading wire:target="salvarMassa" class="ml-2 text-sm">Salvando...</span>
            </div>
        @endif
    @else
        <p>A inclusão em massa ainda não está disponível.</p>
    @endif
</section>
