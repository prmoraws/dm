<nav x-data="{ open: false, darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')"
    class="bg-white dark:bg-gray-900/80 dark:backdrop-blur-sm border-b border-gray-200 dark:border-gray-700/50 fixed top-0 w-full z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                
                <div class="shrink-0 flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-2 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="30" viewBox="0 0 440 376"
                            class="transition-transform duration-300 group-hover:scale-110">
                            <path
                                d="M56.53 6.41h152.93v366.88L70.37 178.11h62.28l33.07 45.89V69.06H74.71l-9.85 13.81L87.53 115.2l-62.43 0.5L2 84z"
                                fill="#2563EB" class="dark:fill-blue-400" />
                            <path
                                d="M229.93 6.29h152.83l54.2 76.26-72.48 101.38-0.18-87.61 9.85-13.31-9.67-13.81-23.22-0.25-0.35 148.54-43.63 61.17-1.76-206.77H271.3l1.05 239.85-45.03 61.17z"
                                fill="#2563EB" class="dark:fill-blue-400" />
                        </svg>
                        <span
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100 transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400"></span>
                        
                        <span class="ml-4 text-red-500 font-bold">Time Atual:
                            <?php echo e(Auth::user()->currentTeam->name); ?></span> 
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center lg:ml-6">
                <div class="flex items-center space-x-3 h-full">

                    <?php
                        $menus = [
                            'Unp' => [
                                'team' => 'Unp', // ALTERAÇÃO: Adicionado time
                                'active' => request()->is('unp/*') && !request()->is('unp/oficios/*'),
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222-4 2.222V20" /></svg>',
                                'links' => [
                                    ['route' => 'dashboard.unp', 'label' => 'Dashboard'],
                                    ['is_divider' => true],
                                    ['route' => 'batismo.dashboard', 'label' => 'Batismos'],
                                    ['route' => 'cargos', 'label' => 'Cargos'],
                                    ['route' => 'cursos', 'label' => 'Cursos'],
                                    ['route' => 'documentos', 'label' => 'Documentos'],
                                    ['route' => 'formaturas', 'label' => 'Formaturas'],
                                    ['route' => 'grupos', 'label' => 'Grupos'],
                                    ['route' => 'instrutores', 'label' => 'Instrutores'],
                                    ['route' => 'presidios', 'label' => 'Presídios'],
                                ],
                            ],
                            'Ofícios' => [
                                'team' => 'Unp', // ALTERAÇÃO: Adicionado time (rotas de ofícios pertencem ao grupo UNP)
                                'active' => request()->is('unp/oficios/*'),
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>',
                                'links' => [
                                    ['route' => 'oficios.dados-cursos', 'label' => 'Dados do Curso'],
                                    ['route' => 'oficios.lista-certificados', 'label' => 'Listas/Certificados'],
                                    ['route' => 'oficio-formaturas', 'label' => 'Ofício-Formatura'],
                                    ['route' => 'oficios.credenciais', 'label' => 'Ofícios de Credencial'],
                                    ['route' => 'oficios.eventos', 'label' => 'Ofícios de Evento'],
                                    ['route' => 'oficios.trabalho', 'label' => 'Ofícios de Trabalho'],
                                    ['route' => 'oficios.cop', 'label' => 'Ofícios COP'],
                                    ['route' => 'oficios.cursos', 'label' => 'Ofícios de Curso'],
                                    ['route' => 'oficios.geral', 'label' => 'Ofícios Gerais'],
                                    ['route' => 'oficios.anexos', 'label' => 'Anexos de Ofício'],
                                    ['route' => 'oficios.reeducandos', 'label' => 'Reeducandos'],
                                    ['route' => 'oficios.convidados', 'label' => 'Convidados/Anexos'],
                                    ['route' => 'oficios.informacao-cursos', 'label' => 'Informações de Cursos'],
                                ],
                            ],
                            'Universal' => [
                                'team' => 'Universal', // ALTERAÇÃO: Adicionado time
                                'active' => request()->is('universal/*'),
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.707 4.586L4.586 7.707m11.707-3.121l-3.121 3.121M12 21a9 9 0 110-18 9 9 0 010 18z" /></svg>',
                                'links' => [
                                    ['route' => 'dashboard.uni', 'label' => 'Dashboard'],
                                    ['is_divider' => true],
                                    ['route' => 'banners', 'label' => 'Banners'],
                                    ['route' => 'blocos', 'label' => 'Blocos'],
                                    ['route' => 'categorias', 'label' => 'Categorias'],
                                    ['route' => 'igrejas', 'label' => 'Igrejas'],
                                    ['route' => 'pastores', 'label' => 'Pastores'],
                                    ['route' => 'pastor-unp', 'label' => 'Pastor UNP'],
                                    ['route' => 'carros-unp', 'label' => 'Carros UNP'],
                                    ['route' => 'universal.pessoas', 'label' => 'Pessoas'],
                                    ['route' => 'universal.credenciados', 'label' => 'Credenciados'],
                                    ['route' => 'regiaos', 'label' => 'Regiões'],
                                    ['route' => 'regiaos', 'label' => 'Regiões'],
                                ],
                            ],
                            'Eventos' => [
                                'team' => 'Eventos', // ALTERAÇÃO: Adicionado time
                                'active' => request()->is('evento/*'),
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
                                'links' => [
                                    ['route' => 'dashboard.ev', 'label' => 'Dashboard'],
                                    ['is_divider' => true],
                                    ['route' => 'cestas', 'label' => 'Cestas'],
                                    ['route' => 'entregas', 'label' => 'Entregas'],
                                    ['route' => 'instituicoes', 'label' => 'Instituições'],
                                    ['route' => 'terreiros', 'label' => 'Terreiros'],
                                ],
                            ],
                            'Política' => [
                                'team' => 'Politica', // ALTERAÇÃO: Adicionado time
                                'active' => request()->is('politica/*'),
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 16.382V5.618a1 1 0 00-1.447-.894L15 7m-6 13v-6.5m6 10V7" /></svg>',
                                'links' => [
                                    ['route' => 'politica.dashboard', 'label' => 'Dashboard Cidades'],
                                    ['is_divider' => true],
                                    ['route' => 'politica.mapa', 'label' => 'Mapa Interativo'],
                                    ['route' => 'politica.candidatos', 'label' => 'Gerenciar Candidatos'],
                                    ['is_divider' => true],
                                ],
                            ],
                            'Secretaria' => [
                                'team' => 'Secretaria', // Este já estava correto
                                'active' => request()->is('secretaria/*') || request()->routeIs('secretaria.pessoas'),
                                'icon' =>
                                    '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" /></svg>',
                                'links' => [
                                    ['route' => 'secretaria.gestao-captacoes', 'label' => 'Gestão de Captações'],
                                    ['route' => 'secretaria.pessoas', 'label' => 'Pessoas'],
                                    ['route' => 'secretaria.gestao-credenciados', 'label' => 'Gestão Credenciados'],
                                ],
                            ],
                            'Adm' => [
                                'team' => 'Adm', // ALTERAÇÃO: Adicionado time para consistência
                                'active' => request()->is('teams/*') || request()->is('adm/*'), // Melhorando a verificação de rota ativa
                                'icon' =>
                                    '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
                                'links' => [
                                    ['route' => 'adm.dashboard', 'label' => 'Dashboard'],
                                    ['route' => 'adm.captacoes', 'label' => 'Gerenciar Captações'],
                                    ['is_divider' => true],
                                    [
                                        'route' => 'teams.show',
                                        'label' => 'Jetstream G',
                                        'params' => Auth::user()->currentTeam->id,
                                    ],
                                    ['route' => 'teams.create', 'label' => 'Jetstream C'],
                                    ['route' => 'adm.users', 'label' => 'Usuarios'],
                                    ['route' => 'adm.teams', 'label' => 'Times'],
                                ],
                            ],
                        ];
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(
                            !isset($menu['team']) ||
                                Auth::user()->currentTeam->name === $menu['team'] ||
                                Auth::user()->currentTeam->name === 'Adm'): ?>
                            <div class="relative group h-full flex items-center" x-data="{ open: false }"
                                @mouseenter="open = true" @mouseleave="open = false">
                                <button
                                    class="px-3 py-2 rounded-md text-sm font-medium flex items-center gap-2 transition-colors <?php echo e($menu['active'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400'); ?>">
                                    <?php echo $menu['icon']; ?>

                                    <span><?php echo e($name); ?></span>
                                    <svg class="ml-1 h-4 w-4 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <span
                                    class="absolute bottom-0 left-0 h-0.5 bg-blue-600 dark:bg-blue-400 transition-all duration-300 <?php echo e($menu['active'] ? 'w-full' : 'w-0'); ?> group-hover:w-full"></span>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-56 rounded-md shadow-lg z-50 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5"
                                    style="display: none;">
                                    <div class="py-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menu['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($link['is_divider'] ?? false): ?>
                                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                            <?php else: ?>
                                                <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route($link['route'], $link['params'] ?? [])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route($link['route'], $link['params'] ?? [])).'']); ?><?php echo e($link['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex items-center ml-3">
                    <div x-data="{ darkMode: document.documentElement.classList.contains('dark') }" class="flex items-center">
                        <button
                            @click="
            darkMode = !darkMode;
            document.documentElement.classList.toggle('dark', darkMode);
            localStorage.setItem('theme', darkMode ? 'dark' : 'light');
        "
                            class="text-gray-600 dark:text-gray-300 hover:text-primary transition"
                            title="Alternar modo escuro">
                            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m8.66-4.66l-.7.7M4.34 6.34l-.7.7M21 12h-1M4 12H3m16.66 4.66l-.7-.7M6.34 17.66l-.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z" />
                            </svg>
                            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.293 14.293A8 8 0 0110.707 3.707a8.001 8.001 0 106.586 10.586z" />
                            </svg>
                        </button>
                    </div>

                    <div class="ms-3 relative">
                        <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                             <?php $__env->slot('trigger', null, []); ?> 
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Laravel\Jetstream\Jetstream::managesProfilePhotos()): ?>
                                    <button
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-blue-500 transition">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                            src="<?php echo e(Auth::user()->profile_photo_url); ?>"
                                            alt="<?php echo e(Auth::user()->name); ?>" />
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                             <?php $__env->endSlot(); ?>
                             <?php $__env->slot('content', null, []); ?> 
                                <div class="block px-4 py-2 text-xs text-gray-400"><?php echo e(__('Manage Account')); ?></div>
                                <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('profile.show')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('profile.show')).'']); ?><?php echo e(__('Profile')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                <div class="border-t border-gray-200 dark:border-gray-700"></div>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" x-data>
                                    <?php echo csrf_field(); ?>
                                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('logout')).'','@click.prevent' => '$root.submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('logout')).'','@click.prevent' => '$root.submit();']); ?><?php echo e(__('Log Out')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                </form>
                             <?php $__env->endSlot(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    
    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden lg:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="pt-2 pb-3 space-y-1">
            <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => ''.e(route('dashboard')).'','active' => request()->routeIs('dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('dashboard')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('dashboard'))]); ?><?php echo e(__('Painel de Controle')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(
                    !isset($menu['team']) ||
                        Auth::user()->currentTeam->name === $menu['team'] ||
                        Auth::user()->currentTeam->name === 'Adm'): ?>
                    <div x-data="{ subMenuOpen: <?php echo e($menu['active'] ? 'true' : 'false'); ?> }" class="py-1 border-t border-gray-200 dark:border-gray-700">
                        <button @click="subMenuOpen = !subMenuOpen"
                            class="w-full flex justify-between items-center px-4 py-2 text-base font-medium rounded-md transition-colors duration-150 <?php echo e($menu['active'] ? 'text-blue-600 dark:text-blue-400 bg-gray-100 dark:bg-gray-800' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'); ?>">
                            <span class="flex items-center gap-2"><?php echo $menu['icon']; ?> <?php echo e($name); ?></span>
                            <svg class="h-5 w-5 transform transition-transform duration-300"
                                :class="{ 'rotate-180': subMenuOpen }" fill="none" viewBox="0 0 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="subMenuOpen" class="pl-4 mt-1 space-y-1 overflow-hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menu['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!($link['is_divider'] ?? false)): ?>
                                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => ''.e(route($link['route'], $link['params'] ?? [])).'','active' => request()->routeIs($link['route'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route($link['route'], $link['params'] ?? [])).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs($link['route']))]); ?><?php echo e($link['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Laravel\Jetstream\Jetstream::managesProfilePhotos()): ?>
                    <div class="shrink-0 me-3"><img class="h-10 w-10 rounded-full object-cover"
                            src="<?php echo e(Auth::user()->profile_photo_url); ?>" alt="<?php echo e(Auth::user()->name); ?>" /></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200"><?php echo e(Auth::user()->name); ?></div>
                    <div class="font-medium text-sm text-gray-500"><?php echo e(Auth::user()->email); ?></div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => ''.e(route('profile.show')).'','active' => request()->routeIs('profile.show')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('profile.show')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('profile.show'))]); ?><?php echo e(__('Profile')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                <form method="POST" action="<?php echo e(route('logout')); ?>" x-data>
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => ''.e(route('logout')).'','xOn:click.prevent' => '$root.submit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('logout')).'','x-on:click.prevent' => '$root.submit()']); ?><?php echo e(__('Log Out')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                </form>
            </div>
            <div x-data="{ darkMode: document.documentElement.classList.contains('dark') }" class="flex items-center px-4 py-3">
                <span class="text-sm text-gray-600 dark:text-gray-400 mr-3">Modo:</span>
                <button
                    @click="
                        darkMode = !darkMode;
                        document.documentElement.classList.toggle('dark', darkMode);
                        localStorage.setItem('darkMode', darkMode);
                    "
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-500 transition"
                    title="Alternar modo escuro">
                    <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m8.66-4.66l-.7.7M4.34 6.34l-.7.7M21 12h-1M4 12H3m16.66 4.66l-.7-.7M6.34 17.66l-.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z" />
                    </svg>
                    <svg x-show="darkMode" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.293 14.293A8 8 0 0110.707 3.707a8.001 8.001 0 106.586 10.586z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH /home/moraws/dm/resources/views/navigation-menu.blade.php ENDPATH**/ ?>