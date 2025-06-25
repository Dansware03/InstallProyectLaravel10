@extends('installer::layout')

@section('title', 'Instalando - Instalación Rápida')

@section('header', 'Instalación en Progreso')

@section('description', 'Por favor espere mientras configuramos su aplicación')

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
        <span class="step-label">Instalación</span>
    </li>
</ul>
@endsection

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-cogs fa-spin" style="font-size: 4rem; color: #FF512F;"></i>
    </div>

    <h4 class="mb-4 text-primary"><i class="fas fa-spinner fa-spin me-2"></i>Configurando su aplicación...</h4>

    <div class="progress mb-4" style="height: 26px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; font-size: 0.8rem;"></div>
    </div>

    <div id="installation-status" class="mb-4">
        <p class="lead">Inicializando instalación...</p>
    </div>

    <div id="installation-steps" class="text-start">
        <div class="installation-step" data-step="database">
            <i class="fas fa-circle-notch fa-spin me-2"></i>
            <span>Configurando base de datos...</span>
        </div>
    </div>

    <div id="installation-complete" style="display: none;" class="mt-4">
        <div class="alert alert-success py-3">
            <i class="fas fa-check-circle fa-lg me-2"></i>
            <strong>¡Instalación completada exitosamente!</strong>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Credenciales de Acceso</h5>
            </div>
            <div class="card-body p-4">
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
        </div>

        <div class="mt-4 pt-2 text-center">
            <a href="/login" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Ir al Login
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
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const progressBar = document.querySelector('.progress-bar');
            const statusElement = document.getElementById('installation-status');
            const stepsContainer = document.getElementById('installation-steps');
            const completeSection = document.getElementById('installation-complete');
            const errorSection = document.getElementById('installation-error');

            let currentStep = 0;
            const steps = [
                { key: 'database', text: 'Configurando base de datos...', progress: 20 },
                { key: 'migrations', text: 'Ejecutando migraciones...', progress: 40 },
                { key: 'security', text: 'Aplicando configuraciones de seguridad...', progress: 60 },
                { key: 'optimizations', text: 'Aplicando optimizaciones...', progress: 80 },
                { key: 'user', text: 'Creando usuario administrador...', progress: 95 },
                { key: 'complete', text: 'Finalizando instalación...', progress: 100 }
            ];

            function updateProgress() {
                if (currentStep < steps.length) {
                    const step = steps[currentStep];

                    // Actualizar barra de progreso
                    progressBar.style.width = step.progress + '%';

                    // Actualizar estado
                    statusElement.innerHTML = `<p class="lead">${step.text}</p>`;

                    // Actualizar pasos
                    updateStepDisplay();

                    currentStep++;

                    if (currentStep < steps.length) {
                        setTimeout(updateProgress, 1500); // Simular tiempo de procesamiento
                    } else {
                        // Ejecutar instalación real
                        executeInstallation();
                    }
                }
            }

            function updateStepDisplay() {
                const currentStepData = steps[currentStep];
                stepsContainer.innerHTML = `
                <div class="installation-step" data-step="${currentStepData.key}">
                    <i class="fas fa-circle-notch fa-spin me-2"></i>
                    <span>${currentStepData.text}</span>
                </div>
            `;
            }

            function executeInstallation() {
                // Hacer petición AJAX real para ejecutar la instalación
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
                            showSuccess(data.credentials);
                        } else {
                            showError(data.message);
                        }
                    })
                    .catch(error => {
                        showError('Error de conexión: ' + error.message);
                    });
            }

            function showSuccess(credentials) {
                progressBar.style.width = '100%';
                statusElement.innerHTML = '<p class="lead text-success">¡Instalación completada!</p>';
                stepsContainer.style.display = 'none';

                // Mostrar credenciales
                document.getElementById('admin-email').value = credentials.email;
                document.getElementById('admin-password').value = credentials.password;

                completeSection.style.display = 'block';
            }

            function showError(message) {
                stepsContainer.style.display = 'none';
                document.getElementById('error-message').textContent = message;
                errorSection.style.display = 'block';
            }

            // Función para copiar al portapapeles
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
            setTimeout(updateProgress, 1000);
        });
    </script>
@endpush