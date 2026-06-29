<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MW | Moraws</title>
    <meta name="description"
        content="Aplicação web desenvolvida por J.M.Moraes, utilizando Laravel, Livewire e Tailwind CSS. Solução eficiente para gestão.">
    <meta name="keywords" content="moraw, laravel, livewire, tailwind, aplicação web, desenvolvimento web, Moraes">
    <meta name="author" content="J.M.Moraes">

    <!-- Open Graph (Redes Sociais) -->
    <meta property="og:title" content="Moraw | Aplicação Web Moderna">
    <meta property="og:description" content="Desenvolvida com Laravel e Livewire para gestão privada.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://moraw.ct.ws">
    <meta property="og:image" content="https://moraw.ct.ws/uploads/moraw-1600x630-thumbnail.jpg">
    <meta property="og:site_name" content="Moraw">
    <link rel="icon"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 440 376'%3E%3Cpath d='M 56.53 6.41 h 152.93 v 366.88 L 70.37 178.11 h 62.28 l 33.07 45.89 V 69.06 H 74.71 l -9.85 13.81 L 87.53 115.2 l -62.43 0.5 L 2 84 z' style='fill:%2399f'/%3E%3Cpath d='M229.93 6.29h152.83l54.2 76.26-72.48 101.38-0.18-87.61 9.85-13.31-9.67-13.81-23.22-0.25-0.35 148.54-43.63 61.17-1.76-206.77H271.3l1.05 239.85-45.03 61.17z' style='fill:%2399f;fill-opacity:.811765'/%3E%3C/svg%3E"
        sizes="any" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @verbatim
        <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Moraw",
      "url": "https://domo.ct.ws",
      "description": "Desenvolvida com Laravel e Livewire.",
      "author": {
        "@type": "Person",
        "name": "J.M.Moraes"
      }
    }
</script>
    @endverbatim
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased">
    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                <!-- Seletor de Modo Escuro -->
                <button
                    @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark')"
                    class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 01 8.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Hero Section -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="flex-1 text-center lg:text-left">
                <h1
                    class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight mb-6 transition-all duration-500 ease-in-out transform starting:translate-y-4 starting:opacity-0 translate-y-0 opacity-100">
                    Bem-vindo <span class="text-blue-600 dark:text-blue-400"></span>
                </h1>
                <p
                    class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed transition-all duration-500 ease-in-out transform starting:translate-y-4 starting:opacity-0 translate-y-0 opacity-100 delay-100">
                    Uma solução moderna para gerenciamento de dados, seu companheiro para o dia a dia e para a vida.
                </p>
                <div
                    class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start transition-all duration-500 ease-in-out transform starting:translate-y-4 starting:opacity-0 translate-y-0 opacity-100 delay-200">
                    <a href="{{ route('terms.show') }}" target="_blank"
                        class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                        Ver Documentação
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                    <a href="{{ route('policy.show') }}" target="_blank"
                        class="inline-flex items-center px-6 py-3 text-base font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Politicas
                    </a>
                </div>
            </div>

            <!-- JM Logo -->
            <div class="w-full max-w-xs mt-6">
                <svg viewBox="0 0 440 376" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="w-full h-auto transition-all duration-750 ease-in-out transform starting:scale-95 starting:opacity-0 scale-100 opacity-100 delay-300"
                    preserveAspectRatio="xMidYMid meet">
                    <g>
                        <path
                            d="M 56.53 6.41 h 152.93 v 366.88 L 70.37 178.11 h 62.28 l 33.07 45.89 V 69.06 H 74.71 l -9.85 13.81 L 87.53 115.2 l -62.43 0.5 L 2 84 z"
                            fill="#2563EB" class="dark:fill-blue-400" />
                        <path
                            d="M229.93 6.29h152.83l54.2 76.26-72.48 101.38-0.18-87.61 9.85-13.31-9.67-13.81-23.22-0.25-0.35 148.54-43.63 61.17-1.76-206.77H271.3l1.05 239.85-45.03 61.17z"
                            fill="#2563EB" class="dark:fill-blue-400" />
                    </g>
                </svg>
            </div>
        </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Desenvolvido com
            <svg class="inline-block size-4" xmlns="http://www.w3.org/2000/svg" fill="#38BDF8" viewBox="0 0 24 24">
                <path
                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
            </svg> por J.M.Moraes
            © {{ date('Y') }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
