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

<div class="mb-4">
    <h4><i class="fas fa-server me-2"></i>Versión de PHP</h4>
    <div class="requirement-item {{ $requirements['php_version']['satisfied'] ? 'success' : 'error' }}">
        <div class="me-3">
            @if($requirements['php_version']['satisfied'])
                <i class="fas fa-check-circle text-success"></i>
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </div>
        <div class="flex-grow-1">
            <strong>Versión de PHP:</strong> {{ $requirements['php_version']['current'] }}
            <small class="text-muted d-block">Requerida: {{ $requirements['php_version']['required'] }} o superior</small>
        </div>
    </div>
</div>

<div class="mb-4">
    <h4><i class="fas fa-puzzle-piece me-2"></i>Extensiones de PHP</h4>
    @foreach($requirements['php_extensions'] as $extension => $status)
        <div class="requirement-item {{ $status['installed'] ? 'success' : 'error' }}">
            <div class="me-3">
                @if($status['installed'])
                    <i class="fas fa-check-circle text-success"></i>
                @else
                    <i class="fas fa-times-circle text-danger"></i>
                @endif
            </div>
            <div class="flex-grow-1">
                <strong>{{ $extension }}</strong>
                @if(!$status['installed'])
                    <small class="text-danger d-block">Esta extensión debe ser instalada y habilitada</small>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="mb-4">
    <h4><i class="fas fa-folder me-2"></i>Permisos de Directorios</h4>
    @foreach($requirements['permissions'] as $path => $status)
        <div class="requirement-item {{ $status['satisfied'] ? 'success' : 'error' }}">
            <div class="me-3">
                @if($status['satisfied'])
                    <i class="fas fa-check-circle text-success"></i>
                @else
                    <i class="fas fa-times-circle text-danger"></i>
                @endif
            </div>
            <div class="flex-grow-1">
                <strong>{{ $path }}</strong>
                <small class="text-muted d-block">
                    Actual: {{ $status['current'] }} | Requerido: {{ $status['required'] }}
                </small>
                @if(!$status['satisfied'])
                    <small class="text-danger d-block">
                        Ejecute: <code>chmod -R {{ $status['required'] }} {{ $path }}</code>
                    </small>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="alert alert-info">
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