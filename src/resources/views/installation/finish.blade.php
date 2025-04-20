@extends('installer::layouts.installer')

@section('title', 'Finalizar Instalación')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">Finalizar Instalación</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('installation.finish') }}" class="space-y-6">
        @csrf

        <div>
            <label for="app_url" class="block font-medium">URL de la Aplicación</label>
            <input type="url" name="app_url" id="app_url" class="mt-1 block w-full" required>
        </div>

        <div>
            <label for="environment" class="block font-medium">Entorno</label>
            <select name="environment" id="environment" class="mt-1 block w-full">
                <option value="local">Local</option>
                <option value="production">Producción</option>
            </select>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="run_migrations" id="run_migrations" class="mr-2">
            <label for="run_migrations">Ejecutar migraciones automáticamente</label>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">Finalizar Instalación</button>
        </div>
    </form>
</div>
@endsection
