<!DOCTYPE html>
<html lang="es" class="h-full" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-bind:class="{ 'dark': darkMode }">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('installer.title', 'Instalador') }} - @yield('title')</title>
        <link rel="icon" href="{{ config('../../installer.favicon') }}"
            type="image/x-icon">

        <!-- Tailwind CSS (CDN para simplificar) -->
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '{{ config('installer.theme.primary_color_50', '#f0f9ff') }}',
                                100: '{{ config('installer.theme.primary_color_100', '#e0f2fe') }}',
                                200: '{{ config('installer.theme.primary_color_200', '#bae6fd') }}',
                                300: '{{ config('installer.theme.primary_color_300', '#7dd3fc') }}',
                                400: '{{ config('installer.theme.primary_color_400', '#38bdf8') }}',
                                500: '{{ config('installer.theme.primary_color', '#0ea5e9') }}',
                                600: '{{ config('installer.theme.primary_color_600', '#0284c7') }}',
                                700: '{{ config('installer.theme.primary_color_700', '#0369a1') }}',
                                800: '{{ config('installer.theme.primary_color_800', '#075985') }}',
                                900: '{{ config('installer.theme.primary_color_900', '#0c4a6e') }}',
                                950: '{{ config('installer.theme.primary_color_950', '#082f49') }}',
                            },
                            secondary: {
                                500: '{{ config('installer.theme.secondary_color', '#6366F1') }}',
                            },
                            background: {
                                light: '{{ config('installer.theme.background_color', '#F3F4F6') }}',
                                dark: '{{ config('installer.theme.background_color_dark', '#111827') }}',
                            },
                            text: {
                                light: '{{ config('installer.theme.text_color', '#1F2937') }}',
                                dark: '{{ config('installer.theme.text_color_dark', '#F9FAFB') }}',
                            }
                        },
                        fontFamily: {
                            'sans': ['Segoe UI', 'system-ui', 'sans-serif'],
                        },
                        boxShadow: {
                            'installer': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                            'installer-dark': '0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.18)',
                        }
                    }
                }
            }
        </script>

        <!-- Link al CSS personalizado -->
        <link rel="stylesheet" href="{{ asset('installer/css/installer.css') }}">

        <!-- Estilos en línea -->
        <style>
            [x-cloak] {
                display: none !important;
            }

            /* Estilos para el scrollbar personalizado */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .dark ::-webkit-scrollbar-track {
                background: #374151;
            }

            ::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            .dark ::-webkit-scrollbar-thumb {
                background: #4B5563;
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: #6B7280;
            }

            /* Animaciones */
            .fade-in {
                animation: fadeIn 0.3s ease-in-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .slide-in {
                animation: slideIn 0.3s ease-in-out;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(-10px);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @yield('styles')
    </head>

    <body
        class="h-full font-sans bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark transition-colors duration-300">
        <div class="flex h-full">
            <!-- Sidebar / Pasos -->
            <div
                class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 bg-gradient-to-b from-primary-700 to-primary-900 dark:from-primary-900 dark:to-primary-950 transition-colors duration-300">
                <div class="flex-1 flex flex-col min-h-0">
                    <!-- Logo y título -->
                    <div
                        class="flex items-center h-16 flex-shrink-0 px-6 border-b border-primary-600/30 dark:border-primary-950/50">
                        <img class="h-8 w-auto"
                            src="{{ config('../../installer.logo') }}"
                            alt="Logo">
                        <span
                            class="ml-3 text-white font-semibold text-lg tracking-tight">{{ config('installer.title', 'Instalador') }}</span>
                    </div>

                    <!-- Pasos de instalación -->
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="px-4 mb-6">
                            <h2 class="text-xs uppercase tracking-wider text-primary-200 font-semibold mb-2 px-2">Pasos
                                de instalación</h2>
                        </div>
                        <nav class="flex-1 px-3 space-y-1">
                            @php
                                $steps = config('installer.steps', []);
                                $currentRoute = Route::currentRouteName();
                                $currentStepIndex = array_search($currentRoute, array_column($steps, 'route'));
                            @endphp

                            @foreach ($steps as $stepKey => $stepInfo)
                                @php
                                    $stepIndex = array_search($stepKey, array_keys($steps));
                                    $isCompleted = $stepIndex < $currentStepIndex;
                                    $isCurrent = $currentRoute === $stepInfo['route'];
                                    $isDisabled = $stepIndex > $currentStepIndex;
                                @endphp

                                <a href="{{ $isDisabled ? '#' : route($stepInfo['route']) }}"
                                    class="group flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ $isCurrent ? 'bg-primary-600/40 dark:bg-primary-800/40 text-white shadow-sm' : ($isCompleted ? 'text-primary-100 hover:bg-primary-600/30 dark:hover:bg-primary-800/30' : 'text-primary-300 opacity-60 cursor-not-allowed') }}">

                                    <div
                                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center mr-3 rounded-full
                                    {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-white text-primary-700' : 'bg-primary-600/30 text-primary-200') }}">
                                        @if ($isCompleted)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <span>{{ $stepIndex + 1 }}</span>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span>{{ $stepInfo['title'] }}</span>

                                            @if ($isCurrent)
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                    Actual
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if (!$isDisabled && !$isCurrent)
                                        <svg class="ml-1 w-5 h-5 text-primary-300 group-hover:text-white transition-colors duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Footer del sidebar -->
                    <div class="flex-shrink-0 px-4 py-4 border-t border-primary-600/30 dark:border-primary-950/50">
                        <div class="flex items-center justify-between">
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                                class="flex items-center justify-center p-2 rounded-md text-primary-200 hover:text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-white"
                                title="Cambiar tema">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </button>

                            <div class="text-xs text-primary-300">
                                <span>Versión 1.0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="lg:pl-72 flex flex-col flex-1">
                <!-- Header móvil -->
                <header class="lg:hidden bg-white dark:bg-gray-800 shadow-md dark:shadow-installer-dark z-10">
                    <div class="px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <img class="h-8 w-auto"
                                src="{{ config('../../installer.logo') }}"
                                alt="Logo">
                            <span
                                class="ml-3 text-gray-900 dark:text-white font-semibold text-lg">{{ config('installer.title', 'Instalador') }}</span>
                        </div>

                        <div class="flex items-center">
                            <!-- Botón modo oscuro -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                                class="flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 mr-2"
                                title="Cambiar tema">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </button>

                            <!-- Menú móvil -->
                            <div x-data="{ mobileMenu: false }">
                                <button @click="mobileMenu = !mobileMenu" type="button"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <span class="sr-only">Abrir menú</span>
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>

                                <!-- Panel de navegación móvil -->
                                <div x-show="mobileMenu" x-cloak class="fixed inset-0 flex z-40"
                                    x-transition:enter="transition-opacity ease-linear duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition-opacity ease-linear duration-300"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                                    <!-- Overlay -->
                                    <div x-show="mobileMenu" @click="mobileMenu = false"
                                        class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

                                    <!-- Panel lateral -->
                                    <div x-show="mobileMenu"
                                        class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-primary-700 to-primary-900 dark:from-primary-900 dark:to-primary-950"
                                        x-transition:enter="transition ease-in-out duration-300 transform"
                                        x-transition:enter-start="-translate-x-full"
                                        x-transition:enter-end="translate-x-0"
                                        x-transition:leave="transition ease-in-out duration-300 transform"
                                        x-transition:leave-start="translate-x-0"
                                        x-transition:leave-end="-translate-x-full">

                                        <!-- Botón cerrar -->
                                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                                            <button @click="mobileMenu = false" type="button"
                                                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                                <span class="sr-only">Cerrar menú</span>
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Contenido del panel -->
                                        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                                            <div class="flex-shrink-0 flex items-center px-4 mb-5">
                                                <img class="h-8 w-auto"
                                                    src="{{ config('../../installer.logo') }}"
                                                    alt="Logo">
                                                <span
                                                    class="ml-3 text-white font-semibold text-lg">{{ config('installer.title', 'Instalador') }}</span>
                                            </div>

                                            <div class="px-4 mb-4">
                                                <h2
                                                    class="text-xs uppercase tracking-wider text-primary-200 font-semibold mb-2 px-2">
                                                    Pasos de instalación</h2>
                                            </div>

                                            <nav class="px-3 space-y-1">
                                                @foreach ($steps as $stepKey => $stepInfo)
                                                    @php
                                                        $stepIndex = array_search($stepKey, array_keys($steps));
                                                        $isCompleted = $stepIndex < $currentStepIndex;
                                                        $isCurrent = $currentRoute === $stepInfo['route'];
                                                        $isDisabled = $stepIndex > $currentStepIndex;
                                                    @endphp

                                                    <a href="{{ $isDisabled ? '#' : route($stepInfo['route']) }}"
                                                        @click="mobileMenu = false"
                                                        class="group flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ $isCurrent ? 'bg-primary-600/40 dark:bg-primary-800/40 text-white shadow-sm' : ($isCompleted ? 'text-primary-100 hover:bg-primary-600/30 dark:hover:bg-primary-800/30' : 'text-primary-300 opacity-60 cursor-not-allowed') }}">

                                                        <div
                                                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center mr-3 rounded-full
                                                        {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-white text-primary-700' : 'bg-primary-600/30 text-primary-200') }}">
                                                            @if ($isCompleted)
                                                                <svg class="w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                            @else
                                                                <span>{{ $stepIndex + 1 }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="flex-1">
                                                            <div class="flex items-center">
                                                                <span>{{ $stepInfo['title'] }}</span>

                                                                @if ($isCurrent)
                                                                    <span
                                                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                                        Actual
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Contenido principal -->
                <main class="flex-1 pb-8">
                    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Encabezado -->
                        <div class="pb-5 border-b border-gray-200 dark:border-gray-700 mb-6">
                            <div class="flex items-center justify-between">
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">@yield('title')</h1>

                                <!-- Indicador de progreso -->
                                <div class="hidden sm:flex items-center space-x-2">
                                    @php
                                        $totalSteps = count($steps);
                                        $currentStep = $currentStepIndex !== false ? $currentStepIndex + 1 : 1;
                                        $progressPercentage = (($currentStep - 1) / ($totalSteps - 1)) * 100;
                                    @endphp

                                    <div class="w-36 bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                        <div class="bg-primary-600 dark:bg-primary-500 h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400">{{ $currentStep }}/{{ $totalSteps }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Alertas -->
                        @if (session('success'))
                            <div
                                class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800 shadow-sm fade-in">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-500 dark:text-green-400" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                            {{ session('success') }}</p>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button"
                                                onclick="this.parentElement.parentElement.parentElement.remove()"
                                                class="inline-flex rounded-md p-1.5 text-green-500 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                                <span class="sr-only">Descartar</span>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div
                                class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800 shadow-sm fade-in">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500 dark:text-red-400" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                            {{ session('error') }}</p>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button"
                                                onclick="this.parentElement.parentElement.parentElement.remove()"
                                                class="inline-flex rounded-md p-1.5 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                                <span class="sr-only">Descartar</span>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div
                                class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800 shadow-sm fade-in">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500 dark:text-red-400" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Hay errores en
                                            el formulario:</h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button"
                                                onclick="this.parentElement.parentElement.parentElement.remove()"
                                                class="inline-flex rounded-md p-1.5 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                                <span class="sr-only">Descartar</span>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Contenido principal -->
                        <div
                            class="bg-white dark:bg-gray-800 shadow-installer dark:shadow-installer-dark rounded-lg p-6 slide-in">
                            @yield('content')
                        </div>

                        <!-- Botones de navegación -->
                        <div class="mt-8 flex justify-between items-center">
                            @php
                                $stepKeys = array_keys($steps);
                                $currentStepIndex = array_search($currentRoute, array_column($steps, 'route'));
                                $previousStepIndex = $currentStepIndex - 1;
                                $nextStepIndex = $currentStepIndex + 1;
                                $hasPrevious = $previousStepIndex >= 0;
                                $hasNext = $nextStepIndex < count($steps);
                            @endphp

                            <div>
                                @if ($hasPrevious)
                                    <a href="{{ route($steps[$stepKeys[$previousStepIndex]]['route']) }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Atrás
                                    </a>
                                @endif
                            </div>

                            <div>
                                @if ($hasNext)
                                    <form id="next-step-form" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" form="installer-form" id="next-step-button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                            Siguiente
                                            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <form id="finish-form" method="POST"
                                        action="{{ route('installation.finish.store') }}" class="inline-block">
                                        @csrf
                                        <button type="submit" form="installer-form" id="finish-button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                            Finalizar instalación
                                            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="bg-white dark:bg-gray-800 shadow-inner">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div class="text-gray-500 dark:text-gray-400 text-sm">
                                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos
                                reservados.
                            </div>
                            <div class="text-gray-500 dark:text-gray-400 text-sm">
                                Powered by <a href="https://dansware.dev" target="_blank"
                                    class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">Dansware Dev</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Script personalizado -->
        <script src="{{ asset('installer/js/installer.js') }}"></script>

        <script>
            // Asegurar que los formularios con método POST siempre incluyan el token CSRF
            document.addEventListener('DOMContentLoaded', function() {
                // Inicializar los tooltips
                const initTooltips = () => {
                    const tooltips = document.querySelectorAll('[data-tooltip]');
                    tooltips.forEach(tooltip => {
                        tooltip.addEventListener('mouseenter', () => {
                            const content = tooltip.getAttribute('data-tooltip');
                            const tooltipEl = document.createElement('div');
                            tooltipEl.className =
                                'absolute z-50 p-2 bg-gray-900 text-white text-xs rounded shadow-lg';
                            tooltipEl.textContent = content;
                            tooltipEl.style.bottom = '100%';
                            tooltipEl.style.left = '50%';
                            tooltipEl.style.transform = 'translateX(-50%) translateY(-5px)';
                            tooltipEl.style.whiteSpace = 'nowrap';
                            tooltip.style.position = 'relative';
                            tooltip.appendChild(tooltipEl);
                        });

                        tooltip.addEventListener('mouseleave', () => {
                            const tooltipEl = tooltip.querySelector('div');
                            if (tooltipEl) tooltipEl.remove();
                        });
                    });
                };

                initTooltips();

                // Inicializar botones de información
                const infoBtns = document.querySelectorAll('.info-btn');
                infoBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const infoPanel = document.getElementById(targetId);
                        if (infoPanel) {
                            if (infoPanel.classList.contains('hidden')) {
                                infoPanel.classList.remove('hidden');
                                infoPanel.classList.add('fade-in');
                            } else {
                                infoPanel.classList.add('hidden');
                                infoPanel.classList.remove('fade-in');
                            }
                        }
                    });
                });

                // Animar las alertas para que desaparezcan después de 5 segundos
                const alerts = document.querySelectorAll('.mb-6.rounded-lg');
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.classList.add('opacity-0');
                        alert.style.transition = 'opacity 0.5s ease-in-out';
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    });
                }, 5000);
            });
        </script>

        @yield('scripts')
    </body>

</html>
