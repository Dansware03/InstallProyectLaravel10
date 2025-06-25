@extends('installer::layout')

@section('title', 'Configuración Final - Instalación Avanzada')

@section('header', 'Paso 5: Configuración Final')

@section('description', 'Ajustes finales antes de proceder con la instalación')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'completed'],
        ['name' => 'Migraciones', 'status' => 'completed'],
        ['name' => 'Entorno', 'status' => 'completed'],
        ['name' => 'Config. Final', 'status' => 'active'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

@section('content')
<form method="POST" action="{{ route('installer.advanced.final-config.process') }}" id="final-config-form">
    @csrf

    <h5 class="mb-3"><i class="fas fa-sliders-h me-2 text-primary"></i>Tipo de Entorno</h5>
    <p class="text-muted small mb-3">Seleccione el tipo de entorno para el cual está instalando la aplicación. Esto afectará ciertas configuraciones de optimización y depuración.</p>

    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
        <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="environment_type" id="env_dev" value="development"
                   {{ old('environment_type', session('installer.final_config.environment_type', 'development')) == 'development' ? 'checked' : '' }}>
            <label class="form-check-label" for="env_dev">
                <strong>Desarrollo</strong>
                <small class="d-block text-muted">Recomendado para entornos locales o de prueba. Habilita mensajes de error detallados y desactiva algunas cachés.</small>
            </label>
        </div>
        <hr class="my-2">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="environment_type" id="env_prod" value="production"
                   {{ old('environment_type', session('installer.final_config.environment_type')) == 'production' ? 'checked' : '' }}>
            <label class="form-check-label" for="env_prod">
                <strong>Producción</strong>
                <small class="d-block text-muted">Recomendado para servidores en vivo. Desactiva el modo debug y habilita optimizaciones de caché.</small>
            </label>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3"><i class="fas fa-cogs me-2 text-primary"></i>Opciones Adicionales</h5>

    <div class="form-check form-switch p-3 border rounded mb-3" style="background-color: #f8f9fa;">
        <input class="form-check-input" type="checkbox" role="switch" id="disable_api" name="disable_api" value="1"
               {{ old('disable_api', session('installer.final_config.disable_api', false)) ? 'checked' : '' }} style="transform: scale(1.3); margin-top: 0.3em;">
        <label class="form-check-label ps-2" for="disable_api" style="font-weight:500;">
            Deshabilitar rutas de API (<code>routes/api.php</code>)
            <small class="d-block text-muted mt-1">Marque esta opción si su aplicación no utilizará la API de Laravel (ej. es una aplicación web tradicional sin endpoints de API).</small>
        </label>
    </div>

    <div class="alert alert-info mt-4 small py-2 px-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Próximo Paso:</strong> Después de confirmar, se procederá con la instalación final. Esto incluye actualizar su archivo <code>.env</code>,
        ejecutar las configuraciones seleccionadas y crear el usuario administrador (si se ejecutaron las migraciones).
    </div>
</form>
@endsection

@section('footer-actions')
    <a href="{{ route('installer.advanced.environment') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Anterior
    </a>
    <button type="submit" form="final-config-form" class="btn btn-installer-primary">
        <i class="fas fa-rocket me-1"></i> Iniciar Instalación
    </button>
@endsection
