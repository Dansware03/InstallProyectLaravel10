@extends('installer::layout')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'active'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

@section('title', 'Configuración de Base de Datos - Instalación Rápida')

@section('header', 'Configuración de Base de Datos')

@section('description', 'Ingrese los datos de conexión a su base de datos MySQL.')

@section('content')
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <strong>¡Requisitos Cumplidos!</strong> Todos los requisitos del sistema se cumplen correctamente.
        </div>
    </div>

    <h5 class="mt-4 mb-3"><i class="fas fa-database me-2 text-primary"></i>Detalles de Conexión a MySQL</h5>

    <form method="POST" action="{{ route('installer.quick.process') }}" id="db-form">
        @csrf
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="database_host" class="form-label">Servidor de Base de Datos</label>
                <input type="text" class="form-control @error('database_host') is-invalid @enderror" id="database_host" name="database_host"
                       value="{{ old('database_host', 'localhost') }}" required>
                @error('database_host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Ej: localhost, 127.0.0.1</div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="database_port" class="form-label">Puerto</label>
                <input type="number" class="form-control @error('database_port') is-invalid @enderror" id="database_port" name="database_port"
                       value="{{ old('database_port', '3306') }}" required>
                @error('database_port') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="database_name" class="form-label">Nombre de la Base de Datos</label>
            <input type="text" class="form-control @error('database_name') is-invalid @enderror" id="database_name" name="database_name"
                   value="{{ old('database_name') }}" required>
            @error('database_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">La base de datos debe existir previamente.</div>
        </div>

        <div class="mb-3">
            <label for="database_username" class="form-label">Usuario de Base de Datos</label>
            <input type="text" class="form-control @error('database_username') is-invalid @enderror" id="database_username" name="database_username"
                   value="{{ old('database_username') }}" required>
            @error('database_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="database_password" class="form-label">Contraseña</label>
            <input type="password" class="form-control @error('database_password') is-invalid @enderror" id="database_password" name="database_password">
            @error('database_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Deje en blanco si el usuario no tiene contraseña.</div>
        </div>

        @error('database')
            <div class="alert alert-danger mt-3 py-2">
                <i class="fas fa-times-circle me-2"></i>{{ $message }}
            </div>
        @enderror

        <div class="alert alert-info mt-4 small py-2 px-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Información sobre la Instalación Rápida:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <li>Se ejecutarán las migraciones de la base de datos.</li>
                <li>Se aplicarán configuraciones de seguridad para producción.</li>
                <li>Se aplicarán optimizaciones de rendimiento.</li>
                <li>Se creará un usuario administrador con credenciales temporales.</li>
            </ul>
        </div>
    </form>
@endsection

@section('footer-actions')
    <a href="{{ route('installer.welcome') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
    <button type="submit" form="db-form" class="btn btn-installer-primary">
        <i class="fas fa-rocket me-1"></i> Iniciar Instalación Rápida
    </button>
@endsection