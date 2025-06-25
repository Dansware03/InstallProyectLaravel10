@extends('installer::layout')

@section('title', 'Entorno - Instalación Avanzada')

@section('header', 'Paso 4: Configuración del Entorno')

@section('description', 'Configure los detalles básicos de su aplicación y el envío de correo')

@section('steps')
<div class="step-indicator">
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step completed"><i class="fas fa-check"></i></div>
    <div class="step active">4</div>
    <div class="step">5</div>
    <div class="step">6</div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('installer.advanced.environment.process') }}">
    @csrf

    <h4 class="mb-3"><i class="fas fa-cogs me-2"></i>Configuración de la Aplicación</h4>
    <div class="mb-3">
        <label for="app_name" class="form-label">Nombre de la Aplicación</label>
        <input type="text" class="form-control" id="app_name" name="app_name"
               value="{{ old('app_name', session('installer.environment.APP_NAME', config('app.name'))) }}">
        <div class="form-text">Este nombre se utilizará en toda la aplicación.</div>
    </div>

    <hr class="my-4">

    <h4 class="mb-3"><i class="fas fa-envelope me-2"></i>Configuración de Correo (Opcional)</h4>
    <p>Configure cómo su aplicación enviará correos electrónicos. Puede omitir esto y configurarlo más tarde en su archivo <code>.env</code>.</p>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="mail_driver" class="form-label">Driver de Correo</label>
            <select class="form-select" id="mail_driver" name="mail_driver">
                <option value="smtp" {{ old('mail_driver', session('installer.environment.MAIL_MAILER')) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                <option value="sendmail" {{ old('mail_driver', session('installer.environment.MAIL_MAILER')) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                <option value="log" {{ old('mail_driver', session('installer.environment.MAIL_MAILER', 'log')) == 'log' ? 'selected' : '' }}>Log (para desarrollo)</option>
                <option value="array" {{ old('mail_driver', session('installer.environment.MAIL_MAILER')) == 'array' ? 'selected' : '' }}>Array (para pruebas)</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="mail_host" class="form-label">Host SMTP</label>
            <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', session('installer.environment.MAIL_HOST')) }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="mail_port" class="form-label">Puerto SMTP</label>
            <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', session('installer.environment.MAIL_PORT')) }}">
        </div>
        <div class="col-md-4 mb-3">
            <label for="mail_username" class="form-label">Usuario SMTP</label>
            <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', session('installer.environment.MAIL_USERNAME')) }}">
        </div>
        <div class="col-md-4 mb-3">
            <label for="mail_password" class="form-label">Contraseña SMTP</label>
            <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ old('mail_password', session('installer.environment.MAIL_PASSWORD')) }}">
        </div>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="mail_encryption" class="form-label">Cifrado SMTP</label>
            <select class="form-select" id="mail_encryption" name="mail_encryption">
                <option value="">Ninguno</option>
                <option value="tls" {{ old('mail_encryption', session('installer.environment.MAIL_ENCRYPTION')) == 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ old('mail_encryption', session('installer.environment.MAIL_ENCRYPTION')) == 'ssl' ? 'selected' : '' }}>SSL</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="mail_from_address" class="form-label">Dirección de Remitente</label>
            <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" placeholder="noreply@example.com" value="{{ old('mail_from_address', session('installer.environment.MAIL_FROM_ADDRESS')) }}">
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('installer.advanced.migrations') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Anterior
        </a>
        <button type="submit" class="btn btn-primary">
            Siguiente: Configuración Final <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>
@endsection
