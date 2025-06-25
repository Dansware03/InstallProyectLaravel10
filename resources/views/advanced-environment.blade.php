@extends('installer::layout')

@section('title', 'Entorno - Instalación Avanzada')

@section('header', 'Paso 4: Configuración del Entorno')

@section('description', 'Configure los detalles básicos de su aplicación y el envío de correo')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'completed'],
        ['name' => 'Migraciones', 'status' => 'completed'],
        ['name' => 'Entorno', 'status' => 'active'],
        ['name' => 'Config. Final', 'status' => 'pending'],
        ['name' => 'Instalación', 'status' => 'pending'],
    ]
];
@endphp

@section('content')
<form method="POST" action="{{ route('installer.advanced.environment.process') }}" id="environment-form">
    @csrf

    <h5 class="mb-3"><i class="fas fa-cogs me-2 text-primary"></i>Configuración de la Aplicación</h5>
    <div class="mb-3">
        <label for="app_name" class="form-label">Nombre de la Aplicación</label>
        <input type="text" class="form-control" id="app_name" name="app_name"
               value="{{ old('app_name', session('installer.environment.APP_NAME', config('app.name', 'Laravel'))) }}">
        <div class="form-text">Este nombre se utilizará en toda la aplicación (ej. títulos de página, correos).</div>
    </div>

    <hr class="my-4">

    <h5 class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i>Configuración de Correo (Opcional)</h5>
    <p class="text-muted small mb-3">
        Configure cómo su aplicación enviará correos electrónicos. Puede omitir esto y configurarlo más tarde en su archivo <code>.env</code>.
    </p>

    <div class="accordion" id="mailConfigAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingMailConfig">
                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMailConfig" aria-expanded="false" aria-controls="collapseMailConfig" style="font-size: 0.9rem; background-color: #f8f9fa;">
                    Mostrar/Ocultar Opciones de Correo
                </button>
            </h2>
            <div id="collapseMailConfig" class="accordion-collapse collapse" aria-labelledby="headingMailConfig" data-bs-parent="#mailConfigAccordion">
                <div class="accordion-body border p-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mail_driver" class="form-label">Driver de Correo</label>
                            <select class="form-select" id="mail_driver" name="mail_driver">
                                <option value="smtp" @if(old('mail_driver', session('installer.environment.MAIL_MAILER', config('mail.default'))) == 'smtp') selected @endif>SMTP</option>
                                <option value="sendmail" @if(old('mail_driver', session('installer.environment.MAIL_MAILER', config('mail.default'))) == 'sendmail') selected @endif>Sendmail</option>
                                <option value="log" @if(old('mail_driver', session('installer.environment.MAIL_MAILER', config('mail.default'))) == 'log') selected @endif>Log (para desarrollo)</option>
                                <option value="array" @if(old('mail_driver', session('installer.environment.MAIL_MAILER', config('mail.default'))) == 'array') selected @endif>Array (para pruebas)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_host" class="form-label">Host SMTP</label>
                            <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', session('installer.environment.MAIL_HOST', config('mail.mailers.smtp.host'))) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mail_port" class="form-label">Puerto SMTP</label>
                            <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', session('installer.environment.MAIL_PORT', config('mail.mailers.smtp.port'))) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mail_encryption" class="form-label">Cifrado SMTP</label>
                            <select class="form-select" id="mail_encryption" name="mail_encryption">
                                <option value="" @if(old('mail_encryption', session('installer.environment.MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption'))) == '') selected @endif>Ninguno</option>
                                <option value="tls" @if(old('mail_encryption', session('installer.environment.MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption'))) == 'tls') selected @endif>TLS</option>
                                <option value="ssl" @if(old('mail_encryption', session('installer.environment.MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption'))) == 'ssl') selected @endif>SSL</option>
                            </select>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label for="mail_from_address" class="form-label">Dirección Remitente</label>
                            <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" placeholder="noreply@example.com" value="{{ old('mail_from_address', session('installer.environment.MAIL_FROM_ADDRESS', config('mail.from.address'))) }}">
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mail_username" class="form-label">Usuario SMTP</label>
                            <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', session('installer.environment.MAIL_USERNAME', config('mail.mailers.smtp.username'))) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_password" class="form-label">Contraseña SMTP</label>
                            <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ old('mail_password', session('installer.environment.MAIL_PASSWORD', config('mail.mailers.smtp.password'))) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('footer-actions')
    <a href="{{ route('installer.advanced.migrations') }}" class="btn btn-installer">
        <i class="fas fa-arrow-left me-1"></i> Anterior
    </a>
    <button type="submit" form="environment-form" class="btn btn-installer-primary">
        Siguiente <i class="fas fa-arrow-right ms-1"></i>
    </button>
@endsection
