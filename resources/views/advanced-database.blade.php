@extends('installer::layout')

@section('title', 'Base de Datos - Instalación Avanzada')

@section('header', 'Paso 2: Configuración de Base de Datos')

@section('description', 'Ingrese los detalles de conexión para su base de datos MySQL.')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'active'],
        ['name' => 'Migraciones', 'status' => 'pending'],
        ['name' => 'Entorno', 'status' => 'pending'],
        ['name' => 'Config. Final', 'status' => 'pending'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

@section('content')
    <h5 class="mb-3"><i class="fas fa-database me-2 text-primary"></i>Datos de Conexión MySQL</h5>
    <p class="text-muted small mb-4">
        Por favor, ingrese los detalles de conexión a su base de datos. El instalador intentará verificar la conexión.
    </p>

    <form method="POST" action="{{ route('installer.advanced.database.process') }}" id="db-form-advanced">
        @csrf
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="database_host" class="form-label">Servidor de Base de Datos</label>
                <input type="text" class="form-control @error('database_host') is-invalid @enderror" id="database_host" name="database_host"
                       value="{{ old('database_host', session('installer.database.DB_HOST', 'localhost')) }}" required>
                @error('database_host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Ej: localhost, 127.0.0.1</div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="database_port" class="form-label">Puerto</label>
                <input type="number" class="form-control @error('database_port') is-invalid @enderror" id="database_port" name="database_port"
                       value="{{ old('database_port', session('installer.database.DB_PORT', '3306')) }}" required>
                @error('database_port') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="database_name" class="form-label">Nombre de la Base de Datos</label>
            <input type="text" class="form-control @error('database_name') is-invalid @enderror" id="database_name" name="database_name"
                   value="{{ old('database_name', session('installer.database.DB_DATABASE')) }}" required>
            @error('database_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">La base de datos debe existir previamente.</div>
        </div>

        <div class="mb-3">
            <label for="database_username" class="form-label">Usuario de Base de Datos</label>
            <input type="text" class="form-control @error('database_username') is-invalid @enderror" id="database_username" name="database_username"
                   value="{{ old('database_username', session('installer.database.DB_USERNAME')) }}" required>
            @error('database_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="database_password" class="form-label">Contraseña</label>
            <input type="password" class="form-control @error('database_password') is-invalid @enderror" id="database_password" name="database_password"
                   value="{{ old('database_password', session('installer.database.DB_PASSWORD')) }}"> {/* No se usa old() para password por seguridad al recargar */}
            @error('database_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Deje en blanco si el usuario no tiene contraseña.</div>
        </div>

        @error('database')
            <div class="alert alert-danger mt-3 py-2">
                 <i class="fas fa-times-circle me-2"></i>{{ $message }}
            </div>
        @enderror

    </form>
@endsection

@section('footer-actions')
    <a href="{{ route('installer.advanced.requirements') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Anterior
    </a>
    <button type="submit" form="db-form-advanced" class="btn btn-installer-primary">
        Probar y Continuar <i class="fas fa-arrow-right ms-1"></i>
    </button>
@endsection
