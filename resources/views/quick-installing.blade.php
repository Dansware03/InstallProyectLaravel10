@extends('installer::layout')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'completed'],
        ['name' => 'Instalación', 'status' => 'active'],
    ]
];
@endphp

@section('title', 'Instalando - Instalación Rápida')
@section('header', 'Instalación en Progreso')
@section('description', 'Por favor espere mientras configuramos su aplicación.')

@section('content')
<div class="text-center" id="installing-content">
    <div class="mb-4">
        <i class="fas fa-cog fa-spin fa-3x text-primary"></i>
    </div>

    <h5 class="mb-3" id="installation-main-status-text">Configurando su aplicación...</h5>

    <div class="progress mb-3" style="height: 20px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="main-progress-bar">0%</div>
    </div>

    <p class="text-muted small mb-4" id="installation-step-text">Inicializando instalación...</p>

    {{-- Esta sección se mostrará al completar la instalación --}}
    <div id="installation-complete" style="display: none;" class="mt-4 text-start">
        <div class="alert alert-success d-flex align-items-center py-3" role="alert">
            <i class="fas fa-check-circle fa-2x me-3"></i>
            <div>
                <strong class="h5">¡Instalación completada exitosamente!</strong>
            </div>
        </div>

        <div class="card mt-3" id="credentials-card" style="display:none;">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-key me-2"></i>Credenciales de Acceso del Administrador</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Guarde estas credenciales. Se recomienda cambiar la contraseña después del primer inicio de sesión.</p>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <label for="admin-email" class="form-label small">Email:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="admin-email" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('admin-email', this)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="admin-password" class="form-label small">Contraseña:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="admin-password" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('admin-password', this)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Esta sección se mostrará si hay un error --}}
    <div id="installation-error" style="display: none;" class="mt-4 text-start">
        <div class="alert alert-danger d-flex align-items-center py-3" role="alert">
             <i class="fas fa-times-circle fa-2x me-3"></i>
            <div>
                <strong class="h5">Error durante la instalación</strong>
                <div id="error-message" class="mt-1 small"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-actions')
    <div id="actions-in-progress" class="w-100 text-center">
        <button type="button" class="btn btn-installer" disabled>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Instalando...
        </button>
    </div>
    <div id="actions-complete" style="display:none;">
        <a href="{{ url('/') }}" class="btn btn-installer-primary">
            <i class="fas fa-home me-1"></i> Ir a la Aplicación
        </a>
    </div>
     <div id="actions-error" style="display:none;">
        <a href="{{ route('installer.welcome') }}" class="btn btn-installer">
            <i class="fas fa-arrow-left me-1"></i> Volver al Inicio
        </a>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainProgressBar = document.getElementById('main-progress-bar');
    const installationMainStatusText = document.getElementById('installation-main-status-text');
    const installationStepText = document.getElementById('installation-step-text');

    const completeSection = document.getElementById('installation-complete');
    const credentialsCard = document.getElementById('credentials-card');
    const adminEmailField = document.getElementById('admin-email');
    const adminPasswordField = document.getElementById('admin-password');

    const errorSection = document.getElementById('installation-error');
    const errorMessageDiv = document.getElementById('error-message');

    const actionsInProgress = document.getElementById('actions-in-progress');
    const actionsComplete = document.getElementById('actions-complete');
    const actionsError = document.getElementById('actions-error');

    const steps = [
        { text: 'Configurando base de datos...', progress: 20 },
        { text: 'Ejecutando migraciones...', progress: 40 },
        { text: 'Aplicando configuraciones de seguridad...', progress: 60 },
        { text: 'Aplicando optimizaciones de producción...', progress: 80 },
        { text: 'Creando usuario administrador...', progress: 95 },
        { text: 'Finalizando instalación...', progress: 100 }
    ];
    let currentStepIndex = 0;

    function updateVisualProgress() {
        if (currentStepIndex < steps.length) {
            const step = steps[currentStepIndex];
            mainProgressBar.style.width = step.progress + '%';
            mainProgressBar.textContent = step.progress + '%';
            mainProgressBar.setAttribute('aria-valuenow', step.progress);
            installationStepText.textContent = step.text;

            currentStepIndex++;
            if (currentStepIndex < steps.length) {
                setTimeout(updateVisualProgress, 700 + Math.random() * 500); // Simular tiempo
            } else {
                // Al final de la simulación visual, realizar la petición real
                installationStepText.textContent = 'Enviando configuración al servidor...';
                executeRealInstallation();
            }
        }
    }

    function executeRealInstallation() {
        fetch('{{ route("installer.quick.execute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                installationMainStatusText.innerHTML = '<i class="fas fa-check-circle me-2 text-success"></i>Instalación Completada';
                installationStepText.textContent = 'Su aplicación ha sido configurada exitosamente.';
                mainProgressBar.style.width = '100%';
                mainProgressBar.textContent = '100%';
                mainProgressBar.classList.add('bg-success');

                completeSection.style.display = 'block';
                if (data.credentials && data.credentials.email && data.credentials.password) {
                    credentialsCard.style.display = 'block';
                    adminEmailField.value = data.credentials.email;
                    adminPasswordField.value = data.credentials.password;
                }
                actionsInProgress.style.display = 'none';
                actionsComplete.style.display = 'block';
            } else {
                showError(data.message || 'Ocurrió un error desconocido durante la instalación.');
            }
        })
        .catch(error => {
            showError('Error de conexión o respuesta inesperada del servidor: ' + error.message);
        });
    }

    function showError(message) {
        installationMainStatusText.innerHTML = '<i class="fas fa-times-circle me-2 text-danger"></i>Fallo en la Instalación';
        installationStepText.textContent = 'No se pudo completar la instalación.';
        mainProgressBar.classList.add('bg-danger');
        mainProgressBar.style.width = '100%'; // Marcar como finalizado pero con error

        errorSection.style.display = 'block';
        errorMessageDiv.innerHTML = message.replace(/\n/g, '<br>');

        actionsInProgress.style.display = 'none';
        actionsError.style.display = 'block';
    }

    window.copyToClipboard = function (elementId, buttonElement) {
        const element = document.getElementById(elementId);
        if (!element) return;
        navigator.clipboard.writeText(element.value).then(function() {
            const originalHtml = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="fas fa-check"></i>';
            buttonElement.classList.add('btn-success');
            buttonElement.classList.remove('btn-outline-secondary');
            setTimeout(() => {
                buttonElement.innerHTML = originalHtml;
                buttonElement.classList.remove('btn-success');
                buttonElement.classList.add('btn-outline-secondary');
            }, 1500);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    };

    // Iniciar el proceso visual
    setTimeout(updateVisualProgress, 500);
});
</script>
@endpush