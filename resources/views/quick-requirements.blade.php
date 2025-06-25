@extends('installer::layout')

@section('title', 'Requisitos del Sistema - Instalación Rápida')

@section('header', 'Verificación de Requisitos')

@section('description', 'Se detectaron problemas que deben resolverse antes de continuar')

@section('content')
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Atención:</strong> Se encontraron algunos requisitos que no se cumplen. 
    Debe resolverlos antes de continuar con la instalación.
</div>

<h4 class="mb-3 mt-4"><i class="fas fa-server me-2"></i>Versión de PHP</h4>
<ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>
            <i class="fas {{ $requirements['php_version']['satisfied'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
            PHP {{ $requirements['php_version']['required'] }} o superior
            <small class="text-muted d-block ms-4 ps-1">Actual: {{ $requirements['php_version']['current'] }}</small>
        </span>
        @if ($requirements['php_version']['satisfied'])
            <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Cumplido</span>
        @else
            <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> No Cumplido</span>
        @endif
    </li>
</ul>

<h4 class="mb-3"><i class="fas fa-puzzle-piece me-2"></i>Extensiones de PHP</h4>
<ul class="list-group mb-4">
    @foreach($requirements['php_extensions'] as $extension => $status)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
                <i class="fas {{ $status['installed'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                Extensión {{ $extension }}
            </span>
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
            <span>
                <i class="fas {{ $status['satisfied'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                Permisos para <code>{{ $path }}</code>
                 <small class="text-muted d-block ms-4 ps-1">Requerido: {{ $status['required'] }} (Actual: {{ $status['current'] }})</small>
            </span>
            @if ($status['satisfied'])
                <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Correctos</span>
            @else
                <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Incorrectos</span>
            @endif
        </li>
        @if (!$status['satisfied'])
        <li class="list-group-item list-group-item-warning py-2">
            <small><i class="fas fa-info-circle me-1"></i>Ejecute: <code>chmod -R {{ $status['required'] }} {{ base_path($path) }}</code></small>
        </li>
        @endif
    @endforeach
</ul>

<div class="alert alert-info mt-4">
    <i class="fas fa-lightbulb me-2"></i>
    <strong>Ayuda:</strong> Contacte con su administrador de sistemas o proveedor de hosting 
    para resolver estos requisitos. Una vez resueltos, recargue esta página para continuar.
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
    <button onclick="location.reload()" class="btn btn-primary">
        <i class="fas fa-sync-alt me-2"></i>Verificar Nuevamente
    </button>
</div>
@endsection