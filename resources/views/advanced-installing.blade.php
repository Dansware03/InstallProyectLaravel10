@extends('installer::layout')

@section('title', 'Instalando - Instalación Avanzada')

@section('header', 'Paso 6: Instalación en Progreso')

@section('description', 'Por favor espere mientras configuramos su aplicación con sus ajustes personalizados')

@php
$stepsData = [
    'total' => [
        ['name' => 'Requisitos', 'status' => 'completed'],
        ['name' => 'Base de Datos', 'status' => 'completed'],
        ['name' => 'Migraciones', 'status' => 'completed'],
        ['name' => 'Entorno', 'status' => 'completed'],
        ['name' => 'Config. Final', 'status' => 'completed'],
        ['name' => 'Instalación', 'status' => 'active'],
    ]
];
@endphp

@section('title', 'Instalando - Instalación Avanzada')
@section('header', 'Paso 6: Instalación en Progreso')
@section('description', 'Aplicando sus configuraciones personalizadas. Por favor espere.')

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

    <p class="text-muted small mb-2" id="installation-step-text">Inicializando instalación avanzada...</p>

    <div id="installation-steps-display" class="list-group list-group-flush text-start small my-3 mx-auto" style="max-width: 400px; font-size: 0.85rem;">
        {{-- Los pasos individuales se añadirán aquí con JS --}}
    </div>


    {{-- Esta sección se mostrará al completar la instalación --}}
    <div id="installation-complete" style="display: none;" class="mt-4 text-start">
        <div class="alert alert-success d-flex align-items-center py-3" role="alert">
            <i class="fas fa-check-circle fa-2x me-3"></i>
            <div>
                <strong class="h5">¡Instalación Avanzada Completada!</strong>
            </div>
        </div>
         <p class="small text-muted">Su aplicación ha sido configurada según sus especificaciones y el archivo <code>.env</code> ha sido actualizado.</p>

        <div class="card mt-3" id="credentials-card-adv" style="display:none;">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-key me-2"></i>Credenciales de Administrador</h6>
            </div>
            <div class="card-body">
                 <p class="small text-muted" id="credentials-message-adv">Guarde estas credenciales. Se recomienda cambiar la contraseña después del primer inicio de sesión.</p>
                <div class="row g-2" id="credentials-fields-adv" style="display:none;">
                    <div class="col-sm-6">
                        <label for="admin-email-adv" class="form-label small">Email:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="admin-email-adv" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('admin-email-adv', this)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="admin-password-adv" class="form-label small">Contraseña:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="admin-password-adv" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('admin-password-adv', this)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Esta sección se mostrará si hay un error --}}
    <div id="installation-error-adv" style="display: none;" class="mt-4 text-start">
        <div class="alert alert-danger d-flex align-items-center py-3" role="alert">
             <i class="fas fa-times-circle fa-2x me-3"></i>
            <div>
                <strong class="h5">Error durante la instalación</strong>
                <div id="error-message-adv" class="mt-1 small"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-actions')
    <div id="actions-in-progress-adv" class="w-100 text-center">
        <button type="button" class="btn btn-installer" disabled>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Instalando...
        </button>
    </div>
    <div id="actions-complete-adv" style="display:none;">
        <a href="{{ url('/') }}" class="btn btn-installer-primary">
            <i class="fas fa-home me-1"></i> Ir a la Aplicación
        </a>
    </div>
     <div id="actions-error-adv" style="display:none;">
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
    const stepsDisplayContainer = document.getElementById('installation-steps-display');

    const completeSection = document.getElementById('installation-complete');
    const credentialsCard = document.getElementById('credentials-card-adv');
    const credentialsFields = document.getElementById('credentials-fields-adv');
    const credentialsMessage = document.getElementById('credentials-message-adv');
    const adminEmailField = document.getElementById('admin-email-adv');
    const adminPasswordField = document.getElementById('admin-password-adv');

    const errorSection = document.getElementById('installation-error-adv');
    const errorMessageDiv = document.getElementById('error-message-adv');

    const actionsInProgress = document.getElementById('actions-in-progress-adv');
    const actionsComplete = document.getElementById('actions-complete-adv');
    const actionsError = document.getElementById('actions-error-adv');

    // Dinámicamente construir los pasos basados en la configuración de sesión
    // Estos son los pasos visuales que se mostrarán, no necesariamente todos se ejecutan si el usuario omitió algo
    const visualSteps = [
        { text: 'Actualizando archivo .env...', key: 'env_update' },
        { text: 'Aplicando configuraciones de entorno...', key: 'env_settings' },
    ];

    @if(session('installer.final_config.disable_api'))
        visualSteps.push({ text: 'Deshabilitando rutas de API...', key: 'api_disable' });
    @endif

    @if(session('installer.migrations_run'))
        visualSteps.push({ text: 'Creando usuario administrador...', key: 'admin_user' });
    @endif
    visualSteps.push({ text: 'Finalizando y limpiando...', key: 'cleanup' });


    let currentVisualStepIndex = 0;
    const totalVisualSteps = visualSteps.length;

    function updateVisualProgress() {
        if (currentVisualStepIndex < totalVisualSteps) {
            const step = visualSteps[currentVisualStepIndex];
            const progressPercentage = Math.round(((currentVisualStepIndex + 1) / totalVisualSteps) * 98); // No llegar al 100% hasta el final

            mainProgressBar.style.width = progressPercentage + '%';
            mainProgressBar.textContent = progressPercentage + '%';
            mainProgressBar.setAttribute('aria-valuenow', progressPercentage);
            installationStepText.textContent = step.text;

            // Actualizar la lista de pasos detallados
            let stepsHtml = '';
            visualSteps.forEach((s, index) => {
                let statusClass = 'list-group-item-light text-muted';
                let icon = 'far fa-circle';
                if (index < currentVisualStepIndex) {
                    statusClass = 'list-group-item-success text-success';
                    icon = 'fas fa-check-circle';
                } else if (index === currentVisualStepIndex) {
                    statusClass = 'list-group-item-primary';
                    icon = 'fas fa-spinner fa-spin';
                }
                stepsHtml += `<div class="list-group-item d-flex justify-content-between align-items-center ${statusClass} py-1 px-2"><span>${s.text}</span><i class="${icon}"></i></div>`;
            });
            stepsDisplayContainer.innerHTML = stepsHtml;

            currentVisualStepIndex++;
            if (currentVisualStepIndex < totalVisualSteps) {
                setTimeout(updateVisualProgress, 700 + Math.random() * 500);
            } else {
                installationStepText.textContent = 'Enviando configuración final al servidor...';
                executeRealInstallation();
            }
        }
    }

    function executeRealInstallation() {
        fetch('{{ route("installer.advanced.execute") }}', {
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
                mainProgressBar.classList.remove('progress-bar-animated');
                mainProgressBar.classList.add('bg-success');
                stepsDisplayContainer.style.opacity = '0.6'; // Atenuar lista de pasos

                completeSection.style.display = 'block';
                credentialsCard.style.display = 'block'; // Mostrar siempre la tarjeta

                if (data.credentials && data.credentials.email && data.credentials.password) {
                    credentialsFields.style.display = 'flex'; // Mostrar campos de credenciales
                    adminEmailField.value = data.credentials.email;
                    adminPasswordField.value = data.credentials.password;
                    credentialsMessage.textContent = 'Guarde estas credenciales. Se recomienda cambiar la contraseña después del primer inicio de sesión.';
                } else {
                     credentialsFields.style.display = 'none'; // Ocultar campos
                     credentialsMessage.innerHTML = '<i class="fas fa-info-circle me-1"></i>El usuario administrador no fue creado (posiblemente porque se omitió la ejecución de migraciones o ya existía).';
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
        mainProgressBar.style.width = '100%';

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
