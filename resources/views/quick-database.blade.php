@extends('installer::layout')

@section('title', 'Configuración de Base de Datos - Instalación Rápida')

@section('header', 'Configuración de Base de Datos')

@section('description', 'Ingrese los datos de conexión a su base de datos MySQL')

@section('steps')
<ul class="step-indicator-vertical">
    <li class="step completed">
        <span class="step-number"><i class="fas fa-check"></i></span>
        <span class="step-label">Requisitos del Sistema</span>
    </li>
    <li class="step active">
        <span class="step-number">2</span>
        <span class="step-label">Base de Datos</span>
    </li>
    <li class="step">
        <span class="step-number">3</span>
        <span class="step-label">Instalación</span>
    </li>
</ul>
@endsection

@section('content')
<form method="POST" action="{{ route('installer.quick.process') }}">
    @csrf
    
    <div class="mb-4">
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Excelente!</strong> Todos los requisitos del sistema se cumplen correctamente.
        </div>
    </div>
    
    <h4 class="mb-4"><i class="fas fa-database me-2"></i>Datos de Conexión MySQL</h4>
    
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="database_host" class="form-label">
                    <i class="fas fa-server me-1"></i>Servidor de Base de Datos
                </label>
                <input type="text" class="form-control" id="database_host" name="database_host" 
                       value="{{ old('database_host', 'localhost') }}" required>
                <div class="form-text">Generalmente es "localhost" o una IP específica</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="database_port" class="form-label">
                    <i class="fas fa-plug me-1"></i>Puerto
                </label>
                <input type="number" class="form-control" id="database_port" name="database_port" 
                       value="{{ old('database_port', '3306') }}" required>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="database_name" class="form-label">
            <i class="fas fa-database me-1"></i>Nombre de la Base de Datos
        </label>
        <input type="text" class="form-control" id="database_name" name="database_name" 
               value="{{ old('database_name') }}" required>
        <div class="form-text">La base de datos debe existir previamente</div>
    </div>
    
    <div class="mb-3">
        <label for="database_username" class="form-label">
            <i class="fas fa-user me-1"></i>Usuario de Base de Datos
        </label>
        <input type="text" class="form-control" id="database_username" name="database_username" 
               value="{{ old('database_username') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="database_password" class="form-label">
            <i class="fas fa-lock me-1"></i>Contraseña
        </label>
        <input type="password" class="form-control" id="database_password" name="database_password" 
               value="{{ old('database_password') }}">
        <div class="form-text">Deje en blanco si no tiene contraseña.</div>
    </div>
    
    <div class="alert alert-info mt-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <strong class="d-block mb-1">La instalación rápida configurará automáticamente:</strong>
                <ul class="list-unstyled mb-0 small">
                    <li><i class="fas fa-shield-alt me-1 text-primary"></i>Configuraciones de seguridad para producción.</li>
                    <li><i class="fas fa-tachometer-alt me-1 text-primary"></i>Optimizaciones de rendimiento.</li>
                    <li><i class="fas fa-user-shield me-1 text-primary"></i>Usuario administrador con credenciales temporales.</li>
                    <li><i class="fas fa-database me-1 text-primary"></i>Ejecución de migraciones de base de datos.</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mt-5">
        <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-rocket me-2"></i>Iniciar Instalación Rápida
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real de la conexión de base de datos
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>
@endpush
@endsection