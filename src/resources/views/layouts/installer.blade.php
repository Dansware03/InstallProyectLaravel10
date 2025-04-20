<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('installer.title', 'Instalador') }} - @yield('title')</title>

        <!-- Tailwind CSS (CDN para simplificar) -->
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                                900: '#0c4a6e',
                                950: '#082f49',
                            },
                        }
                    }
                }
            }
        </script>

        <!-- Estilos adicionales -->
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @yield('styles')
    </head>

    <body class="bg-gray-50 min-h-screen">
        <div class="flex min-h-screen">
            <!-- Sidebar / Pasos -->
            <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-primary-700">
                <div class="flex-1 flex flex-col min-h-0 bg-primary-800">
                    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-primary-900">
                        <img class="h-8 w-auto"
                            src="{{ config('installer.logo', 'https://via.placeholder.com/150x50?text=Logo') }}"
                            alt="Logo">
                        <span
                            class="ml-2 text-white font-semibold text-lg">{{ config('installer.title', 'Instalador') }}</span>
                    </div>
                    <div class="flex-1 flex flex-col overflow-y-auto">
                        <nav class="flex-1 px-2 py-4 space-y-1">
                            @php
                                $steps = config('installer.steps', []);
                                $currentRoute = Route::currentRouteName();
                            @endphp

                            @foreach ($steps as $step => $stepInfo)
                                <a href="{{ route($stepInfo['route']) }}"
                                    class="{{ $currentRoute === $stepInfo['route'] ? 'bg-primary-900 text-white' : 'text-primary-100 hover:bg-primary-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="mr-3 h-6 w-6 {{ $currentRoute === $stepInfo['route'] ? 'text-primary-300' : 'text-primary-400 group-hover:text-primary-300' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        @if ($stepInfo['icon'] === 'home')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        @elseif($stepInfo['icon'] === 'check-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @elseif($stepInfo['icon'] === 'database')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                        @elseif($stepInfo['icon'] === 'flag')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        @endif
                                    </svg>
                                    {{ $stepInfo['title'] }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="md:pl-64 flex flex-col flex-1">
                <main class="flex-1">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            <div class="pb-5 border-b border-gray-200 flex justify-between items-center">
                                <h1 class="text-2xl font-semibold text-gray-900">@yield('title')</h1>

                                <!-- Versión móvil del menú -->
                                <div class="md:hidden">
                                    <div x-data="{ open: false }" @keydown.escape="open = false">
                                        <button @click="open = !open" type="button"
                                            class="inline-flex items-center justify-center p-2 rounded-md text-primary-700 hover:text-primary-900 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                                            <span class="sr-only">Abrir menú</span>
                                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-cloak class="fixed inset-0 flex z-40"
                                            x-transition:enter="transition-opacity ease-linear duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition-opacity ease-linear duration-300"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                                            <div x-show="open" @click="open = false" class="fixed inset-0"
                                                x-description="Background overlay">
                                                <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
                                            </div>

                                            <div x-show="open"
                                                class="relative flex-1 flex flex-col max-w-xs w-full bg-primary-800"
                                                x-transition:enter="transition ease-in-out duration-300 transform"
                                                x-transition:enter-start="-translate-x-full"
                                                x-transition:enter-end="translate-x-0"
                                                x-transition:leave="transition ease-in-out duration-300 transform"
                                                x-transition:leave-start="translate-x-0"
                                                x-transition:leave-end="-translate-x-full">

                                                <div class="absolute top-0 right-0 -mr-12 pt-2">
                                                    <button @click="open = false" type="button"
                                                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                                        <span class="sr-only">Cerrar menú</span>
                                                        <svg class="h-6 w-6 text-white"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                                                    <div class="flex-shrink-0 flex items-center px-4">
                                                        <img class="h-8 w-auto"
                                                            src="{{ config('installer.logo', 'https://via.placeholder.com/150x50?text=Logo') }}"
                                                            alt="Logo">
                                                        <span
                                                            class="ml-2 text-white font-semibold text-lg">{{ config('installer.title', 'Instalador') }}</span>
                                                    </div>
                                                    <nav class="mt-5 px-2 space-y-1">
                                                        @foreach ($steps as $step => $stepInfo)
                                                            <a href="{{ route($stepInfo['route']) }}"
                                                                class="{{ $currentRoute === $stepInfo['route'] ? 'bg-primary-900 text-white' : 'text-primary-100 hover:bg-primary-700' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                                                <svg class="mr-4 h-6 w-6 {{ $currentRoute === $stepInfo['route'] ? 'text-primary-300' : 'text-primary-400 group-hover:text-primary-300' }}"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    @if ($stepInfo['icon'] === 'home')
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                                    @elseif($stepInfo['icon'] === 'check-circle')
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    @elseif($stepInfo['icon'] === 'database')
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                                                    @elseif($stepInfo['icon'] === 'flag')
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                                                    @else
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M5 13l4 4L19 7" />
                                                                    @endif
                                                                </svg>
                                                                {{ $stepInfo['title'] }}
                                                            </a>
                                                        @endforeach
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-6">
                            <!-- Alertas -->
                            @if (session('success'))
                                <div class="mb-4 rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-4 rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-4 rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Contenido principal -->
                            <div class="py-4">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        @yield('scripts')
    </body>

</html>
