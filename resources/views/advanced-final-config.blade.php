@extends('installer::layout')

@section('title', 'Configuración Final - Instalación Avanzada')

@section('header', 'Paso 5: Configuración Final')

@section('description', 'Ajustes finales antes de proceder con la instalación')

@section('steps')
<div class="step-indicator">
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step active">5</div>
    <div class="step">6</div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('installer.advanced.final-config.process') }}">
    @csrf

    <h4 class="mb-3"><i class="fas fa-sliders-h me-2"></i>Tipo de Entorno</h4>
    <p>Seleccione el tipo de entorno para el cual está instalando la aplicación. Esto afectará ciertas configuraciones de optimización y depuración.</p>
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="environment_type" id="env_dev" value="development"
               {{ old('environment_type', session('installer.final_config.environment_type', 'development')) == 'development' ? 'checked' : '' }}>
        <label class="form-check-label" for="env_dev">
            <strong>Desarrollo</strong> (Recomendado para entornos locales o de prueba)
            <small class="d-block text-muted">Habilita mensajes de error detallados, desactiva algunas cachés.</small>
        </label>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="environment_type" id="env_prod" value="production"
               {{ old('environment_type', session('installer.final_config.environment_type')) == 'production' ? 'checked' : '' }}>
        <label class="form-check-label" for="env_prod">
            <strong>Producción</strong> (Recomendado para servidores en vivo)
            <small class="d-block text-muted">Desactiva el modo debug, habilita optimizaciones de caché (configuración, rutas, vistas).</small>
        </label>
    </div>

    <hr class="my-4">

    <h4 class="mb-3"><i class="fas fa-shield-alt me-2"></i>Opciones Adicionales</h4>

    <div class="form-check form-switch mb-3 p-3 border rounded">
        <input class="form-check-input" type="checkbox" role="switch" id="disable_api" name="disable_api" value="1"
               {{ old('disable_api', session('installer.final_config.disable_api', false)) ? 'checked' : '' }}>
        <label class="form-check-label" for="disable_api">
            Deshabilitar rutas de API (<code>routes/api.php</code>)
            <small class="d-block text-muted">Marque esta opción si su aplicación no utilizará la API de Laravel.</small>
        </label>
    </div>

    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        Después de este paso, se procederá con la instalación. Se actualizará su archivo <code>.env</code>,
        se ejecutarán las configuraciones seleccionadas y se creará el usuario administrador si se ejecutaron las migraciones.
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('installer.advanced.environment') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Anterior
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-rocket me-2"></i>Iniciar Instalación
        </button>
    </div>
</form>
@endsection
