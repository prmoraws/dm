<?php $__env->startSection('title', 'Pessoas'); ?>


<?php use \Carbon\Carbon; ?>

<div>
     <?php $__env->slot('header', null, []); ?> 
        
        <div class="flex items-center space-x-3 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                <?php echo e(__('Pessoas')); ?>

            </h2>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-4"
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600 dark:text-teal-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-teal-800 dark:text-teal-200"><?php echo e(session('message')); ?></p>
                        </div>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center mb-6 gap-4">
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-2/3">
                        <div class="w-full sm:w-1/2">
                            <input wire:model.live.debounce.500ms="search" type="text"
                                placeholder="Buscar por Nome ou Celular..."
                                class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                        </div>
                        <div class="w-full sm:w-1/2">
                            <select wire:model.live="filtro_bloco_id"
                                class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                                <option value="">Todos os blocos</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $blocos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bloco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bloco->id); ?>"><?php echo e($bloco->nome); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2 justify-center w-full md:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Nova Pessoa
                    </button>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filtro_bloco_id): ?>
                    <div
                        class="mb-4 -mt-2 text-sm text-gray-600 dark:text-gray-400 flex flex-col sm:flex-row sm:items-center gap-2">
                        <span
                            class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-xs font-medium w-fit">
                            <?php echo e($results->total()); ?> resultado<?php echo e($results->total() !== 1 ? 's' : ''); ?>

                        </span>
                        <button wire:click="$set('search', ''); $set('filtro_bloco_id', '')"
                            class="text-blue-600 dark:text-blue-400 hover:underline text-xs sm:text-sm w-fit">
                            Limpar filtros
                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Bloco</th>
                                <th class="py-3 px-6 text-left">Cargo</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pessoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                
                                <tr wire:key="pessoa-<?php echo e($pessoa->id); ?>"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 animate-slide-up"
                                    style="--delay: <?php echo e((($index % 10) + 1) * 0.05); ?>s;">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <?php echo e($pessoa->nome); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <?php echo e($pessoa->bloco->nome ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <?php echo e($pessoa->cargo->nome ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <div class="flex items-center justify-center space-x-3">

                                            
                                            <?php
                                                $printRoute =
                                                    Auth::user()->currentTeam->name === 'Secretaria'
                                                        ? 'secretaria.pessoas.print.ficha'
                                                        : 'universal.pessoas.print.ficha';
                                            ?>
                                            <a href="<?php echo e(route($printRoute, $pessoa->id)); ?>" target="_blank"
                                                class="w-5 transform hover:text-gray-500 hover:scale-110"
                                                title="Imprimir Ficha">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </a>
                                            

                                            <button wire:click="view(<?php echo e($pessoa->id); ?>)"
                                                class="w-5 transform hover:text-green-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Visualizar"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit(<?php echo e($pessoa->id); ?>)"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg></button>
                                            <button wire:click="confirmDelete(<?php echo e($pessoa->id); ?>)"
                                                class="w-5 transform hover:text-red-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Excluir"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                                    </path>
                                                </svg></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Nenhuma pessoa encontrada para o termo "<?php echo e($search); ?>".
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->hasPages()): ?>
                    <div class="pt-4">
                        <?php echo e($results->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="md:hidden space-y-4 mt-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pessoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    
                    <div wire:key="pessoa-card-<?php echo e($pessoa->id); ?>"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                        style="--delay: <?php echo e((($index % 10) + 1) * 0.05); ?>s;">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100"><?php echo e($pessoa->nome); ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Bloco:
                                    <?php echo e($pessoa->bloco->nome ?? 'N/A'); ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Cargo:
                                    <?php echo e($pessoa->cargo->nome ?? 'N/A'); ?></p>
                            </div>
                            <div class="flex flex-col space-y-4 ml-4">

                                
                                <?php
                                    $printRoute =
                                        Auth::user()->currentTeam->name === 'Secretaria'
                                            ? 'secretaria.pessoas.print.ficha'
                                            : 'universal.pessoas.print.ficha';
                                ?>
                                <a href="<?php echo e(route($printRoute, $pessoa->id)); ?>" target="_blank"
                                    class="w-5 transform hover:text-gray-500 hover:scale-110" title="Imprimir Ficha">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </a>
                                

                                <button wire:click="view(<?php echo e($pessoa->id); ?>)"
                                    class="w-5 transform text-gray-500 hover:text-green-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg></button>
                                <button wire:click="edit(<?php echo e($pessoa->id); ?>)"
                                    class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg></button>
                                <button wire:click="confirmDelete(<?php echo e($pessoa->id); ?>)"
                                    class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                        </path>
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500 dark:text-gray-400">
                        Nenhuma pessoa encontrada.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->hasPages()): ?>
                    <div class="mt-4">
                        <?php echo e($results->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
                
                <div wire:key="create-edit-modal" x-data="{ open: <?php echo json_encode($isOpen, 15, 512) ?> }" x-show="open"
                    x-on:keydown.escape.window="$wire.closeModal()" x-transition @click.self="$wire.closeModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl p-6 md:p-8 mx-auto my-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            <?php echo e($pessoa_id ? 'Editar Pessoa' : 'Criar Pessoa'); ?></h3>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
                            <div x-data="{ show: true }" x-show="show" x-transition
                                class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 dark:bg-red-900/50 dark:text-red-300"
                                role="alert">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm font-medium"><?php echo e($errorMessage); ?></p>
                                    <button @click="show = false" type="button" class="text-red-500">
                                        <span class="sr-only">Dispensar</span>
                                        &times;
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <form wire:submit.prevent="store" enctype="multipart/form-data" class="space-y-6">
                            
                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Liderança e Grupos</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label for="bloco_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label><select
                                            id="bloco_id" wire:model.live="bloco_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allBlocos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bloco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bloco->id); ?>"><?php echo e($bloco->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bloco_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="regiao_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label><select
                                            id="regiao_id" wire:model.live="regiao_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            <?php if(empty($regiaos)): ?> disabled <?php endif; ?>>
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regiaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regiao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($regiao->id); ?>"><?php echo e($regiao->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['regiao_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="igreja_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Igreja</label><select
                                            id="igreja_id" wire:model="igreja_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            <?php if(empty($igrejas)): ?> disabled <?php endif; ?>>
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($igreja->id); ?>"><?php echo e($igreja->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['igreja_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="categoria_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label><select
                                            id="categoria_id" wire:model="categoria_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCategorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categoria_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="cargo_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label><select
                                            id="cargo_id" wire:model="cargo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCargos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cargo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cargo->id); ?>"><?php echo e($cargo->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cargo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="grupo_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grupo</label><select
                                            id="grupo_id" wire:model="grupo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($grupo->id); ?>"><?php echo e($grupo->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['grupo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Informações Pessoais</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2"><label for="nome"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome
                                            Completo</label><input type="text" id="nome" wire:model="nome"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="celular"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</label><input
                                            type="text" id="celular" wire:model="celular"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['celular'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="telefone"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label><input
                                            type="text" id="telefone" wire:model="telefone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="email"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label><input
                                            type="email" id="email" wire:model="email"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="profissao"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profissão</label><input
                                            type="text" id="profissao" wire:model="profissao"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div class="md:col-span-3"><label for="aptidoes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aptidões</label><input
                                            type="text" id="aptidoes" wire:model="aptidoes"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Endereço</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="lg:col-span-2"><label for="endereco"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço</label><input
                                            type="text" id="endereco" wire:model="endereco"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['endereco'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="bairro"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label><input
                                            type="text" id="bairro" wire:model="bairro"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bairro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="cep"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label><input
                                            type="text" id="cep" wire:model="cep"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="estado_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label><select
                                            id="estado_id" wire:model.live="estado_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allEstados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($estado->id); ?>"><?php echo e($estado->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['estado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div><label for="cidade_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label><select
                                            id="cidade_id" wire:model="cidade_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            <?php if(empty($cidades)): ?> disabled <?php endif; ?>>
                                            <option value="">Selecione</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cidade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cidade->id); ?>"><?php echo e($cidade->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cidade_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Outras Informações</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Trabalho</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="interno"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Interno</span></label><label
                                            class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="externo"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Externo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Batismo</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                                value="aguas"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Nas
                                                Águas</span></label><label class="flex items-center"><input
                                                type="checkbox" wire:model="batismo" value="espirito"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Com Espírito
                                                Santo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Preso</p><label
                                            class="flex items-center"><input type="checkbox" wire:model="preso"
                                                value="preso"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Já foi
                                                Preso(a)</span></label><label class="flex items-center"><input
                                                type="checkbox" wire:model="preso" value="familiar"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Familiar
                                                Preso</span></label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div><label for="conversao"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                            Conversão</label><input type="date" id="conversao"
                                            wire:model="conversao"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="obra"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                            Entrada na Obra</label><input type="date" id="obra"
                                            wire:model="obra"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                                <div class="mt-4"><label for="testemunho"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Testemunho</label>
                                    <textarea id="testemunho" wire:model="testemunho" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"></textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="foto"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>
                                    <input id="foto" type="file" wire:model="foto"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoAtual && !$foto): ?>
                                        <div class="mt-2"><img src="<?php echo e(asset($fotoAtual)); ?>" alt="Foto atual"
                                                class="h-20 w-20 object-cover rounded-full"></div>
                                    <?php elseif($foto): ?>
                                        <div class="mt-2"><img src="<?php echo e($foto->temporaryUrl()); ?>" alt="Preview"
                                                class="h-20 w-20 object-cover rounded-full"></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div wire:loading wire:target="foto" class="text-sm text-gray-500 mt-2">
                                        Carregando...</div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </section>
                            <div class="flex justify-end gap-3 pt-4">
                                
                                <button type="button" wire:click="closeModal"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 focus:outline-none focus:shadow-outline">
                                    <?php echo e($pessoa_id ? 'Atualizar' : 'Criar'); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewOpen && $selectedPessoa): ?>
                
                <div wire:key="view-modal" x-data="{ open: <?php echo json_encode($isViewOpen, 15, 512) ?> }" x-show="open"
                    x-on:keydown.escape.window="$wire.closeViewModal()" x-transition
                    @click.self="$wire.closeViewModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl p-6 md:p-8 mx-auto my-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($selectedPessoa->nome); ?>

                            </h3>
                            <button wire:click="closeViewModal"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100 text-3xl leading-none">&times;</button>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 flex-shrink-0">
                                <img src="<?php echo e($selectedPessoa->foto ? asset($selectedPessoa->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedPessoa->nome) . '&color=7F9CF5&background=EBF4FF'); ?>"
                                    alt="Foto de <?php echo e($selectedPessoa->nome); ?>"
                                    class="h-40 w-40 rounded-full object-cover mx-auto mb-4 border-4 border-white dark:border-gray-700 shadow-lg">
                            </div>
                            <div class="md:w-2/3 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Detalhes Pessoais</h4>
                                    <div class="border-t my-1"></div>
                                    <p><strong>Email:</strong> <?php echo e($selectedPessoa->email ?? 'N/A'); ?></p>
                                    <p><strong>Celular:</strong> <?php echo e($selectedPessoa->celular); ?></p>
                                    <p><strong>Telefone:</strong> <?php echo e($selectedPessoa->telefone ?? 'N/A'); ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Localização</h4>
                                    <div class="border-t my-1"></div>
                                    <p><?php echo e($selectedPessoa->endereco); ?>, <?php echo e($selectedPessoa->bairro); ?></p>
                                    <p><?php echo e(optional($selectedPessoa->cidade)->nome); ?> -
                                        <?php echo e(optional(optional($selectedPessoa->cidade)->estado)->uf); ?>, CEP:
                                        <?php echo e($selectedPessoa->cep ?? 'N/A'); ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Dados Espirituais</h4>
                                    <div class="border-t my-1"></div>
                                    <p><strong>Conversão:</strong>
                                        <?php echo e($selectedPessoa->conversao ? Carbon::parse($selectedPessoa->conversao)->format('d/m/Y') : 'N/A'); ?>

                                    </p>
                                    
                                    <?php
                                        // Garante que os atributos sejam arrays, mesmo que venham mal formatados do DB
                                        $batismoArray = is_array($selectedPessoa->batismo)
                                            ? $selectedPessoa->batismo
                                            : [];
                                        $presoArray = is_array($selectedPessoa->preso) ? $selectedPessoa->preso : [];
                                    ?>
                                    <p><strong>Batismo:</strong> Águas:
                                        <?php echo e(in_array('aguas', $batismoArray) ? 'Sim' : 'Não'); ?> |
                                        Espírito Santo:
                                        <?php echo e(in_array('espirito', $batismoArray) ? 'Sim' : 'Não'); ?></p>
                                    <p><strong>Situação:</strong> Já foi preso(a):
                                        <?php echo e(in_array('preso', $presoArray) ? 'Sim' : 'Não'); ?> |
                                        Familiar Preso:
                                        <?php echo e(in_array('familiar', $presoArray) ? 'Sim' : 'Não'); ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Testemunho</h4>
                                    <div class="border-t my-1"></div>
                                    <p class="italic text-gray-600 dark:text-gray-400">
                                        <?php echo e($selectedPessoa->testemunho ?: 'Nenhum testemunho registrado.'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirmDeleteId): ?>
                
                <div wire:key="delete-modal" x-data="{ open: <?php echo json_encode($confirmDeleteId, 15, 512) ?> }" x-show="open"
                    x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                    @click.self="$wire.set('confirmDeleteId', null)"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                        <div class="flex items-center gap-3 mb-4"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este registro?
                            Esta ação não pode ser desfeita.</p>
                        <div class="flex justify-end gap-3">
                            
                            <button wire:click="$set('confirmDeleteId', null)"
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                Cancelar
                            </button>
                            <button wire:click="delete"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg flex items-center gap-2 transition-all duration-150 focus:outline-none focus:shadow-outline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                </svg>
                                Apagar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            /* Inicia invisível */
            animation: slideUp 0.5s ease-out forwards;
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
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</div>
<?php /**PATH /home/moraws/dm/resources/views/livewire/universal/pessoas.blade.php ENDPATH**/ ?>