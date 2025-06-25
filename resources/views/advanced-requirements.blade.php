@extends('installer::layout')

@section('title', 'Requisitos del Sistema - Instalación Avanzada')

@section('header', 'Paso 1: Verificación de Requisitos')

@section('description', 'Comprobando los requisitos del sistema para la instalación avanzada')

@section('steps')
<ul class="step-indicator-vertical">
    <li class="step active">
        <span class="step-number">1</span>
        <span class="step-label">Requisitos</span>
    </li>
    <li class="step">
        <span class="step-number">2</span>
        <span class="step-label">Base de Datos</span>
    </li>
    <li class="step">
        <span class="step-number">3</span>
        <span class="step-label">Migraciones</span>
    </li>
    <li class="step">
        <span class="step-number">4</span>
        <span class="step-label">Entorno</span>
    </li>
    <li class="step">
        <span class="step-number">5</span>
        <span class="step-label">Configuración Final</span>
    </li>
    <li class="step">
        <span class="step-number">6</span>
        <span class="step-label">Instalación</span>
    </li>
</ul>
@endsection

@section('content')
    @php
        $allSatisfied = true;
        if (!$requirements['php_version']['satisfied']) $allSatisfied = false;
        foreach ($requirements['php_extensions'] as $ext) {
            if (!$ext['installed']) $allSatisfied = false;
        }
        foreach ($requirements['permissions'] as $perm) {
            if (!$perm['satisfied']) $allSatisfied = false;
        }
    @endphp

    <h4 class="mb-3"><i class="fas fa-server me-2"></i>Versión de PHP</h4>
    <ul class="list-group mb-4">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            PHP {{ $requirements['php_version']['required'] }} o superior
            @if ($requirements['php_version']['satisfied'])
                <span class="badge bg-success rounded-pill"><i class="fas fa-check me-1"></i> {{ $requirements['php_version']['current'] }}</span>
            @else
                <span class="badge bg-danger rounded-pill"><i class="fas fa-times me-1"></i> {{ $requirements['php_version']['current'] }}</span>
            @endif
        </li>
    </ul>

    <h4 class="mb-3"><i class="fas fa-puzzle-piece me-2"></i>Extensiones de PHP</h4>
    <ul class="list-group mb-4">
        @foreach($requirements['php_extensions'] as $extension => $status)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Extensión {{ $extension }}
                @if ($status['installed'])
                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Instalada</span>
                @else
                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> No Instalada</span>
                @endif
            </li>
        @endforeach
    </ul>

    <h4 class="mb-3"><i class="fas fa-folder-open me-2"></i>Permisos de Directorio</h4>
    <ul class="list-group mb-4">
        @foreach($requirements['permissions'] as $path => $status)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Permisos para <code>{{ $path }}</code> (Requerido: {{ $status['required'] }})
                @if ($status['satisfied'])
                    <span class="badge bg-success rounded-pill"><i class="fas fa-check me-1"></i> {{ $status['current'] }}</span>
                @else
                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times me-1"></i> {{ $status['current'] }}</span>
                @endif
            </li>
            @if (!$status['satisfied'])
            <li class="list-group-item list-group-item-warning">
                <small>Ejecute: <code>chmod -R {{ $status['required'] }} {{ $path }}</code></small>
            </li>
            @endif
        @endforeach
    </ul>

    @if ($allSatisfied)
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Excelente!</strong> Todos los requisitos del sistema se cumplen.
        </div>
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('installer.advanced.database') }}" class="btn btn-primary">
                Siguiente: Base de Datos <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    @else
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Atención:</strong> Algunos requisitos no se cumplen. Por favor, soluciónelos y vuelva a intentarlo.
        </div>
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="location.reload()" class="btn btn-primary">
                <i class="fas fa-sync-alt me-2"></i>Verificar Nuevamente
            </button>
        </div>
    @endif
@endsection
