<div x-data="{ open: false }" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg shadow-sm">
    <div @click="open = !open" class="p-4 cursor-pointer">
        <div class="flex items-center justify-between">
            <p class="font-medium text-gray-900 dark:text-white">{{ $stat['name'] }}</p>
            <div class="flex items-center space-x-4">
                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($stat['total_votes'], 0, ',', '.') }} votos</span>
                <svg class="h-5 w-5 text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </div>
    <div x-show="open" x-transition class="p-4 border-t border-gray-200 dark:border-gray-600" style="display: none;">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Detalhes por Local de Votação:</h4>
        <div class="overflow-x-auto max-h-60">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left p-2">Local</th>
                        <th class="text-left p-2">Endereço (Zona/Seção)</th>
                        <th class="text-right p-2">Votos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($stat['details'] as $detail)
                    <tr>
                        <td class="p-2">{{ $detail->localVotacao->nome }}</td>
                        <td class="p-2 text-gray-500 dark:text-gray-400">{{ $detail->localVotacao->endereco }}</td>
                        <td class="p-2 text-right font-medium">{{ $detail->total_votos }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-2 text-center text-gray-500">Nenhum voto registrado para este candidato nesta cidade.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>