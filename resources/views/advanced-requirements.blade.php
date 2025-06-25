@extends('installer::layout')

@section('title', 'Requisitos del Sistema - Instalación Avanzada')

@section('header', 'Paso 1: Verificación de Requisitos')

@section('description', 'Comprobando los requisitos del sistema para la instalación avanzada')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'active'],
        ['name' => 'Base de Datos', 'status' => 'pending'],
        ['name' => 'Migraciones', 'status' => 'pending'],
        ['name' => 'Entorno', 'status' => 'pending'],
        ['name' => 'Config. Final', 'status' => 'pending'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

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

    @if (!$allSatisfied)
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong>Atención:</strong> Se encontraron algunos requisitos del sistema que no se cumplen.
                Debe resolverlos antes de poder continuar con la instalación.
            </div>
        </div>
    @endif

    <div class="mb-4 mt-3">
        <h5><i class="fas fa-server me-2 text-primary"></i>Versión de PHP</h5>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas {{ $requirements['php_version']['satisfied'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                    PHP {{ $requirements['php_version']['required'] }} o superior
                    <small class="text-muted d-block ms-4">Actual: {{ $requirements['php_version']['current'] }}</small>
                </div>
                @if ($requirements['php_version']['satisfied'])
                    <span class="badge bg-success">Cumplido</span>
                @else
                    <span class="badge bg-danger">No Cumplido</span>
                @endif
            </li>
        </ul>
    </div>

    <div class="mb-4">
        <h5><i class="fas fa-puzzle-piece me-2 text-primary"></i>Extensiones de PHP</h5>
        <ul class="list-group">
            @foreach($requirements['php_extensions'] as $extension => $status)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas {{ $status['installed'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                        Extensión: <strong>{{ $extension }}</strong>
                    </div>
                    @if ($status['installed'])
                        <span class="badge bg-success">Instalada</span>
                    @else
                        <span class="badge bg-danger">No Instalada</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h5><i class="fas fa-folder-open me-2 text-primary"></i>Permisos de Directorio</h5>
        <ul class="list-group">
            @foreach($requirements['permissions'] as $path => $status)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas {{ $status['satisfied'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                        Permisos para <code>{{ $path }}</code>
                        <small class="text-muted d-block ms-4">Requerido: {{ $status['required'] }} (Actual: {{ $status['current'] }})</small>
                    </div>
                    @if ($status['satisfied'])
                        <span class="badge bg-success">Correctos</span>
                    @else
                        <span class="badge bg-danger">Incorrectos</span>
                    @endif
                </li>
                @if (!$status['satisfied'] && isset($status['required']))
                <li class="list-group-item list-group-item-light py-2 small">
                    <i class="fas fa-info-circle me-1"></i>Sugerencia: <code>chmod -R {{ $status['required'] }} {{ $path }}</code> (ejecutar en la raíz del proyecto)
                </li>
                @endif
            @endforeach
        </ul>
    </div>

    @if ($allSatisfied)
        <div class="alert alert-success mt-4 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fa-2x me-3"></i>
            <div>
                <strong>¡Excelente!</strong> Todos los requisitos del sistema se cumplen. Puede continuar con el siguiente paso.
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4 d-flex align-items-center" role="alert">
            <i class="fas fa-lightbulb fa-2x me-3"></i>
            <div>
                <strong>Ayuda:</strong> Contacte con el administrador de su sistema o proveedor de hosting para resolver estos problemas.
                Una vez resueltos, recargue esta página para verificar nuevamente.
            </div>
        </div>
    @endif
@endsection

@section('footer-actions')
    <a href="{{ route('installer.welcome') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
    @if ($allSatisfied)
        <a href="{{ route('installer.advanced.database') }}" class="btn btn-installer-primary">
            Siguiente <i class="fas fa-arrow-right ms-1"></i>
        </a>
    @else
        <button onclick="location.reload()" class="btn btn-installer-primary">
            <i class="fas fa-sync-alt me-1"></i> Verificar Nuevamente
        </button>
    @endif
@endsection
