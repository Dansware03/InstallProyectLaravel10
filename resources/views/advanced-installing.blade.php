@extends('installer::layout')

@section('title', 'Instalando - Instalación Avanzada')

@section('header', 'Paso 6: Instalación en Progreso')

@section('description', 'Por favor espere mientras configuramos su aplicación con sus ajustes personalizados')

@section('steps')
    <div class="step-indicator">
        <div class="step completed"><i class="fas fa-check"></i></div>
        <div class="step completed"><i class="fas fa-check"></i></div>
        <div class="step completed"><i class="fas fa-check"></i></div>
        <div class="step completed"><i class="fas fa-check"></i></div>
        <div class="step completed"><i class="fas fa-check"></i></div>
        <div class="step active">6</div>
    </div>
@endsection

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-cogs fa-spin" style="font-size: 4rem; color: #FF512F;"></i>
    </div>

    <h3 class="mb-4">Configurando su aplicación...</h3>

    <div class="progress mb-4" style="height: 30px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
    </div>

    <div id="installation-status" class="mb-4">
        <p class="lead">Inicializando instalación avanzada...</p>
    </div>

    <div id="installation-steps-display" class="text-start">
        <!-- Los pasos se añadirán aquí con JS -->
    </div>

    <div id="installation-complete" style="display: none;" class="mt-4">
        <div class="alert alert-success py-3">
            <i class="fas fa-check-circle fa-lg me-2"></i>
            <strong>¡Instalación completada exitosamente!</strong>
        </div>

        <div class="card mt-4" id="credentialsCard">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Credenciales de Acceso (Usuario Administrador)</h5>
            </div>
            <div class="card-body p-4">
                <div id="credentialsDisplay">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="admin-email" class="form-label small text-muted">Email:</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="admin-email" readonly>
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                    onclick="copyToClipboard('admin-email', this)">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="admin-password" class="form-label small text-muted">Contraseña:</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="admin-password" readonly>
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                    onclick="copyToClipboard('admin-password', this)">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-4 py-2 px-3 small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Guarde estas credenciales. Se recomienda cambiar la contraseña después del primer acceso.
                    </div>
                </div>
                <div id="noCredentialsDisplay" style="display: none;">
                     <p class="mb-0 text-muted"><i class="fas fa-info-circle me-1"></i>El usuario administrador no fue creado porque se omitió la ejecución de migraciones o ya existía.</p>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4 py-3">
            <i class="fas fa-info-circle fa-lg me-2"></i>
            <span>
                Su archivo <code>.env</code> ha sido actualizado.
                La aplicación ha sido configurada según sus especificaciones.
            </span>
        </div>

        <div class="mt-4 pt-2 text-center">
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i>Ir a la Página Principal
            </a>
        </div>
    </div>

    <div id="installation-error" style="display: none;" class="mt-4">
        <div class="alert alert-danger py-3">
            <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
            <strong>Error durante la instalación:</strong>
            <div id="error-message" class="mt-1 small"></div>
        </div>

        <div class="mt-4 pt-2 text-center">
            <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio del Instalador
            </a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const progressBar = document.querySelector('.progress-bar');
            const statusElement = document.getElementById('installation-status');
            const stepsDisplayContainer = document.getElementById('installation-steps-display');
            const completeSection = document.getElementById('installation-complete');
            const errorSection = document.getElementById('installation-error');
            const adminEmailField = document.getElementById('admin-email');
            const adminPasswordField = document.getElementById('admin-password');
            const credentialsCard = document.querySelector('#installation-complete .card');

            let currentStepIndex = 0;
            // Dinámicamente construir los pasos basados en la configuración de sesión
            const processSteps = [
                { text: 'Actualizando archivo .env con configuraciones de entorno...', progress: 20, active: true },
                { text: 'Aplicando configuraciones de tipo de entorno (desarrollo/producción)...', progress: 40, active: true },
            ];

            @if(session('installer.final_config.disable_api'))
            processSteps.push({ text: 'Deshabilitando rutas de API...', progress: 50, active: true });
            @endif

            @if(session('installer.migrations_run'))
            processSteps.push({ text: 'Creando usuario administrador...', progress: 70, active: true });
            @endif

            processSteps.push({ text: 'Marcando instalación como completada...', progress: 90, active: true });
            processSteps.push({ text: 'Limpiando datos de sesión del instalador...', progress: 95, active: true });
            processSteps.push({ text: 'Finalizando instalación...', progress: 100, active: true });

            const activeProcessSteps = processSteps.filter(step => step.active);

            function updateDisplay() {
                if (currentStepIndex < activeProcessSteps.length) {
                    const step = activeProcessSteps[currentStepIndex];
                    progressBar.style.width = step.progress + '%';
                    statusElement.innerHTML = `<p class="lead">${step.text}</p>`;

                    // Actualizar la lista de pasos
                    let stepsHtml = '';
                    activeProcessSteps.forEach((s, index) => {
                        if (index < currentStepIndex) {
                            stepsHtml += `<div class="installation-step text-success"><i class="fas fa-check me-2"></i><s>${s.text}</s></div>`;
                        } else if (index === currentStepIndex) {
                            stepsHtml += `<div class="installation-step"><i class="fas fa-circle-notch fa-spin me-2"></i><strong>${s.text}</strong></div>`;
                        } else {
                            stepsHtml += `<div class="installation-step text-muted"><i class="far fa-circle me-2"></i>${s.text}</div>`;
                        }
                    });
                    stepsDisplayContainer.innerHTML = stepsHtml;

                    currentStepIndex++;
                    if (currentStepIndex < activeProcessSteps.length) {
                        setTimeout(updateDisplay, 1000 + Math.random() * 500); // Simular tiempo
                    } else {
                        executeActualInstallation();
                    }
                }
            }

            function executeActualInstallation() {
                statusElement.innerHTML = `<p class="lead">Ejecutando procesos finales...</p>`;
                progressBar.style.width = '98%';

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
                        progressBar.style.width = '100%';
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        statusElement.innerHTML = '<p class="lead text-success">¡Instalación Avanzada Completada!</p>';
                        stepsDisplayContainer.style.display = 'none';

                        if (data.credentials && data.credentials.email && data.credentials.password && adminEmailField && adminPasswordField) {
                            adminEmailField.value = data.credentials.email;
                            adminPasswordField.value = data.credentials.password;
                            document.getElementById('credentialsDisplay').style.display = 'block';
                            document.getElementById('noCredentialsDisplay').style.display = 'none';
                            if(credentialsCard) credentialsCard.style.display = 'block';
                        } else {
                            document.getElementById('credentialsDisplay').style.display = 'none';
                            document.getElementById('noCredentialsDisplay').style.display = 'block';
                            // Aún mostrar la tarjeta si no hay credenciales, pero con el mensaje apropiado.
                            if(credentialsCard) credentialsCard.style.display = 'block';
                        }
                        completeSection.style.display = 'block';
                    } else {
                        showError(data.message || 'Ocurrió un error desconocido.');
                    }
                })
                .catch(error => {
                    showError('Error de conexión o respuesta inesperada: ' + error.message);
                });
            }

            function showError(message) {
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-danger');
                statusElement.innerHTML = '<p class="lead text-danger">Fallo en la Instalación</p>';
                stepsDisplayContainer.style.display = 'none';
                document.getElementById('error-message').innerHTML = message.replace(/\n/g, '<br>');
                errorSection.style.display = 'block';
            }

            window.copyToClipboard = function (elementId, buttonElement) {
                const element = document.getElementById(elementId);
                if (!element) return;

                navigator.clipboard.writeText(element.value).then(function() {
                    const originalHtml = buttonElement.innerHTML;
                    buttonElement.innerHTML = '<i class="fas fa-check"></i> Copiado';
                    buttonElement.classList.add('btn-success');
                    buttonElement.classList.remove('btn-outline-secondary');

                    setTimeout(() => {
                        buttonElement.innerHTML = originalHtml;
                        buttonElement.classList.remove('btn-success');
                        buttonElement.classList.add('btn-outline-secondary');
                    }, 2000);
                }, function(err) {
                    // Fallback para execCommand si navigator.clipboard no está disponible o falla
                    try {
                        element.select();
                        element.setSelectionRange(0, 99999); // Para móviles
                        document.execCommand('copy');

                        const originalHtml = buttonElement.innerHTML;
                        buttonElement.innerHTML = '<i class="fas fa-check"></i> Copiado (fallback)';
                        buttonElement.classList.add('btn-success');
                        buttonElement.classList.remove('btn-outline-secondary');

                        setTimeout(() => {
                            buttonElement.innerHTML = originalHtml;
                            buttonElement.classList.remove('btn-success');
                            buttonElement.classList.add('btn-outline-secondary');
                        }, 2000);
                    } catch (execErr) {
                        console.error('Error al copiar al portapapeles: ', execErr);
                        alert('Error al copiar. Por favor, copie manualmente.');
                    }
                });
            };

            // Iniciar el proceso
            setTimeout(updateDisplay, 500);
        });
    </script>
@endpush
[end of resources/views/advanced-installing.blade.php]
