@extends('installer::layout')

@section('title', 'Migraciones - Instalación Avanzada')

@section('header', 'Paso 3: Migraciones de Base de Datos')

@section('description', 'Decida si desea ejecutar las migraciones de la base de datos')

@section('steps')
<ul class="step-indicator-vertical">
    <li class="step completed">
        <span class="step-number"><i class="fas fa-check"></i></span>
        <span class="step-label">Requisitos</span>
    </li>
    <li class="step completed">
        <span class="step-number"><i class="fas fa-check"></i></span>
        <span class="step-label">Base de Datos</span>
    </li>
    <li class="step active">
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
<form method="POST" action="{{ route('installer.advanced.migrations.process') }}">
    @csrf

    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <strong>¡Conexión Exitosa!</strong> Los datos de la base de datos son correctos.
    </div>

    <h4 class="mb-3"><i class="fas fa-database me-2"></i>Ejecutar Migraciones</h4>
    <p>
        Las migraciones configuran la estructura de tablas necesaria para su aplicación.
        Si ya tiene tablas en su base de datos o desea crearlas manualmente más tarde, puede omitir este paso.
    </p>
    <p>
        <strong>Nota:</strong> Si omite este paso, la creación automática del usuario administrador también se omitirá.
    </p>

    <div class="form-check form-switch mb-4 p-3 border rounded">
        <input class="form-check-input" type="checkbox" role="switch" id="run_migrations" name="run_migrations" value="yes"
               {{ old('run_migrations', session('installer.migrations_run', true)) ? 'checked' : '' }}>
        <label class="form-check-label" for="run_migrations">
            Sí, ejecutar las migraciones de la base de datos.
        </label>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('installer.advanced.database') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Anterior
        </a>
        <button type="submit" class="btn btn-primary">
            Siguiente: Configuración de Entorno <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>
@endsection
