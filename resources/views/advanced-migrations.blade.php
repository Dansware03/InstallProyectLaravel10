@extends('installer::layout')

@section('title', 'Migraciones - Instalación Avanzada')

@section('header', 'Paso 3: Migraciones de Base de Datos')

@section('description', 'Decida si desea ejecutar las migraciones de la base de datos')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'completed'],
        ['name' => 'Migraciones', 'status' => 'active'],
        ['name' => 'Entorno', 'status' => 'pending'],
        ['name' => 'Config. Final', 'status' => 'pending'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

@section('content')
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <strong>¡Conexión a Base de Datos Exitosa!</strong> Los datos proporcionados son correctos.
        </div>
    </div>

    <h5 class="mt-4 mb-3"><i class="fas fa-layer-group me-2 text-primary"></i>Ejecutar Migraciones</h5>
    <p class="text-muted small mb-2">
        Las migraciones configuran la estructura de tablas necesaria para su aplicación (ej. tabla de usuarios).
    </p>
    <p class="text-muted small mb-3">
        Si ya tiene tablas en su base de datos o desea crearlas manualmente más tarde, puede omitir este paso.
        <strong>Importante:</strong> Si omite este paso, la creación automática del usuario administrador también se omitirá.
    </p>

    @error('migrations')
        <div class="alert alert-danger mt-3 py-2">
            <i class="fas fa-times-circle me-2"></i>{{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('installer.advanced.migrations.process') }}" id="migrations-form">
        @csrf
        <div class="form-check form-switch p-3 border rounded mb-3" style="background-color: #f8f9fa;">
            <input class="form-check-input" type="checkbox" role="switch" id="run_migrations" name="run_migrations" value="yes"
                   {{ old('run_migrations', session('installer.migrations_run', true)) ? 'checked' : '' }} style="transform: scale(1.3); margin-top: 0.3em;">
            <label class="form-check-label ps-2" for="run_migrations" style="font-weight: 500;">
                Sí, ejecutar las migraciones de la base de datos.
            </label>
        </div>
    </form>
@endsection

@section('footer-actions')
    <a href="{{ route('installer.advanced.database') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Anterior
    </a>
    <button type="submit" form="migrations-form" class="btn btn-installer-primary">
        Siguiente <i class="fas fa-arrow-right ms-1"></i>
    </button>
@endsection
