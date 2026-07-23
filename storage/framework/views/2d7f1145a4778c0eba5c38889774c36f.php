<?php $__env->startSection('title', 'Credenciados'); ?>

<?php use \Carbon\Carbon; ?>

<div x-data="{
    fotoModalOpen: false,
    fotoUrlAtual: '',
    fotoTituloAtual: '',
    abrirFoto(url, titulo) {
        this.fotoUrlAtual = url;
        this.fotoTituloAtual = titulo;
        this.fotoModalOpen = true;
    }
}">
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center space-x-3 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                <?php echo e(__('Credenciados')); ?>

            </h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-2 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-teal-800 dark:text-teal-200"><?php echo e(session('message')); ?></p>
                        </div>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center mb-6 gap-4">
                    <div class="w-full md:w-1/2">
                        <input wire:model.live.debounce.500ms="search" type="text"
                            placeholder="Buscar Credenciado..."
                            class="w-full px-4 py-3 sm:py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 sm:py-2 rounded-lg shadow-md flex items-center justify-center gap-2 w-full md:w-auto transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Credenciado
                    </button>
                </div>

                
                <div
                    class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6 text-left">Nome</th>
                                <th class="py-4 px-6 text-left">Presídio(s)</th>
                                <th class="py-4 px-6 text-left">Igreja</th>
                                <th class="py-4 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody
                            class="text-gray-600 dark:text-gray-300 text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $credenciado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr wire:key="cred-<?php echo e($credenciado->id); ?>"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                        <?php echo e($credenciado->nome); ?>

                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $credenciado->credencialPresidios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <span
                                                    class="inline-block text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800 px-2 py-1 rounded font-semibold whitespace-nowrap">
                                                    <?php echo e($cp->presidio->nome ?? 'N/A'); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span class="text-gray-400 italic text-xs">Nenhum</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        <?php echo e($credenciado->igreja->nome ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view(<?php echo e($credenciado->id); ?>)"
                                                class="w-6 h-6 text-gray-400 hover:text-green-500 transition"
                                                title="Visualizar">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button wire:click="edit(<?php echo e($credenciado->id); ?>)"
                                                class="w-6 h-6 text-gray-400 hover:text-blue-500 transition"
                                                title="Editar">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete(<?php echo e($credenciado->id); ?>)"
                                                class="w-6 h-6 text-gray-400 hover:text-red-500 transition"
                                                title="Excluir">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Nenhum credenciado encontrado.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="block md:hidden space-y-4 mt-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $credenciado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div wire:key="mobile-cred-<?php echo e($credenciado->id); ?>"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-3">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                        <?php echo e($credenciado->nome); ?>

                                    </p>
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $credenciado->credencialPresidios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <span
                                                class="inline-block text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800 px-2 py-1 rounded font-semibold">
                                                <?php echo e($cp->presidio->nome ?? 'N/A'); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <span class="text-xs text-gray-400">Sem presídios</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                        <strong>Igreja:</strong> <?php echo e($credenciado->igreja->nome ?? 'N/A'); ?>

                                    </p>
                                </div>
                                <div
                                    class="flex flex-col space-y-3 border-l border-gray-100 dark:border-gray-700 pl-3 justify-center">
                                    <button wire:click="view(<?php echo e($credenciado->id); ?>)"
                                        class="p-2 bg-gray-50 dark:bg-gray-700 rounded-full text-gray-500 hover:text-green-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit(<?php echo e($credenciado->id); ?>)"
                                        class="p-2 bg-gray-50 dark:bg-gray-700 rounded-full text-gray-500 hover:text-blue-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete(<?php echo e($credenciado->id); ?>)"
                                        class="p-2 bg-gray-50 dark:bg-gray-700 rounded-full text-gray-500 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center text-gray-500 dark:text-gray-400">
                            Nenhum registro encontrado.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->hasPages()): ?>
                    <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <?php echo e($results->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
                <div
                    class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-2 sm:p-4">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-6xl flex flex-col relative border border-gray-200 dark:border-gray-700 overflow-hidden"
                        style="max-height: 92vh;">

                        <div
                            class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex justify-between items-center rounded-t-xl shrink-0">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100 truncate pr-3">
                                <?php echo e($credenciado_id ? 'Editar Credenciado' : 'Novo Credenciado'); ?>

                            </h3>
                            <button wire:click="closeModal" type="button" title="Fechar"
                                class="shrink-0 text-gray-500 dark:text-gray-300 hover:text-white bg-gray-100 hover:bg-red-600 dark:bg-gray-700 dark:hover:bg-red-600 rounded-full p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-4 sm:p-6 space-y-6 overflow-y-auto flex-1 custom-scrollbar"
                            style="min-height: 0;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
                                <div
                                    class="p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                    <p class="text-sm font-medium"><?php echo e($errorMessage); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <form wire:submit.prevent="store" enctype="multipart/form-data" id="credenciadoForm"
                                class="space-y-6">
                                <section
                                    class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4
                                        class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-2 mb-4">
                                        Liderança e Grupos</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bloco</label>
                                            <select wire:model.live="bloco_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allBlocos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bloco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($bloco->id); ?>"><?php echo e($bloco->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Região</label>
                                            <select wire:model.live="regiao_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                <?php if(empty($regiaos)): ?> disabled <?php endif; ?>>
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regiaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regiao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($regiao->id); ?>"><?php echo e($regiao->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Igreja</label>
                                            <select wire:model="igreja_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                <?php if(empty($igrejas)): ?> disabled <?php endif; ?>>
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($igreja->id); ?>"><?php echo e($igreja->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                                            <select wire:model="categoria_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCategorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nome); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                                            <select wire:model="cargo_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCargos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cargo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($cargo->id); ?>"><?php echo e($cargo->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grupo</label>
                                            <select wire:model="grupo_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($grupo->id); ?>"><?php echo e($grupo->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </section>

                                <section
                                    class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4
                                        class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-2 mb-4">
                                        Informações Pessoais</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Nome
                                                Completo</label>
                                            <input type="text" wire:model="nome"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Celular</label>
                                            <input type="text" wire:model="celular"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                            <input type="email" wire:model="email"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Profissão</label>
                                            <input type="text" wire:model="profissao"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Foto
                                                Perfil</label>
                                            <input type="file" wire:model="foto"
                                                class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoAtual && !$foto): ?>
                                                <div
                                                    class="mt-2 w-24 bg-white dark:bg-gray-900 rounded-lg p-1 border dark:border-gray-700 shadow-sm">
                                                    <img src="<?php echo e(asset($fotoAtual)); ?>"
                                                        @click="abrirFoto('<?php echo e(asset($fotoAtual)); ?>', 'Foto de Perfil')"
                                                        class="aspect-square w-full rounded object-contain bg-white cursor-pointer hover:opacity-80 transition"
                                                        title="Clique para ampliar">
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </section>

                                <section
                                    class="bg-yellow-50 dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-yellow-200 dark:border-gray-700">
                                    <h4 class="text-md font-semibold text-yellow-700 dark:text-yellow-400 mb-4">
                                        Documentos de Identidade</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div
                                            class="bg-white dark:bg-gray-900 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <label
                                                class="block text-xs font-bold uppercase text-gray-500 mb-2">Frente</label>
                                            <input type="file" wire:model="identidade_frente"
                                                class="w-full text-sm text-gray-500 mb-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idFrenteAtual && !$identidade_frente): ?>
                                                <div
                                                    class="w-full overflow-hidden rounded border dark:border-gray-700 bg-white p-1">
                                                    <img src="<?php echo e(asset($idFrenteAtual)); ?>"
                                                        @click="abrirFoto('<?php echo e(asset($idFrenteAtual)); ?>', 'Documento - Frente')"
                                                        class="h-20 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                        title="Clique para ampliar">
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-gray-900 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <label
                                                class="block text-xs font-bold uppercase text-gray-500 mb-2">Verso</label>
                                            <input type="file" wire:model="identidade_verso"
                                                class="w-full text-sm text-gray-500 mb-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idVersoAtual && !$identidade_verso): ?>
                                                <div
                                                    class="w-full overflow-hidden rounded border dark:border-gray-700 bg-white p-1">
                                                    <img src="<?php echo e(asset($idVersoAtual)); ?>"
                                                        @click="abrirFoto('<?php echo e(asset($idVersoAtual)); ?>', 'Documento - Verso')"
                                                        class="h-20 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                        title="Clique para ampliar">
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </section>

                                <section
                                    class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4
                                        class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-2 mb-4">
                                        Endereço</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div class="lg:col-span-2">
                                            <label
                                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                                            <input type="text" wire:model="endereco"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Bairro</label>
                                            <input type="text" wire:model="bairro"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                                            <select wire:model.live="estado_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allEstados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($estado->id); ?>"><?php echo e($estado->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                                            <select wire:model="cidade_id"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                <?php if(empty($cidades)): ?> disabled <?php endif; ?>>
                                                <option value="">Selecione</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cidade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($cidade->id); ?>"><?php echo e($cidade->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                                            <input type="text" wire:model="cep"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                </section>

                                <section
                                    class="bg-blue-50 dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-blue-200 dark:border-gray-700">
                                    <div
                                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                                        <h4 class="text-md font-semibold text-blue-700 dark:text-blue-400">Credenciais
                                            (Máx. 10)</h4>
                                        <button type="button" wire:click="addCredencial"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-3 sm:py-2 rounded-lg shadow-md w-full sm:w-auto">
                                            + NOVA CREDENCIAL
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $credenciais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cred): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div
                                                class="p-4 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 relative shadow-sm space-y-4">
                                                <button type="button"
                                                    wire:click="removeCredencial(<?php echo e($index); ?>)"
                                                    class="absolute top-2 right-2 text-red-400 hover:text-red-600 bg-red-50 p-1 rounded-full">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold uppercase text-gray-500 mb-1">Presídio</label>
                                                        <select
                                                            wire:model.live="credenciais.<?php echo e($index); ?>.presidio_id"
                                                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                                            <option value="">Selecione...</option>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allPresidios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presidio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($presidio->id); ?>">
                                                                    <?php echo e($presidio->nome); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold uppercase text-gray-500 mb-1">Data
                                                            de Vencimento</label>
                                                        <input type="date"
                                                            wire:model="credenciais.<?php echo e($index); ?>.data_vencimento"
                                                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                                    </div>

                                                    <div class="flex items-center pt-5">
                                                        <label class="flex items-center space-x-2 cursor-pointer">
                                                            <input type="checkbox"
                                                                wire:model.live="credenciais.<?php echo e($index); ?>.unidade_nao_faz"
                                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4">
                                                            <span
                                                                class="text-xs font-bold text-red-600 dark:text-red-400">A
                                                                unidade não faz credencial (Apenas cadastro)</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($cred['unidade_nao_faz'])): ?>
                                                    <div
                                                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold uppercase text-gray-500 mb-1">Foto
                                                                Frente</label>
                                                            <input type="file"
                                                                wire:model="credenciais.<?php echo e($index); ?>.foto_frente"
                                                                class="w-full text-xs text-gray-500 mb-1">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cred['foto_frente_atual']): ?>
                                                                <div
                                                                    class="w-32 bg-white rounded border dark:border-gray-700 p-1 shadow-sm mt-1">
                                                                    <img src="<?php echo e(asset($cred['foto_frente_atual'])); ?>"
                                                                        @click="abrirFoto('<?php echo e(asset($cred['foto_frente_atual'])); ?>', 'Credencial - Frente')"
                                                                        class="h-16 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                                        title="Clique para ampliar">
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold uppercase text-gray-500 mb-1">Foto
                                                                Verso</label>
                                                            <input type="file"
                                                                wire:model="credenciais.<?php echo e($index); ?>.foto_verso"
                                                                class="w-full text-xs text-gray-500 mb-1">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cred['foto_verso_atual']): ?>
                                                                <div
                                                                    class="w-32 bg-white rounded border dark:border-gray-700 p-1 shadow-sm mt-1">
                                                                    <img src="<?php echo e(asset($cred['foto_verso_atual'])); ?>"
                                                                        @click="abrirFoto('<?php echo e(asset($cred['foto_verso_atual'])); ?>', 'Credencial - Verso')"
                                                                        class="h-16 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                                        title="Clique para ampliar">
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 text-center">
                                                        <span
                                                            class="text-xs font-bold text-red-600 dark:text-red-400">Esta
                                                            unidade realiza apenas o cadastro. Nenhuma foto de
                                                            carterinha é necessária.</span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </section>
                            </form>
                        </div>

                        <div
                            class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col sm:flex-row justify-end gap-3 rounded-b-xl shrink-0">
                            <button type="button" wire:click="closeModal"
                                class="w-full sm:w-auto bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold py-3 sm:py-2 px-5 rounded-lg transition">
                                Cancelar
                            </button>
                            <button type="submit" form="credenciadoForm"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 sm:py-2 px-5 rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                                <?php echo e($credenciado_id ? 'Atualizar Dados' : 'Salvar Novo'); ?>

                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewOpen && $selectedCredenciado): ?>
                <div
                    class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-2 sm:p-4">
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-5xl flex flex-col relative border border-gray-200 dark:border-gray-700 overflow-hidden"
                        style="max-height: 92vh;">

                        <div
                            class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-900 rounded-t-xl shrink-0">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100 truncate pr-3">
                                Ficha Completa do Credenciado</h3>
                            <button wire:click="closeViewModal" type="button" title="Fechar"
                                class="shrink-0 text-gray-500 dark:text-gray-300 hover:text-white bg-gray-100 hover:bg-red-600 dark:bg-gray-800 dark:hover:bg-red-600 rounded-full p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-4 sm:p-8 space-y-8 overflow-y-auto flex-1 custom-scrollbar"
                            style="min-height: 0;">
                            
                            <div
                                class="flex flex-col md:flex-row items-center md:items-start gap-6 bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <div
                                    class="bg-white dark:bg-gray-900 p-1.5 rounded-2xl shadow-sm border dark:border-gray-700">
                                    <img src="<?php echo e($selectedCredenciado->foto ? asset($selectedCredenciado->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedCredenciado->nome) . '&color=7F9CF5&background=EBF4FF'); ?>"
                                        <?php if($selectedCredenciado->foto): ?> @click="abrirFoto('<?php echo e(asset($selectedCredenciado->foto)); ?>', '<?php echo e($selectedCredenciado->nome); ?>')" <?php endif; ?>
                                        class="h-32 w-32 sm:h-40 sm:w-40 rounded-xl object-cover <?php echo e($selectedCredenciado->foto ? 'cursor-pointer hover:opacity-90 transition' : ''); ?>"
                                        title="<?php echo e($selectedCredenciado->foto ? 'Clique para ampliar' : ''); ?>">
                                </div>

                                <div class="flex-1 text-center md:text-left space-y-2">
                                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                        <?php echo e($selectedCredenciado->nome); ?></h2>
                                    <div class="flex flex-wrap justify-center md:justify-start gap-2 pt-1">
                                        <span
                                            class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-bold px-3 py-1 rounded-full">Cargo:
                                            <?php echo e($selectedCredenciado->cargo->nome ?? 'N/A'); ?></span>
                                        <span
                                            class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-xs font-bold px-3 py-1 rounded-full">Categoria:
                                            <?php echo e($selectedCredenciado->categoria->nome ?? 'N/A'); ?></span>
                                        <span
                                            class="bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 text-xs font-bold px-3 py-1 rounded-full">Grupo:
                                            <?php echo e($selectedCredenciado->grupo->nome ?? 'N/A'); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 pt-1">
                                        <strong>Bloco:</strong> <?php echo e($selectedCredenciado->bloco->nome ?? 'N/A'); ?> |
                                        <strong>Região:</strong> <?php echo e($selectedCredenciado->regiao->nome ?? 'N/A'); ?> |
                                        <strong>Igreja:</strong> <?php echo e($selectedCredenciado->igreja->nome ?? 'N/A'); ?>

                                    </p>
                                </div>
                            </div>

                            
                            <div>
                                <h4
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b dark:border-gray-700 pb-2 mb-4">
                                    Informações de Contato e Profissionais</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800">
                                        <p class="text-gray-400 text-[10px] uppercase font-bold">Celular / WhatsApp</p>
                                        <p class="font-semibold dark:text-gray-200 mt-1">
                                            <?php echo e($selectedCredenciado->celular); ?></p>
                                    </div>
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800">
                                        <p class="text-gray-400 text-[10px] uppercase font-bold">E-mail</p>
                                        <p class="font-semibold dark:text-gray-200 mt-1 truncate">
                                            <?php echo e($selectedCredenciado->email ?? 'Não informado'); ?></p>
                                    </div>
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800">
                                        <p class="text-gray-400 text-[10px] uppercase font-bold">Profissão</p>
                                        <p class="font-semibold dark:text-gray-200 mt-1">
                                            <?php echo e($selectedCredenciado->profissao ?? 'Não informada'); ?></p>
                                    </div>
                                </div>
                            </div>

                            
                            <div>
                                <h4
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b dark:border-gray-700 pb-2 mb-4">
                                    Endereço</h4>
                                <div
                                    class="p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800 text-sm space-y-1">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <?php echo e($selectedCredenciado->endereco); ?>, <?php echo e($selectedCredenciado->bairro); ?></p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        <?php echo e(optional($selectedCredenciado->cidade)->nome); ?> -
                                        <?php echo e(optional(optional($selectedCredenciado->cidade)->estado)->uf); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCredenciado->cep): ?>
                                            • CEP: <?php echo e($selectedCredenciado->cep); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            
                            <div>
                                <h4
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b dark:border-gray-700 pb-2 mb-4">
                                    Documentos de Identidade</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800 text-center">
                                        <p class="text-xs font-bold text-gray-500 mb-2">FRENTE DO DOCUMENTO</p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCredenciado->identidade_frente): ?>
                                            <div
                                                class="w-full bg-white dark:bg-gray-900 p-2 rounded-lg border dark:border-gray-700 shadow-sm">
                                                <img src="<?php echo e(asset($selectedCredenciado->identidade_frente)); ?>"
                                                    @click="abrirFoto('<?php echo e(asset($selectedCredenciado->identidade_frente)); ?>', 'Documento - Frente')"
                                                    class="h-32 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                    title="Clique para ampliar">
                                            </div>
                                        <?php else: ?>
                                            <div
                                                class="h-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-xs border dark:border-gray-700">
                                                Não enviada</div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800 text-center">
                                        <p class="text-xs font-bold text-gray-500 mb-2">VERSO DO DOCUMENTO</p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCredenciado->identidade_verso): ?>
                                            <div
                                                class="w-full bg-white dark:bg-gray-900 p-2 rounded-lg border dark:border-gray-700 shadow-sm">
                                                <img src="<?php echo e(asset($selectedCredenciado->identidade_verso)); ?>"
                                                    @click="abrirFoto('<?php echo e(asset($selectedCredenciado->identidade_verso)); ?>', 'Documento - Verso')"
                                                    class="h-32 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                    title="Clique para ampliar">
                                            </div>
                                        <?php else: ?>
                                            <div
                                                class="h-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-xs border dark:border-gray-700">
                                                Não enviado</div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            
                            <div>
                                <h4
                                    class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest border-b border-blue-100 dark:border-blue-900 pb-2 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    Credenciais Prisionais Vinculadas
                                    (<?php echo e($selectedCredenciado->credencialPresidios->count()); ?>)
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selectedCredenciado->credencialPresidios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div
                                            class="p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border dark:border-gray-800 space-y-3">
                                            <div
                                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1">
                                                <p
                                                    class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <?php echo e($cp->presidio->nome ?? 'Presídio Desconhecido'); ?>

                                                </p>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cp->data_vencimento): ?>
                                                    <span
                                                        class="text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 px-2 py-0.5 rounded font-semibold">
                                                        Vencimento: <?php echo e($cp->data_vencimento->format('d/m/Y')); ?>

                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cp->unidade_nao_faz): ?>
                                                <div
                                                    class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-center">
                                                    <span class="text-xs font-bold text-red-600 dark:text-red-400">A
                                                        unidade não faz</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="grid grid-cols-2 gap-2 pt-1">
                                                    <div>
                                                        <p class="text-[10px] font-bold text-gray-500 mb-1">FRENTE</p>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cp->foto_frente): ?>
                                                            <div
                                                                class="bg-white dark:bg-gray-900 p-1 rounded-lg border dark:border-gray-700 shadow-sm">
                                                                <img src="<?php echo e(asset($cp->foto_frente)); ?>"
                                                                    @click="abrirFoto('<?php echo e(asset($cp->foto_frente)); ?>', 'Credencial - <?php echo e($cp->presidio->nome ?? ''); ?> (Frente)')"
                                                                    class="h-20 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                                    title="Clique para ampliar">
                                                            </div>
                                                        <?php else: ?>
                                                            <div
                                                                class="h-20 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">
                                                                A unidade não faz</div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-bold text-gray-500 mb-1">VERSO</p>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cp->foto_verso): ?>
                                                            <div
                                                                class="bg-white dark:bg-gray-900 p-1 rounded-lg border dark:border-gray-700 shadow-sm">
                                                                <img src="<?php echo e(asset($cp->foto_verso)); ?>"
                                                                    @click="abrirFoto('<?php echo e(asset($cp->foto_verso)); ?>', 'Credencial - <?php echo e($cp->presidio->nome ?? ''); ?> (Verso)')"
                                                                    class="h-20 w-full object-contain bg-white cursor-pointer hover:opacity-85 transition"
                                                                    title="Clique para ampliar">
                                                            </div>
                                                        <?php else: ?>
                                                            <div
                                                                class="h-20 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">
                                                                A unidade não faz</div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="text-sm text-gray-500 italic col-span-full">Nenhuma credencial de
                                            presídio registrada.</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCredenciado->testemunho): ?>
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b dark:border-gray-700 pb-2 mb-3">
                                        Testemunho / Observações</h4>
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl italic text-sm text-gray-700 dark:text-gray-300 border-l-4 border-blue-500">
                                        "<?php echo e($selectedCredenciado->testemunho); ?>"
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>

                        <div
                            class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-end rounded-b-xl shrink-0">
                            <button wire:click="closeViewModal"
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2.5 sm:py-2 px-6 rounded-lg transition w-full sm:w-auto">
                                Fechar Ficha
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div x-show="fotoModalOpen" x-transition.opacity.duration.200ms x-cloak
                @keydown.escape.window="fotoModalOpen = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95 backdrop-blur-md p-3 sm:p-6"
                style="display: none;">

                <div class="relative w-full max-w-5xl flex flex-col items-center" style="max-height: 92vh;">
                    <!-- Botão Fechar: dentro do fluxo do modal, sempre visível na tela -->
                    <div class="w-full flex items-start justify-between gap-4 mb-3">
                        <p x-text="fotoTituloAtual"
                            class="text-white text-xs sm:text-sm font-bold tracking-wider uppercase pt-2"></p>
                        <button @click="fotoModalOpen = false" type="button" title="Fechar"
                            class="shrink-0 text-white bg-red-600 hover:bg-red-700 rounded-full p-3 shadow-2xl transition flex items-center justify-center focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div
                        class="relative flex-1 w-full min-h-0 flex items-center justify-center overflow-hidden bg-black/50 rounded-2xl p-2 border border-gray-800">
                        <img :src="fotoUrlAtual" class="max-w-full rounded-xl shadow-2xl object-contain bg-white"
                            style="max-height: 70vh;">
                    </div>

                    <button @click="fotoModalOpen = false"
                        class="mt-4 bg-gray-800 hover:bg-gray-700 text-white font-semibold py-2.5 px-8 rounded-xl transition border border-gray-600 text-sm shadow-md">
                        Fechar Visualização
                    </button>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirmDeleteId): ?>
                <div
                    class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-75 backdrop-blur-sm p-4 flex items-center justify-center">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-sm p-6 text-center border-t-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Excluir Credenciado?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Esta ação não pode ser desfeita.</p>
                        <div class="flex justify-center gap-3">
                            <button wire:click="$set('confirmDeleteId', null)"
                                class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold py-2 px-6 rounded-lg">Cancelar</button>
                            <button wire:click="delete"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg">Excluir</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            animation: slideUp 0.4s ease-out forwards;
            animation-delay: var(--delay, 0s);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(15px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #4b5563;
        }
    </style>
</div>
<?php /**PATH /home/moraws/dm/resources/views/livewire/universal/credenciados.blade.php ENDPATH**/ ?>