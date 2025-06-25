@extends('installer::layout')

@section('title', 'Base de Datos - Instalación Avanzada')

@section('header', 'Paso 2: Configuración de Base de Datos')

@section('description', 'Ingrese los detalles de conexión para su base de datos MySQL')

@section('steps')
<div class="step-indicator">
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step active">2</div>
    <div class="step">3</div>
    <div class="step">4</div>
    <div class="step">5</div>
    <div class="step">6</div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('installer.advanced.database.process') }}">
    @csrf

    <h4 class="mb-4"><i class="fas fa-database me-2"></i>Datos de Conexión MySQL</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="database_host" class="form-label">
                    <i class="fas fa-server me-1"></i>Servidor de Base de Datos
                </label>
                <input type="text" class="form-control @error('database_host') is-invalid @enderror" id="database_host" name="database_host"
                       value="{{ old('database_host', session('installer.database.DB_HOST', 'localhost')) }}" required>
                @error('database_host')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Generalmente es "localhost" o una IP específica.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="database_port" class="form-label">
                    <i class="fas fa-plug me-1"></i>Puerto
                </label>
                <input type="number" class="form-control @error('database_port') is-invalid @enderror" id="database_port" name="database_port"
                       value="{{ old('database_port', session('installer.database.DB_PORT', '3306')) }}" required>
                @error('database_port')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="database_name" class="form-label">
            <i class="fas fa-database me-1"></i>Nombre de la Base de Datos
        </label>
        <input type="text" class="form-control @error('database_name') is-invalid @enderror" id="database_name" name="database_name"
               value="{{ old('database_name', session('installer.database.DB_DATABASE')) }}" required>
        @error('database_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">La base de datos debe existir previamente.</div>
    </div>

    <div class="mb-3">
        <label for="database_username" class="form-label">
            <i class="fas fa-user me-1"></i>Usuario de Base de Datos
        </label>
        <input type="text" class="form-control @error('database_username') is-invalid @enderror" id="database_username" name="database_username"
               value="{{ old('database_username', session('installer.database.DB_USERNAME')) }}" required>
        @error('database_username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="database_password" class="form-label">
            <i class="fas fa-lock me-1"></i>Contraseña
        </label>
        <input type="password" class="form-control @error('database_password') is-invalid @enderror" id="database_password" name="database_password"
               value="{{ old('database_password', session('installer.database.DB_PASSWORD')) }}">
        @error('database_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Deje en blanco si no tiene contraseña.</div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Se intentará establecer una conexión para verificar los datos proporcionados.
        Esta información se guardará temporalmente para los siguientes pasos.
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('installer.advanced.requirements') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Anterior
        </a>
        <button type="submit" class="btn btn-primary">
            Probar Conexión y Continuar <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>
@endsection
