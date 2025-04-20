@extends('installer::layouts.installer')

@section('title', 'Bienvenido al Instalador')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
            Bienvenido al Asistente de Instalación
        </h2>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Este proceso te guiará a través de la configuración básica de tu aplicación Laravel.
        </p>
    </div>
    <div class="border-t border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <p class="text-base text-gray-700">
                Antes de comenzar, necesitamos verificar que tu servidor cumple con los requisitos necesarios
                y configurar algunos parámetros básicos. El proceso consta de los siguientes pasos:
            </p>

            <div class="mt-6 space-y-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600">
                            <span class="text-lg font-medium">1</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Verificación de requisitos</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Comprobamos que tu sistema cumple con todos los requisitos necesarios para ejecutar Laravel.
                        </p>
                    </div>
                </div>

                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600">
                            <span class="text-lg font-medium">2</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Configuración de base de datos</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Configuramos la conexión a tu base de datos (MySQL o SQLite).
                        </p>
                    </div>
                </div>

                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600">
                            <span class="text-lg font-medium">3</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Finalización</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Ejecutamos las migraciones y configuramos los parámetros finales de la aplicación.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('installation.requirements') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Comenzar Instalación
                    <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection