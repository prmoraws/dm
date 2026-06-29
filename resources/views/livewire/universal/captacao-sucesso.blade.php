<div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-100 dark:bg-gray-900">
    @section('title', 'Envio Concluído')

    <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-2xl rounded-3xl p-8 text-center border border-gray-100 dark:border-gray-700 animate-fade-in">
        
        {{-- Ícone de Sucesso --}}
        <div class="mb-6 flex justify-center">
            <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-4">
                <svg class="w-16 h-16 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Tudo Pronto!</h2>
        
        <p class="text-gray-600 dark:text-gray-400 mb-8 text-sm">
            Seus dados e credenciais foram enviados para a <strong>Secretaria da UNP</strong>. 
            Nossa equipe fará a revisão das informações e fotos.
        </p>

        <div class="space-y-4">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300 text-left">
                <p class="font-bold mb-1 italic">Próximos passos:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Análise técnica das fotos enviadas.</li>
                    <li>Liberação do seu acesso administrativo.</li>
                    <li>Emissão das credenciais aprovadas.</li>
                </ul>
            </div>

            <a href="/" class="block w-full py-3 px-6 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 font-bold rounded-xl hover:bg-gray-700 dark:hover:bg-white transition-all shadow-lg text-sm">
                Voltar para o Início
            </a>
        </div>

        <p class="mt-8 text-[10px] text-gray-400 uppercase tracking-widest">
            Universal nos Presídios • Bahia
        </p>
    </div>
</div>